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
final class SapphireFileUploadService implements IFileUploadService {
	/**
	 * @param array $file_ids
	 * @param string  $fieldname
	 * @param IEntity $entity
	 * @return void
	 */
	public function upload(array $file_ids, $fieldname, IEntity $entity){
		// assume that the file is connected via a has-one
		$relation = $entity->hasMethod($fieldname) ? $entity->$fieldname() : null;
		// try to create a file matching the relation
		if($relation && ($relation instanceof RelationList || $relation instanceof UnsavedRelationList)) {
			// has_many or many_many
			$relation->setByIDList($file_ids);
		} elseif($entity->has_one($fieldname)) {
			// has_one
			$entity->{"{$fieldname}ID"} = $file_ids ? reset($file_ids) : 0;
		}
	}
} 