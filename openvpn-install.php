<?php

/*
CREATE TABLE IF NOT EXISTS `stats` (
`CommonName` text NOT NULL,
`RealAddress` text NOT NULL,
`BytesReceived` text NOT NULL,
`BytesSent` text NOT NULL,
`Since` text NOT NULL,
`VirtualAddress` text NOT NULL,
`LastRef` text NOT NULL,
`updated` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

* * * * * cd [cron location]; php cron.php
*/

require 'config/mysql.php';
require 'config/openvpn.php';

require 'functions/functions.php';

function install()
{
    $conn = mysql_connect(get_mysql_host(), get_mysql_user(), get_mysql_pass());
    if(!$conn) 
    {
        logMsg('Could not connect to the MySQL Server. Please validate your login & connection information:');
        logMsg('    host: ' . get_mysql_host());
        logMsg('    user: ' . get_mysql_user());
        logMsg('    ' . mysql_error());
        return;
    }
    
    $selectDb = mysql_select_db(get_mysql_db());
    if(!$selectDb)
    {
        logMsg('Could not select the MySQL Database. Please validate the login information and permissions on the database.');
        logMsg('    db: ' + get_mysql_db());
        logMsg('    ' . mysql_error());
        return;
    }
    
    $sql = 'CREATE TABLE IF NOT EXISTS `stats` (
            `CommonName` text NOT NULL,
            `RealAddress` text NOT NULL,
            `BytesReceived` text NOT NULL,
            `BytesSent` text NOT NULL,
            `Since` text NOT NULL,
            `VirtualAddress` text NOT NULL,
            `LastRef` text NOT NULL,
            `updated` bigint(20) NOT NULL
           ) ENGINE=MyISAM DEFAULT CHARSET=latin1;';
    $result = mysql_query($sql);
    if(!$result)
    {
        logMsg("Could not create table: ". mysql_error());
        return;
    }
    
    $tmpFile = '/tmp/crontab.txt';
    $output = shell_exec('crontab -l');
    file_put_contents($tmpFile, $output);
    $newCron = '* * * * * cd ' . dirname(__FILE__) . '; php ' .  dirname(__FILE__) . '/openvpn-cron.php';
    if( strpos($output,$newCron) !== false) 
    { 
        //Do nothing
    }
    else
    {
        file_put_contents($tmpFile, $output.$newCron.PHP_EOL);
        echo exec('crontab '.$tmpFile);
    }
}

install();
?>