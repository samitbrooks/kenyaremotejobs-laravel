#!/usr/bin/env bash
set -e

SSH_HOST="94.72.107.251"
SSH_PORT="7202"
SSH_USER="kenyarem"
REMOTE_REPO="/home/kenyarem/repositories/kenyaremotejobs-laravel"
REMOTE_PUBLIC_HTML="/home/kenyarem/public_html"
SSH_KEY="$HOME/.ssh/id_rsa"

echo "=================================================="
echo " Starting automated deployment to KenyaRemoteJobs "
echo "=================================================="

echo ""
echo "=== Step 1: Building frontend assets locally ==="
npm run build

echo ""
echo "=== Step 2: Committing and pushing to GitHub ==="
if [ -n "$(git status --porcelain)" ]; then
    git add .
    git commit -m "Deploy: update frontend assets and codebase" || true
fi

echo "Pushing commits to origin/main..."
git push origin main

echo ""
echo "=== Step 3: Deploying on cPanel server via SSH ==="
ssh -o StrictHostKeyChecking=no -p "$SSH_PORT" -i "$SSH_KEY" "$SSH_USER@$SSH_HOST" bash << 'EOF'
set -e

cd /home/kenyarem/repositories/kenyaremotejobs-laravel

echo "-> Pulling latest code from GitHub..."
git pull origin main

echo "-> Syncing public build assets to public_html..."
cp -r public/build /home/kenyarem/public_html/

echo "-> Running database migrations..."
php artisan migrate --force

echo "-> Optimizing application cache..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "-> Running remote job sync..."
php artisan jobs:sync

echo ""
echo "=================================================="
echo " Deployment & Remote Job Sync Complete! "
echo "=================================================="
EOF
