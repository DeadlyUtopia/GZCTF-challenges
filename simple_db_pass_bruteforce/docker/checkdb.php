<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$passwordFile = '/etc/ctfconfig/password.txt';
if (!file_exists($passwordFile) || !is_readable($passwordFile)) {
    http_response_code(500);
    die(json_encode(['success' => false]));
}
$realPass = trim(file_get_contents($passwordFile));

$fixedParams = [
    'a' => 'localhost',
    'p' => '3306',
    'd' => 'ctf',
    'u' => 'root'
];

$postParams = [
    'a' => trim($_POST['a'] ?? ''),
    'p' => trim($_POST['p'] ?? ''),
    'd' => trim($_POST['d'] ?? ''),
    'u' => trim($_POST['u'] ?? ''),
    'pass' => trim($_POST['pass'] ?? '')
];

$isValid = true;
foreach ($fixedParams as $key => $value) {
    if ($postParams[$key] !== $value) {
        $isValid = false;
        break;
    }
}

if ($isValid && $postParams['pass'] === $realPass) {
    $flag = getenv('GZCTF_FLAG') ?: 'flag{If you see me, it means the FLAG was not passed in correctly.}';
    $response = [
        'success' => true,
        'msg' => '数据库连接成功',
        'flag' => $flag
    ];
} else {
    $response = [
        'success' => false,
        'msg' => '错误'
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>