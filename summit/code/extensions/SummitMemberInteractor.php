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
class SummitMemberInteractor extends DataExtension
{
    private static $many_many = array
    (
        'FavoriteSummitEvents'  => 'SummitEvent',
    );

    /**
     * @param int $event_id
     * @return bool
     */
    public function isOnFavorites($event_id){
        $event = SummitEvent::get()->byID($event_id);
        if(is_null($event)) return false;

        $query = new QueryObject($this->owner);
        $query->addAndCondition(QueryCriteria::equal('SummitEvent.ID',$event_id));
        $events = AssociationFactory::getInstance()->getMany2ManyAssociation($this->owner, 'FavoriteSummitEvents', $query)->toArray();

        return (count($events) > 0);
    }

    /**
     * @param SummitEvent $event
     */
    public function addToFavorites(SummitEvent $event){
        AssociationFactory::getInstance()->getMany2ManyAssociation($this->owner, 'FavoriteSummitEvents')->add
        (
            $event,
            []
        );

        PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::AddedToFavorites,
            [$this->owner->ID, $event]);
    }

    /**
     * @param SummitEvent $event
     */
    public function removeFromFavorites(SummitEvent $event){
        AssociationFactory::getInstance()->getMany2ManyAssociation($this->owner, 'FavoriteSummitEvents')->remove($event);

        PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::RemovedFromFavorites,
        [$this->owner->ID, $event]);
    }

    /**
     * @param int $summit_id
     * @return int[]
     */
    public function getFavoritesEventIds($summit_id){
        $res   = [];
        $query = DB::query("SELECT SummitEventID FROM Member_FavoriteSummitEvents 
INNER JOIN SummitEvent ON SummitEvent.ID = Member_FavoriteSummitEvents.SummitEventID
WHERE SummitEvent.SummitID = {$summit_id} AND MemberID = ".$this->owner->ID);
        foreach ($query as $record){
            $res[] = intval($record['SummitEventID']);
        }
        return $res;
    }
}