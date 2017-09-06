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

from __future__ import print_function
import MySQLdb
from dbutils import DBConfig
import numpy as np
from sklearn.pipeline import Pipeline
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.svm import LinearSVC
from sklearn.feature_extraction.text import TfidfTransformer
from sklearn.multiclass import OneVsRestClassifier
from sklearn.preprocessing import MultiLabelBinarizer
import pickle
import shutil
import os
import sys, getopt

def create_model(key, answers, tags):

    filename     = '%s/%s.pickle' % (folder, key)
    X_train      = np.array(answers)
    y_train_text = tags

    mlb = MultiLabelBinarizer()
    Y = mlb.fit_transform(y_train_text)

    classifier = Pipeline([
        ('vectorizer', CountVectorizer()),
        ('tfidf', TfidfTransformer()),
        ('clf', OneVsRestClassifier(LinearSVC()))])

    classifier.fit(X_train, Y)

    # Serialize both the pipeline and binarizer to disk.
    with open(filename, 'wb') as f:
        pickle.dump((mlb, classifier), f)

if len(sys.argv) < 3:
    print("you must provide a working path and a folder to store bayesian model")
    exit(-1)

cursor = None
cursor2 = None
working_dir = sys.argv[1]  # param
folder      = sys.argv[2]  # param

querySelectGetTags = ("SELECT SurveyAnswer.Value AnswerValue, QuestionID, GROUP_CONCAT(SurveyAnswerTag.Value) AS Tags"
                     " FROM SurveyAnswer INNER JOIN SurveyAnswer_Tags ON SurveyAnswer_Tags.SurveyAnswerID = SurveyAnswer.ID"
                     " INNER JOIN SurveyAnswerTag ON SurveyAnswerTag.ID = SurveyAnswer_Tags.SurveyAnswerTagID"
                     " GROUP BY  SurveyAnswer.ID, SurveyAnswer.Value, QuestionID ORDER BY QuestionID;")
queryGetQuestionById = "SELECT Name FROM SurveyQuestionTemplate WHERE ID = %s;"
answers = []
tags    = []
db = None
config = DBConfig(working_dir+"/db.ini").read_db_config()

try:
    # Open database connection
    db      = MySQLdb.connect(**config)
    # prepare a cursor object using cursor() method
    cursor  = db.cursor()
    cursor2 = db.cursor()
    if os.path.exists(folder):
        shutil.rmtree(folder)
    os.makedirs(folder)
    cursor.execute(querySelectGetTags)
    lastQuestionId = 0
    questionName = None
    questionClass = None
    dict = {}
    for (AnswerValue, QuestionID, Tags) in cursor:
        if lastQuestionId != QuestionID:

            if lastQuestionId != 0:
                cursor2.execute(queryGetQuestionById % (lastQuestionId))
                row  = cursor2.fetchone()
                key  = row[0].lower()
                if not key in dict:
                    dict[key] = ([], [])
                dict[key][0].extend(answers)
                dict[key][1].extend(tags)

                answers = []
                tags    = []

            lastQuestionId = QuestionID

        answers.append(AnswerValue)
        tags.append(Tags.split(","))

    cursor2.execute(queryGetQuestionById % (lastQuestionId))
    row = cursor2.fetchone()
    key = row[0].lower()
    if not key in dict:
        dict[key] = ([], [])
    dict[key][0].extend(answers)
    dict[key][1].extend(tags)

    # now construct models ....
    for key, values in dict.iteritems():
        print("creating model for question %s" % (key), end='')
        print()
        create_model(key, values[0], values[1])
    db.commit()

except:
   # Rollback in case there is any error
   db.rollback()
   raise
finally:
    if not (cursor is None):
        cursor.close()
    if not (cursor2 is None):
        cursor2.close()
    # disconnect from server
    if not (db is None):
        db.close()