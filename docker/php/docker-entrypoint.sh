#!/bin/sh
set -e

if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	mkdir -p var/cache var/log

	if [ "$APP_ENV" = 'local' ]; then
		composer install --prefer-dist --no-progress --no-interaction
	fi

	if [ "$APP_ENV" = 'dev' ]; then
    touch /srv/api/.env
    setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX /srv/api/.env
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX /srv/api/.env
  fi

  if [ "$APP_ENV" = 'rc' ]; then
      touch /srv/api/.env
      setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX /srv/api/.env
      setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX /srv/api/.env
    fi

  mkdir -p storage
  chown -R www-data:"$(whoami)" storage

	if [ "$APP_ENV" != 'local' ]; then
	  setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
  fi

  echo "Waiting for db to be ready..."
  ATTEMPTS_LEFT_TO_REACH_DATABASE=60
  until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || bin/console dbal:run-sql "SELECT 1" > /dev/null 2>&1; do
    sleep 1
    ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE-1))
    echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
    bin/console dbal:run-sql "SELECT 1"
  done

  if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
    echo "The db is not up or not reachable"
    exit 1
  else
     echo "The db is now ready and reachable"
  fi

  if ls -A migrations/*.php > /dev/null 2>&1; then
    bin/console doctrine:migrations:migrate --no-interaction
  fi

  crond -l 8 -b

fi

exec docker-php-entrypoint "$@"
