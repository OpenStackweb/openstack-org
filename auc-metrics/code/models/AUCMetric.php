<?php

/**
 * Class AUCMetric
 */
class AUCMetric extends DataObject
{

    /**
     * @var array
     */
    private static $db = [
        'Identifier' => 'Varchar',
        'Value' => 'Varchar',
        'ValueDescription' => 'Varchar',
        'Expires' => 'SS_DateTime'
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'FoundationMember' => 'Member'
    ];

    /**
     * @var array
     */
    private static $indexes = [
        'Identifier' => true
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
    	'FoundationMember.Name' => 'Member Name',
    	'FoundationMember.Email' => 'Member email',
    	'Identifier' => 'Metric name',
    	'Expires.Date' => 'Expiry'
    ];

    /**
     * Gets all of the assigned identifiers in the database
     * @return array
     */
    public static function get_metric_identifiers()
    {
    	$allowedMetrics = new SQLQuery('Identifier', 'AUCMetric');
    	$allowedMetrics->setDistinct(true);
    	$result = $allowedMetrics->execute();
    	$identifiers = [];
    	
    	foreach($result as $row) {
    		$identifiers[] = $row['Identifier'];
    	}

    	return $identifiers;
    }

    /**
     * @return FieldList
     */
    public function searchableFields()
    {
    	return [
    		'MemberSearch' => [
    			'title' => 'Member name or email',
    			'field' => TextField::create('MemberSearch','Member name or email'),
    			'filter' => 'PartialMatchFilter'
    		],
    		'Metric' => [
    			'title' => 'Metric',
    			'field' => DropdownField::create(
    					'Metric',
	    				'Metric', 
    					ArrayLib::valuekey(self::get_metric_identifiers())
    				)
    				->setEmptyString('-- All metrics --'),
    			'filter' => 'ExactMatchFilter'
    		]
    	];
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
    	$fields = FieldList::create(TabSet::create('Root'));
    	$fields->addFieldsToTab('Root.Main', [
    		AutoCompleteField::create('FoundationMemberID','Member')
    			->setSourceClass('Member')
    			->setSourceFields([
    				'FirstName',
    				'Surname',
    				'Email',
    				'SecondEmail',
    				'ThirdEmail'
    			])
    			->setLabelField('Name')
    			->setLimit(10),
    		DropdownField::create(
    			'Identifier',
    			'Metric', 
    			ArrayLib::valuekey(self::get_metric_identifiers())
    		),
    		ReadonlyField::create('Value', $this->ValueDescription)
    	]);

    	return $fields;
    }    

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->Identifier;
    }
}