# Spiritual Matrimony (Spiritual2)

> **Two souls. One path. A lifetime — together.**

A modern, deployment-ready matrimony platform for sincere spiritual seekers. Built in plain PHP 8.2 + MySQL/MariaDB — no framework, no build step, no surprises. Ships with a full admin panel, beautiful spiritual-themed UI, Dockerfile for Coolify, and self-installing database.

![PHP 8.2](https://img.shields.io/badge/PHP-8.2-7B1F1F?style=flat-square) ![MySQL](https://img.shields.io/badge/MariaDB-11-D4A017?style=flat-square) ![Docker](https://img.shields.io/badge/Docker-ready-2D1B4E?style=flat-square)

---

## ✨ Features

### For seekers
- Beautiful, modern UI with spiritual aesthetic (saffron, maroon, indigo)
- Email + password auth, full profile editor with **spiritual details** (path, guru, ishta-devata, sadhana, mantra, scripture)
- Browse & filter by city, religion, diet, spiritual path, age range
- Photo gallery with primary photo
- Send / accept / decline interests · shortlist · one-to-one messaging
- **Profile verification** — submit any government ID + optional live selfie (photo or 5s video captured in-browser); admin reviews and awards a "ID Verified" or "ID + Selfie Verified" badge shown everywhere on the site
- Three pricing tiers — Sadhak (free), Sankalp (premium), Sangam (concierge)

### For admins
- Dashboard with member, interest, and content stats
- Member management (view, block / unblock, delete)
- Blog editor (title, slug, excerpt, body, cover, category, draft / publish)
- Happy stories CMS
- Packages CRUD (price, duration, features, highlight, ordering)
- **Editable site settings** — hero text, taglines, contact info, social, stats — all from the admin
- CMS pages (About, Privacy, Terms, Contact intro) — full HTML editor
- Contact-message inbox
- **Verification queue** — side-by-side ID + selfie vs. profile photos, approve/reject with member-visible reason, auto-email notification, documents purged after decision (data minimisation)

### Tech
- **PHP 8.2 + Apache** (mod_rewrite, gd, pdo_mysql, zip)
- **MariaDB 11** / MySQL 8
- Tiny custom router + PDO wrapper — no Composer / framework dependency
- **Auto-installer**: on first run, schema.sql is loaded automatically and seed passwords are hashed
- CSRF-protected forms · bcrypt passwords · prepared statements everywhere
- Mobile-first responsive layout

---

## 🚀 Quick start (local, with Docker)

```bash
# 1. clone & enter
git clone https://github.com/<your-user>/Spiritual2.git
cd Spiritual2

# 2. start
cp .env.example .env
docker compose up -d --build

# 3. open
open http://localhost:8080
```

The app auto-installs the database schema on the **first request**.

**Default accounts** (please change after first login):

| role   | email                       | password    |
| ------ | --------------------------- | ----------- |
| admin  | `admin@spiritual2.test`     | `admin@123` |
| member | `anjali@example.com`        | `member@123`|
| member | `rohan@example.com`         | `member@123`|

Admin dashboard: <http://localhost:8080/admin>

---

## ☁️ Deploy on Coolify

You have two patterns.

### Pattern A — single Compose deployment (web + db together)
1. In Coolify, create a new resource → **Docker Compose**.
2. Source: the GitHub repo (`Spiritual2`), branch `main`.
3. Coolify reads `docker-compose.yml` from the repo. Done.
4. Set environment variables (override the defaults):
   ```
   APP_URL=https://matrimony.your-domain.com
   APP_ENV=production
   APP_DEBUG=false
   DB_PASSWORD=<strong random>
   DB_ROOT_PASSWORD=<strong random>
   ```
5. Add the domain in Coolify and let it issue a Let's Encrypt cert.

### Pattern B — Coolify-managed MySQL (recommended for production)
1. In Coolify, create a **MariaDB** resource. Note its internal hostname, user, password, db name.
2. Create a new resource → **Dockerfile**. Source: the repo, branch `main`.
3. Set environment variables:
   ```
   APP_URL=https://matrimony.your-domain.com
   APP_ENV=production
   APP_DEBUG=false
   DB_HOST=<coolify mariadb internal host>
   DB_PORT=3306
   DB_DATABASE=<db name>
   DB_USERNAME=<user>
   DB_PASSWORD=<password>
   ```
4. **Add a persistent volume** mapped to `/var/www/html/public/uploads` so user-uploaded photos survive redeploys.
5. Bind a domain → Let's Encrypt.

The schema installs automatically on the first request.

---

## 🛠 Customise

| You want to change…              | Where                                              |
| -------------------------------- | -------------------------------------------------- |
| Hero text, taglines, stats       | Admin → **Site Settings**                          |
| About / Privacy / Terms pages    | Admin → **Pages**                                  |
| Packages & pricing               | Admin → **Packages**                               |
| Blog posts                       | Admin → **Blog**                                   |
| Happy stories on homepage        | Admin → **Happy Stories**                          |
| Colors, fonts, design system     | `public/assets/css/app.css` (CSS variables on `:root`) |
| Database schema                  | `sql/schema.sql`                                   |
| Routes / handlers                | `app/routes.php`, `app/admin_routes.php`           |

---

## 📁 Project structure

```
Spiritual2/
├── public/                   ← Apache DocumentRoot
│   ├── index.php             ← Front controller
│   ├── .htaccess             ← URL rewriting
│   ├── assets/{css,js}
│   └── uploads/              ← User-uploaded photos (mounted volume)
├── app/
│   ├── config.php            ← env → array
│   ├── env.php               ← tiny .env loader
│   ├── DB.php                ← PDO wrapper
│   ├── Auth.php              ← session auth helper
│   ├── Router.php            ← {placeholder} routing
│   ├── helpers.php           ← view(), e(), csrf, flash, etc.
│   ├── routes.php            ← all public + member routes
│   └── admin_routes.php      ← admin-only routes
├── views/
│   ├── layouts/{main,auth,admin}.php
│   ├── partials/{nav,footer}.php
│   ├── home.php
│   ├── auth/, browse/, member/, messages/, profile/, blog/, packages/, admin/, errors/
│   └── page.php, contact.php, happy_stories.php
├── sql/schema.sql            ← Fresh schema + seed data
├── Dockerfile                ← php:8.2-apache w/ gd + pdo_mysql
├── docker-compose.yml        ← Local dev / single-stack Coolify
├── .env.example
└── README.md
```

---

## 🔐 Security notes

- All forms POST a CSRF token (`csrf_field()` / `csrf_check()`).
- Passwords hashed with bcrypt (`PASSWORD_BCRYPT`).
- All queries use prepared statements.
- Session cookies are `HttpOnly` and use `strict_mode`.
- Admin routes guarded by `Auth::requireAdmin()` in every handler.
- Uploads validated by extension and size (4 MB cap).

After deploying, **rotate**:
1. Both seed user passwords (Admin → My Profile → change password).
2. The MariaDB root & app passwords.
3. `APP_KEY` env var.

---

## 🙏 Built with reverence

ॐ शान्ति शान्ति शान्तिः
