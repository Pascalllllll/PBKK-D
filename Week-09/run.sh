#!/usr/bin/env bash
# Menjalankan SIAKAD (port 8080), Toko Online (port 8081), dan MariaDB dengan Docker.
#   ./run.sh up      build image, jalankan container, composer install, migrate + seed
#   ./run.sh start   jalankan lagi container yang sudah ada
#   ./run.sh stop    hentikan container
#   ./run.sh reset   hapus container, database, dan file upload, lalu up dari awal
set -euo pipefail
cd "$(dirname "$0")"

NET=pbkk-ci4
IMG=pbkk-ci4-php
DB=pbkk-db
APPS=("pbkk-siakad:siakad:8080:SiakadSeeder" "pbkk-toko:toko-online:8081:TokoSeeder")

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
      -e MARIADB_ROOT_PASSWORD=root \
      -v "$PWD/docker/init.sql":/docker-entrypoint-initdb.d/init.sql:ro \
      -v pbkk-db-data:/var/lib/mysql -p 127.0.0.1:3307:3306 mariadb:11 >/dev/null
    baru=1
  fi
  docker start $DB >/dev/null
  wait_db

  for app in "${APPS[@]}"; do
    IFS=: read -r name dir port seeder <<<"$app"
    ada "$name" || docker run -d --name "$name" --network $NET \
      -v "$PWD/$dir":/var/www/html -p "127.0.0.1:$port:80" $IMG >/dev/null
    docker start "$name" >/dev/null
    docker exec -u "$(id -u):$(id -g)" -e COMPOSER_HOME=/tmp "$name" bash -c \
      'cd /var/www/html && { [ -d vendor ] || composer install --no-interaction --quiet; } && { [ -f .env ] || cp env.docker .env; }'
    docker exec "$name" bash -c 'cd /var/www/html && mkdir -p public/uploads && chmod -R a+rwX writable public/uploads'
    docker exec -u www-data "$name" php spark migrate --all
    if [ $baru = 1 ]; then docker exec -u www-data "$name" php spark db:seed "$seeder"; fi
  done

  echo
  echo "SIAKAD      -> http://localhost:8080"
  echo "Toko Online -> http://localhost:8081"
}

case "${1:-up}" in
  up)    up ;;
  start) docker start $DB >/dev/null; wait_db
         docker start pbkk-siakad pbkk-toko >/dev/null
         echo "SIAKAD -> http://localhost:8080   Toko Online -> http://localhost:8081" ;;
  stop)  docker stop pbkk-siakad pbkk-toko $DB >/dev/null ;;
  reset) for app in "${APPS[@]}"; do
           IFS=: read -r name dir _ _ <<<"$app"
           ada "$name" && docker exec "$name" find public/uploads -type f ! -name .gitignore -delete
           docker rm -f "$name" >/dev/null 2>&1 || true
         done
         docker rm -f $DB >/dev/null 2>&1 || true
         docker volume rm pbkk-db-data >/dev/null 2>&1 || true
         up ;;
  *) echo "Pemakaian: $0 {up|start|stop|reset}"; exit 1 ;;
esac
