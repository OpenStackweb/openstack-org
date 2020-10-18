<?php
/**
 * Copyright 2019 Open Infrastructure Foundation
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

class FixFileNamesMigration extends AbstractDBMigrationTask
{
    protected $title = "FixFileNamesMigration";

    protected $description = "FixFileNamesMigration";

    function doUp()
    {
        $sql = <<< SQL
select * from File where Filename like '% %';
SQL;

        $res = DB::query($sql);

        $filter    = FileNameFilter::create();
        // this is in case that there are dups files to keep track
        $files_dic = [];
        foreach ($res as $row) {
            $class            = $row['ClassName'];
            $file             = new $class($row);
            $originalName     = $file->Name;
            $originalExt      = pathinfo($originalName, PATHINFO_EXTENSION);
            $originalFileName = $file->FileName;
            $skipCloudCopy    = true;

            if(!isset($files_dic[$originalFileName])) {
                $file->Name     = $filter->filter($originalName);
                if($file->Name == $originalExt)
                {
                    $file->Name = $filter->getDefaultName() . '.' . $originalExt;
                }
                $file->FileName = str_replace($originalName, $file->Name, $file->FileName);
                $files_dic[$originalFileName] = [
                    'Name' => $file->Name,
                    'FileName' => $file->FileName
                ];
                $skipCloudCopy = false;
            }
            else{
                $fileData       = $files_dic[$originalFileName];
                $file->Name     = $fileData['Name'];
                $file->FileName = $fileData['FileName'];
            }

            if(!$skipCloudCopy) {
                $cloud = CloudAssets::inst();

                // does this file fall under a cloud bucket?
                $bucket = $cloud->map($originalFileName);
                if ($bucket) {
                    try {
                        $bucket->rename($file, $originalFileName, $file->FileName);
                    } catch (Exception $ex) {
                        SS_Log::log($ex->getMessage(), SS_Log::WARN);
                    }
                }
            }

            DB::query(sprintf("UPDATE File set Name ='%s', FileName='%s' WHERE ID = %s",
                $file->Name,
                $file->FileName,
                $file->ID
            ));

        }
    }

    function doDown()
    {

    }

}