<?php

/**
 * Copyright 2022 OpenStack Foundation
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
class CustomAssetAdmin extends AssetAdmin
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getEditForm($id = null, $fields = null) {

        $form = LeftAndMain::getEditForm($id, $fields);
        $folder = $this->currentPage();
        $fields = $form->Fields();
        $title = ($folder && $folder->isInDB()) ? $folder->Title : _t('AssetAdmin.FILES', 'Files');
        $fields->push(new HiddenField('ID', false, $folder ? $folder->ID : null));

        // File listing
        $gridFieldConfig = GridFieldConfig::create()->addComponents(
            new GridFieldToolbarHeader(),
            new GridFieldSortableHeader(),
            new GridFieldFilterHeader(),
            new GridFieldDataColumns(),
            new GridFieldPaginator(self::config()->page_length),
            new GridFieldEditButton(),
            new GridFieldDeleteAction(),
            new GridFieldDetailForm(),
            GridFieldLevelup::create($folder->ID)->setLinkSpec('admin/assets/show/%d')
        );

        $gridField = GridField::create('File', $title, $this->getList(), $gridFieldConfig);
        $columns = $gridField->getConfig()->getComponentByType('GridFieldDataColumns');
        $columns->setDisplayFields(array(
            'StripThumbnail' => '',
            'Title' => _t('File.Title', 'Title'),
            'Created' => _t('AssetAdmin.CREATED', 'Date'),
            'Size' => _t('AssetAdmin.SIZE', 'Size'),
        ));
        $columns->setFieldCasting(array(
            'Created' => 'SS_Datetime->Nice'
        ));
        $gridField->setAttribute(
            'data-url-folder-template',
            Controller::join_links($this->Link('show'), '%s')
        );

        if($folder->canCreate()) {
            $uploadBtn = new LiteralField(
                'UploadButton',
                sprintf(
                    '<a class="ss-ui-button font-icon-upload cms-panel-link" data-pjax-target="Content" data-icon="drive-upload" href="%s">%s</a>',
                    Controller::join_links(singleton('CMSFileAddController')->Link(), '?ID=' . $folder->ID),
                    _t('Folder.UploadFilesButton', 'Upload')
                )
            );
        } else {
            $uploadBtn = null;
        }

        if(!$folder->hasMethod('canAddChildren') || ($folder->hasMethod('canAddChildren') && $folder->canAddChildren())) {
            // TODO Will most likely be replaced by GridField logic
            $addFolderBtn = new LiteralField(
                'AddFolderButton',
                sprintf(
                    '<a class="ss-ui-button font-icon-plus-circled cms-add-folder-link" data-icon="add" data-url="%s" href="%s">%s</a>',
                    Controller::join_links($this->Link('AddForm'), '?' . http_build_query(array(
                            'action_doAdd' => 1,
                            'ParentID' => $folder->ID,
                            'SecurityID' => $form->getSecurityToken()->getValue()
                        ))),
                    Controller::join_links($this->Link('addfolder'), '?ParentID=' . $folder->ID),
                    _t('Folder.AddFolderButton', 'Add folder')
                )
            );
        } else {
            $addFolderBtn = '';
        }

        $syncButton = null;

        // Move existing fields to a "details" tab, unless they've already been tabbed out through extensions.
        // Required to keep Folder->getCMSFields() simple and reuseable,
        // without any dependencies into AssetAdmin (e.g. useful for "add folder" views).
        if(!$fields->hasTabset()) {
            $tabs = new TabSet('Root',
                $tabList = new Tab('ListView', _t('AssetAdmin.ListView', 'List View')),
                $tabTree = new Tab('TreeView', _t('AssetAdmin.TreeView', 'Tree View'))
            );
            $tabList->addExtraClass("content-listview cms-tabset-icon list");
            $tabTree->addExtraClass("content-treeview cms-tabset-icon tree");
            if($fields->Count() && $folder && $folder->isInDB()) {
                $tabs->push($tabDetails = new Tab('DetailsView', _t('AssetAdmin.DetailsView', 'Details')));
                $tabDetails->addExtraClass("content-galleryview cms-tabset-icon edit");
                foreach($fields as $field) {
                    $fields->removeByName($field->getName());
                    $tabDetails->push($field);
                }
            }
            $fields->push($tabs);
        }

        // we only add buttons if they're available. User might not have permission and therefore
        // the button shouldn't be available. Adding empty values into a ComposteField breaks template rendering.
        $actionButtonsComposite = CompositeField::create()->addExtraClass('cms-actions-row');
        if($uploadBtn) $actionButtonsComposite->push($uploadBtn);
        if($addFolderBtn) $actionButtonsComposite->push($addFolderBtn);
        if($syncButton) $actionButtonsComposite->push($syncButton);

        // List view
        $fields->addFieldsToTab('Root.ListView', array(
            $actionsComposite = CompositeField::create(
                $actionButtonsComposite
            )->addExtraClass('cms-content-toolbar field'),
            $gridField
        ));

        $treeField = new LiteralField('Tree', '');
        // Tree view
        $fields->addFieldsToTab('Root.TreeView', array(
            clone $actionsComposite,
            // TODO Replace with lazy loading on client to avoid performance hit of rendering potentially unused views
            new LiteralField(
                'Tree',
                FormField::create_tag(
                    'div',
                    array(
                        'class' => 'cms-tree',
                        'data-url-tree' => $this->Link('getsubtree'),
                        'data-url-savetreenode' => $this->Link('savetreenode')
                    ),
                    $this->SiteTreeAsUL()
                )
            )
        ));

        // Move actions to "details" tab (they don't make sense on list/tree view)
        $actions = $form->Actions();
        $saveBtn = $actions->fieldByName('action_save');
        $deleteBtn = $actions->fieldByName('action_delete');
        $actions->removeByName('action_save');
        $actions->removeByName('action_delete');
        if(($saveBtn || $deleteBtn) && $fields->fieldByName('Root.DetailsView')) {
            $fields->addFieldToTab(
                'Root.DetailsView',
                CompositeField::create($saveBtn,$deleteBtn)->addExtraClass('Actions')
            );
        }

        $fields->setForm($form);
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));
        // TODO Can't merge $FormAttributes in template at the moment
        $form->addExtraClass('cms-edit-form ' . $this->BaseCSSClasses());
        $form->setAttribute('data-pjax-fragment', 'CurrentForm');
        $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');

        $this->extend('updateEditForm', $form);

        return $form;
    }
}