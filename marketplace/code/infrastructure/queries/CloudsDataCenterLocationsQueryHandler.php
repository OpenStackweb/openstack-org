<?php

/**
 * Class CloudsDataCenterLocationsQueryHandler
 */
abstract class CloudsDataCenterLocationsQueryHandler implements ICloudsDataCenterLocationsQueryHandler {

	/**
	 * @return string
	 */
	abstract function getClassName();

	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification){

		$params = $specification->getSpecificationParams();
		$filter = '';

		if(!empty($term)){
			$term   = @$params['name_pattern'];
			$term   = Convert::raw2sql($term);

			$countries         = Geoip::getCountryDropDown();
			$matched_countries = array_filter($countries, function($el) use ($term) {
				return ( strpos( strtolower($el), strtolower($term)) !== false );
			});
			$country_filter = '';
			if(count($matched_countries)){
				foreach($matched_countries as $code => $name ){
					$country_filter .= " OR Country = '{$code}' ";
				}
			}
			else{
				$country_filter =  " OR Country LIKE '%{$term}%' ";
			}
			$filter = "AND City LIKE '%{$term}%' {$country_filter}";
		}

		$locations  = array();
		$class_name = $this->getClassName();
		$sql        = <<< SQL
        SELECT City,Country,State FROM DataCenterLocation
        INNER JOIN CompanyService ON CompanyService.ID = DataCenterLocation.CloudServiceID
		WHERE CompanyService.ClassName = '{$class_name}'
		{$filter}
		GROUP BY City,Country,State
		ORDER BY City,State, Country ASC

SQL;
		$results = DB::query($sql);
		for ($i = 0; $i < $results->numRecords(); $i++) {
			$record = $results->nextRecord();
			$city    = $record['City'];
			$state   = $record['State'];
			$country = Geoip::countryCode2name($record['Country']);
			if(!empty($state))
				$value   = sprintf('%s, %s, %s',$city, $state, $country);
			else
				$value   = sprintf('%s, %s',$city, $country);

			array_push($locations, new SearchDTO($value,$value));
		}
		return new OpenStackImplementationNamesQueryResult($locations);
	}

} 