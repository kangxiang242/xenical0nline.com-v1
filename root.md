# xenical0nline.com-v1

> **Laravel 12 + Filament 3** | 纤体減肥类目 | 原项目 xenical0nline.com (Laravel 8 + Dcat Admin) 迁移升级版

## 快速导航

| 项目 | 说明 |
|------|------|
| 生产域名 | `https://www.xenical0nline.com` |
| 本地开发 | `http://localhost:8012` |
| 后台路径 | `/ami3-17drt4-6ne634russ` (由 `ADMIN_PATH` 环境变量控制) |
| 后台管理账户 | `web0wer168888` / `8888d00rkeeper8888` |
| 技术栈 | Laravel 12 + Filament 3.x + SQLite/MySQL |
| 原项目路径 | `/Users/a123/workspace/wwwroot/纤体-減肥/X-xenical0nline/xenical0nline.com` |
| 当前路径 | `/Users/a123/workspace/wwwroot/纤体-減肥/X-xenical0nline/xenical0nline.com-v1` |

## 技术栈

| 元件 | 版本 | 说明 |
|------|------|------|
| Laravel | 12.x | 最新 LTS 版本 |
| PHP | 8.2+ |  |
| Filament | 3.x | 后台管理（替代 Dcat Admin 2.x） |
| 数据库 | SQLite (开发) / MySQL (生产) |  |
| 前端构建 | Vite | `resources/js/vite-app.js` 入口 |
| 静态资源 | `public/static/` | 传统 JS/CSS 文件 |

## 目录结构

```
xenical0nline.com-v1/
├── app/
│   ├── Console/Commands/       # Artisan 命令
│   ├── Exceptions/              # 异常处理
│   ├── Exports/                 # Excel 导出
│   ├── Filament/
│   │   ├── Components/          # 自定義表單元件
│   │   ├── Pages/               # 後台頁面
│   │   ├── Resources/           # 12 個後台資源
│   │   └── Widgets/             # Dashboard Widget
│   ├── Handlers/                # 业务处理器
│   ├── Http/
│   │   ├── Composers/           # View Composer
│   │   ├── Controllers/Web/     # 前端控制器
│   │   ├── Middleware/          # 中间件
│   │   └── Requests/            # 表单验证
│   ├── Jobs/                    # 队列任务
│   ├── Models/                  # 41 个 Eloquent Model
│   ├── Providers/               # 服务提供者
│   ├── Repositories/            # 14 个数据仓库
│   ├── Services/                # 11 个业务服务
│   └── helpers.php              # 全局辅助函数
├── config/
│   ├── global.php               # 站点配置（缓存键映射）
│   ├── captcha.php              # 验证码配置
│   └── ...                      # Laravel 标准配置
├── database/
│   └── migrations/              # 59 个迁移文件
├── resources/
│   ├── lang/                    # 语言包 (zh_TW, zh_CN, en)
│   └── views/
│       ├── web/                 # 桌面端 Blade 模板
│       │   ├── layout/          # 布局 (header/footer/layout)
│       │   ├── order/           # 订单页面
│       │   ├── product/         # 商品页面
│       │   ├── news/            # 文章页面
│       │   └── widgets/         # 通用组件
│       └── errors/              # 错误页面
├── public/
│   ├── static/                  # 静态资源 (JS/CSS/图片/字体)
│   ├── images/                  # 公共图片
│   ├── uploads/                 # 上传文件
│   └── build/                   # Vite 构建输出
└── routes/
    ├── web.php                  # 前端路由 (23 条)
    ├── api.php                  # API 路由
    └── channels.php             # WebSocket 频道
```

## 迁移记录

| 时间 | 事项 |
|------|------|
| 2026-07-16 | 从 xenical0nline.com (Laravel 8 + Dcat Admin) 迁移至 xenical0nline.com-v1 (Laravel 12 + Filament 3) |

### 迁移内容

