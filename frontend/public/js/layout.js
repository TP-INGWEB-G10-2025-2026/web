// Layout builder
function buildLayout({ role = "teacher", pageTitle = "", activePage = "" }) {
  const teacherLinks = `
    <p class="nav-section-title">Espace Enseignant</p>
    <a href="../pages/teacher-new-reservation.html" class="nav-link ${activePage === "new" ? "active" : ""}">
      <span class="nav-icon">➕</span> Nouvelle demande
    </a>
    <a href="../pages/teacher-reservations.html" class="nav-link ${activePage === "my-res" ? "active" : ""}">
      <span class="nav-icon">📋</span> Mes réservations
    </a>
  `;

  const adminLinks = `
    <p class="nav-section-title">Espace Administrateur</p>
    <a href="../pages/admin-reservations.html" class="nav-link ${activePage === "admin-list" ? "active" : ""}">
      <span class="nav-icon">📊</span> Toutes les demandes
    </a>
    <a href="../pages/admin-validate.html" class="nav-link ${activePage === "admin-validate" ? "active" : ""}">
      <span class="nav-icon">✅</span> Valider une demande
    </a>
  `;

  const userName = role === "admin" ? "Admin École" : "Marie Dupont";
  const userInitials = role === "admin" ? "AE" : "MD";

  return `
  <div class="layout">
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo">
        <div class="logo-icon">📅</div>
        <div>
          <h1>RéservApp</h1>
          <span>Gestion du matériel</span>
        </div>
      </div>
      <nav class="sidebar-nav">
        ${role === "teacher" ? teacherLinks : ""}
        ${role === "admin" ? teacherLinks + adminLinks : ""}
      </nav>
      <div class="sidebar-footer">
        <div class="user-avatar">${userInitials}</div>
        <div class="user-info">
          <div class="user-name">${userName}</div>
          <div class="user-role">${role === "admin" ? "Administrateur" : "Enseignant"}</div>
        </div>
      </div>
    </aside>
    <div class="main">
      <header class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
          <button class="btn btn-ghost" onclick="toggleSidebar()" style="display:none;" id="menu-btn">☰</button>
          <span class="topbar-title">${pageTitle}</span>
        </div>
        <div class="topbar-actions">
          <span style="font-size:.82rem;color:var(--gray-400);">Démo — Interface statique</span>
        </div>
      </header>
      <div class="page-content" id="page-content">
  `;
}

function closeLayout() {
  return `
      </div><!-- /page-content -->
    </div><!-- /main -->
  </div><!-- /layout -->
  `;
}
