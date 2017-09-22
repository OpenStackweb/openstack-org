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
final class UserStoriesResfullApi extends AbstractRestfulJsonApi
{
    /**
     * @var IUserStoryRepository
     */
    private $repository;

    /**
     * @var IUserStoryManager
     */
    private $manager;

    private $story_limit = 20;

    /**
     * UserStoryResfullApi constructor.
     * @param IUserStoryRepository $repository
     * @param IUserStoryManager $manager
     */
    public function __construct
    (
        IUserStoryRepository $repository,
        IUserStoryManager $manager
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->manager    = $manager;
    }

    const ApiPrefix = 'api/v1/user-stories';

    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        return true;
    }

    /**
     * @return bool
     */
    protected function authenticate(){
        return true;
    }

    static $url_handlers = array(
        'GET '    => 'getAllUserStories',
    );

    static $allowed_actions = [
       'getAllUserStories',
    ];

    public function getAllUserStories(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $start   = intval((isset($query_string['start'])) ? Convert::raw2sql($query_string['start']) : 0);
            $view    = intval((isset($query_string['view'])) ? Convert::raw2sql($query_string['view']) : 'date');
            $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
            $tag  = (isset($query_string['tag'])) ? Convert::raw2sql($query_string['tag']) : '';

            if ($search_term) {
                $list = $this->repository->findAllActive($search_term);
            } else if ($tag) {
                $list = $this->repository->findAllActiveByTag($tag);
            }  else {
                $list = $this->repository->getAllActive();
            }

            $total = $list->count();
            $has_more = false;
            //$has_more = ($start + $this->story_limit) < $total;
            //$list = $list->limit($this->story_limit, $start);

            $items = [];

            foreach ($list as $item){
                $items[] =
                [
                    'id'            => intval($item->ID),
                    'name'          => trim($item->Name),
                    'description'   => trim($item->Description),
                    'short_desc'    => trim($item->ShortDescription),
                    'date'          => $item->LastEdited,
                    'industry'      => $item->Industry()->IndustryName,
                    'link'          => $item->Link,
                    'organization'  => $item->Organization()->Name,
                    'image'         => $item->Image()->getURL(),
                    'location'      => $item->Location()->Name,
                    'tags'          => array_keys($item->Tags()->map('Tag')->toArray()),
                ];

            }

            return $this->ok(array('stories' => $items, 'total' => $total, 'has_more' => $has_more));
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

}