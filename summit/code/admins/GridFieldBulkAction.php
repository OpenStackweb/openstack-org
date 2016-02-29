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
abstract class GridFieldBulkAction implements GridField_HTMLProvider, GridField_URLHandler, GridField_ColumnProvider
{

    protected $targetFragment;

    protected $gridField;

    protected $title;

    private static $allowed_actions = array(
        'handleAssignBulkAction'
    );

    public function __construct() {
        $this->targetFragment = 'header';
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    protected abstract function getEntities();

    public function getHTMLFragments($gridField)
    {
        $this->gridField = $gridField;
        Requirements::javascript('summit/javascript/GridFieldBulkAction.js');
        Requirements::css('summit/css/GridFieldBulkAction.css');
        $field = new DropdownField(sprintf('%s[EntityID]', __CLASS__), '', $this->getEntities());
        $field->setEmptyString("-- select --");
        $field->addExtraClass('no-change-track');
        $field->addExtraClass('select-entity');
        $data = new ArrayData(array(
            'Title'      => $this->title,
            'Link'       => Controller::join_links($gridField->Link(), 'assignBulkAction', '{entityID}'),
            'ClassField' => $field,
            'Colspan'    => (count($gridField->getColumns()) - 1),
        ));

        return array(
            $this->targetFragment => $data->renderWith(__CLASS__)
        );
    }

    public function getURLHandlers($gridField)
    {
        return array(
            'assignBulkAction/$EntityID!' => 'handleAssignBulkAction'
        );
    }

    public function getRecordIDList()
    {
        $vars = Controller::curr()->getRequest()->requestVars();

        return isset($vars['records'])? $vars['records']:array();
    }

    protected abstract function processRecordIds(array $ids, $entity_id, $gridField, $request);

    public function handleAssignBulkAction($gridField, $request)
    {
        $entity_id       = $request->param('EntityID');
        $controller      = $gridField->getForm()->Controller();
        $this->gridField = $gridField;
        $ids             = $this->getRecordIDList();
        $this->processRecordIds($ids, $entity_id, $gridField, $request);
        $response = new SS_HTTPResponse(Convert::raw2json(array(
            'done' => true,
            'records' => $ids,
        )));
        $response->addHeader('Content-Type', 'text/json');
        $response->setStatusCode(200);
        return $response;
    }

    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('BulkSelect', $columns)) {
            $columns[] = 'BulkSelect';
        }
    }

    /**
     * Names of all columns which are affected by this component.
     * @param GridField $gridField
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return array('BulkSelect');
    }

    /**
     * HTML for the column, content of the <td> element.
     * @param  GridField $gridField
     * @param  DataObject $record - Record displayed in this row
     * @param  string $columnName
     * @return string - HTML for the column. Return NULL to skip.
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        $cb = CheckboxField::create('bulkSelect_'.$record->ID)
            ->addExtraClass('bulkSelect no-change-track')
            ->setAttribute('data-record', $record->ID);
        return $cb->Field();
    }

    /**
     * Attributes for the element containing the content returned by {@link getColumnContent()}.
     * @param  GridField $gridField
     * @param  DataObject $record displayed in this row
     * @param  string $columnName
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array('class' => 'col-bulkSelect');
    }

    /**
     * Additional metadata about the column which can be used by other components,
     * e.g. to set a title for a search column header.
     * @param GridField $gridField
     * @param string $columnName
     * @return array - Map of arbitrary metadata identifiers to their values.
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == 'BulkSelect') {
            return array('title' => 'Select');
        }
    }
}