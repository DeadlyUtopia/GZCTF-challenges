#include <stdlib.h>
#include <unistd.h>

int main(int argc, char *argv[]) {
    if (argc > 1) {
        setuid(0); // 切换到 root 用户权限
        system(argv[1]); // 执行传入的命令
    }
    return 0;
}
