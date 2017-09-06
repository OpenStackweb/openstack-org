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

working_dir = sys.argv[1]  # param
folder      = sys.argv[2]  # param
questionId  = int(sys.argv[3])  # param
mustDeleteFormerAutomaticTags = int(sys.argv[4])  # param

print("must delete former tags %s" % (mustDeleteFormerAutomaticTags))
print("")

queryTag = "SELECT ID FROM SurveyAnswerTag WHERE Type='AUTOMATIC' AND Value = '%s' "
queryInsertTag = "INSERT INTO SurveyAnswerTag (Created,LastEdited, Value, Type, CreatedByID) VALUES(NOW(), NOW(), '%s', 'AUTOMATIC', 0 )"
queryAnswers = "SELECT ID, Value FROM SurveyAnswer WHERE Value IS NOT NULL AND QuestionID = %d"
queryInsertAnswerTag = "INSERT INTO SurveyAnswer_Tags(SurveyAnswerID, SurveyAnswerTagID) VALUES(%d, %d)"
querySelectAnswerTag = "SELECT ID FROM SurveyAnswer_Tags WHERE SurveyAnswerID = %d AND SurveyAnswerTagID = %d"
queryDeleteFormerTags = "DELETE FROM SurveyAnswer_Tags WHERE SurveyAnswerID = %d"
queryGetQuestionById = "SELECT Name, ClassName FROM SurveyQuestionTemplate WHERE ID = %s;"
config = DBConfig(working_dir+"/db.ini").read_db_config()

try:
    # Open database connection
    db = MySQLdb.connect(**config)
    cursor = db.cursor()
    data = sql.read_sql(queryAnswers % questionId, db)
    cursor.execute(queryGetQuestionById % (questionId))
    row = cursor.fetchone()
    key = row[0].lower()
    filename= '%s/%s.pickle' % (folder, key)

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

        for idx, answerId in enumerate(data['ID'].tolist()):
            tags = keywords[idx];
            if len(tags) == 0:
                continue
            print("found tags for answer id %s" % (answerId))
            if mustDeleteFormerAutomaticTags > 0:
                cursor.execute(queryDeleteFormerTags % (answerId))
            # tag processing
            for tag in tags:
                tag = re.sub(r"[^\w\d]", "", tag)
                if len(tag) > 2:
                    cursor.execute(queryTag % (tag))
                    dbTag = cursor.fetchone()
                    tagId = None
                    if dbTag is None:
                        res = cursor.execute(queryInsertTag % (tag))
                        tagId = cursor.lastrowid
                    else:
                        tagId = dbTag[0]
                    cursor.execute(querySelectAnswerTag % (answerId, tagId))
                    existAnswerTag = cursor.fetchone()
                    if existAnswerTag is None:
                        cursor.execute(queryInsertAnswerTag % (answerId, tagId))
    db.commit()
except:
   # Rollback in case there is any error
   db.rollback()
   raise
finally:
    # disconnect from server
    if not (db is None):
        db.close()
    if not (cursor is None):
        cursor.close()