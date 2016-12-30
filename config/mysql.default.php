<?php

$mysql_host = '$(MYSQL_HOST)';
$mysql_db = '$(MYSQL_DB)';
$mysql_user = '$(MYSQL_USER)';
$mysql_pass = '$(MYSQL_PASS)';

function get_mysql_host()
{
    global $mysql_host;
    return $mysql_host;
}

function get_mysql_db()
{
    global $mysql_db;
    return $mysql_db;
}

function get_mysql_user()
{
    global $mysql_user;
    return $mysql_user;
}

function get_mysql_pass()
{
    global $mysql_pass;
    return $mysql_pass;
}

?>
