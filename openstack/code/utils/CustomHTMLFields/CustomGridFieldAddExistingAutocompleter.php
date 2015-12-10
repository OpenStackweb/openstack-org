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
class CustomGridFieldAddExistingAutocompleter extends GridFieldAddExistingAutocompleter
{
    public function doSearch($gridField, $request) {
        $dataClass = $gridField->getList()->dataClass();
        $allList   = $this->searchList ? $this->searchList : DataList::create($dataClass);

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

        if(!$gridField->getList() instanceof UnsavedRelationList)
            $allList = $allList->subtract($gridField->getList());

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
}