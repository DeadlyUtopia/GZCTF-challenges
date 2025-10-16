# edit_dns_ping

```
ssh root@{IP} -p {PORT}
Are you sure you want to continue connecting (yes/no/[fingerprint])? yes
root@{IP}'s password:
Welcome to Ubuntu 22.04.5 LTS (GNU/Linux 6.8.0-51-generic x86_64)

 * Documentation:  https://help.ubuntu.com
 * Management:     https://landscape.canonical.com
 * Support:        https://ubuntu.com/pro

This system has been minimized by removing packages and content that are
not required on a system that users do not log into.

To restore this content, you can run the 'unminimize' command.

The programs included with the Ubuntu system are free software;
the exact distribution terms for each program are described in the
individual files in /usr/share/doc/*/copyright.

Ubuntu comes with ABSOLUTELY NO WARRANTY, to the extent permitted by
applicable law.

root@d78e7ad41dc5:~# ls
root@d78e7ad41dc5:~# cd /
root@d78e7ad41dc5:/# ls
bin  boot  dev  etc  home  lib  lib32  lib64  libx32  media  mnt  opt  proc  root  run  sbin  srv  sys  tmp  usr  var
root@d78e7ad41dc5:/# cat /etc/resolv.conf

# Edit this, then ping
root@d78e7ad41dc5:/# vim /etc/resolv.conf
root@d78e7ad41dc5:/# cat /etc/resolv.conf
nameserver 114.114.114.114
# Edit this, then ping
root@d78e7ad41dc5:/# ping baidu.com
PING baidu.com (39.156.70.37) 56(84) bytes of data.
64 bytes from 39.156.70.37: icmp_seq=1 ttl=50 time=35.2 ms
^C64 bytes from 39.156.70.37: icmp_seq=2 ttl=50 time=35.4 ms

--- baidu.com ping statistics ---
2 packets transmitted, 2 received, 0% packet loss, time 10064ms
rtt min/avg/max/mdev = 35.239/35.324/35.409/0.085 ms
root@d78e7ad41dc5:/# ls
bin   dev  flag  lib    lib64   media  opt   root  sbin  sys  usr
boot  etc  home  lib32  libx32  mnt    proc  run   srv   tmp  var
root@d78e7ad41dc5:/# cat flag
flag{GZCTF_dynamic_flag_test}
root@d78e7ad41dc5:/#
```

- `ssh` 登录（`root/root`）
- 修改 `/etc/resolv.conf` 添加 `DNS` （例如： `nameserver 8.8.8.8`）
- `ping example.com`

- `cat /flag`
