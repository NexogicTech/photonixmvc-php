# 代码高亮示例大全

本文档展示 Photonix 文档系统支持的多种编程语言的代码高亮效果。

## Web 开发语言

### HTML

```html
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photonix 文档</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="navbar">
        <h1>Photonix 文档</h1>
    </header>
</body>
</html>
```

### CSS

```css
.content-body pre {
  background: #0d1117 !important;
  color: #c9d1d9;
  padding: 1rem !important;
  border-radius: 0 0 var(--radius) var(--radius) !important;
  font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
  line-height: 1.5;
}

/* 锚点链接 */
.anchor {
  color: #ccc;
  text-decoration: none;
  margin-right: .5rem;
  opacity: 0;
  transition: opacity .2s;
}
```

### JavaScript

```javascript
function mdToHtml(md) {
  let html = md;
  
  // 代码块（```language）- 优先处理
  html = html.replace(/```(\w*)\n([\s\S]*?)```/g, (_, lang = 'plaintext', code) => {
    return `<pre><code class="language-${lang}">${code.trim()}</code></pre>`;
  });
  
  // 代码高亮（highlight.js 自动检测语言）
  setTimeout(() => {
    if (window.hljs) {
      document.querySelectorAll('pre code').forEach(block => {
        hljs.highlightElement(block);
      });
    }
  }, 0);

  return html;
}
```

## 后端语言

### PHP

```php
<?php
namespace PhotonixCore\View;

class View {
    protected static function compiledPrologue(): string {
        return <<<'PHP'
<?php
// +----------------------------------------------------------------------
// | Nexogic Photonix MVC 模板
// +----------------------------------------------------------------------+
// | 许可证 (MIT): https://opensource.org/license/MIT
// +----------------------------------------------------------------------+

if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die('Photonix MVC require PHP > 8.0.0 !');
}
?>
PHP;
    }
    
    public static function render(string $template, array $data = []): string {
        $cacheFile = self::getCachePath($template);
        
        if (!file_exists($cacheFile) || self::shouldRecompile($template, $cacheFile)) {
            self::compile($template, $cacheFile);
        }
        
        return self::execute($cacheFile, $data);
    }
}
```

### Python

```python
def fibonacci(n):
    """Generate Fibonacci sequence up to n terms."""
    if n <= 0:
        return []
    elif n == 1:
        return [0]
    elif n == 2:
        return [0, 1]
    
    fib = [0, 1]
    for i in range(2, n):
        fib.append(fib[i-1] + fib[i-2])
    
    return fib

# Example usage
if __name__ == "__main__":
    print(fibonacci(10))
```

### SQL

```sql
-- 创建用户表
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 插入示例数据
INSERT INTO users (username, email, password_hash) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 查询用户
SELECT * FROM users WHERE username = 'admin';
```

## 配置文件

### JSON

```json
{
  "name": "photonix",
  "version": "1.0.0",
  "description": "A lightweight PHP MVC framework",
  "keywords": ["php", "mvc", "framework"],
  "license": "MIT",
  "require": {
    "php": ">=8.0",
    "ext-pdo": "*",
    "ext-json": "*"
  },
  "autoload": {
    "psr-4": {
      "PhotonixCore\\": "PhotonixCore/"
    }
  }
}
```

### YAML

```yaml
framework:
  name: Photonix
  version: 1.0.0
  description: A lightweight PHP MVC framework
  
features:
  - routing
  - middleware
  - template_engine
  - database_orm
  - validation
  
database:
  default: mysql
  connections:
    mysql:
      driver: mysql
      host: localhost
      port: 3306
      database: photonix
      username: root
      password: ""
```

## Shell 脚本

### Bash

```bash
#!/bin/bash

# Photonix 框架安装脚本

echo "🚀 开始安装 Photonix 框架..."

# 检查 PHP 版本
PHP_VERSION=$(php -v | grep -oP 'PHP \K[0-9]+\.[0-9]+')
REQUIRED_VERSION="8.0"

if (( $(echo "$PHP_VERSION < $REQUIRED_VERSION" | bc -l) )); then
    echo "❌ PHP 版本过低，需要 PHP $REQUIRED_VERSION 或更高版本"
    exit 1
fi

echo "✅ PHP 版本检查通过: $PHP_VERSION"

# 创建必要的目录
mkdir -p runtime/cache runtime/logs public/assets

# 设置权限
chmod -R 755 runtime
chmod -R 777 runtime/cache runtime/logs

echo "✅ 安装完成！"
```

## 总结

Photonix 文档系统支持以下语言的代码高亮：

| 语言 | 文件扩展名 | 用途 |
|------|------------|------|
| HTML | `.html`, `.htm` | 网页结构 |
| CSS | `.css` | 样式设计 |
| JavaScript | `.js`, `.mjs` | 前端交互 |
| PHP | `.php` | 后端逻辑 |
| Python | `.py` | 脚本编程 |
| SQL | `.sql` | 数据库查询 |
| JSON | `.json` | 数据交换 |
| YAML | `.yml`, `.yaml` | 配置文件 |
| Bash | `.sh` | Shell 脚本 |

所有代码高亮都使用 **highlight.js** 实现，确保在各种语言下都有良好的可读性和美观的显示效果。