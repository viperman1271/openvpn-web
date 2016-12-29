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
<?
//////////////////////////////////////////////////////////////
// PHP script that will parse the openvpn status file
// and display the result in HTML format
//
// Retrieved from on 29-12-2016: 
// http://jeroen.pro/2011/09/openvpn-stats-page-using-php/
//////////////////////////////////////////////////////////////
mysql_connect('localhost', 'openvpn', 'openvpn');
mysql_select_db('openvpn');
function parseLog ($log) {
    $handle = fopen($log, "r");
    $uid = 0;
        while (!feof($handle)) {
            $buffer = fgets($handle, 4096);
            unset($match);
            if (ereg("^Updated,(.+)", $buffer, $match)) {
                $status['updated'] = $match[1];
            }
            if (preg_match("/^(.+),(\d+\.\d+\.\d+\.\d+\:\d+),(\d+),(\d+),(.+)$/", $buffer, $match)) {
                if ($match[1] <> "Common Name") {
                    $cn = $match[1];
 
                    $userlookup[$match[2]] = $uid;
                    $status['users'][$uid]['CommonName'] = $match[1];
                    $status['users'][$uid]['RealAddress'] = $match[2];
                    $status['users'][$uid]['BytesReceived'] = $match[3];
                    $status['users'][$uid]['BytesSent'] = $match[4];
                    $status['users'][$uid]['Since'] = $match[5];
                    $uid++;
                }
            }
            if (preg_match("/^(\d+\.\d+\.\d+\.\d+),(.+),(\d+\.\d+\.\d+\.\d+\:\d+),(.+)$/", $buffer, $match)) {
                if ($match[1] <> "Virtual Address") {
                    $address = $match[3];
                    $uid = $userlookup[$address];
                    $status['users'][$uid]['VirtualAddress'] = $match[1];
                    $status['users'][$uid]['LastRef'] = $match[4];
                }
            }
        }
        fclose($handle);
        return($status);
    }
    function sizeformat($bytesize){
        $i=0;
        while(abs($bytesize) >= 1024){
            $bytesize=$bytesize/1024;
            $i++;
            if($i==4) break;
        }
        $units = array("Bytes","KB","MB","GB","TB");
        $newsize=round($bytesize,2);
        return("$newsize $units[$i]");
    }
$stats = parseLog("/etc/openvpn/openvpn-status.log");
foreach($stats['users'] as $user)
{
    if($user['CommonName'] != "UNDEF")
    {
       $result = mysql_query("UPDATE stats SET updated = '".time()."', VirtualAddress = '".$user['VirtualAddress']."', BytesSent='".$user['BytesSent']."', BytesReceived='".$user['BytesReceived']."', LastRef='".$user['LastRef']."' WHERE CommonName='".$user['CommonName']."' AND Since='".$user['Since']."'") or die(mysql_error());
       echo mysql_affected_rows();
       if (mysql_affected_rows()==0) {
          $result = mysql_query("insert into stats (CommonName, RealAddress, BytesReceived, BytesSent, Since, VirtualAddress, LastRef) values ('".$user['CommonName']."', '".$user['RealAddress']."', '".$user['BytesReceived']."', '".$user['BytesSent']."', '".$user['Since']."', '".$user['VirtualAddress']."', '".$user['LastRef']."')");
       }
    }
}
?>
* * * * * cd [cron location]; php cron.php
<?php
function parseLog ($log) {
    $handle = fopen($log, "r");
    $uid = 0;
        while (!feof($handle)) {
            $buffer = fgets($handle, 4096);
            unset($match);
            if (ereg("^Updated,(.+)", $buffer, $match)) {
                $status['updated'] = $match[1];
            }
            if (preg_match("/^(.+),(\d+\.\d+\.\d+\.\d+\:\d+),(\d+),(\d+),(.+)$/", $buffer, $match)) {
                if ($match[1] <> "Common Name") {
                    $cn = $match[1];
                    $userlookup[$match[2]] = $uid;
                    $status['users'][$uid]['CommonName'] = $match[1];
                    $status['users'][$uid]['RealAddress'] = $match[2];
                    $status['users'][$uid]['BytesReceived'] = sizeformat($match[3]);
                    $status['users'][$uid]['BytesSent'] = sizeformat($match[4]);
                    $status['users'][$uid]['Since'] = $match[5];
                    $uid++;
                }
            }
            if (preg_match("/^(\d+\.\d+\.\d+\.\d+),(.+),(\d+\.\d+\.\d+\.\d+\:\d+),(.+)$/", $buffer, $match)) {
                if ($match[1] <> "Virtual Address") {
                    $address = $match[3];
                    $uid = $userlookup[$address];
                    $status['users'][$uid]['VirtualAddress'] = $match[1];
                    $status['users'][$uid]['LastRef'] = $match[4];
                }
            }
        }
        fclose($handle);
        return($status);
    }
    function sizeformat($bytesize){
        $i=0;
        while(abs($bytesize) >= 1024){
            $bytesize=$bytesize/1024;
            $i++;
            if($i==4) break;
        }
        $units = array("Bytes","KB","MB","GB","TB");
        $newsize=round($bytesize,2);
        return("$newsize $units[$i]");
    }
