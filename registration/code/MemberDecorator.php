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
class MemberDecorator extends DataExtension
{

    private static $db = array
    (
        'SecondEmail' => 'Text',
        'ThirdEmail' => 'Text',
        'HasBeenEmailed' => 'Boolean',
        'ShirtSize' => "Enum('Extra Small, Small, Medium, Large, XL, XXL')",
        'StatementOfInterest' => 'Text',
        'Bio' => 'HTMLText',
        'FoodPreference' => 'Text',
        'OtherFood' => 'Text',
        'IRCHandle' => 'Text',
        'TwitterName' => 'Text',
        'Projects' => 'Text',
        'OtherProject' => 'Text',
        'SubscribedToNewsletter' => 'Boolean',
        'JobTitle' => 'Text',
        'DisplayOnSite' => 'Boolean',
        'Role' => 'Text',
        'LinkedInProfile' => 'Text',
        'Address' => 'Varchar(255)',
        'Suburb' => 'Varchar(64)',
        'State' => 'Varchar(64)',
        'Postcode' => 'Varchar(64)',
        'Country' => 'Varchar(2)',
        'City' => 'Varchar(64)',
        'Gender' => 'Varchar(32)',
        'TypeOfDirector' => 'Text',
        'Active'         => 'Boolean',
    );

    private static $defaults = array(
        'SubscribedToNewsletter' => true,
        'DisplayOnSite' => false,
        'Active' => true,
    );

    private static $has_one = array(
        'Photo' => 'BetterImage',
        'Org' => 'Org'
    );


    private static $has_many = array(
        'LegalAgreements' => 'LegalAgreement',
        'Affiliations' => 'Affiliation'
    );

    private static $belongs_many_many = array(
        'ManagedCompanies' => 'Company'
    );

    public function onBeforeDelete()
    {
        $current_id = $this->owner->ID;

        $legal_agreements = $this->owner->LegalAgreements();
        foreach($legal_agreements as $la)
        {
            $la->delete();
        }

        $affiliations = $this->owner->Affiliations();
        foreach($affiliations as $a)
        {
            $a->delete();
        }

        DB::query("DELETE FROM Company_Administrators WHERE MemberID = {$current_id}");
        DB::query("DELETE FROM Group_Members WHERE MemberID = {$current_id}");

    }

    public function setOwner($owner, $ownerBaseClass = null)
    {
        parent::setOwner($owner, $ownerBaseClass);
        if ($owner) {
            Config::inst()->remove(get_class($owner), 'searchable_fields');
            Config::inst()->update(get_class($owner), 'searchable_fields', array(
                'FirstName' => 'PartialMatchFilter',
                'Surname' => 'PartialMatchFilter',
                'Email' => 'PartialMatchFilter'
            ));
        }
    }

    // Link to the edit profile page
    function Link()
    {
        if ($ProfilePage = EditProfilePage::get()->first()) {
            return $ProfilePage->Link();
        }
    }

    /**
     * Returns true if this user is locked out
     */
    public function canLogIn(&$result)
    {
        if(!$this->owner->Active)
        {
            $result->error('Your account has been disabled');
        }
        return $result;
    }

    function ProfilePhoto($width = 100)
    {
        $img = $this->owner->Photo();
        $twitter_name = $this->owner->TwitterName;
        if (!is_null($img) && $img->exists() && Director::fileExists($img->Filename)) {
            $img = $img->SetWidth($width);

            return "<img alt='{$this->owner->ID}_profile_photo' src='{$img->getURL()}' class='member-profile-photo'/>";
        } elseif (!empty($twitter_name)) {
            if ($width < 100) {
                return '<img src="https://twitter.com/' . $twitter_name . '/profile_image?size=normal" />';
            } else {
                return '<img src="https://twitter.com/' . $twitter_name . '/profile_image?size=bigger" />';
            }
        } else {
            if ($width < 100) {
                return "<img src='themes/openstack/images/generic-profile-photo-small.png'/>";
            } else {
                return "<img src='themes/openstack/images/generic-profile-photo.png'/>";
            }
        }
    }

