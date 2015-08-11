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
		$migration = DataObject::get_one("Migration", "Name='{$this->title}'");
		if (!$migration) {

            //first we add the platform InteropProgramType
            $platform = InteropProgramType::get("InteropProgramType","ShortName = 'Platform'")->first();

            if (!$platform) {
                $platform = new InteropProgramType();
                $platform->Name = 'OpenStack Powered Platform';
                $platform->ShortName = 'Platform';
                $platform->HasCapabilities = true;
                $platform->Order = 1;
                $platform->write();

                $platform = InteropProgramType::get("InteropProgramType","ShortName = 'Platform'")->first();
            }


            //then migrate the old InteropProgramType relations with capability and designated sections
            $relations = DB::query('SELECT cap.ID AS capID, pt.ID AS ptID
                                    FROM interopcapability AS cap
                                    LEFT JOIN interopprogramtype AS pt ON cap.ProgramID = pt.ID');

            foreach ($relations as $relation) {
                $capability = InteropCapability::get_by_id('InteropCapability',$relation['capID']);
                $program = InteropProgramType::get_by_id('InteropProgramType',$relation['ptID']);
                $capability->Program()->add($program);
                $capability->Program()->add($platform);

                $capability->write();
            }

            $relations = DB::query('SELECT ds.ID AS dsID, pt.ID AS ptID
                                    FROM interopdesignatedsection AS ds
                                    LEFT JOIN interopprogramtype AS pt ON ds.ProgramID = pt.ID');

            foreach ($relations as $relation) {
                $dsection = InteropDesignatedSection::get_by_id('InteropDesignatedSection',$relation['dsID']);
                $program = InteropProgramType::get_by_id('InteropProgramType',$relation['ptID']);
                $dsection->Program()->add($program);
                $dsection->Program()->add($platform);

                $dsection->write();
            }

            //finally remove ProgramID column from capability and designatedsection
            DB::query('ALTER TABLE interopcapability DROP COLUMN ProgramID');
            DB::query('ALTER TABLE interopdesignatedsection DROP COLUMN ProgramID');

			$migration = new Migration();
			$migration->Name = $this->title;
			$migration->Description = $this->description;
			$migration->Write();
		}
		echo "Ending  Migration Proc ...<BR>";
	}
}