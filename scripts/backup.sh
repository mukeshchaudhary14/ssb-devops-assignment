#!/bin/bash
# ========================================================
# SSB Digital - Automated Backup Script with Retention
# ========================================================

set -e

BACKUP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
APP_DIR="/var/www/html/ssb-app"
DB_NAME="ssb_db"
DB_USER="ssb_user"
DB_PASS="ssb_pass123"

# Retention Policy: Number of days to keep backups
RETENTION_DAYS=7

mkdir -p "$BACKUP_DIR"

echo "📦 [1/3] Creating Database Backup..."
DB_BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_backup_${TIMESTAMP}.sql"
mysqldump -u "$DB_USER" -p"$DB_PASS" --no-tablespaces "$DB_NAME" > "$DB_BACKUP_FILE"
echo "✅ Database backup saved: $DB_BACKUP_FILE ($(du -h "$DB_BACKUP_FILE" | cut -f1))"

echo "📁 [2/3] Creating Application Files Backup..."
APP_BACKUP_FILE="$BACKUP_DIR/ssb_app_files_${TIMESTAMP}.tar.gz"
tar -czf "$APP_BACKUP_FILE" -C "$APP_DIR" .
echo "✅ Application files backup saved: $APP_BACKUP_FILE ($(du -h "$APP_BACKUP_FILE" | cut -f1))"

echo "🧹 [3/3] Enforcing Retention Policy (Deleting backups older than $RETENTION_DAYS days)..."
DELETED_FILES=$(find "$BACKUP_DIR" -type f \( -name "*.sql" -o -name "*.tar.gz" \) -mtime +$RETENTION_DAYS -print -delete)

if [ -z "$DELETED_FILES" ]; then
    echo "ℹ️ No obsolete backups found older than $RETENTION_DAYS days."
else
    echo "🗑️ Purged expired backups:"
    echo "$DELETED_FILES"
fi

echo "🎉 Backup workflow completed successfully in: $BACKUP_DIR"
ls -lh "$BACKUP_DIR"
