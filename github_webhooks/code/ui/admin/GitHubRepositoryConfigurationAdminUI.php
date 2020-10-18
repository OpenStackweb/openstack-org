<?php
/**
 * Copyright 2017 Open Infrastructure Foundation
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
 * Class GitHubRepositoryConfigurationAdminUI
 */
final class GitHubRepositoryConfigurationAdminUI extends DataExtension
{
    public function updateCMSFields(FieldList $f) {
        //clear all fields
        $oldFields = $f->toArray();
        foreach($oldFields as $field){
            $f->remove($field);
        }


        $f->add($rootTab = new TabSet("Root", $tabMain = new Tab('Main')));


        $f->addFieldToTab('Root.Main', new TextField('Name', 'Name'));
        $f->addFieldToTab('Root.Main', new TextField('WebHookSecret', 'WebHookSecret'));

        $f->addFieldToTab('Root.Main', new TextareaField('RejectReasonNotMember', 'Reject Reason Not Member Found'));
        $f->addFieldToTab('Root.Main', new TextareaField('RejectReasonNotFoundationMember', 'Reject Reason Not Foundation Member'));
        $f->addFieldToTab('Root.Main', new TextareaField('RejectReasonNotCCLATeam', 'Reject Reason Not CCLA Team'));

        if ($this->owner->ID > 0) {
            // pull request
            $config = GridFieldConfig_RecordViewer::create(100);
            $pull_request = new GridField('PullRequests', 'Pull Requests', $this->owner->PullRequests(), $config);
            $f->addFieldToTab('Root.Pull Requests', $pull_request);

            $config = GridFieldConfig_RelationEditor::create(100);
            $teams = new GridField('AllowedTeams', 'Allowed Teams', $this->owner->AllowedTeams(), $config);
            $f->addFieldToTab('Root.Allowed Teams', $teams);
        }
    }
}