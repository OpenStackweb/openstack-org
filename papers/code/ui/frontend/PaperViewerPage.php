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

class PaperViewerPage extends Page {

   static $db = [];

   static $has_one = [
        'Paper' => 'Paper',
   ];


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab('Root.Main', 'Content');
        if ($this->ID) {
            $fields->addFieldsToTab('Root.Main', $ddl_paper = new DropdownField('PaperID', 'Paper', Paper::get()->map('ID', 'Title')));
            $ddl_paper->setEmptyString('(None)');
        }
        return $fields;
    }

    public function renderSections(){
        $output = '';
        foreach($this->Paper()->getOrderedSections() as $section){
            $output .= $this->renderSection($section);
        }
        return $output;
    }

    public function renderSection($section){
        if($section instanceof CaseOfStudySection)
            return $section->renderWith('CasesOfStudy_Section');
        if($section instanceof IndexSection)
            return $section->renderWith('Index_Section');
        return $section->renderWith('Regular_Section');
    }
}

class PaperViewerPage_Controller extends Page_Controller {

    function init()
    {
        parent::init();

        Requirements::CSS('papers/css/paper-viewer-page.css');

        Requirements::javascript('themes/openstack/javascript/filetracking.jquery.js');
        Requirements::javascript('papers/javascript/paper-viewer-page.js');
    }

}