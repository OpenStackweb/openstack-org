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
interface ITagManager
{
    /**
     * @param String $tag_val
     * @return ITag
     */
    public function addTag($tag_val);

    /**
     * @param String $tag_val
     * @param Int $tag_id
     * @return ITag
     */
    public function updateTag($tag_val, $tag_id);

    /**
     * @param Int[] $tag_ids
     * @return Bool
     */
    public function deleteTags($tag_ids);

    /**
     * @param String $merge_tag
     * @param ITag[] $tags
     * @return ITag
     */
    public function mergeTags($merge_tag, $tags);

    /**
     * @param String $tag_val
     * @param Int $tag_id
     * @return Bool
     */
    public function splitTag($tag_val, $tag_id);


}