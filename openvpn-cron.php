<?php
//////////////////////////////////////////////////////////////
// PHP Script that will log the information from the OpenVPN
// status log and store it into the database
//
// Retrieved from on 29-12-2016: 
// http://jeroen.pro/2011/09/openvpn-stats-page-using-php/
//////////////////////////////////////////////////////////////

require 'config/mysql.php';
require 'config/openvpn.php';

require 'functions/functions.php';

//////////////////////////////////////////////////////////////

function cron() 
{
    $conn = mysql_connect(get_mysql_host(), get_mysql_user(), get_mysql_pass());
    if(!$conn) 
    {
        logMsg(mysql_error());
        return;
    }
    $selectDb = mysql_select_db(get_mysql_db());
    if(!$selectDb)
    {
        logMsg(mysql_error());
        return;
    }

    $stats = parseLog(get_openvpn_status());
    foreach($stats['users'] as $user)
    {
        if($user['CommonName'] != "UNDEF")
        {
           $result = mysql_query("UPDATE stats SET updated = '".time()."', VirtualAddress = '".$user['VirtualAddress']."', BytesSent='".$user['BytesSent']."', BytesReceived='".$user['BytesReceived']."', LastRef='".$user['LastRef']."' WHERE CommonName='".$user['CommonName']."' AND Since='".$user['Since']."'") or die(mysql_error());
           if (mysql_affected_rows() == 0) 
           {
              $result = mysql_query("insert into stats (CommonName, RealAddress, BytesReceived, BytesSent, Since, VirtualAddress, LastRef) values ('".$user['CommonName']."', '".$user['RealAddress']."', '".$user['BytesReceived']."', '".$user['BytesSent']."', '".$user['Since']."', '".$user['VirtualAddress']."', '".$user['LastRef']."')");
           }
        }
    }
}

cron();
?>