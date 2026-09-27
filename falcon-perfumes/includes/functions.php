<?php
require_once __DIR__.'/db.php';
ini_set('display_errors','0');
set_exception_handler(function(Throwable $e){error_log((string)$e);http_response_code(500);echo '<h1>Unable to complete this request</h1><p>Please try again. For first-time setup, import database.sql and check includes/config.local.php. Details are recorded in the PHP error log.</p>';});
$adminContext = defined('ADMIN_CONTEXT') && ADMIN_CONTEXT;
session_name($adminContext ? 'FALCON_ADMIN' : 'FALCON_CUSTOMER');
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','samesite'=>'Lax','path'=>rtrim($config['base_url'],'/').($adminContext?'/admin':'').'/']);
ini_set('session.use_strict_mode','1');session_start();
header('X-Content-Type-Options: nosniff');header('X-Frame-Options: DENY');header('Referrer-Policy: strict-origin-when-cross-origin');header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; script-src 'self'; style-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");header('Cache-Control: no-store');
function e($v): string { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
function url(string $path=''): string {global $config;return rtrim($config['base_url'],'/').'/'.ltrim($path,'/');}
function redirect(string $path): never {header('Location: '.url($path),true,303);exit;}
function post(): bool {return $_SERVER['REQUEST_METHOD']==='POST';}
function textval(string $key,bool $fromGet=false): string {$s=$fromGet?$_GET:$_POST;return isset($s[$key])&&is_string($s[$key])?(in_array($key,['password','current_password','confirm_password'],true)?$s[$key]:trim($s[$key])):'';}
function number(string $key,bool $get=false): int { $v=textval($key,$get);return ctype_digit($v)?(int)$v:0; }
function csrf_token(): string {return $_SESSION['csrf']??=bin2hex(random_bytes(32));}
function csrf(): void {echo '<input type="hidden" name="csrf" value="'.e(csrf_token()).'">';}
function verify_csrf(): void {if(!hash_equals(csrf_token(),textval('csrf'))){http_response_code(403);exit('Request expired. Reload the form and try again.');}}
function flash(string $message): void {$_SESSION['flash']=$message;}
function errors(array $errors): void {if($errors){echo '<div class="alert error" role="alert"><ul>';foreach($errors as $x)echo '<li>'.e($x).'</li>';echo '</ul></div>';}}
function money($n): string {global $config;return e($config['currency']).' '.number_format((float)$n,2);}
function user(): ?array {if(empty($_SESSION['user_id']))return null;$u=query('SELECT id,username,email,is_active FROM users WHERE id=?',[$_SESSION['user_id']])->fetch();if(!$u||!$u['is_active']){unset($_SESSION['user_id']);return null;}return $u;}
function require_user(): array {$u=user();if(!$u){flash('Please sign in to continue.');redirect('auth/login.php');}return $u;}
function admin(): ?array {if(empty($_SESSION['admin_id']))return null;$a=query('SELECT id,username,must_change_password FROM admins WHERE id=?',[$_SESSION['admin_id']])->fetch();return $a?:null;}
function require_admin(): array {$a=admin();if(!$a)redirect('admin/login.php');if($a['must_change_password']&&basename($_SERVER['SCRIPT_NAME'])!=='password.php'&&basename($_SERVER['SCRIPT_NAME'])!=='logout.php')redirect('admin/password.php');return $a;}
function end_session(): void {$_SESSION=[];$p=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$p['path'],'',$p['secure'],$p['httponly']);session_destroy();}
function password_errors(string $pass): array {return strlen($pass)<10||strlen($pass)>72?['Password must be between 10 and 72 bytes.']:[];}
function auth_key(string $identity,string $role): string {return hash('sha256',$role.'|'.strtolower($identity).'|'.($_SERVER['REMOTE_ADDR']??'local'));}
function auth_blocked(string $key): bool {return (int)query('SELECT COUNT(*) FROM login_attempts WHERE attempt_key=? AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)',[$key])->fetchColumn()>=5;}
function fail_login(string $key): void {query('INSERT INTO login_attempts(attempt_key) VALUES (?)',[$key]);query('DELETE FROM login_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)');}
function product_sql(): string {return 'SELECT p.*, b.name brand, c.name category, (SELECT path FROM product_images WHERE product_id=p.id ORDER BY sort_order,id LIMIT 1) image FROM products p JOIN brands b ON b.id=p.brand_id JOIN categories c ON c.id=p.category_id';}
function available(array $p): bool {return !$p['is_sold_out'] && $p['stock']>0 && $p['is_active'];}
function product_card(array $p): void { ?>
<article class="product-card"><a class="product-media" href="<?=e(url('product.php?id='.$p['id']))?>"><?php if(!available($p)):?><span class="badge sold">SOLD OUT</span><?php endif?><img src="<?=e(url($p['image']?:'images/placeholder.svg'))?>" alt="<?=e($p['brand'].' '.$p['name'])?>" loading="lazy"></a><div class="product-body"><span class="eyebrow"><?=e($p['brand'])?></span><h3><a href="<?=e(url('product.php?id='.$p['id']))?>"><?=e($p['name'])?></a></h3><p class="muted small"><?=e($p['concentration'].' · '.$p['size_ml'].' ml')?></p><div class="price"><?=money($p['price'])?></div><form method="post" action="<?=e(url('cart.php'))?>"><?php csrf();?><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?=$p['id']?>"><button class="button full" <?=available($p)?'':'disabled'?>><?=available($p)?'Add to Cart':'Sold Out'?></button></form></div></article>
<?php }
function cart_items(): array {$out=[];foreach(($_SESSION['cart']??[]) as $id=>$qty){$p=query(product_sql().' WHERE p.id=?',[$id])->fetch();if($p){$p['quantity']=$qty;$out[]=$p;}}return $out;}
function totals(array $items): array {global $config;$cents=0;foreach($items as $p)$cents+=(int)round((float)$p['price']*100)*$p['quantity'];$shipping=$cents>0&&$cents<(int)round($config['free_shipping_min']*100)?(int)round($config['shipping_fee']*100):0;return ['subtotal'=>$cents/100,'shipping'=>$shipping/100,'total'=>($cents+$shipping)/100];}
function status_options(string $status): array {$map=['Pending'=>['Confirmed','Cancelled'],'Confirmed'=>['Shipped','Cancelled'],'Shipped'=>['Delivered'],'Delivered'=>[],'Cancelled'=>[]];return $map[$status]??[];}
