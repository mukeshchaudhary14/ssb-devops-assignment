#!/bin/bash
# ==========================================
# SSB Digital - Restore and Verify Script
# ==========================================

set -e

BACKUP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/backups"
LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/*.sql 2>/dev/null | head -n 1)
TEST_DB="ssb_test_db"
DB_USER="ssb_user"
DB_PASS="ssb_pass123"

if [ -z "$LATEST_BACKUP" ]; then
    echo "❌ Error: No SQL backup file found in $BACKUP_DIR"
    exit 1
fi

echo "🔄 Restoring from latest backup: $LATEST_BACKUP"
echo "🎯 Target Test Database: $TEST_DB"

# 1. टेस्ट डेटाबेस बनाएं (अगर नहीं है)
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $TEST_DB; GRANT ALL PRIVILEGES ON $TEST_DB.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;"

# 2. बैकअप फाइल को रिस्टोर करें
mysql -u "$DB_USER" -p"$DB_PASS" "$TEST_DB" < "$LATEST_BACKUP"

echo "✅ Database restored successfully into $TEST_DB!"

# 3. डेटा वेरीफाई करें (गिनती करें कि कितने रिकॉर्ड्स रिस्टोर हुए)
echo "🔍 Verifying restored data in $TEST_DB:"
mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT id, title, status FROM $TEST_DB.tasks;"