$stats = parseLog("/etc/openvpn/openvpn-status.log");
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><body><center>';
echo '<h3>logged in users</h3><br>
<table>
    <tr style="font-weight: bold;" bgcolor="#888888">
        <td>Common Name</td>
        <td>Real Address</td>
        <td>Virtual Address</td>
        <td>Logged In At</td>
        <td>Bytes Sent</td>
        <td>Bytes Received</td>
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
        <td>'.$user['BytesReceived'].'</td>
        <td>'.$user['BytesSent'].'</td>
        <td>'.$user['LastRef'].'</td>
    </tr>';
 }
echo '
</table>
<br>
<center>Live status Last Updated: <b>'.$stats['updated'].'
</b>
';
mysql_connect('localhost', 'openvpn', 'openvpn');
mysql_select_db('openvpn');
echo '<h3>bandwidth totals - all time</h3><br>
<table>
    <tr style="font-weight: bold;" bgcolor="#888888">
        <td>Common Name</td>
        <td>Bytes Sent</td>
        <td>Bytes Received</td>
    </tr>
 
';
$result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats") or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    echo '<tr bgcolor="#eeeeee">
        <td>Total</td>
        <td>'.sizeformat($row['totalreceived']).'</td>
        <td>'.sizeformat($row['totalsend']).'</td>
        </tr>';
}
unset($result);
$result = mysql_query("SELECT CommonName, sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats GROUP BY CommonName ORDER BY CommonName ASC") or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    echo '<tr bgcolor="#eeeeee">
        <td>'.$row['CommonName'].'</td>
        <td>'.sizeformat($row['totalreceived']).'</td>
        <td>'.sizeformat($row['totalsend']).'</td>
       </tr>';
}
echo '
</table>
';
 
 
unset($result);
echo '<h3>bandwidth totals - Today</h3><br>
<table>
    <tr style="font-weight: bold;" bgcolor="#888888">
        <td>Common Name</td>
        <td>Bytes Sent</td>
        <td>Bytes Received</td>
    </tr>
 
';
$result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("M")." ".date("j")."%".date("Y")."%'") or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    echo '<tr bgcolor="#eeeeee">
        <td>Total</td>
        <td>'.sizeformat($row['totalreceived']).'</td>
        <td>'.sizeformat($row['totalsend']).'</td>
    </tr>';
}
unset($result);
$result = mysql_query("SELECT CommonName, sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("M")." ".date("j")."%".date("Y")."%' GROUP BY CommonName ") or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    echo '<tr bgcolor="#eeeeee">
        <td>'.$row['CommonName'].'</td>
        <td>'.sizeformat($row['totalreceived']).'</td>
        <td>'.sizeformat($row['totalsend']).'</td>
    </tr>';
}
echo '
</table>
';
 
 
unset($result);
echo '<h3>bandwidth totals - last 7 days</h3><br>
<table>
    <tr style="font-weight: bold;" bgcolor="#888888">
        <td>Common Name</td>
        <td>Bytes Sent</td>
        <td>Bytes Received</td>
    </tr>
 
';
$like = "LastRef LIKE '%".date("M")." ".date("j")."%".date("Y")."%'";
for ($i=0; $i<7; $i++)
{
    $like .= " OR LastRef LIKE '%".date("M",strtotime("-".($i+1)." day"))." ".date("j",strtotime("-".($i+1)." day"))."%".date("Y",strtotime("-".($i+1)." day"))."%'";
}
$result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE ".$like."") or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    echo '<tr bgcolor="#eeeeee">
        <td>Total</td>
        <td>'.sizeformat($row['totalreceived']).'</td>
        <td>'.sizeformat($row['totalsend']).'</td>
    </tr>';
}
unset($result);
$result = mysql_query("SELECT CommonName, sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE ".$like." GROUP BY CommonName ") or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    echo '<tr bgcolor="#eeeeee">
        <td>'.$row['CommonName'].'</td>
        <td>'.sizeformat($row['totalreceived']).'</td>
        <td>'.sizeformat($row['totalsend']).'</td>
    </tr>';
}
echo '
</table>
';
 
unset($result);
echo '<h3>bandwidth totals - This month</h3><br>
<table>
    <tr style="font-weight: bold;" bgcolor="#888888">
        <td>Common Name</td>
        <td>Bytes Sent</td>
        <td>Bytes Received</td>
    </tr>
 
';
$result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("M")."%".date("Y")."%'") or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    echo '<tr bgcolor="#eeeeee">
        <td>Total</td>
        <td>'.sizeformat($row['totalreceived']).'</td>
        <td>'.sizeformat($row['totalsend']).'</td>
    </tr>';
}
unset($result);
$result = mysql_query("SELECT CommonName, sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("M")."%".date("Y")."%' GROUP BY CommonName ") or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    echo '<tr bgcolor="#eeeeee">
        <td>'.$row['CommonName'].'</td>
        <td>'.sizeformat($row['totalreceived']).'</td>
        <td>'.sizeformat($row['totalsend']).'</td>
    </tr>';
}
echo '
</table>
';
 
unset($result);
echo '<h3>bandwidth totals - This year</h3><br>
<table>
    <tr style="font-weight: bold;" bgcolor="#888888">
        <td>Common Name</td>
        <td>Bytes Sent</td>
        <td>Bytes Received</td>
    </tr>
 
';
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
echo '
</table>
';
echo '</center></body></html>';
?>
