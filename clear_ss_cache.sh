#!/bin/bash

if [ ! -d silverstripe-cache ]; then
	echo "creating silverstripe-cache directory";
	mkdir -p silverstripe-cache;
fi

sudo rm -Rf silverstripe-cache/*
sudo chmod 777 silverstripe-cache;
sudo chown $USER:www-data silverstripe-cache;