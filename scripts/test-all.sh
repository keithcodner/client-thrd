#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# scripts/test-all.sh
#
# Orchestrates:
#   1. Laravel Pest unit + feature tests (in-memory SQLite, no server needed)
#   2. Laravel API server for Maestro E2E tests
#   3. Maestro login flows
#
# Usage:
#   bash scripts/test-all.sh
#
# Prerequisites:
#   - PHP + Composer installed
#   - Maestro CLI installed  (curl -Ls "https://get.maestro.mobile.dev" | bash)
#   - A running Android emulator OR connected device (for Maestro)
#   - Environment variables TEST_EMAIL and TEST_PASSWORD set, or .env.testing present
# ---------------------------------------------------------------------------

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
API_DIR="$ROOT_DIR/api"
MOBILE_DIR="$ROOT_DIR/mobile"
MAESTRO_DIR="$MOBILE_DIR/test/maestro"

# ---- colours ---------------------------------------------------------------
GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RED='\033[0;31m'; NC='\033[0m'
info()    { echo -e "${GREEN}[INFO]${NC}  $*"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}  $*"; }
fail()    { echo -e "${RED}[FAIL]${NC}  $*"; exit 1; }

# ---- defaults --------------------------------------------------------------
TEST_EMAIL="${TEST_EMAIL:-test@example.com}"
TEST_PASSWORD="${TEST_PASSWORD:-password}"
API_PORT="${API_PORT:-8001}"
API_PID=""

cleanup() {
    if [[ -n "$API_PID" ]]; then
        info "Stopping Laravel test server (PID $API_PID)…"
        kill "$API_PID" 2>/dev/null || true
    fi
}
trap cleanup EXIT

# ===========================================================================
# STEP 1 — Pest (unit + feature, no server required — uses SQLite :memory:)
# ===========================================================================
info "Running Pest tests…"
cd "$API_DIR"
php artisan config:clear --env=testing 2>/dev/null || true
./vendor/bin/pest --colors=always || fail "Pest tests failed."
info "Pest tests passed."

# ===========================================================================
# STEP 2 — Boot Laravel on a dedicated testing port for Maestro
# ===========================================================================
info "Starting Laravel test server on port $API_PORT…"
php artisan serve \
    --host=127.0.0.1 \
    --port="$API_PORT" \
    --env=testing \
    > /tmp/laravel-test-server.log 2>&1 &
API_PID=$!

# Wait for the server to accept connections (up to 15 s)
MAX_WAIT=15
for i in $(seq 1 $MAX_WAIT); do
    if curl -s "http://127.0.0.1:$API_PORT/api/user" > /dev/null 2>&1; then
        break
    fi
    if [[ $i -eq $MAX_WAIT ]]; then
        fail "Laravel test server did not start in ${MAX_WAIT}s. Check /tmp/laravel-test-server.log"
    fi
    sleep 1
done
info "Laravel test server is up."

# ===========================================================================
# STEP 3 — Maestro E2E flows
# ===========================================================================
info "Running Maestro flows…"
cd "$MAESTRO_DIR"

maestro test \
    --env TEST_EMAIL="$TEST_EMAIL" \
    --env TEST_PASSWORD="$TEST_PASSWORD" \
    flows/login_success.yaml \
    flows/login_failure.yaml \
    || fail "Maestro tests failed."

info "All tests passed. ✓"
