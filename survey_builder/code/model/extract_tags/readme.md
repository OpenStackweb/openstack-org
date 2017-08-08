 
 # Survey Answers Extract Tags (KMEANS)
 
 ## Binary Requirements
 
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

after that, you should give the proper execution rights to scripts

```bash
chmod 770 extract_tags_by_rake.sh
chmod 770 extract_tags_by_kmeans.sh
```

