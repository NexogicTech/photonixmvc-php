# Photonix 框架文档测试

这是一个测试文档，展示各种 Markdown 元素的渲染效果。

## 代码高亮测试

### PHP 代码示例

```php
<?php
namespace PhotonixCore\View;

class View {
    protected static function compiledPrologue(): string {
        return <<<'PHP'
<?php
// +----------------------------------------------------------------------
// | Nexogic Photonix MVC 模板
// +----------------------------------------------------------------------
// | 版权 (c) 2025 http://www.nexogic.org 保留所有权利
// +----------------------------------------------------------------------
// | 许可证 (MIT): https://opensource.org/license/MIT
// +----------------------------------------------------------------------

if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die('Photonix MVC require PHP > 8.0.0 !');
}
?>
PHP;
    }
}
```

### JavaScript 代码示例

```javascript
function mdToHtml(md) {
  let html = md
    // 代码块（```language）
    .replace(/```(\w*)\n([\s\S]*?)```/g, (_, lang = 'plaintext', code) => {
      const escaped = code.replace(/</g, '&lt;').replace(/>/g, '&gt;');
      return `<pre><code class="language-${lang}">${escaped}</code></pre>`;
    });
  
  // 代码高亮（highlight.js 自动检测语言）
  setTimeout(() => {
    if (window.hljs) {
      document.querySelectorAll('pre code').forEach(block => hljs.highlightElement(block));
    }
  }, 0);

  return html;
}
```

### CSS 代码示例

```css
.content-body pre {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: var(--radius);
  padding: 1.5rem;
  overflow-x: auto;
  font-size: .9rem;
  line-height: 1.5;
  margin: 1.5rem 0;
  position: relative;
}
```

## 表格测试

| 功能 | 状态 | 描述 |
|------|------|------|
| 代码高亮 | ✅ | 支持多种语言 |
| 表格渲染 | ✅ | 美观的表格样式 |
| 图片显示 | ✅ | 自适应图片大小 |
| 引用块 | ✅ | 优雅的引用样式 |

## 列表测试

### 无序列表

* 第一项
* 第二项
  * 子项 A
  * 子项 B
* 第三项

### 有序列表

1. 第一步
2. 第二步
   1. 子步骤 1
   2. 子步骤 2
3. 第三步

## 引用测试

> 这是一个引用块，用于显示重要的提示信息或引用他人的话语。
> 
> 引用可以包含多行内容，并且会自动格式化。

## 文本格式化测试

这是 **粗体文本**，这是 *斜体文本*，这是 ~~删除线文本~~，这是 `行内代码`。

## 链接和图片测试

这是一个 [链接到 GitHub](https://github.com) 的示例。

自动链接：https://highlightjs.org/

## 分隔线

---

## 总结

这个测试文档展示了 Photonix 文档系统的各种 Markdown 渲染功能，包括：

* **代码高亮**：支持 PHP、JavaScript、CSS 等多种语言
* **表格**：美观的表格样式，支持对齐
* **列表**：支持嵌套的无序和有序列表
* **引用**：优雅的引用块样式
* **文本格式化**：粗体、斜体、删除线、行内代码
* **链接和图片**：支持超链接和图片显示
* **分隔线**：清晰的内容分隔

所有渲染都是纯前端完成，无需 PHP 后端支持，确保文档系统的高性能和可移植性。