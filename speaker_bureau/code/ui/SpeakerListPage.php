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

					  $('#search_form_input').autocomplete('" . $this->Link('suggestions') . "', {
					        minChars: 3,
					        selectFirst: true,
					        autoFill: true,
					        focus: function(event, ui) {
                                if($(ui.item).val() == 'No Matches')
                                    $(ui.item).disable();
                            },
                            select: function(event, ui){
                                if($(ui.item).val() == 'No Matches')
                                    return false;
                            }
					   });

						$('#search_form_input').focus();

					});						
					
			
			");
    }

    static $allowed_actions = array(
        'profile',
        'results',
        'suggestions',
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

    public function suggestions()
    {
        if ($query = $this->getSearchQuery('q')) {

            $results = DB::query("SELECT CONCAT(FirstName,' ',LastName) AS Result, 'Speaker' AS Source FROM PresentationSpeaker WHERE FirstName LIKE '%$query% AND AvailableForBureau = 1'
                                  UNION
                                  SELECT CONCAT(LastName,', ',FirstName) AS Result, 'Speaker' AS Source FROM PresentationSpeaker WHERE LastName LIKE '%$query% AND AvailableForBureau = 1'
                                  UNION
                                  SELECT Expertise AS Result, 'Expertise' AS Source FROM SpeakerExpertise WHERE Expertise LIKE '%$query%'
                                  UNION
                                  SELECT Name AS Result, 'Country' AS Source FROM Countries WHERE Name LIKE '%$query%'
                                  UNION
                                  SELECT Name AS Result, 'Company' AS Source FROM Org WHERE Name LIKE '%$query%'");

            $Suggestions = '';

            if (count($results) > 0) {
                foreach ($results as $Speaker) {
                    $Suggestions = $Suggestions . $Speaker['Result'].'|' . '1' . "\n";
                }

                return $Suggestions;
            }
        }

        return "No Matches|1";
    }

    public function results()
    {
        if ($query = $this->getSearchQuery()) {

            $Results = PresentationSpeaker::get()
                ->leftJoin("SpeakerExpertise","SpeakerExpertise.SpeakerID = PresentationSpeaker.ID")
                ->leftJoin("Countries","Countries.Code = PresentationSpeaker.Country")
                ->leftJoin("Member","Member.ID = PresentationSpeaker.MemberID")
                ->leftJoin("Affiliation","Affiliation.MemberID = Member.ID")
                ->leftJoin("Org","Org.ID = Affiliation.OrganizationID")
                ->where("(PresentationSpeaker.FirstName LIKE '%{$query}%' OR PresentationSpeaker.LastName LIKE '%{$query}%'
                          OR CONCAT_WS(' ',PresentationSpeaker.FirstName,PresentationSpeaker.LastName) LIKE '%{$query}%'
                          OR Countries.Name LIKE '%{$query}%' OR SpeakerExpertise.Expertise LIKE '%{$query}%')
                          OR Org.Name LIKE '%{$query}%' AND PresentationSpeaker.AvailableForBureau = 1");

            // No Member was found
            if (!isset($Results) || $Results->count() == 0) {
                return $this->customise($Results);
            }

            // If there is only one person with this name, go straight to the resulting profile page
            if ($Results && $Results->Count() == 1) {
                $this->redirect($this->Link() . 'profile/' . $Results->First()->ID);
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

        $this->redirect($this->Link());
    }

    function getSearchQuery($search_var='')
    {
        $search_var = ($search_var) ? $search_var : 'search_query';
        if ($this->request) {
            $query = $this->request->getVar($search_var);
            if (!empty($query)) {
                return Convert::raw2sql($query);
            }

            return false;
        }
    }

    public function ContactForm() {
        $data = Session::get("FormInfo.Form_SpeakerContactForm.data");
        $SpeakerID = Convert::raw2sql($this->request->param("ID"));

        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript("marketplace/code/ui/admin/js/utils.js");

        Requirements::css('speaker_bureau/css/speaker.contact.form.css');
        Requirements::javascript("speaker_bureau/js/speaker-contact-form.js");

        $form = new SpeakerContactForm($this, 'SpeakerContactForm', $SpeakerID);
        // we should also load the data stored in the session. if failed
        if(is_array($data)) {
            $form->loadDataFrom($data);
        }

        return $form;
    }

}
