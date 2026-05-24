#  GestMat — Application de Gestion du Matériel Pédagogique
### Département de Mathématiques et Informatique — Université de Ngaoundéré

---

## 🎯 Description du projet

**GestMat** est une application web développée pour faciliter la **gestion et la réservation du matériel pédagogique** utilisé par le Département de Mathématiques et Informatique de l'Université de Ngaoundéré.

Elle permet aux enseignants et au personnel administratif de consulter, réserver et gérer le matériel nécessaire pour les cours, les travaux pratiques et les projets de recherche (projecteurs, capteurs, marqueurs, matériels de TP, etc.).

---

## ✨ Fonctionnalités principales

| Fonctionnalité | Description |
|---|---|
| 📦 **Catalogue de matériel** | Consultation en ligne du matériel disponible par catégorie et statut |
| 📅 **Réservation** | Les enseignants soumettent des demandes avec vérification de disponibilité en temps réel |
| ✅ **Validation des réservations** | Les administrateurs valident ou rejettent les demandes avec assignation du matériel |
| 🗄️ **Gestion des stocks** | Ajout, modification, suppression (soft delete) et changement d'état des matériels |
| 🔔 **Notifications** | Email (SendGrid) + SMS (Twilio) à chaque étape du cycle de vie d'une réservation |
| 📊 **Suivi des utilisations** | Enregistrement des prêts et retours, historique complet, statistiques d'utilisation |

---

## 🏗️ Architecture technique

### Stack

| Couche | Technologie |
|---|---|
| Backend | Laravel 13 · PHP 8.4 |
| Authentification | Laravel Sanctum (tokens API) |
| Base de données | MySQL (production) · SQLite (tests) |
| Email | SendGrid via SMTP |
| SMS | Twilio |
| File d'attente | Laravel Queue (database driver) |
| Frontend | HTML · CSS · JavaScript (vanilla) |
| Tests | PHPUnit · Laravel Feature Tests |

---

## 📁 Structure du projet

```
app/
├── Enums/              # MaterialStatus, ReservationStatus, ReturnStatus, Role
├── Events/             # ReservationSubmitted, ReservationValidated, ReservationRejected
├── Http/
│   ├── Controllers/    # AuthController, TeacherController, MaterialController...
│   ├── Middleware/     # Authenticate, IsAdmin, IsTeacher
│   ├── Requests/       # Form Requests (validation)
│   └── Resources/      # API Resources (formatage JSON)
├── Listeners/          # SendAdminNotification, SendReservationValidatedNotification...
├── Mail/               # WelcomeTeacherMail, NewReservationMail, DamagedMaterialMail...
├── Models/             # User, Material, Category, Reservation, Loan
├── Services/           # AuthService, MaterialService, ReservationService, StatisticsService...
└── Providers/          # AppServiceProvider (événements)

database/
├── migrations/         # Toutes les migrations versionnées
├── factories/          # UserFactory, MaterialFactory, LoanFactory...
└── seeders/            # AdminSeeder, TeacherSeeder, CategorySeeder...

routes/
├── api.php             # Chargement des routes v1
└── api.v1.php          # Toutes les routes de l'API

tests/
└── Feature/            # AuthTest, TeacherTest, MaterialTest, ReservationAdminTest...
```

---

## 🔐 Rôles & permissions

| Rôle | Accès |
|---|---|
| `admin` | Toutes les fonctionnalités (matériels, catégories, enseignants, réservations, prêts, statistiques) |
| `teacher` | Profil personnel, soumettre/consulter ses réservations |

---

## 🌐 Endpoints API principaux

### Authentification
```
POST   /api/v1/auth/login
POST   /api/v1/auth/logout       [auth]
GET    /api/v1/auth/me           [auth]
```

### Profil
```
GET    /api/v1/profile           [auth]
PUT    /api/v1/profile           [auth]
```

### Enseignants
```
GET    /api/v1/teachers          [admin]
POST   /api/v1/teachers          [admin]
GET    /api/v1/teachers/{id}     [admin]
PUT    /api/v1/teachers/{id}     [admin]
DELETE /api/v1/teachers/{id}     [admin]
PATCH  /api/v1/teachers/{id}/block    [admin]
PATCH  /api/v1/teachers/{id}/unblock  [admin]
```

### Matériels
```
GET    /api/v1/materials              [admin]
POST   /api/v1/materials              [admin]
GET    /api/v1/materials/{id}         [admin]
PUT    /api/v1/materials/{id}         [admin]
DELETE /api/v1/materials/{id}         [admin]
PATCH  /api/v1/materials/{id}/status  [admin]
```

### Catégories
```
GET    /api/v1/categories         [admin]
POST   /api/v1/categories         [admin]
GET    /api/v1/categories/{id}    [admin]
PUT    /api/v1/categories/{id}    [admin]
DELETE /api/v1/categories/{id}    [admin]
```

### Réservations
```
POST   /api/v1/reservations                    [auth]
GET    /api/v1/reservations/available          [auth]
GET    /api/v1/reservations                    [admin]
GET    /api/v1/reservations/{id}               [admin]
PATCH  /api/v1/reservations/{id}/validate      [admin]
PATCH  /api/v1/reservations/{id}/reject        [admin]
```

