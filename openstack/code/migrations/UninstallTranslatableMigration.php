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
 * Class UpdateAnniversaryPage
 */
final class UninstallTranslatableMigration extends AbstractDBMigrationTask {

	protected $title = "Uninstall translatable module";

	protected $description = "Removes all of the old pages from the Translatable module";

	public function up()
	{
		foreach(['Stage','Live'] as $stage) {
			Versioned::reading_stage($stage);	
			$pages = SiteTree::get()->where("
				URLSegment LIKE '%-ja-jp' OR
				URLSegment LIKE '%-zh-cmn' OR
				URLSegment LIKE '%-de-DE' OR
				URLSegment LIKE '%-es-ES'
			");

			$count = $pages->count();
			$pages->removeAll();

			echo "Deleted $count pages from $stage.\n";
		}
		
	}

}