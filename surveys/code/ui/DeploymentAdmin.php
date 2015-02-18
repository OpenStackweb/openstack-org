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
 * Class DeploymentAdmin
 */
final class DeploymentAdmin extends ModelAdmin {
    
    public static $managed_models = array(
        'DeploymentSurvey',
		'Deployment',
    );

	public $showImportForm = false;
    private static $url_segment    = 'deployments';
    private static $menu_title     = 'Deployments';

	public function getList() {
		$context = $this->getSearchContext();
		$params = $this->request->requestVar('q');
		$list = $context->getResults($params, $sort = array('Created' => 'DESC'));

		$this->extend('updateList', $list);

		return $list;
	}
}
