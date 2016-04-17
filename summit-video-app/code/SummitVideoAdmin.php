<?php

class SummitVideoAdmin extends ModelAdmin {

	private static $managed_models = [
		'PresentationVideo'
	];

	private static $url_segment = 'summit-videos';

	private static $menu_title = 'Summit Videos';

	public function getList () {
		$list = parent::getList();
		
		return $list->sort([
			'Featured DESC',
			'Highlighted DESC',
			'DateUploaded DESC',
			'LastEdited DESC'
		]);
	}
}