<?php

ini_set('max_execution_time', 0);

require_once 'meekrodb.2.3.class.php';

require_once 'functions.php';


define('DBENCODING', 'utf8');

class mysql_config {

    public static $host = '127.0.0.1';
    public static $user = 'root';
    public static $pass = 'root';
    public static $database = 'new_db1';
    public static $table = 'angelco2';
    public static $port = '3306';
    public static $id_column = 'data_id';

}

class sphinx_config {

    public static $host = '127.0.0.1';
    public static $rt_index = 'rt_jackodum';
    public static $port = '9306';

}

$mysql_config = new MeekroDB
        (mysql_config::$host, mysql_config::$user, mysql_config::$pass, mysql_config::$database, mysql_config::$port, DBENCODING);

$sphinx_config = new MeekroDB(
        sphinx_config::$host, 'root', '', '', sphinx_config::$port, DBENCODING);
