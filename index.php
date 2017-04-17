<?php

require 'include/page_vars.php';

require 'include/page_start.php';
require 'include/page_end.php';

require 'functions/functions.php';

echo $page_start;
?>

    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
<?php
$stats = parseLog("/etc/openvpn/status.log");
echo '
                    <h3>' . count($stats['users']) . '</h3>
';
?>
                    <p>Connected Clients</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="openvpn-status.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

<?php

echo $page_end;

?>
