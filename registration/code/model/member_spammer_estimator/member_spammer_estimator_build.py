# -*- coding: utf-8 -*-
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
import numpy as np
from sklearn.naive_bayes import MultinomialNB, BernoulliNB, GaussianNB
from sklearn.feature_extraction.text import HashingVectorizer, CountVectorizer, TfidfTransformer
import pickle
from sklearn.pipeline import Pipeline, FeatureUnion
from data_frame_column_extracter import DataFrameColumnExtracter
from sklearn.svm import SVC
import sys
from html_preprocessor import StripHTMLTransformer

queryGetClassifiedMembers = ("SELECT Email, FirstName, Surname, Bio, Type FROM MemberEstimatorFeed; ")
root_dir = sys.argv[1]  # param

cursor = None
config = DBConfig(root_dir+"/db.ini").read_db_config()

try:
    # Open database connection
    db = MySQLdb.connect(**config)
    # prepare a cursor object using cursor() method
    cursor  = db.cursor()
    pd = sql.read_sql(queryGetClassifiedMembers, db)
    data = pd.replace(np.nan, '', regex=True)
    labels = pd.Type
    trainData = data.drop(['Type'], axis=1)

    email_pipe = Pipeline([
        ('data', DataFrameColumnExtracter('Email')),
        ('vectorizer', HashingVectorizer(non_negative=True))
    ])

    fname_pipe = Pipeline([
        ('data', DataFrameColumnExtracter('FirstName')),
        ('vectorizer', HashingVectorizer(non_negative=True))
    ])

    lname_pipe = Pipeline([
        ('data', DataFrameColumnExtracter('Surname')),
        ('vectorizer', HashingVectorizer(non_negative=True))
    ])

    bio_pipe = Pipeline([
        ('data', DataFrameColumnExtracter('Bio')),
        ('preprocessor', StripHTMLTransformer()),
        ('vectorizer', CountVectorizer(strip_accents='unicode', stop_words='english', ngram_range=(1, 3))),
        ('tfidf', TfidfTransformer())
    ])

    features = FeatureUnion(
        n_jobs=1,
        transformer_list=[
            ('email_pipe', email_pipe),
            ('fname_pipe', fname_pipe),
            ('lname_pipe', lname_pipe),
            ('bio_pipe',   bio_pipe)
        ],
        transformer_weights=None)

    classifier = Pipeline([
        ('features', features),
        ('model', MultinomialNB(alpha=0.0001, fit_prior=True))
    ])

    classifier.fit(trainData, labels)

    filename = 'member_classifier.pickle'
    print "writing model to file %s" % (filename)
    pickle.dump(classifier, open(filename, 'wb'))
    db.commit()
except Exception as e:
   print e
   # Rollback in case there is any error
   db.rollback()
   raise

finally:

    if not (cursor is None):
        cursor.close()
    # disconnect from server
    if not (db is None):
        db.close()