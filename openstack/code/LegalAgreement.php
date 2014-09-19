<?php
class LegalAgreement extends DataObject {
 
    static $db = array(
        'Signature' => 'Varchar(255)'
    );
    
    static $has_one = array (
    	'LegalDocumentPage' => 'LegalDocumentPage',
        'Member' => 'Member'
    );

    function DocumentName() {
    	$LegalDocumentPage = LegalDocumentPage::get()->byID($this->LegalDocumentPageID);
    	return $LegalDocumentPage->Title;
    }

    function DocumentLink() {
    	$LegalDocumentPage =  LegalDocumentPage::get()->byID($this->LegalDocumentPageID);
    	return $LegalDocumentPage->Link();
    }

}