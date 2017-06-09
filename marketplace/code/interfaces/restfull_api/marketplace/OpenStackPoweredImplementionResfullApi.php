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
final class OpenStackPoweredImplementionResfullApi extends AbstractRestfulJsonApi
{
    /**
     * @var IOpenStackPoweredServiceRepository
     */
    private $repository;

    /**
     * @var IPoweredOpenStackImplementationManager
     */
    private $manager;

    /**
     * OpenStackPoweredImplementionResfullApi constructor.
     * @param IOpenStackPoweredServiceRepository $repository
     * @param IPoweredOpenStackImplementationManager $manager
     */
    public function __construct
    (
        IOpenStackPoweredServiceRepository $repository,
        IPoweredOpenStackImplementationManager $manager
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->manager    = $manager;
    }

    const ApiPrefix = 'api/v1/marketplace';

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
        if(!Permission::check("SANGRIA_ACCESS"))
            return false;
        return true;
    }

    static $url_handlers = array(
        'GET '                                       => 'getOpenStackImplementations',
        'GET export/csv'                             => 'getOpenStackImplementationsExport',
        'PUT $SERVICE_ID!'                           => 'updateOpenStackImplementation',
        'POST $SERVICE_ID!/$LINK_TYPE!'              => 'addOpenStackImplementationLink',
        'DELETE $SERVICE_ID!/$LINK_TYPE!/$LINK_ID!'  => 'deleteOpenStackImplementationLink',
    );

    static $allowed_actions = [
       'getOpenStackImplementations',
       'updateOpenStackImplementation',
       'addOpenStackImplementationLink',
       'deleteOpenStackImplementationLink',
       'getOpenStackImplementationsExport'
    ];

    public function getOpenStackImplementations(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = intval((isset($query_string['page'])) ? Convert::raw2sql($query_string['page']) : 0);
            $page_size    = intval((isset($query_string['page_size'])) ? Convert::raw2sql($query_string['page_size']) : 25);

            // zero mean showing all ...
            if($page_size == 0) $page_size = PHP_INT_MAX;

            $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
            $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
            $service_type = (isset($query_string['type'])) ? Convert::raw2sql($query_string['type']) : '';
            $filters['expired'] = boolval((isset($query_string['show_expired'])) ? Convert::raw2sql($query_string['show_expired']) : false);
            $filters['powered'] = boolval((isset($query_string['show_powered'])) ? Convert::raw2sql($query_string['show_powered']) : false);

            list($list, $count) = $this->repository->getAllByPage(
                $page,
                $page_size,
                $order,
                $filters,
                $search_term,
                $service_type
            );

            $items = [];

            foreach ($list as $item){
                $items[] =
                [
                    'id'                   => intval($item->ID),
                    'name'                 => trim($item->Name),
                    'type'                 => trim($item->ClassName),
                    'company'              => trim($item->CompanyName),
                    'required_for_compute' => boolval($item->CompatibleWithCompute),
                    'required_for_storage' => boolval($item->CompatibleWithStorage),
                    'federated_identity'   => boolval($item->CompatibleWithFederatedIdentity),
                    'program_version_id'   => intval($item->ProgramVersionID),
                    'expiry_date'          => $item->ExpiryDate,
                    'edited_by'            => trim($item->LastEditedBy),
                ];
            }

            return $this->ok(array('items' => $items, 'count' => $count));
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

    public function getOpenStackImplementationsExport(SS_HTTPRequest $request){
        $data         = [];
        $filename     = "OpenStackImplementations" . date('Ymd') . ".csv";
        $query_string = $request->getVars();
        $order        = (isset($query_string['order'])) ? Convert::raw2sql($query_string['order']) : '';
        $search_term  = (isset($query_string['search_term'])) ? Convert::raw2sql($query_string['search_term']) : '';
        $service_type = (isset($query_string['type'])) ? Convert::raw2sql($query_string['type']) : '';
        $filters['expired'] = boolval((isset($query_string['show_expired'])) ? Convert::raw2sql($query_string['show_expired']) : false);
        $filters['powered'] = boolval((isset($query_string['show_powered'])) ? Convert::raw2sql($query_string['show_powered']) : false);


        list($list, $count) = $this->repository->getAllByPage
        (
            1,
            PHP_INT_MAX,
            $order,
            $filters,
            $search_term,
            $service_type
        );

        foreach ($list as $item){

            $service = OpenStackImplementation::get()->byID($item->ID);
            $data[] =
                [
                    'Id'                   => intval($item->ID),
                    'Name'                 => trim($item->Name),
                    'Type'                 => trim($item->ClassName),
                    'Company'              => trim($item->CompanyName),
                    'Required For Compute' => intval($item->CompatibleWithCompute),
                    'Required For Storage' => intval($item->CompatibleWithStorage),
                    'Federated Identity'   => intval($item->CompatibleWithFederatedIdentity),
                    'Program Version Name' => trim($item->ProgramVersionName),
                    'Expiry Date'          => $item->ExpiryDate,
                    'Edited By'            => trim($item->LastEditedBy),
                    'Administrators'       => $service->getPrintableAdminEmails(),
                    'ZenDesk Links'        => $service->getPrintableZenDeskLinks(),
                    'RefStack Links'       => $service->getPrintableRefStackLinks(),
                    'ReportedRelease'      => trim($service->ReportedRelease()->Name),
                    'PassedRelease'        => trim($service->PassedRelease()->Name),
                    'Notes'                => trim($service->Notes),
                ];
        }

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }

    public function updateOpenStackImplementation(SS_HTTPRequest $request){
        try
        {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $service_id  = intval($request->param('SERVICE_ID'));
            $data        = $this->getJsonRequest();
            $this->manager->updatePoweredProgram($data, $service_id);
            return $this->updated();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
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
    public function addOpenStackImplementationLink(SS_HTTPRequest $request){
        try
        {
            if(!$this->isJson()) return $this->validationError(array('invalid content type!'));
            $service_id  = intval($request->param('SERVICE_ID'));
            $link_type   = $request->param('LINK_TYPE');
            $data        = $this->getJsonRequest();

            return $this->created($this->manager->createImplementationLink($data, $link_type, $service_id));
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
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
    public function deleteOpenStackImplementationLink(SS_HTTPRequest $request){
        try
        {
            $service_id  = intval($request->param('SERVICE_ID'));
            $link_type   = $request->param('LINK_TYPE');
            $link_id     = intval($request->param('LINK_ID'));

            $this->manager->deleteImplementationLink($link_type, $link_id, $service_id);

            return $this->deleted();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessages());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }
}