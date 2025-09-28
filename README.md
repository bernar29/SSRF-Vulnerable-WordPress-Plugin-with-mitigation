
> [!WARNING]
>### This repository contains intentionally vulnerable code for local educational use only — DO NOT DEPLOY.

# SSRF Lab – WordPress Vulnerable Proxy

A self-contained Docker lab that demonstrates **Server-Side Request Forgery (SSRF)** through a deliberately vulnerable WordPress plugin.  
It also includes an internal API and static assets service to simulate typical SSRF targets.

   ⚠️ **Important:** This environment is intentionally insecure and should only be used for **learning and research**.  
         **Do not deploy in production.**

---

## 🔍 What is SSRF?

Server-Side Request Forgery (SSRF) is a vulnerability where an attacker tricks a server into making requests to arbitrary domains, often accessing internal services that are otherwise inaccessible from the outside world.

This lab shows:
- How SSRF can leak secrets from an internal API
 
- How SSRF can be used for timing-based attacks
 
- Why insecure proxy endpoints are dangerous
 
- How the vulnerability can be mitigated with a hardened plugin

 
>[!NOTE]
> The hardened version only shows how to fix the issues, it's not a precise guide


---

## 🧩 Lab Components

| Service         | Description                                                                 |
|-----------------|-----------------------------------------------------------------------------|
| **WordPress**   | Runs with the `vuln-proxy` plugin, exposing an SSRF endpoint via AJAX.      |
| **MySQL (db)**  | Database backend for WordPress.                                             |
| **Internal API**| Flask app with sensitive endpoints: `/secret`, `/health`, `/slow`.          |
| **Static Assets**| Serves benign files (e.g. PNG images) to simulate normal external requests. |
| **Attacker**    | Minimal container with `curl` for running automated SSRF test scripts.      |


---

## ⚙️ Setup Instructions

>[!TIP]
> For detailed lab instructions, setting up docker, and diving deeper: go see [SETUP.md](SETUP.md).


1. **Clone the repo**
   ```bash
   git clone https://github.com/bernar29/SSRF-Purposely-Vulnerable-App-with-mitigation.git
   cd SSRF-Purposely-Vulnerable-App-with-mitigation
   ```
   
2. **Build and start the containers**
   ```bash
   docker compose up --build -d
   ```

### Set up WordPress

   1. Go to http://localhost:8080

   2. Run the setup wizard

   3. Use db as the database host

   4. Activate the vulnerable plugin

   5. Log in as admin

   6. Go to Plugins

   7. Activate Vulnerable Proxy (SSRF Demo)

## 🧪 Running the Demo

Use the provided script to test SSRF behavior:

```bash
chmod +x scripts/test_ssrf.sh
./scripts/test_ssrf.sh
```

### Expected Results

Public Fetch: **Retrieves a PNG from static-assets**

Internal Secret Leak: **Dumps JSON from internal-api/secret**

Timing Attack: **Noticeable delay (~5s) when hitting internal-api/slow**

**Example Output**
```bash
[*] Baseline public fetch via vulnerable proxy. Expected PNG bytes
HTTP/1.1 200 OK
[*] Fetch internal secrets via vuln proxy url (EXPECTED: secret JSON)
{"internal":true,"note":"Internal service data - not meant for direct external exposure.","secret":"flag{THIS_IS_INTERNAL_SECRET}"}
[*] Timing test (slow vs fast) via vuln proxy
real    0m5.092s
...
```
>[!CAUTION]
> Pull requests should not be modified to include a real secret

**Hardening**
>[!TIP]
> This repo also includes a hardened version of the proxy plugin (safe-proxy) that implements mitigations, such as:

-**Input validation**

-**Allowlist of trusted domains**

-**Blocking requests to private/internal IP ranges**

-**Safer error handling**
>[!IMPORTANT]
> ⚠️ **Running the same tests against the safe proxy should prevent the secret leak and block internal access.**

**Repository Structure**
```
├── docker-compose.yml
├── wordpress/
│   └── wp-content/plugins/
│       ├── vuln-proxy/   # Insecure SSRF demo
│       └── safe-proxy/   # Hardened implementation
├── internal-api/
│   └── app.py            # Flask internal service
├── static-assets/
│   ├── entrypoint.sh
│   └── public/           # Public assets (PNG, etc.)
├── scripts/
│   └── test_ssrf.sh      # SSRF demo script
└── README.md
```
**License**
This project is provided as-is for educational use.
Use responsibly. Do not deploy outside of controlled learning environments.
>[!IMPORTANT]
> Use project owner's email listed at SECURITY.md for reporting violations or information.

