<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class PaperMigrationUtils
 */
final class PaperMigrationUtils
{
    /**
     * @param string $content
     * @return string
     */
    static function cleanContent($content){
        $content = trim($content);

        $_arr = preg_split("/[\r\n]+/",$content,-1,PREG_SPLIT_NO_EMPTY);

        $arr=[];
        foreach($_arr as $line){
            // trim these line
            array_push($arr,trim($line));
        }

        return join(PHP_EOL, $arr);
    }

    /**
     * @param Paper $paper
     * @param string $title
     * @param int $order
     * @param array $contents
     * @return PaperSection
     * @throws ValidationException
     */
    static function createRawSection(Paper $paper, $title ,$order, array $contents){
        return self::createRawSectionFull($paper, $title, '' ,$order, $contents);
    }

    /**
     * @param Paper $paper
     * @param string $title
     * @param string $subTitle
     * @param int $order
     * @param array $contents
     * @return PaperSection
     * @throws ValidationException
     */
    static function createRawSectionFull(Paper $paper, $title, $subTitle ,$order, array $contents){
        // intro section
        $section = new PaperSection();
        $section->Title = $title;
        $section->Subtitle = $subTitle;
        $section->Order = $order;
        $section->PaperID = $paper->ID;
        $section->write();

        $idx = 1;

        foreach($contents as $type => $content){
            if(substr( $type, 0, 1 ) === "p") {
                $c = new PaperParagraph();
                $c->Type = 'P';
                $c->Content = self::cleanContent($content);
            }
            else if(substr( $type, 0, 3 ) === "img") {
                $c = new PaperParagraph();
                $c->Type = 'IMG';
                $c->Content = self::cleanContent($content);
            }
            else if(substr( $type, 0, 2 ) === "h4") {
                $c = new PaperParagraph();
                $c->Type = 'H4';
                $c->Content = self::cleanContent($content);
            }
            else if(substr( $type, 0, 2 ) === "h5") {
                $c = new PaperParagraph();
                $c->Type = 'H5';
                $c->Content = self::cleanContent($content);
            }
            else {
                $c = new PaperParagraphList();
                $c->Type = 'LIST';
                $c->SubType = substr( $type, 0, 2 ) === "ul" ? "UL" : "OL";
                $c->write();
                self::createParagraphListItems($c, $content);
            }
            $c->Order = $idx;
            $c->SectionID = $section->ID;
            $c->write();
            $idx++;
        }

        return $section;
    }


    /**
     * @param PaperSection $section
     * @param $title
     * @param $order
     * @param array $contents
     * @throws ValidationException
     */
    static function createCaseOfStudy(PaperSection $section, $title, $order, array $contents){
        $case =  new CaseOfStudy();
        $case->Title = $title;
        $case->Order = $order;
        $case->ParentSectionID = $section->ID;
        $case->PaperID = $section->PaperID;
        $case->write();

        $idx = 1;
        foreach($contents as $type => $content){
            if(substr( $type, 0, 1 ) === "p") {
                $c = new PaperParagraph();
                $c->Type = 'P';
                $c->Content = self::cleanContent($content);
            }
            else if(substr( $type, 0, 3 ) === "img") {
                $c = new PaperParagraph();
                $c->Type = 'IMG';
                $c->Content = self::cleanContent($content);
            }
            else if(substr( $type, 0, 3 ) === "h4") {
                $c = new PaperParagraph();
                $c->Type = 'H4';
                $c->Content = self::cleanContent($content);
            }
            else if(substr( $type, 0, 3 ) === "h5") {
                $c = new PaperParagraph();
                $c->Type = 'H5';
                $c->Content = self::cleanContent($content);
            }
            else {
                $c = new PaperParagraphList();
                $c->Type = 'LIST';
                $c->SubType = substr( $type, 0, 2 ) === "ul" ? "UL" : "OL";
                $c->write();
                self::createParagraphListItems($c, $content);
            }
            $c->Order = $idx;
            $c->SectionID = $case->ID;
            $c->write();
            $idx++;
        }

    }

    /**
     * @param PaperParagraphList $owner
     * @param array $contents
     * @param PaperParagraphListItem|null $parent
     * @return PaperParagraphListItem
     * @throws ValidationException
     */
    public static function createParagraphListItems(PaperParagraphList $owner, array $contents, PaperParagraphListItem $parent = null){
        $idx = 1;
        foreach($contents as $type => $content){
            $list_item = new PaperParagraphListItem();
            $list_item->Order = $idx;
            $list_item->OwnerID = $owner->ID;
            if(!is_null($parent))
                $list_item->ParentID = $parent->ID;
            $list_item->write();
            if(substr( $type, 0, 2 ) === "li") {
                $list_item->Content = self::cleanContent($content);
            }
            else{
                // sublist
                if(isset($content['content']))
                    $list_item->Content = self::cleanContent($content['content']);
                $list_item->SubItemsContainerType = substr( $type, 0, 2 ) === "ul" ? 'UL': 'OL';



                self::createParagraphListItems($owner, $content['items'], $list_item);
            }
            $list_item->write();
        }
    }
}