#!/bin/bash

WORK_DIR=$1
MODEL_FOLDER=$2

export NLTK_DATA=/tmp/nltk_data;

cd $WORK_DIR;
source $WORK_DIR/env/bin/activate;
python bayesian_model_builder.py $WORK_DIR $MODEL_FOLDER;