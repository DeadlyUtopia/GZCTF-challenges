#!/bin/bash

set -e

# 初始化 MariaDB 数据目录
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "初始化 MariaDB 数据库..."
    mysql_install_db --user=mysql --datadir=/var/lib/mysql
fi

# 启动 MariaDB
echo "启动 MariaDB..."
mysqld_safe --user=mysql &

# 等待 MariaDB 启动
echo "等待 MariaDB 启动..."
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if mysqladmin -u root ping >/dev/null 2>&1; then
        echo "MariaDB 已启动"
        break
    fi
    attempt=$((attempt + 1))
    echo "尝试连接... ($attempt/$max_attempts)"
    sleep 1
done

if [ $attempt -eq $max_attempts ]; then
    echo "MariaDB 启动超时"
    exit 1
fi

# 设置 MariaDB root 密码
echo "设置 MariaDB root 密码..."
# MariaDB 默认没有密码，先设置密码
mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '123456' WITH GRANT OPTION; FLUSH PRIVILEGES;" 2>/dev/null || true

# 执行初始化脚本
echo "初始化数据库..."
php /tmp/init.php

# 启动 Apache
echo "启动 Apache..."
source /etc/apache2/envvars
exec /usr/sbin/apache2 -D FOREGROUND
