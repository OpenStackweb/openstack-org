<?php


class PresentationVideoPage extends Page {


}


class PresentationVideoPage_Controller extends Page_Controller {


	public function FilterForm() {
		return Form::create(
			$this,
			"FilterForm",
			FieldList::create()
				->text('Keywords')
				->dropdown('Category')
					->configure()
						->setSource(PresentationCategory::get()->map('ID','Title'))
						->setEmptyString('-- Any category --')
					->end()
				->dropdown('Speaker')
					->configure()
						->setSource(PresentationSpeaker::get()->map('ID','Title'))
						->setEmptyString('-- Any speaker --')
					->end()
				->dropdown('Summit')
					->configure()
						->setSource(Summit::get()->map('ID','Title'))
						->setEmptyString('-- Any summit --')
					->end(),
			FieldList::create(
				FormAction::create('doSearch','Search')
			)
		)
		->disableSecurityToken()
		->setFormAction($this->Link())
		->loadDataFrom($_REQUEST)
		->setFormMethod('GET');
	}
}