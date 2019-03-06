<?php
/**
 * Copyright 2018 Openstack Foundation
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
 * Class TrackTagGroup
 */
final class TrackTagGroup extends DataObject implements ITagGroup
{
    static $db = [
        'Name'      => 'Varchar',
        'Label'     => 'Varchar',
        'Order'     => 'Int',
        'Mandatory' => 'Boolean(0)'
    ];

    private static $has_one = [
        'Summit' => 'Summit',
    ];

    private static $many_many = [
        'AllowedTags' => 'Tag'
    ];

    private static $many_many_extraFields = [
        'AllowedTags' => [
            'IsDefault' => "Boolean",
        ],
    ];

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getCMSFields() {

        $fields = new FieldList();
        $fields->add(new LiteralField('namelabel', 'Name is the label used in CFP, use only lowercase'));
        $fields->add(new TextField('Name', 'Name (lowercase)'));
        $fields->add(new TextField('Label'));
        $fields->add(new HiddenField('SummitID','SummitID'));
        if($this->ID > 0) {

            $config = GridFieldConfig_RelationEditor::create(100);
            $config->removeComponentsByType('GridFieldDataColumns');
            $config->removeComponentsByType('GridFieldDetailForm');
            $config->removeComponentsByType('GridFieldDeleteAction');
            $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
            $config->addComponent(new GridFieldUnSeedAllowedTagOnAllSummitTracksColumnAction);
            //$config->addComponent(new GridFieldSeedAllowedTagOnAllSummitTracksColumnAction);
            $config->addComponent($completer = new GridFieldAddExistingTag, 'GridFieldAddNewButton');
            $completer->setResultsFormat('$Tag');
            $completer->setSearchFields(['Tag']);
            // only can assign tags that arent already assigned to another group or itself
            $completer->setSearchList(Tag::get()->where(sprintf('ID NOT IN (
            SELECT TrackTagGroup_AllowedTags.TagID FROM TrackTagGroup_AllowedTags
INNER JOIN TrackTagGroup ON TrackTagGroup.ID = TrackTagGroup_AllowedTags.TrackTagGroupID
WHERE TrackTagGroup.SummitID = %s
            )', $this->SummitID)));

            $editconf = new GridFieldDetailTagForm();
            $editconf->setFields(FieldList::create(
                TextField::create('Tag','Tag'),
                CheckboxField::create('ManyMany[IsDefault]', 'Is Default'),
                HiddenField::create('SummitID', 'SummitID', $this->Summit()->ID)
            ));

            $summaryfieldsconf = new GridFieldDataColumns();
            $summaryfieldsconf->setDisplayFields(array('Tag' => 'Tag', 'IsDefault' => 'Is Default'));

            $config->addComponent($editconf);
            $config->addComponent($summaryfieldsconf, new GridFieldFilterHeader());

            $allowed_tags = new GridField('AllowedTags', 'Allowed Tags', $this->AllowedTags(), $config);
            $fields->add($allowed_tags);
        }
        return $fields;
    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        // delete all allowed tags from PresentationCategories belonging to this group
        $ids = [];
        foreach($this->AllowedTags() as $tag)
            $ids[] = $tag->ID;

        DB::query(sprintf( "DELETE pct.* FROM PresentationCategory_AllowedTags pct
INNER JOIN PresentationCategory pc ON pc.ID = pct.PresentationCategoryID
WHERE pct.TagID IN (%s) AND pc.SummitID = %s", implode(', ', $ids), $this->SummitID));

    }

}