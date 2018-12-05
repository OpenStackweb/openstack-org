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

/**
 * Class GridFieldSeedWithPreviousQuestionsAction
 */
final class GridFieldSeedWithPreviousQuestionsAction
    implements GridField_HTMLProvider, GridField_URLHandler, GridField_ActionProvider {

    protected $targetFragment;

    private static $allowed_actions = [
        'handleSeedWithPreviousQuestionsAction'
    ];

    public function __construct($targetFragment = 'before') {
        $this->targetFragment = $targetFragment;
    }

    //Generate the HTML fragment for the GridField
    public function getHTMLFragments($gridField) {
        $button = new GridField_FormAction(
            $gridField,
            'seedWithPreviousQuestionsAction',
            'Seed With Previous Questions',
            'seedWithPreviousQuestionsAction',
            null
        );
        $button->setAttribute('data-icon', 'add');
        return [
            $this->targetFragment =>  $button->Field() ,
        ];
    }
    /**
     * Return URLs to be handled by this grid field, in an array the same form
     * as $url_handlers.
     * Handler methods will be called on the component, rather than the
     * {@link GridField}.
     */
    public function getURLHandlers($gridField)
    {
        return [
            'seedWithPreviousQuestionsAction' => 'handleSeedWithPreviousQuestionsAction'
        ];
    }

    public function handleSeedWithPreviousQuestionsAction($grid, $request, $data = null) {

        $page_id = $request->postVar('ID');
        $summit_id = $request->postVar('SummitID');
        $summit = Summit::get()->byID($summit_id);
        $summit_source = null;

        $prev_summit = Summit::get()->exclude('ID', $summit_id)->sort('ID', 'DESC')->first();

        $prev_page = SummitPage::get()->where(
            "ClassName = 'SummitQuestionsPage' AND SummitID = {$prev_summit->ID} 
                    AND (Title LIKE '%Question%' OR Title LIKE 'FAQ') "
            )
            ->sort('Created', 'ASC')
            ->first();

        if (!$prev_page)
            throw new ValidationException('Cannot find a page to feed from.', 0);


        foreach ($prev_page->Categories() as $category) {
            $new_cat_name = str_replace($prev_summit->Title, $summit->Title, $category->Name);
            $new_cat = SummitQuestionCategory::get()->filter(['Name' => $new_cat_name, 'SummitQuestionsPageID' => $page_id])->first();

            if (!$new_cat) {
                $new_cat = $category->duplicate(false);
                $new_cat->Name = $new_cat_name;
                $new_cat->SummitQuestionsPageID = $page_id;
                $new_cat->write();
            }


            foreach($category->Questions() as $question) {
                $new_q = SummitQuestion::get()->filter(['Question' => $question->Question, 'SummitQuestionsPageID' => $page_id])->first();

                if (!$new_q) {
                    $new_q = $question->duplicate(false);
                    $new_q->SummitQuestionsPageID = $page_id;
                    $new_q->CategoryID = $new_cat->ID;
                    $new_q->write();
                }

            }
        }

    }

    public function getActions($gridField) {
        return ['seedWithPreviousQuestionsAction'];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if($actionName == 'seedwithpreviousquestionsaction') {
            return $this->handleSeedWithPreviousQuestionsAction($gridField,Controller::curr()->getRequest(), $data);
        }
    }
}
