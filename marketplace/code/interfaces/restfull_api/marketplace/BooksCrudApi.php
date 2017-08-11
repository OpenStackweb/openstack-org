<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class BooksCrudApi
 */
final class BooksCrudApi extends AbstractRestfulJsonApi
{

    const ApiPrefix = 'api/v1/marketplace/books';

    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        //check permissions
        if(!$this->current_user->isMarketPlaceAdmin()) {
            return false;
        }
        return true;
    }

    /**
     * @var array
     */
    static $url_handlers = array(
        'GET $BOOK_ID!'         => 'getBook',
        'DELETE $BOOK_ID!'      => 'deleteBook',
        'POST $BOOK_ID!/attach' => 'attachImage',
        'POST '                 => 'addBook',
        'PUT '                  => 'updateBook',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'getBook',
        'deleteBook',
        'addBook',
        'updateBook',
        'attachImage',
    );

    /**
     * @var BookManager
     */
    protected $manager;

    /**
     * @var IBookFactory
     */
    protected $factory;

    /**
     * @var IMarketplaceTypeRepository
     */
    protected $marketplace_type_repository;

    public function __construct(BookManager $manager, IBookFactory $factory, IMarketplaceTypeRepository $marketplace_type_repository)
    {
        $this->manager = $manager;
        $this->factory = $factory;
        $this->marketplace_type_repository = $marketplace_type_repository;

        parent::__construct();

    }


    public function getBook()
    {
        $book_id = intval($this->request->param('BOOK_ID'));
        $book = Book::get()->byID($book_id);
        if (!$book)
            return $this->notFound();
        return $this->ok($book->toArray());
    }

    public function addBook()
    {
        try {
            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();
            return $this->created($this->manager->addBook($data)->getIdentifier());
        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->validationError($ex1->getMessages());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function updateBook()
    {
        try {
            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();
            $book_id = $this->manager->updateBook($data)->getIdentifier();
            return $this->ok($book_id);
        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2,SS_Log::ERR);
            return $this->notFound($ex2->getMessage());
        } catch (EntityAlreadyExistsException $ex3) {
            SS_Log::log($ex3,SS_Log::ERR);
            return $this->validationError($ex3->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            die('e: '.print_r($ex));
            return $this->serverError();
        }
    }

    public function attachImage(SS_HTTPRequest $request)
    {
        try
        {
            $book_id     = intval($request->param('BOOK_ID'));

            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_OK:
                    $image = $this->manager->uploadImage($book_id, $_FILES['file']);
                    return $this->ok($image->ID);
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('No file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Exceeded filesize limit.');
                default:
                    throw new RuntimeException('Unknown errors.');
            }
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function deleteBook()
    {
        try {
            $book_id = intval($this->request->param('BOOK_ID'));

            $this->manager->deleteBook($book_id);

            return $this->deleted();
        } catch (NotFoundEntityException $ex1) {
            SS_Log::log($ex1, SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

}