// layout.js — shared sidebar + topbar logic

const NAV_LINKS = [
  { href: 'dashboard.html', icon: '📊', label: 'Tableau de bord', section: null },
  { href: 'teachers.html', icon: '👩‍🏫', label: 'Enseignants', section: 'Gestion' },
  { href: 'profile.html', icon: '👤', label: 'Mon profil', section: null },
];

function buildSidebar(activePage) {
  const user = JSON.parse(localStorage.getItem('edu_user') || '{"name":"Admin Demo","role":"admin"}');
  const initials = user.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);

  let navHTML = '';
  let lastSection = null;

  NAV_LINKS.forEach(link => {
    if (link.section && link.section !== lastSection) {
      navHTML += `<div class="nav-section-title">${link.section}</div>`;
      lastSection = link.section;
    }
    const active = activePage === link.href ? 'active' : '';
    navHTML += `
      <a href="${link.href}" class="nav-link ${active}">
        <span class="nav-icon">${link.icon}</span>
        ${link.label}
      </a>`;
  });

  return `
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo">
        <div class="logo-icon">🎓</div>
        <div>
          <h1>EduAdmin</h1>
          <span>Gestion scolaire</span>
        </div>
      </div>
      <nav class="sidebar-nav">
        ${navHTML}
      </nav>
      <div class="sidebar-footer">
        <div class="user-avatar">${initials}</div>
        <div class="user-info">
          <div class="user-name">${user.name}</div>
          <div class="user-role">${user.role === 'admin' ? 'Administrateur' : 'Enseignant'}</div>
        </div>
        <button class="btn btn-ghost btn-sm" onclick="logout()" title="Déconnexion" style="padding:6px 8px; flex-shrink:0">🚪</button>
      </div>
    </aside>`;
}

function buildTopbar(title) {
  return `
    <header class="topbar">
      <button class="btn btn-ghost" id="menu-toggle" onclick="toggleSidebar()" style="display:none">☰</button>
      <span class="topbar-title">${title}</span>
      <div class="topbar-actions">
        <button class="btn btn-ghost btn-sm" onclick="window.location.href='profile.html'" title="Profil">👤</button>
        <button class="btn btn-ghost btn-sm" onclick="logout()" title="Déconnexion">🚪</button>
      </div>
    </header>`;
}

function initLayout(activePage, topbarTitle) {
  const layout = document.getElementById('app-layout');
  if (!layout) return;
  layout.innerHTML = buildSidebar(activePage) + `<div class="main" id="main-content">${buildTopbar(topbarTitle)}<div class="page-content" id="page-content"></div></div>`;

  // responsive
  if (window.innerWidth <= 768) {
    document.getElementById('menu-toggle').style.display = 'flex';
  }
}

function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

function logout() {
  localStorage.removeItem('edu_user');
  window.location.href = 'login.html';
}
