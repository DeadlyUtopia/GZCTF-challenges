<?php
// 1. 响应变量读取请求（给 JS 返回根目录外的变量）
if (isset($_GET['v']) && $_GET['v'] === '1') {
    $var = trim(file_get_contents('/opt/ctf/var.txt'));
    echo $var;
    exit;
}

// 2. 响应 flag 请求
if (isset($_GET['flag']) && !empty($_GET['flag'])) {
    $requested_flag = $_GET['flag'];
    $real_var = trim(file_get_contents('/opt/ctf/var.txt'));
    // 验证请求的 flag 名称是否正确（防止恶意访问）
    if ($requested_flag === $real_var) {
        // 直接加载根目录外的 flag 模板，动态输出 flag
        require '/opt/ctf/flag-template.php';
    } else {
        echo '403 Forbidden: Invalid request.';
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Game | CTF Challenge</title>
    <style>
        body { margin: 0; padding: 20px; background: #111; color: #fff; font-family: Arial, sans-serif; text-align: center; }
        #gameCanvas { border: 2px solid #333; background: #222; }
        #scoreboard { font-size: 24px; margin-bottom: 10px; }
        #message { font-size: 20px; margin: 10px 0; color: #4CAF50; }
    </style>
</head>
<body>
    <h1>贪吃蛇游戏</h1>
    <div id="scoreboard">分数: 0</div>
    <canvas id="gameCanvas" width="400" height="400"></canvas>
    <div id="message">按下任意键开始游戏</div>
    <script src="snake.js"></script>
</body>
</html>