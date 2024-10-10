# 介绍

DouPress 是一个针对个人网站设计的微型内容管理系统。

主要特点：

1. 不需要数据库支持，只需要一个可以运行 PHP 的 Web 环境
2. 只针对个人网站设计，没有复杂的成员管理和权限设置
3. 没有分类只有标签，免除发布文章时到底该怎么分类的纠结
4. 只有“文章”和“页面”两个系统，没有“评论”、“插件”，让你更专注于创造内容
5. 网站基本信息、主题、URL类型、ICP备案号、账号和密码等可在后台配置

# 安装

1. 下载最新版的 install.php，上传到网站根目录
2. 浏览器访问根目录下 instal.php，填入网站信息和初始账号密码
3. 开始安装

# 文件结构

```
admin              后台管理
core               核心
theme              主题
data               内容数据
  |--config.php    配置文件
  |--posts         文章
  |    |--data     数据
  |    |--index    索引
  |--pages         页面
       |--data     数据
       |--index    索引
```

# URL 格式

文章: `http://doupress.com/?post/[a-z0-5]{6}`

标签: `http://doupress.com/?tag/[^/]+/`

页面: `http://doupress.com/?([-a-zA-Z0-5]+/)+`

# 模板标签

```
mc_site_name()  // 网站标题
mc_site_desc()  // 网站描述
mc_user_nick()  // 站长昵称

mc_theme_url() // 主题文件夹中文件的URL

mc_next_post()   // 循环获取文章
mc_the_name()    // 文章标题
mc_the_date()    // 发布日期
mc_the_time()    // 发布时间
mc_the_content() // 文章内容
mc_the_tags()    // 文章标签
```
