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

/**
 * Class SapphirePresentationVoteRepository
 */
class SapphirePresentationVoteRepository
    extends SapphireRepository
    implements IPresentationVoteRepository {

    public function __construct(){
        parent::__construct(new PresentationVote());
    }

    /**
     * @param int $summit_id
     * @return int
     */
    public function getVoteCountBySummit($summit_id) {

        $query = <<<SQL
            SELECT COUNT(DISTINCT(Vote.ID)) AS vote_count
            FROM PresentationVote AS Vote
            INNER JOIN SummitEvent AS E ON E.ID = Vote.PresentationID
            WHERE E.SummitID = {$summit_id}
SQL;

        return DB::query($query)->value();
    }

    /**
     * @param int $summit_id
     * @return int
     */
    public function getVotersCountBySummit($summit_id) {
        $query = <<<SQL
            SELECT COUNT(DISTINCT(Vote.MemberID)) AS voter_count
            FROM PresentationVote AS Vote
            INNER JOIN SummitEvent AS E ON E.ID = Vote.PresentationID
            WHERE E.SummitID = {$summit_id}
SQL;

        return DB::query($query)->value();
    }

}