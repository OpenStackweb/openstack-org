<?php

/**
 * Defines the ArticleHolder page type
 */
class ArticleHolder extends Page
{
	static $db = array();
	static $has_one = array();

	static $allowed_children = array('ArticlePage');
	static $icon = "themes/tutorial/images/treeicons/news";
}

class ArticleHolder_Controller extends Page_Controller
{
	function rss()
	{
		$rss = new RSSFeed($this->Children(), $this->Link(), "The coolest news around");
		$rss->outputToBrowser();
	}

	function init()
	{
		RSSFeed::linkToFeed($this->Link() . "rss");
		parent::init();
	}
}