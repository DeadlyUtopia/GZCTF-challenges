import os
import random
import datetime
import pytz
from flask import Flask, render_template, request, session, redirect, url_for

app = Flask(__name__)
app.secret_key = os.urandom(24)
app.config['PERMANENT_SESSION_LIFETIME'] = datetime.timedelta(minutes=10)
app.static_folder = 'static'
app.debug = False

# 核心配置
FLAG = os.environ.get("GZCTF_FLAG", "flag{If you see me, it means the FLAG value was not passed correctly.}")
SUCCESS_IMAGES = ["static/images/GtKwbGnbMAIjOW4.jpg", "static/images/Nano_is_not_robot.jpg"]

# 第一关配置
VERIFIED_RESULTS = {
    (0.1, 0.2): "0.30000000000000004",
    (0.1, 0.7): "0.7999999999999999",
    (0.2, 0.4): "0.6000000000000001",
    (0.2, 0.7): "0.8999999999999999",
    (0.3, 0.6): "0.8999999999999999",
    (0.4, 0.8): "1.2000000000000002",
    (0.6, 0.7): "1.2999999999999998",
    (0.8, 0.9): "1.7000000000000002"
}

# 第二关配置
INT32_MAX = 2147483647
INT32_MIN = -2147483648
UINT32_MAX = 4294967295
OPERATIONS = [
    ('+', lambda a, b: (a + b) % (UINT32_MAX + 1)),
    ('*', lambda a, b: (a * b) % (UINT32_MAX + 1))
]
INT_CANDIDATES = [1800000000, 1900000000, 2000000000, 2100000000]

# 第三关配置（不使用）
SCENARIOS = {
    "linux32": {
        "name": "Linux 32位",
        "max_ts": 2147483647,
        "epoch": datetime.datetime(1970, 1, 1),
        "unit": "秒",
        "multiplier": 1,
        "start_year_range": (2030, 2037),
        "max_add_days": 3650,
        "timezone_name": "Asia/Shanghai"
    },
    "javascript": {
        "name": "JavaScript",
        "max_ts": 9007199254740991,
        "epoch": datetime.datetime(1970, 1, 1),
        "unit": "毫秒",
        "multiplier": 1000,
        "start_ts_offset_range": (365*100, 365*300),
        "max_add_days": 3650,
        "timezone_name": "Asia/Shanghai"
    }
}

# 完善的人类答案检测逻辑（重点修复JS模式）
def is_human_answer(stage, user_input, expected_human_val, correct_answer=None, scenario_key=None):
    try:
        if stage == 1:
            if user_input.strip() == correct_answer:
                return False
            user_val = float(user_input.strip())
            real_human_val = expected_human_val
            allowed = [round(real_human_val, 1), round(real_human_val, 2),
                      round(real_human_val, 3), round(real_human_val, 4)]
            return round(user_val, 4) in allowed
        
        elif stage == 2:
            user_val = int(user_input.replace('(', '').replace(')', ''))
            return user_val == expected_human_val
        
        elif stage == 3:  # 保留但不使用
            user_input_clean = user_input.strip().lower()
            expected_human_clean = expected_human_val.strip().lower()
            
            if scenario_key == "javascript":
                if not user_input_clean or '-' not in user_input_clean:
                    return False
                    
                user_parts = user_input_clean.split('-')
                expected_parts = expected_human_clean.split('-')
                
                if len(user_parts) != 3 or len(expected_parts) != 3:
                    return False
                    
                try:
                    user_year = int(user_parts[0])
                    expected_year = int(expected_parts[0])
                    year_diff = abs(user_year - expected_year)
                    year_match = year_diff <= 2
                    
                    user_month = int(user_parts[1])
                    expected_month = int(expected_parts[1])
                    month_diff = abs(user_month - expected_month)
                    month_match = month_diff <= 1 or month_diff >= 11
                    
                    user_day = int(user_parts[2])
                    expected_day = int(expected_parts[2])
                    day_diff = abs(user_day - expected_day)
                    day_match = day_diff <= 3
                    
                    return year_match and month_match and day_match
                    
                except (ValueError, TypeError):
                    return False
            
            return user_input_clean == expected_human_clean
    except:
        return False

