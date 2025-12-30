<?php
header('Content-Type: application/json; charset=utf-8');

// 数据库配置
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '123456';
$db_name = 'ctf';
$db_table = 'ctf1';

// 创建连接
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 检查连接
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed'
    ]);
    exit;
}

$response = [
    'success' => false,
    'data' => [],
    'error' => null
];

// 处理搜索请求
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    
    // SQL查询语句（存在SQL注入漏洞）
    $sql = "select id,username,password from ".$db_table." where username !='flag' and id = '".$id."'";
    
    $query_result = $conn->query($sql);
    
    if ($query_result) {
        $data = [];
        while ($row = $query_result->fetch_assoc()) {
            $data[] = $row;
        }
        
        if (empty($data)) {
            $response['error'] = '未找到匹配的数据';
        } else {
            $response['success'] = true;
            $response['data'] = $data;
        }
    } else {
        $response['error'] = '查询错误: ' . $conn->error;
    }
} else {
    $response['error'] = '请输入用户ID';
}

$conn->close();

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
