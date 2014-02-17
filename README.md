cnblogs to typecho 
---
在 typecho 的官方论坛看到有朋友需要一个这样的功能，来将博客园（cnblogs）的数据导入到 typecho 程序中，所以写了一个简单的小程序，实现这个功能。

#### 使用说明

一、将 cnblogs-to-typecho.php 放置在任意能访问数据库的环境中，最好是与 typecho 相同目录；  
二、确保运行环境支持 pdo 操作 mysql；  
三、访问 cnblogs-to-typecho.php 按要求填写相关配置；  
四、提交。 

#### 补充说明

一、程序非常简单，并且只支持 pdo 操作 mysql，如果你的环境不支持 pdo 或者你的 typecho 是使用 sqlite 数据库的，请自行修改源代码；  
二、运行之前请自行备份好 typecho 的数据，因为这个程序没有做太多的考虑，以及异常处理；

---
**code by [abelyao](http://www.abelyao.com/), 2014**
