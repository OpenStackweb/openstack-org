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
class SapphireSummitRegistrationPromoCodeRepository
    extends SapphireRepository
    implements ISummitRegistrationPromoCodeRepository
{

    public function __construct()
    {
        parent::__construct(new SummitRegistrationPromoCode);
    }

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $term
     * @param string $sort_by
     * @param string $sort_dir
     * @return array
     */
    public function searchByTermAndSummitPaginated($summit_id, $type, $page= 1, $page_size = 10, $term = '', $sort_by = 'code', $sort_dir = 'asc')
    {
        $base_query = <<<SQL
(
SELECT 
SPC.ID,
PC.ClassName,
SP.FirstName AS FirstName,
SP.LastName AS LastName,
IFNULL(SPRR.Email, SM.Email) AS Email,
SPC.SpeakerID AS OwnerID,
SPC.Type,
PC.Code,
PC.EmailSent,
PC.Redeemed,
PC.Source,
C.ID AS CreatorID,
C.Email AS CreatorEmail,
NULL AS Sponsor 
FROM SummitRegistrationPromoCode PC 
INNER JOIN Member AS C ON C.ID = PC.CreatorID
INNER JOIN SpeakerSummitRegistrationPromoCode SPC ON SPC.ID = PC.ID
LEFT  JOIN PresentationSpeaker SP ON SP.ID = SPC.SpeakerID
LEFT  JOIN SpeakerRegistrationRequest SPRR ON SPRR.SpeakerID = SP.ID
LEFT  JOIN Member SM ON SM.ID = SP.MemberID 
WHERE SummitID = {$summit_id} AND PC.ClassName = 'SpeakerSummitRegistrationPromoCode'
UNION
SELECT MC.ID,
PC.ClassName,
IFNULL(O.FirstName,MC.FirstName) AS FirstName,
IFNULL(O.Surname,MC.LastName) AS LastName,
IFNULL(O.Email,MC.Email) AS Email, 
MC.OwnerID,
MC.Type,
PC.Code,
PC.EmailSent,
PC.Redeemed,
PC.Source,
C.ID AS CreatorID,
C.Email AS CreatorEmail,
NULL AS Sponsor 
FROM SummitRegistrationPromoCode PC 
INNER JOIN Member AS C ON C.ID = PC.CreatorID
INNER JOIN MemberSummitRegistrationPromoCode MC ON MC.ID = PC.ID
LEFT JOIN Member O ON O.ID = MC.OwnerID
WHERE SummitID = {$summit_id} AND PC.ClassName = 'MemberSummitRegistrationPromoCode'
UNION
SELECT MC.ID,
PC.ClassName,
IFNULL(O.FirstName,MC.FirstName) AS FirstName,
IFNULL(O.Surname,MC.LastName) AS LastName,
IFNULL(O.Email,MC.Email) AS Email, 
MC.OwnerID,
MC.Type,
PC.Code,
PC.EmailSent,
PC.Redeemed,
PC.Source,
C.ID AS CreatorID,
C.Email AS CreatorEmail,
CNY.Name AS Sponsor 
FROM SummitRegistrationPromoCode PC 
INNER JOIN Member AS C ON C.ID = PC.CreatorID
INNER JOIN MemberSummitRegistrationPromoCode MC ON MC.ID = PC.ID
INNER JOIN SponsorSummitRegistrationPromoCode SSRC ON SSRC.ID = MC.ID
INNER JOIN Company CNY ON CNY.ID = SSRC.SponsorID
LEFT JOIN Member O ON O.ID = MC.OwnerID
WHERE SummitID = {$summit_id} AND PC.ClassName = 'SponsorSummitRegistrationPromoCode' 
) 
AS PROMO_CODES
SQL;

        $offset = ($page - 1 ) * $page_size;

        $sort = '';
        switch(strtolower($sort_by))
        {
            case 'code':
                $sort = ' ORDER BY PROMO_CODES.`Code` '.strtoupper($sort_dir);
                break;
            case 'type':
                $sort = ' ORDER BY PROMO_CODES.Type '.strtoupper($sort_dir);
                break;
        }

        $where = '';

        if ($type) {
            $where = "  PROMO_CODES.Type = '{$type}'";
        }

        if (!empty($term)) {

            if(!empty($where)) $where .= ' AND ';
            $where .= " (`Code` LIKE '%{$term}%' OR FirstName LIKE '%{$term}%' OR LastName LIKE '%{$term}%'
                        OR Email LIKE '%{$term}%'
                        OR CreatorEmail LIKE '%{$term}%'";

            if (is_int($term))
                $where .= " OR OwnerID = '{$term}'";

            $where .= " )";
        }

        if(!empty($where)) $base_query .= ' WHERE '.$where;

        $query_select = "SELECT * FROM ".$base_query;
        $query_select .= (!empty($sort)) ? $sort : "";
        $query_select .= ($page_size) ? " LIMIT {$offset}, {$page_size}" : "";

        $query_count  = "SELECT COUNT(ID) AS QTY FROM ".$base_query;

        $count     = intval(DB::query($query_count)->first()['QTY']);
        $res       = DB::query($query_select);
        $data      = array();

        foreach ($res as $code) {

            $code_array = [
                'id'          =>  intval($code['ID']),
                'code'        => $code['Code'],
                'email_sent'  => intval($code['EmailSent']),
                'redeemed'    => intval($code['Redeemed']),
                'source'      => $code['CreatorEmail'],
                'type'        => $code['Type'],
                'sponsor'     => $code['Sponsor'],
                'owner'       => sprintf("%s %s", $code['FirstName'], $code['LastName']),
                'owner_email' => $code['Email'],
            ];

            $data[] = $code_array;
        }

        return array($page, $page_size, $count, $data);
    }

    /**
     * @param int $summit_id
     * @param string $type
     * @param string $prefix
     * @param int $company_id
     * @param int $limit
     * @return ISummitRegistrationPromoCode[]
     */
    public function getFreeByTypeAndSummit($summit_id, $type, $prefix = '', $company_id = null, $limit)
    {
        $where = "SummitID = {$summit_id} AND Type = '{$type}'";
        $where .= (!empty($prefix)) ? " AND `Code` LIKE '{$prefix}%'" : "";

        switch ($type) {
            case 'ACCEPTED':
            case 'ALTERNATE':
                $where .= " AND IFNULL(SpeakerID, 0) = 0";
                $promocodes = SpeakerSummitRegistrationPromoCode::get()->where($where);
                break;
            case 'VIP':
            case 'ATC':
            case 'MEDIA ANALYST':
                $where .= " AND IFNULL(OwnerID, 0) = 0 AND IFNULL(FirstName, '') = ''";
                $where .= " AND IFNULL(LastName, '') = '' AND IFNULL(Email, '') = ''";
                $promocodes = MemberSummitRegistrationPromoCode::get()->where($where);
                break;
            case 'SPONSOR':
                $where .= " AND SponsorID = {$company_id} AND IFNULL(OwnerID, 0) = 0 AND IFNULL(FirstName, '') = ''";
                $where .= " AND IFNULL(LastName, '') = '' AND IFNULL(Email, '') = ''";
                $promocodes = SponsorSummitRegistrationPromoCode::get()->where($where);
                break;
        }

        return $promocodes->sort("Code")->limit($limit);
    }


    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $term
     * @param string $sort_by
     * @param string $sort_dir
     * @return array
     */
    public function searchSponsorByTermAndSummitPaginated($summit_id, $page= 1, $page_size = 10, $term = '', $sort_by = 'sponsor', $sort_dir = 'asc')
    {
        $offset = ($page - 1 ) * $page_size;
        $sort = '';
        switch(strtolower($sort_by))
        {
            case 'sponsor':
                $sort = ' ORDER BY C.`Name` '.strtoupper($sort_dir);
                break;
        }

        $query = <<<SQL
SELECT C.ID,C.Name,GROUP_CONCAT(PC.Code SEPARATOR ', ') AS Codes
FROM SponsorSummitRegistrationPromoCode AS SPC
LEFT JOIN SummitRegistrationPromoCode AS PC ON PC.ID = SPC.ID
LEFT JOIN Company AS C ON C.ID = SPC.SponsorID
WHERE SummitID = {$summit_id}
GROUP BY SPC.SponsorID
HAVING (C.Name LIKE '%{$term}%' OR Codes LIKE '%{$term}%' )
{$sort}
SQL;

        $res       = DB::query($query);
        $count     = $res->numRecords();
        $res       = DB::query($query." LIMIT {$offset}, {$page_size}");
        $data = array();

        foreach ($res as $code) {
            $code_array = array(
                'id' => $code['ID'],
                'sponsor' => $code['Name'],
                'codes' => $code['Codes'],
            );

            $data[] = $code_array;
        }

        return array($page, $page_size, $count, $data);
    }

    /**
     * @param int $summit_id
     * @param string $code
     * @return ISummitRegistrationPromoCode
     */
    public function getByCode($summit_id, $code)
    {
        $query = new QueryObject();
        $query->addAndCondition(QueryCriteria::equal('SummitID', $summit_id));
        $query->addAndCondition(QueryCriteria::equal('Code', $code));

        return $this->getBy($query);
    }

    /**
     * @param int $summit_id
     * @param int $company_id
     * @return ISummitRegistrationPromoCode[]
     */
    public function getBySponsor($summit_id, $company_id)
    {
        $promo_codes = SponsorSummitRegistrationPromoCode::get()->where("SummitID = $summit_id AND SponsorID = $company_id");

        return $promo_codes;
    }

    /**
     * @param int $summit_id
     * @return ArrayList
     */
    public function getGroupedBySponsor($summit_id)
    {
        $query = <<<SQL
        SELECT C.ID AS ID,C.Name,GROUP_CONCAT(PC.Code SEPARATOR ', ') AS Codes
        FROM SponsorSummitRegistrationPromoCode AS SPC
        LEFT JOIN SummitRegistrationPromoCode AS PC ON PC.ID = SPC.ID
        LEFT JOIN Company AS C ON C.ID = SPC.SponsorID
        WHERE PC.SummitID = {$summit_id} GROUP BY SPC.SponsorID ORDER BY C.Name LIMIT 20
SQL;
        $db_result = DB::query($query);
        $result = new ArrayList();
        foreach ($db_result as $sponsor) {
            $result->push(new ArrayData($sponsor));
        }

        return $result;
    }

    /**
     * @param int $summit_id
     * @param int $owner_id
     * @return ISummitRegistrationPromoCode
     */
    public function getByOwner($summit_id, $owner_id)
    {
        $promo_codes = MemberSummitRegistrationPromoCode::get()->where("SummitID = $summit_id AND MemberSummitRegistrationPromoCode.OwnerID = $owner_id");

        return $promo_codes;
    }

    /**
     * @param int $summit_id
     * @param int $speaker_id
     * @return ISummitRegistrationPromoCode
     */
    public function getBySpeaker($summit_id, $speaker_id)
    {
        $promo_codes = SpeakerSummitRegistrationPromoCode::get()->where("SummitID = $summit_id AND SpeakerID = $speaker_id");

        return $promo_codes;
    }

    /**
     * @param int $summit_id
     * @param string $email
     * @return ISummitRegistrationPromoCode
     */
    public function getByEmail($summit_id, $email)
    {
        $promo_codes = MemberSummitRegistrationPromoCode::get()->where("SummitID = $summit_id AND Email = '$email'");

        return $promo_codes;
    }

}