<?php
// 读取环境变量中的flag并输出
$flag = getenv('GZCTF_FLAG');
if ($flag) {
    echo "$flag";
} else {
    echo "Flag not found! Please set GZCTF_FLAG environment variable.";
}
?>