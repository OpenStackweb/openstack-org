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
class SponsorsGridFieldAddExistingAutocompleter extends GridFieldAddExistingAutocompleter
{
    protected $summit_id;

    public function doSearch($gridField, $request) {
        $dataClass = $gridField->getList()->dataClass();
        $allList   = $this->searchList;

        if ($this->getCompanyList($gridField))
            $allList = $allList->subtract($this->getCompanyList($gridField));

        $searchFields = ($this->getSearchFields())
            ? $this->getSearchFields()
            : $this->scaffoldSearchFields($dataClass);
        if(!$searchFields) {
            throw new LogicException(
                sprintf('GridFieldAddExistingAutocompleter: No searchable fields could be found for class "%s"',
                    $dataClass));
        }

        $params = array();
        foreach($searchFields as $searchField) {
            $name = (strpos($searchField, ':') !== FALSE) ? $searchField : "$searchField:StartsWith";
            $params[$name] = $request->getVar('gridfield_relationsearch');
        }

        $results = $allList
            ->filterAny($params)
            ->sort(strtok($searchFields[0], ':'), 'ASC')
            ->limit($this->getResultsLimit());

        $json = array();
        $originalSourceFileComments = Config::inst()->get('SSViewer', 'source_file_comments');
        Config::inst()->update('SSViewer', 'source_file_comments', false);
        foreach($results as $result) {
            $json[$result->ID] = html_entity_decode(SSViewer::fromString($this->resultsFormat)->process($result));
        }
        Config::inst()->update('SSViewer', 'source_file_comments', $originalSourceFileComments);

        return Convert::array2json($json);
    }

    public function getManipulatedData(GridField $gridField, SS_List $dataList) {
        if(!$gridField->State->GridFieldAddRelation) {
            return $dataList;
        }
        $objectID = Convert::raw2sql($gridField->State->GridFieldAddRelation);
        if($objectID) {
            $object = DataObject::get_by_id('Company', $objectID);
            if($object) {
                $sponsor = new Sponsor();
                $sponsor->CompanyID = $objectID;
                $sponsor->SummitID = $this->getSummitID();
                $sponsor->write();
                $dataList->add($sponsor);
            }
        }
        $gridField->State->GridFieldAddRelation = null;
        return $dataList;
    }

    public function getCompanyList($gridField) {
        $company_ids = $gridField->getList()->column('CompanyID');
        $companies = null;

        if (count($company_ids))
            $companies = Company::get()->byIDs($company_ids);

        return $companies;
    }

    public  function getSummitID() {
        return $this->summit_id;
    }

    public function setSummitID ($summit_id) {
        $this->summit_id = (int)$summit_id;
    }

}