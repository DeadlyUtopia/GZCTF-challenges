import hashlib
import os
from flask import Flask, request, jsonify, render_template_string, send_file

app = Flask(__name__)

# 计算修改后的图片的32位MD5值，并提取需要验证的部分（去掉中间16位后剩下的部分）
def calculate_image_md5():
    with open('homo.jpg', 'rb') as f:
        md5_hash = hashlib.md5()
        while chunk := f.read(4096):
            md5_hash.update(chunk)
        # 获取完整32位MD5
        full_md5 = md5_hash.hexdigest()
        # 提取前8位 + 后8位（去掉中间16位后剩下的部分）
        return full_md5[:8] + full_md5[-8:]

# 计算需要验证的MD5部分并存储
VERIFY_MD5_PART = calculate_image_md5()

# 获取环境变量GZCTF_FLAG的值
GZCTF_VALUE = os.environ['GZCTF_FLAG']  # 必须设置环境变量

# HTML表单页面，更新提示文本
HTML_FORM = '''
<!DOCTYPE html>
<html>
<head>
    <title>MD5验证</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .container { text-align: center; }
        input { padding: 8px; width: 300px; margin: 10px 0; }
        button { padding: 8px 16px; background-color: #4CAF50; color: white; border: none; cursor: pointer; margin: 5px; }
        button:hover { background-color: #45a049; }
        .download-btn { background-color: #2196F3; }
        .download-btn:hover { background-color: #0b7dda; }
        .result { margin-top: 20px; padding: 10px; border-radius: 4px; }
        .success { background-color: #dff0d8; color: #3c763d; }
        .error { background-color: #f2dede; color: #a94442; }
    </style>
</head>
<body>
    <div class="container">
        <h1>MD5(16)验证？</h1>
        <!-- 图片下载按钮 -->
        <a href="/download">
            <button type="button" class="download-btn">下载图片</button>
        </a>
        
        <form method="post">
            <input type="text" name="md5" placeholder="请输入 16 位字符串" required>
            <br>
            <button type="submit">验证</button>
        </form>
        
        {% if result %}
            <div class="result {{ 'success' if result.success else 'error' }}">
                {{ result.message }}
            </div>
        {% endif %}
    </div>
</body>
</html>
'''

@app.route('/', methods=['GET', 'POST'])
def index():
    result = None
    if request.method == 'POST':
        user_md5 = request.form.get('md5', '').strip().lower()
        
        # 验证输入是否为16位
        if len(user_md5) != 16:
            result = {
                'success': False,
                'message': f'输入长度错误！请输入16位字符，你输入了{len(user_md5)}位'
            }
        else:
            # 验证MD5部分是否匹配
            if user_md5 == VERIFY_MD5_PART:
                result = {
                    'success': True,
                    'message': f'验证成功！\n{GZCTF_VALUE}'
                }
            else:
                result = {
                    'success': False,
                    'message': '验证失败！MD5部分不匹配'
                }
    
    return render_template_string(HTML_FORM, result=result)

# 图片下载路由
@app.route('/download')
def download_image():
    if not os.path.exists('homo.jpg'):
        return "图片文件不存在", 404
    
    return send_file('homo.jpg', as_attachment=True, download_name='homo_modified.jpg')

# API接口
@app.route('/api/verify', methods=['POST'])
def api_verify():
    data = request.json
    if not data or 'md5' not in data:
        return jsonify({'success': False, 'message': '请提供md5参数'}), 400
    
    user_md5 = data['md5'].strip().lower()
    
    if len(user_md5) != 16:
        return jsonify({
            'success': False,
            'message': f'输入长度错误！请输入16位字符，你输入了{len(user_md5)}位'
        })
    
    if user_md5 == VERIFY_MD5_PART:
        return jsonify({
            'success': True,
            'message': '验证成功',
            'gzctf': GZCTF_VALUE
        })
    else:
        return jsonify({
            'success': False,
            'message': '验证失败！MD5部分不匹配'
        })

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
