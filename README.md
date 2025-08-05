# Admin Dashboard - Laravel + Jetstream (Vue + Tailwind + Inertia.js)

A responsive admin dashboard built using Laravel with Jetstream, Vue 3, Inertia.js, and Tailwind CSS. Includes dark/light mode toggle, sidebar navigation, user dropdown menu, and role/permission management using Spatie Laravel Permission.

## 🛠️ Features

- ✅ Laravel 10 + Jetstream (Inertia + Vue 3 + Tailwind)
- ✅ Spatie Laravel Permission for Role & Permission management
- ✅ Responsive layout (Sidebar 20% / Content 80%)
- ✅ Sidebar with grouped menu and icons
- ✅ Header with User Dropdown (Profile + Logout)
- ✅ Dark/Light mode toggle
- ✅ Layout via `MainLayout.vue`

---

## 📁 Project Structure
```
resources/
├── js/
│ ├── Components/
│ │ ├── Sidebar.vue
│ │ ├── UserDropdown.vue
│ ├── Layouts/
│ │ └── MainLayout.vue
│ ├── Pages/
│ │ ├── Dashboard.vue
│ │ ├── Profile.vue
│ │ └── Items.vue
│ └── app.js
```

---

## ⚙️ Setup Instructions

### 1. Clone the repository
```
git clone https://github.com/haniscreator/laravel-admin.git
cd laravel-admin
```

### 2. Install dependencies
```
composer install
npm install && npm run dev
```

### 3. Set up environment
```
composer install
npm install && npm run dev
```

### 4. Run migrations
```
php artisan migrate
```

### 5. (Optional) Seed roles/permissions
You can use Spatie's seeder structure or create your own.

### 6. Serve the app
```
php artisan serve
```

---

## ✨ Credits
- Laravel
- Jetstream
- Inertia.js
- Vue 3
- Tailwind CSS
- Spatie Laravel Permission

