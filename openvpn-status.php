<?php
//////////////////////////////////////////////////////////////
// PHP script that will parse the openvpn status file
// and display the result in HTML format
//
// Modified version from: 
// http://jeroen.pro/2011/09/openvpn-stats-page-using-php/
//////////////////////////////////////////////////////////////

require 'config/mysql.php';
require 'config/openvpn.php';

require 'functions/functions.php';

require 'include/page_start.php';
require 'include/page_end.php';

echo $page_start;

//////////////////////////////////////////////////////////////

$stats = parseLog("/etc/openvpn/status.log");
echo '
            <h3>Active Users</h3> 
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
    echo '
                <tr bgcolor="#eeeeee">
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
            </table>';
            
echo $page_end;
?>