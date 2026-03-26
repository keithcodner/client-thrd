# Testing Guide

## Directory layout

```
api/tests/
  Feature/Auth/LoginApiTest.php   ← Pest feature tests for login API
  Feature/Auth/AuthenticationTest.php

mobile/test/
  maestro/
    flows/
      login_success.yaml          ← E2E happy path
      login_failure.yaml          ← E2E wrong credentials
    config.yaml                   ← Flow suite definition

scripts/
  test-all.sh                     ← Orchestration (Linux/macOS/WSL)
  test-all.ps1                    ← Orchestration (Windows PowerShell)
```

---

## 1 · Backend — Pest

### Run all tests
```bash
cd api
./vendor/bin/pest
```

### Run only the login suite
```bash
./vendor/bin/pest tests/Feature/Auth/LoginApiTest.php --verbose
```

### What is covered

| Test | Expectation |
|------|-------------|
| Valid credentials | `200` + `{ token, user }` |
| Token authenticates `/api/user` | `200` + correct user id |
| Wrong password | `422` + `message: "Invalid credentials"` |
| Non-existent email | `422` |
| Missing email field | `422` |
| Missing password field | `422` |
| Invalid email format | `422` |
| Authenticated user hits login | non-`200` |

`RefreshDatabase` is applied globally via `Pest.php` to all Feature tests — every test gets a clean SQLite `:memory:` database.

---

## 2 · Frontend — Maestro

### Install Maestro CLI
```bash
curl -Ls "https://get.maestro.mobile.dev" | bash
```

### Run a single flow
```bash
cd mobile/test/maestro
maestro test flows/login_success.yaml \
  --env TEST_EMAIL=test@example.com \
  --env TEST_PASSWORD=password
```

### Run the full suite
```bash
maestro test --env TEST_EMAIL=... --env TEST_PASSWORD=... \
  flows/login_success.yaml flows/login_failure.yaml
```

### How Maestro finds elements

`testID` props were added to three components in `app/(auth)/sign-in.tsx`:

| `testID` | Element |
|----------|---------|
| `login-email-input` | Email / username `TextInput` |
| `login-password-input` | Password `TextInput` |
| `login-submit-button` | Login `TouchableOpacity` |

Maestro targets them with `id:` selectors:
```yaml
- tapOn:
    id: "login-email-input"
```

> **Note:** On Android, `testID` maps directly to `resource-id`. On iOS it maps to `accessibilityIdentifier`. Both work with Maestro out of the box.

---

## 3 · Orchestration

### Linux / macOS / WSL
```bash
# Optional: set credentials as env vars
export TEST_EMAIL=test@example.com
export TEST_PASSWORD=password

bash scripts/test-all.sh
```

### Windows
```powershell
.\scripts\test-all.ps1 -TestEmail "test@example.com" -TestPassword "password"
```

The scripts:
1. Run **Pest** (no server needed — SQLite `:memory:`)
2. Boot `php artisan serve --env=testing` on port `8001` in the background
3. Wait up to 15 s for the server to become ready
4. Run both **Maestro** flows with credentials injected as env vars
5. Kill the server on exit (success or failure)

### Using a different port
```bash
API_PORT=9000 bash scripts/test-all.sh
```
