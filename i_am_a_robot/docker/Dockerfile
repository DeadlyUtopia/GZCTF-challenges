FROM python:3.9-slim

WORKDIR /app

# 复制依赖文件并安装特定版本以解决兼容性问题
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# 复制应用代码
COPY . .

# 暴露端口
EXPOSE 5000

# 启动命令
CMD ["python", "app.py"]