// 轻量级 Markdown 解析 + 代码高亮（纯前端，无需 PHP）
// 支持：标题、粗斜体、行内代码、代码块、链接、列表、引用、表格、图片、换行、分隔线

function mdToHtml(md) {
  let html = md;
  
  // 转义 HTML 特殊字符
  html = html.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  
  // 代码块（```language）- 优先处理
  html = html.replace(/```(\w*)\n([\s\S]*?)```/g, (_, lang = 'plaintext', code) => {
    return `<pre><code class="language-${lang}">${code.trim()}</code></pre>`;
  });
  
  // 表格
  html = html.replace(/\n\n\|(.+)\|\n\|(.+)\|\n((?:\|.+\|\n)*)/g, (match, header, separator, rows) => {
    const headers = header.split('|').map(h => h.trim()).filter(h => h);
    const headerHtml = '<thead><tr>' + headers.map(h => `<th>${h}</th>`).join('') + '</tr></thead>';
    
    const rowMatches = rows.match(/\|(.+)\|/g) || [];
    const rowHtml = rowMatches.map(row => {
      const cells = row.replace(/^\||\|$/g, '').split('|').map(c => c.trim());
      return '<tr>' + cells.map(c => `<td>${c}</td>`).join('') + '</tr>';
    }).join('');
    
    return `<table>${headerHtml}<tbody>${rowHtml}</tbody></table>`;
  });
  
  // 图片
  html = html.replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img src="$2" alt="$1">');
  
  // 引用（多行）
  html = html.replace(/(^> .*(?:\n> .*)*)/gm, (match) => {
    const content = match.replace(/^> /gm, '').replace(/^>$/gm, '');
    return `<blockquote>${content}</blockquote>`;
  });
  
  // 列表（支持嵌套）
  html = html.replace(/^(\s*)[*+-] (.+)$/gm, (match, indent, content) => {
    const level = Math.floor(indent.length / 2);
    return `${'  '.repeat(level)}<li>${content}</li>`;
  });
  
  // 有序列表
  html = html.replace(/^(\s*)\d+\. (.+)$/gm, (match, indent, content) => {
    const level = Math.floor(indent.length / 2);
    return `${'  '.repeat(level)}<li>${content}</li>`;
  });
  
  // 处理列表结构
  html = html.replace(/(<li>.*<\/li>)(?=\s*<li>)/g, '$1\n');
  html = html.replace(/(<li>.*<\/li>)/g, '<ul>\n$1\n</ul>');
  html = html.replace(/<\/ul>\s*<ul>/g, '\n');
  
  // 标题（带 ID 和锚点）
  html = html.replace(/^### (.*)$/gm, '<h3 id="$1"><a href="#$1" class="anchor">#</a> $1</h3>');
  html = html.replace(/^## (.*)$/gm, '<h2 id="$1"><a href="#$1" class="anchor">#</a> $1</h2>');
  html = html.replace(/^# (.*)$/gm, '<h1 id="$1"><a href="#$1" class="anchor">#</a> $1</h1>');
  
  // 粗体/斜体
  html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
  html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
  
  // 删除线
  html = html.replace(/~~(.*?)~~/g, '<del>$1</del>');
  
  // 行内代码
  html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
  
  // 链接
  html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>');
  
  // 自动链接
  html = html.replace(/(?<!\]\()https?:\/\/[^\s]+/g, '<a href="$&">$&</a>');
  
  // 分隔线
  html = html.replace(/^---+$/gm, '<hr>');
  html = html.replace(/^\*\*\*+$/gm, '<hr>');
  html = html.replace(/^___+$/gm, '<hr>');
  
  // 段落（改进处理）
  html = html.split('\n\n').map(block => {
    block = block.trim();
    if (!block) return '';
    
    // 如果已经是 HTML 块级元素，直接返回
    if (block.match(/^<(h[1-6]|pre|blockquote|ul|ol|table|hr)/)) {
      return block;
    }
    
    // 处理段落内的换行
    block = block.replace(/\n/g, '<br>');
    return `<p>${block}</p>`;
  }).join('\n');

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

// 导出供 app.js 使用
window.mdToHtml = mdToHtml;