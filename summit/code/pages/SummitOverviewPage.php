<?php

class SummitOverviewPage extends SummitPage {

    
}


class SummitOverviewPage_Controller extends SummitPage_Controller {

	public function init() {
        $this->top_section = 'full';
        parent::init();
	}    
    
}