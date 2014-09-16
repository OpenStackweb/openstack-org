<?php

/**
 * Class NewsRequestForm
 */
final class NewsRequestForm extends HoneyPotForm {

	function __construct($controller, $name, $article = null, $use_actions = true) {

		//madatory fields
		$HeadlineField = new TextField('headline','Headline');
		$SummaryField = new TextareaField('summary','Summary',2,2);
		$TagsField = new TextField('tags','Tags');
		$DateField = new DateField('date','Date');
        $DateField->addExtraClass('date inline');
        $DateEmbargoField = new DateField('date_embargo','Embargo Date');
        $DateEmbargoField->addExtraClass('date inline');
        $UpdatedField = new DatetimeField_Readonly('date_updated','Last Updated');
        $UpdatedField->addExtraClass('inline');
        //optional fields
        $BodyField = new TextareaField('body','Body');
        $LinkField = new TextField('link','Link');
        $DocumentField = new FileField('Document','Document');
        $ImageField = new CustomSimpleImageField('Image', 'Image');
        $DateExpireField = new TextField('date_expire','Date Expire');
        $DateExpireField->addExtraClass('date');
        // submitter fields
        $SubmitterFirstNameField = new TextField('submitter_first_name','First Name');
        $SubmitterLastNameField = new TextField('submitter_last_name','Last Name');
        $SubmitterEmailField = new TextField('submitter_email','Email');
        $SubmitterCompanyField = new TextField('submitter_company','Company');
        $SubmitterPhoneField = new TextField('submitter_phone','Phone');

        /*if($article) {
            $FirstNameField->setValue($article->FirstName);
            $LastNameField->setValue($article->Surname);
            $BioField->setValue($speaker->Bio);
            $SpeakerIDField->setValue($speaker->ID);
            $MemberIDField->setValue($speaker->MemberID);
            $TitleField->setValue($speaker->Title);
            $IRCHandleField->setValue($speaker->IRCHandle);
            $TwiiterNameField->setValue($speaker->TwitterName);
            $OptInField->setValue($speaker->AviableForBureau);
            $FundedTravelField->setValue($speaker->FundedTravel);
            $ExpertiseField->setValue($speaker->Expertise);
        }*/

        $fields = new FieldSet (
            $HeadlineField,
            $SummaryField,
            $TagsField,
            $DateField,
            $DateEmbargoField,
            $UpdatedField,
            new LiteralField('clear', '<div class="clear"></div>'),
            $BodyField,
            $LinkField,
            $DocumentField,
            new LiteralField('break', '<br/>'),
            $ImageField,
            new LiteralField('break', '<br/>'),
            $DateExpireField,
            new LiteralField('break', '<br/><hr/>'),
            new LiteralField('title', '<h2>Submitter</h2>'),
            $SubmitterFirstNameField,
            $SubmitterLastNameField,
            $SubmitterEmailField,
            $SubmitterCompanyField,
            $SubmitterPhoneField
        );

		// Create action
		$actions = new FieldSet();
	    $actions->push(new FormAction('saveNewsArticle', 'Save'));

		// Create validators
		$validator = new ConditionalAndValidationRule(array(new RequiredFields('headline','summary','tags','date','date_embargo')));
		$validator->setJavascriptValidationHandler('none');
        $this->addExtraClass('news-registration-form');
		parent::__construct($controller, $name, $fields, $actions, $validator);
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