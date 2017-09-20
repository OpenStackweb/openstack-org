#!/bin/bash
# Copyright (c) 2017 OpenStack Foundation
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#    http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
# implied.
# See the License for the specific language governing permissions and
# limitations under the License.

WORK_DIR=$1
ROOT_DIR=$2
QUESTION_ID=$3
MAX_TAGS=$4
DELETE=$5
CLUSTER=$6

export NLTK_DATA=/tmp/nltk_data;
export PYTHONPATH="$PYTHONPATH:$ROOT_DIR/pyutils";

source $ROOT_DIR/env/bin/activate;
cd $WORK_DIR;
python extract_tags_by_kmeans.py $ROOT_DIR $QUESTION_ID $MAX_TAGS $DELETE $CLUSTER;
deactivate;