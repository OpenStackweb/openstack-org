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
class GridFieldUpdateDefaultCategoryTags implements GridField_HTMLProvider, GridField_URLHandler, GridField_ActionProvider {

    protected $targetFragment;

    private static $allowed_actions = array(
        'handleUpdateDefaultCategoryTags'
    );

    public function __construct($targetFragment = 'before') {
        $this->targetFragment = $targetFragment;
    }

    //Generate the HTML fragment for the GridField
    public function getHTMLFragments($gridField) {
        $button = new GridField_FormAction(
            $gridField,
            'updateDefaultCategoryTags',
            'Update All Categories',
            'updateDefaultCategoryTags',
            null
        );
        $button->setAttribute('data-icon', 'chain--plus');
        return array(
            $this->targetFragment =>  $button->Field() ,
        );
    }
    /**
     * Return URLs to be handled by this grid field, in an array the same form
     * as $url_handlers.
     * Handler methods will be called on the component, rather than the
     * {@link GridField}.
     */
    public function getURLHandlers($gridField)
    {
        return array(
            'updateDefaultCategoryTags' => 'handleUpdateDefaultCategoryTags'
        );
    }

    public function handleUpdateDefaultCategoryTags($grid, $request, $data = null) {

        $summit_id = intval($request->param('ID'));
        if($summit_id > 0 && $summit = Summit::get()->byID($summit_id)) {
            $default_tags = $summit->CategoryDefaultTags();
            foreach($summit->Categories() as $category) {
                $category_tags = $category->AllowedTags()->filter('IsDefault',0);
                $category->AllowedTags()->filter('IsDefault',1)->removeAll();
                foreach ($default_tags as $dtag) {
                    $found_tag = $category_tags->find('TagID',$dtag->TagID);
                    if ($found_tag && $found_tag->Group == $dtag->Group ) continue;

                    if (!$found_tag) {
                        $found_tag = $dtag;
                    }
                    $category->AllowedTags()->remove($found_tag);
                    $category->AllowedTags()->add($dtag,array('Group' => $dtag->Group, 'IsDefault' => 1));
                }

            }
        }
    }


    public function getActions($gridField) {
        return array('updateDefaultCategoryTags');
    }


    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if($actionName == 'updatedefaultcategorytags') {
            return $this->handleUpdateDefaultCategoryTags($gridField,Controller::curr()->getRequest(), $data);
        }
    }
}

