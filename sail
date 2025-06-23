#!/usr/bin/env bash

UNAMEOUT="$(uname -s)"

# Verify operating system is supported...
case "${UNAMEOUT}" in
    Linux*)             MACHINE=linux;;
    Darwin*)            MACHINE=mac;;
    *)                  MACHINE="UNKNOWN"
esac

if [ "$MACHINE" == "UNKNOWN" ]; then
    echo "Unsupported operating system [$(uname -s)]. Laravel Sail supports macOS, Linux, and Windows (WSL2)." >&2

    exit 1
fi

# Define environment variables...
export APP_PORT=${APP_PORT:-80}
export APP_SERVICE=${APP_SERVICE:-"laravel.test"}
export DB_PORT=${DB_PORT:-3306}
export WWWUSER=${WWWUSER:-$UID}
export WWWGROUP=${WWWGROUP:-$(id -g)}

# Ensure that Docker is running...
if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running." >&2

    exit 1
fi

# Determine if Sail is currently up...
if docker compose ps "$APP_SERVICE" 2>&1 | grep 'Up\|running'; then
    EXEC="yes"
else
    EXEC="no"
fi

# Function that outputs Sail is not running...
function sail_is_not_running {
    echo "Sail is not running."
    echo ""
    echo "You may Sail using the following commands: './sail up' or './sail up -d'"

    exit 1
}

if [ $# -gt 0 ]; then
    # Source the ".env" file so Laravel's environment variables are available...
    if [ -f ./.env ]; then
        source ./.env
    fi

    # Proxy PHP commands to the "php" binary on the application container...
    if [ "$1" == "php" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker compose exec \
                -u sail \
                "$APP_SERVICE" \
                php "$@"
        else
            sail_is_not_running
        fi

    # Proxy Artisan commands to the "artisan" binary on the application container...
    elif [ "$1" == "artisan" ] || [ "$1" == "art" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker compose exec \
                -u sail \
                "$APP_SERVICE" \
                php artisan "$@"
        else
            sail_is_not_running
        fi

    # Proxy Composer commands to the "composer" binary on the application container...
    elif [ "$1" == "composer" ] || [ "$1" == "comp" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker compose exec \
                -u sail \
                "$APP_SERVICE" \
                composer "$@"
        else
            sail_is_not_running
        fi

    # Proxy NPM commands to the "npm" binary on the application container...
    elif [ "$1" == "npm" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker compose exec \
                -u sail \
                "$APP_SERVICE" \
                npm "$@"
        else
            sail_is_not_running
        fi

    # Proxy NPX commands to the "npx" binary on the application container...
    elif [ "$1" == "npx" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker compose exec \
                -u sail \
                "$APP_SERVICE" \
                npx "$@"
        else
            sail_is_not_running
        fi

    # Proxy Yarn commands to the "yarn" binary on the application container...
    elif [ "$1" == "yarn" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker compose exec \
                -u sail \
                "$APP_SERVICE" \
                yarn "$@"
        else
            sail_is_not_running
        fi

    # Initiate a MySQL CLI terminal session within the "mysql" container...
    elif [ "$1" == "mysql" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker compose exec \
                mysql \
                bash -c 'mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$@"'
        else
            sail_is_not_running
        fi

    # Initiate a Bash shell within the application container...
    elif [ "$1" == "shell" ] || [ "$1" == "bash" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker compose exec \
                -u sail \
                "$APP_SERVICE" \
                bash "$@"
        else
            sail_is_not_running
        fi

    # Initiate a root user Bash shell within the application container...
    elif [ "$1" == "root-shell" ] || [ "$1" == "root-bash" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker compose exec \
                "$APP_SERVICE" \
                bash "$@"
        else
            sail_is_not_running
        fi

    # Pass unknown commands to the "docker compose" binary...
    else
        docker compose "$@"
    fi
else
    docker compose ps
fi