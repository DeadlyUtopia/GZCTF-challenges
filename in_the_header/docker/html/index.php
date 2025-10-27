<?php
$flag = file_get_contents('/tmp/flag');
header("Flag: {$flag}");
header("X-Powered-By: PHP/7.4");
echo "where is flag?";
?>