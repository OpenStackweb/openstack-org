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
class SpeakerListPage extends Page
{
    static $db = array();
    static $has_one = array();
    static $has_many = array();
}

class SpeakerListPage_Controller extends Page_Controller
{

    function init()
    {
        parent::init();

        //CSS
        Requirements::css("themes/openstack/css/jquery.autocomplete.css");

        Requirements::javascript("themes/openstack/javascript/jquery.autocomplete.min.js");
        Requirements::CustomScript("
							
					jQuery(function(){

					  $('#SearchForm_SpeakerSearchForm_mq').autocomplete('" . $this->Link('results') . "', {
					        minChars: 3,
					        selectFirst: true,
					        autoFill: true,
					   });

						$('#SearchForm_SpeakerSearchForm_mq').focus();

					});						
					
			
			");
    }

    static $allowed_actions = array(
        'profile',
        'results',
        'SpeakerSearchForm'
    );

    function SpeakerList()
    {

        if (isset($_GET['letter'])) {

            $requestedLetter = Convert::raw2xml($_GET['letter']);

            if ($requestedLetter == 'intl') {
                $likeString = "NOT LastName REGEXP '[A-Za-z0-9]'";
            } elseif (ctype_alpha($requestedLetter)) {
                $likeString = "LastName LIKE '" . substr($requestedLetter, 0, 1) . "%'";
            } else {
                $likeString = "LastName LIKE 'a%'";
            }

        } else {
            $likeString = "LastName LIKE 'a%'";
        }


        $list = PresentationSpeaker::get()
            ->where("AvailableForBureau = 1 AND " . $likeString)
            ->sort('LastName');

        return GroupedList::create($list);
    }

    function findSpeaker($SpeakerID)
    {
        $SpeakerID = intval($SpeakerID);
        $query       = PresentationSpeaker::get()->where(" ID = {$SpeakerID}" )->sql();
        $res         = DB::query($query.' LOCK IN SHARE MODE');
        if($res->numRecords() > 0)
        {
            $Speaker = new PresentationSpeaker($res->first());
            // Check to make sure they are in the foundation membership group
            If ($Speaker && $Speaker->AvailableForBureau == 1)
            {
                return $Speaker;
            }
        }
    }

    //Show the profile of the speaker using the SpeakerListPage_profile.ss template
    function profile()
    {
        // Grab speaker ID from the URL
        $SpeakerID = Convert::raw2sql($this->request->param("ID"));

        // Check to see if the ID is numeric
        if (is_numeric($SpeakerID)) {

            // Check to make sure there's a member with the current id
            if ($Profile = $this->findSpeaker($SpeakerID)) {

                $data["Profile"] = $Profile;

                //return our $Data to use on the page
                return $this->Customise($data);
            }
        }

        return $this->httpError(404, 'Sorry that speaker could not be found');
    }

    public function SpeakerSearchForm()
    {
        $searchField = new TextField('mq', 'Search Speaker', $this->getSearchQuery());
        $searchField->setAttribute("placeholder", "first name, last name or irc nickname");
        $fields = new FieldList($searchField);

        $form = new SearchForm($this, 'SpeakerSearchForm', $fields);

        $form->setFormAction($this->Link('results'));

        return $form;
    }

    public function results()
    {
        if ($query = $this->getSearchQuery()) {

            // Search for only foundation members (Group 5) against the query.

            $filter = "FirstName LIKE '%{$query}%'
					OR LastName LIKE '%{$query}%'
					OR IRCHandle LIKE '%{$query}%'";

            $Results = PresentationSpeaker::get()->where($filter);
            // No Member was found
            if (!isset($Results) || $Results->count() == 0) {
                return $this->customise($Results);
            }

            // For AutoComplete
            if (Director::is_ajax()) {

                $Speakers = $Results->map('ID', 'Name');
                $Suggestions = '';

                foreach ($Speakers as $Speaker) {
                    $Suggestions = $Suggestions . $Speaker . '|' . '1' . "\n";
                }

                return $Suggestions;
            } // For Results Template
            else {
                $filter = "FirstName LIKE '%{$query}%'
					OR LastName LIKE '%{$query}%'
					OR IRCHandle LIKE '%{$query}%'";

                $OneSpeaker = PresentationSpeaker::get()->where($filter);

                // see if one member exactly matches the search term

                if ($OneSpeaker) {
                    $Results = $OneSpeaker;
                }

                // If there is only one person with this name, go straight to the resulting profile page
                if ($OneSpeaker && $OneSpeaker->Count() == 1) {
                    $this->redirect($this->Link() . 'profile/' . $OneSpeaker->First()->ID);
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
}
