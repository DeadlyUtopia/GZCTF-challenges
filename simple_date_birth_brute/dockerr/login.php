<?php
session_start();

// 配置非学生账号密码
$adminConfig = [
    'username' => 'admin',
    'password' => '340855abb119dacebde56b36524d574f089e85f6f50daf27f058a9043cea4ed7'
];

// 获取用户输入
$role = $_POST['role'] ?? '';
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// 学生登录逻辑（验证学号与身份证对应关系）
if ($role === 'student') {
    $mappingFile = '/var/www/data/student_mapping.json';
    if (!file_exists($mappingFile)) {
        die("系统错误：数据文件缺失");
    }
    
    $students = json_decode(file_get_contents($mappingFile), true);
    $isValid = false;
    
    foreach ($students as $s) {
        if ($s['student_id'] === $username && $s['id_card'] === $password) {
            $isValid = true;
            break;
        }
    }
    
    if ($isValid) {
        echo "登录成功！Flag: " . getenv('GZCTF_FLAG');
    } else {
        echo "登录失败：学号与身份证号不匹配";
    }
}
// 非学生登录逻辑
else {
    if ($username === $adminConfig['username'] && $password === $adminConfig['password']) {
        echo "登录成功（flag is not here!）";
    } else {
        echo "登录失败：账号或密码错误";
    }
}
?>
    