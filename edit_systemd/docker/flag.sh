#!/bin/bash
set -e

[ "$(id -u)" -eq 0 ] || exit 1
cat /root/flag