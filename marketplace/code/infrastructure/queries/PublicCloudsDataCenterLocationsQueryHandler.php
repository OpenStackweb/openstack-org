<?php
/***
 * Class PublicCloudsDataCenterLocationsQueryHandler
 */
final class PublicCloudsDataCenterLocationsQueryHandler
	extends CloudsDataCenterLocationsQueryHandler {

	/**
	 * @return string
	 */
	function getClassName(){
		return 'PublicCloudService';
	}
}