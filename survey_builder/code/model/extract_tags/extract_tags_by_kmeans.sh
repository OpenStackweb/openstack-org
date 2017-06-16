#!/bin/bash

WORK_DIR=$1
QUESTION_ID=$2
MAX_TAGS=$3
DELETE=$4
CLUSTER=$5
export NLTK_DATA=/tmp/nltk_data;

cd $WORK_DIR;
source $WORK_DIR/env/bin/activate;
python extract_tags_by_kmeans.py $WORK_DIR $QUESTION_ID $MAX_TAGS $DELETE $CLUSTER;