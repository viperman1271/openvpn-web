<?php

require 'config/mysql.php';
require 'config/openvpn.php';

require 'include/page_vars.php';

require 'include/page_start.php';
require 'include/page_end.php';

require 'functions/functions.php';

$stats = parseLog("/etc/openvpn/status.log");

mysql_connect(get_mysql_host(), get_mysql_user(), get_mysql_pass());
mysql_select_db(get_mysql_db());
$result = mysql_query("SELECT sum(BytesSent) as 'totalsend', sum(BytesReceived) as 'totalreceived' FROM stats WHERE LastRef LIKE '%".date("M")." ".date("j")."%".date("Y")."%'") or die(mysql_error());

echo $page_start;
?>

    <section class="content-header">
      <h1>Home</h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
      </ol>
    </section>
    <br/>
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
<?php
echo '                    <h3>' . count($stats['users']) . '</h3>';
?>
                    <p>Connected Clients</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="openvpn-status.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
<?php
    $data_sent = 0;
    $data_recv = 0;
    while ($row = mysql_fetch_assoc($result)) 
    {
        $data_sent = sizeformat($row['totalsend']);
        $data_recv = sizeformat($row['totalreceived']);
    }
echo '                    <h3>' . $data_sent . '</h3>';
?>
                    <p>Download</p>
                </div>
                <div class="icon">
                    <i class="fa fa-download"></i>
                </div>
                <a href="openvpn-status.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
<?php
echo '
                    <h3>' . $data_recv . '</h3>
';
?>
                    <p>Upload</p>
                </div>
                <div class="icon">
                    <i class="fa fa-upload"></i>
                </div>
                <a href="openvpn-status.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

<?php

echo $page_end;

?>