    function ProfilePhotoUrl($width = 100, $generic_photo_type = 'member')
    {
        $img = $this->owner->Photo();
        $twitter_name = $this->owner->TwitterName;
        if (!is_null($img) && $img->exists() && Director::fileExists($img->Filename)) {
            $img = $img->SetWidth($width);

            return $img->getURL();
        } elseif (!empty($twitter_name)) {
            if ($width < 100) {
                return 'https://twitter.com/' . $twitter_name . '/profile_image?size=normal';
            } else {
                return 'https://twitter.com/' . $twitter_name . '/profile_image?size=bigger';
            }
        } elseif ($generic_photo_type == 'speaker') {
            return '/summit/images/generic-speaker-icon.png';
        } else {
            return 'themes/openstack/images/generic-profile-photo.png';
        }
    }

    function getFullName()
    {
        return $this->owner->FirstName . ' ' . $this->owner->Surname;
    }

    function getCurrentPosition()
    {
        $current = $this->getCurrentAffiliation();
        if(is_null($current)) return '';
        $org = $current->Organization();
        $res = '';
        if(!is_null($org))
            $res = $org->Name;
        $job_title = $current->JobTitle;
        if(!empty($job_title))
            $res .= ', '.$job_title;
        return $res;
    }

    // Used to group members by last name when displaying the member listing
    public function getSurnameFirstLetter()
    {
        $firstLetter = $this->owner->Surname[0];
        $firstLetter = strtr($firstLetter,
            'ŠŽšžŸµÀÁÂÃÄÅÈÉÊËÌÍÎÏÐÑÒÓÔÕÖÙÚÛÜÝ',
            'SZszYuAAAAAAEEEEIIIIDNOOOOOUUUUY');
        $firstLetter = strtoupper($firstLetter);

        return $firstLetter;
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName("Bio");
        $fields->addFieldsToTab('Root.Main',
            new HTMLEditorField('Bio', 'Bio: A Little Bit About You <em>(Optional)</em>'));
        $fields->removeByName("TypeOfDirector");
        if ($this->owner->inGroup('board-of-directors')) {
            $fields->addFieldsToTab('Root.Main',
                new TextField('TypeOfDirector', 'Type Of Director: <em>(Optional)</em>'));
        }
    }

    public function getDDLAdminSecurityGroup()
    {
        $groups = array();
        $companyId = $_REQUEST["CompanyId"];
        if (isset($companyId)) {
            $sqlQuery = new SQLQuery();
            $sqlQuery->setSelect("GroupID");
            $sqlQuery->setFrom("Company_Administrators");
            $sqlQuery->setWhere("MemberID={$this->owner->ID} AND CompanyID={$companyId}");
            $groups = $sqlQuery->execute()->keyedColumn();
        }
        $sql_query_groups = new SQLQuery();
        $permissions = "'" . implode("', '", $this->getAdminPermissionSet()) . "'";
        $sql_query_groups->setSelect(array(
            'G.ID',
            'G.Title'
        ));
        $sql_query_groups->setFrom("`Group` G INNER JOIN  ( SELECT DISTINCT(GroupID) FROM `Permission` WHERE `Code` IN ({$permissions})) PG ON PG.GroupID=G.ID");
        $company_security_groups = $sql_query_groups->execute()->map();

        return new MultiDropdownField("AdminSecurityGroup_{$this->owner->ID}", "AdminSecurityGroup_{$this->owner->ID}",
            $company_security_groups, $groups);
    }


    function getAdminPermissionSet()
    {
        $res = array('ADD_COMPANY', 'DELETE_COMPANY', 'EDIT_COMPANY', 'MANAGE_COMPANY_PROFILE', 'MANAGE_COMPANY_LOGOS');
        if ($this->owner->getExtensionInstances()) {
            foreach ($this->owner->getExtensionInstances() as $instance) {
                if (method_exists($instance, 'getAdminPermissionSet') && !($instance instanceof MemberDecorator)) {
                    $instance->setOwner($this->owner);
                    $value = $instance->getAdminPermissionSet($res);
                    if ($value !== null) {
                        $values[] = $value;
                    }
                    $instance->clearOwner();
                }
            }
        }

        return $res;
    }

