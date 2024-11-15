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

ROOT_DIR=$PWD;

echo "installing python virtual env...";

if [[ -f "$ROOT_DIR/requirements.txt" ]]; then
	echo "found requirements.txt files";
	# install virtual env

	echo "creating py venv"
  python3 -m venv env
  source $ROOT_DIR/env/bin/activate;

  cd $ROOT_DIR && pip install --upgrade pip
  cd $ROOT_DIR && pip install --upgrade setuptools

	# install dependencies
	$ROOT_DIR/env/bin/python env/bin/pip install -r requirements.txt;

    # here you need to add all bash/python facades scripts
    FILES="survey_builder/code/model/extract_tags/extract_tags_by_rake.sh
    survey_builder/code/model/extract_tags/extract_tags_by_kmeans.sh
    survey_builder/code/model/extract_tags/bayesian_model_builder.sh
    survey_builder/code/model/extract_tags/bayesian_tag_extraction.sh
    registration/code/model/member_spammer_estimator/member_spammer_estimator_build.sh
    registration/code/model/member_spammer_estimator/member_spammer_estimator_process.sh
    ";
    for f in $FILES
    do
        echo "setting permissions to file $ROOT_DIR/$f";
	    chmod 770 $ROOT_DIR/$f;
	done
fi