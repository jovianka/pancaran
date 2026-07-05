-- Runs only on first initialisation of the postgres data volume.
-- Creates the database used by the PHPUnit test suite (see phpunit.xml: DB_DATABASE=testing).
-- Owned by POSTGRES_USER so the app credentials can migrate/refresh it.
CREATE DATABASE testing;
