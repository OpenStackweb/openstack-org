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
class TagsCrudApi extends AbstractRestfulJsonApi
{
    const ApiPrefix = 'api/v1/tags';

    /**
     * @return bool
     */
    public function checkOwnAjaxRequest()
    {
        $referer = @$_SERVER['HTTP_REFERER'];
        if (empty($referer)) {
            return false;
        }
        //validate
        if (!Director::is_ajax()) {
            return false;
        }
        return Director::is_site_url($referer);
    }


    /**
     * @return bool
     */
    protected function isApiCall()
    {
        $request = $this->getRequest();
        if (is_null($request)) {
            return false;
        }
        return strpos(strtolower($request->getURL()), self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function authenticate()
    {
        return Permission::check('SANGRIA_ACCESS');
    }

    /**
     * @var array
     */
    static $url_handlers = array(
        'GET '              => 'getTags',
        'PUT merge'         => 'mergeTags',
        'PUT $TAG_ID!'      => 'updateTag',
        'POST '             => 'addTag',
        'DELETE '           => 'deleteTags',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'getTags',
        'updateTag',
        'addTag',
        'deleteTags',
        'mergeTags',
    );

    /**
     * @var TagManager
     */
    protected $manager;

    /**
     * @var ITagRepository
     */
    protected $repository;

    public function __construct(ITagRepository $repository, TagManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        parent::__construct();

    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getTags(SS_HTTPRequest $request){
        $query_string = $request->getVars();
        $search       = (isset($query_string['search'])) ? Convert::raw2sql($query_string['search']) : '';

        try{
            $tags = $this->repository->getByTag($search);

            $data = [];
            foreach ($tags as $tag) {

                $data[] = [
                    'id'    => $tag['ID'],
                    'tag'   => $tag['Tag'],
                    'count' => $tag['ETCount'] + $tag['PCTCount'] + $tag['STCount'] + $tag['USTCount']
                ];
            }

            return $this->ok($data);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function updateTag(SS_HTTPRequest $request){
        $query_string = $request->getVars();
        $tag_val      = Convert::raw2sql($query_string['tag']);
        $is_split     = Convert::raw2sql($query_string['is_split']);
        $tag_id       = $request->param('TAG_ID');

        try{
            if ($is_split) {
                $this->manager->splitTag($tag_val, $tag_id);
            } else {
                $this->manager->updateTag($tag_val, $tag_id);
            }

            return $this->getTags($request);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function addTag(SS_HTTPRequest $request){
        $query_string = $request->getVars();
        $tag_val      = Convert::raw2sql($query_string['tag']);

        try{
            $this->manager->addTag($tag_val);
            return $this->getTags($request);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function deleteTags(SS_HTTPRequest $request){
        $vars      = $this->getJsonRequest();
        $tag_ids   = $vars['tag_ids'];

        try{
            $this->manager->deleteTags($tag_ids);
            return $this->getTags($request);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function mergeTags(SS_HTTPRequest $request){
        $vars               = $this->getJsonRequest();
        $merge_tag          = Convert::raw2sql($vars['merge_tag']);
        $selected_tag_ids   = $vars['selected_tags'];

        try{
            if (count($selected_tag_ids) < 2)
                throw new ValidationException();

            $tags = Tag::get()->byIDs($selected_tag_ids);

            $this->manager->mergeTags($merge_tag, $tags);
            return $this->getTags($request);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            echo $ex->getMessage();

            return $this->serverError();
        }
    }
}