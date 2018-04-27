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
interface ITagRepository extends IEntityRepository
{
    /**
     * @param string $search
     * @return ITag[]
     */
    public function getByTag($search);

    /**
     * @param int $summit_id
     * @param int $page
     * @param int $page_size
     * @param string $sort
     * @param string $sort_dir
     * @param string $id
     * @return ISummitEventFeedback[]
     */
    public function searchAllPaged($summit_id,$page,$page_size,$sort,$sort_dir,$search_term);
}