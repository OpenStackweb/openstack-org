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
 * Class Company
 */
class Company extends DataObject implements PermissionProvider,IEntity
{

    public function getIdentifier() {
        return $this->ID;
    }

    static $has_one = array(

        'CompanyListPage' => 'CompanyListPage',
        'Logo'            => 'BetterImage',
        'BigLogo'         => 'BetterImage',
        'Submitter'       => 'Member',
        'CompanyAdmin'    => 'Member'
    );

    static $many_many_extraFields = array(
        'Administrators' => array(
            'GroupID' => "Int",
        ),
    );

    private static $db = array(
        'Name' => 'Text',
        'URL' => 'Text',
        'DisplayOnSite' => 'Boolean',
        'Featured' => 'Boolean',
        'City' => 'Varchar(255)',
        'State' => 'Varchar(255)',
        'Country' => 'Varchar(255)',
        'Description' => 'HTMLText',
        'Industry' => 'Text',
        'Products' => 'HTMLText',
        'Contributions' => 'HTMLText',
        'ContactEmail' => 'Text',
        'MemberLevel' => "Enum('Platinum, Gold, StartUp, Corporate, Mention, None','None')",
        'AdminEmail' => 'Text',
        'URLSegment' => 'Text',
        'Color' => 'Text',
        //marketplace updates
        'Overview' => 'HTMLText',
        'Commitment' => 'HTMLText',
        'CommitmentAuthor' => 'Varchar(255)',
    );

    private static $defaults = array(
        "Color" => 'C34431',
    );

    private static $has_many = array(
        'Photos'   => 'BetterImage',
        'Contracts' => 'Contract',
        'Services'  => 'CompanyService',
    );

    private static $many_many = array(
        'Administrators' => 'Member'
    );

    private static $singular_name = 'Company';
    private static $plural_name = 'Companies';

    //Administrators Security Groups
    private static $belongs_many_many = array(
        'SponsorsPages' => 'SponsorsPage'
    );
    private static $summary_fields = array(
        'Name' => 'Company',
        'MemberLevel' => 'MemberLevel'
    );


    function providePermissions()
    {
        return array(
            "ADD_COMPANY" => array(
                'name' => 'Add Companies',
                'category' => 'Company Management',
                'help' => 'Allows to add a new company',
                'sort' => 0
            ),
            "DELETE_COMPANY" => array(
                'name' => 'Delete Companies',
                'category' => 'Company Management',
                'help' => 'Allows to delete a company',
                'sort' => 0
            ),
            "EDIT_COMPANY" => array(
                'name' => 'Edit Companies',
                'category' => 'Company Management',
                'help' => 'Allows to edit a company',
                'sort' => 0
            ),
            "MANAGE_COMPANY_PROFILE" => array(
                'name' => 'Manage Company Profile',
                'category' => 'Company Management',
                'help' => 'Allows to manage a company profile',
                'sort' => 0
            ),
            "MANAGE_COMPANY_LOGOS" => array(
                'name' => 'Manage Company Logo',
                'category' => 'Company Management',
                'help' => 'Allows to manage a company Logo',
                'sort' => 0
            ),
        );
    }

    public function getCompanyColor()
    {
        return empty($this->Color) ? "C34431" : $this->Color;
    }

    public function getCompanyColorRGB()
    {
        $rgb_color = "rgb(195,68,49)";
        if (!empty($this->Color)) {
            if (strlen($this->Color) == 3) {
                $r = hexdec(substr($this->Color, 0, 1) . substr($this->Color, 0, 1));
                $g = hexdec(substr($this->Color, 1, 1) . substr($this->Color, 1, 1));
                $b = hexdec(substr($this->Color, 2, 1) . substr($this->Color, 2, 1));
            } else {
                $r = hexdec(substr($this->Color, 0, 2));
                $g = hexdec(substr($this->Color, 2, 2));
                $b = hexdec(substr($this->Color, 4, 2));
            }
            $rgb_color = 'rgb(' . $r . ',' . $g . ',' . $b . ')';
        }

        return $rgb_color;
    }

