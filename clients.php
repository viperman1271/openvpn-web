<?php

require 'config/mysql.php';
require 'config/openvpn.php';

require 'functions/functions.php';

function clients()
{
    $files = array();
    foreach(glob("/etc/openvpn/easy-rsa/2.0/keys/*.csr") as $file)
    {
        $files[] = $file;
        $filename = substr($file, strrpos($file, '/') + 1);
        $filename = substr($filename, 0, strlen($filename) - 4);
        echo '
                    <tr>
                        <td>' . $filename . '</td>
                        <td>' . $file . '</td>
                    </tr>        
        ';
    }
}

require 'include/page_vars.php';

$page_clients = TRUE;

require 'include/page_start.php';
require 'include/page_end.php';

echo $page_start;

?>
        <section class="content-header">
            <h1>Clients</h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active"><i class="fa fa-clients"></i> Clients</li>
            </ol>
        </section>
        <br/>
        <div class="box">
            <div class="box-header">
              <h3 class="box-title">Clients</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <table class="table table-striped">
                    <tr>
                        <th>Client</th>
                        <th>Activated</th>
                    </tr>
<?php

clients();

?>
                </table>
            </div>
        </div>
<?php
echo $page_end;
?>
