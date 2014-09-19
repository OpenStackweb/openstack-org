<?php
/***
 * Class CCLACompanyService
 */
final class CCLACompanyService {

	/**
	 * @var ICLACompanyRepository
	 */
	private $company_repository;

	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;

	public function __construct(ICLACompanyRepository $company_repository, ITransactionManager $tx_manager){
		$this->company_repository = $company_repository;
		$this->tx_manager         = $tx_manager;
	}

	/**
	 * @param int $company_id
	 * @return DateTime
	 */
	public function signCCLA($company_id){

		$company_repository = $this->company_repository;

		return $this->tx_manager->transaction(function() use($company_id, $company_repository){
			$company = $company_repository->getById($company_id);

			if(!$company)
				throw new NotFoundEntityException('Company',sprintf(' id %s',$company_id));

			if(!$company->isICLASigned())
				$company->signICLA();

			return $company->ICLASignedDate();
		});
	}

	/**
	 * @param int $company_id
	 * @return void
	 */
	public function unsignCCLA($company_id){

		$company_repository = $this->company_repository;

		return $this->tx_manager->transaction(function() use($company_id, $company_repository){
			$company = $company_repository->getById($company_id);

			if(!$company)
				throw new NotFoundEntityException('Company',sprintf(' id %s',$company_id));

			if($company->isICLASigned())
				$company->unsignICLA();

		});
	}
} 