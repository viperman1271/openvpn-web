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

echo'
        <div class="box">
            <div class="box-header">
              <h3 class="box-title">Active Users</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <table class="table table-striped">
                    <tr>
                        <th>Common Name</th>
                        <th>Real Address</th>
                        <th>Virtual Address</th>
                        <th>Logged In At</th>
                        <th>Received</th>
                        <th>Sent</th>
                        <th>Last Activity</th>
                    </tr>
';

foreach($stats['users'] as $user)
{  
    echo '
                    <tr>
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
            </div>
        </div>';
            
echo $page_end;
?>