### Prêts
```
GET    /api/v1/loans                     [admin]
POST   /api/v1/loans                     [admin]
GET    /api/v1/loans/{id}                [admin]
PATCH  /api/v1/loans/{id}/return         [admin]
GET    /api/v1/materials/{id}/loans      [admin]
GET    /api/v1/teachers/{id}/loans       [admin]
```

### Statistiques
```
GET    /api/v1/statistics/usage          [admin]
GET    /api/v1/statistics/summary        [admin]
GET    /api/v1/statistics/materials/top  [admin]
GET    /api/v1/statistics/loans/monthly  [admin]
GET    /api/v1/statistics/overdue        [admin]
```

---

## ⚙️ Installation

### Prérequis
- PHP >= 8.4
- Composer
- MySQL >= 8.0
- Compte SendGrid (email)
- Compte Twilio (SMS)

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/votre-repo/gestmat.git
cd gestmat

# 2. Installer les dépendances PHP
composer install

# 3. Copier et configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Configurer la base de données dans .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestmat
DB_USERNAME=root
DB_PASSWORD=

# 5. Configurer les services de notification dans .env
SENDGRID_API_KEY=your_sendgrid_api_key
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_auth_token
TWILIO_FROM=+1234567890
ADMIN_EMAIL=admin@gestmat.com
ADMIN_PHONE=+237600000000

# 6. Exécuter les migrations et seeders
php artisan migrate --seed

# 7. Créer le lien symbolique pour le stockage
php artisan storage:link

# 8. Démarrer le serveur de développement
php artisan serve

# 9. Démarrer le worker de file d'attente (dans un autre terminal)
php artisan queue:work
```

---

## 🧪 Tests

```bash
# Lancer tous les tests
php artisan test

# Lancer un fichier de tests spécifique
php artisan test tests/Feature/AuthTest.php --verbose


```

### Suites de tests disponibles

| Fichier | Description |
|---|---|
| `AuthTest` | Connexion, déconnexion, me |
| `TeacherTest` | CRUD enseignants, blocage |
| `ProfileTest` | Consultation et mise à jour du profil |
| `CategoryTest` | CRUD catégories |
| `MaterialTest` | CRUD matériels, changement d'état |
| `ReservationSubmissionTest` | Soumission et vérification de disponibilité |
| `ReservationAdminTest` | Validation et rejet par l'admin |
| `ReservationNotificationTest` | Envoi email et SMS |
| `LoanTest` | Enregistrement prêts et retours |
| `LoanHistoryTest` | Historique, immuabilité |
| `StatisticsTest` | Dashboard statistiques |

---

## 🔑 Compte administrateur par défaut

```
Email    : admin@admin.com
Mot de passe : Admin@12345
```

> ⚠️ Changez ces identifiants en production.

---

## 📱 Frontend

Le frontend est développé en **HTML / CSS / JavaScript vanilla** (sans framework), organisé en pages :

| Page | Route | Rôle |
|---|---|---|
| Connexion | `/login.html` | Public |
| Mon profil | `/profile.html` | Auth |
| Matériels | `/materials.html` | Admin |
| Nouveau matériel | `/material-form.html` | Admin |
| Détail matériel | `/material-detail.html` | Admin |
| Catégories | `/categories.html` | Admin |
| Enseignants | `/teachers.html` | Admin |
| Réservations (admin) | `/admin-reservations.html` | Admin |
| Nouvelle réservation | `/new-reservation.html` | Enseignant |
| Mes réservations | `/my-reservations.html` | Enseignant |
| Prêts | `/loans.html` | Admin |
| Nouveau prêt | `/loan-new.html` | Admin |
| Historique | `/loan-history.html` | Admin |
| Statistiques | `/statistics.html` | Admin |

---

## 🔄 Cycle de vie d'une réservation

```
[Enseignant]  ──► Soumet une demande (pending)
                        │
                        ▼
[Admin]       ──► Valide avec assignation matériel (validated)
              ──► ou Rejette avec raison optionnelle (rejected)
                        │
                        ▼
[Admin]       ──► Enregistre le prêt
                        │
                        ▼
[Admin]       ──► Enregistre le retour (good / damaged / lost)
                        │
                        ▼
              Matériel → available / broken selon état de retour
```

---

## 📧 Notifications envoyées

| Événement | Destinataire | Canal |
|---|---|---|
| Nouvelle demande de réservation | Administrateur | Email + SMS |
| Réservation validée | Enseignant | Email + SMS |
| Réservation rejetée | Enseignant | Email + SMS |
| Nouveau compte enseignant | Enseignant | Email + SMS |
| Matériel endommagé/perdu | Administrateur | Email |
| Compte bloqué/débloqué | Enseignant | SMS |

---

## 📄 Licence

Projet académique — Université de Ngaoundéré, Département de Mathématiques et Informatique.

---

## 👨‍💻 Développement

> Projet réalisé dans le cadre du cours de développement d'applications web.
> Toute contribution doit suivre les conventions de nommage Laravel et les standards PSR-12.