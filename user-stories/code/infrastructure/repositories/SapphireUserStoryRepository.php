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
 * Class SapphireUserStoryRepository
 */
final class SapphireUserStoryRepository
    extends SapphireRepository
    implements IUserStoryRepository {

	public function __construct(){
		parent::__construct(new UserStoryDO());
	}

    public function getAllStories($sort_by = 'LastEdited', $sort_dir = 'DESC') {
        return UserStoryDO::get()->sort($sort_by, $sort_dir);
    }

    public function getAllActive($sort_by = 'LastEdited', $sort_dir = 'DESC') {
        return UserStoryDO::get()->filter('Active',1)->sort($sort_by, $sort_dir);
    }

    public function findAllActive($search_term) {
        return UserStoryDO::get()->filter('Active',1)
            ->where("Name LIKE '%$search_term%' OR Description LIKE '%$search_term%'")
            ->sort('LastEdited', 'DESC');
    }

    public function findAllActiveByTag($tag) {
        return UserStoryDO::get()
            ->filter(['Active' => 1, 'Tags.Tag' => $tag])
            ->sort('LastEdited', 'DESC');
    }

}