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
 * Class UpdateDriversTask
 */
final class UpdateDriversTask extends CronTask
{

    function run()
    {

        set_time_limit(0);

        try {
            DB::query("UPDATE Driver SET Active = 0");
            DB::query("UPDATE DriverRelease SET Active = 0");

            $url = 'https://opendev.org/x/driverlog/raw/branch/master/etc/default_data.json';
            $jsonResponse = @file_get_contents($url);

            $driverLog = json_decode($jsonResponse, true);
            $drivers = $driverLog['drivers'];
            $projectsRaw = $driverLog['projects'];
            $projectArray = [];
            $releasesRaw = $driverLog['releases'];
            $releaseArray = [];

            foreach ($projectsRaw as $project) {
                $projectArray[$project['id']] = $project['name'];
            }

            foreach ($releasesRaw as $release) {
                $releaseArray[$release['id']] = $release;
            }

            foreach ($drivers as $contents) {
                if(!isset($contents['project_id']) || !isset($contents['name'])) continue;

                $projectName = trim($projectArray[$contents['project_id']]);

                $driver = Driver::get()->filter(
                    array(
                        "Name" => trim($contents['name']),
                        "Project" => $projectName
                    )
                )->first();

                if (!$driver) {
                    $driver = new Driver();
                }

                $driver->Name = trim($contents['name']);
                $driver->Description = isset($contents['description']) ? $contents['description']: null;
                $driver->Project = $projectName;
                $driver->Vendor = isset($contents['vendor'])?$contents['vendor']: null;
                $driver->Url = isset($contents['wiki'])?$contents['wiki']: null;
                $driver->Active = 1;

                $driver->Releases()->removeAll();
                if (isset($contents['releases'])) {
                    $releases = $contents['releases'];
                    foreach ($releases as $release) {
                        $driver_release = DriverRelease::get()->filter("Name", trim($release))->first();

                        if (!$driver_release) {
                            $driver_release = new DriverRelease();
                        }

                        $driver_release->Name = trim($release);
                        $driver_release->Url = $releaseArray[$release]['wiki'];
                        $startDate = $releaseArray[$release]['start'];
                        $driver_release->Start = date('Y-m-d',strtotime($startDate));
                        $driver_release->Active = 1;

                        $driver_release->write();

                        $driver->Releases()->add($driver_release);
                    }
                }

                $driver->write();
            }

            return 'OK';
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            echo $ex->getMessage();
        }
    }
} 