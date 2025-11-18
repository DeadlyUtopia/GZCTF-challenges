#!/bin/bash
rm -rf init.sh

if [ -z "$BASH_VERSION" ]; then
    exec /bin/bash "$0" "$@"
fi

fix_resolv_perm() {
    TARGET_FILE="/etc/resolv.conf"
    [ ! -f "$TARGET_FILE" ] && touch "$TARGET_FILE"
    chown root:ctf "$TARGET_FILE" || true
    chmod 664 "$TARGET_FILE" || true
    chattr -i "$TARGET_FILE" 2>/dev/null || true
}

fix_resolv_perm
echo "# Edit this, then ping" > /etc/resolv.conf

cat <<'BANNER'
======================================
🔒 CTF网络挑战
📝 任务：修复DNS并ping通外网域名获取flag
💡 提示：
1. 当前DNS配置已初始化，可直接编辑
2. 修复命令：echo "nameserver {DNS server}" > /etc/resolv.conf
3. 成功ping通外网域名后，flag将写入/flag文件
======================================
BANNER

PRIVATE_IPS=(
    "10." "127." "192.168." 
    "172.16." "172.17." "172.18." "172.19."
    "172.20." "172.21." "172.22." "172.23."
    "172.24." "172.25." "172.26." "172.27."
    "172.28." "172.29." "172.30." "172.31."
)

is_valid_target() {
    target="$1"
    if echo "$target" | grep -Eq '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$'; then
        return 1
    fi
    ip=$(dig +short +timeout=5 "$target" 2>/dev/null | grep -v '^;' | head -n1)
    if [ -z "$ip" ]; then
        return 1
    fi
    for prefix in "${PRIVATE_IPS[@]}"; do
        if echo "$ip" | grep -Eq "^$prefix"; then
            return 1
        fi
    done
    return 0
}

setcap cap_net_raw+ep /bin/ping || true
chown root:ctf /bin/ping || true
chmod 750 /bin/ping || true

echo "auth required pam_succeed_if.so user != root quiet" >> /etc/pam.d/su 2>/dev/null || true
sed -i "/ctf/d" /etc/sudoers 2>/dev/null || true

echo "开始监控网络状态，等待有效ping操作..."
while true; do
    fix_resolv_perm

    ping_target=$(
        ps aux | grep -E ' ping [a-zA-Z0-9.-]+' | grep -v grep | grep -v "$0" |
        awk '{for(i=1;i<=NF;i++) if($i=="ping") print $(i+1)}' | head -n1
    )
    
    if [ -n "$ping_target" ] && is_valid_target "$ping_target" && ping -c1 -W3 "$ping_target" >/dev/null 2>&1; then
        echo "$GZCTF_FLAG" > /flag
        chown root:ctf /flag && chmod 440 /flag
        echo -e "\n🎉 成功检测到外网域名ping！"
        echo "📜 flag已写入/flag文件，执行 cat /flag 查看"
        break
    fi
    
    sleep 1
done

tail -f /dev/null
