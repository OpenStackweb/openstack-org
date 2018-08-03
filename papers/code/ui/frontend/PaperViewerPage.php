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
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class PaperViewerPage extends Page
{

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

    public function renderSections()
    {
        $output = '';
        foreach ($this->Paper()->getOrderedSections() as $section) {
            $output .= $this->renderSection($section);
        }
        $translators = $this->Paper()->getTranslatorsByCurrentLocale();
        if($translators->count() > 0){
            $data = new ArrayData([
                'Translators' => $translators,
            ]);
            $output .= $data->renderWith('Translators_Section');
        }
        return $output;
    }

    public function renderSection($section)
    {
        $output = '';

        if ($section instanceof CaseOfStudySection)
            return $section->renderWith('CasesOfStudy_Section');
        else if ($section instanceof IndexSection)
            $output = $section->renderWith('Index_Section');
        else
            $output = $section->renderWith('Regular_Section');

        foreach ($section->getOrderedSubSections() as $subSection) {
            $output .= $this->renderSection($subSection);
        }

        return $output;
    }
}

class PaperViewerPage_Controller extends Page_Controller
{

    function init()
    {
        parent::init();

        Requirements::CSS('papers/css/paper-viewer-page.css');

        Requirements::javascript('themes/openstack/javascript/filetracking.jquery.js');
        Requirements::javascript('papers/javascript/paper-viewer-page.js');
    }

    function getAvailableLanguages(){
        $paper = $this->Paper();
        $available_langs = [];
        $path =sprintf("%s/%s/_config/translations.yml", Director::baseFolder(), "papers");
        $yaml = Yaml::parse(file_get_contents($path));
        if(!is_null($yaml) && count($yaml))
        {
            foreach($yaml as $project_id => $info){
                $id = intval($info['id']);
                if($paper->ID != $id) continue;

                $po_files = $info['po_files'];
                foreach ($po_files as $po_file){
                    foreach ($po_file as $doc_id => $languages) {
                        foreach($languages as $language) {
                            $available_langs[] = $language['lang_local'];
                        }
                    }
                }

            }
        }

        $res = '';

        foreach($available_langs as $lang){
            if(!empty($res)) $res .=', ';
            $res .= sprintf("'%s'", $lang);
        }

        return sprintf("[%s]", $res);
    }

}