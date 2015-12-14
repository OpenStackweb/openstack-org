<?php

/**
 * Class PresentationForm
 */
final class PresentationForm extends BootstrapForm
{

    public function __construct($controller, $name, $actions) {
        parent::__construct(
            $controller, 
            $name, 
            $this->getPresentationFields(),
            $actions,
            $this->getPresentationValidator()
        );
    }

    protected function getPresentationFields() {
        $categorySource = Summit::get_active()->Categories()->map('ID','FormattedTitleAndDescription')->toArray();
        $categorySource['other'] = '<h4 class="category-label">Other topic...</h4>';

        $fields = FieldList::create()
            ->text('Title', 'Proposed Presentation Title')
                ->configure()
                    ->setAttribute('autofocus','TRUE')
                ->end()
            ->dropdown('Level','Please select the level of your presentation content')
                ->configure()
                    ->setEmptyString('-- Select one --')
                    ->setSource(Presentation::create()->dbObject('Level')->enumValues())
                ->end()
            ->literal('AbstractHelp','<hr/><p>YouTube and other services limit the length of your presentation\'s description. Please provide a shorter, YouTube-friendly summary below.</p>')
            ->tinyMCEEditor('ShortDescription','Abstract (1000 chars)')
                ->configure()
                    ->setRows(20)
                    ->setColumns(8)
                    ->setMaxCharLimit(1000)
                ->end()
            ->tinyMCEEditor('ProblemAddressed','What is the problem or use case you’re addressing in this session? (1000 chars)')
                ->configure()
                    ->setRows(20)
                    ->setColumns(8)
                    ->setMaxCharLimit(1000)
                ->end()
            ->tinyMCEEditor('AttendeesExpectedLearnt','What should attendees expect to learn? (1000 chars)')
                ->configure()
                    ->setRows(20)
                    ->setColumns(8)
                    ->setMaxCharLimit(1000)
                ->end()
            ->tinyMCEEditor('SelectionMotive','Why should this session be selected? (1000 chars)')
                ->configure()
                    ->setRows(20)
                    ->setColumns(8)
                    ->setMaxCharLimit(1000)
                ->end()
            ->literal('PresentationMaterialsTitle','<h3>Please provide any relevant links to additional information, such as code repositories, case studies, papers, blog posts etc. (Up to 5 links)</h3>')
            ->text('PresentationLink[1]','#1')
            ->text('PresentationLink[2]','#2')
            ->text('PresentationLink[3]','#3')
            ->text('PresentationLink[4]','#4')
            ->text('PresentationLink[5]','#5')
            ->literal('HR','<hr/>')            
            ->optionset(
                'CategoryID',
                'What is the general topic of the presentation?'                
            )
                ->configure()
                    ->setSource($categorySource)
                ->end()
            ->text('OtherTopic','Other topic (if one above does not match)')
                ->configure()
                    ->displayIf('CategoryID')->isEqualTo('other')->end()
                ->end()
            ->literal('TagHelp','<p>You can optionally add tags help attendees find presentations that interest them. Examples: <i>nova, ubuntu, ldap.</i></p>')
            ->bootstrapTag('Tags','Presentation Tags (Optional)')
                ->configure()
                    ->setLabelField('Tag')
                    ->setSource(Tag::get())
                    ->setPrefetch(
                        Tag::get()
                            ->leftJoin('SummitEvent_Tags', 'TagID = Tag.ID')
                            ->sort('COUNT(Tag.ID)','DESC')
                            ->limit(10)
                            ->alterDataQuery(function($query) {
                                $query->groupby('Tag.ID');
                            })
                    )
                    ->setFreeInput(true)
                ->end();
        
        return $fields;

    }


    protected function getPresentationValidator() {
        return RequiredFields::create('Title','Level');
    }

    public function loadDataFrom($data, $mergeStrategy = 0, $fieldList = null)
    {
        parent::loadDataFrom($data, $mergeStrategy, $fieldList);
        if (!$data instanceof Presentation) {
            return;
        }

        $presentation = $data;

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
            $presentation->Materials()->add( PresentationLink::create(array('Link' => trim($val))));
        }
    }


}