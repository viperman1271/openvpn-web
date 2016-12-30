<?php

$openvpn_status = '/etc/openvpn/status.log';
$openvpn_log = '/etc/openvpn/openvpn.log';

function get_openvpn_status()
{
    global $openvpn_status;
    return $openvpn_status;
}

function get_openvpn_log()
{
    global $openvpn_log;
    return $openvpn_log;
}

?>
