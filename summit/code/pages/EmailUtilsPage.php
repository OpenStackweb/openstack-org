<?php

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

    private function CurrentSpeakers() {

    	$summit = Summit::get_active();
    	return $summit->Speakers(); 
    }

    private function AssembleEmail($speaker, $fp) {

        $votingURL = "https://www.openstack.org/summit/tokyo-2015/vote-for-speakers/Presentation/";
    	
        $myPresentations = $speaker->MyPresentations();
        $otherPresentations = $speaker->OtherPresentations();

        // Build a list of the presentations this person owns for export
        $myPresentationList = '';
        if($myPresentations->count()) $myPresentationList = '<strong>Presentations You Submitted</strong>  <ul>';

    	foreach ($myPresentations as $presentation) {

    		$myPresentationList = $myPresentationList . 
                '<li>'.
                $presentation->Title . '<br/>' .
                '<a href="' . $votingURL . $presentation->ID . '">' . $votingURL . $presentation->ID . '</a></li>';
    	}

        if($myPresentations->count()) $myPresentationList = $myPresentationList . '</ul>';

        // Build a list of presentaitons created by others that feature this person as a speaker
        $otherPresentationList = '';
        if($otherPresentations->count()) $otherPresentationList = '<strong>Presentations Others Submitted With You As A Speaker</strong> <ul>';

        foreach ($otherPresentations as $presentation) {

            $otherPresentationList = $otherPresentationList . 
                '<li>'.
                $presentation->Title . '<br/>' .
                '<a href="' . $votingURL . $presentation->ID . '">' . $votingURL . $presentation->ID . '</a></li>';
        }

        if($otherPresentations->count()) $otherPresentationList = $otherPresentationList . '</ul>';

        $fullPresentationList = $myPresentationList . $otherPresentationList;

		// Output speaker row
		$fields = array($speaker->FirstName, $speaker->LastName, $speaker->ID, $speaker->MemberID, $speaker->Member()->Email, $fullPresentationList);
		fputcsv($fp, $fields);

    }

    public function ExportNoticeEmails() {

    	$speakers = Summit::get_active()->Speakers();

		$filepath = $_SERVER['DOCUMENT_ROOT'].'/assets/speaker-notifications.csv';
		$fp = fopen($filepath, 'w');    	

		$fields = array('First_Name', 'Last_Name', 'Speaker_ID', 'Member_ID', 'Email', 'Presentation_List');
		fputcsv($fp, $fields);

        foreach ($speakers as $speaker) {
            $this->AssembleEmail($speaker, $fp);
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