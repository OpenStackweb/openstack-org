<?php

/**
 * Copyright 2015 OpenStack Foundation
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
class SummitAppMembersApi extends AbstractRestfulJsonApi {


    /**
     * @var IEntityRepository
     */
    private $summit_repository;

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    public function __construct()
    {
        parent::__construct();
        // TODO: set by IOC
        $this->summit_repository     = new SapphireSummitRepository;
        $this->member_repository     = new SapphireMemberRepository();
    }


    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        if(!Permission::check('ADMIN')) return false;
        return $this->checkOwnAjaxRequest();
    }

    protected function authenticate() {
        return true;
    }


    static $url_handlers = array(
        'GET options'      => 'getMemberSearchOptions',
    );

    static $allowed_actions = array(
        'getMemberSearchOptions',
    );

    // this is called when typing a member name to add as a tag
    public function getMemberSearchOptions(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $query        = Convert::raw2sql($query_string['query']);
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $members = DB::query("SELECT M.ID AS id, CONCAT(M.FirstName,' ',M.Surname,' (',M.ID,')') AS name FROM Member AS M
                                    LEFT JOIN Group_Members AS GM ON M.ID = GM.MemberID
                                    LEFT JOIN `Group` AS G ON G.ID = GM.GroupID
                                    WHERE (M.FirstName LIKE '{$query}%' OR M.Surname LIKE '{$query}%')
                                    AND(G.Code = '".IFoundationMember::CommunityMemberGroupSlug."' OR G.Code = '".IFoundationMember::FoundationMemberGroupSlug."')
                                    GROUP BY M.ID
                                    ORDER BY M.FirstName, M.Surname");

            $json_array = array();
            foreach ($members as $member) {

                $json_array[] = $member;
            }

            echo json_encode($json_array);
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