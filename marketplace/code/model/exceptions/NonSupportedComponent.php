<?php

/**
 * Class NonSupportedComponent
 */
class NonSupportedComponent extends Exception {

	public function __construct(IOpenStackRelease $release, IOpenStackComponent $component){
		$message = sprintf('Release %s does not support Component %s'
			, $release->getName()
			, $component->getCodeName());
		parent::__construct($message);
	}
}