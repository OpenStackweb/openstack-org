<?php

class SummitPageSectionSettings extends DataObject{
    private static $db = array (
        'Name' => 'Varchar(100)',
        'Order' => 'Int',
    );

    private static $has_one = array(
        'SummitPage' => 'SummitPage',
        'BackgroundImage' => 'BetterImage',
        'BackgroundColor' => 'SummitColor',
    );

    private static $summary_fields = array (
        'Name' => 'Name'
    );

    private static $default_sort = "Order";

    public function getCMSFields()
    {
        $fields =  FieldList::create(TabSet::create('Root'));

        $fields->addFieldsToTab('Root.Main', new LiteralField('BackgroundMessage', "<span>Please set background image or color. If both are set, background image take precedence over color</span>"));
        $fields->addFieldsToTab("Root.Main", $backgroundImage = new UploadField('BackgroundImage','Background Image'));

        $backgroundImage->setFolderName('summits/sectionssettings');
        $backgroundImage->setAllowedMaxFileNumber(1);
        $backgroundImage->setAllowedFileCategories('image');
        $backgroundImage->setOverwriteWarning(false);
        $backgroundImage->getUpload()->setReplaceFile(true);

        $fields->addFieldsToTab('Root.Main', new LiteralField('BackgroundColorSectionOpen', "<div class='field'><label class='left'>Background Color</label>"));
        $fields->addFieldsToTab('Root.Main', new LiteralField('BackgroundColorClear1', "<div style='clear:both'></div>"));
        $colors = array();
        foreach ($this->SummitPage()->Summit()->Colors() as $color) {
            $colors[$color->ID] = "#".$color->Color;
            $fields->addFieldsToTab('Root.Main', new LiteralField($color->Color, "<div class='color-preview'><div style='background-color:#{$color->Color};'></div><span>#{$color->Color}</span></div>"));
        }
        $fields->addFieldsToTab('Root.Main', new LiteralField('BackgroundColorClear2', "<div style='clear:both'></div>"));

        $fields->addFieldsToTab('Root.Main', $backgroundColor = new DropdownField('BackgroundColorID', '', $colors));
        $backgroundColor->setHasEmptyDefault(true);
        $fields->addFieldsToTab('Root.Main', new LiteralField('BackgroundColorSectionClose', "</div>"));

        return $fields;
    }
}