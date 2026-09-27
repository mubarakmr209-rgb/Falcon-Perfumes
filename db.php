<?php
require_once __DIR__.'/config.php';
function db(): PDO {
    static $pdo;
    global $config;
    if (!$pdo) $pdo = new PDO("mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset=utf8mb4",$config['db_user'],$config['db_password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
    return $pdo;
}
function query(string $sql,array $args=[]): PDOStatement { $s=db()->prepare($sql);$s->execute($args);return $s; }
