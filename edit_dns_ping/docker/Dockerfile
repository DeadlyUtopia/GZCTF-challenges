FROM ubuntu:22.04
ENV DEBIAN_FRONTEND=noninteractive

# 配置国内源
RUN echo "deb http://mirrors.aliyun.com/ubuntu/ jammy main restricted universe multiverse" > /etc/apt/sources.list && \
    echo "deb http://mirrors.aliyun.com/ubuntu/ jammy-updates main restricted universe multiverse" >> /etc/apt/sources.list && \
    echo "deb http://mirrors.aliyun.com/ubuntu/ jammy-security main restricted universe multiverse" >> /etc/apt/sources.list

# 安装必要工具
RUN apt update && apt install -y \
    openssh-server \
    iputils-ping \
    dnsutils \
    vim \
    && rm -rf /var/lib/apt/lists/*

# 配置SSH（密码: root）
RUN mkdir -p /var/run/sshd && \
    sed -i 's/^#\?PermitRootLogin.*/PermitRootLogin yes/' /etc/ssh/sshd_config && \
    sed -i 's/^#\?PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config && \
    echo "root:root" | chpasswd

# 复制并配置初始化脚本
COPY init.sh /init.sh
RUN chmod +x /init.sh

# 启动命令
CMD ["/bin/bash", "-c", "/init.sh & /usr/sbin/sshd -D"]
EXPOSE 22
