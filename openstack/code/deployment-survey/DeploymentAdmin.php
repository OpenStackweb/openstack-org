<?php

/**
 * Class DeploymentAdmin
 */
final class DeploymentAdmin extends ModelAdmin {
    
    public static $managed_models = array(
        'Deployment',
        'DeploymentSurvey'
    );

	public $showImportForm = false;
    static $url_segment    = 'deployments';
    static $menu_title     = 'Deployments';

	/**
	 * @param string $collection_controller_class Override for controller class
	 */
	//public static $collection_controller_class = "DeploymentAdmin_CollectionController";
}

/**
 * Class DeploymentAdmin_CollectionController
 */
/*final class DeploymentAdmin_CollectionController extends ModelAdmin_CollectionController{
	public function CreateForm() {
		if($this->modelClass==='DeploymentSurvey')
			return false;
		return parent::CreateForm();
	}
}*/