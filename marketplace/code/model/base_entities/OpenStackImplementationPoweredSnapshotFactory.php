<?php

/**
 * Copyright 2016 OpenStack Foundation
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
class OpenStackImplementationPoweredSnapshotFactory
{
    public static function build(IOpenStackImplementation $implementation, array $data){

        $snapshot = new OpenStackPoweredProgramHistory();

        $snapshot->OpenStackImplementationID   = $implementation->getIdentifier();
        $snapshot->CompatibleWithComputeBefore = $implementation->CompatibleWithCompute;
        $snapshot->CompatibleWithStorageBefore = $implementation->CompatibleWithStorage;
        $snapshot->ExpiryDateBefore            = $implementation->ExpiryDate;
        $snapshot->ProgramVersionIDBefore      = $implementation->ProgramVersionID;
        $snapshot->ReportedReleaseIDBefore     = $implementation->ReportedReleaseID;
        $snapshot->PassedReleaseIDBefore       = $implementation->PassedReleaseID;
        $snapshot->NotesBefore                 = $implementation->Notes;

        if($implementation->ProgramVersionID > 0)
            $snapshot->ProgramVersionNameBefore  = $implementation->ProgramVersion()->Name;

        if($implementation->ReportedReleaseID > 0)
            $snapshot->ReportedReleaseNameBefore = $implementation->ReportedRelease()->Name;

        if($implementation->PassedReleaseID > 0)
            $snapshot->PassedReleaseNameBefore = $implementation->PassedRelease()->Name;

        if(isset($data['required_for_compute']))
            $snapshot->CompatibleWithComputeCurrent = boolval($data['required_for_compute']);
        else
            $snapshot->CompatibleWithComputeCurrent = $implementation->CompatibleWithCompute;

        if(isset($data['required_for_storage']))
            $snapshot->CompatibleWithStorageCurrent = boolval($data['required_for_storage']);
        else
            $snapshot->CompatibleWithStorageCurrent = $implementation->CompatibleWithStorage;

        if(isset($data['expiry_date']))
            $snapshot->ExpiryDateCurrent = $data['expiry_date'];
        else
            $snapshot->ExpiryDateCurrent = $implementation->ExpiryDate;

        if(isset($data['program_version_id']))
            $snapshot->ProgramVersionIDCurrent = intval($data['program_version_id']);
        else
            $snapshot->ProgramVersionIDCurrent = $implementation->ProgramVersionID;

        if(isset($data['notes']))
            $snapshot->NotesCurrent = trim($data['notes']);
        else
            $snapshot->NotesCurrent = $implementation->NotesBefore;

        if(isset($data['reported_release_id']))
            $snapshot->ReportedReleaseIDCurrent = intval($data['reported_release_id']);
        else
            $snapshot->ReportedReleaseIDCurrent = $implementation->ReportedReleaseIDBefore;

        if(isset($data['passed_release_id']))
            $snapshot->PassedReleaseIDCurrent = trim($data['passed_release_id']);
        else
            $snapshot->PassedReleaseIDCurrent = $implementation->PassedReleaseIDBefore;

        if($snapshot->ProgramVersionIDCurrent != $snapshot->ProgramVersionIDBefore && $snapshot->ProgramVersionIDCurrent > 0) {
            $program = InteropProgramVersion::get()->byID($snapshot->ProgramVersionIDCurrent);
            if(!is_null($program))
                $snapshot->ProgramVersionNameCurrent = $program->Name;
        }
        else
            $snapshot->ProgramVersionNameCurrent = $snapshot->ProgramVersionNameBefore;

        if($snapshot->ReportedReleaseIDCurrent != $snapshot->ReportedReleaseIDBefore && $snapshot->ReportedReleaseIDCurrent > 0) {
            $release = OpenStackRelease::get()->byID($snapshot->ReportedReleaseIDCurrent);
            if(!is_null($release))
                $snapshot->ReportedReleaseNameCurrent = $release->Name;
        }
        else
            $snapshot->ReportedReleaseNameCurrent = $snapshot->ReportedReleaseNameBefore;

        if($snapshot->PassedReleaseIDCurrent != $snapshot->PassedReleaseIDBefore && $snapshot->PassedReleaseIDCurrent > 0) {
            $release = OpenStackRelease::get()->byID($snapshot->PassedReleaseIDCurrent);
            if(!is_null($release))
                $snapshot->PassedReleaseNameCurrent = $release->Name;
        }
        else
            $snapshot->PassedReleaseNameCurrent = $snapshot->PassedReleaseNameBefore;

        $snapshot->OwnerID                   = Member::currentUserID();
        return $snapshot;
    }
}