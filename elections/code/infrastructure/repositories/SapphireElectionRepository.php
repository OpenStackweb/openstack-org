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
 * Class SapphireElectionRepository
 */
final class SapphireElectionRepository extends SapphireRepository
	implements IElectionRepository  {

    public function __construct(){
		parent::__construct(new Election());
	}

	/**
	 * @param int $n
	 * @return IElection[]
	 */
	public function getLatestNElections($n)
	{
	    $sql = <<<SQL
select * from Election
WHERE ElectionsClose < UTC_DATE()
ORDER BY ElectionsOpen DESC LIMIT 0,$n;
SQL;

        $result = DB::query($sql);

        $elections = new ArrayList();
        foreach($result as $rowArray) {
            // concept: new Product($rowArray)
            $elections->push(new $rowArray['ClassName']($rowArray));
        }

        return $elections;
	}

	/**
	 * @param int $years
	 * @return IElection
	 */
	public function getEarliestElectionSince($years)
	{
		$sql = 'SELECT * FROM Election WHERE ElectionsClose >= DATE_ADD(UTC_TIMESTAMP(), INTERVAL -'.$years.' YEAR) ORDER BY ElectionsClose ASC LIMIT 0,1;';
		$result = DB::query($sql);
		// let Silverstripe work the magic
		$elections = new ArrayList();
		foreach($result as $rowArray) {
			// concept: new Product($rowArray)
			$elections->push(new $rowArray['ClassName']($rowArray));
		}
		return $elections->first();
	}
}