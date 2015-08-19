<?php
/**
 * Copyright 2015 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class SurveyRankingQuestionTemplate
 */
class SurveyRankingQuestionTemplate
    extends SurveyMultiValueQuestionTemplate
    implements ISurveyRankableQuestion {

    static $db = array(
        'MaxItemsToRank' => 'Int',
        'Intro'          => 'HTMLText',
    );

    private static $defaults = array(
        'MaxItemsToRank' => 5,
    );

    public function Type(){
        return 'Ranking';
    }

    public function getCMSFields() {

        $fields = parent::getCMSFields();

        $fields->removeByName('EmptyString');
        $fields->removeByName('DefaultValueID');

        $fields->add(new NumericField('MaxItemsToRank', 'Max. Items To Rank'));
        $fields->add(new HtmlEditorField('Intro', 'Intro Text'));

        return $fields;
    }
}

