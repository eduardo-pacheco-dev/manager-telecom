#!/usr/bin/env bash
#
# Setup do worker de filas (Supervisor) para manager-telecom.
# Idempotente: pode rodar quantas vezes precisar.
# Usage: sudo bash scripts/setup-queue.sh
#
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/manager-telecom}"
PHP_BIN="${PHP_BIN:-php}"
QUEUE_SUPERVISOR_NAME="${QUEUE_SUPERVISOR_NAME:-manager-telecom-queue}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-database}"
QUEUE_WORKERS="${QUEUE_WORKERS:-2}"
QUEUE_SLEEP="${QUEUE_SLEEP:-3}"
QUEUE_TRIES="${QUEUE_TRIES:-3}"
QUEUE_MAX_TIME="${QUEUE_MAX_TIME:-3600}"
QUEUE_USER="${QUEUE_USER:-www-data}"
QUEUE_STOPWAITSEC="${QUEUE_STOPWAITSEC:-3600}"
QUEUE_LOGFILE="${QUEUE_LOGFILE:-$APP_DIR/storage/logs/queue.log}"

SUPERVISOR_CONF_DIR="/etc/supervisor/conf.d"
QUEUE_CONF_FILE="$SUPERVISOR_CONF_DIR/$QUEUE_SUPERVISOR_NAME.conf"

# ── Cores ──────────────────────────────────────────────────────────
if [ -t 1 ]; then
    C_GREEN=$'\e[32m'; C_RED=$'\e[31m'; C_YELLOW=$'\e[33m'
    C_CYAN=$'\e[36m'; C_BOLD=$'\e[1m'; C_RESET=$'\e[0m'
else
    C_GREEN=""; C_RED=""; C_YELLOW=""; C_CYAN=""; C_BOLD=""; C_RESET=""
fi

ok()   { echo "${C_GREEN}✓${C_RESET} $*"; }
warn() { echo "${C_YELLOW}!${C_RESET} $*"; }
fail() { echo "${C_RED}✗${C_RESET} $*"; }

if [ "$(id -u)" -ne 0 ]; then
    fail "Execute com sudo: sudo bash scripts/setup-queue.sh"
    exit 1
fi

# ── 1. Instalar supervisor ─────────────────────────────────────────
if command -v supervisorctl >/dev/null 2>&1 && command -v supervisord >/dev/null 2>&1; then
    ok "Supervisor já instalado"
else
    echo "${C_CYAN}Instalando supervisor...${C_RESET}"
    export DEBIAN_FRONTEND=noninteractive
    apt-get update -qq
    apt-get install -y -qq supervisor
    ok "Supervisor instalado"
fi

# ── 2. Garantir diretórios de log ──────────────────────────────────
install -d -o "$QUEUE_USER" -g "$QUEUE_USER" "$(dirname "$QUEUE_LOGFILE")" "$APP_DIR/storage/logs"
touch "$QUEUE_LOGFILE"
chown "$QUEUE_USER:$QUEUE_USER" "$QUEUE_LOGFILE"
ok "Log de fila pronto: $QUEUE_LOGFILE"

# ── 3. Escrever config do worker ───────────────────────────────────
cat > "$QUEUE_CONF_FILE" <<EOF
[program:$QUEUE_SUPERVISOR_NAME]
process_name=%(program_name)s_%(process_num)02d
command=$PHP_BIN $APP_DIR/artisan queue:work $QUEUE_CONNECTION --sleep=$QUEUE_SLEEP --tries=$QUEUE_TRIES --max-time=$QUEUE_MAX_TIME
directory=$APP_DIR
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=$QUEUE_USER
numprocs=$QUEUE_WORKERS
redirect_stderr=true
stdout_logfile=$QUEUE_LOGFILE
stopwaitsecs=$QUEUE_STOPWAITSEC
EOF
ok "Config escrita em $QUEUE_CONF_FILE"

# ── 4. Recarregar supervisor e iniciar ─────────────────────────────
supervisorctl reread
supervisorctl update
supervisorctl start "$QUEUE_SUPERVISOR_NAME:*" >/dev/null 2>&1 || true
ok "Supervisor recarregado e worker iniciado"

# ── 5. Garantir que o supervisor sobe no boot ──────────────────────
if command -v systemctl >/dev/null 2>&1; then
    systemctl enable supervisor >/dev/null 2>&1 || true
    ok "Supervisor habilitado para iniciar no boot"
else
    warn "systemctl não encontrado - verifique se o supervisor inicia no boot"
fi

# ── 6. Watchdog: recria a fila automaticamente se cair ─────────────
WATCHDOG_LABEL="manager-telecom-queue-watchdog"
WATCHDOG_SCRIPT="/usr/local/bin/manager-telecom-queue-watchdog.sh"
WATCHDOG_CRON="/etc/cron.d/$WATCHDOG_LABEL"

cat > "$WATCHDOG_SCRIPT" <<'EOF'
#!/usr/bin/env bash
# Reinicia o worker de filas do manager-telecom se ele não estiver rodando.
set -euo pipefail
APP_DIR="${APP_DIR:-/var/www/manager-telecom}"
QUEUE_SUPERVISOR_NAME="${QUEUE_SUPERVISOR_NAME:-manager-telecom-queue}"
if ! supervisorctl status "$QUEUE_SUPERVISOR_NAME:*" 2>/dev/null | grep -q RUNNING; then
    supervisorctl start "$QUEUE_SUPERVISOR_NAME:*" >/dev/null 2>&1 || \
        bash "$APP_DIR/scripts/setup-queue.sh" >/dev/null 2>&1 || true
fi
EOF
chmod +x "$WATCHDOG_SCRIPT"
chown root:root "$WATCHDOG_SCRIPT"

cat > "$WATCHDOG_CRON" <<EOF
SHELL=/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
* * * * * root $WATCHDOG_SCRIPT
EOF
chmod 644 "$WATCHDOG_CRON"
ok "Watchdog configurado (verifica a fila a cada minuto)"

# ── 7. Verificar status ────────────────────────────────────────────
sleep 1
STATUS=$(supervisorctl status "$QUEUE_SUPERVISOR_NAME:*" 2>/dev/null || true)
echo "$STATUS"
if echo "$STATUS" | grep -q "RUNNING"; then
    ok "Worker(s) rodando"
else
    warn "Status inesperado. Veja o log: $QUEUE_LOGFILE"
    exit 1
fi