| 类别 | 旧版 | 新版 |
|------|------|------|
| 框架 | Laravel 8.x | Laravel 12.x |
| 后台管理 | Dcat Admin 2.x | Filament 3.x |
| 后台路径 | `/admin` | `/ami3-17drt4-6ne634russ` (隐藏) |
| PHP 版本 | 7.4 | 8.2+ |
| Session 驱动 | file | database |
| Cache 驱动 | file | database |
| Queue 驱动 | sync | database |
| 图片处理 | Intervention 2.x | Intervention 3.x |
| 验证码 | mews/captcha 3.2 | mews/captcha 3.5 |
| Excel | maatwebsite/excel 3.1 | maatwebsite/excel 3.1 |

### 后台资源 (12 个)

| Resource | 标签 | 旧 Dcat Admin 对应 | 分组 | 排序 |
|----------|------|-------------------|------|------|
| OrderResource | 訂單管理 | OrderController | — | 1 |
| ProductResource | 商品管理 | ProductController | — | 2 |
| ArticleResource | 文章管理 | ArticleController | 內容管理 | 1 |
| ArticleCateResource | 文章分類 | ArticleCateController | 內容管理 | 4 |
| MessageResource | 訊息管理 | MessageController | — | 3 |
| BannerResource | 橫幅管理 | BannerController | 內容管理 | 5 |
| SeoResource | SEO管理 | SeoController | 內容管理 | 7 |
| FaqResource | FAQ管理 | FaqController | 內容管理 | 8 |
| PageResource | 單頁管理 | SiteGuideController | 內容管理 | 9 |
| AnchorResource | 錨點管理 | AnchorController | 內容管理 | 11 |
| ExceptionResource | 異常日誌 | ExceptionController | 系統管理 | 20 |
| AccessLogResource | 訪問日誌 | AccessLogController | 系統管理 | 21 |

### 保留的中间件

| 中间件 | 说明 |
|--------|------|
| AccessLogMiddleware | 访问日志记录 |
| DefendMiddleware | 请求防御 |
| RedirectDeviceMiddleware | 设备跳转 |
| EncryptCookies | Cookie 加密 |
| VerifyCsrfToken | CSRF 保护 |
| TrustProxies | 代理信任 |

### 新增功能

| 功能 | 说明 |
|------|------|
| 訂單匯出 | 全部匯出 / 匯出選中 (XLSX, 12欄) |
| Dashboard Widget | 新訂單/新留言/新設備統計 + 頁面訪問排行 |
| wangEditor5 | 富文本編輯器 (取代 Filament RichEditor) |
| Release Token | 版本追蹤系統 (`php artisan release:stamp`) |
| 客製 Login 頁 | 禁用瀏覽器自動填充帳號密碼 |
| Filament Hooks | 自訂 CSS (左標題右編輯框、FilePond 透明背景) |

## 数据库

| 项目 | 值 |
|------|-----|
| 数据库名 | `xenical0nline` |
| 生产主机 | `45.14.226.187` |
| 本地开发 | SQLite (默认) 或 MySQL |
| 迁移文件 | `database/migrations/` (59 个) |

**核心表**: articles, article_cates, orders, order_products, messages, products, product_attrs, banners, seos, configs, exceptions, faqs, anchors, slides, computes, comments, brands, success_cases, jou_access_logs, observers 等。

## 开发

```bash
# 启动开发服务器
php artisan serve --port=8012

# 启动 Vite 开发
npm run dev

# 清除缓存
php artisan optimize:clear

# 建立 Release Token
php artisan release:stamp --bump=patch   # 1.0.0 → 1.0.1
php artisan release:stamp --bump=minor   # 1.0.1 → 1.1.0
php artisan release:stamp --bump=major   # 1.0.1 → 2.0.0

# 后台访问
open http://localhost:8012/ami3-17drt4-6ne634russ
```

## 部署准备

- [ ] 切换 `DB_CONNECTION=mysql` 并配置生产数据库凭据
- [ ] 设置 `ADMIN_PATH=ami3-17drt4-6ne634russ` (生产环境)
- [ ] `APP_DEBUG=false`
- [ ] 运行 `php artisan migrate` 建立表结构
- [ ] 从生产环境导入数据
- [ ] 创建管理员用户: `php artisan tinker` → `User::create([...])`
- [ ] 运行 `npm run build` 编译前端资源
- [ ] 配置 Nginx + PHP-FPM
- [ ] 配置 Cloudflare CDN + WAF
