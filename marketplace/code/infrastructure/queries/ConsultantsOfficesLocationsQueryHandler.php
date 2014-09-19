<?php
/**
 * Class ConsultantsOfficesLocationsQueryHandler
 */
final class ConsultantsOfficesLocationsQueryHandler implements IConsultantsOfficesLocationsQueryHandler {
	/**
	 * @param IQuerySpecification $specification
	 * @return IQueryResult
	 */
	public function handle(IQuerySpecification $specification)
	{
		$params = $specification->getSpecificationParams();
		$term   = @$params['name_pattern'];
		$filter = '';
		if(!empty($term)){
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
			$filter = "WHERE City LIKE '%{$term}%' {$country_filter}";
		}

		$locations = array();
		$sql    = <<< SQL
        SELECT City,State,Country FROM Office
		{$filter}
		GROUP BY City,State,Country

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