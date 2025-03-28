#!/bin/bash

# Определим путь к проекту
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$SCRIPT_DIR/.."

# Установим переменные
export WEB_GID=33    # Обычно это группа web-сервера (например, www-data на Linux)
export WEB_UID=$(id -u)  # Текущий пользователь, если нужно для доступа

# Массив директорий для восстановления прав
DIRS_TO_FIX=(
    "$PROJECT_DIR/storage"
    "$PROJECT_DIR/bootstrap/cache"
)

# Функция для восстановления прав
perms() {
    sudo chown -R $WEB_UID:$WEB_GID "$1"
    sudo chmod -R g+rwx "$1"
}

# Применяем права ко всем директориям в DIRS_TO_FIX
for DIR in "${DIRS_TO_FIX[@]}"; do
    perms "$DIR"
done

# Дополнительно можно добавить другие директории, если они понадобятся
