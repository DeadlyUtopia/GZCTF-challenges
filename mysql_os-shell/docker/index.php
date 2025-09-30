<?php
include("../sql-connections/sql-connect.php");
error_reporting(0);

$exec_result = "";
$exec_cmd = "";

// 处理按钮提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. 查看Linux所有用户
    if (isset($_POST['show_users'])) {
        $exec_cmd = "cat /etc/passwd";
        $exec_result = shell_exec("/usr/local/bin/exec-wrapper " . escapeshellarg($exec_cmd) . " 2>&1");
    }
    // 2. 查看当前运行MySQL的用户
    elseif (isset($_POST['show_mysql_user'])) {
        $exec_cmd = "ps aux | grep mysql | grep -v grep";
        $exec_result = shell_exec("/usr/local/bin/exec-wrapper " . escapeshellarg($exec_cmd) . " 2>&1");
    }
    // 3. 修改目录所属用户
    elseif (isset($_POST['chown'])) {
        $path = escapeshellarg($_POST['path']);
        $user = escapeshellarg($_POST['user']);
        $exec_cmd = "chown $user $path";
        $exec_result = shell_exec("/usr/local/bin/exec-wrapper " . escapeshellarg($exec_cmd) . " 2>&1");
        if (trim($exec_result) === '') {
            $exec_result = "✅ 修改成功";
        }
    }
    // 4. 设置 secure_file_priv 为 空字符串
    elseif (isset($_POST['set_secure_file_priv_empty'])) {
        $cmd = "pkill mysqld && sleep 2 && sed -i 's/^secure_file_priv=.*/secure_file_priv=\"\"/' /etc/mysql/my.cnf && nohup mysqld_safe > /dev/null 2>&1 &";
        $exec_cmd = $cmd;
        $exec_result = shell_exec("/usr/local/bin/exec-wrapper " . escapeshellarg($cmd) . " 2>&1");
        if (trim($exec_result) === '') {
            $exec_result = "✅ 设置为空字符串并重启MySQL成功";
        }
    }
    // 5. 查看 secure_file_priv 当前值
    elseif (isset($_POST['check_secure_file_priv'])) {
        $exec_cmd = "mysql -uroot -e \"SHOW VARIABLES LIKE 'secure_file_priv';\"";
        $exec_result = shell_exec("/usr/local/bin/exec-wrapper " . escapeshellarg($exec_cmd) . " 2>&1");
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Less-2 **Error Based- Intiger**</title>
<style>
body {
    background-color: #000000;
    color: #FFF;
    font-family: Arial, "微软雅黑", sans-serif;
    font-size: 14px;
}
.container {
    margin-top: 60px;
    text-align: center;
}
input[type=text] {
    width: 180px;
    padding: 6px;
    border-radius: 5px;
    border: none;
    font-size: 14px;
    margin-right: 8px;
}
button, input[type=submit] {
    background-color: #FF0000;
    border: none;
    color: white;
    padding: 8px 16px;
    margin: 6px 4px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}
button:hover, input[type=submit]:hover {
    background-color: #FF4500;
}
.result {
    margin-top: 20px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    background-color: #111;
    padding: 15px;
    border-radius: 6px;
    color: #99FF00;
    white-space: pre-wrap;
    text-align: left;
    font-family: Consolas, monospace;
    font-size: 13px;
}
.warning {
    color: #FFA500;
    margin-top: 10px;
    font-size: 13px;
}
</style>
</head>

<body>
<div class="container">
<div style="color:#FFF; font-size:23px; text-align:center;">
Welcome&nbsp;&nbsp;&nbsp;<font color="#FF0000"> Dhakkan </font><br>
<font size="3" color="#FFFF00">

<?php
if(isset($_GET['id']))
{
    $id=$_GET['id'];
    $fp=fopen('result.txt','a');
    fwrite($fp,'ID:'.$id."\n");
    fclose($fp);

    $sql="SELECT * FROM users WHERE id=$id LIMIT 0,1";
    $result=mysql_query($sql);
    $row = mysql_fetch_array($result);

    if($row)
    {
        echo "<font size='5' color= '#99FF00'>";
        echo 'Your Login name:'. $row['username'];
        echo "<br>";
        echo 'Your Password:' .$row['password'];
        echo "</font>";
    }
    else
    {
        echo '<font color= "#FFFF00">';
        print_r(mysql_error());
        echo "</font>";
    }
}
else
{
    echo "Please input the ID as parameter with numeric value";
}
?>

</font> </div>

<!-- 操作面板 -->
<form method="post" style="margin-top:40px;">
    <input type="submit" name="show_users" value="查看 Linux 所有用户" />
</form>

<form method="post" style="margin-top:10px;">
    <input type="submit" name="show_mysql_user" value="查看当前运行 MySQL 用户" />
</form>

<form method="post" style="margin-top:10px;">
    <input type="text" name="path" placeholder="目录路径" required />
    <input type="text" name="user" placeholder="用户名" required />
    <input type="submit" name="chown" value="修改目录所属用户" />
</form>

<form method="post" style="margin-top:10px;">
    <input type="submit" name="set_secure_file_priv_empty" value="将secure_file_priv设置为空字符串" />
</form>

<form method="post" style="margin-top:10px;">
    <input type="submit" name="check_secure_file_priv" value="查看当前 secure_file_priv 值" />
</form>

<?php if ($exec_cmd): ?>
    <div class="result">
        <strong>执行命令:</strong> <?php echo htmlspecialchars($exec_cmd); ?><br/><br/>
        <strong>执行结果:</strong><br/><?php echo htmlspecialchars($exec_result); ?>
        <div class="warning">⚠️ 若出现错误，可能是 MySQL 服务仍在重启中，请稍等几秒后刷新页面或重试。</div>
    </div>
<?php endif; ?>

<br><br><br>
<center><img src="../images/Less-2.jpg" /></center>
</div>
</body>
</html>
