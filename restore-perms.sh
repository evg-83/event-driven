#!/bin/bash
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

#. $SCRIPT_DIR/settings.sh || {
#  echo "no settings" && exit 1
#}

export WEB_GID=33  # Группа веб-сервера (обычно 33 = www-data в Debian/Ubuntu)

perms() {
  sudo chown -R $UID:$WEB_GID "$1"
  sudo chmod -R g+rwx "$1"
}

# Папки, которым нужны права
perms "$SCRIPT_DIR/../storage"
perms "$SCRIPT_DIR/../bootstrap/cache"
perms "$SCRIPT_DIR/../public"

# Проверка и создание лог-файла
LOG_FILE="$SCRIPT_DIR/../storage/logs/laravel.log"
if [ ! -f "$LOG_FILE" ]; then
  touch "$LOG_FILE"
  perms "$LOG_FILE"
fi
