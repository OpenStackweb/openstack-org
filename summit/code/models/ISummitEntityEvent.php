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
interface ISummitEntityEvent
{
    const UpdatedEntity        = 'updated_summit_entity';
    const InsertedEntity       = 'inserted_summit_entity';
    const DeletedEntity        = 'deleted_summit_entity';
    const AddedToSchedule      = 'added_to_schedule';
    const RemovedFromSchedule    = 'removed_from_schedule';
    const AddedToFavorites     = 'added_to_favorites';
    const RemovedFromFavorites = 'removed_from_favorites';
}