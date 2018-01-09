import string

from HTMLParser import HTMLParser
from sklearn.base import TransformerMixin

class StripHTMLTransformer(TransformerMixin):

    def transform(self, X, **transform_params):
        return [
            self.stripHtmlOff(doc) for doc in X
        ]

    def stripHtmlOff(self, document):
        if document == '':
            return document
        s = HTMLStripper()
        s.feed(document)
        strip = s.get_data()
        return strip

    def fit(self, X, y=None, **fit_params):
        return self

class HTMLStripper(HTMLParser):
    def __init__(self):
        self.reset()
        self.fed = []

    def handle_data(self, d):
        self.fed.append(d)

    def get_data(self):
        return ''.join(self.fed)
