# DeploymentIS

Lab assignments for the **Deployment of Information and Communication Systems** course.

Author: Mykhailo Davydenko, group IK-41.

## Labs

| # | Topic | Code | Report | Branch |
|---|---|---|---|---|
| 1 | Creating a virtual machine with Vagrant | [`labs/labvagrant/`](labs/labvagrant/) | [lab_1.docx](reports/lab_1.docx) | [`labs/lab_1`](https://github.com/MykhailoDav/DeploymentIS/tree/labs/lab_1) |
| 2 | Installing Docker and running the first container | — (Docker CLI commands only) | [lab_2.docx](reports/lab_2.docx) | [`labs/lab_2`](https://github.com/MykhailoDav/DeploymentIS/tree/labs/lab_2) |
| 3 | Working with Docker images | [`mydockerapp/`](labs/mydockerapp/), [`myflaskapp/`](labs/myflaskapp/), [`mynodeapp/`](labs/mynodeapp/) | [lab_3.docx](reports/lab_3.docx) | [`labs/lab_3`](https://github.com/MykhailoDav/DeploymentIS/tree/labs/lab_3) |
| 4 | Networking in Docker | [`myflaskapp/`](labs/myflaskapp/) (v3.0 with MySQL) | [lab_4.docx](reports/lab_4.docx) | [`labs/lab_4`](https://github.com/MykhailoDav/DeploymentIS/tree/labs/lab_4) |
| 5 | Docker Compose: multi-container apps (LEMP stack) | [`docker_compose_lab/`](labs/docker_compose_lab/), [`lemp_compose/`](labs/lemp_compose/) | [lab_5.docx](reports/lab_5.docx) | [`labs/lab_5`](https://github.com/MykhailoDav/DeploymentIS/tree/labs/lab_5) |

Each lab is done in its own `labs/lab_N` branch and merged into `main` via a pull request, so a lab branch shows the code exactly as it was at that lab. Reports are written in Ukrainian.

## Repository structure

```
.
├── labs/
│   ├── labvagrant/          # Lab 1: Vagrantfile for two VMs (Ubuntu 24.04 + Debian 12)
│   ├── mydockerapp/         # Lab 3: static page served by nginx
│   ├── myflaskapp/          # Lab 3–4: Flask app (Python), connects to MySQL since v3.0
│   ├── mynodeapp/           # Lab 3: Express app (Node.js)
│   ├── docker_compose_lab/  # Lab 5: nginx + PHP-FPM via Compose
│   └── lemp_compose/        # Lab 5: LEMP stack (nginx, PHP-FPM, MySQL, phpMyAdmin)
└── reports/                 # Lab reports (.docx)
```

## Environment

- macOS on Apple Silicon (arm64)
- Vagrant 2.4.9 + UTM hypervisor (`vagrant_utm` plugin 0.1.6)
- Docker Desktop 4.95.0 (Docker Engine 29.8.2, Docker Compose v5.6.0)

Docker images are built for `linux/arm64`. All Docker commands below assume Docker Desktop is running and are run from the repository root unless stated otherwise.

## Running

### Lab 1 — Vagrant

```bash
vagrant plugin install vagrant_utm   # once
cd labs/labvagrant
vagrant up                           # VMs: ubuntu (2 CPU, 2 GB RAM) and debian (1 CPU, 1 GB RAM)
vagrant status
vagrant ssh ubuntu
vagrant destroy -f
```

### Lab 3 — Docker images

```bash
# nginx — static page → http://localhost:8080
docker build -t my-nginx:1.0 labs/mydockerapp
docker run -d -p 8080:80 my-nginx:1.0

# Node.js (Express) → http://localhost:4000 and http://localhost:4000/about
docker build -t mynodeapp:2.0 labs/mynodeapp
docker run -d -p 4000:4000 mynodeapp:2.0
```

- **Node.js:** the port comes from the `PORT` environment variable (`ENV PORT=4000` in the Dockerfile). It can be overridden without rebuilding:
  `docker run -d -p 5050:5050 -e PORT=5050 mynodeapp:2.0`
- **Flask:** the Lab 3 version (2.0) is on the [`labs/lab_3`](https://github.com/MykhailoDav/DeploymentIS/tree/labs/lab_3) branch; `main` holds v3.0 from Lab 4.

### Lab 4 — Networking in Docker

The Flask app (v3.0) connects to MySQL by the container name `db`, which Docker's built-in DNS resolves inside a user-defined network.

```bash
docker network create myapp-net
docker run -d --name db --network=myapp-net -e MYSQL_ROOT_PASSWORD=1234 mysql:8
docker build -t myflaskapp:3.0 labs/myflaskapp
docker run -d --name flaskapp --network=myapp-net -p 5000:5000 myflaskapp:3.0

# wait ~30 s for MySQL to start, then open http://localhost:5000/db
docker exec -it flaskapp ping -c 4 db
```

- The database has no published port, so it is reachable only from containers in `myapp-net`.
- `werkzeug==2.2.3` is pinned because Flask 2.2 is incompatible with Werkzeug 3.x; `cryptography` is required by PyMySQL for MySQL 8 authentication.
- `iputils-ping` is installed in the image because `python:*-slim` does not include `ping`.
- On macOS, port 5000 may be taken by AirPlay Receiver. If so, use `-p 5001:5000`.

Clean up: `docker rm -f flaskapp db && docker network rm myapp-net`.

### Lab 5 — Docker Compose

**nginx + PHP-FPM** → http://localhost:8080 (`phpinfo()` page)

```bash
cd labs/docker_compose_lab
docker compose up -d
docker compose down
```

**LEMP stack** → app at http://localhost:8080, phpMyAdmin at http://localhost:8081 (login `webuser` / `webpass`)

```bash
cd labs/lemp_compose
docker compose up -d --build
docker compose logs -f db            # wait for "ready for connections ... port: 3306", then Ctrl+C
docker compose down                  # keeps the database volume; add -v to delete the data too
```

- The PHP service is built from [`php/Dockerfile`](labs/lemp_compose/php/Dockerfile), which adds the `mysqli` extension missing from the official `php:8.2-fpm` image.
- The obsolete top-level `version:` key is omitted; current Compose ignores it and prints a warning.
- `depends_on` only waits for the MySQL container to start, not for the database to be ready, so the page shows a connection error for the first 10–30 s after the first start.

To stop containers started with `docker run`: `docker ps`, then `docker stop <container_id>`.
