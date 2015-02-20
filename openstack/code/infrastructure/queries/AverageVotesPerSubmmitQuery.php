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

final class AverageVotesPerSubmmitQuery implements IQueryHandler {

    /**
     * @param IQuerySpecification $specification
     * @return IQueryResult
     */
    public function handle(IQuerySpecification $specification)
    {
        $res = 0;
        if($specification instanceof SummitQuerySpecification) {

            $params  = $specification->getSpecificationParams();
            $summit_id = $params[0];
            $sql1 = <<< SQL
			SELECT COUNT(V.ID) FROM SpeakerVote V INNER JOIN Talk T ON T.ID = V.TalkID
WHERE T.SummitID = $summit_id;

SQL;
            $votes = (int)DB::query($sql1)->value();

            $sql2 = <<< SQL
		SELECT COUNT(T.ID) FROM  Talk T WHERE T.SummitID = $summit_id;

SQL;
            $talks = (int)DB::query($sql2)->value();

            $res = $votes / $talks;
        }

        return new AbstractQueryResult(array($res));
    }
}