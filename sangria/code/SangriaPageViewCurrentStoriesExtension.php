<?php

/**
 * Copyright 2014 Openstack Foundation
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
 * Class SangriaPageViewCurrentStoriesExtension
 */
final class SangriaPageViewCurrentStoriesExtension extends Extension
{

    public function onBeforeInit()
    {

        Config::inst()->update(get_class($this), 'allowed_actions', array(
            'ViewCurrentStories',
            'UpdateStories',
            'SetAdminSS',
            'UnPublishFromUserStories',
        ));

        Config::inst()->update(get_class($this->owner), 'allowed_actions', array(
            'ViewCurrentStories',
            'UpdateStories',
            'SetAdminSS',
            'UnPublishFromUserStories',
        ));
    }

    public function ViewCurrentStories(){
        Requirements::javascript("themes/openstack/javascript/sangria/view.current.stories.js");
        return $this->owner->Customise(array())->renderWith(array('SangriaPage_ViewCurrentStories','SangriaPage','SangriaPage'));
    }

    // Update Stories Industry and Order
    function UpdateStories()
    {

        foreach ($_POST['industry'] as $story_id => $industry) {
            $story = SiteTree::get_by_id("UserStory", $story_id);
            $story->UserStoriesIndustryID = $industry;
            $story->Sort = $_POST['order'][$story_id];
            $story->Video = $_POST['video'][$story_id];
            $story->Title = $_POST['title'][$story_id];
            $story->ShowVideo = ($_POST['video'][$story_id]) ? true : false;
            $story->write();
        }

        $this->owner->setMessage('Success', 'User Stories saved.');

        $this->owner->redirectBack();
    }

    function SetAdminSS()
    {
        if (isset($_GET['ID']) && is_numeric($_GET['ID'])) {
            $UserStory = $_GET['ID'];
        } else {
            die();
        }

        $showinAdmin = ( isset($_GET['Set']) && intval($_GET['Set']) === 1) ? 1 : 0;

        $story  = SiteTree::get_by_id("UserStory", $UserStory);
        $parent = UserStoryHolder::get()->first();

        if (!$parent) {
            $this->owner->setMessage('Error', 'could not publish user story bc there is not any available parent page(UserStoryHolder).');
            Controller::curr()->redirectBack();
        }

        $story->ShowInAdmin = $showinAdmin;
        $story->setParent($parent); // Should set the ID once the Holder is created...
        $story->write();
        $story->publish("Live", "Stage");

        $this->owner->setMessage('Success', '<b>' . $story->Title . '</b> updated.');

        $this->owner->redirectBack();
    }

    function UserStoriesIndustries()
    {
        return UserStoriesIndustry::get()->filter('Active', 1);
    }

    function UnPublishFromUserStories() {
        if (isset($_GET['ID']) && is_numeric($_GET['ID'])) {
            $user_story_id = $_GET['ID'];
            $story         = UserStory::get()->byID($user_story_id);
            $story->doUnpublish();
            $this->owner->setMessage('Success', '<b>' . $story->Title . '</b> unpublished.');
            $this->owner->redirectBack();
        }
    }

} 