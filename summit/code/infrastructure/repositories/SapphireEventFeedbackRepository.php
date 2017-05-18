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
 * Class SapphireEventFeedbackRepository
 */
final class SapphireEventFeedbackRepository extends SapphireRepository implements IEventFeedbackRepository
{

    public function __construct()
    {
        parent::__construct(new SummitEventFeedback());
    }

    public function getFeedback($event_id,$member_id) {
        $query = new QueryObject(new SummitEventFeedback());
        $query->addAndCondition(QueryCriteria::equal('OwnerID',$member_id));
        $query->addAndCondition(QueryCriteria::equal('EventID',$event_id));
        return  $this->getBy($query);
    }

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $sort
     * @param string $sort_dir
     * @param string $source
     * @param int $id
     * @param string $id
     * @return ISummitEventFeedback[]
     */
    public function getBySourcePaged($summit_id,$page,$page_size,$sort,$sort_dir,$source,$id,$search_term)
    {
        $query_body = <<<SQL
            FROM SummitEventFeedback AS F
            INNER JOIN SummitEvent AS E ON E.ID = F.EventID
            INNER JOIN Presentation AS P ON E.ID = P.ID
            INNER JOIN PresentationCategory AS PC ON E.CategoryID = PC.ID
            LEFT JOIN Presentation_Speakers AS PS ON PS.PresentationID = P.ID
            LEFT JOIN PresentationSpeaker AS S ON PS.PresentationSpeakerID = S.ID
            LEFT JOIN Member AS M ON M.ID = F.OwnerID
SQL;

        $query_where = <<<SQL
            WHERE E.SummitID = {$summit_id}
SQL;

        if ($search_term) {
            $query_where .= " AND (E.Title LIKE '%{$search_term}%' OR S.FirstName LIKE '%{$search_term}%'
                            OR S.LastName LIKE '%{$search_term}%' OR CONCAT(S.FirstName,' ',S.LastName) LIKE '%{$search_term}%'
                            OR PC.Title LIKE '%{$search_term}%' OR P.ID = '{$search_term}' )";
        }

        switch ($source) {
            case 'track' :
                $query_where .= " AND PC.ID = {$id}";
                $header_query = "SELECT PC.Title AS title, ROUND(AVG(F.Rate),1) AS avg_rate " . $query_body . $query_where . " GROUP BY PC.ID";
                break;
            case 'presentation' :
                $query_where .= " AND P.ID = {$id}";
                $header_query = "SELECT E.Title AS title, ROUND(AVG(F.Rate),1) AS avg_rate " . $query_body . $query_where . " GROUP BY P.ID";
                break;
            case 'speaker' :
                $query_where .= " AND (S.ID = {$id} OR S2.ID = {$id})";
                $header_query = "SELECT CONCAT(S.FirstName,' ',S.LastName) AS title, ROUND(AVG(F.Rate),1) AS avg_rate " . $query_body . $query_where . " GROUP BY S.ID";
                break;
            default:
                $header_query = "SELECT 'All Feedbacks' AS title, ROUND(AVG(F.Rate),1) AS avg_rate " . $query_body . $query_where . " GROUP BY NULL";
        }

        $query_count = "SELECT COUNT(*) FROM (SELECT F.ID ";

        $query = <<<SQL
        SELECT
        P.ID AS presentation_id,
        E.Title AS title,
        PC.Title AS track,
        F.Rate AS rate,
        F.Note AS note,
        M.FirstName AS first_name,
        M.Surname AS last_name,
        GROUP_CONCAT(CONCAT(S.FirstName,' ',S.LastName) ) AS speakers
SQL;

        $query .= $query_body . $query_where . " GROUP BY F.ID ORDER BY {$sort} {$sort_dir}";
        $query_count .= $query_body . $query_where . " GROUP BY F.ID ) AS Q1";

        if ($page && $page_size) {
            $offset = ($page - 1 ) * $page_size;
            $query .= " LIMIT {$offset}, {$page_size}";
        }

        $data = array();
        foreach (DB::query($query) as $row) {
            $data[] = $row;
        }

        $total = DB::query($query_count)->value();
        $header = DB::query($header_query)->record();
        $result = array('total' => $total, 'data' => $data, 'header' => $header);
        return $result;
    }

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $sort
     * @param string $sort_dir
     * @param string $search_term
     * @param string $group_by
     * @return array()
     */
    public function searchByTermGrouped($summit_id,$page,$page_size,$sort,$sort_dir,$search_term,$group_by)
    {
        $query_body = <<<SQL
            FROM SummitEventFeedback AS F
            INNER JOIN SummitEvent AS E ON E.ID = F.EventID
            INNER JOIN Presentation AS P ON E.ID = P.ID
            INNER JOIN PresentationCategory AS PC ON E.CategoryID = PC.ID
            LEFT JOIN Presentation_Speakers AS PS ON PS.PresentationID = P.ID
            LEFT JOIN PresentationSpeaker AS S ON PS.PresentationSpeakerID = S.ID
            LEFT JOIN Member AS M ON M.ID = F.OwnerID
SQL;

        $query_where = <<<SQL
            WHERE E.SummitID = {$summit_id}
SQL;

        if ($search_term) {
            $query_where .= " AND (E.Title LIKE '%{$search_term}%' OR S.FirstName LIKE '%{$search_term}%'
                            OR S.LastName LIKE '%{$search_term}%' OR CONCAT(S.FirstName,' ',S.LastName) LIKE '%{$search_term}%'
                            OR PC.Title LIKE '%{$search_term}%' OR P.ID = '{$search_term}' )";
        }

        if ($group_by == 'presentation') {
            $query = "SELECT E.Title AS grouped_item,
                      COUNT(DISTINCT F.ID) AS feedback_count,
                      ROUND(AVG(F.Rate),1) AS avg_rate,
                      'presentation' AS source,
                      P.ID AS source_id
                      {$query_body} {$query_where} GROUP BY P.ID ORDER BY E.Title";
        } else if ($group_by == 'speaker') {
            $query = "SELECT CONCAT(S.FirstName,' ',S.LastName) AS grouped_item,
                      COUNT(DISTINCT F.ID) AS feedback_count,
                      ROUND(AVG(F.Rate),1) AS avg_rate,
                      'speaker' AS source,
                      S.ID AS source_id
                      {$query_body} {$query_where} GROUP BY S.ID ORDER BY S.LastName";
        } else {
            $query = "SELECT PC.Title AS grouped_item,
                      COUNT(DISTINCT F.ID) AS feedback_count,
                      ROUND(AVG(F.Rate),1) AS avg_rate,
                      'track' AS source,
                      PC.ID AS source_id
                      {$query_body} {$query_where} GROUP BY PC.ID ORDER BY PC.Title";
        }

        if ($page && $page_size) {
            $offset = ($page - 1 ) * $page_size;
            $query .= " LIMIT {$offset}, {$page_size}";
        }

        $data = array();
        foreach (DB::query($query) as $row) {
            $data[] = $row;
        }

        $total = DB::query("SELECT COUNT(*) FROM (" . $query . " ) AS Q1")->value();
        $result = array('total' => $total, 'data' => $data);
        return $result;
    }
}