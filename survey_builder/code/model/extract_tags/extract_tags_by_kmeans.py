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
import pandas.io.sql as sql
import pandas as pd
import nltk
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.cluster import KMeans
import sys, getopt
import re
from nltk import WordNetLemmatizer
from nltk import sent_tokenize
from nltk import pos_tag
from nltk import wordpunct_tokenize
import string
from nltk.corpus import wordnet as wn
import traceback

if len(sys.argv) < 6:
    print("you must provide a working path , a question id , a max number of tags, delete param, qty of clusters")
    exit(-1)

working_dir = sys.argv[1]  # param
question_id = int(sys.argv[2])  # param
max_tags = int(sys.argv[3])  # param
delete_former_tags = int(sys.argv[4])  # param
num_clusters = int(sys.argv[5])
download_dir="/tmp/nltk_data"

# fixed config params
max_df = 0.6
# min_idf: this could be an integer (e.g. 5) and the term would have to be in at least 5 of the documents to be
# considered.
# Here I pass 3; the term must be in at least 3 documents.
min_df = 3
min_tag_len = 2
# query def
queryTag = "SELECT ID FROM SurveyAnswerTag WHERE Type='AUTOMATIC' AND Value = '%s' "
queryInsertTag = "INSERT INTO SurveyAnswerTag (Created,LastEdited, Value, Type, CreatedByID) VALUES(NOW(), NOW(), '%s', 'AUTOMATIC', 0 )"
queryAnswers = "SELECT ID, Value FROM SurveyAnswer WHERE Value IS NOT NULL AND QuestionID = %d"
queryInsertAnswerTag = "INSERT INTO SurveyAnswer_Tags(SurveyAnswerID, SurveyAnswerTagID) VALUES(%d, %d)"
querySelectAnswerTag = "SELECT ID FROM SurveyAnswer_Tags WHERE SurveyAnswerID = %d AND SurveyAnswerTagID = %d"
queryDeleteFormerTags = "DELETE FROM SurveyAnswer_Tags WHERE SurveyAnswerID = %d AND SurveyAnswerTagID IN (SELECT ID FROM SurveyAnswerTag where Type = 'AUTOMATIC')"

# resources download
nltk.download('stopwords', download_dir)
nltk.download('punkt', download_dir)
nltk.download('averaged_perceptron_tagger', download_dir)
nltk.download('wordnet', download_dir)
nltk.download('pickle', download_dir)

punct = string.punctuation
stopwords = nltk.corpus.stopwords.words('english')
lemmatizer = WordNetLemmatizer()

def process_text(text):

    for sent in sent_tokenize(text):
        for token, tag in pos_tag(wordpunct_tokenize(sent)):
            # Apply preprocessing to the token
            token = token.lower()
            token = token.strip()
            token = token.strip('_')
            token = token.strip('*')
            # If punctuation or stopword, ignore token and continue
            if token in stopwords or all(char in punct for char in token):
                continue

            # Lemmatize the token and yield
            lemma = lemmatize(token, tag)
            yield lemma

def lemmatize(token, tag):
        """
        Converts the Penn Treebank tag to a WordNet POS tag, then uses that
        tag to perform much more accurate WordNet lemmatization.
        """
        tag = {
            'N': wn.NOUN,
            'V': wn.VERB,
            'R': wn.ADV,
            'J': wn.ADJ
        }.get(tag[0], wn.NOUN)

        return lemmatizer.lemmatize(token, tag)

cursor = None
db = None
try:
    config = DBConfig(working_dir+"/db.ini").read_db_config()
    # Open database connection
    db = MySQLdb.connect(**config)
    data = sql.read_sql(queryAnswers % question_id, db)
    cursor = db.cursor()
    vectorizer = TfidfVectorizer(tokenizer=process_text,
                                 stop_words=stopwords,
                                 max_df=max_df,
                                 min_df=min_df,
                                 use_idf=True,
                                 lowercase=True)

    docs = data['Value'].tolist()
    ids = data['ID'].tolist()

    tfidf_model = vectorizer.fit_transform(docs)

    km = KMeans(n_clusters=num_clusters, init='k-means++', max_iter=100000, n_init=1)
    km.fit(tfidf_model)

    clusters = km.labels_.tolist()

    # create main data frame
    frame = pd.DataFrame({'ids': ids, 'answers': docs, 'cluster': clusters}, index=[clusters], columns=['ids', 'answers', 'cluster'])

    order_centroids = km.cluster_centers_.argsort()[:, ::-1]

    terms = vectorizer.get_feature_names()

    for cluster_id in range(num_clusters):
        # check if we had values for cluster ...
        if cluster_id in frame.index.values:
            print("Cluster %d tags:" % cluster_id, end='')
            print()
            print()
            tags_values = []
            tags_ids = []
            # term per cluster
            # process all term till i get the max
            for term_idx in order_centroids[cluster_id]:
                tag = terms[term_idx]
                print(' %s' % tag, end=',')
                # remove non word characters from tag candidate
                tag = re.sub(r"[^\w\d]", "", tag)
                if len(tag) > min_tag_len:
                    cursor.execute(queryTag % (tag))
                    dbTag = cursor.fetchone()
                    id = None
                    if dbTag is None:
                        res = cursor.execute(queryInsertTag % (tag))
                        id = cursor.lastrowid
                    else:
                        id = dbTag[0]
                    tags_values.append(tag)
                    tags_ids.append(id)
                    if len(tags_values) == max_tags:
                        break

            frame_tags = pd.DataFrame({'ids': tags_ids, 'values': tags_values}, columns=['ids', 'values'])

            for answer_row_idx, answer_row in enumerate(frame.loc[cluster_id].values):
                print(answer_row)
                if hasattr(answer_row, "__iter__"):
                    answer_id = answer_row[0]
                else:
                    # if the cluster has only one row, then the values are the columns of the unique
                    # row and we are only interested on the first element (answer_id, answer_value, cluster)
                    answer_id = answer_row
                    if answer_row_idx > 0:
                        continue

                if delete_former_tags > 0:
                    cursor.execute(queryDeleteFormerTags % (answer_id))

                # insert labels to answers
                for tag_idx, tag in enumerate(frame_tags['values']):
                    tag_id = frame_tags['ids'].values[tag_idx]
                    cursor.execute(querySelectAnswerTag % (answer_id, tag_id))
                    existAnswerTag = cursor.fetchone();
                    if existAnswerTag is None:
                        cursor.execute(queryInsertAnswerTag % (answer_id, tag_id))
    db.commit()
except AttributeError, e:
    print()
    print(e)
    traceback.print_exc()
    raise
except IndexError, e2:
     print()
     print(e2)
     traceback.print_exc()
     raise
except Exception as e:
   print(e)
   # Rollback in case there is any error
   if not (db is None):
        db.rollback()
   print()
   print ("Unexpected error:", sys.exc_info()[0])
   raise
finally:
    # disconnect from server
    if not (db is None):
        db.close()
    if not (cursor is None):
        cursor.close()