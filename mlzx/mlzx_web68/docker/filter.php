<?php
// 静默过滤GZCTF_FLAG相关操作
$flag_pattern = '/\$GZCTF_FLAG|\"GZCTF_FLAG\"|\'GZCTF_FLAG\'/i';
if (isset($_POST['c']) && preg_match($flag_pattern, $_POST['c'])) {
    die();
}
?>