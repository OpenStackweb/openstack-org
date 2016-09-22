<?php

class AUCMetricAdmin extends ModelAdmin
{
	private static $url_segment = 'auc';

	private static $menu_title = 'AUC Metrics';

	private static $managed_models = [
		'AUCMetric'
	];

	public function getExportFields() {
		
		return [
			'FoundationMemberID' => 'FoundationMemberID',
			'Identifier' => 'Identifier',
			'Expires' => 'Expires'
		];
	}

	public function getList() {
		$list = AUCMetric::get()
			->sort([
				'Identifier ASC',
				'Expires ASC'
			]);		
		$params = $this->request->requestVar('q');
		
		if(!empty($params['MemberSearch'])) {
			$s = Convert::raw2sql($params['MemberSearch']);
			$list = $list
				->innerJoin('Member','FoundationMemberID = Member.ID')
	            ->where("
                  	Member.Email LIKE '%{$s}%'
                  	OR Member.SecondEmail LIKE '%{$s}%'
                  	OR Member.ThirdEmail LIKE '%{$s}%'
                    OR (CONCAT_WS(' ', Member.FirstName, Member.Surname)) LIKE '%{$s}%'
	            ");
		}

		if(!empty($params['Metric'])) {
			$list = $list->filter('Identifier', $params['Metric']);
		}

		return $list;
	}

}