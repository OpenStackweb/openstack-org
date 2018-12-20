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
 * Class CandidateApplicationForm
 */
final class CandidateApplicationForm extends HoneyPotForm {

    /**
     * CandidateApplicationForm constructor.
     * @param Controller $controller
     * @param String $name
     * @param ElectionPage $page
     */
   function __construct($controller, $name, ElectionPage $page) {

      $fields = new FieldList (
        new TextAreaField('Bio', $page->CandidateApplicationFormBioLabel),
        new TextAreaField('RelationshipToOpenStack', $page->CandidateApplicationFormRelationshipToOpenStackLabel),
        new TextAreaField('Experience', $page->CandidateApplicationFormExperienceLabel),
        new TextAreaField('BoardsRole', $page->CandidateApplicationFormBoardsRoleLabel),
        new TextAreaField('TopPriority', $page->CandidateApplicationFormTopPriorityLabel)
      );

      $actionButton = new FormAction('saveCandidateApplicationForm', 'Save Candidate Application');
      //$actionButton->addExtraClass('btn green-btn');
	 
       $actions = new FieldList(
          $actionButton
       );
   
      parent::__construct($controller, $name, $fields, $actions);

   }
 
   function forTemplate() {
      return $this->renderWith([
         $this->class,
         'Form'
      ]);
   }
  
}
