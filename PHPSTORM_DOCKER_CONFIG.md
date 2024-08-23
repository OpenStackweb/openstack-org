Debug config at PHPSTORM
==========================

1. Add a new CLI interpreter using [Docker]
   1. choose image www-openstack:latest
   2. click [OK]
   3. select new CLI interpreter
   4. click [Apply]

2. Edit network settings at container
   1. goto Settings->PHP and locate "Docker Container" input.
   2. Click on Folder icon.
   3. a new popup titled "Edit Docker Container Settings" will open.
   4. fill the "Network Mode" input with the bridge name, to find it out run ```$docker network list``` command.
   and put the name of the bridge there.

3. Create new server
   1. goto Settings->PHP->Servers
   2. click on [+]
   3. fill up Name with "Docker"
   4. fill up Host with "0.0.0.0"
   5. fill up Port with "80"
   6. click use map mappings
   7. map root to /var/www

4. Create a remote debug configuration profile
   1. goto Run->Debug->Edit Configurations
   2. create a new "PHP Remote debug" Profile
   3. set name as "Docker"
   4. check "Filter debug connection by IDE key"
   5. set IDE KEY as "PHPSTORM"
   6. set Server as "Docker"