# sql_web171

其中的查询语句如下

```php
$sql = "select id,username,password from ".$db_table." where username !='flag' and id = '".$id."'";
```

直接 `1' or 1=1 --+` 查看所有用户即可，在回显数据底部发现 `FLAG`