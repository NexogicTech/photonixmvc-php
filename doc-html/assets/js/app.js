(() => {
  const SIDEBAR_KEY = 'doc-sidebar-state';
  const ACTIVE_KEY = 'doc-active-item';
  const sidebar = document.getElementById('sidebar');
  const sidebarNav = document.getElementById('sidebar-nav');
  const contentBody = document.getElementById('content-body');
  const searchInput = document.getElementById('search-input');
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const sidebarClose = document.getElementById('sidebar-close');

  let sidebarState = JSON.parse(localStorage.getItem(SIDEBAR_KEY) || '{}');
  let activeItem = localStorage.getItem(ACTIVE_KEY) || '';

  // 构建侧边栏
  async function buildSidebar() {
    sidebarNav.innerHTML = '';
    const tree = await fetchTree('../docs');
    renderNav(tree, sidebarNav, 0);
    restoreSidebarState();
    setActiveItem(activeItem);
  }

  // 获取目录树（本地扫描）
  async function fetchTree(base) {
    try {
      const res = await fetch('tree.php');
      if (!res.ok) throw new Error('tree.php 不可用');
      return await res.json();
    } catch {
      // 降级：使用硬编码结构
      return [
      { name: '代码示例', path: 'code-examples.md', children: [] },
      { name: '测试文档', path: 'test.md', children: [] },
      { name: '总述', path: '总述.md', children: [] },
      {
        name: '路由',
        path: '',
        children: [
          { name: '简介', path: 'router/Photonix Router简介.md', children: [] },
          { name: '注册路由', path: 'router/注册路由.md', children: [] },
          { name: '绑定路由', path: 'router/绑定路由.md', children: [] },
          { name: '访问已定义的路由', path: 'router/访问已定义的路由.md', children: [] },
          { name: '路由配置', path: 'router/路由配置.md', children: [] }
        ]
      },
      {
        name: '控制器',
        path: '',
        children: [
          { name: '概述', path: 'controller/1-概述.md', children: [] },
          { name: '快速开始', path: 'controller/2-快速开始.md', children: [] },
          { name: '请求对象Request', path: 'controller/3-请求对象Request.md', children: [] },
          { name: '参数映射与路由', path: 'controller/4-参数映射与路由.md', children: [] },
          { name: '响应与视图', path: 'controller/5-响应与视图.md', children: [] },
          { name: 'JSON输出', path: 'controller/6-JSON输出.md', children: [] },
          { name: '错误处理与日志', path: 'controller/7-错误处理与日志.md', children: [] },
          { name: '最佳实践', path: 'controller/8-最佳实践.md', children: [] }
        ]
      },
      {
        name: '视图',
        path: '',
        children: [
          { name: '概述', path: 'view/1-概述.md', children: [] },
          { name: '模板语法', path: 'view/2-模板语法.md', children: [] },
          { name: '参数与assign', path: 'view/3-参数与assign.md', children: [] },
          { name: '目录与缓存', path: 'view/4-目录与缓存.md', children: [] },
          { name: '示例', path: 'view/5-示例.md', children: [] }
        ]
      },
      {
        name: '数据库',
        path: '',
        children: [
          { name: '使用Photonix DB连接数据库', path: 'db/使用Photonix DB连接数据库.md', children: [] }
        ]
      },
      {
        name: '模型',
        path: '',
        children: [
          { name: '使用Phtonix ORM', path: 'model/使用Phtonix ORM.md', children: [] }
        ]
      },
      {
        name: '插件',
        path: '',
        children: [
          { name: '下载', path: 'plugin/下载.md', children: [] }
        ]
      },
      {
        name: 'API',
        path: '',
        children: [
          { name: '概述', path: 'api/log/1-概述.md', children: [] },
          { name: '快速开始', path: 'api/log/2-快速开始.md', children: [] },
          { name: '配置说明', path: 'api/log/3-配置说明.md', children: [] },
          { name: 'API参考', path: 'api/log/4-API参考.md', children: [] },
          { name: '最佳实践', path: 'api/log/5-最佳实践.md', children: [] }
        ]
      }
    ];
    }
  }

  // 渲染导航
  function renderNav(nodes, container, depth) {
    const ul = document.createElement('ul');
    nodes.forEach(node => {
      const li = document.createElement('li');
      if (node.children && node.children.length) {
        const folder = document.createElement('div');
        folder.className = 'folder';
        folder.textContent = node.name;
        folder.tabIndex = 0;
        folder.addEventListener('click', () => toggleFolder(folder));
        folder.addEventListener('keydown', e => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleFolder(folder);
          }
        });
        li.appendChild(folder);
        const childUl = document.createElement('ul');
        childUl.className = 'collapse';
        renderNav(node.children, childUl, depth + 1);
        li.appendChild(childUl);
      } else {
        const a = document.createElement('a');
        a.href = '#' + encodeURIComponent(node.path);
        a.textContent = node.name;
        a.className = 'level-' + Math.min(depth, 3);
        a.addEventListener('click', e => {
          e.preventDefault();
          loadDoc(node.path, a);
        });
        li.appendChild(a);
      }
      ul.appendChild(li);
    });
    container.appendChild(ul);
  }

  // 展开/折叠
  function toggleFolder(folder) {
    folder.classList.toggle('open');
    const collapse = folder.nextElementSibling;
    if (collapse) {
      collapse.style.display = folder.classList.contains('open') ? 'block' : 'none';
    }
    saveSidebarState();
  }

  // 持久化侧边栏状态
  function saveSidebarState() {
    const state = {};
    sidebarNav.querySelectorAll('.folder').forEach(f => {
      state[f.textContent] = f.classList.contains('open');
    });
    localStorage.setItem(SIDEBAR_KEY, JSON.stringify(state));
  }
  function restoreSidebarState() {
    Object.entries(sidebarState).forEach(([name, open]) => {
      const folder = [...sidebarNav.querySelectorAll('.folder')].find(f => f.textContent === name);
      if (folder && open) {
        folder.classList.add('open');
        const collapse = folder.nextElementSibling;
        if (collapse) collapse.style.display = 'block';
      }
    });
  }

  // 设置活动项
  function setActiveItem(path) {
    sidebarNav.querySelectorAll('a').forEach(a => a.classList.remove('active'));
    if (path) {
      const target = [...sidebarNav.querySelectorAll('a')].find(a => a.getAttribute('href') === '#' + encodeURIComponent(path));
      if (target) {
        target.classList.add('active');
        target.scrollIntoView({ block: 'nearest' });
      }
    }
  }

  // 加载文档
  async function loadDoc(path, link) {
    if (!path) return;
    contentBody.innerHTML = '<div class="loading">加载中...</div>';
    try {
      const res = await fetch('../docs/' + path);
      if (!res.ok) throw new Error('文档不存在');
      const md = await res.text();
      const html = mdToHtml(md);
      contentBody.innerHTML = html;
      activeItem = path;
      localStorage.setItem(ACTIVE_KEY, path);
      setActiveItem(path);
      // 锚点滚动
      const hash = location.hash.slice(1);
      if (hash && hash !== encodeURIComponent(path)) {
        const el = document.getElementById(decodeURIComponent(hash));
        if (el) el.scrollIntoView({ behavior: 'smooth' });
      }
    } catch (e) {
      contentBody.innerHTML = '<p style="color:#c00">加载失败：' + e.message + '</p>';
    }
  }

  // 调用独立 Markdown 解析器（纯前端）
  function mdToHtml(md) {
    return window.mdToHtml ? window.mdToHtml(md) : '<pre>' + md + '</pre>';
  }

  // 搜索高亮
  searchInput.addEventListener('input', () => {
    const kw = searchInput.value.trim().toLowerCase();
    sidebarNav.querySelectorAll('a').forEach(a => {
      const txt = a.textContent.toLowerCase();
      a.style.display = kw ? (txt.includes(kw) ? 'flex' : 'none') : 'flex';
    });
  });

  // 侧边栏开关
  sidebarToggle.addEventListener('click', () => sidebar.classList.add('show'));
  sidebarClose.addEventListener('click', () => sidebar.classList.remove('show'));

  // 键盘导航
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') sidebar.classList.remove('show');
  });

  // 初始化
  buildSidebar();
  // 默认加载 index.md
  if (!location.hash) loadDoc('index.md', null);
  else {
    const path = decodeURIComponent(location.hash.slice(1));
    loadDoc(path, null);
  }
})();