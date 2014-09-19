<?php

class DeploymentSurveyOrgInfoForm extends Form {

   function __construct($controller, $name) {

      // Define fields //////////////////////////////////////
	   Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
	   Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
	   Requirements::javascript('themes/openstack/javascript/deployment.survey.org.info.form.js');

      if (is_array($this->allowedCountries)) {
         $allowedCountries = $this->allowedCountries;
      }
      $CountryCodes = CountryCodes::$iso_3166_countryCodes;
	  $org_field = null;
	  $current_user = Member::currentUser();
	  $current_affiliations = $current_user->getCurrentAffiliations();
	  $org_field_name = 'Organization';
	  if(!$current_affiliations)
	        $org_field =  new TextField('Organization','Your Organization Name');
	  else{
		  if(count($current_affiliations) > 1){
			  $source = array();
			  foreach($current_affiliations as $a){
				  $org = $a->Organization();
				  $source[$org->ID] = $org->Name;
			  }
			  $source['0'] = "-- New One --";
			  $org_field_name = 'OrgID';
			  $ddl = new DropdownField('OrgID','Your Organization',$source,'',null,'-- Select Your Organization --');
			  $org_field = new FieldGroup();
			  $org_field->push($ddl);
			  $org_field->push($txt = new TextField('Organization',''));
			  $txt->addExtraClass('new-org-name');
		  }
		  else{
			  $org_field = new TextField('Organization','Your Organization Name',$current_user->getOrgName());
		  }
	  }

      $fields = new FieldList (
	    $org_field,
        new LiteralField('Break','<p>(Changing your organization here will also update your OpenStack Foundation profile.)</p>') ,
        new LiteralField('Break', ColumnFormatter::$left_column_start),
        new DropdownField(
            'Industry',
            'Industry',
            DeploymentSurvey::$industry_options
        ),
        new TextField('OtherIndustry','Other Industry'),
        new LiteralField('Break', ColumnFormatter::$right_column_start),
        new DropdownField(
            'OrgSize',
            'Organization Size',
            DeploymentSurvey::$organization_size_options
        ),
        new LiteralField('Break', ColumnFormatter::$end_columns),
        new LiteralField('Break', '<hr/>'),
        new LiteralField('Break','<p>Where is the primary location or headquarters of your organization?</p>') ,
        new LiteralField('Break', ColumnFormatter::$left_column_start),
        new TextField('PrimaryCity','City'),
        new LiteralField('Break', ColumnFormatter::$right_column_start),
        new TextField('PrimaryState','State/Province'),
        new LiteralField('Break', ColumnFormatter::$end_columns),
        new DropdownField(
            'PrimaryCountry',
            'Country',
            $CountryCodes
        ),
        new LiteralField('Break', '<hr/>'),
        new TextField('Title','Your Job Title'),
        new CheckboxSetField('OpenStackInvolvement','What best describes your involvement with OpenStack?',DeploymentSurvey::$openstack_involvement_options),
        new CheckboxSetField('InformationSources','Where do you go for information about using OpenStack?',DeploymentSurvey::$information_options),
        new TextField('OtherInformationSources','Other information sources'),
        new OptionSetField(
            'UserGroupMember',
            'Are you a member of a user group?',
            array('1' => 'Yes','0' => 'No'),
            0
        ),
        new TextField('UserGroupName','If yes, which user group (<a href="http://wiki.openstack.org/OpenStackUserGroups" target="_blank">User Group List</a>)?'),
        new CheckboxField('OkToContact','The OpenStack Foundation and User Committee can communicate with me in the future about my usage'),
        new LiteralField('Break', '<hr/>'),
        new LiteralField('Break', '<p>We would love to hear how OpenStack and the OpenStack Foundation can better meet your needs. These free-form questions are optional, but will provide valuable insights.</p>'),
        new TextAreaField('FurtherEnhancement','What areas of OpenStack software require further enhancement? (optional)'),
        new TextAreaField('FoundationUserCommitteePriorities','What should be the priorities for the Foundation and User Committee during the coming year? (optional)'),
        new TextAreaField('WhatDoYouLikeMost','What do you like most about OpenStack? (optional)'),
        new CheckboxSetField(
            'BusinessDrivers',
            'What are your business drivers for using OpenStack? (optional)',
            DeploymentSurvey::$business_drivers_options),
        new TextField('OtherBusinessDrivers','Other business drivers')
      );

      // $prevButton = new CancelFormAction($controller->Link().'Login', 'Previous Step');
      $nextButton = new FormAction('NextStep', '  Next Step  ');

      $actions = new FieldList(
          $nextButton
      );

      // Create Validators
      $validator = new RequiredFields($org_field_name,'Title','PrimaryCity','PrimaryCountry','UserGroupMember');
      parent::__construct($controller, $name, $fields, $actions, $validator);
   }

   function forTemplate() {
      return $this->renderWith(array(
         $this->class,
         'Form'
      ));
   }
}