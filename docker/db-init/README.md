# Database Init

Place your database dump here as a `.sql` file to auto-import it when the MySQL container starts for the first time.

## Steps

1. Export the live database from your hosting panel (phpMyAdmin or SSH):
   ```
   mysqldump -u USERNAME -p DATABASE_NAME > gamtech_live.sql
   ```

2. Copy the `.sql` file into this folder:
   ```
   docker/db-init/gamtech_live.sql
   ```

3. Run `docker compose up -d` — MySQL will automatically import it on first start.

> **Note:** The auto-import only runs once (on first container creation).
> If the container already exists, delete the volume first:
> `docker compose down -v` then `docker compose up -d`
