<?php

/*  Used to load the Javascript app that runs the track chairs process
*/

class EmailUtilsPage extends Page {

}


class EmailUtilsPage_Controller extends Page_Controller {


	public function init()
	{
		if (!Permission::check("ADMIN")) Security::permissionFailure();

		parent::init();
	}

	private static $allowed_actions = array (
		'ExportNoticeEmails',
		'ClearMailingList'
	);	

    private function CurrentPresentations() {

    	$summit = Summit::get_active();
    	$presentations = Presentation::get()->filter(array(
    			'Status' => 'Received',
    			'SummitID' => $summit->ID
    		));
    	return $presentations;
    }

    private function AssembleSpeakerEmail($speaker, $fp) {
    	$presentations = $speaker->AllPresentations();

    	$speakerPresentationList = '';

    	foreach ($presentations as $presentation) {
    		$speakerPresentationList = $speakerPresentationList . $presentation->Title . '<br/>';
    	}

		// Output speaker row
		$fields = array('speaker', $speaker->ID, $speaker->MemberID, $speaker->Member()->Email, $speakerPresentationList);
		fputcsv($fp, $fields);

    }

    private function AssembleCreatorEmail($member, $fp) {
    	$presentations = $this->CurrentPresentations()->filter(
    		'CreatorID', $member->ID
    	);

    	$creatorPresentationList = '';

    	foreach ($presentations as $presentation) {
    		$creatorPresentationList = $creatorPresentationList . $presentation->Title . '<br/>';
    	}

		// Output speaker row
		$fields = array('creator','',$member->ID, $member->Email, $creatorPresentationList);
		fputcsv($fp, $fields);

    }


    public function ExportNoticeEmails() {

    	$presentations = $this->CurrentPresentations();

		$filepath = $_SERVER['DOCUMENT_ROOT'].'/assets/speaker-notifications.csv';
		$fp = fopen($filepath, 'w');    	

		$fields = array('Type', 'Speaker ID', 'Member ID', 'Email', 'Presentations');
		fputcsv($fp, $fields);

    	foreach ($presentations as $presentation) {

    		// Email speakers first
    		$speakers = $presentation->Speakers();
    		foreach ($speakers as $speaker) {
    			if(!$speaker->BeenEmailed) {
    				$this->AssembleSpeakerEmail($speaker, $fp);
    			}

    			$speaker->BeenEmailed = true;
    			$speaker->write();

    		}

    		// Email creator if need be
    		if(!$presentation->creatorIsSpeaker()) {
    			$this->AssembleCreatorEmail($presentation->Creator(), $fp);
    		}

    		$presentation->BeenEmailed = true;
    		$presentation->write();   		

    	}

    	fclose($fp);
        
        header("Cache-control: private");
        header("Content-type: application/force-download");
        header("Content-transfer-encoding: binary\n");
        header("Content-disposition: attachment; filename=\"speaker-notifications.csv\"");
        header("Content-Length: ".filesize($filepath));
        readfile($filepath);


    }

    public function ClearMailingList() {
    	$presentations = $this->CurrentPresentations();

    	foreach ($presentations as $presentation) {

    		// Email speakers first
    		$speakers = $presentation->Speakers();
    		foreach ($speakers as $speaker) {
    			$speaker->BeenEmailed = false;
    			$speaker->write();
    		}

    		$presentation->BeenEmailed = false;
    		$presentation->write();

    	}

    	$this->redirectBack();

    }


}