<?php
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
?>
