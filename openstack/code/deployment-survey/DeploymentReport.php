<?php
/**
 * Defines the DeploymentSurveyPage
 */

class DeploymentSurveyReport extends Page {
   static $db = array(
	 );
   static $has_one = array(
   );

 	function getCMSFields() {
    	$fields = parent::getCMSFields();

    	return $fields;
 	}
}

class DeploymentSurveyReport_Controller extends Page_Controller {


	function init() {
	    parent::init();
	}

  function MembersWithPublicDeployments() {
      $MembersWithPublicDeployments = New ArrayList();
      $DeploymentSurveys = DeploymentSurvey::get();
      foreach ($DeploymentSurveys as $CurrentSurvey) {
          $PublicDeployments = Deployment::get()->filter(array('DeploymentSurveyID' => $CurrentSurvey->ID, 'IsPublic' => 1));
          If($PublicDeployments) {
            $Member = Member::get()->byID($CurrentSurvey->MemberID);
            $MembersWithPublicDeployments->push($Member);
            echo $Member->FirstName." has public deployments on DeploymentSurvey ID ".$CurrentSurvey->ID.'<br/>';
          }
      }

      return $MembersWithPublicDeployments;

  }

  function PublicDeployments() {
      $Deployments = Deployment::get()->filter('IsPublic', 1)->sort('DeploymentType');
      return $Deployments;
  }  

  function DeploymentsAsJSON() {
      $Deployments = Deployment::get();
      $f = new JSONDataFormatter(); 
      echo $f->convertDataObjectSet($Deployments);
  }

}