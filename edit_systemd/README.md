# edit_systemd

- 在 `/home/ctfuser` 目录，即用户家目录编写 `flag.service` 文件

- `flag.service` 至少要含有以下内容

  ```service
  [Unit]
  [Service]
  User=root
  ExecStart=/usr/local/lib/flag.sh
  ```

- 执行 `systemctl start flag.service` ，如果 `flag.service` 没有问题则会输出 `FLAG`