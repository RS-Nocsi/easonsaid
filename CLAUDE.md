# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 项目概述

EasonSaid 是一个随机展示陈奕迅歌词分享的网站，由前端静态站点 + PHP API 后端组成。域名：`easonsaid.cn`（前端）、`api.easonsaid.cn`（API）。

## 项目结构

```
/                       ← 前端主站（部署到 easonsaid.cn）
├── index.html          ← 主页
├── script.js           ← 前端逻辑
├── styles.css          ← 样式
├── 404.html            ← 404页面（内联 JS，不依赖 script.js）
├── schema.sql          ← 数据库结构（建表 + 视图）
├── database.csv        ← 歌词数据（UTF-8 BOM 编码）
└── api/                ← PHP 后端（部署到 api.easonsaid.cn）
    ├── BaseEasonAPI.php    ← 基类：PDO 数据库连接、CORS、随机查询
    ├── output.php          ← 歌词 API（继承 BaseEasonAPI）
    ├── blog.php            ← 博客嵌入用歌词 API（HTML格式）
    ├── portrait_images.php ← 竖屏壁纸 API（随机返回图片流）
    ├── landscape_images.php← 横屏壁纸 API（随机返回图片流）
    ├── datouzai_icon.php   ← 大头仔图标 API（随机返回 favicon）
    ├── config.php          ← 数据库配置（已 gitignore，含敏感信息）
    ├── index.html          ← API 首页（开发者文档入口）
    ├── docs.html           ← API 文档页
    ├── icon/               ← 大头仔图标资源
    └── pics/
        ├── portrait/       ← 竖屏壁纸（webp 格式）
        └── landscape/      ← 横屏壁纸（webp 格式）
```

## 架构要点

### 后端（PHP）

- **无框架**：纯 PHP，使用 PDO 连接 MySQL
- **继承体系**：`BaseEasonAPI` → `EasonSaidOutputAPI` / `EasonSaidBlogAPI`
- **数据库**：查询 `eason_said_with_counts` 视图，字段包括 `Contents`（歌词）、`Source`（出处）、`Contributors`（分享者）、`LyricShareCount`、`SongShareCount`
- **随机查询**：`ORDER BY RAND() LIMIT 1`
- **CORS**：`BaseEasonAPI` 中硬编码允许 `easonsaid.cn` 的变体域名；壁纸/图标 API 使用 `Access-Control-Allow-Origin: *`
- **壁纸/图标 API**：直接用 `glob()` 扫描文件 + `array_rand()` + `readfile()` 输出图片流，不走数据库

### 前端

- **纯 HTML/CSS/JS**，无构建工具、无框架
- **API_BASE**：`https://api.easonsaid.cn`（script.js 第2行）
- **背景图片**：根据屏幕方向（portrait/landscape）请求不同 API，通过 CSS 变量 `--bg-image` + `body::before` 实现
- **缓存策略**：前端用 `?v=2.0`/`?v=3.0` 版本号缓存静态资源；背景图片用时间戳避免缓存
- **歌词解析**：正则匹配 `"xxx" 向你分享："xxx"。它出自：《xxx》。` 格式的文本响应

### API 端点

| 端点 | 返回格式 | 说明 |
|------|---------|------|
| `GET /output.php` | text/plain | 随机歌词（默认纯文本） |
| `GET /output.php?output=json` | application/json | 随机歌词（JSON 格式） |
| `GET /blog.php` | text/html | 歌词 HTML 片段（用于嵌入） |
| `GET /landscape_images.php` | image/* | 随机横屏壁纸 |
| `GET /portrait_images.php` | image/* | 随机竖屏壁纸 |
| `GET /datouzai_icon.php` | image/* | 随机大头仔图标 |

## 关键注意事项

- `api/config.php` 中的数据库敏感信息已手动删除，可正常提交
- 壁纸/图标 API 的 CORS 是 `*`，歌词 API 的 CORS 受 `BaseEasonAPI` 中白名单限制
- 前端 JS 中 `console.log` 语句用于调试背景图片加载，修改时注意保留或清理
- 404 页面有独立的内联 JS 设置背景图片（不依赖 script.js）
- CSS 使用 `100dvh`（dynamic viewport height）配合 `100vh` 回退，适配移动端浏览器
