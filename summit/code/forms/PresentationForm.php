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

        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript('themes/openstack/javascript/jquery.persistable-form.js');
        Requirements::javascript('summit/javascript/presentation-form.js');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');


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
            $public_groups = $this->summit->CategoryGroups()->filter('ClassName', 'PresentationCategoryGroup')->toArray();
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

        $fields = FieldList::create()
            ->text('Title', 'Proposed Presentation Title')
                ->configure()
                    ->setAttribute('autofocus','TRUE')
                ->end()
            ->dropdown('TypeID','Select the format (Presentation: Max 3 speakers; Panel: Max 4 speakers, 1 moderator)')
                ->configure()
                    ->setEmptyString('-- Select one --')
                    ->setSource(PresentationType::get()->filter('SummitID',$this->summit->ID)->exclude('Type','Keynotes')->map('ID', 'Type'))
                ->end()
            ->literal('CategoryContainer','<div id="category_options"></div>')
            ->dropdown('Level','Select the technical level of your presentation content')
                ->configure()
                    ->setEmptyString('-- Select one --')
                    ->setSource(Presentation::create()->dbObject('Level')->enumValues())
                ->end()
            ->optionset('FeatureCloud','Does this talk feature an OpenStack cloud?', array( 1 => 'Yes', 0 => 'No' ))
                ->configure()
                    ->setTemplate('BootstrapAwesomeOptionsetField')
                    ->setInline(true)
                ->end()
            ->optionset('LightningTalk',
                'Would you be willing to present your presentation/panel as a Lightning Talk in the event your submission is not chosen?',
                array(
                    1 => 'Yes',
                    0 => 'No'
                )
            )
                ->configure()
                    ->setTemplate('BootstrapAwesomeOptionsetField')
                    ->setInline(true)
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
            ->literal('PresentationMaterialsTitle','<h3>Please provide any relevant links to additional information, such as code repositories, case studies, papers, blog posts etc. (Up to 5 links)</h3>')
            ->text('PresentationLink[1]','#1')
            ->text('PresentationLink[2]','#2')
            ->text('PresentationLink[3]','#3')
            ->text('PresentationLink[4]','#4')
            ->text('PresentationLink[5]','#5')
            ->hidden('ID','ID')
            ->hidden('SummitID','',$this->summit->ID)
            ->hidden('CategoryIDbis','');

        $CategoryGroupField = new CategoryGroupField('GroupID','Select the <a href="'.$this->summit->Link.'categories" target="_blank">Summit Category</a> of your presentation');
        $CategoryGroupField->setSource($category_groups_map);
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

}
