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

SELECT_QUERY = ("SELECT ID, Email, FirstName, Surname FROM Member WHERE Type = 'None';")
UPDATE_MEMBER = ("UPDATE Member SET Type = '%s', Active = %s WHERE ID = %s ;")
SELECT_EXISTS = ("SELECT * FROM MemberEstimatorFeed WHERE Email='%s' AND FirstName ='%s' AND Surname ='%s'")
INSERT_MEMBER_TRAINING_DATA = ("INSERT INTO MemberEstimatorFeed (Email, FirstName, Surname, Type) VALUES('%s', '%s', '%s', '%s');")

root_dir = sys.argv[1]  # param
cursor = None
config = DBConfig(root_dir+"/db.ini").read_db_config()

try:
    # Open database connection
    db = MySQLdb.connect(**config)
    # prepare a cursor object using cursor() method
    cursor = db.cursor()
    df = sql.read_sql(SELECT_QUERY, db)
    X_test = df.replace(np.nan, '', regex=True)

    if not X_test.empty:
        print("Member classification process excerpt :")
        print("")
        print("")

        classifier = pickle.load(open('member_classifier.pickle', 'rb'))

        predicted  = classifier.predict(X_test.drop(['ID'], axis = 1 ))

        for item, type in zip(X_test.to_dict( orient = 'records'), predicted):
            cursor.execute(UPDATE_MEMBER % (type, 1 if type == 'Ham' else 0, item['ID']))
            if type == 'Spam':
                print("[SPAM] - marking member (%s,%s,%s,%s) as spammer and deactivating it.") % (item['Email'].encode('utf-8'), item['FirstName'].encode('utf-8'), item['Surname'].encode('utf-8'), item['ID'])
                cursor.execute(SELECT_EXISTS % (item['Email'],item['FirstName'],item['Surname']))
                exists = cursor.fetchone();
                if exists is None:
                    cursor.execute(INSERT_MEMBER_TRAINING_DATA % (item['Email'], item['FirstName'], item['Surname'], type))
            else:
                print("[HAM] - marking member (%s,%s,%s,%s) as nom spammer.") % (item['Email'].encode('utf-8'), item['FirstName'].encode('utf-8'), item['Surname'].encode('utf-8'), item['ID'])

    else:
        print("nothing to process ... ")

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