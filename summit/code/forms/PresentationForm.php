<?php

/**
 * Class PresentationForm
 */
final class PresentationForm extends BootstrapForm
{
    /**
     * @var IPresentationManager
     */
    private $presentation_manager;

    /**
     * @var ISummit
     */
    private $summit;

    /**
     * @var IPresentation
     */
    private $presentation;

    /**
     * PresentationForm constructor.
     * @param Controller $controller
     * @param string $name
     * @param FieldList $actions
     * @param ISummit $summit
     * @param IPresentationManager $presentation_manager
     * @param IPresentation $presentation
     */
    public function __construct($controller, $name, $actions, ISummit $summit, IPresentationManager $presentation_manager, IPresentation $presentation) {

        $this->presentation_manager = $presentation_manager;
        $this->summit               = $summit;
        $this->presentation         = $presentation;
        $this->setTemplate('PresentationForm');

        JQueryValidateDependencies::renderRequirements(true,false);
        Requirements::javascript('summit/javascript/presentation-form.js');
        Requirements::javascript('summit/javascript/presentation-form-save-actions.js');
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');

        parent::__construct(
            $controller,
            $name,
            $this->getPresentationFields(),
            $actions,
            $this->getPresentationValidator()
         );
    }

    protected function getPresentationFields() {

        $private_groups = $this->presentation_manager->getPrivateCategoryGroupsFor(Member::currentUser(), $this->summit);
        if ($this->summit->isCallForSpeakersOpen()) {
            $public_groups = $this->summit->getOpenSelectionPlanForStage('Submission')->getPublicCategoryGroups();
            $category_groups = array_merge($public_groups,$private_groups);
        } else {
            $category_groups = $private_groups;
        }

        $category_groups_map = array();
        foreach ($category_groups as $group) {
            $group_type = ($group->ClassName == 'PrivatePresentationCategoryGroup') ? 'private' : 'public';

            if (!$group->Categories()->count()) continue;
            if (!$group->hasCategoryVisible() && $group_type == 'public') continue;

            $category_groups_map[] = array('id' => $group->ID,'title' => $group->Name, 'group_type' => $group_type);
        }

        usort($category_groups_map, function($a, $b) { return strcmp($a["title"], $b["title"]); });
        $types = PresentationType::get()
                    ->filter('SummitID', $this->summit->ID)
                    ->exclude('Type', [IPresentationType::Keynotes, IPresentationType::LightingTalks]);
        
        $instructions = '(';
        foreach($types as $type){
            $instructions .= $type->Type;
            if(intval($type->MaxSpeakers > 0))
                $instructions .= sprintf(" Max %s speakers", $type->MaxSpeakers);

            if(intval($type->MaxModerators > 0))
                $instructions .= sprintf(" %s moderator", $type->MaxModerators);

            $instructions .= '; ';
        }
        $instructions .= ')';
        $fields = FieldList::create()
            ->text('Title', 'Proposed Presentation Title')
                ->configure()
                    ->setAttribute('autofocus','TRUE')
                ->end()
            ->literal('TypeIDHelp','<label>Select the format</label> <br>'.$instructions)
            ->dropdown('TypeID','')
                ->configure()
                    ->setEmptyString('-- Select one --')
                    ->setSource($types->map('ID', 'Type'))
                ->end()
            ->literal('CategoryContainer','<div id="category_options"></div>')
            ->dropdown('Level','Select the technical level of your presentation content')
                ->configure()
                    ->setEmptyString('-- Select one --')
                    ->setSource(Presentation::create()->dbObject('Level')->enumValues())
                ->end()
            ->tinyMCEEditor('Abstract','Abstract (1000 chars)')
                ->configure()
                    ->setRows(20)
                    ->setColumns(8)
                ->end()
            ->literal('SocialSummaryHelp','<hr/><p>Used for social sharing and YouTube description.</p>')
            ->textArea('SocialSummary', 'Social Summary (100 chars)')
                ->configure()
                    ->setRows(10)
                    ->setColumns(8)
                ->end()
            ->tinyMCEEditor('AttendeesExpectedLearnt','What should attendees expect to learn? (1000 chars)')
                ->configure()
                    ->setRows(20)
                    ->setColumns(8)
                ->end()
            ->optionset('AttendingMedia',
                'Are you available to discuss the topic of this presentation with attending media?',
                array(
                    1 => 'Yes',
                    0 => 'No'
                )
            )
            ->configure()
            ->setTemplate('BootstrapAwesomeOptionsetField')
            ->setInline(true)
            ->end()
            ->literal('PresentationMaterialsTitle','<h3>Please provide any relevant links to additional information, such as code repositories, case studies, papers, blog posts etc. (Up to 5 links)</h3>')
            ->text('PresentationLink[1]','#1')
            ->text('PresentationLink[2]','#2')
            ->text('PresentationLink[3]','#3')
            ->text('PresentationLink[4]','#4')
            ->text('PresentationLink[5]','#5')
            ->hidden('ID','ID')
            ->hidden('SummitID','',$this->summit->ID)
            ->hidden('CategoryIDbis','')
            ->hidden('Continue','',1)
                ->configure()
                    ->addExtraClass('continue_field')
                ->end()
            ->literal('EndHr','<hr>');

        $CategoryGroupField = new CategoryGroupField('GroupID','Select the <a href="'.$this->summit->Link.'categories" target="_blank">Summit Category</a> of your presentation');
        $CategoryGroupField->setSource($category_groups_map);
        if(count($category_groups_map) < 2) {
            $CategoryGroupField->setValue($category_groups_map[0]['id']);
            $CategoryGroupField->addHolderClass('hidden');
        }
        $fields->insertAfter($CategoryGroupField,'TypeID');

        return $fields;
    }

