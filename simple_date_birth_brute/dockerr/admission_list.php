<?php
// 仅允许下载处理后的文件
$processedFile = '/var/www/html/list_processed.xlsx';

if (file_exists($processedFile)) {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="录取名单.xlsx"');
    header('Content-Length: ' . filesize($processedFile));
    readfile($processedFile);
    exit;
} else {
    die("文件不存在");
}
?>