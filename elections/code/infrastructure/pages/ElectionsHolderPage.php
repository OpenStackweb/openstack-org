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
final class ElectionsHolderPage extends Page
{
	static $db = [];

    static $allowed_children = ['ElectionPage','Page'];

	function getCMSFields()
	{
		$fields = parent::getCMSFields();

		$elections_config = GridFieldConfig_RecordEditor::create();
		$elections        = DataList::create('ElectionPage');
		$elections_grid   = new GridField(
			'All Election Pages Grid', // Field name
			'Election Pages', // Field title
			$elections,
			$elections_config
		);
		$fields->addFieldToTab('Root.ElectionPages', $elections_grid);

		return $fields;
	}

	public function getCurrentElectionPage(){
	    $current_election = Election::getCurrent();
        if(!$current_election) return null;
        return ElectionPage::get()->filter('CurrentElectionID', $current_election->ID)->first();
    }
}

/**
 * Class ElectionsHolderPage_Controller
 */
final class ElectionsHolderPage_Controller extends Page_Controller
{

	function index(SS_HTTPRequest $request)
	{
		// Redirect to current election
        $current_election_page = $this->getCurrentElectionPage();
        if(!is_null($current_election_page))
		    return $this->redirect($current_election_page->Link());
        $this->redirect('404');
	}

}