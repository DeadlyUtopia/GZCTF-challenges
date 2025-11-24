<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Index</title>
    <style>
        body { font-family: "Courier New", monospace; text-align: center; margin-top: 120px; background: #f5f5f5; }
        .container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        h1 { color: #2c3e50; font-size: 28px; }
        .time { color: #95a5a6; font-size: 14px; margin-top: 10px; line-height: 1.6; }
        .local-time { color: #3498db; font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Not here</h1>
        <div class="time">Server Time: <span id="server-time"><?php echo date('Y-m-d H:i:s'); ?></span></div>
        <div class="time">Local Time: <span id="local-time" class="local-time"></span></div>
    </div>

    <script>
        // 1. 获取浏览器本地时间（自动适配当前时区）
        function updateLocalTime() {
            const now = new Date();
            // 格式化时间：年-月-日 时:分:秒（带时区标识）
            const options = { 
                year: 'numeric', month: '2-digit', day: '2-digit',
                hour: '2-digit', minute: '2-digit', second: '2-digit',
                timeZoneName: 'short' // 显示时区（如 GMT+8、CST）
            };
            const localTimeStr = now.toLocaleString('en-CA', options).replace(/\//g, '-');
            document.getElementById('local-time').textContent = localTimeStr;
        }

        // 2. 实时刷新（1秒更新一次本地时间）
        updateLocalTime(); // 初始化显示
        setInterval(updateLocalTime, 1000); // 每秒刷新

        // 3. 同步更新服务器时间（避免页面刷新，用JS模拟递增）
        function updateServerTime() {
            const serverTimeEl = document.getElementById('server-time');
            const currentServerTime = new Date(serverTimeEl.textContent);
            currentServerTime.setSeconds(currentServerTime.getSeconds() + 1);
            // 格式化服务器时间，保持与PHP输出一致
            const formatted = currentServerTime.toISOString().slice(0, 19).replace('T', ' ');
            serverTimeEl.textContent = formatted;
        }
        setInterval(updateServerTime, 1000);
    </script>
</body>
</html>