<?php
// Read-only local checks: php tests/smoke.php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../includes/db.php';
$failed=0;
function check($ok,$label){global $failed;echo ($ok?'PASS ':'FAIL ').$label.PHP_EOL;if(!$ok)$failed++;}
foreach(['users','admins','messages','brands','categories','products','product_images','orders','order_items','login_attempts'] as $table){$s=query('SELECT table_name FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=?',[$table]);check((bool)$s->fetch(),'Table '.$table);}
check((int)query('SELECT COUNT(*) FROM products WHERE is_active=1')->fetchColumn()>0,'Catalogue contains active products');
check((int)query('SELECT COUNT(*) FROM products p WHERE is_active=1 AND (SELECT COUNT(*) FROM product_images i WHERE i.product_id=p.id)<2')->fetchColumn()===0,'Each active product has at least two photos');
foreach(query('SELECT path FROM product_images')->fetchAll() as $image)check(is_file(__DIR__.'/../'.$image['path']),'Local image '.$image['path']);
foreach(query('SELECT password FROM admins UNION ALL SELECT password FROM users')->fetchAll() as $row)check(str_starts_with($row['password'],'$2y$'),'Password uses bcrypt');
$hash=password_hash('LocalTest!234',PASSWORD_BCRYPT);check(password_verify('LocalTest!234',$hash)&&!password_verify('wrong',$hash),'Password hashing and verification');
exit($failed?1:0);
