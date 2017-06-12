<?php

/**
 * Copyright 2016 OpenStack Foundation
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
final class SurveyDatabaseContentPOTFileGeneratorTask extends BuildTask {

    const POT_FILE_ENTRY_TPL = <<<POT_FILE_ENTRY_TPL
# %s
msgid "%s"
msgstr ""


POT_FILE_ENTRY_TPL;

    public function run ($request) {


        if (!isset($_GET['survey_template_id']))
        {
            echo "you must provide a survey_template_id!".PHP_EOL;
            return -1;
        }

        $survey_template = SurveyTemplate::get()->byID(intval($_GET['survey_template_id']));

        if(is_null($survey_template))
        {
            echo "survey template does not exists".PHP_EOL;
            return -1;
        }

        $pot_file     = '';
        $pot_file_dic = [];
        $pot_entries  = [];

        self::createPOTDictionary($survey_template, $pot_file_dic, $pot_entries);
        foreach($survey_template->EntitySurveys() as $entity_survey){
            self::createPOTDictionary($entity_survey, $pot_file_dic, $pot_entries);
        }

        foreach ($pot_entries as $msgid){
            $pot_file .= sprintf(self::POT_FILE_ENTRY_TPL, $pot_file_dic[$msgid] , $msgid);
        }

        file_put_contents(sprintf("/tmp/survey_template_%s.pot", $survey_template->ID), $pot_file);

    }

    private static function addEntryToPOT(array &$pot_file_dic = [],  array &$pot_file_entries = [], $msgid, $comment){
        $msgid = trim($msgid);
        $msgid = str_replace("\'","'", $msgid);
        $msgid = str_replace("\r\n", '', $msgid);
        if(!isset($pot_file_dic[$msgid])){
            $pot_file_dic[$msgid] = $comment;
            $pot_file_entries[]   = $msgid;
        }
        else{
            $pot_file_dic[$msgid] = sprintf("%s, %s", $pot_file_dic[$msgid], $comment);
        }
    }

    private static function createPOTDictionary($survey_template, array &$pot_file_dic = [],  array &$pot_file_entries = []){

        self::addEntryToPOT($pot_file_dic, $pot_file_entries, $survey_template->Title, sprintf("survey template %s title", $survey_template->ID));

        foreach($survey_template->getSteps() as $step){

            if(!empty($step->Content)) {

                self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($step->Content), sprintf("survey template %s - survey step %s content", $survey_template->ID, $step->Title));
            }

            self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($step->FriendlyName), sprintf("survey template %s - survey step %s friendly name", $survey_template->ID, $step->Title));

            if($step->getType() == 'SurveyRegularStepTemplate'){
                foreach ($step->getQuestions() as $question){

                    if(!empty($question->Label)) {
                        self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($question->Label), sprintf("step %s - question %s label", $step->Title, $question->Name));
                    }
                    // question by type

                    switch($question->Type()){
                        case "ComboBox":
                        case "RadioButtonList":
                        case "CheckBoxList":
                        case "Ranking":

                            if(!empty($question->EmptyString)) {
                                self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($question->EmptyString), sprintf("question %s empty string", $question->Name));
                            }

                            if($question->DefaultValueID > 0) {
                                self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($question->DefaultValue()->Label), sprintf("question %s default value", $question->Name));
                            }

                            if($question->Type() == 'ComboBox' && $question->isCountrySelector()) continue;

                            foreach ($question->getValues() as $value){
                                $key                = !empty($value->Label) ? addslashes($value->Label) : addslashes($value->Value);
                                self::addEntryToPOT($pot_file_dic, $pot_file_entries, $key, sprintf("question %s value %s label", $question->Name, $value->Value));
                            }

                            if($question->Type() == 'Ranking'){
                                self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($question->Intro), sprintf("question %s intro", $question->Name));
                            }

                            break;
                        case "Literal":
                            self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($question->Content), sprintf("question %s literal content", $question->Name));
                            break;
                        case "RadioButtonMatrix":
                            self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($question->RowsLabel), sprintf("question %s rows label", $question->Name));
                            self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($question->AdditionalRowsLabel), sprintf("question %s rows additional label", $question->Name));
                            self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($question->AdditionalRowsDescription), sprintf("question %s rows description", $question->Name));

                            foreach($question->Columns() as $col){
                                $key                = !empty($col->Label) ? addslashes($col->Label) : addslashes($col->Value);
                                self::addEntryToPOT($pot_file_dic, $pot_file_entries, $key, sprintf("question %s col %s label", $question->Name, $col->Value));
                            }

                            foreach($question->Rows() as $row){
                                $key                = !empty($row->Label) ? addslashes($row->Label) : addslashes($row->Value);
                                self::addEntryToPOT($pot_file_dic, $pot_file_entries, $key, sprintf("question %s row %s label", $question->Name, $row->Value));
                            }
                            break;
                    }
                }
            }

            if($step->getType() == 'SurveyDynamicEntityStepTemplate'){

                if(!empty($step->AddEntityText)) {
                    self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($step->AddEntityText), sprintf("survey step %s AddEntityText", $step->Title));
                }

                if(!empty($step->DeleteEntityText)) {
                    self::addEntryToPOT($pot_file_dic, $pot_file_entries, addslashes($step->DeleteEntityText), sprintf("survey step %s DeleteEntityText", $step->Title));
                }

                if(!empty($step->EditEntityText)) {
                    self::addEntryToPOT($pot_file_dic, $pot_file_entries,addslashes($step->EditEntityText), sprintf("survey step %s EditEntityText", $step->Title));
                }
            }
        }
        return $pot_file_dic;
    }
}