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

class SurveyCheckboxSetField extends CustomCheckboxSetField {

    /**
     * @var bool
     */
    private $required;

    /**
     * @var bool
     */
    private $visible;

    /**
     * @var IMultiValueQuestionTemplate
     */
    private $question;

    public function setRequired(){
        $this->required = true;
    }

    public function isRequired(){
        return  $this->required;
    }


    public function setVisible($visible){
        $this->visible = $visible;
    }

    public function isVisible(){
        return  $this->visible;
    }

    /**
     * SurveyCheckboxSetField constructor.
     * @param string $name
     * @param null $title
     * @param array $source
     * @param string $value
     * @param null $form
     * @param null $emptyString
     * @param IMultiValueQuestionTemplate $question
     */
    public function __construct
    (
        $name,
        $title=null,
        $source=[],
        $value='',
        $form=null,
        $emptyString=null,
        IMultiValueQuestionTemplate $question
    )
    {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
        $this->visible  = true;
        $this->question = $question;
    }

    public function addExtraClass($class) {
        if($class === 'hidden') $this->setVisible(false);
        return parent::addExtraClass($class);
    }


    /**
     * @return array
     */
    protected function buildExtraProperties(){
        $groups  = [];
        $options = $this->buildOptions();

        if($this->question instanceof SurveyCheckBoxListQuestionTemplate){
            if($this->question->Groups()->count() > 0) {
                $options_dic = [];
                foreach ($options as $opt){
                    $options_dic[$opt->Value] = $opt;
                }

                foreach ($this->question->Groups()->sort("Order", "ASC" ) as $group) {

                    $group_options = new ArrayList();
                    foreach ($group->Values()->sort("Order", "ASC" ) as $val) {
                        if(isset($options_dic[$val->ID])) {
                            $group_options->add($options_dic[$val->ID]);
                            unset($options_dic[$val->ID]);
                        }
                    }

                    $groups[] = new ArrayData([
                        'Slug'    => preg_replace("/[^A-Za-z0-9 ]/", '_', strtolower(strip_tags($group->Label))),
                        'Label'   => GetTextTemplateHelpers::_t("survey_template", $group->Label),
                        'Options' => $group_options
                    ]);
                }

                $ungrouped_options = new ArrayList();
                foreach($options_dic as $id => $opt){
                    $ungrouped_options->add($opt);
                }

                if($ungrouped_options->count() > 0)
                    $groups[] = new ArrayData([
                        'Slug'    => preg_replace("/[^A-Za-z0-9 ]/", '_', strtolower(strip_tags($this->question->getDefaultGroupLabel()))),
                        'Label'   => GetTextTemplateHelpers::_t("survey_template", $this->question->getDefaultGroupLabel()),
                        'Options' => $ungrouped_options
                    ]);

                return ['Groups' => new ArrayList($groups)];
            }
        }
        return ['Options' => new ArrayList($options)];
    }

    /**
     * @return bool
     */
    public function isMobileClient(){
        $mobile_detect = new Mobile_Detect();
        return $mobile_detect->isMobile();
    }
}