<?php
/**
 * Class NonSupportedApiVersion
 */
class NonSupportedApiVersion extends Exception {

	public function __construct(IOpenStackRelease $release, IOpenStackApiVersion $version){
		$message = sprintf('Release %s does not support Api Version %s'
			, $release->getName()
			, $version->getVersion());
		parent::__construct($message);
	}
}