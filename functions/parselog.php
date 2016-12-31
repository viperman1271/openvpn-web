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
                $status['users'][$uid]['DataReceived'] = sizeformat($match[3]);
                $status['users'][$uid]['DataSent'] = sizeformat($match[4]);
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
?>
