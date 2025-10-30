<?php
$d1 = $_GET['d1'] ?? '';
$d2 = $_GET['d2'] ?? '';

// 验证是否为数字
if (!is_numeric($d1) || !is_numeric($d2)) {
    header('Location: /0/0/not-here.txt');
    exit;
}
$d1 = (int)$d1;
$d2 = (int)$d2;

// 允许访问的范围：d1=0-100，d2=0-100（用户可爆破此范围）
if ($d1 < 0 || $d1 > 100 || $d2 < 0 || $d2 > 100) {
    header('Location: /0/0/not-here.txt');
    exit;
}

// 验证该目录下是否存在flag
$flagFile = "/var/www/html/{$d1}/{$d2}/flag.txt";
if (file_exists($flagFile) && is_readable($flagFile)) {
    echo file_get_contents($flagFile);
} else {
    header('Location: /0/0/not-here.txt');
    exit;
}