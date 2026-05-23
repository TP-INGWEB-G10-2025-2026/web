// ── Mock Data ──────────────────────────────────────────────
const MOCK_MATERIALS = [
  { id: 1, name: "Projecteur Epson EB-X51", category: "Audiovisuel", icon: "📽️" },
  { id: 2, name: "Tablette iPad Pro 12.9", category: "Informatique", icon: "📱" },
  { id: 3, name: "Caméra Sony Alpha A6400", category: "Photographie", icon: "📷" },
  { id: 4, name: "Micro HF Sennheiser", category: "Sonorisation", icon: "🎙️" },
  { id: 5, name: "Écran interactif 75\"", category: "Audiovisuel", icon: "🖥️" },
  { id: 6, name: "Drone DJI Mini 3", category: "Photographie", icon: "🚁" },
];

const MOCK_USERS = [
  { id: 1, name: "Marie Dupont", email: "m.dupont@ecole.fr", role: "teacher", blocked: false },
  { id: 2, name: "Jean Martin", email: "j.martin@ecole.fr", role: "teacher", blocked: false },
  { id: 3, name: "Sophie Bernard", email: "s.bernard@ecole.fr", role: "teacher", blocked: true },
  { id: 4, name: "Admin École", email: "admin@ecole.fr", role: "admin", blocked: false },
];

let MOCK_RESERVATIONS = [
  { id: 1, user: MOCK_USERS[0], material: MOCK_MATERIALS[0], start_date: "2026-06-01", end_date: "2026-06-03", status: "pending", rejection_reason: null, created_at: "2026-05-20" },
  { id: 2, user: MOCK_USERS[1], material: MOCK_MATERIALS[2], start_date: "2026-05-28", end_date: "2026-05-30", status: "validated", rejection_reason: null, created_at: "2026-05-18" },
  { id: 3, user: MOCK_USERS[0], material: null, start_date: "2026-06-10", end_date: "2026-06-12", status: "rejected", rejection_reason: "Matériel déjà réservé pour une activité externe.", created_at: "2026-05-15" },
  { id: 4, user: MOCK_USERS[1], material: null, start_date: "2026-06-15", end_date: "2026-06-17", status: "pending", rejection_reason: null, created_at: "2026-05-22" },
  { id: 5, user: MOCK_USERS[0], material: MOCK_MATERIALS[4], start_date: "2026-05-10", end_date: "2026-05-12", status: "validated", rejection_reason: null, created_at: "2026-05-05" },
];

// ── Mock API ────────────────────────────────────────────────
const API = {
  delay: (ms = 600) => new Promise(r => setTimeout(r, ms)),

  async getReservations(filters = {}) {
    await this.delay();
    let list = [...MOCK_RESERVATIONS];
    if (filters.status && filters.status !== 'all') list = list.filter(r => r.status === filters.status);
    if (filters.userId) list = list.filter(r => r.user.id === filters.userId);
    return list.reverse();
  },

  async getReservation(id) {
    await this.delay(300);
    return MOCK_RESERVATIONS.find(r => r.id === id) || null;
  },

  async getAvailable(startDate, endDate) {
    await this.delay();
    // Simulate some conflicts
    const busy = [1, 3];
    return MOCK_MATERIALS.filter(m => !busy.includes(m.id));
  },

  async createReservation(data) {
    await this.delay(800);
    const newRes = {
      id: MOCK_RESERVATIONS.length + 1,
      user: MOCK_USERS[0],
      material: null,
      start_date: data.start_date,
      end_date: data.end_date,
      status: "pending",
      rejection_reason: null,
      created_at: new Date().toISOString().slice(0, 10),
    };
    MOCK_RESERVATIONS.push(newRes);
    return newRes;
  },

  async validateReservation(id, materialId) {
    await this.delay(700);
    const r = MOCK_RESERVATIONS.find(r => r.id === id);
    if (r) {
      r.status = "validated";
      r.material = MOCK_MATERIALS.find(m => m.id === materialId) || null;
    }
    return r;
  },

  async rejectReservation(id, reason) {
    await this.delay(700);
    const r = MOCK_RESERVATIONS.find(r => r.id === id);
    if (r) { r.status = "rejected"; r.rejection_reason = reason || null; }
    return r;
  },
};

// ── Helpers ─────────────────────────────────────────────────
function formatDate(d) {
  if (!d) return "—";
  return new Date(d).toLocaleDateString("fr-FR", { day: "2-digit", month: "short", year: "numeric" });
}

function statusBadge(status) {
  const map = {
    pending:   { label: "En attente",  cls: "badge-pending" },
    validated: { label: "Validée",     cls: "badge-validated" },
    rejected:  { label: "Rejetée",     cls: "badge-rejected" },
    cancelled: { label: "Annulée",     cls: "badge-cancelled" },
  };
  const s = map[status] || { label: status, cls: "badge-pending" };
  return `<span class="badge ${s.cls}">${s.label}</span>`;
}

function statusIcon(status) {
  return { pending: "🕐", validated: "✅", rejected: "❌", cancelled: "🚫" }[status] || "•";
}

function showToast(msg, type = "success") {
  const t = document.createElement("div");
  t.className = `alert alert-${type}`;
  t.style.cssText = "position:fixed;top:20px;right:20px;z-index:9999;min-width:280px;max-width:380px;box-shadow:0 8px 24px rgba(0,0,0,.15);animation:fadeInUp .3s ease";
  t.innerHTML = `<span class="alert-icon">${type === "success" ? "✅" : type === "error" ? "❌" : "ℹ️"}</span> ${msg}`;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3500);
}

function openModal(id) { document.getElementById(id)?.classList.add("open"); }
function closeModal(id) { document.getElementById(id)?.classList.remove("open"); }

// Close modal on backdrop click
document.addEventListener("click", e => {
  if (e.target.classList.contains("modal-backdrop")) {
    e.target.classList.remove("open");
  }
});

// ── Sidebar mobile toggle ───────────────────────────────────
function toggleSidebar() {
  document.querySelector(".sidebar")?.classList.toggle("open");
}

// ── Active nav link ─────────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const links = document.querySelectorAll(".nav-link");
  links.forEach(l => {
    if (l.href === location.href) l.classList.add("active");
  });
});
