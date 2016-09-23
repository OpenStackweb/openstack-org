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
 * Class SapphirePresentationCategoryRepository
 */
final class SapphirePresentationCategoryRepository extends SapphireRepository implements IPresentationCategoryRepository{

	public function __construct(){
		parent::__construct(new PresentationCategory);
	}

    /**
     * @param int $summit_id
     * @param string $term
     * @return PresentationCategory[]
     */
    public function searchBySummitAndHasExtraQuestions($summit_id, $term) {
        $categories = array();

        $sql_events   = <<<SQL
        SELECT PC.* FROM PresentationCategory_ExtraQuestions AS EQ
        LEFT JOIN PresentationCategory AS PC ON PC.ID = EQ.PresentationCategoryID
        WHERE PC.SummitID = {$summit_id} AND (PC.Title LIKE '%{$term}' OR PC.ID = '{$term}')
        GROUP BY PC.ID ORDER BY PC.Title
SQL;

        foreach(DB::query($sql_events) as $row)
        {
            $class = $row['ClassName'];
            array_push($categories, new $class($row));
        }

        return $categories;

    }

}