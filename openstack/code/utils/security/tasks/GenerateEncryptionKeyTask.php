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

/**
 * Class GenerateEncryptionKeyTask
 */
final class GenerateEncryptionKeyTask extends BuildTask
{

    protected $title = "GenerateEncryptionKeyTask";

    protected $description = "Generate a Random Encryption Key for Encrypter_Key Define";

    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     */
    public function run($request)
    {
        $cypher = $request->param('cipher');
        if(empty($cypher)){
            $cypher = 'AES-256-CBC';
        }

        $key =  'base64:'.base64_encode(random_bytes(
                $cypher == 'AES-128-CBC' ? 16 : 32
            ));

        echo sprintf('New Encrypter_Key "%s"',$key).PHP_EOL;
    }
}