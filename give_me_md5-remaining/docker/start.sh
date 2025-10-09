#!/bin/bash
set -e

# 检查必要文件是否存在
if [ ! -f "/app/homo.jpg" ]; then
  echo "错误：未找到homo.jpg文件！"
  exit 1
fi

# 检查环境变量是否设置
if [ -z "$GZCTF_FLAG" ]; then
  echo "错误：必须设置GZCTF_FLAG环境变量！"
  exit 1
fi

# 生成16位随机字符串并追加到图片（改变MD5值）
RANDOM_STR=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 16 | head -n 1)
echo "$RANDOM_STR" >> /app/homo.jpg
echo "已向homo.jpg追加随机字符串：$RANDOM_STR"

# 启动Flask服务
echo "启动MD5验证服务..."
python3 /app/app.py
    