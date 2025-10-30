# simple_hash_series-php:01

```Python
import hashlib

# 遍历可能的token值
for i in range(100000000):
    token = str(i)
    md5_hash = hashlib.md5(token.encode()).hexdigest()
    # 检查第2、15、18位是否相同且为数字，第32位是否为'3'
    if (len(md5_hash) == 32 and
        md5_hash[1] == md5_hash[14] == md5_hash[17] and
        md5_hash[1].isdigit() and
        md5_hash[1] != '0' and
        md5_hash[31] == '3'):
        print(f"符合条件的token: {token}")
        print(f"对应的MD5: {md5_hash}")
        break
```

访问 `IP:PORT/?token=422` 即可得到 `FLAG`