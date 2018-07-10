<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class PaperContentPOTFileGeneratorTask
 */
final class PaperContentPOTFileGeneratorTask extends BuildTask {

    public function run ($request) {

        if (!isset($_GET['paper_id']))
        {
            echo "you must provide a paper_id!".PHP_EOL;
            return -1;
        }

        $paper = Paper::get()->byID(intval($_GET['paper_id']));

        if(is_null($paper))
        {
            echo "paper does not exists".PHP_EOL;
            return -1;
        }
        $generator = new POTFileGenerator();

        self::createPOTDictionary($paper, $generator);

        file_put_contents(sprintf("%s/papers/pot/%s.pot", Director::baseFolder(), $paper->getI18nContext()), $generator->generateFileContent());
    }


    private static function createPOTDictionary(Paper $paper, POTFileGenerator $generator){

        $generator->addEntry($paper->Title, sprintf("paper %s title", $paper->ID));
        if(!empty($paper->Subtitle))
            $generator->addEntry($paper->Subtitle, sprintf("paper %s Subtitle", $paper->ID));

        if(!empty($paper->Abstract))
            $generator->addEntry($paper->Abstract, sprintf("paper %s Abstract", $paper->ID));

        if(!empty($paper->Footer))
            $generator->addEntry($paper->Footer, sprintf("paper %s Footer", $paper->ID));

        foreach($paper->getOrderedSections() as $section){

            $generator->addEntry(addslashes($section->Title), sprintf("paper %s - section %s title", $paper->ID, $section->ID));

            if(!empty($section->Subtitle)) {
                $generator->addEntry( addslashes($section->Subtitle), sprintf("paper %s - section %s subtitle", $paper->ID, $section->ID));
            }

            foreach ($section->getOrderedContents() as $content) {
                $generator->addEntry(addslashes($content->Content), sprintf("paper %s - section %s paragraph %s", $paper->ID, $section->ID, $content->ID));
            }

            if($section instanceof CaseOfStudySection){
                foreach ($section->CasesOfStudy() as $cs) {
                    $generator->addEntry(addslashes($cs->Title), sprintf("paper %s - section %s case of study %s title", $paper->ID, $section->ID, $cs->ID));

                    foreach ($cs->Contents() as $cs_content) {
                        $generator->addEntry(addslashes($cs_content->Content), sprintf("paper %s - section %s case of study %s paragraph %s", $paper->ID, $section->ID, $cs->ID, $cs_content->ID));
                    }
                }
            }


            if($section instanceof IndexSection){
                foreach ($section->Items() as $i) {
                    $generator->addEntry(addslashes($i->Title), sprintf("paper %s - section %s item %s title", $paper->ID, $section->ID, $i->ID));

                    if(!empty($i->Content))
                        $generator->addEntry(addslashes($i->Content), printf("paper %s - section %s item %s content", $paper->ID, $section->ID, $i->ID));
                }
            }
        }

    }
}