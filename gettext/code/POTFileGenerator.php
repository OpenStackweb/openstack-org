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
 * Class POTFileGenerator
 */
final class POTFileGenerator
{
    const POT_FILE_HEADER = <<<POT
#, fuzzy
msgid ""
msgstr ""
"MIME-Version: 1.0\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
POT;

    const POT_FILE_ENTRY_TPL = <<<POT_FILE_ENTRY_TPL
# %s
msgid "%s"
msgstr ""
POT_FILE_ENTRY_TPL;

    const POT_FILE_ENTRY_MULTI_TPL = <<<POT_FILE_ENTRY_TPL
# %s
msgid ""
%s
msgstr ""
POT_FILE_ENTRY_TPL;

    private $pot_file;
    private $pot_file_dic;
    private $pot_entries;

    public function __construct()
    {
        $this->pot_file     = self::POT_FILE_HEADER.PHP_EOL;
        $this->pot_file_dic = [];
        $this->pot_entries  = [];
    }

    /**
     * @param $msgid
     * @param $comment
     */
     public function addEntry($msgid, $comment){
        $msgid = trim($msgid);

        $_arr = preg_split("/[\r\n]+/",$msgid,-1,PREG_SPLIT_NO_EMPTY);

        $arr = [];
        foreach($_arr as $line){
            // trim these line
            array_push($arr,trim($line));
        }

        if(count($arr) == 1) {
            $msgid = $arr[0];
            $hash  = hash('md5', $msgid);
        }
        else{
            $msgid = $arr;
            $hash = '';
            foreach($arr as $line)
                $hash .= $line;
            $hash  = hash('md5', $hash);
        }
        if(!isset($this->pot_file_dic[$hash])){
            $this->pot_file_dic[$hash] = $comment;
            $this->pot_entries[]   = $msgid;
        }
        else{
            $this->pot_file_dic[$hash] = sprintf("%s, %s", $this->pot_file_dic[$hash], $comment);
        }
    }

    /**
     * @return string
     */
    public function generateFileContent(){
        $pot_file     = self::POT_FILE_HEADER.PHP_EOL;

        foreach ($this->pot_entries as $msgid){
            if(is_array($msgid)) {
                $multi = '';
                $hash = '';
                foreach($msgid as $line){
                    $hash .= $line;
                    $multi .= sprintf('"%s"'.PHP_EOL, $line);
                }

                $hash  = hash('md5', $hash);
                $pot_file .= sprintf(self::POT_FILE_ENTRY_MULTI_TPL, $this->pot_file_dic[$hash], rtrim($multi)).PHP_EOL;
            }
            else {
                $hash  = hash('md5', $msgid);
                $pot_file .= sprintf(self::POT_FILE_ENTRY_TPL, $this->pot_file_dic[$hash], $msgid).PHP_EOL;;
            }
        }

        return $pot_file;
    }
}