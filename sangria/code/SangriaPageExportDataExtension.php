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
 * Class SangriaPageExportDataExtension
 */
final class SangriaPageExportDataExtension extends Extension
{

    public function __construct() {
        parent::__construct();
    }

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', array(
            'ExportDataUsersByRole',
            'exportCLAUsers',
            'exportConditionrs',
            'exportGerritUsers',
            'ExportDataGerritUsers',
            'ExportDataCompanyData',
            'ExportSurveyResults',
            'exportFoundationMembers',
            'exportCompanyData',
            'exportDupUsers',
            'exportMarketplaceAdmins',
            'ExportSurveyResultsFlat',
            'ExportSpeakersData',
            'ExportSpeakersSubmissions',
            'ExportSurveyResultsByCompany',
        ));

        Config::inst()->update(get_class($this->owner), 'allowed_actions', array(
            'ExportDataUsersByRole',
            'exportCLAUsers',
            'exportConditionrs',
            'exportGerritUsers',
            'ExportDataGerritUsers',
            'ExportDataCompanyData',
            'ExportSurveyResults',
            'exportFoundationMembers',
            'exportCompanyData',
            'exportDupUsers',
            'exportMarketplaceAdmins',
            'ExportSurveyResultsFlat',
            'ExportSpeakersData',
            'ExportSpeakersSubmissions',
            'ExportSurveyResultsByCompany',
        ));

        set_time_limit(0);
    }

    public function onAfterInit() {
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
        Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
        Requirements::javascript('themes/openstack/javascript/sangria/sangria.page.export.data.js');
    }

    function ExportDataUsersByRole()
    {
        $this->Title = 'Export Users By Role';
        return $this->owner->getViewer('ExportUsersByRole')->process($this->owner);
    }

    function exportCLAUsers()
    {
        $params = $this->owner->getRequest()->getVars();
        $fields = $params['fields'];
        $groups = $params['groups'];
        $ext = $params['ext'];

        if (!isset($params['fields']) || empty($params['fields']))
            return $this->owner->httpError('412', 'missing required param fields');
        if (!isset($params['groups']) || empty($params['groups']))
            return $this->owner->httpError('412', 'missing required param groups');
        if (!isset($params['ext']) || empty($params['ext']))
            return $this->owner->httpError('412', 'missing required param ext');
        if (!count($fields)) {
            return $this->httpError('412', 'missing required param fields');
        }

        $query = new SQLQuery();
        $query->setFrom('Member');
        $query->addLeftJoin('Group_Members', 'GM.MemberID = Member.ID','GM');
        $query->addLeftJoin('Group', 'Group.ID = GM.GroupID');
        //$query->addWhere('Member.GerritID IS NOT NULL');
        $query->addWhere('GM.GroupID IN ('.implode(',',$groups).')');
        $fields['Groups'] = "GROUP_CONCAT(Group.Code SEPARATOR ' | ')";
        $query->setSelect($fields);
        $query->addGroupBy('Member.ID');
        $query->addOrderBy(array('Member.Surname','Member.FirstName'));

        $result = $query->execute();
        $filename = "MembersByRole-" . date('Ymd') . "." . $ext;
        $delimiter = ($ext == 'xls') ? "\t" : "," ;
        return CSVExporter::getInstance()->export($filename, $result, $delimiter);
    }

    function ExportDataGerritUsers()
    {
        $this->Title = 'Export Gerrit Users';
        return $this->owner->getViewer('ExportGerritUsers')->process($this->owner);
    }

    function ExportDataCompanyData()
    {
        $this->Title = 'Export Company Data';
        return $this->owner->getViewer('ExportCompanyData')->process($this->owner);
    }

    function ExportSpeakersData()
    {
        $this->Title = 'Export Speakers Data';
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $summits = Summit::get();

            return $this->owner->getViewer('ExportSpeakersData')->process($this->owner->Customise(array("Summits" => $summits)));
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $summits = Summit::get();
            $selectedSummits = array();
            foreach($summits as $summit) {
                if (isset($_POST["summit_".$summit->ID])) {
                    $selectedSummits[] = $summit->ID;
                }
            }

            $onlyApprovedSpeakers = isset($_POST["onlyApprovedSpeakers"]);
            $affiliation = $_POST["affiliation"];

            $speakersExportQuerySpecification = new SpeakersExportQuerySpecification($selectedSummits, $onlyApprovedSpeakers, $affiliation);
            $speakersExportQuery = new SpeakersExportQuery();
            $res = $speakersExportQuery->handle($speakersExportQuerySpecification);


            $ext = $_POST['ext'];
            $filename = "PresentationSpeakers_" . date('Ymd') . "." . $ext;
            $delimiter = ",";

            return CSVExporter::getInstance()->export($filename, $res->getResult()[0], $delimiter);
        }
    }

    function GetSpeakersData($sort=SangriaPageExportDataExtension::SpeakersSortSummit) {

    }

    function ExportSpeakersSubmissions()
    {
        $selected_summit_ids = $this->owner->request->postVar('summit');
        $summits = Summit::get()->sort('SummitBeginDate');

        if ($this->owner->request->isGET()) {

            return $this->owner->getViewer('ExportSpeakersSubmissions')
                ->process($this->owner->Customise(array(
                    "Summits" => $summits,
                    "statusAlternate" => 1,
                    "statusPrimary" => 1,
                )));
        }
        else if ($this->owner->request->isPOST()) {
            $status_alternate = $this->owner->request->postVar('statusAlternate');
            $status_primary = $this->owner->request->postVar('statusPrimary');
            $status_submitted = $this->owner->request->postVar('statusSubmitted');

            $speakersSubmissionsExportQuerySpecification = new SpeakersSubmissionsExportQuerySpecification($selected_summit_ids);
            $speakersSubmissionsExportQuery = new SpeakersSubmissionsExportQuery();
            $res = $speakersSubmissionsExportQuery->handle($speakersSubmissionsExportQuerySpecification);

            $submissions = array();
            foreach ($res->getResult()[0] as $submission){
                if ($submission['ListType'] == 'Group'){
                    if ($submission['Order'] <= $submission['SessionCount']){
                        if ($status_primary) $submission['Status'] = 'PRIMARY';
                        else continue;
                    } else {
                        if ($status_alternate) $submission['Status'] = 'ALTERNATE';
                        else continue;
                    }
                } else {
                    if ($status_submitted) $submission['Status'] = 'NOT ACCEPTED';
                    else continue;
                }
                $submissions[] = $submission;
            }

            $ext = $_POST['ext'];
            $filename = "PresentationSpeakers_" . date('Ymd') . "." . $ext;
            $delimiter = ",";

            return CSVExporter::getInstance()->export($filename, $submissions, $delimiter);
        }
    }

    function exportConditionrs()
    {

        $params = $this->owner->getRequest()->getVars();
        if (!isset($params['fields']) || empty($params['fields']))
            return $this->owner->httpError('412', 'missing required param fields');

        if (!isset($params['ext']) || empty($params['ext']))
            return $this->owner->httpError('412', 'missing required param ext');

        $fields = $params['fields'];
        $ext = $params['ext'];

        $sanitized_fields = array();

        if (!count($fields)) {
            return $this->httpError('412', 'missing required param fields');
        }

        $allowed_fields = array('ID' => 'ID', 'FirstName' => 'FirstName', 'SurName' => 'SurName', 'Email' => 'Email');

        for ($i = 0; $i < count($fields); $i++) {
            if (!array_key_exists($fields[$i], $allowed_fields))
                return $this->httpError('412', 'invalid field');
            array_push($sanitized_fields, 'M.' . $fields[$i]);
        }

        $sanitized_fields = implode(',', $sanitized_fields);

        $sql = <<< SQL
		SELECT {$sanitized_fields}
		, GROUP_CONCAT(G.Code, ' | ') AS Groups
		FROM Member M
		LEFT JOIN Group_Members GM on GM.MemberID = M.ID
		LEFT JOIN `Group` G  on G.ID = GM.GroupID
		WHERE GerritID IS NOT NULL
		GROUP BY M.ID
		ORDER BY M.SurName, M.FirstName;
SQL;

        $result = DB::query($sql);
        $data = array();
        array_push($fields, 'Groups');
        foreach ($result as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "CLAMembers_" . date('Ymd') . "." . $ext;
        $delimiter = ($ext == 'xls') ? "\t" : "," ;

        return CSVExporter::getInstance()->export($filename, $data, $delimiter);
    }

    function exportGerritUsers()
    {
        $params = $this->owner->getRequest()->getVars();
        if (!isset($params['status']) || empty($params['status']))
            return $this->owner->httpError('412', 'missing required param status');

        if (!isset($params['ext']) || empty($params['ext']))
            return $this->owner->httpError('412', 'missing required param ext');

        $status = $params['status'];
        $ext = $params['ext'];

        $sanitized_filters = array();
        $allowed_filter_values = array('foundation-members' => 'foundation-members', 'community-members' => 'community-members');
        for ($i = 0; $i < count($status); $i++) {
            if (!array_key_exists($status[$i], $allowed_filter_values))
                return $this->httpError('412', 'invalid filter value');
            array_push($sanitized_filters, $status[$i]);
        }

        $sanitized_filters = implode("','", $sanitized_filters);
        $sql = <<<SQL

		SELECT M.FirstName,
		   M.Surname,
	       M.Email,
		   COALESCE(NULLIF(M.SecondEmail , ''), 'N/A') AS Secondary_Email ,
	       M.GerritID,
	       COALESCE(NULLIF(M.LastCodeCommit, ''), 'N/A') AS LastCodeCommitDate,
		   g.Code as Member_Status,
		   CASE g.Code WHEN 'foundation-members' THEN (SELECT LA.Created FROM LegalAgreement LA WHERE LA.MemberID =  M.ID and LA.LegalDocumentPageID = 422 LIMIT 1) ELSE 'N/A'END AS FoundationMemberJoinDate,
		   CASE g.Code WHEN 'foundation-members' THEN 'N/A' ELSE ( SELECT ActionDate FROM FoundationMemberRevocationNotification WHERE RecipientID = M.ID AND Action = 'Revoked' LIMIT 1) END AS DateMemberStatusChanged ,
		   GROUP_CONCAT(O.Name, ' | ') AS Company_Affiliations
		FROM Member M
		LEFT JOIN Affiliation A on A.MemberID = M.ID
		LEFT JOIN Org O on O.ID = A.OrganizationID
		INNER JOIN Group_Members gm on gm.MemberID = M.ID
		INNER JOIN `Group` g on g.ID = gm.GroupID and ( g.Code = 'foundation-members' or g.Code = 'community-members')
		WHERE GerritID IS NOT NULL AND g.Code IN ('{$sanitized_filters}')
		GROUP BY M.ID;
SQL;

        $res = DB::query($sql);
        $fields = array('FirstName', 'Surname', 'Email', 'Secondary_Email', 'GerritID', 'LastCodeCommitDate', 'Member_Status', 'FoundationMemberJoinDate', 'DateMemberStatusChanged', 'Company_Affiliations');
        $data = array();

        foreach ($res as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "Gerrit_Users_" . date('Ymd') . "." . $ext;
        $delimiter = ($ext == 'xls') ? "\t" : "," ;

        return CSVExporter::getInstance()->export($filename, $data, $delimiter);
    }

    public function Groups()
    {
        $sql = <<<SQL
		SELECT G.ID,G.Code,G.Title,G.ClassName FROM `Group` G ORDER BY G.Title;
SQL;
        $result = DB::query($sql);

        // let Silverstripe work the magic

        $groups = new ArrayList();

        foreach ($result as $rowArray) {
            // $res: new Product($rowArray)
            $groups->push(new $rowArray['ClassName']($rowArray));
        }

        return $groups;
    }

    public function MarketplaceTypes()
    {
        $sql = <<<SQL
		SELECT Name,Slug,AdminGroupID FROM MarketPlaceType WHERE Active = 1 ORDER BY AdminGroupID;
SQL;
        $result = DB::query($sql);

        // let Silverstripe work the magic

        $types = new ArrayList();

        foreach ($result as $rowArray) {
            $types->push($rowArray);
        }

        return $types;
    }

    // Export CSV of all Deployment Surveys and Associated Deployments


    private function ExportSurveyResultsDataSurveyBuilder()
    {
        SangriaPage_Controller::generateDateFilters('REPORT', 'LastEdited');

        $date_filter = SangriaPage_Controller::$date_filter_query;

        $template_id = intval(Controller::curr()->getRequest()->getVar('Range'));

$query = <<<SQL
-- surveys
SELECT * FROM (
SELECT
S.ID AS SurveyID,
S.CreatedByID,
M.Email,
S.Created,
S.LastEdited,
SETPL.Name AS Step,
Q.Name AS Question,
Q.ID   AS QuestionID,
SETPL.`Order` AS StepOrder,
Q.`Order` AS QuestionOrder,
Q.ClassName AS QuestionClass,
-- SA.ID,
CASE Q.ClassName
WHEN 'SurveyDropDownQuestionTemplate'
THEN (
IF(
	( SELECT IsCountrySelector FROM SurveyDropDownQuestionTemplate DDL WHERE DDL.ID = Q.ID) = 1,
    SA.Value,
    ( SELECT GROUP_CONCAT(Value SEPARATOR '|') FROM SurveyQuestionValueTemplate WHERE FIND_IN_SET(ID, SA.Value) > 0)
    )
)
WHEN 'SurveyCheckBoxListQuestionTemplate'
THEN ( SELECT GROUP_CONCAT(Value SEPARATOR '|') FROM SurveyQuestionValueTemplate WHERE FIND_IN_SET(ID, SA.Value) > 0)
WHEN 'SurveyRankingQuestionTemplate'
THEN ( SELECT GROUP_CONCAT(Value SEPARATOR '|') FROM SurveyQuestionValueTemplate WHERE FIND_IN_SET(ID, SA.Value) > 0)
WHEN 'SurveyRadioButtonListQuestionTemplate'
THEN ( SELECT GROUP_CONCAT(Value SEPARATOR '|') FROM SurveyQuestionValueTemplate WHERE FIND_IN_SET(ID, SA.Value) > 0)
ELSE SA.Value END AS Answer
FROM Survey S
INNER JOIN SurveyStep SE ON SE.SurveyID = S.ID
INNER JOIN Member M ON M.ID = S.CreatedByID
INNER JOIN SurveyStepTemplate SETPL ON SETPL.ID = SE.TemplateID
INNER JOIN SurveyAnswer SA ON SA.StepID = SE.ID
INNER JOIN SurveyQuestionTemplate Q ON SA.QuestionID = Q.ID
INNER JOIN SurveyTemplate ST ON S.TemplateID = ST.ID
WHERE
S.ClassName = 'Survey' AND S.IsTest = 0
AND ST.ID = {$template_id}
) REPORT
WHERE {$date_filter}
ORDER BY SurveyID ASC , StepOrder ASC, QuestionOrder ASC;
SQL;
        return DB::query($query);
    }


    function ExportSurveyResults()
    {
        $fileDate = date('Ymdhis');
        $data = $this->getSurveyBuilderExportData();
        $filename = "Survey_" . $fileDate . ".csv";
        return CSVExporter::getInstance()->export($filename, $data, ',');
    }

    private function buildSurveyBuilderHeaders($flat_fields = array(), $flat_fields_entity = array())
    {
        $template_id = intval(Controller::curr()->getRequest()->getVar('Range'));

        $survey_header_query = <<<SQL
SELECT SS.Name, Q.ID AS QuestionID, Q.Name, Q.ClassName FROM SurveyTemplate S
INNER JOIN SurveyStepTemplate SS ON SS.SurveyTemplateID = S.ID
INNER JOIN SurveyQuestionTemplate Q ON Q.StepID = SS.ID
WHERE
S.ClassName = 'SurveyTemplate'
AND Q.ClassName <> 'SurveyLiteralContentQuestionTemplate'
AND S.ID = {$template_id}
ORDER BY SS.`Order`, Q.`Order`;
SQL;

        $res = DB::query($survey_header_query);
        $template_1 = array();
        $template_1['SurveyID']    = null;
        $template_1['CreatedByID'] = null;
        $template_1['Email']       = null;
        $template_1['Created']     = null;
        $template_1['LastEdited']  = null;

        foreach($res as $row)
        {
            $name = $row['Name'];
            if(in_array($name, $flat_fields))
            {
                $q = SurveyMultiValueQuestionTemplate::get()->byID(intval($row['QuestionID']));
                if(is_null($q)) continue;

                foreach($q->Values() as $v)
                {
                    $header = sprintf('%s - %s',$name, $v->Value );
                    $template_1[$header] = null;
                }
            }
            else
                $template_1[$name] = null;
        }

        $entity_survey_header_query = <<<SQL
SELECT SS.Name, Q.ID AS QuestionID, Q.Name, Q.ClassName
FROM SurveyTemplate S
INNER JOIN EntitySurveyTemplate ES ON ES.ID = S.ID
INNER JOIN SurveyStepTemplate SS ON SS.SurveyTemplateID = S.ID
INNER JOIN SurveyQuestionTemplate Q ON Q.StepID = SS.ID
WHERE
S.ClassName = 'EntitySurveyTemplate'
AND Q.ClassName <> 'SurveyLiteralContentQuestionTemplate'
AND ES.EntityName = 'Deployment'
AND ES.ParentID = {$template_id}
ORDER BY SS.`Order`, Q.`Order`;
SQL;

        $res = DB::query($entity_survey_header_query);
        $template_2 = array( 'DeploymentID' => null);
        foreach($res as $row)
        {
            $name = $row['Name'];
            if(in_array($name, $flat_fields_entity))
            {
                $q = SurveyMultiValueQuestionTemplate::get()->byID(intval($row['QuestionID']));
                if(is_null($q)) continue;

                foreach($q->Values() as $v)
                {
                    $header = sprintf('%s - %s',$name, $v->Value );
                    $template_2[$header] = null;
                }
            }
            else
            {
                if($row['ClassName'] === 'SurveyRadioButtonMatrixTemplateQuestion')
                {
                    $q = SurveyRadioButtonMatrixTemplateQuestion::get()->byID(intval($row['QuestionID']));
                    if(is_null($q)) continue;
                    foreach($q->Rows() as $r)
                    {
                        $header = sprintf('%s - %s',$name, $r->Value );
                        $template_2[$header] = null;
                    }
                }
                else
                    $template_2[$name] = null;
            }
        }

        return array($template_1, $template_2);

    }

    private function getDeploymentsData($survey_id)
    {
        $query = <<<SQL

        -- deployments
SELECT * FROM (
SELECT
ES.ParentID AS SurveyID,
S.ID AS EntityID,
S.CreatedByID,
S.Created,
S.LastEdited,
SETPL.Name AS Step,
Q.Name AS Question,
SETPL.`Order` AS StepOrder,
Q.`Order` AS QuestionOrder,
Q.ClassName AS QuestionClass,
-- SA.ID,
CASE Q.ClassName
WHEN 'SurveyDropDownQuestionTemplate'
THEN (
IF(
    ( SELECT IsCountrySelector FROM SurveyDropDownQuestionTemplate DDL WHERE DDL.ID = Q.ID) = 1,
    SA.Value,
    ( SELECT GROUP_CONCAT(Value SEPARATOR '|') FROM SurveyQuestionValueTemplate WHERE FIND_IN_SET(ID, SA.Value) > 0)
    )
)
WHEN 'SurveyCheckBoxListQuestionTemplate'
THEN ( SELECT GROUP_CONCAT(Value SEPARATOR '|') FROM SurveyQuestionValueTemplate WHERE FIND_IN_SET(ID, SA.Value) > 0)
WHEN 'SurveyRankingQuestionTemplate'
THEN ( SELECT GROUP_CONCAT(Value SEPARATOR '|') FROM SurveyQuestionValueTemplate WHERE FIND_IN_SET(ID, SA.Value) > 0)
WHEN 'SurveyRadioButtonListQuestionTemplate'
THEN ( SELECT GROUP_CONCAT(Value SEPARATOR '|') FROM SurveyQuestionValueTemplate WHERE FIND_IN_SET(ID, SA.Value) > 0)
ELSE SA.Value END AS Answer
FROM Survey S
INNER JOIN EntitySurvey ES ON ES.ID = S.ID
INNER JOIN EntitySurveyTemplate EST ON EST.ID = S.TemplateID
INNER JOIN SurveyStep SE ON SE.SurveyID = S.ID
INNER JOIN SurveyStepTemplate SETPL ON SETPL.ID = SE.TemplateID
INNER JOIN SurveyAnswer SA ON SA.StepID = SE.ID
INNER JOIN SurveyQuestionTemplate Q ON SA.QuestionID = Q.ID
WHERE
S.ClassName = 'EntitySurvey' AND S.IsTest = 0
AND EST.EntityName = 'Deployment'
AND ES.ParentID = {$survey_id}

) REPORT
ORDER BY EntityID ASC , StepOrder ASC, QuestionOrder ASC;

SQL;

        $res = DB::query($query);

        return $res;
    }

    public static function getRowsAndColumns(){
        $query = <<<SQL
        SELECT ID, `Value` FROM SurveyQuestionValueTemplate WHERE ClassName = 'SurveyQuestionRowValueTemplate';
SQL;

        $res = DB::query($query);
        $rows = array();
        foreach($res as $row)
        {
            $id = $row['ID'];
            $value = $row['Value'];
            $rows[$id] = $value;
        }

        $query = <<<SQL
        SELECT ID, `Value` FROM SurveyQuestionValueTemplate WHERE ClassName = 'SurveyQuestionColumnValueTemplate';
SQL;

        $res = DB::query($query);
        $columns = array();
        foreach($res as $row)
        {
            $id = $row['ID'];
            $value = $row['Value'];
            $columns[$id] = $value;
        }


        return array($rows, $columns);
    }

    private function getSurveyBuilderExportData($flat_fields = array(), $flat_fields_entity = array())
    {
        $res        = $this->ExportSurveyResultsDataSurveyBuilder();
        $survey_id  = 0;
        $file_data  = array();
        list($header_template1, $header_template2) = $this->buildSurveyBuilderHeaders($flat_fields, $flat_fields_entity);

        $line = $header_template1;
        list($rows, $columns)  = self::getRowsAndColumns();

        foreach ($res as $row)
        {
            if($survey_id !== intval($row['SurveyID']))
            {
                // reset
                if($survey_id > 0)
                {
                    $res2                 = $this->getDeploymentsData($survey_id);
                    $line2                = $header_template2;
                    $entity_survey_id     = 0;
                    $entities_surveys_set = array();

                    foreach($res2 as $row2)
                    {

                        if($entity_survey_id !== intval($row2['EntityID']))
                        {

                            if($entity_survey_id > 0)
                            {
                                array_push($entities_surveys_set, $line2);
                            }

                            $line2                 = $header_template2;
                            $line2['DeploymentID'] = intval($row2['EntityID']);
                            $entity_survey_id      = intval($row2['EntityID']);

                        }

                        $question         = $row2['Question'];
                        $class            = $row2['QuestionClass'];
                        $answer           = $row2['Answer'];

                        if(empty($answer)) continue;

                        if($class === 'SurveyRadioButtonMatrixTemplateQuestion')
                        {
                            $tuples = explode(',', $answer);
                            $translation = '';
                            foreach($tuples as $t)
                            {
                                $t = explode(':', $t);
                                $r = $t[0];
                                if(!isset($rows[$r])) continue;
                                $r = $rows[$r];
                                $c = $t[1];
                                if(!isset($columns[$c]))
                                {
                                   continue;
                                }
                                $c = $columns[$c];
                                $translation .= sprintf("%s:%s",$r, $c). ',';
                            }
                            $answer = trim($translation,',');
                        }

                        if(in_array($question, $flat_fields_entity))
                        {
                            if($class === 'SurveyRankingQuestionTemplate')
                            {

                                $question_id = intval($row['QuestionID']);
                                $q = SurveyRankingQuestionTemplate::get()->byID($question_id);
                                if(!is_null($q))
                                {
                                    $values = $q->Values()->sort('Order', 'ASC');
                                    $options = array();
                                    foreach($values as $v)
                                    {
                                        array_push($options, $v->Value);
                                    }
                                    $answers = explode('|', $row['Answer']);
                                    foreach($options as $o)
                                    {
                                        $index = array_search($o, $answers);
                                        $line[sprintf("%s - %s", $question, $o)] =  $index === false ? '0' : ($index + 1);;
                                    }
                                }
                            }
                            else
                            {
                                $answers = explode('|', $answer);
                                foreach ($answers as $a) {
                                    $line2[sprintf("%s - %s", $question, $a)] = 1;
                                }
                            }
                        }
                        else
                        {
                            if($class === 'SurveyRadioButtonMatrixTemplateQuestion')
                            {
                                $answer = preg_split('/,/',$answer);
                                foreach($answer as $a)
                                {
                                    $a =  preg_split('/:/',$a);
                                    if( count($a) !== 2) continue;
                                    $line2[sprintf("%s - %s",$question,$a[0])] = $a[1];
                                }
                            }
                            else
                                $line2[$question] = $answer;
                        }

                    }

                    if(isset($line2['DeploymentID']) && intval($line2['DeploymentID']) > 0)
                        array_push($entities_surveys_set, $line2);

                    if(count($entities_surveys_set) === 0)
                    {
                        array_push($entities_surveys_set, $header_template2);
                    }

                    foreach($entities_surveys_set as $line2)
                    {
                        array_push($file_data, array_merge($line, $line2));
                    }

                }
                $line = $header_template1;
                $line['SurveyID']    = intval($row['SurveyID']);
                $line['CreatedByID'] = intval($row['CreatedByID']);
                $line['Email']       = $row['Email'];
                $line['Created']     = $row['Created'];
                $line['LastEdited']  = $row['LastEdited'];
                $survey_id           = intval($row['SurveyID']);
            }

            $question        = $row['Question'];
            $class           = $row['QuestionClass'];

            if(in_array($question, $flat_fields))
            {
                if($class === 'SurveyRankingQuestionTemplate')
                {

                    $question_id = intval($row['QuestionID']);
                    $q = SurveyRankingQuestionTemplate::get()->byID($question_id);
                    if(!is_null($q))
                    {
                        $values = $q->Values()->sort('Order', 'ASC');
                        $options = array();
                        foreach($values as $v)
                        {
                            array_push($options, $v->Value);
                        }
                        $answers = explode('|', $row['Answer']);
                        foreach($options as $o)
                        {
                            $index = array_search($o, $answers);
                            $line[sprintf("%s - %s", $question, $o)] =  $index === false ? '0' : ($index + 1);;
                        }
                    }
                }
                else
                {
                    $answers = explode('|', $row['Answer']);
                    foreach ($answers as $a) {
                        $line[sprintf("%s - %s", $question, $a)] = 1;
                    }
                }
            }
            else
                $line[$question] = $row['Answer'];
        }

        if(isset($line['SurveyID']) && intval($line['SurveyID']) > 0)
            array_push($file_data, array_merge($line, $header_template2));

        return $file_data;
    }

    function ExportSurveyResultsFlat()
    {
        $fileDate = date('Ymdhis');

        $file_data = $this->getSurveyBuilderExportData
        (
                array
                (
                    "OpenStackActivity",
                    "BusinessDrivers",
                    "InteractingClouds",
                    "Stacks",
                ),
                array
                (
                    "CurrentReleases",
                    "UsedPackages",
                    "IdentityDrivers",
                    "WorkloadsCategories",
                    "DeploymentTools",
                    "PaasTools",
                    "Hypervisors",
                    "UsedDBForOpenStackComponents",
                    "NetworkDrivers",
                    "OperatingSystems",
                    "SupportedFeatures",
                    "WhyNovaNetwork",
                    "ProjectsUsed",
                    "ProjectsUsedPoC",
                )
        );

        $filename = "Survey_Flat_" . $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $file_data, ',');
    }

    function ExportSurveyResultsByCompany()
    {
        $fileDate = date('Ymdhis');

        SangriaPage_Controller::generateDateFilters('S', 'LastEdited');

        $date_filter = SangriaPage_Controller::$date_filter_query;

        $template_id = intval(Controller::curr()->getRequest()->getVar('Range'));

        $query = <<<SQL

        SELECT  S.ID AS SurveyID , A.Value AS Org , M.Email from SurveyAnswer A
INNER JOIN SurveyQuestionTemplate QT ON QT.ID = A.QuestionID
INNER JOIN SurveyStep ST on ST.ID = A.StepID
INNER JOIN Survey S ON S.ID = ST.SurveyID
INNER JOIN Member M ON M.ID = S.CreatedByID
WHERE QT.ClassName = 'SurveyOrganizationQuestionTemplate'
AND S.TemplateID = {$template_id} AND S.IsTest = 0 AND {$date_filter}
ORDER BY Org ASC;

SQL;
        $res = DB::query($query);
        $file_data = array();

        foreach($res as $row)
        {
            array_push($file_data, $row);
        }
        $filename = "Survey_by_companies" . $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $file_data, ',');
    }

   function exportFoundationMembers()
    {
        $params = $this->owner->getRequest()->getVars();
        if (!isset($params['fields']) || empty($params['fields']))
            return $this->owner->httpError('412', 'missing required param fields');

        if (!isset($params['ext']) || empty($params['ext']))
            return $this->owner->httpError('412', 'missing required param ext');

        $fields = $params['fields'];
        $ext = $params['ext'];

        $sanitized_fields = array();

        if (!count($fields)) {
            return $this->owner->httpError('412', 'missing required param fields');
        }

        $allowed_fields = array('ID' => 'ID', 'FirstName' => 'FirstName', 'SurName' => 'SurName', 'Email' => 'Email');

        for ($i = 0; $i < count($fields); $i++) {
            if (!array_key_exists($fields[$i], $allowed_fields))
                return $this->httpError('412', 'invalid field');
            array_push($sanitized_fields, 'Member.' . $fields[$i]);
        }

        $query = new SQLQuery();

        $query->setFrom('Member');
        $query->setSelect($sanitized_fields);
        $query->addInnerJoin('Group_Members', 'Group_Members.MemberID = Member.ID');
        $query->addInnerJoin('Group', "Group.ID = Group_Members.GroupID AND Group.Code='foundation-members'");
        $query->setOrderBy('SurName,FirstName');

        $result = $query->execute();

        $data = array();

        foreach ($result as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "Foundation_Members_" . date('Ymd') . "." . $ext;

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }

    function exportCompanyData()
    {
        $params = $this->owner->getRequest()->getVars();

        if (!isset($params['report_name']) || empty($params['report_name']) || !count($params['report_name']))
            return $this->owner->httpError('412', 'missing required param report_name');

        if (!isset($params['extension']) || empty($params['extension']))
            return $this->owner->httpError('412', 'missing required param extension');

        $report_name = (isset($params['report_name'])) ? $params['report_name'] : '';
        $fields = (isset($params['fields'])) ? $params['fields'] : array();
        $ext = $params['extension'];

        $query = new SQLQuery();

        if($report_name) {
            switch($report_name) {
                case 'sponsorship_type' :
                    $query->setFrom('Company');
                    $query->addLeftJoin('SummitSponsorPage_Companies', 'SummitSponsorPage_Companies.CompanyID = Company.ID');
                    $query->addLeftJoin('Summit', 'Summit.ID = SummitSponsorPage_Companies.SummitID');
                    $query->addWhere('Summit.Active','1');
                    $fields = array_merge($fields,array('Sponsorship'=>'SummitSponsorPage_Companies.SponsorshipType','Summit ID'=>'Summit.ID'));

                    $query->setSelect($fields);
                    $query->addOrderBy('SummitSponsorPage_Companies.SponsorshipType');

                    $filename = "Sponsorship_Levels_" . date('Ymd') . "." . $ext;
                    break;
                case 'member_level' :
                    $query->setFrom('Company');
                    array_push($fields, 'Company.MemberLevel');
                    $query->setSelect($fields);

                    $filename = "Foundation_Levels_" . date('Ymd') . "." . $ext;
                    break;
                case 'users_roles' :
                    $query->setFrom('Company');
                    $query->addInnerJoin('Company_Administrators', 'Company_Administrators.CompanyID = Company.ID');
                    $query->addLeftJoin('Member', 'Member.ID = Company_Administrators.MemberID');
                    $query->addLeftJoin('Group', 'Group.ID = Company_Administrators.GroupID');
                    array_push($fields, 'Group.Title');
                    $query->setSelect($fields);
                    $query->addOrderBy('Company.Name');

                    $filename = "User_Roles_" . date('Ymd') . "." . $ext;
                    break;
                case 'affiliates' :
                    $query->setFrom('Org');
                    $query->addLeftJoin('Affiliation', 'Affiliation.OrganizationID = Org.ID');
                    $query->addLeftJoin('Member', 'Member.ID = Affiliation.MemberID');
                    $fields = array_merge($fields,array('Is Current'=>'Affiliation.Current','Job Title'=>'Affiliation.JobTitle'));
                    $query->setSelect($fields);
                    $query->addOrderBy('Org.Name');

                    $filename = "Employees_Affiliates_" . date('Ymd') . "." . $ext;
                    break;
                case 'deployments' :
                    $query->setFrom('Org');
                    $query->addInnerJoin('Deployment', 'Deployment.OrgID = Org.ID');
                    $custom_fields = array('Creation'=>'Deployment.Created','Edited'=>'Deployment.LastEdited',
                                           'Label'=>'Deployment.Label','Is Public'=>'Deployment.IsPublic');
                    $fields = array_merge($fields,$custom_fields);
                    $query->setSelect($fields);
                    $query->selectField("CONCAT('http://openstack.org/sangria/DeploymentDetails/',Deployment.ID)","Link");
                    $query->addOrderBy('Org.Name');

                    $filename = "Deployments_" . date('Ymd') . "." . $ext;
                    break;
                case 'deployment_surveys' :
                    $query->setFrom('Org');
                    $query->addLeftJoin('DeploymentSurvey', 'DeploymentSurvey.OrgID = Org.ID');
                    $query->addLeftJoin('Member', 'DeploymentSurvey.MemberID = Member.ID');
                    $custom_fields = array('Creation'=>'DeploymentSurvey.Created','Edited'=>'DeploymentSurvey.LastEdited',
                                        'Title'=>'DeploymentSurvey.Title','City'=>'DeploymentSurvey.PrimaryCity',
                                        'State'=>'DeploymentSurvey.PrimaryState','Country'=>'DeploymentSurvey.PrimaryCountry',
                                        'Org Size'=>'DeploymentSurvey.OrgSize','Is Group Member'=>'DeploymentSurvey.UserGroupMember',
                                        'Group Name'=>'DeploymentSurvey.UserGroupName','Ok to Contact'=>'DeploymentSurvey.OkToContact');

                    //insert custom fields after org fields
                    $pos = -1;
                    foreach ($fields as $field) {
                        $pos++;
                        if (strpos($field,'Org') !== false) continue;
                        else {
                            array_splice($fields,$pos,0,$custom_fields);
                            break;
                        }
                    }

                    $query->setSelect($fields);
                    $query->selectField("CONCAT('http://openstack.org/sangria/SurveyDetails/',DeploymentSurvey.ID)","Link");

                    $filename = "Deployment_Surveys" . date('Ymd') . "." . $ext;
                    break;
                case 'speakers' :
                    $query->setFrom('PresentationSpeaker');
                    $query->addLeftJoin('Affiliation', 'Affiliation.MemberID = PresentationSpeaker.MemberID');
                    $query->addLeftJoin('Org', 'Affiliation.OrganizationID = Org.ID');
                    $query->addLeftJoin('Summit', 'Summit.ID = PresentationSpeaker.SummitID');
                    $custom_fields = array('Speaker Name'=>'PresentationSpeaker.FirstName',
                                        'Speaker Surname'=>'PresentationSpeaker.LastName','Summit'=>'Summit.Name');
                    $fields = array_merge($fields,$custom_fields);
                    $query->setSelect($fields);

                    $filename = "Speakers_" . date('Ymd') . "." . $ext;
                    break;
            }
        }

        //die($query->sql());
        $result = $query->execute();
        $delimiter = ($ext == 'xls') ? "\t" : "," ;

        return CSVExporter::getInstance()->export($filename, $result, $delimiter);
    }

    public function exportDupUsers()
    {

        $fileDate = date('Ymdhis');

        SangriaPage_Controller::generateDateFilters('s');

        $sql = <<< SQL
select FirstName, Surname, count(FirstName) AS Qty , group_concat(Email SEPARATOR '|') AS Emails,group_concat(ID SEPARATOR '|') AS MemberIds
from Member
group by FirstName, Surname
having count(FirstName) > 1
order by FirstName, Surname;
SQL;

        $res = DB::query($sql);

        $fields = array('FirstName', 'Surname', 'Qty', 'Emails', 'MemberIds');
        $data = array();

        foreach ($res as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "Duplicate_Users_" . $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }

    public function exportMarketplaceAdmins()
    {

        $params = $this->owner->getRequest()->getVars();
        if (!isset($params['marketplace_type']) || empty($params['marketplace_type']))
            return $this->owner->httpError('412', 'missing required param marketplace type');

        $marketplace_type = $params['marketplace_type'];

        $filters_string = implode("','", $marketplace_type);

        $fileDate = date('Ymdhis');

        SangriaPage_Controller::generateDateFilters('s');

        $sql = <<< SQL
SELECT M.FirstName, M.Surname, M.Email, C.Name AS Company, GROUP_CONCAT(MT.Name ORDER BY MT.Name ASC SEPARATOR ' - ') AS Marketplace
FROM Member AS M
INNER JOIN ( SELECT MemberID, CompanyID, GroupID FROM Company_Administrators WHERE Company_Administrators.GroupID IN ('{$filters_string}') ) AS CA ON CA.MemberID = M.ID
INNER JOIN Company AS C ON C.ID = CA.CompanyID
INNER JOIN MarketPlaceType AS MT ON MT.AdminGroupID = CA.GroupID
GROUP BY M.FirstName, M.Surname, M.Email, C.Name
ORDER BY M.Email, C.Name ;
SQL;

        $res = DB::query($sql);

        $fields = array('FirstName', 'Surname', 'Email', 'Company', 'Marketplace');
        $data = array();

        foreach ($res as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = str_replace(',', ' ', $row[$field]); //commas tabs cell in excel
            }
            array_push($data, $member);
        }

        $filename = "Marketplace_Admins_" . $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }
}