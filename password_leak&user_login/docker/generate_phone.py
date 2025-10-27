import random
import os

# 生成9位随机号码（首位1-9，后8位0-9）
random.seed(os.urandom(16))
first = random.choice('123456789')
others = ''.join(random.choices('0123456789', k=8))
print(first + others)  # 仅输出号码，供shell脚本捕获