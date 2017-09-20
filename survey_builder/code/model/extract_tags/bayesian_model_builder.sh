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
MODEL_FOLDER=$3

export NLTK_DATA=/tmp/nltk_data;
export PYTHONPATH="$ROOT_DIR/pyutils";

source $ROOT_DIR/env/bin/activate;
cd $WORK_DIR;

python bayesian_model_builder.py $ROOT_DIR $MODEL_FOLDER;

deactivate;