<?php


class PresentationForm extends BootstrapForm
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
            ->tinyMCEEditor('Description','Abstract')
                ->configure()
                    ->setRows(20)
                ->end()
            ->literal('ShortDescriptionHelp','<hr/><p>YouTube and other services limit the length of your presentation\'s description. Please provide a shorter, YouTube-friendly summary below.</p>')
            ->literal('ShortDescriptionWordCount','<p id="word-count"></p>')
            ->tinyMCEEditor('ShortDescription','Short Description (450 Word Max)')
                ->configure()
                    ->setRows(7)
                    ->setWordCount(450)
                ->end()
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


}