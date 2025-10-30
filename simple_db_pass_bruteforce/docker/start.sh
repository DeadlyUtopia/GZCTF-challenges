#!/bin/bash
# 生成567890到952700之间的随机6位数字密码
RANDOM_PASS=$(shuf -i 567890-952700 -n 1)
# 存储到非Web目录
mkdir -p /etc/ctfconfig/
echo "$RANDOM_PASS" > /etc/ctfconfig/password.txt
# 设置权限
chmod 640 /etc/ctfconfig/password.txt
chown root:www-data /etc/ctfconfig/password.txt
# 启动Apache服务
exec apache2-foreground