<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
if(isset($_POST['c'])){
    $c = $_POST['c'];
    eval($c);
    $s = ob_get_contents(); 
    ob_end_clean();
    $filtered = preg_replace("/[a-zA-Z0-9]/", "?", $s);
    echo $filtered;
}else{
    highlight_file(__FILE__);
    echo "你不觉得我和 flag 就是一对苦命鸳鸯吗 :D";
}
?>