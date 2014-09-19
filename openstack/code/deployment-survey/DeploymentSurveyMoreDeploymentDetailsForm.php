<?php

class DeploymentSurveyMoreDeploymentDetailsForm extends Form
{

	function __construct($controller, $name)
	{

		$CurrentDeploymentID = Session::get('CurrentDeploymentID');

		// Define fields //////////////////////////////////////

		$fields = new FieldList (
			new LiteralField('Break', '<p>The information below will help us better understand
        the most common configuration and component choices OpenStack deployments are using.</p>'),

			new LiteralField('Break', ColumnFormatter::$left_column_start),
			new CheckboxSetField(
				'Hypervisors',
				'If you are using OpenStack Compute, which hypervisors are you using?',
				Deployment::$hypervisors_options
			),
			new TextField('OtherHypervisor', 'Other Hypervisor'),
			new CheckboxSetField(
				'NetworkDrivers',
				'Do you use nova-network, or OpenStack Network (Neutron)? If you are using OpenStack Network (Neutron), which drivers are you using?',
				Deployment::$network_driver_options
			),
			new TextField('OtherNetworkDriver', 'Other Network Driver'),
			new TextAreaField(
				'WhyNovaNetwork',
				'If you are using nova-network and not OpenStack Networking (Neutron), what would allow you to migrate? (optional)'),

			new LiteralField('Break', ColumnFormatter::$right_column_start),
			new CheckboxSetField(
				'BlockStorageDrivers',
				'If you are using OpenStack Block Storage, which drivers are you using?',
				Deployment::$block_storage_divers_options
			),
			new TextField('OtherBlockStorageDriver', 'Other Block Storage Driver'),
			new CheckboxSetField(
				'IdentityDrivers',
				'If you are using OpenStack Identity which OpenStack Identity drivers are you using?',
				Deployment::$identity_driver_options
			),
			new TextField('OtherIndentityDriver', 'Other/Custom Identity Driver'),
			new LiteralField('Break', ColumnFormatter::$end_columns),


			new LiteralField('Break', '<hr/>'),
			new LiteralField('Break', ColumnFormatter::$left_column_start),
			new CheckboxSetField(
				'SupportedFeatures',
				'Which of the following compatibility APIs does/will your deployment support?',
				Deployment::$deployment_features_options_new
			),
			new LiteralField('Break', ColumnFormatter::$right_column_start),
			new CheckboxSetField(
				'DeploymentTools',
				'What tools are you using to deploy/configure your cluster?',
				Deployment::$deployment_tools_options
			),
			new TextField('OtherDeploymentTools', 'Other tools'),
			new LiteralField('Break', ColumnFormatter::$end_columns),

			new LiteralField('Break', ColumnFormatter::$left_column_start),
			new CheckboxSetField(
				'OperatingSystems',
				'What is the main Operating System you are using to run your OpenStack cloud?',
				Deployment::$operating_systems_options
			),
			new TextField('OtherOperatingSystems', 'Other Operating System'),
			new LiteralField('Break', ColumnFormatter::$right_column_start),
			new LiteralField('Break', ColumnFormatter::$end_columns),


			new LiteralField('Break', '<hr/>'),
			new LiteralField('Break', '<p>Please provide the following information about the
        size and scale of this OpenStack deployment. This information is optional, but will
        be kept confidential and never published in connection with your organization.</p>'),
			new LiteralField('Break', '<p><strong>If using OpenStack Compute, what’s the size of your cloud?</strong></p>'),
			new LiteralField('Break', ColumnFormatter::$left_column_start),
			new DropdownField(
				'ComputeNodes',
				'Physical compute nodes',
				Deployment::$compute_nodes_options
			),
			new LiteralField('Break', ColumnFormatter::$right_column_start),
			new DropdownField(
				'ComputeCores',
				'Processor cores',
				Deployment::$compute_cores_options
			),
			new LiteralField('Break', ColumnFormatter::$end_columns),
			new DropdownField(
				'ComputeInstances',
				'Number of instances',
				Deployment::$compute_instances_options
			),
			new DropdownField(
				'BlockStorageTotalSize',
				'If using OpenStack Block Storage, what’s the size of your cloud by total storage in terabytes?',
				Deployment::$storage_size_options
			),
			new LiteralField('Break', '<p><strong>If using OpenStack Object Storage, what’s the size of your cloud?</strong></p>'),
			new LiteralField('Break', ColumnFormatter::$left_column_start),
			new DropdownField(
				'ObjectStorageSize',
				'Total storage in terabytes',
				Deployment::$storage_size_options
			),
			new LiteralField('Break', ColumnFormatter::$right_column_start),
			new DropdownField(
				'ObjectStorageNumObjects',
				'Total objects stored',
				Deployment::$stoage_objects_options
			),
			new LiteralField('Break', ColumnFormatter::$end_columns),
			new DropdownField(
				'NetworkNumIPs',
				'If using OpenStack Network, what’s the size of your cloud by number of fixed/floating IPs?',
				Deployment::$network_ip_options
			)
		);

		$saveButton = new FormAction('SaveDeployment', 'Save Deployment');
		$cancelButton = new CancelFormAction($controller->Link() . 'Deployments', 'Cancel');

		$actions = new FieldList(
			$saveButton, $cancelButton
		);

		// Create Validators
		$validator = new RequiredFields();


		parent::__construct($controller, $name, $fields, $actions, $validator);

		if ($CurrentDeploymentID) {
			//Populate the form with the current members data
			if ($Deployment = $this->controller->LoadDeployment($CurrentDeploymentID)) {
				$this->loadDataFrom($Deployment->data());
			} else {
				// HTTP ERROR
				return $this->httpError(403, 'Access Denied.');
			}
		}

	}

	function SaveDeployment($data, $form)
	{

		$id = Session::get('CurrentDeploymentID');

		// Only loaded if it belongs to current user
		$Deployment = $form->controller->LoadDeployment($id);

		$form->saveInto($Deployment);
		$Deployment->write();

		Session::clear('CurrentDeploymentID');
		Controller::curr()->redirect($form->controller->Link() . 'Deployments');
	}

	function Cancel($data, $form)
	{
		Controller::curr()->redirect($form->controller->Link() . 'Deployments');
	}

	function forTemplate()
	{
		return $this->renderWith(array(
			$this->class,
			'Form'
		));
	}

}