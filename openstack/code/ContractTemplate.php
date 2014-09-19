<?php

/**
 * Class ContractTemplate
 */
class ContractTemplate extends DataObject {

    static $db = array(
        'Name'     => 'Varchar',
	    //in days
	    'Duration'  => 'Int',
	    'AutoRenew' => 'Boolean',
    );

	static $defaults = array('AutoRenew' => false);

    static $has_one = array(
        'PDF'             => 'File',
    );

	static $has_many = array(
		'Contracts' => 'Contract',
	);

    public static $summary_fields = array(
        'Name'                 => 'Name',
        'PDF.Name'             => 'Pdf filename',
    );

	static $searchable_fields = array(
		"Name" => array(
	  	    "field"   => "TextField"
	  	),

		"PDF.Name" => array(
			"field"   => "TextField"
		),
	 );

	/**
	 * @return FieldSet
	 */

	public function getCMSFields()
    {
        $fields = new FieldList();
        $fields->push(new TextField("Name","Name"));
	    $fields->push(new TextField("Duration","Duration (in Days)"));
	    $fields->push(new CheckboxField('AutoRenew','Auto Renew'));
        $fields->push(new FileIFrameField("PDF","PDF"));
        return $fields;
    }

	function getCMSValidator()
	{
		return $this->getValidator();
	}

	function getValidator()
	{
		$validator_required       = new RequiredFields(array('Name','Duration'));
		$int_rule                 = new NetefxValidatorRuleGREATER('Duration','Insert a number greater than 0', null, 0);
		$validator_integer_fields = new NetefxValidator($int_rule);
		return new ConditionalAndValidationRule(array($validator_required,$validator_integer_fields));
	}

}