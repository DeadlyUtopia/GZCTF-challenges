#!/bin/bash

FLAG_FILENAME=$(cat /dev/urandom | tr -dc 'a-z0-9' | fold -w 8 | head -n 1)
echo "$FLAG_FILENAME" > /opt/ctf/var.txt
chmod 644 /opt/ctf/var.txt

exec apache2-foreground