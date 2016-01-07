<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class SapphireSummitPresentationRepository
 */
final class SapphireSummitPresentationRepository extends SapphireSummitEventRepository implements ISummitPresentationRepository
{

    public function __construct()
    {
        parent::__construct(new Presentation());
    }

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getUnpublishedBySummit($summit_id, $page = 1, $page_size = 10, $order = null)
    {
       if(is_null($order)) $order = array('SummitEvent.Created' => 'ASC');
       $list      = Presentation::get()->filter( array('SummitID' => $summit_id, 'Published' => 0))->where(" Title IS NOT NULL AND Title <>'' ")->sort($order);
       $count     = intval($list->count());
       $offset    = ($page - 1 ) * $page_size;
       $data      = $list->limit($page_size, $offset);
       return array($page, $page_size, $count, $data);
    }
    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param array $order
     * @return array
     */
    public function getUnpublishedBySummitAndTrackList($summit_id, $track_list = null,  $page = 1 ,$page_size = 10,  $order = null)
    {
        if(is_null($order)) $order = array('SummitEvent.Created' => 'ASC');
        $filter = array('SummitID' => $summit_id, 'Published' => 0);
        if(!empty($track_list)){
            $filter['SummitSelectedPresentationList.ID '] = $track_list;
        }

        $list = Presentation::get()->filter($filter)->where(" Title IS NOT NULL AND Title <>'' ")
            ->innerJoin('SummitSelectedPresentation', 'SummitSelectedPresentation.PresentationID = Presentation.ID')
            ->innerJoin('SummitSelectedPresentationList', "SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID AND (ListType = 'Group')")
            ->sort($order);

        $count     = intval($list->count());
        $offset    = ($page - 1 ) * $page_size;
        $data      = $list->limit($page_size, $offset);
        return array($page, $page_size, $count,  $data);
    }
}