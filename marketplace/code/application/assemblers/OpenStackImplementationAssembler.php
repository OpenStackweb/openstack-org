<?php

/**
 * Class OpenStackImplementationAssembler
 */
final class OpenStackImplementationAssembler {

	/**
	 * @param IOpenStackImplementation $implementation
	 * @return array
	 */
	public static function convertOpenStackImplementationToArray(IOpenStackImplementation $implementation){
		$res = RegionalSupportedCompanyServiceAssembler::convertRegionalSupportedCompanyServiceToArray($implementation);
		//capabilities
		$capabilities = array();
		foreach($implementation->getCapabilities() as $capability){
			array_push($capabilities,OpenStackImplementationAssembler::convertCapabilityToArray($capability));
		}
		$res['capabilities'] = $capabilities;
			//hypervisors
		$hypervisors = array();
		foreach($implementation->getHyperVisors() as $hypervisor){
			array_push($hypervisors,$hypervisor->getIdentifier());
		}
		$res['hypervisors'] = $hypervisors;
		//os guests
		$guest_os = array();
		foreach($implementation->getGuests() as $guest){
			array_push($guest_os,$guest->getIdentifier());
		}
		$res['guest_os'] = $guest_os;
		return $res;
	}

	/**
	 * @param IOpenStackImplementationApiCoverage $capability
	 * @return array
	 */
	public static function convertCapabilityToArray(IOpenStackImplementationApiCoverage $capability){
		$res                        = array();
		$res['id']                  = $capability->getIdentifier();
		$res['component_id']        = $capability->getReleaseSupportedApiVersion()->getOpenStackComponent()->getIdentifier();
		$res['supports_versioning'] = $capability->getReleaseSupportedApiVersion()->getOpenStackComponent()->getSupportsVersioning();
		$res['release_id']          = $capability->getReleaseSupportedApiVersion()->getRelease()->getIdentifier();
		$res['version_id']          = $capability->getReleaseSupportedApiVersion()->getApiVersion()->getIdentifier();
		$res['version_name']        = $capability->getReleaseSupportedApiVersion()->getApiVersion()->getVersion();
		$res['coverage']            = $capability->getCoveragePercent();
		return $res;
	}

} 