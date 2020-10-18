<?php

/**
 * Copyright 2020 Open Infrastructure Foundation
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
final class BookManager implements IBookManager
{

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IValidatorFactory
     */
    protected $validator_factory;


    /**
     * @var IMarketplaceFactory
     */
    protected $marketplace_factory;

    /**
     * @var IMarketplaceTypeRepository
     */
    protected $marketplace_type_repository;

    /**
     * @var IBookFactory
     */
    protected $factory;

    /**
     * SoftwareManager constructor.
     * @param IEntitySerializer $serializer
     * @param ITransactionManager $tx_manager
     */
    public function __construct(
        IMarketplaceTypeRepository $marketplace_type_repository,
        IBookFactory $factory,
        IMarketplaceFactory $marketplace_factory,
        IValidatorFactory $validator_factory,
        ITransactionManager $tx_manager)
    {
        $this->marketplace_type_repository = $marketplace_type_repository;
        $this->marketplace_factory = $marketplace_factory;
        $this->factory = $factory;
        $this->validator_factory = $validator_factory;
        $this->tx_manager = $tx_manager;
    }

    public function buildBook($data, $company)
    {
        $authors = [];

        $title = (isset($data['title'])) ? Convert::raw2sql($data['title']) : '';
        $link = (isset($data['link'])) ? Convert::raw2sql($data['link']) : '';
        $description = (isset($data['description'])) ? Convert::raw2sql($data['description']) : '';

        if (array_key_exists('authors', $data) && is_array($data['authors'])) {
            $reference_author_data = $data['authors'];
            foreach ($reference_author_data as $author_data) {
                $first_name = $author_data['first_name'];
                $last_name = $author_data['last_name'];
                if (!($first_name || $last_name)) continue;

                $author_do = BookAuthor::get()->filter(['FirstName' => $first_name, 'LastName' => $last_name])->first();
                if (!$author_do) {
                    $author_do = $this->factory->buildAuthor($first_name, $last_name);
                    $author_do->write();
                }

                $authors[] = $author_do;
            }
        }

        $book = $this->factory->buildBook(
            $title,
            $link,
            $description,
            $company,
            $authors
        );

        return $book;
    }

    protected function getMarketPlaceType()
    {
        $marketplace_type =  $this->marketplace_type_repository->getByType(IBook::MarketPlaceType);
        if(!$marketplace_type)
            throw new NotFoundEntityException('MarketPlaceType',sprintf("type %s ",IBook::MarketPlaceType));

        return $marketplace_type;
    }

    /**
     * @param Array $data
     * @return IBook
     */
    public function addBook($data){
        $book_factory = $this->factory;
        $validator_factory = $this->validator_factory;
        $marketplace_factory = $this->marketplace_factory;

        return $this->tx_manager->transaction(function () use (
            $book_factory,
            $data,
            $validator_factory,
            $marketplace_factory
        ) {

            $validator = $validator_factory->buildValidatorForBook($data);

            if ($validator->fails()) {
                throw new EntityValidationException($validator->messages());
            }

            $company = $marketplace_factory->buildCompanyById(intval($data['company_id']));
            $book = $this->buildBook($data, $company);
            $book->write();

            return $book;
        });
    }

    /**
     * @param Array $data
     * @return IBook
     */
    public function updateBook($data){
        $validator_factory = $this->validator_factory;
        $marketplace_factory = $this->marketplace_factory;

        return $this->tx_manager->transaction(function () use (
            $marketplace_factory,
            $data,
            $validator_factory
        ) {
            $validator = $validator_factory->buildValidatorForBook($data);
            if ($validator->fails()) {
                throw new EntityValidationException($validator->messages());
            }

            $id = intval($data['id']);
            $book = Book::get()->byId($id);
            if (!$book) {
                throw new NotFoundEntityException('Book', sprintf("id %s", $id));
            }

            $book->Title = Convert::raw2sql($data['title']);
            $book->Link = Convert::raw2sql($data['link']);
            $book->Description = Convert::raw2sql($data['description']);
            $book->CompanyID = $data['company_id'];

            $res = Book::get()->filter(['Title' => $book->Title, 'CompanyID' => $book->CompanyID, 'ID:not' => $book->ID])->first();

            if ($res) {
                throw new EntityAlreadyExistsException('Book',
                    sprintf('title %s', $book->Title));
            }

            $authors = [];

            if (array_key_exists('authors', $data) && is_array($data['authors'])) {
                $reference_author_data = $data['authors'];
                foreach ($reference_author_data as $author_data) {
                    $first_name = $author_data['first_name'];
                    $last_name = $author_data['last_name'];
                    if (!($first_name || $last_name)) continue;

                    $author_do = BookAuthor::get()->filter(['FirstName' => $first_name, 'LastName' => $last_name])->first();
                    if (!$author_do) {
                        $author_do = $this->factory->buildAuthor($first_name, $last_name);
                        $author_do->write();
                    }

                    $authors[] = $author_do;
                }
            }

            $book->Authors()->removeAll();
            $book->setAuthors($authors);

            $book->write();

            return $book;
        });

    }

    /**
     * @param $book_id
     * @param $tmp_file
     * @param int $max_file_size
     * @return File
     */
    public function uploadImage($book_id, $tmp_file, $max_file_size=10*1024*1024) {

        return $this->tx_manager->transaction(function () use ($book_id, $tmp_file, $max_file_size) {

            $book_id = intval($book_id);
            $book    = Book::get()->byId($book_id);

            if(is_null($book))
                throw new NotFoundEntityException('Book');

            $image      = new Image();
            $upload     = new Upload();
            $validator  = new Upload_Validator();

            $validator->setAllowedExtensions(['png','jpg','jpeg','gif','pdf']);
            $validator->setAllowedMaxFileSize($max_file_size);
            $upload->setValidator($validator);

            if (!$upload->loadIntoFile($tmp_file, $image)) {
                throw new EntityValidationException($upload->getErrors());
            }

            $new_file_id = $image->write();

            $book->ImageID = $new_file_id;
            $book->write();

            return $image;

        });
    }

    /**
     * @param int $book_id
     * @return bool
     */
    public function deleteBook($book_id){
        $this->tx_manager->transaction(function () use ($book_id) {
            $book = Book::get()->byId($book_id);
            if (!$book) {
                throw new NotFoundEntityException('Book', sprintf("id %s", $book_id));
            }

            $book->delete();
        });
    }
}