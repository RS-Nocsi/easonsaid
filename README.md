# EasonSaid

属于EF们的答案之书！

本项目最开始是想搜集大家喜欢的Eason的歌词，然后我做成一个类似一言Hitokoto的网站。[在小红书上发了一个帖子](http://xhslink.com/o/6QzemhsOyIY)
后来受我的好朋友 @缪斯茶 启发，Eason的歌词中充满人生百态，生活中遇到了什么事情，都能从Eason的歌词中找到答案，被触动。我被打动了，我觉得这个网站就是属于EF的答案之书，当你生活中遇到了什么事情，都可以来这里看看，说不定里面的某一句歌词会触动你！
随后在小红书上[正式公布了这个项目](http://xhslink.com/o/3H400uyVW3X)

过了一段时间，于2026年6月9日增加了API说明页和API文档，开放给大家调用。并正式在Github上开源！

## 链接

- **主站**：[https://easonsaid.cn](https://easonsaid.cn)
- **API**：[https://api.easonsaid.cn](https://api.easonsaid.cn)

## 技术栈

本项目所有代码完全开源，包括数据库结构和数据（最后更新于2026年6月9日）
- 前端：原生 HTML / CSS / JavaScript
- 后端：PHP + MySQL

本项目有使用AI进行辅助开发（非纯AI生成）

### 项目结构

```
├── index.html          主页
├── script.js           前端逻辑（API 请求、背景图片加载、导航菜单）
├── styles.css          响应式样式（适配横竖屏、桌面/移动端）
├── 404.html            404 页面（内联 JS，独立于主站脚本）
├── schema.sql          数据库结构（建表 + 视图）
├── database.csv        歌词数据（165条，UTF-8 BOM 编码）
└── api/                PHP 后端（api.easonsaid.cn）
    ├── BaseEasonAPI.php      基类：PDO 数据库连接、CORS、通用查询方法
    ├── output.php            歌词 API，支持纯文本和 JSON 两种输出格式
    ├── blog.php              博客嵌入用歌词 API，返回 HTML 片段
    ├── portrait_images.php   竖屏壁纸 API
    ├── landscape_images.php  横屏壁纸 API
    ├── datouzai_icon.php     大头仔图标 API
    ├── config.php            数据库配置（数据库信息已删除）
    ├── index.html            API 首页
    ├── docs.html             API 文档
    ├── icon/                 大头仔图标资源
    └── pics/
        ├── portrait/         竖屏壁纸（webp）
        └── landscape/        横屏壁纸（webp）
```

### 技术原理

**前端**：纯原生实现，无框架无构建工具。页面加载时通过 `fetch()` 请求歌词 API，用正则解析返回的文本格式并渲染到页面。背景图片根据屏幕方向（portrait / landscape）自动请求对应的壁纸 API，通过 CSS 变量 `--bg-image` 配合 `body::before` 伪元素实现全屏背景。静态资源使用版本号（`?v=3.0`）缓存，背景图片使用时间戳避免缓存。

**后端**：PHP 无框架，使用 PDO 连接 MySQL。`BaseEasonAPI` 基类封装了数据库连接、CORS 头设置和随机查询逻辑（`ORDER BY RAND() LIMIT 1` 查询 `eason_said_with_counts` 视图），歌词 API 和博客 API 继承该基类并各自实现不同的输出格式。壁纸和图标 API 则独立于数据库，通过 `glob()` 扫描本地图片目录随机返回一张图片流。

### 免责声明
1. 本项目及其网站为非营利性质，相关费用只会由站长一人承担，永远不会以捐赠、付费使用等任何形式收费，代码以MIT协议完全开源

2. 本项目所有分享内容均来自网络，随机展示，版权归原作者所有，本人不享有壁纸图片和歌词的版权

3. 背景图片收集自[@林默](https://xhslink.com/m/NfFAx2544w)，已获得使用许可

4. 用户因使用本项目内容而产生的任何后果，本人不承担相关责任

5. 如发现侵权内容，或者分享内容出现错误，或者有任何建议，都请联系我。

## 开源协议

MIT
