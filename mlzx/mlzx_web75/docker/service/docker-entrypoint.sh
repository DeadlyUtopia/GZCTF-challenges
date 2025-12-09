#!/bin/bash

rm -f /docker-entrypoint.sh

mysqld_safe &

mysql_ready() {
	mysqladmin ping --socket=/run/mysqld/mysqld.sock --user=root --password=root > /dev/null 2>&1
}

while !(mysql_ready)
do
	echo "waiting for mysql ..."
	sleep 3
done

# Check the environment variables for the flag and assign to INSERT_FLAG
if [ "$GZCTF_FLAG" ]; then
    INSERT_FLAG="$GZCTF_FLAG"
elif [ "$FLAG" ]; then
    INSERT_FLAG="$FLAG"
elif [ "$DASFLAG" ]; then
    INSERT_FLAG="$DASFLAG"
else
    INSERT_FLAG="flag{TEST_Dynamic_FLAG}"
fi

# Generate random 4-character filename (alphanumeric)
RANDOM_SUFFIX=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 4 | head -n 1)
FLAG_FILE="/flag${RANDOM_SUFFIX}.txt"

echo "Writing flag to: $FLAG_FILE"

# Write FLAG to file
echo "$INSERT_FLAG" > "$FLAG_FILE"

# Ensure the flag file can be read by MySQL
chmod 644 "$FLAG_FILE"

# Create database and load file capability
mysql -u root -p123456 -e "
USE ctf;
create table users (id varchar(300),username varchar(300),password varchar(300));
insert into users values('1','user1','Man! What can i see!}');
insert into users values('3','user3','Nothing!');
insert into users values('4','user4','What are you doing?');
"

php-fpm & nginx &

echo "Running..."

tail -F /var/log/nginx/access.log /var/log/nginx/error.log