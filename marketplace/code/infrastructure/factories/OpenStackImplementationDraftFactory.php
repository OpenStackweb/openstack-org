<?php

abstract class OpenStackImplementationDraftFactory
	extends RegionalSupportedCompanyServiceDraftFactory
	implements IOpenStackImplementationFactory {
    /**
     * @param int                      $coverage_percent
     * @param IReleaseSupportedApiVersion $release_supported_api_version
     * @param IOpenStackImplementation $implementation
     * @return IOpenStackImplementationApiCoverage
     */
    public function buildCapability($coverage_percent, IReleaseSupportedApiVersion $release_supported_api_version, IOpenStackImplementation $implementation)
    {
        $capability = new OpenStackImplementationApiCoverageDraft;
        $capability->setCoveragePercent($coverage_percent);
        $capability->setReleaseSupportedApiVersion($release_supported_api_version);
        $capability->setImplementation($implementation);
        return $capability;
    }

    /**
     * @param IOpenStackImplementation $implementation
     * @param                          $data
     */
    public function buildOpenStackPowered(IOpenStackImplementation $implementation, $data)
    {
        $implementation->setCompatibleWithCompute($data['compatible_compute']);
        $implementation->setCompatibleWithStorage($data['compatible_storage']);
        $implementation->setCompatibleWithFederatedIdentity($data['compatible_federated_identity']);
        if(isset($data['interop_program_version_id'])) {
            $program_version = new InteropProgramVersion();
            $program_version->ID = (int)$data['interop_program_version_id'];
            $implementation->setProgramVersion($program_version);
        }
    }


} 