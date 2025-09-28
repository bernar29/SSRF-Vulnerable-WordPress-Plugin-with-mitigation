# 🐳 Docker Setup Instructions for SSRF Lab

### This guide shows you how to set up and run the SSRF Lab using Docker Desktop or plain Docker + Docker Compose.
### The lab runs multiple containers (WordPress, database, vulnerable/safe plugins, attacker container, and internal services) so Docker is the easiest way to manage them. 
---

## Prerequisites

- Docker Desktop

- or Docker Engine + Docker Compose plugin

- Git (to clone this repository)

### Verify installation:
```bash
docker --version
docker compose version
```

>[!NOTE]
> Both should print a version string.


### Clone this repository
```bash
git clone https://github.com/bernar29/SSRF-Vulnerable-WordPress-Plugin-with-mitigation.git
cd SSRF-Vulnerable-WordPress-Plugin-with-mitigation
```
---

### Start the lab

From the project root, run:
```bash
docker compose up --build
```
>[!TIP]
> --build ensures images are rebuilt if you changed plugin code.
>

>[!NOTE]
> By default the stack runs in the foreground. To run in background:
>  ```bash
>  docker compose up -d
> ```
---

### Verify running containers

**List containers:**
```bash
docker compose ps
```

#### You should see services like:

- ssrf-lab-wordpress-1      ...   Up   0.0.0.0:8080->80/tcp
- ssrf-lab-internal-api-1   ...   Up
- ssrf-lab-db-1             ...   Up (healthy)
- ssrf-lab-attacker-1       ...   Up
- ssrf-lab-static-assets-1  ...   Up (unhealthy/healthy)
---

### Access WordPress

Open your browser at:

### 👉 http://127.0.0.1:8080

#### Default admin credentials (demo-only):

- Username: admin

- Password: password

>[!IMPORTANT]
> You can now activate plugins (Vulnerable Proxy or Safe Proxy) via the WordPress admin panel.
>
---

### Run the test script

To demonstrate SSRF behavior, use the provided script:
```bash
./scripts/test_ssrf.sh
```

#### Expected:

- With vulnerable plugin: internal secret leaks + timing **attack works**.

- With safe plugin: requests to internal resources are **blocked**.
---

### Stopping the lab

#### To stop containers but keep data volumes:
```bash
docker compose down
```

#### To remove volumes (reset database/content):
```bash
docker compose down --volumes
```
>[!TIP]
> If they're not in use, it's smart to stop the containers to conserve system resources
>
---
### Container debugging tips

#### Open a shell inside the attacker container:
```bash
docker compose exec attacker bash
```

#### Check logs of a service:
```bash
docker compose logs wordpress
docker compose logs internal-api
```
---
### Next steps

- Try switching between vuln-proxy and safe-proxy plugins.

- Experiment with modifying the safe plugin to add stronger allowlists.

- Extend the test script or attacker container to explore mitigations.
---

## ⚠️ Security Warning

This lab is **intentionally vulnerable**.

>[!CAUTION]
> It is for local use only — **do not expose containers to the internet or deploy this stack in production**.
>


