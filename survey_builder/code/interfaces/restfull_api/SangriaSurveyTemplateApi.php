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
use  Symfony\Component\Process\Process;
/**
 * Class SangriaSurveyTemplateApi
 */
final class SangriaSurveyTemplateApi extends AbstractRestfulJsonApi
{

    const NO_BAYESIAN_MODEL_FILE_ERROR_CODE = 253;

    const NO_BAYESIAN_MODEL_FILE_ERROR_TEXT = <<<TEXT
There is not any Bayesian Model for this question type so far.\n
Please complete by hand some training data and perform a model rebuild.
TEXT;

    /**
     * @var ISurveyFreeTextAnswerManager
     */
    private $manager;

    /**
     * @var ISurveyTemplateManager
     */
    private $template_manager;

    /**
     * @var array
     */
    static $url_handlers = [
        'PUT free-text-answers/tagging/bayes/rebuild'                                            => 'rebuildBayesianNaiveModel',
        'PUT $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/free-text-answers/tagging/kmeans'        => 'extractTagsByKMeans',
        'PUT $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/free-text-answers/tagging/bayes'         => 'extractTagsByBayes',
        'PUT $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/free-text-answers/tagging/rake'          => 'extractTagsByRake',
        'GET $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/free-text-answers/all/tags'              => 'getAllFreeTextAnswersTags',
        'POST $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/free-text-answers/$ANSWER_ID/tags'      => 'addFreeTextAnswerTag',
        'DELETE $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/free-text-answers/$ANSWER_ID/tags'    => 'removeFreeTextAnswerTag',
        'POST $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/free-text-answers/merge_tags'           => 'addFreeTextTagMerge',
        'PUT $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/free-text-answers/$ANSWER_ID'            => 'updateFreeTextAnswer',
        'GET $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/free-text-answers'                       => 'getFreeTextAnswers',
        'GET $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/languages'                               => 'getLanguages',
        'GET $SURVEY_TEMPLATE_ID/questions/$QUESTION_ID/export-answers'                          => 'exportAnswers',
        'GET $SURVEY_TEMPLATE_ID/questions'                                                      => 'getFreeTextQuestions',
    ];

    /**
     * @var string[]
     */
    static $allowed_actions = [
        'getFreeTextQuestions',
        'getFreeTextAnswers',
        'addFreeTextAnswerTag',
        'removeFreeTextAnswerTag',
        'extractTagsByKMeans',
        'extractTagsByBayes',
        'rebuildBayesianNaiveModel',
        'extractTagsByRake',
        'updateFreeTextAnswer',
        'getAllFreeTextAnswersTags',
        'addFreeTextTagMerge',
        'getLanguages',
        'exportAnswers',
    ];

    public function __construct(ISurveyFreeTextAnswerManager $manager,  ISurveyTemplateManager $template_manager)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->template_manager = $template_manager;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        //check permissions
        if(!Permission::check("FREE_TEXT_TAGGING_ACCESS")) {
            return false;
        }