    protected function getPresentationValidator()
    {
        return RequiredFields::create('Title','Level');
    }

    public function loadDataFrom($data, $mergeStrategy = 0, $fieldList = null)
    {
        parent::loadDataFrom($data, $mergeStrategy, $fieldList);
        if (!$data instanceof Presentation) {
            return;
        }

        $presentation = $data;

        $this->fields->fieldByName('Continue')->setValue(1);

        if ($presentation->Category()->ID) {
            $group = $presentation->Category()->getCategoryGroups()->first();
            $this->fields->fieldByName('GroupID')->setValue($group->ID);
            $this->fields->fieldByName('CategoryIDbis')->setValue($presentation->Category()->ID);
        }

        foreach ($presentation->Materials()->filter('ClassName', 'PresentationLink') as $key => $link)
        {
            if ($key > 4) break;
            $this->fields->fieldByName('PresentationLink['.($key+1).']')->setValue($link->Link);
        }

        return $this;
    }

    public function saveInto(DataObjectInterface $dataObject, $fieldList = null)
    {
        parent::saveInto($dataObject, $fieldList);

        if (!$dataObject instanceof Presentation) {
            return;
        }

        $presentation = $dataObject;

        $old_materials = $presentation->Materials()->filter('ClassName', 'PresentationLink');
        foreach($old_materials as $o) $o->Delete();

        for($i = 1 ; $i <= 5 ; $i++ ){
            $field = $this->fields->fieldByName("PresentationLink[{$i}]");
            if(is_null($field)) continue;
            $val = $field->Value();
            if(empty($val)) continue;
            $presentation = PresentationLink::create(['Name' => trim($val), 'Link' => trim($val)]);
            $presentation->Materials()->add($presentation);
        }

        $extra_questions = ($presentation->Category()->Exists()) ? $presentation->Category()->ExtraQuestions() : array();
        foreach ($extra_questions as $question) {
            $field = $this->fields->fieldByName($question->Name);
            if(is_null($field)) continue;
            $answer_value = $field->Value();
            if(empty($answer_value)) continue;

            if (!$answer = $presentation->findAnswerByQuestion($question)) {
                $answer = new TrackAnswer();
            }

            if(is_array($answer_value) ){
                $answer_value = str_replace('{comma}', ',', $answer_value);
                $answer->Value = implode(',', $answer_value);
            }
            else{
                $answer->Value = $answer_value;
            }
            $answer->QuestionID = $question->getIdentifier();
            $answer->write();

            $presentation->ExtraAnswers()->add($answer);
        }
    }

    public function forTemplate() {
        parent::forTemplate();

        $return = $this->renderWith(['PresentationForm']);

        // Now that we're rendered, clear message
        $this->clearMessage();

        return $return;
    }

}
