<?
//////////////////////////////////////////////////////////////
// PHP Script that will log the information from the OpenVPN
// status log and store it into the database
//
// Retrieved from on 29-12-2016: 
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

//////////////////////////////////////////////////////////////
    
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

//////////////////////////////////////////////////////////////

mysql_connect('localhost', 'openvpn', 'openvpn');
mysql_select_db('openvpn');

$stats = parseLog("/etc/openvpn/openvpn.log");
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