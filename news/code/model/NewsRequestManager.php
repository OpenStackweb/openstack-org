<?php
/**
 * Class NewsRequestManager
 */
final class NewsRequestManager {
	/**
	 * @var INewsValidationFactory
	 */
	private $validator_factory;
	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;
	/**
	 * @var INewsFactory
	 */
	private $factory;

	/**
	 * @var IEntityRepository
	 */
	private $news_repository;

    /**
     * @var IEntityRepository
     */
    private $submitter_repository;

	/**
	 * @param IEntityRepository      $news_repository
     * @param IEntityRepository      $submitter_repository
	 * @param INewsFactory           $factory
	 * @param INewsValidationFactory $validator_factory
	 * @param ITransactionManager    $tx_manager
	 */
	public function __construct(IEntityRepository $news_repository,
                                IEntityRepository $submitter_repository,
	                            INewsFactory $factory,
	                            INewsValidationFactory $validator_factory,
	                            ITransactionManager $tx_manager){

		$this->news_repository         = $news_repository;
        $this->submitter_repository         = $submitter_repository;
		$this->validator_factory       = $validator_factory;
		$this->factory                 = $factory;
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
        $submitter_repository = $this->submitter_repository;

		return $this->tx_manager->transaction(function() use($data, $repository, $submitter_repository, $factory, $validator_factory){
			$validator = $validator_factory->buildValidatorForNews($data);
			if ($validator->fails()) {
					throw new EntityValidationException($validator->messages());
			}

            $submitter = $submitter_repository->getSubmitterByEmail($data['submitter_email']);
            if (!$submitter) {
                $submitter = $factory->buildNewsSubmitter($data);
            }

			$news = $factory->buildNews(
				$factory->buildNewsMainInfo($data),
				$data['tags'],
                $submitter
			);

			$repository->add($news);
		});
	}

	/**
	 * @param array $data
	 * @return INews
	 */
	public function updateNews(array $data){
		$validator_factory = $this->validator_factory;
        $factory           = $this->factory;
        $repository        = $this->news_repository ;

		return $this->tx_manager->transaction(function() use($data, $repository, $validator_factory, $factory){
			$validator = $validator_factory->buildValidatorForNews($data);
			if ($validator->fails()) {
				throw new EntityValidationException($validator->messages());
			}

			$news = $repository->getById(intval($data['id']));
			if(!$news)
				throw new NotFoundEntityException('News',sprintf('id %s',$data['id'] ));

            $news->registerMainInfo($factory->buildNewsMainInfo($data));
            $tags = $factory->buildTags($data);
            $news->clearTags();
			foreach($tags as $tag)
                $news->registerTag($tag);

            $news->registerSubmitter($factory->buildSubmitter($data));

			return $news;
		});
	}

} 