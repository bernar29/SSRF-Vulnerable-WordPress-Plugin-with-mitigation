#!/usr/bin/env bash
set -e
cd public
python -m http.server 8000
chmod +x static-assets/entrypoint.sh
