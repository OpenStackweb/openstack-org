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
 * Class EditProfileDupesMemberExtension
 */
final class EditProfileDupesMemberExtension extends Extension {

    /**
     * @var DupesMembersManager
     */
    private $manager;

    /**
     *
     */
    public function __construct(){
        parent::__construct();
        $this->manager = new DupesMembersManager(new SapphireDupesMemberRepository,
            new DupeMemberMergeRequestFactory,
            new DupeMemberDeleteRequestFactory,
            new SapphireDupeMemberMergeRequestRepository,
            new SapphireDupeMemberDeleteRequestRepository,
            new SapphireDeletedDupeMemberRepository,
            new DeletedDupeMemberFactory,
            new SapphireCandidateNominationRepository,
            SapphireTransactionManager::getInstance(),
            SapphireBulkQueryRegistry::getInstance());
    }


    public function onBeforeInit(){
    }
    /*
     *
     */
    public function getRenderUITopExtensions(&$html){
        $dupe_members = $this->manager->getDupes(Member::currentUser());
        $qty          = count($dupe_members);
        if($qty > 0){

            Requirements::css('dupe_members/css/edit.profile.dupes.member.extension.css');
            Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
            Requirements::javascript('dupe_members/javascript/edit.profile.dupes.member.extension.js');

            $members_emails_warning  =  "<div id=\"dupes-email-warning-container\" class=\"alert alert-danger alert-dismissible\" role=\"alert\">";
            $members_emails_warning .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>";
            $members_emails_warning .= "<p>We currently show there are {$qty} additional accounts with your First & Last Name:</p>";
            $members_emails_warning .= '<ul id="dupes-emails-list">';

            foreach ($dupe_members as $dupe_mem) {
                $id                      = $dupe_mem->getIdentifier();
                $delete_btn_html         = '<a class="roundedButton dupes-member-delete-account" data-id="'.$id.'" href="#" title="Delete Account">Delete</a>';
                $merge_btn_html          = '<a class="roundedButton dupes-member-merge-account"  data-id="'.$id.'" href="#" title="Merge Account">Merge</a>';
                $members_emails_warning .= '<li><span class="dupes-email">'.preg_replace('/(?<=.).(?=.*.@)/u','*',$dupe_mem->getEmail()).'</span>'.$merge_btn_html.'&nbsp;'.$delete_btn_html.'</li>';
            }
            $members_emails_warning .= '</ul></div>';
            $html .= $members_emails_warning;
        }
    }
} 