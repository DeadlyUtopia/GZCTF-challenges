<?php
// 密码缓存逻辑（使用 /var/www/password_cache 目录）
$cacheFile = '/var/www/password_cache/valid_password.txt';
$dictFile = '/var/www/html/passwords.txt';
$validPassword = '';

// 首次启动生成密码，之后读取缓存
if (file_exists($cacheFile)) {
    $validPassword = trim(file_get_contents($cacheFile));
} else {
    $passwords = file($dictFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $validPassword = $passwords[array_rand($passwords)];
    file_put_contents($cacheFile, $validPassword);
}

$authHeader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

if (strpos($authHeader, 'Basic ') !== 0) {
    header('WWW-Authenticate: Basic realm="登录"');
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

$decoded = base64_decode(substr($authHeader, 6), true);
if ($decoded === false) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}
list($user, $pass) = explode(':', $decoded, 2) + [null, null];
$user = trim((string)$user);
$pass = trim((string)$pass);

if ($user === 'admin' && $pass === $validPassword) {
    echo getenv('GZCTF_FLAG') ?: 'flag{If you see me, it means the flag was not passed in correctly.}';
} else {
    header('WWW-Authenticate: Basic realm="密码错误"');
    header('HTTP/1.1 401 Unauthorized');
    exit;
}
?>
