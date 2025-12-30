FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteractive
ENV GZCTF_FLAG=flag{default_flag}

# 安装 MariaDB、PHP 和 Apache
RUN apt-get update && apt-get install -y \
    mariadb-server \
    php \
    php-mysqli \
    apache2 \
    libapache2-mod-php \
    && rm -rf /var/lib/apt/lists/*

# 启用 Apache 模块
RUN a2enmod php7.4 rewrite

# 配置 MariaDB 使用更少内存
RUN mkdir -p /etc/mysql/mariadb.conf.d/
RUN echo "[mysqld]\n\
innodb_buffer_pool_size = 64M\n\
innodb_log_buffer_size = 8M\n\
key_buffer_size = 16M\n\
max_connections = 20\n\
table_open_cache = 64\n\
sort_buffer_size = 512K\n\
read_buffer_size = 256K\n\
read_rnd_buffer_size = 512K\n\
myisam_sort_buffer_size = 8M\n\
thread_cache_size = 8\n\
query_cache_size = 0\n\
tmp_table_size = 16M\n\
max_heap_table_size = 16M\n\
performance_schema = OFF" > /etc/mysql/mariadb.conf.d/99-low-memory.cnf

# 创建 MySQL 数据目录
RUN mkdir -p /var/run/mysqld && \
    chown -R mysql:mysql /var/run/mysqld /var/lib/mysql

# 复制网站文件
RUN rm -f /var/www/html/index.html
COPY index.html /var/www/html/
COPY api.php /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# 复制初始化脚本（不在网站根目录）
COPY init.php /tmp/init.php

# 复制启动脚本
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
