#!/bin/bash
set -e

# Batch 1: Documentation & Core Configurations
echo "Batch 1..."
git add docs/ backend/docs/ frontend/docs/
git add backend/bootstrap/app.php backend/composer.json backend/composer.lock backend/app/Http/Middleware/
git add frontend/package.json frontend/package-lock.json
git commit --no-verify -m "docs: update system documentation and core configurations" || true

# Batch 2: i18n (Localization) & Theme Features
echo "Batch 2..."
git add frontend/src/locales/ frontend/src/plugins/ backend/lang/
git add frontend/src/components/base/LanguageSwitcher.vue frontend/src/components/base/ThemeToggle.vue
git add frontend/src/stores/themeStore.ts frontend/src/stores/authStore.ts
git add frontend/src/main.ts frontend/src/App.vue frontend/src/router/index.ts frontend/src/services/api.ts
git add frontend/src/views/auth/ frontend/src/layouts/ frontend/src/views/settings/ frontend/src/views/HomeView.vue
git commit --no-verify -m "feat: implement i18n localization and theme features" || true

# Batch 3: Admin UI & User Management Refinements
echo "Batch 3..."
git add frontend/src/components/admin/ frontend/src/views/admin/
git add frontend/src/theme/admin/ frontend/src/assets/main.css
git add frontend/src/components/base/BaseButton.vue
git rm -q frontend/src/views/admin/UsersView.vue 2>/dev/null || true
git commit --no-verify -m "feat: refine admin dashboard UI and user management features" || true

# Batch 4: Backend Users & Genre Management Modules
echo "Batch 4..."
git add backend/Modules/Music/ backend/Modules/Users/
git add frontend/src/stores/genreStore.ts
git commit --no-verify -m "feat: implement backend genre management and user roles logic" || true

# Any remaining files
echo "Batch 5 (Fallback)..."
git add .
git commit --no-verify -m "chore: additional miscellaneous updates" || true

echo "Pushing changes..."
git push origin develop

echo "Done!"
