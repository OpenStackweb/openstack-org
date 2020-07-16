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
use Symfony\Component\Yaml\Yaml;
use mikehaertl\wkhtmlto\Pdf;

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

    public function renderSection($section, int $level = 3)
    {
        $output = '';

        if ($section instanceof CaseOfStudySection)
            return $section->renderWith('CasesOfStudy_Section');
        else if ($section instanceof IndexSection)
            $output = $section->renderWith('Index_Section');
        else {
            $output = $section->renderWith('Regular_Section', ['Level' => $level, 'SubLevel' => $level + 1]);
        }

        foreach ($section->getOrderedSubSections() as $subSection) {
            $output .= $this->renderSection($subSection, $level + 1);
        }

        return $output;
    }


    public function renderSectionPDF($section, int $level = 3)
    {
        $output = '';

        if ($section instanceof CaseOfStudySection)
            return $section->renderWith('CasesOfStudy_Section_PDF');
        else if ($section instanceof IndexSection)
            $output = $section->renderWith('Index_Section_PDF');
        else
            $output = $section->renderWith('Regular_Section_PDF', ['Level' => $level, 'SubLevel' => $level + 1]);

        foreach ($section->getOrderedSubSections() as $subSection) {
            $output .= $this->renderSection($subSection, $level + 1);
        }

        return $output;
    }

    public function toHTMLRender(){
        $paper = $this->Paper();
        $output = <<<HTML
        <h1>{$paper->Title}</h1>
        <h2>{$paper->Subtitle}</h2>
        <p>{$paper->Abstract}</p>
HTML;

        foreach ($paper->getOrderedSections() as $section) {
            $output .= $this->renderSectionPDF($section);
        }
        $translators = $paper->getTranslatorsByCurrentLocale();
        if($translators->count() > 0){
            $data = new ArrayData([
                'Translators' => $translators,
            ]);
            $output .= $data->renderWith('Translators_Section');
        }
        return $output;
    }
}

/**
 * Class PaperViewerPage_Controller
 */
class PaperViewerPage_Controller extends Page_Controller
{
    private static $url_handlers = [
        'GET pdf' => 'getPDFRender',
    ];

    private static $allowed_actions = [
        'getPDFRender',
    ];

    /**
     * needs
     * sudo apt install xvfb
     * sudo apt install wkhtmltopdf
     * https://github.com/mikehaertl/phpwkhtmltopdf
     * https://github.com/wkhtmltopdf/wkhtmltopdf/issues/2037
     * https://help.accusoft.com/PrizmDoc/v12.2/HTML/Installing_Asian_Fonts_on_Ubuntu_and_Debian.html
     */
    public function getPDFRender(){

        $css = file_get_contents(Director::getAbsFile("papers/css/paper-viewer-page.css"));

        $html = "<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<style>"
.$css.
"</style>
<body>".$this->toHTMLRender()."</body></html>";


        // execute conversion

        $options = [
            'encoding' => 'utf-8',  // option with argument
            'ignoreWarnings' => true,
            // Explicitly tell wkhtmltopdf that we're using an X environment
            //'use-xserver',
            // Enable built in Xvfb support in the command
            'commandOptions' => [
                'enableXvfb' => true,
                'useExec' => true,
                // Optional: Set your path to xvfb-run. Default is just 'xvfb-run'.
                'xvfbRunBinary' => '/usr/bin/xvfb-run',

                // Optional: Set options for xfvb-run. The following defaults are used.
                // 'xvfbRunOptions' =>  '--server-args="-screen 0, 1024x768x24"',
            ],
        ];

        $pdf = new Pdf($html);
        $pdf->setOptions($options);

        $filename = $this->Paper()->Title.".pdf";

        if (!$pdf->send($filename)) {
            SS_Log::log($pdf->getError(), SS_Log::ERR);
            $response = new SS_HTTPResponse();
            $response->setStatusCode(500);
            $response->setBody($pdf->getError());
            return $response;
        }
    }

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