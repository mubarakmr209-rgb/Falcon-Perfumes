<?php
// Local XAMPP defaults. Override in config.local.php (never commit credentials).
$config = ['db_host'=>'127.0.0.1','db_port'=>3306,'db_name'=>'falcon_perfumes','db_user'=>'root','db_password'=>'','base_url'=>'/falcon-perfumes','currency'=>'USD','shipping_fee'=>5.00,'free_shipping_min'=>50.00];
if (is_file(__DIR__.'/config.local.php')) $config = array_replace($config, require __DIR__.'/config.local.php');
