<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class GridFieldImporter_Request
 */
abstract class GridFieldImporter_Request extends RequestHandler
{

    /**
     * Gridfield instance
     * @var GridField
     */
    protected $gridField;

    /**
     * The parent GridFieldImporter
     * @var GridFieldImporter
     */
    protected $component;

    /**
     * URLSegment for this request handler
     * @var string
     */
    protected $urlSegment = 'importer';

    /**
     * Parent handler to link up to
     * @var RequestHandler
     */
    protected $requestHandler;

    /**
     * RequestHandler allowed actions
     * @var array
     */
    private static $allowed_actions = array(
        'preview', 'upload', 'import'
    );

    /**
     * RequestHandler url => action map
     * @var array
     */
    private static $url_handlers = [
        'upload!'         => 'upload',
        '$Action/$FileID' => '$Action'
    ];

    /**
     * Handler's constructor
     *
     * @param GridField $gridField
     * @param GridField_URLHandler $component
     * @param RequestHandler $handler
     */
    public function __construct($gridField, $component, $handler)
    {
        $this->gridField = $gridField;
        $this->component = $component;
        $this->requestHandler = $handler;
        parent::__construct();
    }

    /**
     * Return the original component's UploadField
     *
     * @return UploadField UploadField instance as defined in the component
     */
    public function getUploadField()
    {
        return $this->component->getUploadField($this->gridField);
    }

    /**
     * Upload the given file, and import or start preview.
     * @param  SS_HTTPRequest $request
     * @return string
     */
    public function upload(SS_HTTPRequest $request)
    {
        $body = [];
        try {

            $field = $this->getUploadField();
            $uploadResponse = $field->upload($request);
            //decode response body. ugly hack ;o
            $body = Convert::json2array($uploadResponse->getBody());
            $body = array_shift($body);
            $this->import($body['url']);
            //don't return buttons at all
            unset($body['buttons']);
        }
        catch (Exception $ex){
            $body['error'] = $ex->getMessage();
        }

        //re-encode
        $response = new SS_HTTPResponse(Convert::raw2json(array($body)));

        return $response;
    }

    abstract public function import($file_path);

}