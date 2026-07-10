#!/bin/bash
# Clones the primary via pg_basebackup on first boot, then runs as a hot standby
# that streams WAL from the primary through a named replication slot.
set -e

# Use the data directory baked into the image (PG18 uses a versioned subdir such
# as /var/lib/postgresql/18/docker; older majors use /var/lib/postgresql/data).
PGDATA="${PGDATA:-/var/lib/postgresql/data}"
REP_PASS="$(cat /run/secrets/replication_password)"

mkdir -p "$PGDATA"
chown -R postgres:postgres /var/lib/postgresql

if [ ! -s "$PGDATA/PG_VERSION" ]; then
    echo "[replica ${REPLICATION_SLOT}] cloning from ${PRIMARY_HOST} into ${PGDATA} ..."
    rm -rf "${PGDATA:?}/"* 2>/dev/null || true
    export PGPASSWORD="$REP_PASS"

    until pg_isready -h "$PRIMARY_HOST" -p 5432 -U "$REPLICATION_USER" -q; do
        echo "[replica ${REPLICATION_SLOT}] waiting for ${PRIMARY_HOST} ..."
        sleep 2
    done

    gosu postgres pg_basebackup -h "$PRIMARY_HOST" -p 5432 -U "$REPLICATION_USER" -D "$PGDATA" -Fp -Xs -P

    cat >> "$PGDATA/postgresql.auto.conf" <<-EOF
primary_conninfo = 'host=${PRIMARY_HOST} port=5432 user=${REPLICATION_USER} password=${REP_PASS} application_name=${REPLICATION_SLOT}'
primary_slot_name = '${REPLICATION_SLOT}'
EOF
    touch "$PGDATA/standby.signal"
    chown -R postgres:postgres /var/lib/postgresql
    chmod 0700 "$PGDATA"
fi

exec gosu postgres postgres -D "$PGDATA" -c listen_addresses='*' -c hot_standby=on
