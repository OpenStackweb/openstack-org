<?php
/**
 * Copyright 2017 Openstack Foundation
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
 * Class BooksDirectoryPage
 */
class BooksDirectoryPage extends MarketPlaceDirectoryPage
{
	static $allowed_children = "none";
}
/**
 * Class BooksDirectoryPage_Controller
 */
class BooksDirectoryPage_Controller extends MarketPlaceDirectoryPage_Controller {

	static $allowed_actions = array(
        'handleIndex',
        'handleFilter',
	);

	static $url_handlers = array(
        'f/$Loc/$Service/$Keyword/$Region' => 'handleFilter',
		'$Company!/$Slug!' => 'handleIndex',
	);

	/**
	 * @var BookManager
	 */
	private $manager;

	function init()	{
		parent::init();

		Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

		Requirements::customScript("jQuery(document).ready(function($) {
            $('#books','.marketplace-nav').addClass('current');
        });");

        JSChosenDependencies::renderRequirements();

        $this->manager = new BookManager (
			new SapphireMarketPlaceTypeRepository,
            new BookFactory,
            new MarketplaceFactory,
            new ValidatorFactory,
            SapphireTransactionManager::getInstance()
		);

	}

	public function handleIndex() {
		$params = $this->request->allParams();
		if(isset($params["Company"]) && isset($params["Slug"])){
			//render instance ...
			return $this->book();
		}
	}

    public function handleFilter()
    {
        $keyword = $this->request->param('Keyword');
        $keyword_val = ($keyword == 'a') ? '' : $keyword;
        return $this->getViewer('')->process($this->customise(array('Keyword' => $keyword_val)));
    }

	public function getBooks(){
		//return on view model
		return Book::get()->sort('Title');
	}

	public function book(){
		try{
			$params              = $this->request->allParams();
			$company_url_segment = Convert::raw2sql($params["Company"]);
			$slug                = Convert::raw2sql($params["Slug"]);
            $book                = Book::get()->filter('Slug')->first();
			if(!$book) throw new NotFoundEntityException('Book','by slug');
			if($book->getCompany()->URLSegment != $company_url_segment) throw new NotFoundEntityException('','');
            // we need this for reviews.
            $this->book_ID = $book->getIdentifier();
			$render = new BookSapphireRender($book);
			return $render->draw();
		}
		catch (Exception $ex) {
			return $this->httpError(404, 'Sorry that Book could not be found!.');
		}
	}


}
