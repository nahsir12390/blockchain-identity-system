# Render Deployment Guide

This project is ready to deploy on Render as a Docker Laravel web service with SQLite for a low-cost student demo.

Important: Render Free web services use an ephemeral filesystem. SQLite will work, but its data can reset after a redeploy, restart, or spin-down. The app seeds a demo admin on startup so the student can still present the system even if the SQLite file is recreated.

## 1. Generate the Laravel app key

Run this locally inside the project:

```bash
php artisan key:generate --show
```

Copy the full value that starts with `base64:`.

## 2. Push the project to GitHub

Render deploys from a Git provider, so push this `blockchain-identity-system` folder to a GitHub repository.

Do not commit `.env`, `vendor`, or `node_modules`.

## 3. Create the Render Blueprint

In Render:

1. Click **New**.
2. Choose **Blueprint**.
3. Connect the GitHub repository.
4. Select the branch that contains `render.yaml`.
5. Deploy the Blueprint.

Render will create:

- A Docker web service.
- Environment variables wired to the app.

## 4. Fill the required secret variables

When Render asks for variables marked `sync: false`, use:

```text
APP_KEY=base64:paste-the-generated-key-here
APP_URL=https://your-render-service-name.onrender.com
ASSET_URL=https://your-render-service-name.onrender.com
DEMO_ADMIN_EMAIL=admin@example.com
DEMO_ADMIN_PASSWORD=UseAStrongDemoPassword123!
```

After the first deploy, the student can log in with the demo admin email and password.

## 5. Presentation notes

For a simple school presentation, keep:

```text
BLOCKCHAIN_ENABLED=false
BLOCKCHAIN_NETWORK=render-demo-local-ledger
```

That uses the local proof ledger already built into the app. If you later connect a live blockchain RPC and deployed smart contract, update:

```text
BLOCKCHAIN_ENABLED=true
BLOCKCHAIN_RPC_URL=
BLOCKCHAIN_PRIVATE_KEY=
BLOCKCHAIN_CONTRACT_ADDRESS=
BLOCKCHAIN_NETWORK=
```

## 6. If deployment fails

Check Render logs first. The startup script runs:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
```

Most failures are usually from a missing `APP_KEY`, wrong database URL, or a weak demo password if production password rules are active.

## 7. If you need data to survive restarts

SQLite needs persistent disk storage to survive reliably. Render Free does not provide persistent disks. For long-term data persistence, use one of these:

- Render paid web service with a persistent disk mounted to the SQLite database path.
- An external database provider with a free tier.
- A small VPS where the SQLite file lives on the server disk.
