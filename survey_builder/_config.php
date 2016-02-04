<?php
/**
 * Copyright 2015 OpenStack Foundation
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

PublisherSubscriberManager::getInstance()->subscribe('survey_organization_selected', function($member, $organization_name){
    //create the affiliation as current

    $organization_name = Convert::raw2sql(trim($organization_name));

    if (!empty($organization_name)) {
        $org = Org::get()->filter(array('Name' => $organization_name))->first();
        if (!$org) {
            $org                    = new Org;
            $org->Name              = $organization_name;
            $org->IsStandardizedOrg = false;
            $org->write();
        }

        // If a new org name was provided for the member, find / create the new org and update the member record
        if (!$member->hasCurrentAffiliation($organization_name)) {
            $newAffiliation            = new StdClass;
            $newAffiliation->StartDate = date('Y-m-d');
            $newAffiliation->EndDate   = null;
            $newAffiliation->Current   = 1;
            $newAffiliation->JobTitle  = "";
            $newAffiliation->Role      = "";
            AffiliationController::Save(new Affiliation(), $newAffiliation, $organization_name, $member);
        }
    }
});