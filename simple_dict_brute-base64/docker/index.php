<?php
// 密码保存的文件路径（容器内路径，权限足够）
$passwordFile = '/var/www/html/valid_password.txt';
$dictFile = '/var/www/html/passwords.txt';
$validPassword = '';

// 关键逻辑：判断密码文件是否存在
if (file_exists($passwordFile)) {
    // 若存在，直接从文件读取密码（trim() 去除可能的空行）
    $validPassword = trim(file_get_contents($passwordFile));
} else {
    // 若不存在（容器启动后第一次访问），随机抽取密码并写入文件
    $passwords = file($dictFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $validPassword = $passwords[array_rand($passwords)];
    // 写入文件（确保容器内该路径有写入权限，一般默认有）
    file_put_contents($passwordFile, $validPassword);
    // 日志输出，方便查看容器启动时的密码
    error_log("Password: {$validPassword}");
}

// 认证逻辑
$authHeader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
if (strpos($authHeader, 'Basic ') !== 0) {
    header('WWW-Authenticate: Basic realm="登录"');
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

// 解码并验证
$decoded = base64_decode(substr($authHeader, 6), true);
if ($decoded === false) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}
list($user, $pass) = explode(':', $decoded, 2) + [null, null];
$user = trim((string)$user);
$pass = trim((string)$pass);

if ($user === 'admin' && $pass === $validPassword) {
    echo getenv('GZCTF_FLAG') ?: 'flag{local_http_success}';
} else {
    header('WWW-Authenticate: Basic realm="密码错误"');
    header('HTTP/1.1 401 Unauthorized');
    exit;
}
?>