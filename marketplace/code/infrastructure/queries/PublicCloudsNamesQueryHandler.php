<?php
/**
 * Class PublicCloudsNamesQueryHandler
 */
final class PublicCloudsNamesQueryHandler extends CloudsNamesQueryHandler  {
	/**
	 * @return string
	 */
	function getClassName(){
		return 'PublicCloudService';
	}
}