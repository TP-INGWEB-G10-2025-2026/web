// ─────────────────────────────────────────────
//  EduPlatform — SPA Router (History API)
// ─────────────────────────────────────────────

const API = "http://localhost:8000/api";

// ── Auth helpers ──────────────────────────────

function getToken() {
  return localStorage.getItem("token");
}
function getUser() {
  return JSON.parse(localStorage.getItem("user") || "null");
}
function isAuthenticated() {
  return !!getToken();
}
function isAdmin() {
  return getUser()?.role === "admin";
}
function isTeacher() {
  return getUser()?.role === "teacher";
}

// ── Navigation ────────────────────────────────
//  Use navigateTo() everywhere instead of window.location

function navigateTo(path) {
  console.log("Navigating to:", path);

  window.history.pushState({}, "", path);
  handleRouting();
}

// ── Route definitions ─────────────────────────
//
//  path  — URL pathname (e.g. '/admin/materials/:id')
//  page  — HTML file fetched and injected into #app
//  guard — 'public' | 'auth' | 'admin' | 'teacher'
//

const routes = [
  // ── Public ──────────────────────────────────
  { path: "/login", page: "pages/login.html", guard: "public" },

  // ── Shared (any authenticated user) ──────────
  { path: "/profile", page: "pages/profile.html", guard: "auth" },

  // ── Admin — Materials ─────────────────────────
  { path: "/admin", page: "pages/admin/dashboard.html", guard: "admin" },
  {
    path: "/admin/materials",
    page: "pages/admin/materials/index.html",
    guard: "admin",
  },
  {
    path: "/admin/materials/create",
    page: "pages/admin/materials/create.html",
    guard: "admin",
  },
  {
    path: "/admin/materials/:id/edit",
    page: "pages/admin/materials/edit.html",
    guard: "admin",
  },
  {
    path: "/admin/materials/:id/loans",
    page: "pages/admin/loans/history.html",
    guard: "admin",
  },
  {
    path: "/admin/materials/:id",
    page: "pages/admin/materials/show.html",
    guard: "admin",
  },

  // ── Admin — Categories ────────────────────────
  {
    path: "/admin/categories",
    page: "pages/admin/categories/index.html",
    guard: "admin",
  },

  // ── Admin — Reservations ──────────────────────
  {
    path: "/admin/reservations",
    page: "pages/admin/reservations/index.html",
    guard: "admin",
  },
  {
    path: "/admin/reservations/:id/validate",
    page: "pages/admin/reservations/validate.html",
    guard: "admin",
  },

  // ── Admin — Loans ─────────────────────────────
  {
    path: "/admin/loans",
    page: "pages/admin/loans/index.html",
    guard: "admin",
  },
  {
    path: "/admin/loans/new",
    page: "pages/admin/loans/create.html",
    guard: "admin",
  },
  {
    path: "/admin/loans/history",
    page: "pages/admin/loans/history.html",
    guard: "admin",
  },

  // ── Admin — Teachers ──────────────────────────

  {
    path: "/admin/teachers",
    page: "pages/admin/teachers/index.html",
    guard: "admin",
  },
  {
    path: "/admin/teachers/:id/loans",
    page: "pages/admin/loans/history.html",
    guard: "admin",
  },
  {
    path: "/admin/teachers/:id",
    page: "pages/admin/teachers/show.html",
    guard: "admin",
  },

  // ── Admin — Statistics ────────────────────────
  {
    path: "/admin/statistics",
    page: "pages/admin/statistics/index.html",
    guard: "admin",
  },

  // ── Teacher ───────────────────────────────────
  { path: "/teacher", page: "pages/teacher/dashboard.html", guard: "teacher" },
  {
    path: "/teacher/reservations",
    page: "pages/teacher/reservations/index.html",
    guard: "teacher",
  },
  {
    path: "/teacher/reservations/new",
    page: "pages/teacher/reservations/create.html",
    guard: "teacher",
  },
];

// ── Dynamic segment matcher ───────────────────
//  matchRoute('/admin/materials/:id', '/admin/materials/42')
//  → { matched: true, params: { id: '42' } }