        return true;
    }

    public function getFreeTextAnswers(SS_HTTPRequest $request){

        $query_string = $request->getVars();
        $question_id  = intval($request->param('QUESTION_ID'));
        $page         = intval((isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : 0);
        $page_size    = intval((isset($query_string['page_size'])) ? Convert::raw2sql($query_string['page_size']) : 25);
        $search_term  = isset($query_string['search_term']) ? trim($query_string['search_term']): '';
        $languages    = isset($query_string['languages']) ? explode(',',$query_string['languages']): '';
        // zero mean showing all ...
        if($page_size == 0) $page_size = PHP_INT_MAX;

        try {
            list($list, $count) = $this->manager->getFreeTextAnswerByQuestion($question_id, $page, $page_size, $search_term, $languages);
            $items = [];

            // serialization

            foreach ($list as $answer) {
                $tags = [];

                foreach ($answer->Tags() as $tag) {
                    $tags[] = [
                        'id' => intval($tag->ID),
                        'value' => trim($tag->Value),
                        'type' => trim($tag->Type)
                    ];
                }

                $items[] = [
                    'id' => intval($answer->ID),
                    'member_email' => trim($answer->Step()->Survey()->CreatedBy()->Email),
                    'value' => trim($answer->Value),
                    'tags' => $tags,
                    'survey_id' => intval($answer->SurveyID)
                ];
            }

            return $this->ok(['items' => $items, 'count' => $count]);
        }
        catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function exportAnswers(SS_HTTPRequest $request){

        $query_string = $request->getVars();
        $question_id  = intval($request->param('QUESTION_ID'));
        $search_term  = isset($query_string['search_term']) ? trim($query_string['search_term']): '';
        $languages    = isset($query_string['languages']) ? explode(',',$query_string['languages']): '';
        $question     = SurveyQuestionTemplate::get()->byID($question_id);

        try {
            list($list, $count) = $this->manager->getFreeTextAnswerByQuestion($question_id, 1, PHP_INT_MAX, $search_term, $languages);
            $items = [];

            // serialization
            foreach ($list as $answer) {
                $tags = $answer->Tags()->column('Value');

                $items[] = [
                    'id' => intval($answer->ID),
                    'member_email' => trim($answer->Step()->Survey()->CreatedBy()->Email),
                    'value' => trim($answer->Value),
                    'tags' => implode(', ',$tags)
                ];
            }

            $fields = [];
            foreach($items as $item) {
                $fields[] = [
                    'ID' => $item['id'],
                    'Answer' => $item['value'],
                    'Tags' => $item['tags']
                ];
            }

            $filename = "FreeTextAnswers-" . $question->Label . ".csv";

            return CSVExporter::getInstance()->export($filename, $fields, ',');

        }
        catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getFreeTextQuestions(SS_HTTPRequest $request){

        $template_id = intval($request->param('SURVEY_TEMPLATE_ID'));

        try {

            $questions = $this->template_manager->getAllFreeTextQuestionByTemplate($template_id, true);
            // serialization

            $list = [];

            foreach ($questions as $question) {
                $list[] = [
                    'id' => intval($question->ID),
                    'name' => $question->Name,
                    'step_name' => $question->Step()->Name,
                    'step_id' => intval($question->Step()->ID),
                    'survey_template_name' => $question->Step()->SurveyTemplate()->Title,
                    'survey_template_id' => intval($question->Step()->SurveyTemplate()->ID),
                ];
            }

            return $this->ok(['items' => $list, 'count' => count($list)]);
        }
        catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getAllFreeTextAnswersTags(SS_HTTPRequest $request){
        $template_id = intval($request->param('SURVEY_TEMPLATE_ID'));
        $question_id = intval($request->param('QUESTION_ID'));

        try{

            $tags  = $this->manager->getAllFreeTextTagsByQuestion($question_id);
            $items = [];
            foreach ($tags as $tag){
                $items[] = [
                    'id'    => intval($tag->ID),
                    'value' => trim($tag->Value),
                    'type'  => trim($tag->Type)
                ];
            }
            return $this->ok(['items' => $items, 'count' => count($items)]);
        }
        catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function removeFreeTextAnswerTag(SS_HTTPRequest $request){

        $template_id = intval($request->param('SURVEY_TEMPLATE_ID'));
        $question_id = intval($request->param('QUESTION_ID'));
        $answer_id   = intval($request->param('ANSWER_ID'));

        if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
        $data        = $this->getJsonRequest();

        try{
            if(!isset($data['tag']))
                throw new EntityValidationException('tag is mandatory!');

            $this->manager->deleteTagToFreeTextAnswers($template_id, $question_id, $answer_id, $data['tag']);
            return $this->deleted();
        }
        catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function addFreeTextAnswerTag(SS_HTTPRequest $request){

        $template_id = intval($request->param('SURVEY_TEMPLATE_ID'));
        $question_id = intval($request->param('QUESTION_ID'));
        $answer_id   = intval($request->param('ANSWER_ID'));

        if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
        $data        = $this->getJsonRequest();

        try{
            if(!isset($data['tag']))
                throw new EntityValidationException('tag is mandatory!');
            $this->manager->addTagToFreeTextAnswers($template_id, $question_id, $answer_id, $data['tag']);
            return $this->updated();
        }
        catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function updateFreeTextAnswer(SS_HTTPRequest $request){

        $template_id = intval($request->param('SURVEY_TEMPLATE_ID'));
        $question_id = intval($request->param('QUESTION_ID'));
        $answer_id   = intval($request->param('ANSWER_ID'));

        if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
        $data        = $this->getJsonRequest();

        try {
            $this->manager->updateFreeTextAnswer($template_id, $question_id, $answer_id, $data);
            return $this->updated();
        }
        catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     *  Automatic extraction methods
     */

    public function extractTagsByKMeans(SS_HTTPRequest $request){

        $template_id         = intval($request->param('SURVEY_TEMPLATE_ID'));
        $question_id         = intval($request->param('QUESTION_ID'));

        $query_string        = $request->getVars();
        $clusters_qty        = isset($query_string['clusters']) ? intval($query_string['clusters']) : 5;
        $max_tags            = isset($query_string['max_tags']) ? intval($query_string['max_tags']) : 5;
        $delete_former_tags  = isset($query_string['delete_former_tags']) ? intval($query_string['delete_former_tags']) : 1;

        $command = sprintf( '%1$s/survey_builder/code/model/extract_tags/extract_tags_by_kmeans.sh "%1$s/survey_builder/code/model/extract_tags" "%1$s" %2$s %3$s %4$s %5$s', Director::baseFolder(), $question_id, $max_tags, $delete_former_tags, $clusters_qty);
        $process = new Process($command);
        $process->setWorkingDirectory(sprintf('%s/survey_builder/code/model/extract_tags', Director::baseFolder()));
        $process->setTimeout(PHP_INT_MAX);
        $process->setIdleTimeout(PHP_INT_MAX);
        $process->run();

        while ($process->isRunning()) {
        }

        $output = $process->getOutput();

        if (!$process->isSuccessful()) {
            return $this->serverError();
        }

        return $this->ok();
    }

    public function extractTagsByBayes(SS_HTTPRequest $request){
        $template_id         = intval($request->param('SURVEY_TEMPLATE_ID'));
        $question_id         = intval($request->param('QUESTION_ID'));
        $query_string        = $request->getVars();
        $delete_former_tags  = isset($query_string['delete_former_tags']) ? intval($query_string['delete_former_tags']) : 1;
        $model_folder = Director::baseFolder().'/survey_builder/code/model/extract_tags/bayesian_models';
        $command = sprintf( '%1$s/survey_builder/code/model/extract_tags/bayesian_tag_extraction.sh "%1$s/survey_builder/code/model/extract_tags" "%1$s" %2$s %3$s %4$s', Director::baseFolder(), $model_folder, $question_id, $delete_former_tags);
        $process = new Process($command);
        $process->setWorkingDirectory(sprintf('%s/survey_builder/code/model/extract_tags', Director::baseFolder()));
        $process->setTimeout(PHP_INT_MAX);
        $process->setIdleTimeout(PHP_INT_MAX);
        $process->run();

        while ($process->isRunning()) {
        }

        $output = $process->getOutput();

        if (!$process->isSuccessful()) {
            if($process->getExitCode() == self::NO_BAYESIAN_MODEL_FILE_ERROR_CODE){
                return $this->validationError(self::NO_BAYESIAN_MODEL_FILE_ERROR_TEXT);
            }
            return $this->serverError();
        }

        return $this->ok();
    }

    public function rebuildBayesianNaiveModel(SS_HTTPRequest $request){
        $model_folder = Director::baseFolder().'/survey_builder/code/model/extract_tags/bayesian_models';
        $command = sprintf( '%1$s/survey_builder/code/model/extract_tags/bayesian_model_builder.sh "%1$s/survey_builder/code/model/extract_tags" "%1$s" "%2$s"', Director::baseFolder(), $model_folder);
        $process = new Process($command);
        $process->setWorkingDirectory(sprintf('%s/survey_builder/code/model/extract_tags', Director::baseFolder()));
        $process->setTimeout(PHP_INT_MAX);
        $process->setIdleTimeout(PHP_INT_MAX);
        $process->run();

        while ($process->isRunning()) {
        }

        $output = $process->getOutput();

        if (!$process->isSuccessful()) {
            return $this->serverError();
        }

        return $this->ok();
    }

    public function extractTagsByRake(SS_HTTPRequest $request){

        $template_id         = intval($request->param('SURVEY_TEMPLATE_ID'));
        $question_id         = intval($request->param('QUESTION_ID'));

        $query_string        = $request->getVars();
        $max_tags            = isset($query_string['max_tags']) ? intval($query_string['max_tags']) : 3;
        $delete_former_tags  = isset($query_string['delete_former_tags']) ? intval($query_string['delete_former_tags']) : 1;

        $command = sprintf( '%1$s/survey_builder/code/model/extract_tags/extract_tags_by_rake.sh "%s/survey_builder/code/model/extract_tags" "%1$s" %2$s %3$s %4$s', Director::baseFolder(),  $question_id, $max_tags, $delete_former_tags);
        $process = new Process($command);
        $process->setWorkingDirectory(sprintf('%s/survey_builder/code/model/extract_tags', Director::baseFolder()));
        $process->setTimeout(PHP_INT_MAX);
        $process->setIdleTimeout(PHP_INT_MAX);
        $process->run();

        while ($process->isRunning()) {
        }

        $output = $process->getOutput();

        if (!$process->isSuccessful()) {
            return $this->serverError();
        }

        return $this->ok();
    }

    public function addFreeTextTagMerge(SS_HTTPRequest $request){

        $template_id = intval($request->param('SURVEY_TEMPLATE_ID'));
        $question_id = intval($request->param('QUESTION_ID'));

        if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
        $data        = $this->getJsonRequest();

        try{
            if(!isset($data['tags']) || !isset($data['replace_tag']) || empty($data['tags']) || !$data['replace_tag'])
                throw new EntityValidationException('tags is mandatory!');

            $this->manager->mergeTagsInFreeTextQuestion($template_id, $question_id, $data['tags'], $data['replace_tag']);
            return $this->updated();
        }
        catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getLanguages(SS_HTTPRequest $request){

        $question_id  = intval($request->param('QUESTION_ID'));

        try {
            $languages = $this->manager->getLanguagesByQuestion($question_id);
            $items = ['All'];
            $items = array_merge($items, $languages);

            return $this->ok(['items' => $items]);
        }
        catch (EntityValidationException $ex1) {
            SS_Log::log($ex1, SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        } catch (NotFoundEntityException $ex2) {
            SS_Log::log($ex2, SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            return $this->serverError();
        }
    }
}