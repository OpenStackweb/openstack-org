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
 * Interface IOpenStackImplementationApiCoverage
 */
interface IOpenStackImplementationApiCoverage extends IEntity {

	/**
	 * @return int
	 */
	public function getCoveragePercent();

	/**
	 * @param int $coverage
	 * @return void
	 */
	public function setCoveragePercent($coverage);

	/**
	 * @return IOpenStackImplementation
	 */
	public function getImplementation();

	/**
	 * @param IOpenStackImplementation $implementation
	 * @return void
	 */
	public function setImplementation(IOpenStackImplementation $implementation);

	/**
	 * @return IReleaseSupportedApiVersion
	 */
	public function getReleaseSupportedApiVersion();

	public function setReleaseSupportedApiVersion(IReleaseSupportedApiVersion $release_supported_api_version);

	/**
	 * @return bool
	 */
	public function SupportsVersioning();

} 