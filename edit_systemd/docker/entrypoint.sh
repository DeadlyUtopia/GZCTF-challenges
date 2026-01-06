#!/bin/bash
set -e

if [ -n "$GZCTF_FLAG" ]; then
    echo -n "$GZCTF_FLAG" > /root/flag
    chmod 600 /root/flag
    chown root:root /root/flag
fi

exec /usr/sbin/sshd -D