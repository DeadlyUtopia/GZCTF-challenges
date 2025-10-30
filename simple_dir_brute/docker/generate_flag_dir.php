<?php
// 生成随机目录
$dir1 = rand(30, 100);
$dir2 = rand(1, 100);
$flagDir = "/var/www/html/{$dir1}/{$dir2}/";

// 创建目录
mkdir($flagDir, 0755, true);

// 写入flag
$flagContent = getenv('GZCTF_FLAG') ?: 'flag{If you see me, it means the flag was not passed in correctly.}';
file_put_contents($flagDir . 'flag.txt', $flagContent);

// 记录正确目录（仅root可见）
file_put_contents('/var/www/html/flag_dir.txt', "{$dir1}/{$dir2}/");