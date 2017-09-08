#!/bin/bash

WORK_DIR=$1
MODEL_FOLDER=$2
QUESTION_ID=$3
DELETE=$4

export NLTK_DATA=/tmp/nltk_data;

cd $WORK_DIR;
source $WORK_DIR/env/bin/activate;
python bayesian_tag_extraction.py $WORK_DIR $MODEL_FOLDER $QUESTION_ID $DELETE;