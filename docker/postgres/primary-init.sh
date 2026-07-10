#!/bin/bash
# Runs once, during first initialisation of the primary's data volume.
# Creates the replication role + physical slots and allows replica connections.
set -e

REP_PASS="$(cat /run/secrets/replication_password)"

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE ROLE replicator WITH REPLICATION LOGIN PASSWORD '${REP_PASS}';
    SELECT pg_create_physical_replication_slot('replica1_slot');
    SELECT pg_create_physical_replication_slot('replica2_slot');
EOSQL

# Allow the replicas (any host on the compose network) to stream.
echo "host replication replicator all scram-sha-256" >> "$PGDATA/pg_hba.conf"