# 安全生成超范围日期字符串（保留但不使用）
def generate_future_date_str(total_seconds):
    base_year = 9999
    base_month = 12
    base_day = 31
    base_seconds = (datetime.datetime(base_year, base_month, base_day) - datetime.datetime(1970, 1, 1)).total_seconds()
    
    if total_seconds <= base_seconds:
        date = datetime.datetime(1970, 1, 1) + datetime.timedelta(seconds=total_seconds)
        return date.strftime("%Y-%m-%d")
    
    extra_seconds = total_seconds - base_seconds
    extra_years = int(extra_seconds // (365 * 86400))
    remaining_seconds = extra_seconds % (365 * 86400)
    
    month = random.randint(1, 12)
    if month == 2:
        day = random.randint(1, 28)
    elif month in [4, 6, 9, 11]:
        day = random.randint(1, 30)
    else:
        day = random.randint(1, 31)
    
    return f"{base_year + extra_years}-{month:02d}-{day:02d}"

# 第三关日期生成（保留但不使用）
def generate_safe_date(scenario_key):
    scenario = SCENARIOS[scenario_key]
    timezone = pytz.timezone(scenario["timezone_name"])
    start_date_str = ""
    start_ts = 0

    if scenario_key == "linux32":
        start_year = random.randint(*scenario["start_year_range"])
        start_month = random.randint(1, 12)
        
        if start_month == 2:
            if (start_year % 4 == 0 and start_year % 100 != 0) or (start_year % 400 == 0):
                max_day = 29
            else:
                max_day = 28
        elif start_month in [4, 6, 9, 11]:
            max_day = 30
        else:
            max_day = 31
        start_day = random.randint(1, max_day)
        
        base_date = datetime.datetime(start_year, start_month, start_day)
        start_date = timezone.localize(base_date)
        epoch = scenario["epoch"].replace(tzinfo=timezone)
        delta = start_date - epoch
        start_ts = int(delta.total_seconds() * scenario["multiplier"])
        start_ts = max(1, min(start_ts, scenario["max_ts"] - 86400))
        start_date_str = start_date.strftime("%Y-%m-%d")

    else:
        offset_days = random.randint(*scenario["start_ts_offset_range"])
        start_ts = scenario["max_ts"] - offset_days * 86400 * scenario["multiplier"]
        start_ts = max(1, start_ts)
        
        total_seconds = start_ts / scenario["multiplier"]
        start_date_str = generate_future_date_str(total_seconds)

    seconds_per_day = 86400
    remaining_ts = scenario["max_ts"] - start_ts
    min_add_days = (remaining_ts // (seconds_per_day * scenario["multiplier"])) + 1
    
    max_add_days = max(scenario["max_add_days"], min_add_days + 100)
    add_days = random.randint(min_add_days, max_add_days)

    human_date_str = ""
    correct_date_str = ""

    if scenario_key == "linux32":
        human_date = start_date + datetime.timedelta(days=add_days)
        human_date_str = human_date.strftime("%Y-%m-%d")
        
        total_ts = start_ts + add_days * seconds_per_day * scenario["multiplier"]
        total_ts = total_ts % (2**32)
        if total_ts > 2**31 - 1:
            total_ts -= 2**32
        correct_date = epoch + datetime.timedelta(seconds=total_ts / scenario["multiplier"])
        correct_date_str = correct_date.strftime("%Y-%m-%d")

    else:
        start_parts = start_date_str.split('-')
        start_year = int(start_parts[0])
        start_month = int(start_parts[1])
        start_day = int(start_parts[2])
        
        years_add = add_days // 365
        days_remaining = add_days % 365
        
        current_month = start_month
        current_day = start_day
        
        months_add = days_remaining // 30
        days_remaining_after_months = days_remaining % 30
        
        current_month += months_add
        current_day += days_remaining_after_months
        
        if current_day > 31:
            current_day -= 31
            current_month += 1
            
        if current_month > 12:
            years_add += current_month // 12
            current_month = current_month % 12
            if current_month == 0:
                current_month = 12
                
        human_year = start_year + years_add
        human_date_str = f"{human_year}-{current_month:02d}-{current_day:02d}"
        
        correct_date_str = "Invalid Date"

    return {
        "scenario_name": scenario["name"],
        "start_date_str": start_date_str,
        "add_days": add_days,
        "human_date": human_date_str,
        "correct_date": correct_date_str
    }

# 路由部分
@app.route('/', methods=['GET', 'POST'])
def index():
    show_human_alert = request.args.get('human', 'false') == 'true'
    alert_message = "为时已晚，有机体（人类）！" if show_human_alert else None
    
    if request.method == 'POST' and 'robot' in request.form:
        session['stage'] = 1
        a, b = random.choice(list(VERIFIED_RESULTS.keys()))
        if random.choice([True, False]):
            a, b = b, a
        session['stage1_original'] = (min(a, b), max(a, b))
        session['stage1_display'] = (a, b)
        return redirect(url_for('stage1'))
        
    return render_template('index.html', alert_message=alert_message)

@app.route('/stage1', methods=['GET', 'POST'])
def stage1():
    if 'stage' not in session or session['stage'] != 1:
        return redirect(url_for('index'))
    
    if request.method == 'GET':
        a, b = session['stage1_display']
        return render_template('stage1.html', a=a, b=b, error=None)
    
    else:
        user_answer = request.form.get('answer', '').strip()
        a, b = session.get('stage1_display', (0, 0))
        original_a, original_b = session.get('stage1_original', (0, 0))
        correct_answer = VERIFIED_RESULTS.get((original_a, original_b), '')
        
        if user_answer == correct_answer:
            session['stage'] = 2
            op_symbol, op_func = random.choice(OPERATIONS)
            a = random.choice(INT_CANDIDATES)
            b = random.choice(INT_CANDIDATES)
            
            correct_answer = op_func(a, b)
            if correct_answer > INT32_MAX:
                correct_answer -= (UINT32_MAX + 1)
                
            session['stage2_data'] = {
                'a': a, 'b': b, 'op_symbol': op_symbol, 'correct_answer': correct_answer
            }
            return redirect(url_for('stage2'))
        elif is_human_answer(1, user_answer, a + b, correct_answer):
            session.clear()
            return redirect(url_for('index', human='true'))
        else:
            return render_template('stage1.html', a=a, b=b, error="计算错误")

@app.route('/stage2', methods=['GET', 'POST'])
def stage2():
    if 'stage' not in session or session['stage'] != 2 or 'stage2_data' not in session:
        return redirect(url_for('index'))
    
    data = session['stage2_data']
    
    if request.method == 'GET':
        return render_template('stage2.html', a=data['a'], b=data['b'], op=data['op_symbol'], error=None)
    
    else:
        user_answer = request.form.get('answer', '').strip().replace('(', '').replace(')', '')
        correct_answer = str(data.get('correct_answer', 0))
        
        if user_answer == correct_answer:
            # 完成stage2后直接跳转到success
            session['stage'] = 4
            return redirect(url_for('success'))
        elif is_human_answer(2, user_answer, data['a'] + data['b'] if data['op_symbol'] == '+' else data['a'] * data['b']):
            session.clear()
            return redirect(url_for('index', human='true'))
        else:
            return render_template('stage2.html', a=data['a'], b=data['b'], op=data['op_symbol'], error="计算错误")

# 注释掉stage3路由
# @app.route('/stage3', methods=['GET', 'POST'])
# def stage3():
#     if 'stage' not in session or session['stage'] != 3 or 'stage3_data' not in session:
#         return redirect(url_for('index'))
#     
#     data = session['stage3_data']
#     
#     if request.method == 'GET':
#         return render_template(
#             'stage3.html',
#             scenario_name=data['scenario']['name'],
#             start_date_str=data['start_date_str'],
#             add_days=data['add_days'],
#             error=None
#         )
#     
#     else:
#         user_answer = request.form.get('answer', '').strip()
#         
#         if user_answer.lower() == data['correct_date'].lower():
#             session['stage'] = 4
#             return redirect(url_for('success'))
#         elif is_human_answer(3, user_answer, data['human_date'], scenario_key=data['scenario_key']):
#             session.clear()
#             return redirect(url_for('index', human='true'))
#         else:
#             return render_template(
#                 'stage3.html',
#                 scenario_name=data['scenario']['name'],
#                 start_date_str=data['start_date_str'],
#                 add_days=data['add_days'],
#                 error="计算错误"
#             )

@app.route('/success')
def success():
    if 'stage' not in session or session['stage'] != 4:
        return redirect(url_for('index'))
    
    return render_template('success.html', flag=FLAG, image_path=random.choice(SUCCESS_IMAGES))

if __name__ == '__main__':
    os.makedirs('static/images', exist_ok=True)
    app.run(host='0.0.0.0', port=5000)
