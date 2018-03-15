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
final class SapphireTagRepository extends SapphireRepository implements ITagRepository
{

    public function __construct(){
        parent::__construct(new Tag);
    }
    /**
     * @param string $search
     * @return ITag[]
     */
    public function getByTag($search)
    {
        $where = "WHERE Tag.Tag != ''";

        if (!empty($search)) {
            $where .= " AND Tag.Tag LIKE '%$search%'";
        }

        $query = <<<SQL
            SELECT
            Tag.ID,
            Tag.Tag,
            (SELECT COUNT(*) FROM SummitEvent_Tags ET WHERE ET.TagID = Tag.ID) AS ETCount,
            (SELECT COUNT(*) FROM PresentationCategory_AllowedTags PCT WHERE PCT.TagID = Tag.ID) AS PCTCount,
            (SELECT COUNT(*) FROM TrackTagGroup_AllowedTags ST WHERE ST.TagID = Tag.ID) AS STCount,
            (SELECT COUNT(*) FROM UserStoryDO_Tags UST WHERE UST.TagID = Tag.ID) AS USTCount
            FROM Tag
            {$where}
            GROUP BY Tag.ID
            ORDER BY Tag.Tag ASC
SQL;

        return DB::query($query);
    }
}