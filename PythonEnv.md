# Binary Requirements
 
 ```bash

 sudo apt-get install python-pip python-dev libmysqlclient-dev;
 pip install virtualenv;
 cd survey_builder/code/model/extract_tags;
 virtualenv env;
 source env/bin/activate;
 pip install -r requirements.txt;
 
```

if your run in any issue bc path is too long

then run

```bash
env/bin/python env/bin/pip install -r requirements.txt;
```

##### Upgraded to Ubuntu 16.04 MySQL-python dependencies broken error

if you get error

```bash
ImportError: libmysqlclient.so.18: cannot open shared object file: No such file or directory
```

then

```bash
pip uninstall --no-binary MySQL-python MySQL-python
pip install --no-binary MySQL-python MySQL-python
``` 


after that, you should give the proper execution rights to scripts

```bash
chmod 770 scripts/setup_python_env.sh
./scripts/setup_python_env.sh
```
