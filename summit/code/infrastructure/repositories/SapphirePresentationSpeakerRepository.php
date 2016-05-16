<?php

/**
 * Copyright 2015 OpenStack Foundation
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
final class SapphirePresentationSpeakerRepository extends SapphireRepository implements ISpeakerRepository
{
    public function __construct()
    {
        parent::__construct(new PresentationSpeaker);
    }

    /**
     * @param string $term
     * @param int $limit
     * @return array;
     */
    public function searchByTerm($term, $limit = 10)
    {

        $term       = trim($term);
        $term_split = explode(' ',$term);
        $first_name = $term;
        $last_name1 = $term;
        $last_name2 = '';

        if(count($term_split) == 2)
        {
            $first_name  = trim($term_split[0]);
            $last_name2  = trim($term_split[1]);
        }

        $member_sql = <<<SQL
SELECT
CONCAT(M.ID,'_',IFNULL(PS.ID , 0)) AS unique_id,
M.ID AS member_id ,
M.ID AS id, CONCAT(M.FirstName,' ',M.Surname,' (',IFNULL(M.Email , PSR.Email),')') AS name,
M.FirstName AS firstname,
M.Surname AS surname,
IFNULL(PS.ID , 0) AS speaker_id,
IFNULL(M.Email , PSR.Email) AS email
FROM Member AS M
LEFT JOIN PresentationSpeaker AS PS ON PS.MemberID = M.ID
LEFT JOIN SpeakerRegistrationRequest AS PSR ON PSR.SpeakerID = PS.ID
SQL;

        $speakers_sql = <<<SQL
SELECT
CONCAT(PS.MemberID,'_',IFNULL(PS.ID , 0)) AS unique_id,
PS.MemberID AS member_id ,
PS.ID AS id, CONCAT(PS.FirstName ,' ',PS.LastName,' (', PSR.Email, ')') AS name,
PS.FirstName AS firstname,
PS.LastName  AS surname,
PS.ID  AS speaker_id,
PSR.Email AS email
FROM PresentationSpeaker AS PS
INNER JOIN SpeakerRegistrationRequest AS PSR ON PSR.ID = PS.RegistrationRequestID
SQL;

        $member_conditions = array(
            "combined" => "M.FirstName LIKE '{$first_name}%' AND M.Surname LIKE '{$last_name2}%' ",
            "single"   => array(
                "M.FirstName LIKE '{$first_name}%'",
                "M.Surname   LIKE '{$last_name1}%'",
                "M.Email LIKE '{$first_name}%'",
                "M.ID LIKE '{$first_name}%'",
            ),

        );

        $speakers_conditions = array(
            "combined" => "PS.FirstName LIKE '{$first_name}%' AND PS.LastName LIKE '{$last_name2}%' ",
            "single"   => array(
                "PS.FirstName LIKE '{$first_name}%'",
                "PS.LastName   LIKE '{$last_name1}%'",
                "PSR.Email LIKE '{$first_name}%'",
            ),
        );

        $query = '';

        foreach($member_conditions as $type => $condition){
            if(!empty($first_name) && !empty($last_name2) && $type =='combined')
            {
                $query .= $member_sql . ' WHERE ' . $condition;
                $query .= ' UNION ';
            }
            if($type == 'single' && empty($last_name2) ) {
                foreach($condition as $c) {
                    $query .= $member_sql . ' WHERE ' . $c;
                    $query .= ' UNION ';
                }
            }
        }

        foreach($speakers_conditions as $type => $condition){
            if(!empty($first_name) && !empty($last_name2) && $type =='combined')
            {
                $query .= $speakers_sql . ' WHERE ' . $condition;
                $query .= ' UNION ';
            }
            if($type == 'single' && empty($last_name2) ) {
                foreach($condition as $c) {
                    $query .= $speakers_sql . ' WHERE ' . $c;
                    $query .= ' UNION ';
                }
            }
        }
        $query = substr($query,0, strlen($query) - strlen(' UNION '));
        $query .= " ORDER BY `name` LIMIT 0, {$limit};";
        $res = DB::query($query);
        $data = array();
        foreach ($res as $row) {

            $data[] = array
            (
                'unique_id'  => $row['unique_id'],
                'member_id'  => $row['member_id'],
                'name'       => $row['name'],
                'firstname'  => $row['firstname'],
                'surname'    => $row['surname'],
                'speaker_id' => $row['speaker_id'],
                'email'      => $row['email'],
            );
        }
        return $data;
    }
    /**
     * @param ISummit $summit
     * @param string $term
     * @return IPresentationSpeaker[]
     */
    public function searchBySummitAndTerm(ISummit $summit, $term)
    {

        $speakers        = array();
        $summit_id       = $summit->getIdentifier();

        $sql_speakers = <<<SQL
      SELECT DISTINCT S.*, CONCAT(S.FirstName,' ',S.LastName) AS FullName FROM PresentationSpeaker S
      WHERE EXISTS
      (
            SELECT P.ID From Presentation P
            INNER JOIN SummitEvent E ON E.ID = P.ID AND E.Published = 1 AND E.SummitID = {$summit_id}
            INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
            WHERE PS.PresentationSpeakerID = S.ID
      )
      HAVING FullName LIKE '%{$term}%'
      UNION
      SELECT DISTINCT S.*, CONCAT(S.FirstName,' ',S.LastName) AS FullName FROM PresentationSpeaker S
      WHERE EXISTS
      (
            SELECT P.ID From Presentation P
            INNER JOIN SummitEvent E ON E.ID = P.ID AND E.Published = 1 AND E.SummitID = {$summit_id}
            INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
             WHERE PS.PresentationSpeakerID = S.ID
      )
      HAVING SOUNDEX(FullName) = SOUNDEX('{$term}')
      UNION
      SELECT DISTINCT S.*, CONCAT(S.FirstName,' ',S.LastName) AS FullName FROM PresentationSpeaker S
      WHERE EXISTS
      (
            SELECT P.ID From Presentation P
            INNER JOIN SummitEvent E ON E.ID = P.ID AND E.Published = 1 AND E.SummitID = {$summit_id}
            INNER JOIN Presentation_Speakers PS ON PS.PresentationID = P.ID
            WHERE PS.PresentationSpeakerID = S.ID AND E.Title LIKE '%{$term}%'
      )
SQL;

        foreach(DB::query($sql_speakers) as $row)
        {
            $class = $row['ClassName'];
            array_push($speakers, new $class($row));
        }

        return $speakers;
    }


    private function buildSpeakersSearchParams($page= 1, $page_size = 10, $term = '', $sort_by = 'id', $sort_dir = 'asc'){
        $offset = ($page - 1 ) * $page_size;
        $sort  = '';
        $where = '';
        if(!empty($term))
        {
            $where = " HAVING FullName LIKE  '%{$term}%' ";
        }
        switch(strtolower($sort_by))
        {
            case 'id':
                $sort = ' ORDER BY PresentationSpeaker.ID '.strtoupper($sort_dir);
                break;
            case 'fullname':
                $sort = ' ORDER BY FullName '.strtoupper($sort_dir);
                break;
            case 'email':
                $sort = ' ORDER BY Email '.strtoupper($sort_dir);
                break;
        }
        return array($offset, $sort, $where);
    }

    /**
     * @param string $where
     * @return string
     */
    private function buildSearchSpeakersBaseCountSQLQuery($where){
        $sql = <<<SQL
        SELECT COUNT(FullName) AS QTY FROM
        (
            SELECT
            IFNULL(CONCAT(PresentationSpeaker.FirstName,' ', PresentationSpeaker.LastName), CONCAT(Member.FirstName,' ', Member.Surname)) AS FullName
            FROM PresentationSpeaker
            LEFT JOIN Member ON Member.ID = PresentationSpeaker.MemberID
            LEFT JOIN SpeakerRegistrationRequest ON SpeakerRegistrationRequest.SpeakerID = PresentationSpeaker.ID
            {$where}
        ) AS P;
SQL;
        return $sql;
    }

    /**
     * @param string $where
     * @param string $sort
     * @param string $offset
     * @param string $page_size
     * @return string
     */
    private function buildSearchSpeakersBaseSQLQuery($where, $sort,$offset ,$page_size){

        $sql =<<<SQL
    SELECT DISTINCT PresentationSpeaker.*,
IFNULL(CONCAT(PresentationSpeaker.FirstName,' ', PresentationSpeaker.LastName), CONCAT(Member.FirstName,' ', Member.Surname)) AS FullName,
IFNULL(Member.Email, SpeakerRegistrationRequest.Email) AS Email
FROM PresentationSpeaker
LEFT JOIN Member ON Member.ID = PresentationSpeaker.MemberID
LEFT JOIN SpeakerRegistrationRequest ON SpeakerRegistrationRequest.SpeakerID = PresentationSpeaker.ID
 {$where}
 {$sort} LIMIT {$offset}, {$page_size}
SQL;
        return $sql;
    }

    /**
     * Gets all speakers that belongs to given summit , those are the ones that has at least a
     * Presentation on the give summit
     * @param ISummit $summit
     * @param int $page
     * @param int $page_size
     * @param string $term
     * @param string $sort_by
     * @param string $sort_dir
     * @return array
     */
    public function searchBySummitPaginated(ISummit $summit, $page= 1, $page_size = 10, $term = '', $sort_by = 'id', $sort_dir = 'asc')
    {

        list($offset, $sort, $where_having) = $this->buildSpeakersSearchParams($page, $page_size, $term, $sort_by, $sort_dir);

        $where = <<<SQL
      WHERE EXISTS
            (
                SELECT 1 FROM SummitEvent
                INNER JOIN Presentation ON Presentation.ID = SummitEvent.ID
                INNER JOIN Presentation_Speakers ON Presentation_Speakers.PresentationID = Presentation.ID
                WHERE SummitEvent.SummitID = {$summit->ID}
                AND Presentation_Speakers.PresentationSpeakerID  = PresentationSpeaker.ID
            )
            {$where_having}
SQL;

        $query_count = $this->buildSearchSpeakersBaseCountSQLQuery($where);
        $query       = $this->buildSearchSpeakersBaseSQLQuery($where, $sort, $offset, $page_size);


        $count_res = DB::query($query_count)->first();
        $res       = DB::query($query);
        $count     = intval($count_res['QTY']);
        $data      = array();

        foreach($res as $row)
        {
            array_push($data, new PresentationSpeaker($row));
        }

        return array($page, $page_size, $count, $data);
    }

    /**
     * Gets all speakers that belongs to given summit , those are the ones that has at least a
     * Published Presentation on the give summit
     * @param ISummit $summit
     * @param int $page
     * @param int $page_size
     * @param string $term
     * @param string $sort_by
     * @param string $sort_dir
     * @return array
     */
    public function searchBySummitSchedulePaginated(ISummit $summit, $page= 1, $page_size = 10, $term = '', $sort_by = 'id', $sort_dir = 'asc')
    {

        list($offset, $sort, $where_having) = $this->buildSpeakersSearchParams($page, $page_size, $term, $sort_by, $sort_dir);

        $where = <<<SQL
      WHERE EXISTS
            (
                SELECT 1 FROM SummitEvent
                INNER JOIN Presentation ON Presentation.ID = SummitEvent.ID
                INNER JOIN Presentation_Speakers ON Presentation_Speakers.PresentationID = Presentation.ID
                WHERE SummitEvent.SummitID = {$summit->ID}
                AND Presentation_Speakers.PresentationSpeakerID  = PresentationSpeaker.ID
                AND SummitEvent.Published = 1
            )
            {$where_having}
SQL;

        $query_count = $this->buildSearchSpeakersBaseCountSQLQuery($where);
        $query       = $this->buildSearchSpeakersBaseSQLQuery($where, $sort, $offset, $page_size);


        $count_res = DB::query($query_count)->first();
        $res       = DB::query($query);
        $count     = intval($count_res['QTY']);
        $data      = array();

        foreach($res as $row)
        {
            array_push($data, new PresentationSpeaker($row));
        }

        return array($page, $page_size, $count, $data);
    }


    /**
     * @param int $page
     * @param int $page_size
     * @param string $term
     * @param string $sort_by
     * @param string $sort_dir
     * @return array
     */
    public function searchByTermPaginated($page= 1, $page_size = 10, $term = '', $sort_by = 'id', $sort_dir = 'asc'){

        list($offset, $sort, $where_having) = $this->buildSpeakersSearchParams($page, $page_size, $term, $sort_by, $sort_dir);

        $query_count = $this->buildSearchSpeakersBaseCountSQLQuery($where_having);
        $query       = $this->buildSearchSpeakersBaseSQLQuery($where_having, $sort, $offset, $page_size);

        $count_res = DB::query($query_count)->first();
        $res       = DB::query($query);
        $count     = intval($count_res['QTY']);
        $data      = array();
        foreach($res as $row)
        {
            array_push($data, new PresentationSpeaker($row));
        }
        return array($page, $page_size, $count, $data);
    }

    /**
     * @param int $member_id
     * @return IPresentationSpeaker
     */
    public function getByMemberID($member_id)
    {
        return PresentationSpeaker::get()->filter('MemberID', $member_id)->first();
    }


    /**
     * @param string $email
     * @return IPresentationSpeaker
     */
    public function getByEmail($email)
    {
        $speaker = PresentationSpeaker::get()
            ->filter(array(
                'Member.Email' => $email,
            ))->first();

        if (is_null($speaker)) {
            $speaker = PresentationSpeaker::get()
                ->filter(array(
                    'Member.SecondEmail' => $email,
                ))->first();
        }

        if (is_null($speaker)) {
            $speaker = PresentationSpeaker::get()
                ->filter(array(
                    'Member.ThirdEmail' => $email,
                ))->first();
        }

        if (is_null($speaker)) {
            $registration_request = SpeakerRegistrationRequest::get()->filter(array(
                'Email' => $email,
                'IsConfirmed' => 0,
            ))->first();
            if(!is_null($registration_request))
                $speaker = $registration_request->Speaker();
        }

        return $speaker;
    }

}
