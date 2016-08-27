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

    static $allowed_actions = array(
        'profile',
        'results',
        'suggestions',
        'SpeakerSearchForm'
    );

    function init()
    {
        parent::init();

        //CSS
        Requirements::css("themes/openstack/css/jquery.autocomplete.css");
        Requirements::css("speaker_bureau/css/speaker.bureau.css");
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        //JS
        Requirements::javascript("themes/openstack/javascript/jquery.autocomplete.min.js");
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript("speaker_bureau/js/speaker.bureau.js");
        Requirements::CustomScript(" var suggestions_url = '" . $this->Link('suggestions') . "'; ");
    }

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

    //Show the profile of the speaker using the SpeakerListPage_profile.ss template

    function findSpeaker($SpeakerID)
    {
        return PresentationSpeaker::get()->filter(array('ID'=>$SpeakerID,'AvailableForBureau'=>1))->first();
    }

    public function suggestions()
    {
        if ($query = $this->getSearchQuery('q')) {

            $results = DB::query("SELECT CONCAT(FirstName,' ',LastName) AS Result, 'Speaker' AS Source FROM PresentationSpeaker
                                    WHERE FirstName LIKE '%$query%' AND AvailableForBureau = 1 GROUP BY Result
                                  UNION
                                  SELECT CONCAT(LastName,', ',FirstName) AS Result, 'Speaker' AS Source FROM PresentationSpeaker
                                    WHERE LastName LIKE '%$query%' AND AvailableForBureau = 1 GROUP BY Result
                                  UNION
                                  SELECT E.Expertise AS Result, 'Expertise' AS Source FROM SpeakerExpertise AS E
                                    JOIN PresentationSpeaker AS S ON S.ID = E.SpeakerID
                                    WHERE E.Expertise LIKE '%$query%' AND S.AvailableForBureau = 1 GROUP BY Result
                                  UNION
                                  SELECT O.Name AS Result, 'Company' AS Source FROM Org AS O
                                    JOIN Affiliation AS A ON A.OrganizationID = O.ID
                                    JOIN Member AS M ON M.ID = A.MemberID
                                    JOIN PresentationSpeaker AS S ON S.MemberID = M.ID
                                    WHERE O.Name LIKE '%$query%' AND S.AvailableForBureau = 1 GROUP BY Result");

            $Suggestions = '';
            if (count($results) > 0) {
                foreach ($results as $Speaker) {
                    $Suggestions = $Suggestions . $Speaker['Result'] . '|' . '1' . "\n";
                }

                return $Suggestions;
            }
        }

        return "No Matches|1";
    }

    function getSearchQuery($search_var = '')
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

    function getSearchQueryAsString($search_var) {
        $query_var = $this->getSearchQuery($search_var);
        return implode(', ',$query_var);
    }

    public function results()
    {
        $empty_search = true;
        $where_string = "PresentationSpeaker.AvailableForBureau = 1";


        if ($spoken_language = $this->getSearchQuery('spoken_language')) {
            $empty_search = false;
            $languages = "'" . implode("','", $spoken_language) . "'";
            $where_string .= " AND SpeakerLanguage.Language IN ({$languages})";
        }

        if ($country_origin = $this->getSearchQuery('country_origin')) {
            $empty_search = false;
            $countries = "'" . implode("','", $country_origin) . "'";
            $where_string .= " AND Countries.Name IN ({$countries})";
        }

        if ($travel_preference = $this->getSearchQuery('travel_preference')) {
            $empty_search = false;
            $preferences = "'" . implode("','", $travel_preference) . "'";
            $where_string .= " AND Countries2.Name IN ({$preferences})";
        }

        if ($query = $this->getSearchQuery('search_query')) {
            $empty_search = false;
            $where_string .= " AND (PresentationSpeaker.FirstName LIKE '%{$query}%' OR PresentationSpeaker.LastName LIKE '%{$query}%'
                          OR CONCAT_WS(' ',PresentationSpeaker.FirstName,PresentationSpeaker.LastName) LIKE '%{$query}%'
                          OR CONCAT_WS(', ',PresentationSpeaker.LastName,PresentationSpeaker.FirstName) LIKE '%{$query}%'
                          OR SpeakerExpertise.Expertise LIKE '%{$query}%' OR Org.Name LIKE '%{$query}%')";
        }

        //die($where_string);

        if (!$empty_search) {
            $Results = PresentationSpeaker::get()
                ->leftJoin("SpeakerExpertise", "SpeakerExpertise.SpeakerID = PresentationSpeaker.ID")
                ->leftJoin("Countries", "Countries.Code = PresentationSpeaker.Country")
                ->leftJoin("Member", "Member.ID = PresentationSpeaker.MemberID")
                ->leftJoin("Affiliation", "Affiliation.MemberID = Member.ID")
                ->leftJoin("Org", "Org.ID = Affiliation.OrganizationID")
                ->leftJoin("SpeakerLanguage", "SpeakerLanguage.SpeakerID = PresentationSpeaker.ID")
                ->leftJoin("SpeakerTravelPreference", "SpeakerTravelPreference.SpeakerID = PresentationSpeaker.ID")
                ->leftJoin("Countries", "Countries2.Code = SpeakerTravelPreference.Country", "Countries2")
                ->where($where_string);

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

    public function ContactForm()
    {
        $data = Session::get("FormInfo.Form_SpeakerContactForm.data");
        $SpeakerID = Convert::raw2sql($this->request->param("ID"));

        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript("marketplace/code/ui/admin/js/utils.js");

        Requirements::css('speaker_bureau/css/speaker.contact.form.css');
        Requirements::javascript("speaker_bureau/js/speaker-contact-form.js");

        $form = new SpeakerContactForm($this, 'SpeakerContactForm', $SpeakerID);
        // we should also load the data stored in the session. if failed
        if (is_array($data)) {
            $form->loadDataFrom($data);
        }

        return $form;
    }

    function LettersWithSpeakers()
    {
        $query = DB::Query("SELECT DISTINCT SUBSTRING(LastName,1,1) as letter
                                  FROM PresentationSpeaker WHERE AvailableForBureau = 1 ORDER BY letter");

        $letter_list = array();
        foreach ($query as $letter) {
            $letter_list[] = new ArrayData(array("Letter" => $letter['letter']));
        }

        return new ArrayList($letter_list);
    }

    function AvailableTravelCountries()
    {
        $query = DB::Query("SELECT Name FROM Countries");

        $country_list = array();
        foreach ($query as $country) {
            $country_list[] = new ArrayData(array("Country" => $country['Name']));
        }

        return new ArrayList($country_list);
    }

    function AvailableLanguages()
    {
        $query = DB::Query("SELECT DISTINCT SL.Language FROM SpeakerLanguage AS SL
                            RIGHT JOIN PresentationSpeaker AS PS ON PS.ID = SL.SpeakerID
                            WHERE PS.AvailableForBureau = 1");

        $language_list = array();
        foreach ($query as $language) {
            $language_list[] = new ArrayData(array("Language" => $language['Language']));
        }

        return new ArrayList($language_list);
    }

    function AvailableCountries()
    {
        $query = DB::Query("SELECT DISTINCT C.Name FROM Countries AS C
                            RIGHT JOIN PresentationSpeaker AS PS ON PS.Country = C.Code
                            WHERE PS.AvailableForBureau = 1");

        $country_list = array();
        foreach ($query as $country) {
            $country_list[] = new ArrayData(array("Country" => $country['Name']));
        }

        return new ArrayList($country_list);
    }

    public function optionSelected($filter, $option) {
        $query_var = $this->getSearchQuery($filter);

        return ($query_var && in_array($option,$query_var)) ? 'selected' : '';
    }

}