    function getCMSFields()
    {

        $_REQUEST["CompanyId"] = $this->ID;

        $large_logo = new UploadField('BigLogo', 'Large Company Logo');
        $large_logo->setFolderName('companies/main_logo');
        $large_logo->setAllowedFileCategories('image');

        $small_logo = new UploadField('Logo', 'Small Company Logo');
        $small_logo->setAllowedFileCategories('image');
        //logo validation rules
        $large_logo_validator = new Upload_Image_Validator();
        $large_logo_validator->setAllowedExtensions(array('jpg', 'png', 'jpeg'));
        $large_logo_validator->setAllowedMaxImageWidth(500);
        $large_logo->setValidator($large_logo_validator);

        $small_logo_validator = new Upload_Image_Validator();
        $small_logo_validator->setAllowedExtensions(array('jpg', 'png', 'jpeg'));
        $small_logo_validator->setAllowedMaxImageWidth(200);
        $small_logo->setValidator($small_logo_validator);


        $fields = new FieldList(new TabSet(
            $name = "Root",
            new Tab(
                $title = 'Company',
                new HeaderField("Company Data"),
                new TextField('Name', 'Company Name'),
                new TextField('URLSegment', 'Unique page name for this company profile (ie: company-name)'),
                new TextField ('URL', 'Company Web Address (URL)'),
                $level = new DropDownField(
                    'MemberLevel',
                    'OpenStack Foundation Member Level',
                    $this->dbObject('MemberLevel')->enumValues()
                ),
                new ColorField("Color", "Company Color"),
                new CheckboxField ('DisplayOnSite', 'List this company on openstack.org'),
                new CheckboxField ('Featured', 'Include this company in featured companies area'),
                new LiteralField('Break', '<hr/>'),
                $this->canEditLogo() ? $large_logo : new LiteralField('Space', '<br/>'),
                $this->canEditLogo() ? $small_logo : new LiteralField('Space', '<br/>'),
                new TextField('Industry', 'Industry (<4 Words)'),
                new HtmlEditorField('Description', 'Company Description'),
                new HtmlEditorField('Contributions', 'How you are contributing to OpenStack (<150 words)'),
                new HtmlEditorField('Products', 'Products/Services Related to OpenStack (<100 words)'),
                new HtmlEditorField('Overview', 'Company Overview'),
                new TextField('CommitmentAuthor', 'Commitment Author (Optional)'),
                new HtmlEditorField('Commitment', "OpenStack Commitment"),
                new LiteralField('Break', '<hr/>'),
                new TextField('ContactEmail', 'Best Contact email address (optional)')
            )
        ));

        $level->setEmptyString('-- Choose One --');

        if ($this->ID > 0) {

            $admin_list = $this->Administrators()->sort('ID');
            $query = $admin_list->dataQuery();

            $query->groupby('MemberID');

            $admin_list = $admin_list->setDataQuery($query);

            $config = GridFieldConfig_RelationEditor::create(PHP_INT_MAX);

            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');


            $config->getComponentByType('GridFieldDataColumns')->setDisplayFields(
                [
                    'FirstName'             => 'First Name',
                    'Surname'               => 'Last Name',
                    'Email'                 => 'Email',
                    'DDLAdminSecurityGroup' => 'Security Group'
                ]);

            $admins    = new GridField('Administrators', 'Company Administrators', $admin_list, $config);
            $contracts = new GridField("Contracts", "Contracts", $this->Contracts(), GridFieldConfig_RecordEditor::create(10));

            $fields->addFieldsToTab('Root.Administrators',
                array(
                    new HeaderField("Companies Administrators"),
                    $admins
                )
            );

            $fields->addFieldsToTab('Root.Contracts',
                array(
                    new HeaderField("Companies Contracts"),
                    $contracts
                )
            );
        }

        return $fields;
    }

    function canEditLogo()
    {
        $MemberID = Member::currentUserID();

        return $this->CompanyAdminID == $MemberID || Permission::check("MANAGE_COMPANY_LOGOS") || Permission::check("MANAGE_COMPANY_PROFILE") || $this->PermissionCheck(array(
            "MANAGE_COMPANY_PROFILE",
            'MANAGE_COMPANY_LOGOS'
        ));
    }

