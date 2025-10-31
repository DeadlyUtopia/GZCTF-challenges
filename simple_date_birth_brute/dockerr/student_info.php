<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>学生学籍信息查询</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; }
        .query-box { border: 1px solid #ccc; padding: 20px; border-radius: 5px; }
        h2 { text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #2196F3; color: white; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #0b7dda; }
    </style>
</head>
<body>
    <div class="query-box">
        <h2>学生学籍信息查询</h2>
        <form method="post" action="student_info_query.php">
            <div class="form-group">
                <label for="name">姓名</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="id_card">身份证号码</label>
                <input type="text" id="id_card" name="id_card" required>
            </div>
            <button type="submit">查询</button>
        </form>
    </div>
</body>
</html>
    