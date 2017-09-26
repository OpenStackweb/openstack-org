# Docker Configuration

```bash
wget https://releases.hashicorp.com/vagrant/2.0.0/vagrant_2.0.0_x86_64.deb
dpkg -i vagrant_2.0.0_x86_64.deb
apt-get install docker.io
adduser [user] docker 
```

# Docker up

set execution permissions

```bash
chmod 775 dockerup.sh
```

```bash
./dockerup.sh [SERVER_NAME]
```
this scripts optionally takes as parameter SERVER_NAME
is none is given, then default value is "local.openstack.org"

## cheat sheet

List Container Processes

```bash
docker ps
``` 

Access to Container Shell

```bash
docker exec -i -t SERVER_NAME /bin/bash
``` 

Stop Container

```bash
docker stop [SERVER_NAME]
``` 

Remove Container

```bash
docker rm [SERVER_NAME]
``` 