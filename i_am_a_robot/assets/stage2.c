#include <stdio.h>
#include <stdint.h>

int main() {
    // 直接在代码中定义要计算的数值
    int32_t a = 1145141919810;		// 可替换为第二关的第一个数
    int32_t b = 1145141919810;		// 可替换为第二关的第二个数
    
    // 计算加法（自动处理溢出）
    int32_t add_result = a + b;
    // 计算乘法（先64位计算再截断，模拟C语言溢出）
    int32_t mul_result = (int32_t)((int64_t)a * b);
    
    // 输出结果
    printf("32位整数计算结果：\n");
    printf("%d + %d = %lld\n", a, b, (long long)add_result);
    printf("%d * %d = %lld\n", a, b, (long long)mul_result);
    
    return 0;
}