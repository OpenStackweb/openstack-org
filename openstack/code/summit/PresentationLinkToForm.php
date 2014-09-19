<?php

class PresentationLinkToForm extends Form {

   function __construct($controller, $name) {

            $LinkField = new TextField('HostedMediaURL','Link (URL) for your online presentation:');

             $fields = new FieldList(
                     $LinkField
             );
             $actions = new FieldList(
                     new FormAction('saveLink', 'Save Link')
             );
             $validator = new RequiredFields(array('HostedMediaURL'));

             parent::__construct($controller, $name, $fields, $actions, $validator);

     }

     function forTemplate() {
        return $this->renderWith(array(
           $this->class,
           'Form'
        ));
     }

     function saveLink($data, $form) {

       $url = $data['HostedMediaURL'];

       $EventID = Session::get('UploadMedia.PresentationID');
       if($EventID) $Event  = SchedEvent::get()->byID($EventID);
       if($Event) $Metadata = SchedEventMetadata::get()->filter('event_key', $Event->event_key)->first();
       
       // If this event exists but has no metadata, create a new record and link it to the event.
       if($Event && !$Metadata) {
            $Metadata = new SchedEventMetadata();
            $Metadata->event_key = $Event->event_key;
        }

        // Attach a protocol if needed
        if(substr($url,0,7) != 'http://' && substr($url,0,8) != 'https://') $url = 'http://'.$url;

        if(!filter_var($url, FILTER_VALIDATE_URL))
        {
            $form->sessionMessage('That does not appear to be a valid URL','bad'); 
            return $this->controller()->redirectBack(); 
        } elseif(!$Event || !$Metadata) {
            $data["HasError"] = TRUE;
            return $this->controller()->Customise($data);
        } else {
            $Metadata->HostedMediaURL = $url;
            $Metadata->MediaType = 'URL';
            $Metadata->write();
            Session::set('UploadMedia.Success', TRUE);
            Session::set('UploadMedia.URL', $url);
            Session::set('UploadMedia.Type', 'URL');

	        Controller::curr()->redirect($form->controller()->link().'Success');

        }

     }

}