function matchRoute(routePath, currentPath) {
  const rParts = routePath.split("/").filter(Boolean);
  const cParts = currentPath.split("/").filter(Boolean);

  if (rParts.length !== cParts.length) return { matched: false };

  const params = {};

  for (let i = 0; i < rParts.length; i++) {
    if (rParts[i].startsWith(":")) {
      params[rParts[i].slice(1)] = decodeURIComponent(cParts[i]);
    } else if (rParts[i] !== cParts[i]) {
      return { matched: false };
    }
  }

  return { matched: true, params };
}

// ── Guard ─────────────────────────────────────

function applyGuard(guard) {
  // Public route: redirect logged-in users to their dashboard

  console.log(`Checking guard for route with guard: ${guard}`);

  if (guard === "public") {
    if (isAuthenticated()) {
      log("Already authenticated, redirecting to dashboard");
      navigateTo(isAdmin() ? "/admin/materials" : "/teacher/reservations");
      return false;
    }
    return true;
  }

  // All other routes require authentication
  if (!isAuthenticated()) {
    console.log("Not authenticated, redirecting to /login");
    navigateTo("/login");
    return false;
  }

  if (guard === "admin" && !isAdmin()) {
    console.log("User is not an admin, redirecting to teacher dashboard");
    navigateTo("/teacher/reservations");
    return false;
  }

  if (guard === "teacher" && !isTeacher()) {
    console.log("User is not a teacher, redirecting to admin dashboard");
    navigateTo("/admin/materials");
    return false;
  }

  return true;
}

// ── Page loader ───────────────────────────────

async function loadPage(url) {
  const app = document.getElementById("app");
  if (!app) return;

  app.innerHTML = '<div class="page-loading">Chargement…</div>';

  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status} — ${url}`);

    const html = await res.text();
    app.innerHTML = html;

    // Re-execute <script> tags injected with the page HTML
    app.querySelectorAll("script").forEach((old) => {
      const s = document.createElement("script");
      old.src ? (s.src = old.src) : (s.textContent = old.textContent);
      document.body.appendChild(s);
      old.remove();
    });
  } catch (err) {
    app.innerHTML = `
      <div class="page-error">
        <h2>Page introuvable</h2>
        <p>${err.message}</p>
        <button onclick="history.back()">← Retour</button>
      </div>`;
  }
}

// ── Core router ───────────────────────────────

function handleRouting() {
  console.log("Handling routing for path:", window.location.pathname);
  const path = window.location.pathname;

  // Walk routes in order — exact segments win over dynamic ones
  // because specific routes are declared before dynamic ones in the array
  let matched = null;
  let params = {};

  for (const route of routes) {
    const result = matchRoute(route.path, path);
    if (result.matched) {
      matched = route;
      params = result.params;
      break;
    }
  }

  // 404
  if (!matched) {
    document.getElementById("app").innerHTML = `
      <div class="page-error">
        <h2>404 — Page introuvable</h2>
        <a href="/login" onclick="navigateTo('/login'); return false;">
          Retour à l'accueil
        </a>
      </div>`;
    return;
  }

  // Expose params so loaded pages can read them: routeParams.id
  window.routeParams = params;

  if (!applyGuard(matched.guard)) return;
  log(
    `Route matched: ${matched.path} (guard: ${matched.guard}) with params:`,
    params,
  );
  loadPage(matched.page);
}

// ── Intercept all <a> clicks ──────────────────
//  Prevents full page reload for internal links.
//  Use <a href="/admin/materials"> normally in your HTML.

document.addEventListener("click", (e) => {
  const link = e.target.closest("a[href]");
  if (!link) return;

  const href = link.getAttribute("href");

  // Let external links, anchors, and mailto/tel open normally
  if (
    href.startsWith("http") ||
    href.startsWith("//") ||
    href.startsWith("#") ||
    href.startsWith("mailto:") ||
    href.startsWith("tel:")
  )
    return;

  e.preventDefault();
  navigateTo(href);
});

// ── Bootstrap ─────────────────────────────────

// Browser back / forward buttons
window.addEventListener("popstate", handleRouting);

// Initial load
window.addEventListener("DOMContentLoaded", () => {
  // If landing on '/', send to the right dashboard or login
  if (window.location.pathname === "/") {
    const dest = isAuthenticated()
      ? isAdmin()
        ? "/admin/materials"
        : "/teacher/reservations"
      : "/login";
    window.history.replaceState({}, "", dest);
  }
  handleRouting();
});
