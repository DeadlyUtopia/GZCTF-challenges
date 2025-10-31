<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>学校统一登录平台</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; }
       .login-box { border: 1px solid #ccc; padding: 20px; border-radius: 5px; }
        h2 { text-align: center; }
       .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #4CAF50; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #45a049; }
       .links { margin-top: 20px; text-align: center; }
        a { display: block; margin: 5px 0; color: #0066cc; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>学校统一登录平台</h2>
        <form method="post" action="login.php">
            <div class="form-group">
                <label for="role">身份选择</label>
                <select id="role" name="role" required>
                    <option value="department">部门</option>
                    <option value="teacher">教师</option>
                    <option value="student" selected>学生</option>
                    <option value="visitor">访客</option>
                </select>
            </div>
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">登录</button>
        </form>
        <div class="links">
            <a href="admission_list.php">录取名单查询</a>
            <a href="student_info.php">学生学籍信息查询</a>
        </div>
    </div>
</body>
</html>
    