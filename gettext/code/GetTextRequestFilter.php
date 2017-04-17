<?php

/**
 * Copyright 2016 OpenStack Foundation
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
class GetTextRequestFilter implements RequestFilter {

    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model) {
        // Ensures routes etc are setup
        // We need to inject the presented session temporarily, as there is no current controller set
        GetTextSession::with_session($session, function() {
            GetText::init();
        });
    }

    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model) {
    }

}