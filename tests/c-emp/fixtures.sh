#!/usr/bin/env bash
# Disposable C-EMP fixtures only; never uses the normal Compose database or volumes.
set -euo pipefail
cd "$(dirname "$0")/../.."
case "${1:-}" in
start)
    test -f vendor/autoload.php || { echo 'Provide the locked Composer vendor/ dependencies first (see README).'; exit 1; }
    docker image inspect montero-dental-dev-app mariadb:10.11 >/dev/null
    for name in cemp-test-db cemp-test-app cemp-test-net cemp-test-web; do
        if docker inspect "$name" >/dev/null 2>&1; then echo "Refusing existing resource: $name"; exit 1; fi
    done
    # Nested read-only mounts need targets; existing ignored configs are never overwritten.
    test -f config/config.php || cp config/config.example.php config/config.php
    test -f config/local.php || cp config/docker.example.php config/local.php
    docker network create --internal --label montero.cemp.disposable=true cemp-test-net >/dev/null
    docker network create --label montero.cemp.disposable=true cemp-test-web >/dev/null
    docker run -d --name cemp-test-db --label montero.cemp.disposable=true \
        --network cemp-test-net --network-alias db \
        --tmpfs /var/lib/mysql:rw,noexec,nosuid,size=512m \
        -e MARIADB_ROOT_PASSWORD=CempDisposableOnly2026 \
        -e MARIADB_DATABASE=monterodental_demo -e MARIADB_USER=montero_demo \
        -e MARIADB_PASSWORD='LocalDemo2026!' mariadb:10.11 >/dev/null
    ready=false
    for attempt in {1..30}; do
        if docker exec cemp-test-db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" -N monterodental_demo -e "SELECT 1"' >/dev/null 2>&1; then ready=true; break; fi
        sleep 1
    done
    "$ready" || { echo 'Test database not ready; inspect cemp-test-db logs.'; exit 1; }
    docker run -d --name cemp-test-app --label montero.cemp.disposable=true \
        --network cemp-test-net -p 127.0.0.1:8018:8000 \
        --mount "type=bind,src=$PWD,dst=/workspace,readonly" \
        --mount "type=bind,src=$PWD/config/config.example.php,dst=/workspace/config/config.php,readonly" \
        --mount "type=bind,src=$PWD/config/docker.example.php,dst=/workspace/config/local.php,readonly" \
        --tmpfs /workspace/storage/sessions:rw,nosuid,size=16m \
        --tmpfs /workspace/storage/attachments:rw,nosuid,size=16m \
        -e MONTERO_DEMO_DOCKER=1 -e MONTERO_DEMO_DB_ROOT_PASSWORD=CempDisposableOnly2026 \
        -w /workspace/public montero-dental-dev-app php -S 0.0.0.0:8000 dev-router.php >/dev/null
    docker network connect cemp-test-web cemp-test-app
    python3 -B tests/c-emp/acceptance.py --guard
    python3 - <<'PY'
import subprocess
out = subprocess.check_output(['docker','exec','-i','cemp-test-db','sh','-c',
    'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" -N'],
    input="SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'monterodental_demo';", text=True)
if out.strip() != '0':
    raise SystemExit('Refusing nonempty fixture database')
seed = subprocess.check_output(['docker','exec','cemp-test-app','php','/workspace/scripts/setup-demo.php'], text=True)
if 'Seeded demo:' not in seed:
    raise SystemExit('Fictional fixture seed did not complete')
print('PASS fresh owned tmpfs DB seeded; credential output omitted')
PY
    ;;
dispose)
    # Guard checks names, ownership, storage, networks and actual DB destination first.
    python3 -B tests/c-emp/acceptance.py --guard
    docker rm -f cemp-test-app cemp-test-db >/dev/null
    docker network rm cemp-test-web cemp-test-net >/dev/null
    echo 'PASS only owned C-EMP fixture containers/networks disposed; tmpfs data discarded'
    ;;
*) echo 'Usage: bash tests/c-emp/fixtures.sh {start|dispose}'; exit 2 ;;
esac
