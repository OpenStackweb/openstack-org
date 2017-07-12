#!/usr/bin/env python

# Licensed under the Apache License, Version 2.0 (the "License"); you may
# not use this file except in compliance with the License. You may obtain
# a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
# WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
# License for the specific language governing permissions and limitations
# under the License.

import os
import requests


POFILE_URL = "https://translate.openstack.org/rest/file/translation/"\
    "openstack-user-survey/openstack-user-survey/{lang_zanata}"\
    "/po?docId={doc_id}"
POFILE = "{lang_survey}/LC_MESSAGES/{doc_id}.po"

SURVEY_TEMPLATE = "survey_template"
SURVEY_UI = "survey_ui"

LANGUAGES = {
    "de": "de_DE",
    "id": "id_ID",
    "ja": "ja_JP",
    "ko-KR": "ko_KR",
    "zh-CN": "zh_CN",
    "zh-TW": "zh_TW",
}


def download_po(lang_zanata, lang_survey, doc_id):
    pofile = POFILE.format(lang_survey=lang_survey,
                           doc_id=SURVEY_TEMPLATE)
    pofile_dir = os.path.dirname(pofile)
    if not os.path.exists(pofile_dir):
        os.makedirs(pofile_dir)

    new_po = requests.get((POFILE_URL).format(
        lang_zanata=lang_zanata,
        doc_id=SURVEY_TEMPLATE))

    # Ensure to use UTF-8 encoding
    new_po.encoding = 'utf-8'

    with open(pofile, 'w+') as f:
        f.write(new_po.text.encode('utf-8'))

    print("Successfully downloaded for %s - %s" % (lang_zanata, doc_id))

for lang_zanata, lang_survey in LANGUAGES.items():
    download_po(lang_zanata, lang_survey, SURVEY_TEMPLATE)
    download_po(lang_zanata, lang_survey, SURVEY_UI)
