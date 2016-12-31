<?php
//////////////////////////////////////////////////////////////
// PHP script that will parse the openvpn status file
// and display the result in HTML format
//
// Retrieved from on 29-12-2016: 
// http://jeroen.pro/2011/09/openvpn-stats-page-using-php/
//////////////////////////////////////////////////////////////

require 'config/mysql.php';
require 'config/openvpn.php';

require 'functions/functions.php';

function history()
{
    $stats = parseLog(get_openvpn_status());
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><title>OpenVPN Usage History</title><body><center>';
    echo '<h3>Active Users</h3>
    <table>
        <tr style="font-weight: bold;" bgcolor="#888888">
            <td>Common Name</td>
            <td>Real Address</td>
            <td>Virtual Address</td>
            <td>Logged In At</td>
            <td>Received</td>
            <td>Sent</td>
            <td>Last Activity</td>
        </tr>
    ';
    foreach($stats['users'] as $user)
    {  
        echo '<tr bgcolor="#eeeeee">
            <td>'.$user['CommonName'].'</td>
            <td>'.$user['RealAddress'].'</td>
            <td>'.$user['VirtualAddress'].'</td>
            <td>'.$user['Since'].'</td>
            <td>'.$user['DataSent'].'</td>
            <td>'.$user['DataReceived'].'</td>
            <td>'.$user['LastRef'].'</td>
        </tr>';
     }
    echo '
    </table>
    <br>
    <center>Live status Last Updated: <b>'.$stats['updated'].'
    </b>
    ';
    mysql_connect(get_mysql_host(), get_mysql_user(), get_mysql_pass());
    mysql_select_db(get_mysql_db());
    echo '<h3>Bandwidth Totals - All Time</h3>
    <table>
        <tr style="font-weight: bold;" bgcolor="#888888">
            <td>Common Name</td>
            <td>Received</td>
            <td>Sent</td>
        </tr>
     
    ';
    $result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) 
    {
        echo '<tr bgcolor="#eeeeee">
            <td>Total</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
            </tr>';
    }
    unset($result);
    $result = mysql_query("SELECT CommonName, sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats GROUP BY CommonName ORDER BY CommonName ASC") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) 
    {
        echo '<tr bgcolor="#eeeeee">
            <td>'.$row['CommonName'].'</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
           </tr>';
    }
    echo '</table>';

    unset($result);

    echo '<h3>Bandwidth Totals - Today</h3>
    <table>
        <tr style="font-weight: bold;" bgcolor="#888888">
            <td>Common Name</td>
            <td>Received</td>
            <td>Sent</td>
        </tr>';
    $result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("M")." ".date("j")."%".date("Y")."%'") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) 
    {
        echo '<tr bgcolor="#eeeeee">
            <td>Total</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
        </tr>';
    }
    unset($result);
    $result = mysql_query("SELECT CommonName, sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("M")." ".date("j")."%".date("Y")."%' GROUP BY CommonName ") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) 
    {
        echo '<tr bgcolor="#eeeeee">
            <td>'.$row['CommonName'].'</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
        </tr>';
    }
    echo '</table>';

    unset($result);
    echo '<h3>Bandwidth Totals - Last 7 Days</h3>
    <table>
        <tr style="font-weight: bold;" bgcolor="#888888">
            <td>Common Name</td>
            <td>Bytes Received</td>
            <td>Bytes Sent</td>
        </tr>';
    $like = "LastRef LIKE '%".date("M")." ".date("j")."%".date("Y")."%'";
    for ($i=0; $i<7; $i++)
    {
        $like .= " OR LastRef LIKE '%".date("M",strtotime("-".($i+1)." day"))." ".date("j",strtotime("-".($i+1)." day"))."%".date("Y",strtotime("-".($i+1)." day"))."%'";
    }
    $result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE ".$like."") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) 
    {
        echo '<tr bgcolor="#eeeeee">
            <td>Total</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
        </tr>';
    }
    unset($result);
    $result = mysql_query("SELECT CommonName, sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE ".$like." GROUP BY CommonName ") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) 
    {
        echo '<tr bgcolor="#eeeeee">
            <td>'.$row['CommonName'].'</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
        </tr>';
    }
    echo '</table>';

    unset($result);

    echo '<h3>Bandwidth Totals - This Month</h3>
    <table>
        <tr style="font-weight: bold;" bgcolor="#888888">
            <td>Common Name</td>
            <td>Bytes Received</td>
            <td>Bytes Sent</td>
        </tr>';
    $result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("M")."%".date("Y")."%'") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) 
    {
        echo '<tr bgcolor="#eeeeee">
            <td>Total</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
        </tr>';
    }
    unset($result);
    $result = mysql_query("SELECT CommonName, sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("M")."%".date("Y")."%' GROUP BY CommonName ") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) 
    {
        echo '<tr bgcolor="#eeeeee">
            <td>'.$row['CommonName'].'</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
        </tr>';
    }
    echo '</table>';

    unset($result);

    echo '<h3>Bandwidth Totals - This Year</h3>
    <table>
        <tr style="font-weight: bold;" bgcolor="#888888">
            <td>Common Name</td>
            <td>Bytes Received</td>
            <td>Bytes Sent</td>
        </tr>';
    $result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("Y")."%'") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        echo '<tr bgcolor="#eeeeee">
            <td>Total</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
        </tr>';
    }
    unset($result);
    $result = mysql_query("SELECT CommonName, sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("Y")."%' GROUP BY CommonName ") or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        echo '<tr bgcolor="#eeeeee">
            <td>'.$row['CommonName'].'</td>
            <td>'.sizeformat($row['totalreceived']).'</td>
            <td>'.sizeformat($row['totalsend']).'</td>
        </tr>';
    }
    echo '</table>';
    echo '</center></body></html>';
}

history();
?>
