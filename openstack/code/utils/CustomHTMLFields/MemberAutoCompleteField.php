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
class MemberAutoCompleteField extends TextField
{
    /**
     * @var array
     */
    public static $allowed_actions = array
    (
        'suggest',
    );

    public function __construct($name, $title = null, $value = '', $form = null)
    {
        parent::__construct($name, $title, $value, $maxLength = null, $form);
    }

    public function Field($properties = array())
    {
        Requirements::javascript('openstack/code/utils/CustomHTMLFields/js/MemberAutoCompleteField.js');
        Requirements::css('openstack/code/utils/CustomHTMLFields/css/MemberAutoCompleteField.css');
        $this->setAttribute('data-ss-member-field-suggest-url', $this->getSuggestURL());
        $this->addExtraClass('ss-member-autocomplete-field');

        return $this
            ->customise($properties)
            ->renderWith(array("MemberAutoCompleteField"));
    }

    protected function getSuggestURL()
    {
        return Controller::join_links($this->Link(), 'suggest');
    }

    private $member_id = 0;

    public function setValue($value, $source = null)
    {
        if($source instanceof DataObject)
        {
            $name            = $this->getName();
            $email           = $source->$name()->Email;
            $this->member_id = $source->$name()->ID;
            parent::setValue($email);
        }
    }

    public function getHiddenAttributesHTML()
    {
        return sprintf("value='%s'", $this->member_id);
    }


    /**
     * Returns a JSON string of tags, for lazy loading.
     *
     * @param SS_HTTPRequest $request
     *
     * @return SS_HTTPResponse
     */
    public function suggest(SS_HTTPRequest $request)
    {
        $members = $this->getMembers($request->getVar('term'));

        $response = new SS_HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(json_encode($members));

        return $response;
    }

    protected $lazyLoadItemLimit = 10;


    protected function getMembers($term)
    {
        $term  = Convert::raw2sql($term);
        $query = Member::get()
            ->where("Email LIKE '%{$term}%' OR FirstName LIKE '%{$term}%' OR Surname LIKE '%{$term}%' ")
            ->sort('Email')
            ->limit($this->getLazyLoadItemLimit());

        $items = array();

        foreach ($query->map('ID', 'Email') as $id => $title) {
            if (!in_array($title, $items)) {
                $items[] = array(
                    'id' => $id,
                    'label' => $title,
                    'value' => $title
                );
            }
        }

        return $items;
    }

    public function getLazyLoadItemLimit()
    {
        return $this->lazyLoadItemLimit;
    }

    public function saveInto(DataObjectInterface $record)
    {
        $field_name = $this->getName().'ID';
        $member_id   = intval($_POST[$field_name]);
        $record->$field_name = $member_id;
    }
}