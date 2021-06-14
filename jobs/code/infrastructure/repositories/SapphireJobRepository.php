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
 * Class SapphireJobRepository
 */
final class SapphireJobRepository extends SapphireRepository implements IJobRepository
{

    public function __construct()
    {
        parent::__construct(new Job);
    }

    public function delete($entity)
    {
        $entity->clearLocations();
        parent::delete($entity);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAllPosted($offset = 0, $limit = 10)
    {
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::equal('IsActive', 1));
        $query->addAndCondition(QueryCriteria::greater('ExpirationDate', date('Y-m-d H:i:s')));
        $query->addOrder(QueryOrder::desc('PostedDate'));
        return $this->getAll($query, $offset, $limit);
    }

    public function getDateSortedJobs($foundation = 0)
    {
        $query = new QueryObject(new Job);

        if ($foundation)
            $query->addAndCondition(QueryCriteria::equal('IsFoundationJob', 1));

        $now = new DateTime();
        $six_months_ago = new DateTime();
        $query->addAndCondition(QueryCriteria::equal('IsActive', 1));
        $post_date = $six_months_ago->sub(new DateInterval('P6M'));
        $query->addAndCondition(QueryCriteria::greaterOrEqual('PostedDate', $post_date->format('Y-m-d H:i:s')));
        $query->addAndCondition(QueryCriteria::greaterOrEqual('ExpirationDate', $now->format('Y-m-d H:i:s')));
        $query->addOrder(QueryOrder::desc('PostedDate'));
        $query->addOrder(QueryOrder::desc('ID'));
        list($jobs, $size) = $this->getAll($query, 0, PHP_INT_MAX);
        return new ArrayList($jobs);
    }

    /**
     * @return array
     */
    private function getJobsByFoundationCompanyMemberLevel(): array
    {
        $now = new DateTime();
        $six_months_ago = new DateTime();
        $post_date = $six_months_ago->sub(new DateInterval('P6M'))->format('Y-m-d H:i:s');
        $expiration_date = $now->format('Y-m-d H:i:s');
        $sql = <<<SQL
select Job.* from Job inner join
Company on CompanyID = Company.ID
WHERE
    Job.`IsActive` = 1 AND
      Job.PostedDate >= '{$post_date}' AND
      Job.`ExpirationDate` >= '{$expiration_date}' AND
      Company.MemberLevel = 'Platinum'
UNION

select Job.* from Job inner join
Company on CompanyID = Company.ID
WHERE
    Job.`IsActive` = 1 AND
      Job.PostedDate >= '{$post_date}' AND
      Job.`ExpirationDate` >= '{$expiration_date}' AND
      Company.MemberLevel = 'Gold'

UNION
select Job.* from Job inner join
Company on CompanyID = Company.ID
WHERE
    Job.`IsActive` = 1 AND
      Job.PostedDate >= '{$post_date}' AND
      Job.`ExpirationDate` >= '{$expiration_date}' AND
      Company.MemberLevel = 'Silver';
SQL;

        $res = DB::query($sql);
        $list = [];
        $excluded = [];
        foreach ($res as $record) {
            $className = $record['ClassName'];
            $excluded[] = (int)$record["ID"];
            $list[] = new $className($record);
        }
        $excluded = implode(',', $excluded);
        $sql = <<<SQL
select Job.* from Job
WHERE
    Job.`IsActive` = 1 AND
    Job.PostedDate >= '{$post_date}' AND
    Job.`ExpirationDate` >= '{$expiration_date}' AND
    Job.ID NOT IN ({$excluded})  
    ORDER BY Job.PostedDate DESC, Job.ID DESC;
SQL;
        $res = DB::query($sql);
        foreach ($res as $record) {
            $className = $record['ClassName'];
            $list[] = new $className($record);
        }
        return $list;
    }

    public function getJobsByKeywordTypeAndSortedBy($keywords, $type_id, $sort_by)
    {

        $query = new QueryObject(new Job);
        $now = new DateTime();
        $six_months_ago = new DateTime();
        $post_date = $six_months_ago->sub(new DateInterval('P6M'));

        $query->addAndCondition(QueryCriteria::equal('IsActive', 1));
        $query->addAndCondition(QueryCriteria::greaterOrEqual('PostedDate', $post_date->format('Y-m-d H:i:s')));
        $query->addAndCondition(QueryCriteria::greaterOrEqual('ExpirationDate', $now->format('Y-m-d H:i:s')));

        if (!empty($keywords)) {
            $query->addAndCondition(
                QueryCompoundCriteria::compoundOr(
                    [
                        QueryCriteria::like('Title', trim($keywords)),
                        QueryCriteria::like('Description', trim($keywords))
                    ]
                )
            );
        }

        if (intval($type_id) > 0) {
            $query->addAndCondition(
                QueryCriteria::equal('TypeID', $type_id)
            );
        }

        if (!empty($sort_by)) {
            switch (strtolower($sort_by)) {
                case 'foundation_members':
                    return $this->getJobsByFoundationCompanyMemberLevel();
                    break;
                case 'coa':
                    $query = $query->addOrder(QueryOrder::desc('IsCOANeeded'));
                    break;
                case 'foundation':
                    $query = $query->addOrder(QueryOrder::desc('IsFoundationJob'));
                    break;
                case 'company':
                    $query = $query->addOrder(QueryOrder::asc('CompanyName'));
                    break;
                case 'posted':
                    $query = $query->addOrder(QueryOrder::desc('PostedDate'));
                    $query = $query->addOrder(QueryOrder::desc('ID'));
                    break;
                case 'location':
                    $query = $query->addOrder(QueryOrder::asc('LocationType'));
                    break;
            }
        }

        list($jobs, $size) = $this->getAll($query, 0, PHP_INT_MAX);
        return new ArrayList($jobs);
    }
} 