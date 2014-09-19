<?php

final class CompanyServiceAssembler {

	public static function convertCompanyServiceToArray(ICompanyService $company_service){
		$res = array();
		//header
		$res['id']                = $company_service->getIdentifier();
		$res['name']              = $company_service->getName();
		$res['overview']          = $company_service->getOverview();
		$res['call_2_action_uri'] = $company_service->getCall2ActionUri();
		$res['active']            = $company_service->isActive();
		$company = $company_service->getCompany();
		if($company)
			$res['company_id'] = $company->getIdentifier();
		//resources
		$additional_resources = array();
		foreach ($company_service->getResources() as $resource) {
			array_push($additional_resources,CompanyServiceAssembler::convertResource2Array($resource));
		}
		$res['additional_resources'] = $additional_resources;
		//videos
		$videos = array();
		foreach($company_service->getVideos() as $video){
			array_push($videos,MarketPlaceAssembler::convertVideo2Array($video));
		}
		$res['videos'] = $videos;
		return $res;
	}

	public static function convertResource2Array(ICompanyServiceResource $resource){
		$res = array();
		$res['id']    = $resource->getIdentifier();
		$res['name']  = $resource->getName();
		$res['order'] = $resource->getOrder();
		$res['link']  = $resource->getUri();
		return $res;
	}

} 