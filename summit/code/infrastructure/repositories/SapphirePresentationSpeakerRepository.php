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


    /**
     * @param ISummit $summit
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function getBySummit(ISummit $summit, $page= 1, $page_size = 10)
    {

        $offset    = ($page - 1 ) * $page_size;
        $query_count = <<<SQL
SELECT COUNT( DISTINCT PresentationSpeaker.ID) AS QTY FROM PresentationSpeaker
WHERE EXISTS
(
	SELECT 1 FROM SummitEvent
    INNER JOIN Presentation ON Presentation.ID = SummitEvent.ID
    INNER JOIN Presentation_Speakers ON Presentation_Speakers.PresentationID = Presentation.ID
    WHERE SummitEvent.SummitID = {$summit->ID}
    AND Presentation_Speakers.PresentationSpeakerID  = PresentationSpeaker.ID
);
SQL;


        $query = <<<SQL
SELECT DISTINCT PresentationSpeaker.*  FROM PresentationSpeaker
WHERE EXISTS
(
	SELECT 1 FROM SummitEvent
    INNER JOIN Presentation ON Presentation.ID = SummitEvent.ID
    INNER JOIN Presentation_Speakers ON Presentation_Speakers.PresentationID = Presentation.ID
    WHERE SummitEvent.SummitID = {$summit->ID}
    AND Presentation_Speakers.PresentationSpeakerID  = PresentationSpeaker.ID
) LIMIT {$offset}, {$page_size};
SQL;


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
}
