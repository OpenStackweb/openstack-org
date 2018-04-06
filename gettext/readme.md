# Requirements

````bash
 sudo apt-get install gettext
 sudo apt-get install language-pack-en-base
 sudo apt-get install language-pack-es-base
 sudo apt-get install language-pack-de-base
 sudo apt-get install language-pack-fr-base
 sudo apt-get install language-pack-ru-base
 sudo apt-get install language-pack-gr-base
 sudo apt-get install language-pack-ko-base
 sudo apt-get install language-pack-ja-base
 sudo apt-get install language-pack-id-base
 sudo apt-get install language-pack-zh-hant-base
 sudo apt-get install language-pack-zh-hans-base
````

to compile from .po to .mo files 
there is a custom task

````
sake dev/tasks/CompilePO2MOTask
````

check system language support 

```
locale -a
```


download po files from zanata server

```
sake dev/tasks/ZanataServerPOFilesDownloaderTask
```

# Workflow

once that a .pot file is updated, should be uploaded to zanata
for new translations