    private function PermissionCheck(array $permission_2_check)
    {
        //check groups
        $current_user_id = intval(Member::currentUserID());
        $admins_groups_for_user = $this->getManyManyComponents("Administrators", "MemberID={$current_user_id}", "ID");
        if ($admins_groups_for_user) {//current user has some admin level
            foreach ($admins_groups_for_user as $admin_group) {
                $group_id = intval($admin_group->GroupID);
                $permissions = Permission::get()->filter('GroupID', $group_id);
                foreach ($permissions as $p) {
                    if (in_array($p->Code, $permission_2_check)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function validate()
    {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;
        if (is_subclass_of(Controller::curr(), "LeftAndMain")) {
            if (empty($this->Name)) {
                return $valid->error('Name is empty!');
            }

            if (empty($this->URL)) {
                return $valid->error('URL is empty!');
            }

            if (empty($this->URLSegment)) {
                $filter = URLSegmentFilter::create();
                $slug = $filter->filter($this->Name);

                // Fallback to generic page name if path is empty (= no valid, convertable characters)
                if (!$slug || $slug == '-' || $slug == '-1') {
                    return $valid->error(sprintf('invalid Autogenerated URLSegment (%s) ! please set one by hand.',
                        $slug));
                }

                $this->URLSegment = $slug;
            }

            if (empty($this->URLSegment)) {
                return $valid->error('URLSegmen is empty!');
            }

            if ($this->LookForExistingURLSegment($this->URLSegment)) {
                return $valid->error(sprintf('invalid URLSegment: %s already exists! choose another one',
                    $this->URLSegment));
            }

        }
        return $valid;
    }

    //Generate Yes/No for DOM / Complex Table Field

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        if (isset($_REQUEST["action_doDelete"])) {
            return new RequiredFields();
        }

        $validator_fields = new RequiredFields(array('Logo'));

        return $validator_fields;
    }

    //Test whether the URLSegment exists already on another Product

    public function DisplayNice()
    {
        return $this->DisplayOnSite ? 'Yes' : 'No';
    }

    function onBeforeWrite() {
        parent::onBeforeWrite();

        // Assign an Admin using the provided email address
        if ($this->AdminEmail) {
            $EmailAddress = Convert::raw2sql($this->AdminEmail);
            $Member = Member::get()->filter('Email',$EmailAddress)->first();
            if ($Member) {
                $this->CompanyAdminID = $Member->ID;
            }
        } else {
            $this->CompanyAdminID = "";
        }

    }
    //Test whether the URLSegment exists already on another Product
    function LookForExistingURLSegment($URLSegment)
    {
        return Company::get()->filter(array('URLSegment' => $URLSegment, 'ID:not' => $this->ID))->first();
    }

    function AdminName()
    {
        $Admin = Member::get()->byID($this->CompanyAdminID);
        if ($Admin) {
            return $Admin->FirstName . " " . $Admin->Surname;
        } else {
            return "(no member assigned)";
        }
    }

    function EditLink()
    {
        $CompaniesPage = CompanyListPage::get()->first();

        return $CompaniesPage->Link() . "edit/" . $this->ID;
    }

    //Generate the link for this product
    function ShowLink()
    {
        $CompaniesPage = CompanyListPage::get()->first();
        if ($this->Description) {
            return $CompaniesPage->Link() . "profile/" . $this->URLSegment;
        } else {
            return $this->URL;
        }
    }

    //helper function to create Drop Down for Sponsorship type
    function IsExternalUrl()
    {
        return !$this->Description ? true : false;
    }

    //helper function to create Drop Down for Logo Size

    public function getInputSubmitPageUrl()
    {
        $type = null;
        $pageId = Convert::raw2sql($_REQUEST["PageId"]);
        if (isset($pageId)) {
            $sqlQuery = new SQLQuery();
            $sqlQuery->setSelect("SubmitPageUrl");
            $sqlQuery->setFrom("SummitSponsorPage_Companies");
            $sqlQuery->setWhere("CompanyID={$this->ID} AND SummitSponsorPageID={$pageId}");
            $type = $sqlQuery->execute()->value();
        }

        return new TextField("SubmitPageUrl_{$this->ID}", "SubmitPageUrl_{$this->ID}", $type, 255);
    }

    public function getDDLSponsorshipType()
    {
        $type = null;
        $pageId = Convert::raw2sql($_REQUEST["PageId"]);

        if (isset($pageId)) {
            $sqlQuery = new SQLQuery();
            $sqlQuery->setSelect("SponsorshipType");
            $sqlQuery->setFrom("SummitSponsorPage_Companies");
            $sqlQuery->setWhere("CompanyID={$this->ID} AND SummitSponsorPageID={$pageId}");
            $type = $sqlQuery->execute()->value();
        }

        return new DropdownField("SponsorshipType_{$this->ID}", "SponsorshipType_{$this->ID}", array(
            'Headline' => 'Headline',
            'Premier' => 'Premier',
            'Event' => 'Event',
            'Startup' => 'Startup',
            'InKind' => 'In Kind',
            'Spotlight' => 'Spotlight',
            'Media' => 'Media',
            '' => '--NONE--'
        ), $type);
    }

    public function getDDLLogoSize()
    {
        $size = null;
        $pageId = Convert::raw2sql($_REQUEST["PageId"]);

        if (isset($pageId)) {
            $sqlQuery = new SQLQuery();
            $sqlQuery->setSelect("LogoSize");
            $sqlQuery->setFrom("SummitSponsorPage_Companies");
            $sqlQuery->setWhere("CompanyID={$this->ID} AND SummitSponsorPageID={$pageId}");
            $size = $sqlQuery->execute()->value();
            if (is_null($size)) {
                $size = 'None';
            }
        }

        $sizes = array(
            'Small' => 'Small',
            'Medium' => 'Medium',
            'Large' => 'Large',
            'Big' => 'Big',
            'None' => '--NONE--'
        );

        return new DropdownField("LogoSize_{$this->ID}", "LogoSize_{$this->ID}", $sizes, $size);
    }

    public function SidebarLogoPreview()
    {
        $img = $this->Logo();
        $img = $img->exists() ? $img : $this->Logo();
        if (isset($img) && Director::fileExists($img->Filename) && $img->exists()) {
            $img = $img->SetWidth(100);

            return "<img alt='{$this->Name}_sidebar_logo' src='{$img->getURL()}' class='sidebar-logo-company company-logo'/>";
        }

        return 'missing';
    }

    public function SmallLogoPreview($width = 210)
    {
        $img = $this->Logo();
        $img = $img->SetWidth($width);
        if (isset($img) && Director::fileExists($img->Filename) && $img->exists()) {
            return "<img alt='{$this->Name}_small_logo' src='{$img->getURL()}' class='small-logo-company company-logo'/>";
        }

        return 'missing';
    }

    public function MediumLogoPreview()
    {
        $img = $this->BigLogo();
        $img = $img->exists() ? $img : $this->Logo();
        if (isset($img) && Director::fileExists($img->Filename) && $img->exists()) {
            $img = $img->SetWidth(210);

            return "<img alt='{$this->Name}_medium_logo' src='{$img->getURL()}' class='medium-logo-company company-logo'/>";
        }

        return 'missing';
    }

    public function MediumLogoUrl()
    {
        $img = $this->BigLogo();
        $img = $img->exists() ? $img : $this->Logo();
        if (isset($img) && Director::fileExists($img->Filename) && $img->exists()) {
            $img = $img->SetWidth(210);

            return $img->getURL();
        }

        return '#';
    }

    public function LargeLogoPreview()
    {
        $img = $this->BigLogo();
        $img = $img->exists() ? $img : $this->Logo();
        if (isset($img) && Director::fileExists($img->Filename) && $img->exists()) {
            $img = $img->SetWidth(300);

            return "<img alt='{$this->Name}_large_logo' src='{$img->getURL()}' class='large-logo-company company-logo'/>";
        }

        return 'missing';
    }

    public function SubmitLandPageUrl()
    {
        $url = $this->URL;
        $page = Director::get_current_page();
        $res = $page->getManyManyComponents("Companies", "CompanyID={$this->ID}", "ID")->First();
        $submit_url = $res->SubmitPageUrl;
        if (isset($submit_url) && $submit_url != '') {
            return $submit_url;
        }

        return $url;
    }

    public function SubmitLogo()
    {
        return $this->BigLogoPreview();
    }

    //Security checks

    public function BigLogoPreview()
    {
        $img = $this->BigLogo();
        if (isset($img) && Director::fileExists($img->Filename) && $img->exists()) {
            $img = $img->SetWidth(300);

            return "<img alt='{$this->Name}_big_logo' src='{$img->getURL()}' class='big-logo-company company-logo'/>";
        }

        return 'missing';
    }

    function onAfterWrite()
    {
        parent::onAfterWrite();

        if (Controller::curr() instanceof CompanyAdmin) { // check if we are on admin (CMS side)
            //update all relationships with Administrators
            foreach ($this->Administrators() as $member) {
                if (isset($_REQUEST["AdminSecurityGroup_{$member->ID}"])) {
                    $groups_ids = $_REQUEST["AdminSecurityGroup_{$member->ID}"];
                    if (is_array($groups_ids) && count($groups_ids) > 0) {
                        DB::query("DELETE FROM Company_Administrators WHERE CompanyID={$this->ID} AND MemberID={$member->ID};");
                        foreach ($groups_ids as $group_id) {
                            $group_id = intval(Convert::raw2sql($group_id));
                            DB::query("INSERT INTO Company_Administrators (GroupID,CompanyID,MemberID) VALUES ({$group_id},{$this->ID},{$member->ID});");
                        }
                    }
                } else {
                    DB::query("DELETE FROM Company_Administrators WHERE CompanyID={$this->ID} AND MemberID={$member->ID};");
                    DB::query("INSERT INTO Company_Administrators (GroupID,CompanyID,MemberID) VALUES (0,{$this->ID},{$member->ID});");
                }
            }
        }
    }

    public function canCreate($member = null)
    {
        $MemberID = Member::currentUserID();

        return $this->CompanyAdminID == $MemberID || Permission::check("ADD_COMPANY") || $this->PermissionCheck(array("ADD_COMPANY"));
    }

    public function canEdit($member = null)
    {
        $MemberID = Member::currentUserID();

        return $this->CompanyAdminID == $MemberID || Permission::check("EDIT_COMPANY") || $this->PermissionCheck(array("EDIT_COMPANY"));
    }

    /*
     * Helper method to check if current user has the permissions
     * passed by arg ($permission_2_check) on the company admin security group that is currently assigned
     */

    public function canDelete($member = null)
    {
        $MemberID = Member::currentUserID();

        return $this->CompanyAdminID == $MemberID || Permission::check("DELETE_COMPANY") || $this->PermissionCheck(array("DELETE_COMPANY"));
    }

    public function canView($member = null)
    {
        $MemberID = Member::currentUserID();

        return $this->CompanyAdminID == $MemberID || Permission::check("EDIT_COMPANY") || $this->PermissionCheck(array("EDIT_COMPANY"));
    }

    function IsCompanyAdmin($memberId)
    {
        $admin = $this->getManyManyComponents("Administrators", "MemberID={$memberId}", "ID")->First();
        if ($admin) {//current user has some admin level
            $group_id = $admin->GroupID;

            return $group_id > 0;
        }

        return false;
    }

    function canEditProfile()
    {
        $MemberID = Member::currentUserID();

        return $this->CompanyAdminID == $MemberID || Permission::check("MANAGE_COMPANY_PROFILE") || $this->PermissionCheck(array("MANAGE_COMPANY_PROFILE"));
    }

    /**
     * @param int $memberId
     * @return Group[]|null
     */
    function getAdminGroupsByMember($memberId)
    {
        $associations = $this->getManyManyComponents("Administrators", "MemberID={$memberId}", "ID");
        if ($associations) {
            $res = array();
            foreach ($associations as $a) {
                $g = Group::get()->byID((int)$a->GroupID);
                if ($g) {
                    $res[$g->Code] = $g;
                }
            }

            return $res;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isCOAPartner(){
        foreach (COALandingPage::get() as $coa_page) { // we check all coa pages as there are duplicates (Students)
            if (is_null($coa_page)) continue;
            if ($coa_page->TrainingPartners()->count() == 0) continue;

            if (intval($coa_page->TrainingPartners()->filter('CompanyID', $this->ID)->count()) > 0)
                return true;
        }

        return false;
    }
}