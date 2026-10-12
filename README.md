# DouPress 程江®博客管理系统

## 🚀 项目介绍

DouPress 是一套基于 PHP 语言开发的开源、简洁、高效的博客程序，它满足个人博客必要的建站需求，剔除了常见博客程序复杂臃肿的功能。

DouPress 不需要数据库的支持，采用的是文本数据库的存储形式。上传本程序到支持 PHP 的主机或虚拟空间，立即就可使用。博客内容的备份和还原非常方便，只需下载和上传程序目录下的全部文件即可，所有的文章、配置等均可完整保留和再现。

## ✨ 功能特性

1. 不需要数据库支持，只需要一个可以运行 PHP 的 Web 环境
2. 只针对个人网站设计，没有复杂的成员管理和权限设置
3. 没有分类只有标签，免除发布文章时到底该怎么分类的纠结
4. 只有"文章"和"页面"两个系统，没有"评论"、"插件"，让你更专注于创造内容
5. 内置 SEO 支持：文章描述、关键词、最后修改时间、sitemap.xml 自动生成
6. 其他功能：网站基本信息、副标题、状态、主题、URL 类型、ICP 备案号、头部/底部代码、账号和密码等可在后台配置

## 📦 部署安装

1. 下载最新版的 install.php，上传到网站根目录
2. 浏览器访问根目录下 instal.php（例如：`http://example.com/instal.php`），填入网站信息和初始账号密码
3. 开始安装

## 📁 项目结构

```
/src/
├── index.php        # 入口文件
├── admin/           # 后台管理
├── core/            # 核心
├── data/            # 内容数据
│   ├── config.php   # 配置文件
│   ├── posts/       # 文章
│   │   ├── data/    # 数据
│   │   └── index/   # 索引
│   └── pages/       # 页面
│       ├── data/    # 数据
│       └── index/   # 索引
│   └── hits/        # 浏览量数据
│       ├── posts/   # 文章浏览量
│       ├── pages/   # 页面浏览量
│       └── site.dat # 网站总浏览量汇总文件
├── .htaccess        # Apache rewrite 配置文件
├── nginx.conf       # Nginx rewrite 配置文件
├── build.php        # 打包发布安装脚本 install.php
├── install.php      # 系统安装文件
└── theme/           # 主题目录
```

## 🎨 网页 URL 格式

### 默认 query 模式

文章: `http://example.com/?post/[a-z0-9]{1,8}`

标签: `http://example.com/?tag/[^/]+`

日期: `http://example.com/?date/([0-9]{4}-[0-9]{2}`

页面: `http://example.com/?([-_.a-zA-Z0-9]+)+`

### 可选 history 模式，需要服务器支持开启路径重写

文章: `http://example.com/post/[a-z0-9]{1,8}`

标签: `http://example.com/tag/[^/]+`

日期: `http://example.com/date/([0-9]{4}-[0-9]{2}`

页面: `http://example.com/([-_.a-zA-Z0-9]+)+`

## 主题模板标签函数

```
app_site_name()    // 网站标题
app_site_subname() // 网站副标题（未配置时回退网站描述）
app_site_desc()    // 网站描述
app_nick_name()    // 站长昵称
app_theme_url()    // 主题文件夹中文件的URL

app_seo_title()       // 网页标题（文章/页面：标题|站名，其他：站名|副标题）
app_seo_description() // 网页 SEO description（文章/页面用desc，其他用网站描述）
app_seo_keywords()    // 网页 SEO keywords（文章/页面用tags，其他用网站关键词）

app_next_post()    // 循环获取文章
app_post_title()   // 文章标题
app_post_link()    // 文章标题A链接
app_post_content() // 文章内容
app_post_url()     // 文章URL
app_post_date()    // 文章发布日期
app_post_time()    // 文章发布时间
app_post_tags()    // 文章标签
app_mod_time()     // 文章最后修改时间（有修改时间才显示）
app_mod_time_has() // 是否有最后修改时间

app_comment_code() // 文章评论代码
app_footer_code()  // 网页底部代码
app_header_code()  // 网页头部代码（</head>前）

app_post_hits()    // 文章/页面浏览量
app_site_hits()    // 网站总浏览量（O(1)读取汇总文件）
app_icp_no()       // ICP备案号链接（未配置时输出空）
```

## 📦 打包发布

打包安装包文件 install.php 的构建命令

```shell
# 使用 php 命令执行
php build.php [指定版本号]
# 示例
php build.php 1.11.3
```

## 🤝 贡献及反馈

欢迎提交 [Issue](https://github.com/DouPress/DouPress/issues) 和 [Pull Request](https://github.com/DouPress/DouPress/pulls)！

## 📄 许可证

[MIT License](LICENSE)

## 🙏 致谢

感谢所有为这个项目做出贡献的开发者们！尤其感谢[上游仓库](https://github.com/bg5sbk/MiniCMS)
