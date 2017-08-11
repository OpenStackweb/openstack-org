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
 * Class BookFactory
 */
final class BookFactory
	implements IBookFactory {

	/**
	 * @param string           $title
	 * @param string           $link
	 * @param string           $description
	 * @param ICompany         $company
	 * @param IBookAuthor[]    $authors
	 * @return IBook
	 */
	public function buildBook($title, $link, $description, ICompany $company, $authors)
	{
		$book = new Book;
        $book->Title = $title;
        $book->Link = $link;
        $book->Description = $description;
        $book->setCompany($company);
        if ($authors) {
            $book->setAuthors($authors);
        }

     	return $book;
	}

	/**
	 * @param string $first_name
	 * @param string $last_name
	 * @return IBookAuthor
	 */
	public function buildAuthor($first_name, $last_name)
	{
		$author     = new BookAuthor();
        $author->FirstName = $first_name;
        $author->LastName = $last_name;

		return $author;
	}

}