import pandas as pd
import json
import random
import datetime

# 路径配置（绝对路径，避免相对路径风险）
INPUT_EXCEL = '/var/www/data/list.xlsx'
ADDRESS_JSON = '/var/www/data/address.json'
MAPPING_JSON = '/var/www/data/student_mapping.json'
OUTPUT_EXCEL = '/var/www/html/list_processed.xlsx'

def generate_female_id():
    """生成符合要求的女性身份证号（1970-01-01至2038-01-19）"""
    # 读取并筛选有效地址码（6位，后两位非00）
    with open(ADDRESS_JSON, 'r', encoding='utf-8') as f:
        address_data = json.load(f)
    valid_codes = [code for code in address_data.keys() 
                  if len(code) == 6 and code[-2:] != '00']
    address_code = random.choice(valid_codes)

    # 生成出生日期（1970-01-01至2038-01-19）
    start = datetime.date(1970, 1, 1)
    end = datetime.date(2038, 1, 19)
    delta = end - start
    birth_date = start + datetime.timedelta(days=random.randint(0, delta.days))
    birth_str = birth_date.strftime('%Y%m%d')

    # 生成顺序码（第17位为偶数，确保女性）
    sequence = random.randint(0, 499) * 2  # 0-998间的偶数
    sequence_str = f"{sequence:03d}"

    # 计算校验码
    pre_17 = address_code + birth_str + sequence_str
    weights = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2]
    check_dict = {0:'1',1:'0',2:'X',3:'9',4:'8',5:'7',6:'6',7:'5',8:'4',9:'3',10:'2'}
    total = sum(int(pre_17[i]) * weights[i] for i in range(17))
    check_code = check_dict[total % 11]

    return pre_17 + check_code

def process_excel():
    """处理Excel文件，生成完整信息和处理后文件（适配实际表头行+列结构）"""
    # 关键修正：指定表头行为第3行（index=2，Excel行号从0开始），跳过前两行
    df = pd.read_excel(INPUT_EXCEL, header=2)
    
    # 清理空列（删除全为空的列）
    df = df.dropna(axis=1, how='all')
    
    # 提取学生信息（姓名、学号），填充身份证号
    students = []
    for idx, row in df.iterrows():
        # 只处理前5条有效学生数据（序号1-5）
        if pd.notna(row['姓名']) and pd.notna(row['学号']) and idx < 5:
            id_card = generate_female_id()
            students.append({
                'name': str(row['姓名']).strip(),
                'student_id': str(row['学号']).strip(),
                'id_card': id_card
            })
            # 填充身份证号码列
            df.at[idx, '身份证号码'] = id_card

    # 保存包含完整信息的原始文件（覆盖源文件）
    df.to_excel(INPUT_EXCEL, index=False, header=True)
    
    # 生成映射关系文件（供登录/查询使用）
    with open(MAPPING_JSON, 'w', encoding='utf-8') as f:
        json.dump(students, f, ensure_ascii=False)
    
    # 生成处理后的文件（隐藏生日+删除学号）
    processed_df = df.copy()
    # 删除学号列
    if '学号' in processed_df.columns:
        processed_df = processed_df.drop(columns=['学号'])
    # 隐藏身份证中的生日（第7-14位替换为*）
    if '身份证号码' in processed_df.columns:
        processed_df['身份证号码'] = processed_df['身份证号码'].apply(
            lambda x: x[:6] + '********' + x[14:] if isinstance(x, str) and len(x) == 18 else x
        )
    # 保存处理后的文件到Web根目录（供用户下载）
    processed_df.to_excel(OUTPUT_EXCEL, index=False, header=True)

if __name__ == '__main__':
    process_excel()