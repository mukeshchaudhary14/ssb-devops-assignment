# Incident Report: Database Connection Failure

- **Incident Date & Time:** $(date)
- **Affected Service:** SSB Internal Operations Portal (`/ssb-app`)
- **Severity:** High (Application Outage)

### 1. Symptom
Users attempting to access `http://localhost/ssb-app/index.php` received a 500 fatal database error:
`Database Connection Failed: SQLSTATE[HY000] [2002] Connection refused`.

### 2. Investigation & Log Evidence
- Checked Apache error logs: `/var/log/apache2/error.log` indicated connection socket failures.
- Executed service check: `sudo systemctl status mysql`
  - Evidence: Output showed `Active: inactive (dead)`.
- Checked port availability: `ss -tulpn | grep 3306` returned empty (MySQL daemon was not listening).

### 3. Root Cause
The `mysql.service` daemon had unexpectedly stopped, preventing the PHP backend (PDO) from establishing a connection on port 3306.

### 4. Resolution
- Started the MySQL service using: `sudo systemctl start mysql`
- Verified service state: `sudo systemctl status mysql` (returned `active (running)`).
- Tested application endpoint: Refreshed `http://localhost/ssb-app/index.php` and verified that records loaded successfully.

### 5. Prevention & Action Items
- Enable systemd auto-restart on failure by configuring `Restart=on-failure` in `/lib/systemd/system/mysql.service`.
- Implement a scheduled health-check script/cron job to alert when MySQL port 3306 does not respond.
