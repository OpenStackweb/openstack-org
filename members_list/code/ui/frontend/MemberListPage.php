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
class MemberListPage extends Page
{
    static $db       = [];
    static $has_one  = [];
    static $has_many = [];
}

class MemberListPage_Controller extends Page_Controller
{

    function init()
    {
        parent::init();

        //CSS
        Requirements::css("themes/openstack/css/jquery.autocomplete.css");

        Requirements::javascript("themes/openstack/javascript/jquery.autocomplete.min.js");
        Requirements::CustomScript("
							
					jQuery(function(){

					  $('#SearchForm_MemberSearchForm_mq').autocomplete('" . $this->Link('results') . "', {
					        minChars: 3,
					        selectFirst: true,
					        autoFill: true,
					   });

						$('#SearchForm_MemberSearchForm_mq').focus();

					});						
					
			
			");
    }

    static $allowed_actions = [
        'profile',
        'profileRedirect',
        'results',
        'MemberSearchForm',
        'idList'      => 'ADMIN',
        'assignGroup' => 'ADMIN',
        'ListExport',
        'CSVExport'   => 'ADMIN'
    ];

    private static $url_handlers = [
        'profile/$MemberID!/$NameSlug!' => 'profile',
        'profile/$MemberID!'            => 'profileRedirect',
    ];

    function MemberList()
    {

        if (isset($_GET['letter'])) {

            $requestedLetter = Convert::raw2xml($_GET['letter']);

            if ($requestedLetter == 'intl') {
                $likeString = "NOT Surname REGEXP '[A-Za-z0-9]'";
            } elseif (ctype_alpha($requestedLetter)) {
                $likeString = "Surname LIKE '" . substr($requestedLetter, 0, 1) . "%'";
            } else {
                $likeString = "Surname LIKE 'a%'";
            }

        } else {
            $likeString = "Surname LIKE 'a%'";
        }


        $list = Member::get()
            ->where("Group_Members.GroupID = 5 AND " . $likeString)
            ->leftJoin('Group_Members', 'Member.ID = Group_Members.MemberID')
            ->sort('Surname');

        return GroupedList::create($list);
    }

    function findMember($member_id)
    {
        $member  = Member::get()->byID(intval($member_id));
        if(!is_null($member))
        {
            // Check to make sure they are in the foundation membership group
            If ($member->inGroup(5, true) && $member->isActive())
            {
                return $member;
            }
        }
    }

    function profileRedirect()
    {
        $member_id = Convert::raw2sql($this->request->param("MemberID"));
        if (is_numeric($member_id)) {
            // Check to make sure there's a member with the current id
            if ($member = $this->findMember($member_id)) {
                return $this->redirect($this->Link('profile/'.$member_id.'/'.$member->getNameSlug()), 301);
            }
        }

        return $this->httpError(404, 'Sorry that member could not be found');
    }

    //Show the profile of the member using the MemberListPage_profile.ss template
    function profile()
    {
        // Grab member ID from the URL
        $MemberID = Convert::raw2sql($this->request->param("MemberID"));
        // Check to see if the ID is numeric
        if (is_numeric($MemberID)) {
            // Check to make sure there's a member with the current id
            if ($Profile = $this->findMember($MemberID)) {

                $data["Profile"] = $Profile;
                // A member is looking at own profile
                if (Member::currentUserID() == $MemberID) {
                    $data["OwnProfile"] = true;
                }

                //return our $Data to use on the page
                return $this->getViewer('profile')->process
                    (
                        $this->customise($data)
                    );
            }
        }
        return $this->httpError(404, 'Sorry that member could not be found');
    }

    public function MemberSearchForm()
    {
        $searchField = new TextField('mq', 'Search Member', $this->getSearchQuery());
        $searchField->setAttribute("placeholder", "first name, last name or irc nickname");
        $fields = new FieldList($searchField);

        $form = new SearchForm($this, 'MemberSearchForm', $fields);

        $form->setFormAction($this->Link('results'));

        return $form;
    }

    public function results()
    {
        if ($query = $this->getSearchQuery()) {

            // Search for only foundation members (Group 5) against the query.

            $filter = "(MATCH (FirstName, Surname) AGAINST ('{$query}')
					OR FirstName LIKE '%{$query}%'
					OR Surname LIKE '%{$query}%'
					OR IRCHandle LIKE '%{$query}%') AND Group_Members.GroupID=5 AND Member.Active = 1";

            $Results = Member::get()
                ->where($filter)
                ->leftJoin("Group_Members", "Member.ID = Group_Members.MemberID");
            // No Member was found
            if (!isset($Results) || $Results->count() == 0) {
                return $this->customise($Results);
            }

            // For AutoComplete
            if (Director::is_ajax()) {

                $Members = $Results->map('ID', 'Name');
                $Suggestions = '';

                foreach ($Members as $Member) {
                    $Suggestions = $Suggestions . $Member . '|' . '1' . "\n";
                }

                return $Suggestions;
            } // For Results Template
            else {


                $filter = "(MATCH (FirstName, Surname) AGAINST ('{$query}')
					OR FirstName LIKE '%{$query}%'
					OR Surname LIKE '%{$query}%'
					OR IRCHandle LIKE '%{$query}%') AND Group_Members.GroupID=5 AND Member.Active = 1";

                $OneMember = Member::get()
                    ->where($filter)
                    ->leftJoin("Group_Members", "Member.ID = Group_Members.MemberID");

                // see if one member exactly matches the search term

                if ($OneMember) {
                    $Results = $OneMember;
                }

                // If there is only one person with this name, go straight to the resulting profile page
                if ($OneMember && $OneMember->Count() == 1) {
                    $this->redirect($this->Link() . 'profile/' . $OneMember->First()->ID .'/'. $OneMember->First()->getNameSlug());
                }

                $Output = new ArrayData(array(
                    'Title' => 'Results',
                    'Results' => $Results
                ));
                if ($Results->count() == 0) {
                    $message = $this->getViewer('results')->process($this->customise($Output));
                    $this->response->setBody($message);
                    throw new SS_HTTPResponse_Exception($this->response, 404);
                }

                return $this->customise($Output);
            }
        }

