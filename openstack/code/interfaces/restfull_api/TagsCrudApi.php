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
        'PUT $TAG_ID!'      => 'updateTag',
        'POST '             => 'addTag',
        'DELETE $TAG_ID!'   => 'deleteTag',
    );

    /**
     * @var array
     */
    static $allowed_actions = array(
        'getTags',
        'updateTag',
        'addTag',
        'deleteTag',
    );


    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function getTags(SS_HTTPRequest $request){
        $query_string = $request->getVars();
        $search       = (isset($query_string['search'])) ? Convert::raw2sql($query_string['search']) : '';

        try{
            $tags = Tag::get()->where("Tag.Tag != ''");
            if (!empty($search)) {
                $tags = $tags->filter(['Tag:PartialMatch' => $search]);
            }
            $tags = $tags->sort('Tag','ASC');

            $data = [];
            foreach ($tags as $tag) {

                $data[] = [
                    'id'   => $tag->ID,
                    'tag' => $tag->Tag
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
        $tag_id       = $request->param('TAG_ID');

        try{

            $tag = Tag::get()->byID($tag_id);
            if (!$tag)
                throw new NotFoundEntityException('Tag');

            if (empty($tag_val))
                throw new ValidationException();

            $tag->Tag = $tag_val;
            $tag->write();


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

            if (empty($tag_val))
                throw new ValidationException();

            $tag = new Tag();
            $tag->Tag = $tag_val;
            $tag->write();

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
    public function deleteTag(SS_HTTPRequest $request){
        $tag_id       = $request->param('TAG_ID');

        try{

            $tag = Tag::get()->byID($tag_id);
            if (!$tag)
                throw new NotFoundEntityException('Tag');

            $tag->delete();

            return $this->getTags($request);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }
}