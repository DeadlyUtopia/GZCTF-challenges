<?php
// 缓存文件路径（只随机生成一次，后续不变）
$cacheFile = '/tmp/ctf_aes_cache.txt';

// 生成16位随机数字密钥（CryptoJS手动传IV场景→SHA1+重复填充）
function generateRandomKey($length = 16) {
    $chars = '0123456789';
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $key;
}

// 生成16位随机大小写字母IV（CryptoJS直接Latin1解析）
function generateRandomIV($length = 16) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $iv = '';
    for ($i = 0; $i < $length; $i++) {
        $iv .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $iv;
}

// 生成16位随机复杂密码（目标密码，ZeroPadding无需额外填充）
function generateRandomPassword($length = 16) {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

// 首次访问生成随机值并缓存，后续直接读取
if (file_exists($cacheFile)) {
    $cacheData = json_decode(file_get_contents($cacheFile), true);
    $global_key = $cacheData['key'];
    $global_iv = $cacheData['iv'];
    $targetPassword = $cacheData['password'];
    $encryptedTargetHex = $cacheData['ciphertext'];
} else {
    $global_key = generateRandomKey(16);
    $global_iv = generateRandomIV(16);
    $targetPassword = generateRandomPassword(16);

    // ------------------------
    // 1. 密钥处理：Latin1编码 → SHA1哈希 → 重复填充到32字节（256位密钥）
    $key_latin1 = iconv('UTF-8', 'ISO-8859-1', $global_key); // 同CryptoJS.enc.Latin1.parse
    $key_sha1 = sha1($key_latin1, true); // SHA1哈希→20字节二进制
    $key_256 = str_repeat($key_sha1, 2); // 重复2次→40字节
    $key_256 = substr($key_256, 0, 32); // 截取前32字节→256位（AES-256要求）
    
    // 2. IV处理：Latin1编码（同CryptoJS，不变）
    $iv_latin1 = iconv('UTF-8', 'ISO-8859-1', $global_iv);
    
    // 3. 加密：AES-256-CBC-ZeroPadding（不变）
    $encrypted_bin = openssl_encrypt(
        $targetPassword,
        'aes-256-cbc', // AES-256，和前端一致
        $key_256,      // 填充后的256位密钥
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
        $iv_latin1
    );
    
    // 4. 密文输出：二进制→16进制字符串（同CryptoJS，不变）
    $encryptedTargetHex = bin2hex($encrypted_bin);

    // 写入缓存
    $cacheData = [
        'key' => $global_key,
        'iv' => $global_iv,
        'password' => $targetPassword,
        'ciphertext' => $encryptedTargetHex
    ];
    file_put_contents($cacheFile, json_encode($cacheData));
}

// ------------ 登录验证逻辑------------
error_reporting(0);
$flag = getenv('GZCTF_FLAG') ?: 'flag{_not_found_value_}';
$u = $_POST['username'];
$p = $_POST['pazzword'];
if (isset($u) && isset($p) && $u === 'admin' && $p === $encryptedTargetHex) {
    echo $flag;
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <script src="js/jquery.min.js"></script>
    <script src="js/crypto-js.js"></script>
    <title>登录</title>
</head>
<body>
    <h3>请输入管理员账号密码</h3>
    <form action="#" method="post" id="loginForm">
        用户名：<input type="text" name="username" value="admin" readonly><br>
        密  码：<input type="password" name="pazzword" id="pazzword" placeholder="输入16位密码"><br>
        <button type="button" onclick="checkForm()">提交</button>
    </form>
</body>
<script type="text/javascript">
    var key = "<?php echo $global_key; ?>";
    var iv = "<?php echo $global_iv; ?>";

    function checkForm() {
        var pazzword = $("#pazzword").val();
        var passwordRegex = /^[A-Za-z0-9_\-]{16}$/;
        if (!passwordRegex.test(pazzword)) {
            alert("密码必须是16位字符（大小写字母、数字、_、-）！");
            return;
        }
        pazzword = encryptToHex(pazzword, key, iv);
        $("#pazzword").val(pazzword);
        $("#loginForm").submit();
    }

    function encryptToHex(data, key, iv) {
        var key_latin1 = CryptoJS.enc.Latin1.parse(key);
        var key_sha1 = CryptoJS.SHA1(key_latin1);
        var key_sha1_hex = key_sha1.toString(CryptoJS.enc.Hex);
        var key_256_hex = (key_sha1_hex + key_sha1_hex).substring(0, 64);
        var key_256 = CryptoJS.enc.Hex.parse(key_256_hex);

        var iv_latin1 = CryptoJS.enc.Latin1.parse(iv);
        var encrypted = CryptoJS.AES.encrypt(data, key_256, {
            iv: iv_latin1,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.ZeroPadding
        });
        return encrypted.ciphertext.toString(CryptoJS.enc.Hex);
    }
</script>

<!--
error_reporting(0);
$flag = getenv('FLAG') ?: 'flag{_Not_here_}';
$u = $_POST['username'];
$p = $_POST['pazzword'];
$correct_pazzword = "<?php echo $encryptedTargetHex; ?>";
if(isset($u) && isset($p)){
    if($u === 'admin' && $p === $correct_pazzword){
        echo $flag;
    }
}
-->

</html>