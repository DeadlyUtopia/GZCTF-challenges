from flask import Flask, request, jsonify, send_from_directory
import requests
import os

app = Flask(__name__, static_folder='frontend', static_url_path='')

ERROR_MESSAGES = {
    "q3004": "验证码验证错误",
    "q3005": "访问已被限速，请输入验证码",
    "q3007": "用户积分不足",
    "u3009": "缺少必要请求参数",
    "u3010": "参数类型错误",
    "q3011": "用户缺少必要权限，请联系管理员 quake@360.cn",
    "q3015": "查询语句解析错误",
    "q3017": "暂不支持该字段查询"
}

@app.route('/')
def index():
    return send_from_directory(app.static_folder, 'index.html')

@app.route('/quake-verify', methods=['POST'])
def quake_verify():
    data = request.get_json()
    api_key = data.get('api_key')
    if not api_key:
        return jsonify({'success': False, 'message': 'API KEY 不能为空'}), 400

    headers = {"X-QuakeToken": api_key}

    try:
        res = requests.get("https://quake.360.net/api/v3/user/info", headers=headers)
        res.raise_for_status()
        res_json = res.json()
    except requests.exceptions.RequestException as e:
        return jsonify({'success': False, 'message': f'请求错误：{str(e)}'}), 500
    except ValueError:
        return jsonify({'success': False, 'message': '响应非 JSON 格式'}), 500

    code = res_json.get("code")
    msg = res_json.get("message", "")
    data = res_json.get("data", {})

    if code == 0 and msg == "Successful.":
        user = data.get("user", {})
        flag = os.getenv("GZCTF_FLAG", "FLAG_NOT_SET")
        return jsonify({
            "success": True,
            "flag": flag[:len(flag)//2],  # 前半段flag
            "user_info": {
                "id": user.get("id"),
                "username": user.get("username"),
                "fullname": user.get("fullname"),
                "email": user.get("email"),
                "credit": data.get("credit"),
                "baned": data.get("baned"),
                "role": data.get("role", [])
            }
        })

    error_msg = ERROR_MESSAGES.get(code, msg)
    return jsonify({
        "success": False,
        "message": f"验证失败（{code}）：{error_msg}"
    }), 200


@app.route('/silicon-verify', methods=['POST'])
def silicon_verify():
    data = request.get_json()
    api_key = data.get('api_key')
    if not api_key:
        return jsonify({'success': False, 'message': 'API KEY 不能为空'}), 400

    headers = {"Authorization": f"Bearer {api_key}"}

    try:
        res = requests.get("https://api.siliconflow.cn/v1/user/info", headers=headers)
        res.raise_for_status()
        res_json = res.json()
    except requests.exceptions.RequestException as e:
        return jsonify({'success': False, 'message': f'请求错误：{str(e)}'}), 500
    except ValueError:
        return jsonify({'success': False, 'message': '响应非 JSON 格式'}), 500

    code = res_json.get("code")
    msg = res_json.get("message", "")
    success_status = res_json.get("status", False)
    data = res_json.get("data", {})

    if code == 20000 and msg.lower() == "ok" and success_status:
        flag = os.getenv("GZCTF_FLAG", "FLAG_NOT_SET")
        return jsonify({
            "success": True,
            "flag": flag[len(flag)//2:],  # 后半段flag
            "user_info": {
                "id": data.get("id"),
                "name": data.get("name"),
                "email": data.get("email") or "无",
                "balance": data.get("balance"),
                "status": data.get("status"),
            }
        })

    return jsonify({
        "success": False,
        "message": f"验证失败（{code}）：{msg}"
    }), 200


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
