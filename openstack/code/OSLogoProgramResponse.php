<?php

class OSLogoProgramResponse extends DataObject {

	static $db = array(
		'FirstName' => 'Text',
		'Surname' => 'Text',
		'Email' => 'Text',
		'Phone' => 'Text',
        'Program' => 'Text',
		'CurrentSponsor' => 'Boolean',
		'CompanyDetails' => 'Text',
        'Product' => 'Text',
		'Category' => 'Text',
		'Regions' => 'Text',
		'APIExposed' => 'Boolean',
		'OtherCompany' => 'Text',
		'Projects' => 'Text'
	);

	static $has_one = array ( 
		'Company' => 'Company' 
	); 

	static $singular_name = 'OSLogoProgramResponse';
	static $plural_name = 'OSLogoProgramResponses';

	public static $avialable_categories = array (
		'Public Clouds' => 'Public Clouds',
		'Distributions' => 'Distributions',
		'Converged Appliances' => 'Converged Appliances',
		'Storage' => 'Storage',
		'Consultants & System Integrators' => 'Consultants & System Integrators',
		'Training' => 'Training',
		'PaaS' => 'PaaS',
		'Management & Monitoring' => 'Management & Monitoring',
		'Apps on OpenStack' => 'Apps on OpenStack',
		'Compatible HW & SW' => 'Compatible Hardware & Software'
	);

	public static $avialable_regions = array (
		'North America' => 'North America',
		'South America' => 'South America',
		'Europe' => 'Europe',
		'Asia Pacific' => 'Asia Pacific'
	);

    public static $avialable_programs = array (
        'Powered' => 'Powered',
        'Compatible' => 'Compatible',
        'Training' => 'Training'
    );

}