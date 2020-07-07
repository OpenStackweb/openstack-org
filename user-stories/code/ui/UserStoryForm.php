<?php
/**
 * Copyright 2017 Openstack Foundation
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
 * Class UserStoryForm
 */
final class UserStoryForm extends BootstrapForm {

    public function __construct($controller, $name) {
        parent::__construct(
            $controller,
            $name,
            $this->getFields(),
            $this->getActions()
        );
    }

    protected function getFields() {

        $fields = FieldList::create()
            ->literal('<div class="row">')
            ->text('Name')
                ->configure()
                    ->setAttribute('autofocus','TRUE')
                    ->setAttribute('required', true)
                    ->addHolderClass('col-md-6')
                ->end()
            ->text('Organization')
                ->configure()
                    ->addHolderClass('col-md-6')
                ->end()
            ->literal('</div><div class="row">')
            ->text('Link')
                ->configure()
                    ->addHolderClass('col-md-12')
                ->end()

            ->literal('</div>')
            ->literal('<div class="row">')
            ->checkbox('Active')
            ->configure()
            ->setFieldHolderTemplate('BootstrapAwesomeCheckboxField')
            ->addHolderClass('col-md-6')
            ->end()
            ->checkbox('ShowAtHomePage')
            ->configure()
            ->setFieldHolderTemplate('BootstrapAwesomeCheckboxField')
            ->addHolderClass('col-md-6')
            ->end()
            ->literal('</div>')
            ->tinyMCEEditor('Description')
                ->configure()
                    ->setRows(8)
                    ->setRequired(true)
                ->end()
            ->tinyMCEEditor('ShortDescription', 'Short Description')
                ->configure()
                    ->setRows(4)
                    ->setRequired(true)
                ->end()

            ->dropdown('Industry', 'Industry', UserStoriesIndustry::get()->filter('Active',1)->map('ID','IndustryName'))
                ->configure()
                    ->setEmptyString('-- Select Industry --')
                    ->setAttribute('required', true)
                ->end()
            ->dropdown('Location', 'Location', Continent::get()->map('ID','Name'))
                ->configure()
                    ->setEmptyString('-- Select Location --')
                    ->setAttribute('required', true)
                ->end()
            ->text('Tags')
                ->configure()
                    ->setAttribute('required', true)
                ->end()
            ->fileAttachment('Image','Upload story image')
                ->configure()
                    ->setPermission('delete', false)
                    ->setAcceptedFiles(array('.png','.gif','.jpeg','.jpg'))
                    ->setView('grid')
                    ->setMaxFilesize(1)
                ->end()
            ->bootstrapIgnore('Image')
            ->hidden('ID',0);


        return $fields;
    }

    protected function getActions() {
        $actions = new FieldList();
        $actions->push(FormAction::create('saveUserStory', 'Save'));
        return $actions;
    }

    public function loadDataFrom($data, $mergeStrategy = 0, $fieldList = NULL) {
        parent::loadDataFrom($data, $mergeStrategy, $fieldList);

        if(!$data instanceof UserStoryDO) return;

        $this->Fields()->fieldByName('Location')->setValue($data->Location()->ID);
        $this->Fields()->fieldByName('Industry')->setValue($data->Industry()->ID);
        $this->Fields()->fieldByName('Tags')->setValue(implode(',',$data->Tags()->map('ID','Tag')->toArray()));

        if ($data->ImageID)
            $this->Fields()->fieldByName('Image')->setValue($data->ImageID);

        return $this;
    }

    public function loadTagsData($data) {
        if ($data->Organization()->Exists()) {
            Requirements::customScript("
                var org = {id: " . $data->Organization()->ID . " , name: '" . $data->Organization()->Name . "' };
            ");
        }

        if ($data->Tags()->Count()) {
            $tag_array = [];
            foreach ($data->Tags() as $tag) {
                $tag_array[] = [ 'id' => $tag->ID, 'name' => $tag->Tag ];
            }
            Requirements::customScript("
                var tags = " . json_encode($tag_array) . ";
            ");
        }
    }

    public function saveInto(DataObjectInterface $dataObject, $fieldList = null) {

        $user_story = $dataObject;
        parent::saveInto($user_story, $fieldList);
        if(!$dataObject instanceof UserStoryDO) return;

        $organization_id = $this->fields->fieldByName("Organization")->Value();
        $user_story->OrganizationID = $organization_id;

        $industry_id = $this->fields->fieldByName("Industry")->Value();
        $user_story->IndustryID = $industry_id;

        $location_id = $this->fields->fieldByName("Location")->Value();
        $user_story->LocationID = $location_id;

        $tags = $this->Fields()->fieldByName("Tags")->Value();
        $user_story->Tags()->setByIdList(explode(',',$tags));

    }

}