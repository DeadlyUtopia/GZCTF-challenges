#!/bin/sh
echo "$GZCTF_FLAG" > /flag.txt
export GZCTF_FLAG=""
ulimit -n 65536
export JAVA_OPTS="-Xms32m -Xmx192m -XX:+UseSerialGC -XX:-TieredCompilation -XX:TieredStopAtLevel=1"
exec /usr/local/FineBI5.1/bin/finebi
