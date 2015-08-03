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
class SummitTrackChair extends DataObject {
	
	static $has_one = array(
		'Member' => 'Member',
		'Summit' => 'Summit'
	);

    static $many_many = array(
        'Categories' => 'PresentationCategory'
    );
    
    public static function addChair($member, $catgoryID) {

    	$priorChair = SummitTrackChair::get()->filter('MemberID',$member->ID)->first();
	    $category = PresentationCategory::get()->byID($catgoryID);    	

    	if(!$priorChair) {
	    	$chair = new self();
	    	$chair->MemberID = $member->ID;
	    	$chair->SummitID = Summit::get_active()->ID;
	    	$chair->write();
	    	$chair->Categories()->add($category);

			//Find or create the 'track-chairs' group
			if (!$Group = Group::get()->filter('Code', 'track-chairs')->first()) {
				$Group = new Group();
				$Group->Code = "track-chairs";
				$Group->Title = "Track Chairs";
				$Group->Write();
				$Member->Groups()->add($Group);
			}
			//Add member to the group
			$member->Groups()->add($Group);
	    	
	    } else {
	    	$priorChair->Categories()->add($category);
	    }

    }

}