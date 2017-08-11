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
 * Class BookAssembler
 */
final class BookAssembler
{

    /**
     * @param IBook $book
     * @return array
     */
    public static function convertBookToArray(IBook $book)
    {
        $res['id'] = $book->getIdentifier();
        $res['title'] = $book->Title;
        $res['link'] = $book->Link;
        $res['description'] = $book->Description;
        $res['image_url'] = $book->Image()->getURL();
        $company = $book->getCompany();
        if($company)
            $res['company_id'] = $company->getIdentifier();

        $res['authors'] = array();

        foreach ($book->Authors() as $author) {
            $res['authors'][] = ['id' => $author->ID, 'first_name' => $author->FirstName, 'last_name' => $author->LastName];
        }

        return $res;
    }

} 