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

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;


class IngestMemberBadgesForm extends Form
{

	function __construct($controller, $name)
	{


		$badgeType = new DropdownField('badgeType','Choose A type', array(
            'oui' => "Upstream Institute",
            'cca' => "Community Contributor Award",
        ));

		$fileInput = new FileAttachmentField('ingestFile', 'Select file to ingest');
        $fileInput->setAllowedMaxFileNumber(1);
        $fileInput->setAllowedExtensions(array('yml', 'yaml'));

		$fields = new FieldList(
            $badgeType,
            $fileInput
		);

		$actions = new FieldList(
			new FormAction('submit', 'Ingest')
		);

		parent::__construct($controller, $name, $fields, $actions);

		// Create Validators
		$validator = new RequiredFields('badgeType');


		$this->disableSecurityToken();


	}

	function forTemplate()
	{
		return $this->renderWith(array(
			$this->class,
			'Form'
		));
	}

	function submit($data, $form)
	{
        $ingestFile = CloudFile::get()->byID($data['ingestFile']);

        // parse yaml file and ingest
        $content = file_get_contents($ingestFile->getUrl());
        $entries = Yaml::parse($content);

        foreach($entries['recipients'] as $ingestMember) {
            $member = Member::get()->filter('Email', $ingestMember['email'])->first();
            if (!$member) continue;

            if ($data['badgeType'] == 'oui') {
                if ($member && !$member->isUpstreamStudent()) {
                    $ouiMember = new OSUpstreamInstituteStudent();
                    $ouiMember->Email = $ingestMember['email'];
                    $ouiMember->FirstName = $ingestMember['first_name'];
                    $ouiMember->LastName = $ingestMember['last_names'];
                    $ouiMember->MemberID = $member->ID;
                    $ouiMember->write();
                }
            }

            if ($data['badgeType'] == 'cca') {
                if ($member && !$contributor = $member->CommunityContributor()) {
                    $contributor = new CommunityContributor();
                    $contributor->Email = $ingestMember['email'];
                    $contributor->FirstName = $ingestMember['first_name'];
                    $contributor->LastName = $ingestMember['last_name'];
                    $contributor->MemberID = $member->ID;
                    $contributor->write();
                }

                $awards = explode(',',$ingestMember['awards']);
                foreach ($awards as $award) {
                    if (!$contributor->Awards()->filter('Name', $award)->first()) {
                        $awardObj = new CommunityAward();
                        $awardObj->Name = $award;
                        $awardObj->write();
                        $contributor->Awards()->add($awardObj);
                    }
                }

                $contributor->write();

            }


        }

        Controller::curr()->redirect('/sangria/');
	}

}