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

//////////////////////////////////////////////////////////////

$stats = parseLog("/etc/openvpn/status.log");
echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <title>OpenVPN Status</title>
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
            </table>
        </center>
    </body>
</html>';
?>

