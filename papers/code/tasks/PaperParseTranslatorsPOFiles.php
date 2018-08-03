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
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class PaperParseTranslatorsPOFiles extends BuildTask
{
    public function run($request)
    {
        try {
            echo "parsing po files looking for translators ...".PHP_EOL;
            $path = sprintf("%s/%s/_config/translations.yml", Director::baseFolder(), 'papers');
            echo "reading translation list from ".$path.' ...'.PHP_EOL;
            $yaml = Yaml::parse(file_get_contents($path));
            $files = [];
            $papers_id = [];
            if(!is_null($yaml) && count($yaml))
            {
                foreach($yaml as $project_id => $info){
                    $id = $info['id'];
                    $papers_id[] = $id;
                    $version_id = $info['version_id'];
                    $po_files = $info['po_files'];
                    foreach ($po_files as $po_file){
                        foreach ($po_file as $doc_id => $languages) {
                            foreach($languages as $language) {
                                $files[] = [
                                    'paper_id'    => intval($id),
                                    'doc_id'      => $doc_id,
                                    'lang_zanata' => $language['lang_zanata'],
                                    'lang_local'  => $language['lang_local'],
                                ];
                            }
                        }
                    }

                }
            }

            foreach($papers_id as $paper_id){
                $paper = Paper::get()->byID($paper_id);
                if(!$paper) continue;
                echo sprintf("deleting former translators from paper %s", $paper_id).PHP_EOL;
                $paper->Translators()->removeAll();
            }

            foreach($files as $file_info){

                $file_path = $path = sprintf("%s/%s/Locale/%s/LC_MESSAGES/%s.po",
                    Director::baseFolder(),
                    'papers',
                    $file_info['lang_local'],
                    $file_info['doc_id']
                    );

                if (!file_exists($file_path))
                    continue;

                $lines = file($file_path);

                foreach($lines as $line){
                    if(strstr($line,"msgid") !== false) break;

                    if(strstr($line,"#") !== false){
                        $matches = [];
                        if(preg_match('/# (.*)(\<.*\>)/', $line, $matches) !== 1) continue;
                        if(count($matches) < 3) continue;
                        $pieces = explode(" ", $line);
                        $name = $matches[1];
                        $email = $matches[2];
                        $translator = new PaperTranslator();
                        $translator->DisplayName = sprintf("%s, %s", trim($name), trim($email));
                        $translator->PaperID = $file_info['paper_id'];
                        $translator->LanguageCode = $file_info['lang_local'];
                        $translator->write();
                        echo sprintf("added translator %s to paper %s lang %s ",
                                $translator->DisplayName,
                                $translator->PaperID,
                                $translator->LanguageCode).PHP_EOL;
                    }
                }
            }

        }
        catch (ParseException $e) {
            echo printf("Unable to parse the YAML string: %s", $e->getMessage()).PHP_EOL;
        }
    }
}