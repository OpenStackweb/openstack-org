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
	 * @var IFileUploadService
	 */
	private $upload_service;

	/**
	 * @param IEntityRepository      $news_repository
	 * @param IEntityRepository      $submitter_repository
	 * @param INewsFactory           $factory
	 * @param INewsValidationFactory $validator_factory
	 * @param IFileUploadService     $upload_service
	 * @param ITransactionManager    $tx_manager
	 */
	public function __construct(IEntityRepository $news_repository,
                                IEntityRepository $submitter_repository,
	                            INewsFactory $factory,
	                            INewsValidationFactory $validator_factory,
	                            IFileUploadService $upload_service,
	                            ITransactionManager $tx_manager){

		$this->news_repository         = $news_repository;
        $this->submitter_repository         = $submitter_repository;
		$this->validator_factory       = $validator_factory;
		$this->factory                 = $factory;
		$this->upload_service          = $upload_service;
		$this->tx_manager              = $tx_manager;
	}
	/**
	 * @param array $data
	 * @return INews
	 */
	public function postNews(array $data){
		$validator_factory    = $this->validator_factory;
		$factory              = $this->factory;
		$repository           = $this->news_repository ;
        $submitter_repository = $this->submitter_repository;
		$upload_service       = $this->upload_service;

		return $this->tx_manager->transaction(function() use($data, $repository, $submitter_repository, $factory, $validator_factory, $upload_service){
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
                $submitter,
				$upload_service
			);

			$repository->add($news);
		});
	}

	/**
	 * @param array $data
	 * @return INews
	 */
	public function updateNews(array $data){
        $validator_factory    = $this->validator_factory;
        $factory              = $this->factory;
        $repository           = $this->news_repository ;
        $upload_service       = $this->upload_service;

		return $this->tx_manager->transaction(function() use($data, $repository, $validator_factory, $factory, $upload_service){
			$validator = $validator_factory->buildValidatorForNews($data);
			if ($validator->fails()) {
				throw new EntityValidationException($validator->messages());
			}

			$news = $repository->getById(intval($data['newsID']));
			if(!$news)
				throw new NotFoundEntityException('News',sprintf('id %s',$data['id'] ));


            $news_main_info = $factory->buildNewsMainInfo($data);
            $news->registerMainInfo($news_main_info);
            //create image object
            $image_info = $news_main_info->getImage();
            if($image_info['size']){
                $news->registerImage($upload_service);
            }
            //create image object
            $document_info = $news_main_info->getDocument();
            if($document_info['size']){
                $news->registerDocument($upload_service);
            }

            $news->clearTags();
            $news->registerTags($data['tags']);

			return $news;
		});
	}

} 