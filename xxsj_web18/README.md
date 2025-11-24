# xxsj_web18

查看网页源码可以看到 `js` 文件中有一个函数

```js
function checkClearCondition() {
    if (score > 120 && !game_over) {
        (async () => {
            const s = await (await fetch('?v=1')).text();
            console.log("&#85;&#82;&#76;&#47;&#63;&#102;&#108;&#97;&#103;&#61;&#115;");
            console.log(s);
            message.textContent = "Reset:"+s;
        })();
        game_over = true;
        updateScoreboard();
    }
}
```

## 第一种方法

访问容器网页，按下 <kbd>F12</kbd> 打开开发人员工具，找到控制台。按下 <kbd>CTRL</kbd>+<kbd>R</kbd> 重新加载，然后依次输入以下内容

```
score=130
game_over=false
run()
```

会返回类似如下内容

```
&#85;&#82;&#76;&#47;&#63;&#102;&#108;&#97;&#103;&#61;&#115;
hsqjhpu0
```

`&#85;&#82;&#76;&#47;&#63;&#102;&#108;&#97;&#103;&#61;&#115;`Unicode解码出来就是 `URL/?flag=s`

访问 `URL/?flag=hsqjhpu0` 即可得到 `flag`

## 第二种方法

访问 `URL/?v=1` 得到`hsqjhpu0`

访问 `URL/?flag=hsqjhpu0` 得到 `flag`