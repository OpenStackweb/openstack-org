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
 * Class ProgramTypeRelationMigrationTask
 */
final class ProgramTypeRelationMigrationTask extends MigrationTask {

	protected $title = "ProgramType Relation Migration";

	protected $description = "Capabilities and DesignatedSections now have a many_many relation with ProgramType.
	This migration removes the deprecated ProgramID column from these tables and sets the new relations based on the old ones.";

	function up(){
		echo "Starting Migration Proc ...<BR>";
		//check if migration already had ran ...
            DB::query('ALTER TABLE InteropCapability DROP COLUMN Order');
            DB::query('ALTER TABLE InteropDesignatedSection DROP COLUMN Order');

		echo "Ending  Migration Proc ...<BR>";
	}
}