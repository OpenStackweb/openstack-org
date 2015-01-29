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
 * Class NewsRequestForm
 */
final class NewsRequestForm extends HoneyPotForm {

	function __construct($controller, $name, $article = null, bool $is_manager, $use_actions = true) {

        $IDField = new HiddenField('newsID');
		//madatory fields
		$HeadlineField = new TextField('headline','Headline (50 char max)','',$maxLength = 50);
		$SummaryField = new HtmlEditorField('summary','Summary (250 char max)','',$maxLength = 250);
        $SummaryField->addExtraClass('summary');
        $CityField = new TextField('city','City');
        $StateField = new TextField('state','State');
        $CountryField = new CountryDropdownField('country','Country');
		$TagsField = new TextField('tags','Tags');
		$DateField = new TextField('date','Date of Article/Release');
        $DateField->addExtraClass('datefield inline');
        if ($is_manager) {
            $DateEmbargoField = new TextField('date_embargo','Embargo Date');
            $DateEmbargoField->addExtraClass('datefield inline');
            $DateExpireField = new TextField('date_expire','Expire Date');
            $DateExpireField->addExtraClass('datefield');
        }

        $UpdatedField = new DatetimeField_Readonly('date_updated','Last Updated');
        $UpdatedField->addExtraClass('inline');
        //optional fields
        $BodyField = new HtmlEditorField('body','Body (2000 char max)','',$maxLength = 2000);
        $LinkField = new TextField('link','Link');

		$DocumentField = new CustomUploadField('Document', 'Document');
		$DocumentField->addExtraClass('hidden');
		$DocumentField->setCanAttachExisting(false);
		$DocumentField->setAllowedMaxFileNumber(1);
		$DocumentField->setAllowedFileCategories('doc');
		$DocumentField->setTemplateFileButtons('CustomUploadField_FrontEndFIleButtons');
		$DocumentField->setFolderName('news-documents');
		$sizeMB = 1; // 1 MB
		$size = $sizeMB * 1024 * 1024; // 1 MB in bytes
		$DocumentField->getValidator()->setAllowedMaxFileSize($size);
		$DocumentField->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field
		$DocumentField->setRecordClass('File');

		$ImageField = new CustomUploadField('Image', 'Image (Max size 2Mb - Recommend size is 293x381px)');
		$ImageField->setCanAttachExisting(false);
		$ImageField->setAllowedMaxFileNumber(1);
		$ImageField->setAllowedFileCategories('image');
		$ImageField->setTemplateFileButtons('CustomUploadField_FrontEndFIleButtons');
		$ImageField->setFolderName('news-images');
		$ImageField->setRecordClass('BetterImage');
        $ImageField->getUpload()->setReplaceFile(false);
        $ImageField->setOverwriteWarning(false);
		$sizeMB = 2; // 2 MB
		$size = $sizeMB * 1024 * 1024; // 2 MB in bytes
		$ImageField->getValidator()->setAllowedMaxFileSize($size);

		$ImageField->setCanPreviewFolder(false); // Don't show target filesystem folder on upload field

        if($article) {
            $IDField->setValue($article->ID);
            $HeadlineField->setValue($article->Headline);
            $SummaryField->setValue($article->Summary);
            $CityField->setValue($article->City);
            $StateField->setValue($article->State);
            $CountryField->setValue($article->Country);
            $TagsField->setValue($article->getTagsCSV());
            $DateField->setValue($article->Date);
            $DateEmbargoField->setValue($article->DateEmbargo);
            $UpdatedField->setValue($article->LastEdited);
            $BodyField->setValue($article->Body);
            $LinkField->setValue($article->Link);
            $DateExpireField->setValue($article->DateExpire);
            //submitter read only
            $SubmitterFirstNameField = new ReadonlyField('submitter_first_name','First Name');
            $SubmitterLastNameField = new ReadonlyField('submitter_last_name','Last Name');
            $SubmitterEmailField = new ReadonlyField('submitter_email','Email');
            $SubmitterCompanyField = new ReadonlyField('submitter_company','Company');
            $SubmitterPhoneField = new ReadonlyField('submitter_phone','Phone');

            $SubmitterFirstNameField->setValue($article->getSubmitter()->FirstName);
            $SubmitterLastNameField->setValue($article->getSubmitter()->LastName);
            $SubmitterEmailField->setValue($article->getSubmitter()->Email);
            $SubmitterCompanyField->setValue($article->getSubmitter()->Company);
            $SubmitterPhoneField->setValue($article->getSubmitter()->Phone);
        } else {
            // submitter fields
            $SubmitterFirstNameField = new TextField('submitter_first_name','First Name');
            $SubmitterLastNameField = new TextField('submitter_last_name','Last Name');
            $SubmitterEmailField = new TextField('submitter_email','Email');
            $SubmitterCompanyField = new TextField('submitter_company','Company');
            $SubmitterPhoneField = new TextField('submitter_phone','Phone');
        }

        $fields = new FieldList (
            $IDField,
            $HeadlineField,
            $SummaryField,
            $CityField,
            $StateField,
            $CountryField,
            $TagsField,
            $DateField
        );

        if ($is_manager) {
            $fields->push($DateEmbargoField);
            $fields->push($UpdatedField);
        }

        $fields->push(new LiteralField('clear', '<div class="clear"></div>'));
        $fields->push($BodyField);
        $fields->push($LinkField);
        $fields->push($DocumentField);


        if ($article) {
            $image = $article->Image();
            $document = $article->Document();
            if ($document->exists()) {
                $fields->push(new LiteralField('image_preview', $document->CMSThumbnail()));
            }
            $fields->push(new LiteralField('break', '<br/>'));
            $fields->push($ImageField);
            if ($image->exists()) {
	            $ImageField->setValue(null,$article);
            }
        } else {
            $fields->push(new LiteralField('break', '<br/>'));
            $fields->push($ImageField);
        }

        if ($is_manager) {
            $fields->push(new LiteralField('break', '<br/>'));
            $fields->push($DateExpireField);
        }

        $fields->push(new LiteralField('break', '<br/><hr/>'));
        $fields->push(new LiteralField('title', '<h2>Submitter</h2>'));
        $fields->push($SubmitterFirstNameField);
        $fields->push($SubmitterLastNameField);
        $fields->push($SubmitterEmailField);
        $fields->push($SubmitterCompanyField);
        $fields->push($SubmitterPhoneField);


		// Create action
		$actions = new FieldList();
	    $actions->push(new FormAction('saveNewsArticle', 'Save'));

	    $this->addExtraClass('news-registration-form');
		parent::__construct($controller, $name, $fields, $actions, $validator = null);
	}

	function forTemplate() {
		return $this->renderWith(array(
			$this->class,
			'Form'
		));
	}

	function submit($data, $form) {
		// do stuff here
	}
}