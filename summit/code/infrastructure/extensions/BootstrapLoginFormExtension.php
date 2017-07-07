<?php


class BootstrapLoginFormExtension extends DataExtension {

	public function updateLoginForm() {		
		$this->owner->Actions()->first()->setStyle('success');
	}
}