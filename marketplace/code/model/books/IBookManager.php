<?php

/**
 * Copyright 2017 OpenStack Foundation
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
interface IBookManager
{
    /**
     * @param Array $data
     * @return IBook
     */
    public function addBook($data);

    /**
     * @param Array $data
     * @return IBook
     */
    public function updateBook($data);

    /**
     * @param $book_id
     * @param $tmp_file
     * @param int $max_file_size
     * @return File
     */
    public function uploadImage($book_id, $tmp_file, $max_file_size);

    /**
     * @param int $book_id
     * @return bool
     */
    public function deleteBook($book_id);


}