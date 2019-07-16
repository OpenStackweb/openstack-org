<?php

/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
/**
 * Class SangriaPageSurveyDetailsExtension
 */

final class SangriaPageSurveyDetailsExtension  extends Extension {

	public function onBeforeInit(){

		Config::inst()->update(get_class($this), 'allowed_actions', array(
			'SurveyDetails',
		));

		Config::inst()->update(get_class($this->owner), 'allowed_actions', array(
			'SurveyDetails',
		));
	}

	function SurveyDetails(){
		$params     = $this->owner->request->allParams();
		$survey_id  = intval(Convert::raw2sql($params["ID"]));;
		$survey = DeploymentSurvey::get()->byID($survey_id);
		if($survey) {
			$back_url = $this->owner->request->getVar('BackURL');
			if(empty($back_url))
				$back_url = '#';
			$details_template = $survey->getSurveyType() == SurveyType::OLD ? "SangriaPage_SurveyDetailsOld":"SangriaPage_SurveyDetails";
			return $this->owner->Customise(
				array("Survey" => $survey,
					"BackURL" => $back_url
				)
			)->renderWith(array($details_template, 'SangriaPage', 'SangriaPage'));
		}
		return $this->owner->httpError(404, 'Sorry that survey could not be found!.');
	}

} 