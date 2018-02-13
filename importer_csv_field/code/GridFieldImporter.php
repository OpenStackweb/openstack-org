<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class GridFieldImporter
 */
final class GridFieldImporter implements GridField_HTMLProvider, GridField_URLHandler
{

    /**
     * Fragment to write the button to
     * @var string
     */
    protected $targetFragment;

    /**
     * The BulkLoader to load with
     * @var string
     */
    protected $loader = null;

    /**
     * Can the user clear records
     * @var boolean
     */
    protected $canClearData = true;

    public function __construct($targetFragment = "after")
    {
        $this->targetFragment = $targetFragment;
    }

    /**
     * Get the html/css button and upload field to perform import.
     */
    public function getHTMLFragments($gridField)
    {
        $button = new GridField_FormAction(
            $gridField,
            'import',
            _t('TableListField.CSVIMPORT', 'Import from CSV'),
            'import',
            null
        );
        $button->setAttribute('data-icon', 'drive-upload');
        $button->addExtraClass('no-ajax');
        $uploadfield = $this->getUploadField($gridField);
        $data = array(
            'Button' => $button,
            'UploadField' => $uploadfield
        );
        $importerHTML = ArrayData::create($data)->renderWith("GridFieldImporter");
        Requirements::javascript('importer_csv_field/javascript/GridFieldImporter.js');

        return array(
            $this->targetFragment => $importerHTML
        );
    }

    /**
     * Return a configured UploadField instance
     *
     * @param  GridField $gridField Current GridField
     * @return UploadField          Configured UploadField instance
     */
    public function getUploadField(GridField $gridField)
    {
        $uploadField = UploadField::create(
            $gridField->Name."_ImportUploadField", 'Upload CSV'
        )
            ->setForm($gridField->getForm())
            ->setConfig('url', $gridField->Link('importer/upload'))
            ->setConfig('edit_url', $gridField->Link('importer/import'))
            ->setConfig('allowedMaxFileNumber', 1)
            ->setConfig('changeDetection', false)
            ->setConfig('canPreviewFolder', false)
            ->setConfig('canAttachExisting', false)
            ->setConfig('overwriteWarning', false)
            ->setAllowedExtensions(array('csv'))
            ->setFolderName('csvImports') //TODO: don't store temp CSV in assets
            ->addExtraClass("import-upload-csv-field");

        return $uploadField;
    }

    public function getActions($gridField)
    {
        return array('importer');
    }

    public function getURLHandlers($gridField)
    {
        return array(
            'importer' => 'handleImporter'
        );
    }

    /**
     * Pass importer requests to a new GridFieldImporter_Request
     */
    public function handleImporter($gridField, $request = null)
    {
        // $controller = $gridField->getForm()->getController();
        //$handler    = new GridFieldImporter_Request($gridField, $this, $controller);
        if(is_null($this->handler)) return;
        return $this->handler->handleRequest($request, DataModel::inst());
    }

    /**
     * @var GridFieldImporter_Request
     */
    private $handler;

    public function setHandler(GridFieldImporter_Request $handler){
        $this->handler = $handler;
    }
}