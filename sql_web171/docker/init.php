<?php
// 防止网页直接访问此文件
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Access Denied');
}
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '123456';
$db_name = 'ctf';
$db_table = 'ctf1';

// 从环境变量获取FLAG
$flag = getenv('GZCTF_FLAG') ?: 'flag{default_flag}';

// 创建连接（不指定数据库）
$conn = new mysqli($db_host, $db_user, $db_pass);

if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 创建数据库
$sql = "CREATE DATABASE IF NOT EXISTS ".$db_name;
if ($conn->query($sql) !== TRUE) {
    die("创建数据库失败: " . $conn->error);
}

// 选择数据库
$conn->select_db($db_name);

// 创建用户表
$sql = "CREATE TABLE IF NOT EXISTS ".$db_table." (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL
)";

if ($conn->query($sql) !== TRUE) {
    die("创建表失败: " . $conn->error);
}

// 清空现有数据
$conn->query("TRUNCATE TABLE ".$db_table);

// 生成随机字符串函数
function generateRandomString($length, $type = 'alphanum') {
    if ($type === 'alpha') {
        // 4位随机大小写字母
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    } else {
        // 8位随机字母大小写加数字
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    }
    
    $result = '';
    $charLen = strlen($chars);
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[random_int(0, $charLen - 1)];
    }
    return $result;
}

// 生成测试数据
$test_data = [];
for ($i = 0; $i < 36; $i++) {
    $test_data[] = [
        generateRandomString(4, 'alpha'),
        generateRandomString(8, 'alphanum')
    ];
}

// 添加flag用户
$test_data[] = ['flag', $flag];

// 插入数据
foreach ($test_data as $data) {
    $username = $conn->real_escape_string($data[0]);
    $password = $conn->real_escape_string($data[1]);
    $sql = "INSERT INTO ".$db_table." (username, password) VALUES ('$username', '$password')";
    
    if ($conn->query($sql) !== TRUE) {
        die("插入数据失败: " . $conn->error);
    }
}

echo "✓ 数据库初始化成功！<br>";
echo "✓ 数据库名: ".$db_name."<br>";
echo "✓ 表名: ".$db_table."<br>";
echo "✓ FLAG: ".$flag."<br>";
echo "✓ 已插入 " . count($test_data) . " 条测试数据<br><br>";
echo "现在可以访问 index.php 进行查询";

$conn->close();
?>
