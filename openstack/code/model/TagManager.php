<?php

/**
 * Copyright 2017 Open Infrastructure Foundation
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
final class TagManager implements ITagManager
{

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * TagManager constructor.
     * @param ITransactionManager $tx_manager
     */
    public function __construct(ITransactionManager $tx_manager)
    {
        $this->tx_manager = $tx_manager;
    }

    /**
     * @param String $tag_val
     * @return ITag
     */
    public function addTag($tag_val) {

        return $this->tx_manager->transaction(function () use ($tag_val) {
            if (empty($tag_val))
                throw new ValidationException();

            $tag = new Tag();
            $tag->Tag = $tag_val;
            $tag->write();

            return $tag;
        });
    }

    /**
     * @param String $tag_val
     * @param Int $tag_id
     * @return ITag
     */
    public function updateTag($tag_val, $tag_id) {
        return $this->tx_manager->transaction(function () use ($tag_val, $tag_id) {
            $tag = Tag::get()->byID($tag_id);
            if (!$tag)
                throw new NotFoundEntityException('Tag');

            if (empty($tag_val))
                throw new ValidationException();

            $tag->Tag = $tag_val;
            $tag->write();

            return $tag;
        });
    }

    /**
     * @param Int $tag_id
     * @return Bool
     */
    public function deleteTags($tag_ids){
        return $this->tx_manager->transaction(function () use ($tag_ids) {
            foreach ($tag_ids as $tag_id) {
                $tag = Tag::get()->byID($tag_id);
                if (!$tag)
                    throw new NotFoundEntityException('Tag');

                $tag->delete();
            }

            return true;
        });
    }

    /**
     * @param String $merge_tag
     * @param ITag[] $tags
     * @return ITag
     */
    public function mergeTags($merge_tag, $tags){
        return $this->tx_manager->transaction(function () use ($merge_tag, $tags) {
            if (empty($merge_tag))
                throw new ValidationException();

            $new_tag = Tag::get()->filter(['Tag:case' => $merge_tag])->first();
            if (!$new_tag) {
                $new_tag = new Tag();
                $new_tag->Tag = $merge_tag;
                $new_tag->write();
            }

            $tags = $tags->exclude('ID', $new_tag->ID);

            foreach ($tags as $old_tag) {
                if($manyMany = $old_tag->many_many()) {
                    foreach($manyMany as $relationship => $class) {
                        $newComponents = $new_tag->getManyManyComponents($relationship)->column('ID');
                        $oldComponents = $old_tag->getManyManyComponents($relationship)->column('ID');

                        $allComponents = array_unique(array_merge($newComponents, $oldComponents));

                        $new_tag->getManyManyComponents($relationship)->setByIDList($allComponents);
                    }
                }

                $old_tag->delete();
            }

            return $new_tag;
        });
    }

    /**
     * @param String $tag_val
     * @param Int $tag_id
     * @return Bool
     */
    public function splitTag($tag_val, $tag_id){
        return $this->tx_manager->transaction(function () use ($tag_val, $tag_id) {
            $tag = Tag::get()->byID($tag_id);
            if (!$tag)
                throw new NotFoundEntityException('Tag');

            if (empty($tag_val))
                throw new ValidationException();

            $tag_vals = explode(' ', $tag_val);
            foreach ($tag_vals as $tag_val) {
                $new_tag = Tag::get()->filter(['Tag:case' => $tag_val])->first();

                if (!$new_tag) {
                    $new_tag = new Tag();
                    $new_tag->Tag = $tag_val;
                    $new_tag->write();
                }

                if($manyMany = $tag->many_many()) {
                    foreach($manyMany as $relationship => $class) {
                        $newComponents = $new_tag->getManyManyComponents($relationship)->column('ID');
                        $oldComponents = $tag->getManyManyComponents($relationship)->column('ID');

                        $allComponents = array_unique(array_merge($newComponents, $oldComponents));

                        $new_tag->getManyManyComponents($relationship)->setByIDList($allComponents);
                    }
                }
            }
            $tag->delete();

            return true;
        });
    }

}