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

import pickle
import MySQLdb
from dbutils import DBConfig
import pandas.io.sql as sql
import numpy as np
import sys
from html_preprocessor import HTMLStripper


root_dir = sys.argv[1]  # param
cursor = None
config = DBConfig(root_dir+"/db.ini").read_db_config()

def stripHtmlFromBody(doc):
    s = HTMLStripper()
    s.feed(doc)
    return s.get_data()

SELECT_QUERY = ("SELECT ID, Email, FirstName, Surname, Bio FROM Member WHERE Type = 'None' AND Bio IS NOT NULL AND Bio <> '';")

try:
    # Open database connection
    db = MySQLdb.connect(**config)
    # prepare a cursor object using cursor() method
    cursor = db.cursor()
    df = sql.read_sql(SELECT_QUERY, db)
    X_test = df.replace(np.nan, '', regex=True)
    X_test['Bio'] = X_test['Bio'].apply(stripHtmlFromBody)
    print("ID,Type")
    if not X_test.empty:
            classifier = pickle.load(open('member_classifier.pickle', 'rb'))
            predicted  = classifier.predict(X_test.drop(['ID'], axis = 1 ))
            for item, type in zip(X_test.to_dict( orient = 'records'), predicted):
                if type == 'Spam':
                    print("%s,%s") % (item['ID'],'Spam')
                else:
                     print("%s,%s") % (item['ID'],'Ham')
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