<?php
// 读取映射关系（仅内部访问）
$mappingFile = '/var/www/data/student_mapping.json';
if (!file_exists($mappingFile)) {
    die("查询失败：系统数据缺失");
}

$students = json_decode(file_get_contents($mappingFile), true);
$name = trim($_POST['name'] ?? '');
$idCard = trim($_POST['id_card'] ?? '');

$found = false;
foreach ($students as $s) {
    if ($s['name'] === $name && $s['id_card'] === $idCard) {
        echo "查询成功！<br>";
        echo "学号：{$s['student_id']}<br>";
        echo "初始登录密码：{$s['id_card']}";
        $found = true;
        break;
    }
}

if (!$found) {
    echo "未查询到匹配的学生信息";
}
?>
    