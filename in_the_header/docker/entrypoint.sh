#!/bin/bash

# 将环境变量GZCTF_FLAG写入/tmp/flag文件（容器启动时执行）
echo "$GZCTF_FLAG" > /tmp/flag

# 启动PHP-FPM和Nginx
service php7.4-fpm start
nginx -g "daemon off;"