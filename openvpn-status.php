<?php
//////////////////////////////////////////////////////////////
// PHP script that will parse the openvpn status file
// and display the result in HTML format
//
// Modified version from: 
// http://jeroen.pro/2011/09/openvpn-stats-page-using-php/
//////////////////////////////////////////////////////////////
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
$stats = parseLog("/etc/openvpn/status.log");
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<body>
    <center>';
echo '
        <h3>Active Users</h3> 
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
    echo '
            <tr bgcolor="#eeeeee">
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
    </center>
</body>
</html>';
?>

