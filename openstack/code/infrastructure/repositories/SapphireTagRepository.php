<?php

/**
 * Copyright 2017 Open Infrastructure Foundation
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


    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $sort
     * @param string $sort_dir
     * @param string $id
     * @return ISummitEventFeedback[]
     */
    public function searchAllPaged($summit_id,$page,$page_size,$sort,$sort_dir,$search_term, $published)
    {
        $sort = $sort ? $sort : 'tag';

        $query_body = <<<SQL
            FROM Tag 
            INNER JOIN SummitEvent_Tags AS ET ON ET.TagID = Tag.ID
            INNER JOIN SummitEvent AS E ON E.ID = ET.SummitEventID
SQL;

        $query_where = <<<SQL
            WHERE E.SummitID = {$summit_id}
SQL;

        if ($search_term) {
            $query_where .= " AND (Tag.Tag LIKE '%{$search_term}%')";
        }

        if ($published) {
            $query_where .= " AND (E.Published = 1)";
        }

        $query_count = "SELECT COUNT(*) FROM (SELECT Tag.ID ";

        $query = <<<SQL
        SELECT
        Tag.Tag AS tag,
        Tag.ID AS id,
        COUNT(ET.ID) AS tag_count
SQL;

        $query .= $query_body . $query_where . " GROUP BY Tag.ID ORDER BY {$sort} {$sort_dir}";
        $query_count .= $query_body . $query_where . " GROUP BY Tag.ID ) AS Q1";

        if ($page && $page_size) {
            $offset = ($page - 1 ) * $page_size;
            $query .= " LIMIT {$offset}, {$page_size}";
        }

        $data = array();
        foreach (DB::query($query) as $row) {
            $data[] = $row;
        }

        $total = DB::query($query_count)->value();

        $result = array('total' => $total, 'tags' => $data);
        return $result;
    }


    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $sort
     * @param string $sort_dir
     * @param bool $published
     * @param string $tag_id
     * @return ISummitEvent[]
     */
    public function getEventsByTag($summit_id,$page,$page_size,$sort,$sort_dir,$published, $tag_id)
    {
        $sort = $sort ? $sort : 'E.ID';

        $query_body = <<<SQL
            FROM SummitEvent E 
            INNER JOIN SummitEvent_Tags AS ET ON ET.SummitEventID = E.ID
            INNER JOIN Presentation_Speakers AS PS ON PS.PresentationID = E.ID
            INNER JOIN PresentationSpeaker AS S ON S.ID = PS.PresentationSpeakerID
            INNER JOIN Presentation AS P ON P.ID = E.ID
SQL;

        $query_where = <<<SQL
            WHERE E.SummitID = {$summit_id} AND ET.TagID = {$tag_id}
SQL;

        if ($published) {
            $query_where .= " AND (E.Published = 1)";
        }

        $query_count = "SELECT COUNT(*) FROM (SELECT E.ID ";

        $query = <<<SQL
        SELECT
        E.ID AS id,
        E.Title AS title,
        P.AttendingMedia AS attending_media,
        GROUP_CONCAT(DISTINCT CONCAT(S.FirstName,' ',S.LastName) SEPARATOR ', ') AS speakers
SQL;

        $query .= $query_body . $query_where . " GROUP BY E.ID ORDER BY {$sort} {$sort_dir}";
        $query_count .= $query_body . $query_where . " GROUP BY E.ID ) AS Q1";

        if ($page && $page_size) {
            $offset = ($page - 1 ) * $page_size;
            $query .= " LIMIT {$offset}, {$page_size}";
        }

        $data = array();
        foreach (DB::query($query) as $row) {
            $data[] = $row;
        }

        $total = DB::query($query_count)->value();

        $result = array('total' => $total, 'events' => $data);
        return $result;
    }
}