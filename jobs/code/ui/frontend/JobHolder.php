<?php
/**
 * Class JobHolder
 */
class JobHolder extends Page {
	private static $db = array();
	private static $has_one = array();
	private static $allowed_children = array('JobPage');
}
/**
 * Class JobHolder_Controller
 */
class JobHolder_Controller extends Page_Controller {
	/**
	 * @var IEntityRepository
	 */
	private $repository;

	function init(){
		parent::init();
		RSSFeed::linkToFeed($this->Link() . "rss");
		Requirements::css('jobs/css/jobs.css');
		Requirements::javascript('jobs/js/jobs.js');
		$this->repository = new SapphireJobRepository;
	}

	function rss() {
		$rss = new RSSFeed($this->Children(), $this->Link(), "OpenStack Jobs Feed");
		$rss->outputToBrowser();
	}

	public function DateSortedJobs(){
		$query = new QueryObject(new JobPage);
		if(isset($_GET['foundation']))
			$query->addAddCondition(QueryCriteria::equal('FoundationJob',1));
		$now      = new DateTime();
		$query->addAddCondition(QueryCriteria::equal('Active',1));
		$post_date = $now->sub(new DateInterval('P6M'));
		$query->addAddCondition(QueryCriteria::greaterOrEqual('JobPostedDate',$post_date->format('Y-m-d')));
		$query->addAddCondition(QueryCriteria::greaterOrEqual('ExpirationDate',$now->format('Y-m-d')));
		$query->addOrder(QueryOrder::desc('JobPostedDate'));
		$query->addOrder(QueryOrder::desc('ID'));
		list($jobs,$size) = $this->repository->getAll($query,0,1000);
		return new ArrayList($jobs);
	}

	function PostJobLink(){
		$page = JobRegistrationRequestPage::get()->first();
		if($page){
			return $page->getAbsoluteLiveLink(false);
		}
		return '#';
	}
}