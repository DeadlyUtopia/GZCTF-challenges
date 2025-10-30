# simple_hash_series-php:02

访问页面看到如下内容

```php
<?php
error_reporting(0);
include("flag.php");
if(isset($_GET['r'])){
    $r = $_GET['r'];
    mt_srand(18853);
    if(intval($r)===intval(mt_rand())){
        echo $flag;
    }
}else{
    highlight_file(__FILE__);
    echo system('cat /proc/version');
}
?> Linux version 6.16.8+kali-amd64 (devel@kali.org) (x86_64-linux-gnu-gcc-14 (Debian 14.3.0-8) 14.3.0, GNU ld (GNU Binutils for Debian) 2.45) #1 SMP PREEMPT_DYNAMIC Kali 6.16.8-1kali1 (2025-09-24) Linux version 6.16.8+kali-amd64 (devel@kali.org) (x86_64-linux-gnu-gcc-14 (Debian 14.3.0-8) 14.3.0, GNU ld (GNU Binutils for Debian) 2.45) #1 SMP PREEMPT_DYNAMIC Kali 6.16.8-1kali1 (2025-09-24)
```

使用以下脚本计算参数 `r` 的值

```php
<?php
mt_srand(18853);	// 结果：535876038
echo mt_rand();
?>
```

得到参数 `r` 的值后访问 `IP:PORT/?r=XXX` ，例如这里应该访问 `IP:PORT/?r=535876038`

最后得到 FLAG