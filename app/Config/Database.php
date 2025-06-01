<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'etatcivil');
define('DB_USER', 'root');
define('DB_PASS', '');

$db =   new PDO("mysql: host=" . DB_HOST . "; dbname=" . DB_NAME, DB_USER, DB_PASS);
