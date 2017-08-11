<?php
/**
 * Copyright 2017 Openstack Foundation
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
 * Class Book
 */
final class Book
	extends DataObject
	implements IBook
{

    private static $db = array(
        'Title'       => 'Varchar(255)',
        'Link'        => 'Varchar(255)',
        'Description' => 'Text',
        'Slug'        => 'Varchar(255)'
    );

    private static $has_one = array(
        'Company' => 'Company',
        'Image'   => 'Image'
    );

	static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

	static $many_many = array(
		'Authors' => 'BookAuthor',
	);

    protected function onBeforeWrite() {
        //generate slug...
        $this->Slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->Title)));
        parent::onBeforeWrite();
    }

	/**
     * @return int
     */
	public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function setCompany(ICompany $company)
    {
        AssociationFactory::getInstance()->getMany2OneAssociation($this,'Company')->setTarget($company);
    }

    public function setAuthors($authors)
    {
        AssociationFactory::getInstance()->getMany2ManyAssociation($this,'Authors')->addMany($authors);
    }

    public function getCompany()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Company')->getTarget();
    }

}