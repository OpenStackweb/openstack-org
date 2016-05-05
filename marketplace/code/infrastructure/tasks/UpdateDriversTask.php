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

            $url = 'http://stackalytics.com/driverlog/api/1.0/drivers';
            $jsonResponse = @file_get_contents($url);

            $driverArray = json_decode($jsonResponse, true);
            $array = $driverArray['drivers'];

            foreach ($array as $contents) {
                if(!isset($contents['project_name']) || !isset($contents['name'])) continue;

                $driver = Driver::get()->filter(
                    array(
                        "Name" => trim($contents['name']),
                        "Project" => trim($contents['project_name'])
                    )
                )->first();

                if (!$driver) {
                    $driver = new Driver();
                }

                $driver->Name = trim($contents['name']);
                $driver->Description = isset($contents['description'])?$contents['description']: null;
                $driver->Project = $contents['project_name'];
                $driver->Vendor = isset($contents['vendor'])?$contents['vendor']: null;
                $driver->Url = isset($contents['wiki'])?$contents['wiki']: null;
                $driver->Active = 1;

                if (isset($contents['releases_info'])) {
                    $releases = $contents['releases_info'];
                    foreach ($releases as $release) {
                        $driver_release = DriverRelease::get()->filter("Name", trim($release['name']))->first();

                        if (!$driver_release) {
                            $driver_release = new DriverRelease();
                        }

                        $driver_release->Name = trim($release['name']);
                        $driver_release->Url = $release['wiki'];
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