    /*
     * Get all managed companies on where user is an admin (old way CompanyAdminID and new way through security groups)
     */
    public function getManagedCompanies()
    {

        $query = DB::query("SELECT Company.* from Company INNER JOIN Company_Administrators ON Company_Administrators.CompanyID=Company.ID AND Company_Administrators.MemberID={$this->owner->ID} INNER JOIN (
            SELECT DISTINCT(GroupID) FROM `Permission` WHERE `Code` IN
            ('MANAGE_COMPANY_PROFILE','MANAGE_COMPANY_LOGOS') ) PG ON PG.GroupID = Company_Administrators.GroupID");

        $companies = new ArrayList();

        if (!is_null($query) && $query->numRecords() > 0) {
            for ($i = 0; $i < $query->numRecords(); $i++) {
                $record = $query->nextRecord();
                $companies->push(new $record['ClassName']($record));
            }
        }

        $old_companies = Company::get()->filter(array('CompanyAdminID' => $this->owner->ID));

        $joined_companies = new ArrayList();
        if ($companies && $companies->Count() > 0) {
            foreach ($companies as $company) {
                $joined_companies->push($company);
            }
        }
        if ($old_companies && $old_companies->Count() > 0) {
            foreach ($old_companies as $company) {
                $joined_companies->push($company);
            }
        }

        return $joined_companies;
    }


    public function OrderedAffiliations()
    {
        return $this->owner->Affiliations("", "Current DESC, StartDate DESC, EndDate DESC");
    }

    public function getCurrentAffiliation()
    {
        $current_affiliation = $this->getCurrentAffiliations();

        return $current_affiliation->first();
    }

    public function getCurrentAffiliations()
    {
        $current_date = DateTimeUtils::getCurrentDate();

        return $this->owner->Affiliations(" ( Current=1 OR EndDate > '{$current_date}') ",
            "Current DESC, StartDate DESC, EndDate DESC,LastEdited DESC");
    }

    public function hasCurrentAffiliation($org)
    {
        $org = Convert::raw2sql($org);
        $affiliations = $this->owner->Affiliations();

        $affiliations = $affiliations->filterAny(array(
            'Current' => '1',
            'EndDate' => 'NULL',
        ));

        if (is_numeric($org)) {

            $affiliations = $affiliations->filter(array(
                'OrganizationID' => intval($org)
            ));
        } else {

            $affiliations = $affiliations->innerJoin('Org', 'O.ID = Affiliation.OrganizationID', 'O');
            $affiliations = $affiliations->filter(array(
                'O.Name' => $org
            ));
        }

        return $affiliations->count() > 0;
    }

    public function getCurrentOrganization()
    {
        $current_affiliation = $this->getCurrentAffiliation();

        return $current_affiliation ? $current_affiliation->Organization() : $this->owner->Org();
    }

    public function getOrgName()
    {
        $org = $this->getCurrentOrganization();

        return !is_null($org) ? $org->Name : "";
    }

    public function getCurrentCompany()
    {
        $org = $this->getCurrentOrganization();

        return !is_null($org) ? $org->Name : $this->owner->Org()->Name;
    }

    public function getCurrentJobTitle()
    {
        $a = $this->getCurrentAffiliation();
        if (!is_null($a)) {
            return empty($a->JobTitle) ? $this->owner->JobTitle : $a->JobTitle;
        }

        return $this->owner->JobTitle;
    }

    public function getCurrentRole()
    {
        $a = $this->getCurrentAffiliation();
        if (!is_null($a)) {
            return empty($a->Role) ? $this->owner->Role : $a->Role;
        }

        return $this->owner->Role;
    }

    public function getCurrentCompanies()
    {
        $res = '';
        $current_affiliations = $this->owner->Affiliations("Current=1 OR EndDate IS NULL",
            "Current DESC, StartDate DESC, EndDate DESC,LastEdited DESC");
        if (!is_null($current_affiliations)) {
            foreach ($current_affiliations as $a) {
                $res .= $a->Organization()->Name . ', ';
            }
            $res = trim($res, ' ');
            $res = trim($res, ',');
        } else {
            $res = $this->owner->Org()->Name;
        }

        return $res;
    }

    public function canView($member = null)
    {
        $res = Permission::check("EDIT_COMPANY");

        return $res;
    }

    public function canEdit($member = null)
    {
        $res = Permission::check("EDIT_COMPANY");

        return $res;
    }

    public function isAdmin()
    {
        return Permission::checkMember($this->owner, 'ADMIN');
    }
}