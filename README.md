# Lifescend

Laravel application source assembled from the cPanel exports provided on 2026-09-25.
This repository is an initial snapshot, not a verified clone of the current live deployment.

## Layout

- The Laravel application lives at the repository root.
- `public/` contains the web entry point and static assets.
- The live cPanel entry point originally referenced a sibling `lifescend/` directory. The repository's `public/index.php` uses Laravel's standard relative paths for local development.
- Runtime files, uploaded media, dependencies, database exports, and credentials are excluded from Git.

## Local configuration

Create a local `.env` from `.env.example`, generate a fresh `APP_KEY`, and use a separate database. Keep mail in the log and use Stripe test credentials if payment testing is needed. Uploaded media from the source export can be copied into `storage/app/public` locally; it is not tracked.

## Deployment

Before each production release, compare the chosen commit with the live files and test its changes against a separate database. The production web root uses a different `index.php` path, so deploy that file with the appropriate server-specific paths. Never deploy `.env`, `bootstrap/cache`, or `storage` from this repository over the live copies.

The source snapshot was compared with the live application on 2026-09-25; the tracked application files matched. SSH access is available using the dedicated key at `~/.ssh/lifescend_deploy_ed25519`:

```text
Host: server390.web-hosting.com
Port: 21098
User: lifenhzd
Application: /home/lifenhzd/lifescend
Web root: /home/lifenhzd/public_html
```

The live app is not a Git checkout. Pushing to GitHub does not publish the website. For an approved release, compare the chosen commit with the current server, back up any affected files and database tables, copy only the reviewed code and static assets over SSH, preserve the server-specific `public_html/index.php` and `.htaccess`, and run any required Laravel cache or migration commands deliberately. Uploaded media remains on the server under `storage`.
