#!/usr/bin/env python
#
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

import MySQLdb
from dbutils import DBConfig
import pandas.io.sql as sql
import os
import numpy as np
import pickle
import re
import sys, getopt

cursor = None
db = None

if len(sys.argv) < 5:
    print("you must provide a working path , a folder to store bayesian model , and a question id")
    exit(-1)

root_dir = sys.argv[1]  # param
model_folder = sys.argv[2]  # param
question_id  = int(sys.argv[3])  # param
must_delete_former_tags = int(sys.argv[4])  # param

print("must delete former tags %s" % (must_delete_former_tags))
print("")

QUERY_TAG = "SELECT ID FROM SurveyAnswerTag WHERE Type='AUTOMATIC' AND Value = '%s' "
QUERY_INSERT_TAG = "INSERT INTO SurveyAnswerTag (Created,LastEdited, Value, Type, CreatedByID) VALUES(NOW(), NOW(), '%s', 'AUTOMATIC', 0 )"
QUERY_ANSWERS = "SELECT ID, Value FROM SurveyAnswer WHERE Value IS NOT NULL AND QuestionID = %d"
QUERY_INSERT_ANSWER_TAG = "INSERT INTO SurveyAnswer_Tags(SurveyAnswerID, SurveyAnswerTagID) VALUES(%d, %d)"
QUERY_SELECT_ANSWER_TAG = "SELECT ID FROM SurveyAnswer_Tags WHERE SurveyAnswerID = %d AND SurveyAnswerTagID = %d"
QUERY_DELETE_FORMER_TAGS = "DELETE FROM SurveyAnswer_Tags WHERE SurveyAnswerID = %d"
QUERY_GET_QUESTION_BY_ID = "SELECT Name, ClassName FROM SurveyQuestionTemplate WHERE ID = %s;"

config = DBConfig(root_dir+"/db.ini").read_db_config()

try:
    # Open database connection
    db = MySQLdb.connect(**config)
    cursor = db.cursor()
    data = sql.read_sql(QUERY_ANSWERS % question_id, db)
    cursor.execute(QUERY_GET_QUESTION_BY_ID % (question_id))
    row = cursor.fetchone()
    key = row[0].lower()
    filename= '%s/%s.pickle' % (model_folder, key)

    if not os.path.exists(filename):
        print "%s file model does not exists!" % (filename)
        exit(-3)

    # Hydrate the serialized objects.
    with open(filename, 'rb') as f:
        mlb, classifier = pickle.load(f)
        predicted = classifier.predict(np.array(data['Value'].tolist()))
        # list of tuple per each element
        keywords = mlb.inverse_transform(predicted)
        if len(keywords) == 0:
            exit(-4)

        for idx, answer_id in enumerate(data['ID'].tolist()):
            tags = keywords[idx];
            if len(tags) == 0:
                continue
            print("found tags for answer id %s" % (answer_id))
            if must_delete_former_tags > 0:
                print("deleting former tags for answer id %s ..." % answer_id)
                cursor.execute(QUERY_DELETE_FORMER_TAGS % (answer_id))
            # tag processing
            for tag in tags:
                tag = re.sub(r"[^\w\d]", "", tag)
                if len(tag) > 2:
                    cursor.execute(QUERY_TAG % (tag))
                    db_tag = cursor.fetchone()
                    tag_id = None
                    if db_tag is None:
                        res = cursor.execute(QUERY_INSERT_TAG % (tag))
                        tag_id = cursor.lastrowid
                    else:
                        tag_id = db_tag[0]
                    cursor.execute(QUERY_SELECT_ANSWER_TAG % (answer_id, tag_id))
                    exists_answer_tag = cursor.fetchone()
                    if exists_answer_tag is None:
                        cursor.execute(QUERY_INSERT_ANSWER_TAG % (answer_id, tag_id))
    db.commit()
except Exception as e:
   print(e)
   # Rollback in case there is any error
   db.rollback()
   raise
finally:
    # disconnect from server
    if not (db is None):
        db.close()
    if not (cursor is None):
        cursor.close()