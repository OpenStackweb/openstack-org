<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class JobHolder
 */
class JobHolder extends Page {
	private static $db = array();
	private static $has_one = array();
}
/**
 * Class JobHolder_Controller
 */
class JobHolder_Controller extends Page_Controller {
	/**
	 * @var IEntityRepository
	 */
	private $repository;

	static $allowed_actions = array(
        'JobDetailsPage',
	);

    static $url_handlers = array(
        'GET view/$JOB_ID/$JOB_TITLE'   => 'JobDetailsPage',
    );

	function init(){
		parent::init();
		RSSFeed::linkToFeed($this->Link() . "rss");

		Requirements::css('jobs/css/jobs.css');
  		Requirements::javascript('jobs/js/jobs.js');

		$this->repository = new SapphireJobRepository;
	}

	function rss() {
        $request = Controller::curr()->getRequest();
        $foundation = ($request->requestVar('foundation'));
        $jobs = $this->repository->getDateSortedJobs($foundation);
		$rss = new RSSFeed($jobs, $this->Link(), "OpenStack Jobs Feed");
		$rss->outputToBrowser();
	}

    public function getDateSortedJobs() {
        $output = '';
        $request = Controller::curr()->getRequest();
        $foundation = ($request->requestVar('foundation'));
        $jobs = $this->repository->getDateSortedJobs($foundation);

        foreach ($jobs as $job) {
            $output .= $job->renderWith('JobHolder_job');
        }

        return $output;
    }

	function PostJobLink(){
		$page = JobRegistrationRequestPage::get()->first();
		if($page){
			return $page->getAbsoluteLiveLink(false);
		}
		return '#';
	}

    function JobDetailsPage() {
        $job_id = intval($this->request->param('JOB_ID'));
        $job = JobPage::get()->byID($job_id);
        if($job)
            return $this->renderWith(array('JobDetail','Page'),$job);
        return $this->httpError(404, 'Sorry that Job could not be found!.');
    }
}