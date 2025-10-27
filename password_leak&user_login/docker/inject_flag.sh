#!/bin/sh
# 步骤1：生成随机号码（调用Python脚本）
new_number=$(python3 /app/generate_phone.py)

# 步骤2：替换Hexo主题配置中的旧号码
sed -i "s/952701145/$new_number/g" /app/themes/next/_config.yml

# 步骤3：在容器启动时生成Hexo静态文件（符合你的要求）
cd /app && npx hexo clean && npx hexo generate && cp -r public/* /usr/share/nginx/html/

# 步骤4：生成Nginx认证密码
htpasswd -bc /etc/nginx/.htpasswd admin "$new_number"

# 步骤5：注入Flag
sed "s/{{GZCTF_FLAG}}/${GZCTF_FLAG:-FLAG{test}}/g" /usr/share/nginx/html/admin/index.html.template > /usr/share/nginx/html/admin/index.html

# 步骤6：启动Nginx
nginx -g "daemon off;"