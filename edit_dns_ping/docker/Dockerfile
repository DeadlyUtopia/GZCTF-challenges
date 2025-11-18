FROM ubuntu:22.04
ENV DEBIAN_FRONTEND=noninteractive

# 国内源配置
RUN echo "deb http://mirrors.aliyun.com/ubuntu/ jammy main restricted universe multiverse" > /etc/apt/sources.list && \
    echo "deb http://mirrors.aliyun.com/ubuntu/ jammy-updates main restricted universe multiverse" >> /etc/apt/sources.list && \
    echo "deb http://mirrors.aliyun.com/ubuntu/ jammy-security main restricted universe multiverse" >> /etc/apt/sources.list

# 工具安装
RUN apt update && apt install -y \
    openssh-server \
    iputils-ping \
    dnsutils \
    strace \
    vim && \
    rm -rf /var/lib/apt/lists/*

# 核心配置
RUN mkdir -p /var/run/sshd && \
    sed -i 's/^#\?PermitRootLogin.*/PermitRootLogin no/' /etc/ssh/sshd_config && \
    sed -i 's/^#\?PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config && \
    useradd -m -s /bin/bash ctf && \
    echo "ctf:flag" | chpasswd && \
    usermod -G "" ctf && \
    echo "AllowUsers ctf" >> /etc/ssh/sshd_config && \
    rm -f /etc/resolv.conf 2>/dev/null || true && \
    touch /etc/resolv.conf && \
    chown root:ctf /etc/resolv.conf && \
    chmod 664 /etc/resolv.conf && \
    chattr -i /etc/resolv.conf 2>/dev/null || true

# 复制脚本
COPY init.sh /init.sh
RUN chmod +x /init.sh

# 启动命令
CMD ["/bin/bash", "-c", "/init.sh & /usr/sbin/sshd -D"]
EXPOSE 22
