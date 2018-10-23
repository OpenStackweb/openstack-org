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
require_once 'Zend/Date.php';

class CompanyListPage extends Page
{
    static $db = array();
    static $has_one = array();
    static $has_many = array(
        'Company'   => 'Company'
    );

    static $many_many = array(
        'Donors'    => 'Company'
    );

    //sponsor type
    static $many_many_extraFields = array(
        'Donors' => array(
            'SortOrder' => 'Int',
        )
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $companiesTable = new GridField('Company', 'Company',$this->Company());
        $fields->addFieldToTab('Root.Companies', $companiesTable);

        $config = GridFieldConfig_RelationEditor::create(20);
        $config->addComponent($sort = new GridFieldSortableRows('SortOrder'));
        $config->removeComponentsByType('GridFieldEditButton');
        $config->removeComponentsByType('GridFieldAddNewButton');
        $donorsTable = new GridField('Donors', 'Donors', $this->Donors(), $config);
        $fields->addFieldToTab('Root.Companies', $donorsTable);

        return $fields;
    }
}

class CompanyListPage_Controller extends Page_Controller
{

    static $allowed_actions = array(
        'profile',
        'edit',
        'save',
        'CompanyEditForm',
    );

    function init()
    {
        parent::init();

        Requirements::css("themes/openstack/css/jquery.autocomplete.css");
	    Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
	    Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
        JSChosenDependencies::renderRequirements();
        JQueryValidateDependencies::renderRequirements();

        Requirements::combine_files('company_list.js', array(
            "themes/openstack/javascript/jquery.autocomplete.min.js",
        ));
    }

    public function EditorToolbar() {
        return HtmlEditorField_Toolbar::create($this, "EditorToolbar");
    }

    function DisplayedCompanies($type)
    {
        if ($type == 'Combined') {

	        $DisplayedCompanies = Company::get()->filter(array( 'DisplayOnSite' => 1 ))->filterAny( array( 'MemberLevel' => array('Startup','Corporate') ))->sort('Name');

        } else {

            $DisplayedCompanies =  Company::get()->filter(array('DisplayOnSite' => 1, 'MemberLevel' => $type ))->sort('Name');
        }
        if ($DisplayedCompanies) {
            return $DisplayedCompanies;
        } else {
            return NULL;
        }
    }

    function MostRecent()
    {

        $DisplayedCompanies =  Company::get()->filter(array('DisplayOnSite' => 1))->sort('Name');
        $DisplayedCompanies->sort('Created');
        $MostRecent = $DisplayedCompanies->Last();
        return $MostRecent;
    }

    function Featured()
    {
        $FeaturedCompanies = Company::get()->filter('Featured' , 1)->sort('Name');
        return $FeaturedCompanies;
    }

    function getDonorsOrdered()
    {
        $DonorCompanies = $this->Donors()->sort('SortOrder');
        return $DonorCompanies;
    }

    //Show the Company detail page using the CompanyListPage_show.ss template
    function profile()
    {
        if ($Company = $this->getCompanyByURLSegment()) {
            $Data = array(
                'Company' => $Company
            );

            //return our $Data to use on the page
            return $this->Customise($Data);
        } else {
            //Company member not found
            return $this->httpError(404, 'Sorry that comapny could not be found');
        }
    }

    // EditCompanyForm
    function CompanyEditForm()
    {
        $current_company= $this->getCompany();
        if(!$current_company){
            $current_company = $this->CurrentCompany();
        }

        if(is_null($current_company))
            return $this->httpError(404, 'Sorry that company could not be found');

        $CompanyEditForm = new CompanyEditForm($this, 'CompanyEditForm',$current_company);
        $CompanyEditForm->disableSecurityToken();
        // Fill in the form
        if ($current_company) {
            Session::set('CompanyID', $current_company->ID);
            $CompanyEditForm->loadDataFrom($current_company, False);
            return $CompanyEditForm;
        } elseif ($this->request->isPost()) {
            // SS is returning to the form controller to post data
            return $CompanyEditForm;
        } else {
            // Attempted to load the edit form, but the id was missing or didn't match an id in the database
            return $this->httpError(404, 'Sorry that company could not be found');
        }
    }

    // Save an edited company
    function save($data, $form)
    {
        $CompanyID = Session::get('CompanyID');
        // Check to see if it is set and numeric
        if ($CompanyID && is_numeric($CompanyID)) {
            // Try to pull the company data record by ID

            $Company =  Company::get()->byID($CompanyID);
            $MemberID = Member::currentUserID();
            $allow = $Company->CompanyAdminID == $MemberID;
            if (!$allow) {
                //check groups
                $allow = $Company->canEditProfile() || $Company->canEditLogo();
            }
            // Check to see if the currently logged in member is an admin for this company
            if ($allow) {
                // Load the data from the form and save the edits to the company
                $form->saveInto($Company);
                $Company->write();

                $this->setMessage('Success', 'Your edits have been saved.');

                Session::clear('CompanyID');

                $this->redirectBack();
            } else {
                $this->setMessage('Error', 'You do not seem to have permission to edit this company.');
                $this->redirectBack();
            }

        } else {
            $this->setMessage('Error', 'There was an error saving your edits.');
            $this->redirectBack();
        }

    }

    public function isCompanyAdmin()
    {
        if (($company = $this->getCompany()) && ($MemberID = Member::currentUserID())) {
           return $company->canEditProfile() || $company->canEditLogo();
        } else {
            return false;
        }
    }

    // Check to see if a member is logged in and allowed to edit this company
    public function canEditCompanyProfile(){
        if (($Company = $this->getCompany()) && ($MemberID = Member::currentUserID())) {
            return $Company->canEditProfile() || $Company->canEditLogo();
        } else {
            return false;
        }
    }

    //Get the current Company from the URL, if any
    public function getCompany()
    {
        $params = $this->getURLParams();
        if (is_numeric($params['ID']) && $Company = Company::get()->byID((int)$params['ID'])) {
            Session::set('CompanyID', $Company->ID);
            return $Company;
        }
        return null;
    }

    //Get the current Company from the URL, if any
    public function getCompanyByURLSegment()
    {
        $Params = $this->getURLParams();
        $Segment = convert::raw2sql($Params['ID']);

        if ($Params['ID'] && $Company =  Company::get()->filter('URLSegment',$Segment)->first()) {
            return $Company;
        }
    }

    //Return our custom breadcrumbs
    public function Breadcrumbs()
    {

        //Get the default breadcrumbs
        $Breadcrumbs = parent::Breadcrumbs();

        if ($Company = $this->getCompany()) {
            //Explode them into their individual parts
            $Parts = explode(SiteTree::$breadcrumbs_delimiter, $Breadcrumbs);

            //Count the parts
            $NumOfParts = count($Parts);

            //Change the last item to a link instead of just text
            $Parts[$NumOfParts - 1] = ("<a href=\"" . $this->Link() . "\">" . $this->Title . "</a>");

            //Add our extra piece on the end
            $Parts[$NumOfParts] = $Company->Name;

            //Return the imploded array
            $Breadcrumbs = implode(SiteTree::$breadcrumbs_delimiter, $Parts);
        }

        return $Breadcrumbs;
    }

    function CurrentCompany()
    {
        $CompanyID = Session::get('CompanyID');
        if ($CompanyID && is_numeric($CompanyID)) {
            $Company = Company::get()->byID((int)$CompanyID);
            return $Company;
        }
        return null;
    }
}