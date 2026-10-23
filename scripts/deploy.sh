#!/bin/bash
set -e

SOURCE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../app" && pwd)/"
TARGET_DIR="/var/www/html/ssb-app/"

echo "🚀 Starting Deployment Process..."
echo "Source: $SOURCE_DIR"
echo "Target: $TARGET_DIR"

if [ ! -d "$SOURCE_DIR" ]; then
    echo "❌ Error: Source directory does not exist: $SOURCE_DIR"
    exit 1
fi

if [ ! -d "$TARGET_DIR" ]; then
    sudo mkdir -p "$TARGET_DIR"
fi

DRY_RUN=""
if [ "$1" == "--dry-run" ]; then
    DRY_RUN="--dry-run"
    echo "🔍 Running in DRY-RUN mode..."
fi

# Rsync files (Exclude Git files and logs)
rsync -avz $DRY_RUN \
    --delete \
    --exclude='.git*' \
    --exclude='*.log' \
    --exclude='*.sql' \
    "$SOURCE_DIR" "$TARGET_DIR"

# Permissions setup
if [ -z "$DRY_RUN" ]; then
    echo "🔒 Applying secure file permissions..."
    sudo chown -R www-data:www-data "$TARGET_DIR"
    sudo find "$TARGET_DIR" -type d -exec chmod 755 {} \;
    sudo find "$TARGET_DIR" -type f -exec chmod 644 {} \;
    echo "✅ Deployment completed successfully!"
fi
