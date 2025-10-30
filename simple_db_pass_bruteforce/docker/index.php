<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FLAG 系统安装</title>
    <style>
        body {
            font-family: "Microsoft Yahei", sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>数据库配置</h2>
        <div class="form-group">
            <label for="a">数据库地址</label>
            <input type="text" id="a" placeholder="localhost">
        </div>
        <div class="form-group">
            <label for="p">端口</label>
            <input type="text" id="p" placeholder="3306">
        </div>
        <div class="form-group">
            <label for="d">数据库名</label>
            <input type="text" id="d" placeholder="ctf">
        </div>
        <div class="form-group">
            <label for="u">用户名</label>
            <input type="text" id="u" placeholder="root">
        </div>
        <div class="form-group">
            <label for="pass">密码</label>
            <input type="text" id="pass" placeholder="12345678">
        </div>
        <button onclick="submitForm()">确认无误，开始安装</button>
        <div id="result" class="result"></div>
    </div>

    <script>
        function submitForm() {
            const data = {
                a: document.getElementById('a').value,
                p: document.getElementById('p').value,
                d: document.getElementById('d').value,
                u: document.getElementById('u').value,
                pass: document.getElementById('pass').value
            };
            fetch('checkdb.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data)
            })
            .then(res => res.json())
            .then(data => {
                const resultEl = document.getElementById('result');
                resultEl.style.display = 'block';
                if (data.success) {
                    resultEl.className = 'result success';
                    resultEl.textContent = '成功连接数据库';
                } else {
                    resultEl.className = 'result error';
                    resultEl.textContent = '错误';
                }
            });
        }
    </script>
</body>
</html>