<?php

/**
 * Class ModelAsControllerCustom
 * custom class to avoid bot errors
 */
final class ModelAsControllerCustom extends ModelAsController {

	public function getNestedController() {
		try{
			return parent::getNestedController();
		}
		catch(Exception $ex){
			if($response = ErrorPage::response_for(404)) {
				return $response;
			} else {
				$this->httpError(404, 'The requested page could not be found.');
			}
		}
	}
} 