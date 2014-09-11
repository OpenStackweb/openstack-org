<?php
/**
 * Class NewsRegistrationRequestManager
 */
final class NewsRegistrationRequestManager {
	/**
	 * @var IEntityRepository
	 */
	private $repository;
	/**
	 * @var INewsValidationFactory
	 */
	private $validator_factory;
	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;
	/**
	 * @var IJobFactory
	 */
	private $factory;

	/**
	 * @var INewsPublishingService
	 */
	private $news_publishing_service;

	/**
	 * @var IEntityRepository
	 */
	private $news_repository;

	/**
	 * @param IEntityRepository      $news_repository
	 * @param IJobFactory            $factory
	 * @param IJobsValidationFactory $validator_factory
	 * @param INewsPublishingService  $news_publishing_service
	 * @param ITransactionManager    $tx_manager
	 */
	public function __construct(IEntityRepository $news_repository,
	                            INewsFactory $factory,
	                            INewsValidationFactory $validator_factory,
	                            INewsPublishingService $news_publishing_service,
	                            ITransactionManager $tx_manager){

		$this->news_repository         = $news_repository;
		$this->validator_factory       = $validator_factory;
		$this->factory                 = $factory;
		$this->news_publishing_service = $news_publishing_service;
		$this->tx_manager              = $tx_manager;
	}
	/**
	 * @param array $data
	 * @return INews
	 */
	public function postNews(array $data){
		$validator_factory = $this->validator_factory;
		$factory           = $this->factory;
		$repository        = $this->news_repository ;
		return $this->tx_manager->transaction(function() use($data, $repository, $factory, $validator_factory){
			$validator = $validator_factory->buildValidatorForNewsRegistration($data);
			if ($validator->fails()) {
					throw new EntityValidationException($validator->messages());
			}
			$new_registration_request = $factory->buildNewsRegistrationRequest(
				$factory->buildNewsMainInfo($data),
				$factory->buildNewsTags($data),
				$factory->buildNewsSubmitter($data)
			);

			$repository->add($new_registration_request);
		});
	}

	/**
	 * @param array $data
	 * @return INewsRegistrationRequest
	 */
	public function updateNewsRegistrationRequest(array $data){
		$validator_factory = $this->validator_factory;
		$repository        = $this->repository ;
		$factory           = $this->factory;
        $news_publishing_service  = $this->news_publishing_service;

		return $this->tx_manager->transaction(function() use($data, $repository, $validator_factory, $factory,$news_publishing_service){
			$validator = $validator_factory->buildValidatorForNewsRegistration($data);
			if ($validator->fails()) {
				throw new EntityValidationException($validator->messages());
			}

			$request = $repository->getById(intval($data['id']));
			if(!$request)
				throw new NotFoundEntityException('NewsRegistrationRequest',sprintf('id %s',$data['id'] ));

			$request->registerMainInfo($factory->buildNewsMainInfo($data));
            $tags = $factory->buildTags($data);
			$request->clearTags();
			foreach($tags as $tag)
				$request->registerTag($tag);

			$request->registerSubmitter($factory->buildSubmitter($data));

			return $request;
		});
	}

	/**
	 * @param $id
	 * @param $news_link
	 * @return INews
	 */
	public function postNewsRegistrationRequest($id, $news_link){
		$repository               = $this->repository;
		$factory                  = $this->factory;
		$news_repository          = $this->news_repository;
		$news_publishing_service  = $this->news_publishing_service;

		return  $this->tx_manager->transaction(function() use ($id, $repository, $news_repository, $factory, $news_publishing_service, $news_link){
			$request = $repository->getById($id);
			if(!$request) throw new NotFoundEntityException('NewsRegistrationRequest',sprintf('id %s',$id ));
			$news = $factory->buildNews($request);
            $news_repository->add($news);
			$request->markAsPosted();
            $news_publishing_service->publish($news);

			return $news;
		});
	}



} 