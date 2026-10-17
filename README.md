# DeploymentIS

Lab assignments for the **Deployment of Information and Communication Systems** course.

Author: Mykhailo Davydenko, group IK-41.

## Labs

| # | Topic | Code | Report | Branch |
|---|---|---|---|---|
| 1 | Creating a virtual machine with Vagrant | [`labvagrant/`](labvagrant/) | [lab_1.docx](reports/lab_1.docx) | [`labs/lab_1`](https://github.com/MykhailoDav/DeploymentIS/tree/labs/lab_1) |
| 2 | Installing Docker and running the first container | — (Docker CLI commands only) | [lab_2.docx](reports/lab_2.docx) | [`labs/lab_2`](https://github.com/MykhailoDav/DeploymentIS/tree/labs/lab_2) |
| 3 | Working with Docker images | [`labs/`](labs/) | [lab_3.docx](reports/lab_3.docx) | [`labs/lab_3`](https://github.com/MykhailoDav/DeploymentIS/tree/labs/lab_3) |

Each lab is done in its own `labs/lab_N` branch and merged into `main` via a pull request. Reports are written in Ukrainian.

## Repository structure

```
.
├── labvagrant/          # Lab 1: Vagrantfile for two VMs (Ubuntu 24.04 + Debian 12)
├── labs/                # Lab 3: custom Docker images
│   ├── mydockerapp/     #   static page served by nginx
│   ├── myflaskapp/      #   Flask app (Python)
│   └── mynodeapp/       #   Express app (Node.js)
└── reports/             # Lab reports (.docx)
```

## Environment

- macOS on Apple Silicon (arm64)
- Vagrant 2.4.9 + UTM hypervisor (`vagrant_utm` plugin 0.1.6)
- Docker Desktop 4.95.0 (Docker Engine 29.8.2)

Docker images are built for `linux/arm64`.

## Running

### Lab 1 — Vagrant

```bash
vagrant plugin install vagrant_utm   # once
cd labvagrant
vagrant up                           # VMs: ubuntu (2 CPU, 2 GB RAM) and debian (1 CPU, 1 GB RAM)
vagrant status
vagrant ssh ubuntu
vagrant destroy -f
```

### Lab 3 — Docker images

Docker Desktop must be running. Run the commands from the repository root.

```bash
# nginx — static page → http://localhost:8080
docker build -t my-nginx:1.0 labs/mydockerapp
docker run -d -p 8080:80 my-nginx:1.0

# Flask → http://localhost:5000
docker build -t myflaskapp:2.0 labs/myflaskapp
docker run -d -p 5000:5000 myflaskapp:2.0

# Node.js (Express) → http://localhost:4000 and http://localhost:4000/about
docker build -t mynodeapp:2.0 labs/mynodeapp
docker run -d -p 4000:4000 mynodeapp:2.0
```

The repository holds the latest (2.0) version of the Flask and Node.js apps. Changes from version 1.0 are described in the Lab 3 report.

- **Flask:** `werkzeug==2.2.3` is pinned explicitly because Flask 2.2 is incompatible with Werkzeug 3.x.
- **Node.js:** the port comes from the `PORT` environment variable (`ENV PORT=4000` in the Dockerfile). It can be overridden without rebuilding:
  `docker run -d -p 5050:5050 -e PORT=5050 mynodeapp:2.0`
- **macOS:** port 5000 may be taken by AirPlay Receiver. If so, run Flask with `-p 5001:5000`.

To stop containers: `docker ps`, then `docker stop <container_id>`.
