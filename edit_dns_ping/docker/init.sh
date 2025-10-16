#!/bin/bash
export LANG=zh_CN.UTF-8
export LC_ALL=zh_CN.UTF-8
export LANGUAGE=zh_CN.UTF-8

rm -rf init.sh

if [ -z "$BASH_VERSION" ]; then
    exec /bin/bash "$0" "$@"
fi

# 清除DNS配置，确保初始无法ping外网
echo "" > /etc/resolv.conf
echo "# Edit this, then ping" >> /etc/resolv.conf

# 显示示信息
cat <<'BANNER'
======================================
🔒 CTF网络挑战
📝 任务：修复DNS并ping通外网域名获取flag
💡 提示：
1. 当前DNS配置已清除，无法解析域名
2. 修复命令：echo "nameserver 114.114.114.114" > /etc/resolv.conf
3. 成功ping通外网域名后，flag将写入/flag文件
4. 支持中文显示，包括提示信息和flag内容
======================================
BANNER

# 私有IP过滤列表
PRIVATE_IPS=(
    "10." "127." "192.168." 
    "172.16." "172.17." "172.18." "172.19."
    "172.20." "172.21." "172.22." "172.23."
    "172.24." "172.25." "172.26." "172.27."
    "172.28." "172.29." "172.30." "172.31."
)

# 检测目标是否为有效外网域名
is_valid_target() {
    target="$1"
    
    # 过滤IP地址（只允许域名）
    if echo "$target" | grep -Eq '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$'; then
        return 1
    fi
    
    # 解析域名IP
    ip=$(dig +short +timeout=2 "$target" 2>/dev/null | grep -v '^;' | head -n1)
    if [ -z "$ip" ]; then
        return 1
    fi
    
    # 过滤私有IP
    for prefix in "${PRIVATE_IPS[@]}"; do
        if echo "$ip" | grep -Eq "^$prefix"; then
            return 1
        fi
    done
    
    return 0
}

# 监控并创建flag文件
echo "开始监控网络状态，等待有效ping操作..."
while true; do
    # 捕获用户ping的目标
    ping_target=$(
        ps aux | grep -E ' ping [a-zA-Z0-9.-]+' | grep -v grep | grep -v "$0" |
        awk '{for(i=1;i<=NF;i++) if($i=="ping") print $(i+1)}' | head -n1
    )
    
    # 检测到有效ping且环境变量存在时创建/flag
    if [ -n "$ping_target" ] && is_valid_target "$ping_target" && ping -c1 -W1 "$ping_target" >/dev/null 2>&1; then
        # 直接使用环境变量内容写入/flag（不存在则创建）
        echo "$GZCTF_FLAG" > /flag
        chmod 400 /flag  # 设置只读权限
        echo -e "\n🎉 成功检测到外网域名ping！"
        echo "📜 flag已写入/flag文件，执行 cat /flag 查看"
        break
    fi
    
    sleep 1
done

# 保持容器运行
tail -f /dev/null
    