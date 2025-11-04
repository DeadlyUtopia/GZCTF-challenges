<?php
// 静默过滤：检测POST['c']中是否包含GZCTF_FLAG相关内容
$pattern = '/\$GZCTF_FLAG|\"GZCTF_FLAG\"|\'GZCTF_FLAG\'/i';
if (isset($_POST['c']) && preg_match($pattern, $_POST['c'])) {
    die();
}
?>