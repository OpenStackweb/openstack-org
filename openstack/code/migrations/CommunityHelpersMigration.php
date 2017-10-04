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
final class CommunityHelpersMigration extends AbstractDBMigrationTask
{
    protected $title = "Add Members as helpers in new community page";

    protected $description = "Community page has helpers set from cms, this migration populates all defaults";

    function doUp()
    {
        $managers = array(369,39506,35859,4840,14820);
        $ambassadors = array(8604,8889,7040,163,2755,175,139,11306,10422,8772,5882,9202);
        $community_page = CommunityPageBis::get()->first();

        $order = 1;
        foreach($managers as $manager) {
            $member = Member::get()->byID($manager);
            if ($member && !$community_page->CommunityManagers()->find('ID',$manager))
                $community_page->CommunityManagers()->add($member, ['Order' => $order]);
            $order++;
        }

        $order = 1;
        foreach($ambassadors as $ambassador) {
            $member = Member::get()->byID($ambassador);
            if ($member && !$community_page->Ambassadors()->find('ID',$ambassador))
                $community_page->Ambassadors()->add($member, ['Order' => $order]);
            $order++;
        }
    }

    function doDown()
    {

    }
}
