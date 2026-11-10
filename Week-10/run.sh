#!/usr/bin/env bash
# Menjalankan ShopEase (port 8090) dan MariaDB dengan Docker.
#   ./run.sh up      build image, jalankan container, composer install, migrate + seed
#   ./run.sh start   jalankan lagi container yang sudah ada
#   ./run.sh stop    hentikan container
#   ./run.sh reset   hapus container, database, dan file upload, lalu up dari awal
set -euo pipefail
cd "$(dirname "$0")"

NET=pbkk10-net
IMG=pbkk-ci4-php
DB=pbkk10-db
APP=pbkk10-app
PORT=8090
# Apache dan spark dijalankan dengan UID host, karena chmod tidak berlaku di partisi NTFS
UIDGID="$(id -u):$(id -g)"

ada() { docker container inspect "$1" >/dev/null 2>&1; }

wait_db() {
  echo "Menunggu MariaDB siap..."
  until docker exec $DB healthcheck.sh --connect --innodb_initialized >/dev/null 2>&1; do sleep 2; done
}

up() {
  docker network inspect $NET >/dev/null 2>&1 || docker network create $NET >/dev/null
  docker build -q -t $IMG docker >/dev/null

  local baru=0
  if ! ada $DB; then
    docker run -d --name $DB --network $NET \
      -e MARIADB_ROOT_PASSWORD=root -e TZ=Asia/Jakarta \
      -v "$PWD/docker/init.sql":/docker-entrypoint-initdb.d/init.sql:ro \
      -v pbkk10-db-data:/var/lib/mysql -p 127.0.0.1:3310:3306 mariadb:11 >/dev/null
    baru=1
  fi
  docker start $DB >/dev/null
  wait_db

  ada $APP || docker run -d --name $APP --network $NET -e TZ=Asia/Jakarta \
    -e APACHE_RUN_USER="#$(id -u)" -e APACHE_RUN_GROUP="#$(id -g)" \
    -v "$PWD/shopease":/var/www/html -p "127.0.0.1:$PORT:80" $IMG >/dev/null
  docker start $APP >/dev/null
  docker exec -u "$UIDGID" -e COMPOSER_HOME=/tmp $APP bash -c \
    'cd /var/www/html && { [ -d vendor ] || composer install --no-interaction --quiet; } && { [ -f .env ] || cp env.docker .env; }
     mkdir -p public/uploads/produk writable/uploads/bukti'
  docker exec -u "$UIDGID" $APP php spark migrate --all
  if [ $baru = 1 ]; then docker exec -u "$UIDGID" $APP php spark db:seed ShopEaseSeeder; fi

  echo
  echo "ShopEase -> http://localhost:$PORT"
}

case "${1:-up}" in
  up)    up ;;
  start) docker start $DB >/dev/null; wait_db
         docker start $APP >/dev/null
         echo "ShopEase -> http://localhost:$PORT" ;;
  stop)  docker stop $APP $DB >/dev/null ;;
  reset) ada $APP && docker exec $APP find public/uploads writable/uploads -type f ! -name .gitignore ! -name index.html -delete
         docker rm -f $APP $DB >/dev/null 2>&1 || true
         docker volume rm pbkk10-db-data >/dev/null 2>&1 || true
         up ;;
  *) echo "Pemakaian: $0 {up|start|stop|reset}"; exit 1 ;;
esac
