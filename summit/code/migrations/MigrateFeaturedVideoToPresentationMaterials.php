<?php
/**
 * Copyright 2017 OpenStack Foundation
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
 * Class MigrateFeaturedVideoToPresentationMaterials
 */
final class MigrateFeaturedVideoToPresentationMaterials extends AbstractDBMigrationTask
{
    protected $title = "MigrateFeaturedVideoToPresentationMaterials";

    protected $description = "MigrateFeaturedVideoToPresentationMaterials";

    private static $category_pages_summit = [
        409   => 19,
        741   => 20,
        1550  => 21,
        1134  => 1,
        2096  => 3,
        2520  => 4,
        2681  => 5,
    ];

    private function createLegacyPresentation(FeaturedVideo $v, Summit $summit)
    {
        return Presentation::create([
            'Title'         => $v->Name,
            'Description'   => $v->Description,
            'StartDate'     => $summit->SummitBeginDate,
            'EndDate'       => $summit->SummitBeginDate,
            'Published'     => true,
            'PublishedDate' => $v->Created,
            'SummitID'      => $summit->ID,
            'Legacy'        => true
        ]);
    }


    private function createLegacyVideoMaterial(Presentation $p, FeaturedVideo $v)
    {

        $pres_video = PresentationVideo::create([
            'PresentationID' => $p->ID,
            'Name'           => $v->Name,
            'DisplayOnSite'  => true,
            'Processed'      => true,
            'YouTubeID'      => trim($v->YouTubeID),
            'Highlighted'    => true,
            'Created'        => $v->Created,
            'LastEdited'     => $v->LastEdited,
            'DateUploaded'   => $p->Summit()->SummitBeginDate
        ]);

        return $pres_video;
    }

    function doUp()
    {
        global $database;

        // fix uploaded dates for older videos ...
        DB::query("UPDATE PresentationVideo
INNER JOIN VideoPresentation ON VideoPresentation.YouTubeID = PresentationVideo.YouTubeID
SET DateUploaded = VideoPresentation.Created;");

        Config::inst()->update('DataObject', 'validation_enabled', false);
        foreach(FeaturedVideo::get() as $featured_video){
            $summit_id = @self::$category_pages_summit[intval($featured_video->PresentationCategoryPageID)];

            $youtube_id = trim($featured_video->YouTubeID);
            if(empty($youtube_id)){
                echo sprintf("%s (%s) - youtube id is empty for video %s",$featured_video->Name, $featured_video->URLSegment, $featured_video->ID).PHP_EOL;
                continue;
            }
            $old_video  = PresentationVideo::get()->filter(['YouTubeID' => $youtube_id])->first();
            if(!is_null($old_video)){
                echo sprintf("%s (%s) - youtube id %s already exist on DB!", $featured_video->Name, $featured_video->URLSegment, $youtube_id).PHP_EOL;
                $old_video->Highlighted = true;
                $old_video->write();

                continue;
            }

            $summit     = Summit::get()->byID($summit_id);

            if(is_null($summit)){
                echo sprintf("summit not found for Presentation Category Page %s", $featured_video->PresentationCategoryPageID).PHP_EOL;
                continue;
            }

            $presentation = $this->createLegacyPresentation($featured_video, $summit);

            $presentation->write(false, true);


            $material = $this->createLegacyVideoMaterial($presentation, $featured_video);
            $material->write();

            DB::query(sprintf(
                "UPDATE PresentationMaterial SET Created = '%s', LastEdited = '%s' WHERE ID = '%s'",
                $featured_video->LastEdited,
                $featured_video->LastEdited,
                $material->ID
            ));

            if(!empty($featured_video->URLSegment))
                DB::query(sprintf(
                    "UPDATE Presentation 
                        SET Slug = '%s' , Legacy = 1 
                        WHERE ID = '%s'",
                    $featured_video->URLSegment,
                    $presentation->ID
                ));

            echo "Created video {$material->Name}({$material->YouTubeID}) - segment {$featured_video->URLSegment} with date uploaded {$material->DateUploaded}." . PHP_EOL;
        }

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}