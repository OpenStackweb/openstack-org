<?php


class PresentationVideoPage extends Page {


}


class PresentationVideoPage_Controller extends Page_Controller {


	private static $allowed_actions = array (
		'show'
	);

	public function index(SS_HTTPRequest $r) {
		$p = SchedPresentation::get()->filter(array(
			'DisplayOnSite' => true
		));

		$k = $r->getVar('Keywords');
		$cat = $r->getVar('Category');
		$speaker = $r->getVar('Speaker');
		$summit = $r->getVar('Summit');
		$tag = $r->getVar('Tag');

		if(!empty($k)) {
			$p = $p->filterAny(array(
				'Title:PartialMatch' => $k,
				'Description:PartialMatch' => $k,
				'Tags.Title:ExactMatch' => $k
			));
		}

		if(!empty($cat)) {
			$p = $p->filter(array(
				'CategoryID' => $cat
			));
		}

		if(!empty($speaker)) {
			$p = $p->filter(array(
				'PresentationSpeakers.ID' => $speaker
			));
		}

		if(!empty($summit)) {
			$p = $p->filter(array(
				'SummmitID' => $summit
			));
		}

		if(!empty($tag)) {
			$p = $p->filter(array(
				'Tags.Title' => $tag
			));
		}

		return array (
			'Results' => new PaginatedList($p, $this->request)
		);
	}


	public function show(SS_HTTPRequest $r) {
		$presentation = Sluggable::get_by_slug('SchedPresentation', $r->param('ID'));

		if(!$presentation) return $this->httpError(404);

		return array (
			'Presentation' => $presentation
		);
	}


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