        $this->redirect($this->Link());
    }

    function getSearchQuery()
    {
        if ($this->request) {
            $query = $this->request->getVar("mq");
            if (!empty($query)) {
                return Convert::raw2sql($query);
            }

            return false;
        }
    }

    private function GetMembersInDateRange($StartTime = null, $EndTime = null)
    {

        $DateRange = "";

        if ($StartTime && $EndTime) {
            $DateRange = " AND (LastEdited BETWEEN '" . $StartTime . "' AND '" . $EndTime . "')";

        } elseif ($EndTime) {
            $DateRange = " AND (LastEdited <= '" . $EndTime . "')";

        } elseif ($StartTime) {
            $DateRange = " AND (LastEdited >= '" . $StartTime . "')";

        } else {
            $DateRange = "";
        }

        // Pull Members using a custom db query. This returns a MySQLQuery object
        $MemberList = DB::query("

				SELECT Member.ID, `FirstName`, `Surname`, `IRCHandle`, `TwitterName`, `Email`, `SecondEmail`, `ThirdEmail`
				FROM `Member`
				LEFT JOIN `Group_Members` ON `Member`.`ID` = `Group_Members`.`MemberID`
				WHERE `Group_Members`.`GroupID`= 5" . $DateRange

        );


        return $MemberList;

    }

    function ListExport()
    {

        if (isset($_GET['token']) && $_GET['token'] == "fcv4x7Nl8v") {

            // Check URL parameters for start and end times
            $Start = isset($_GET['start']) ? $_GET['start'] : null;
            $End = isset($_GET['end']) ? $_GET['end'] : null;

            $StartTime = $Start ? date("Y-m-d H:i:s", strtotime($Start)) : null;
            $EndTime = $End ? date("Y-m-d H:i:s", strtotime($End)) : null;


            $MemberList = $this->GetMembersInDateRange($StartTime, $EndTime);


            $results = array();


            // Transform the MySQLQuery object created above into an ArrayData object

            if ($MemberList) {
                foreach ($MemberList as $Member) {

                    $dbMember = Member::get()->byID($Member['ID']);

                    if (!is_null($dbMember)) {

                        $AffiliationList = $dbMember->OrderedAffiliations();

                        // If there are Affiliation updates, push a new copy of the member to the results array filled in with the org and date from the update
                        if ($AffiliationList && $AffiliationList->Count() > 0) {

                            foreach ($AffiliationList as $a) {
                                $currentMemberOrg = $a->Organization();
                                $Member['OrgAffiliations'] = $currentMemberOrg->Name;
                                if ($a->Current) {
                                    $Member['untilDate'] = null;
                                } else {
                                    $Member['untilDate'] = $a->EndDate;
                                }

                                // Push the member to the results
                                array_push($results, $Member);
                            }
                        } else {
                            //no affiliations
                            $Member['OrgAffiliations'] = null;
                            $Member['untilDate'] = null;
                            array_push($results, $Member);
                        }
                    }

                }
            }


            // Finally, convert the array from the ArrayData object to JSON
            $json = Convert::Array2JSON($results);

            return $json;
        }

    }

    function FullMemberList()
    {
        return Member::get()->leftJoin('Group_Members',
            '`Member`.`ID` = `Group_Members`.`MemberID` AND Group_Members.GroupID=5');
    }

    /**
     *  Extension points
     */

    function getHeaderExtensions()
    {
        $html = '';
        $this->extend('getHeaderExtensions', $html);
        return $html;
    }

    function getProfileExtensions(){

        $html = '';
        $this->extend('getProfileExtensions', $html);
        return $html;
    }

    function getProfileExtensionsFooter(){
        $html = '';
        $this->extend('getProfileExtensionsFooter', $html);
        return $html;
    }
}
