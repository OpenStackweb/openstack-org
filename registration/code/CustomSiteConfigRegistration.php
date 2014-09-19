<?php

class CustomSiteConfigRegistration extends DataExtension {

	private static $db =  array(
		'RegistrationSendMail' =>'Boolean',
		'RegistrationFromMessage' => 'Text',
		'RegistrationSubjectMessage' => 'Text',
		'RegistrationHTMLMessage' => 'HTMLText',
		'RegistrationPlainTextMessage' => 'Text',
	);

    public function updateCMSFields(FieldList $fields) {

        $fields->addFieldToTab("Root.RegistrationThankYouEmail", new CheckboxField ('RegistrationSendMail','Send Registration Thank You Email'));
        $fields->addFieldToTab("Root.RegistrationThankYouEmail", new TextField('RegistrationFromMessage','From Email'));
        $fields->addFieldToTab("Root.RegistrationThankYouEmail", new TextField('RegistrationSubjectMessage','Subject'));
        $fields->addFieldToTab("Root.RegistrationThankYouEmail", new HtmlEditorField('RegistrationHTMLMessage','Html Body'));
        $fields->addFieldToTab("Root.RegistrationThankYouEmail", new TextareaField('RegistrationPlainTextMessage','Plain Text Body'));

    }

    public function getCMSValidator(){
        $validator = new RequiredFields('RegistrationFromMessage','RegistrationSubjectMessage','RegistrationHTMLMessage','RegistrationPlainTextMessage');
        return $validator;
    }
}