<?php

/**
 * Class AUCMetricMember
 */
class AUCMetricMember extends DataExtension
{

	/**
	 * @var array
	 */
	private static $db = [
		'AskOpenStackUsername' => 'Varchar'
	];

    /**
     * @var array
     */
    private static $has_many = [
        'AUCMetrics' => 'AUCMetric'
    ];

    /**
     * @param $identifier
     * @return mixed
     */
    public function hasAUCMetric($identifier)
    {
        return $this->owner->AUCMetrics()->filter([
            'Identifier' => $identifier
        ])->exists();
    }
}