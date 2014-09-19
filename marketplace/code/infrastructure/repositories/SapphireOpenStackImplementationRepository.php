<?php
/**
 * Class SapphireOpenStackImplementationRepository
 */
abstract class SapphireOpenStackImplementationRepository
	extends SapphireRegionalSupportedCompanyServiceRepository
	implements IOpenStackImplementationRepository
{

	public function getWithCapabilitiesEnabled(QueryObject $query, $offset = 0, $limit = 10){
		$class = $this->entity_class;
		$do    = $class::get()->where(" EXISTS( SELECT ID FROM OpenStackImplementationApiCoverage C WHERE ImplementationID = CompanyService.ID) ")->order($query->getOrder())->limit($limit,$offset);
		if(is_null($do)) return array(array(),0);
		$res    = $do->toArray();
		return array($res, (int) $do->count());
	}

	public function delete(IEntity $entity){
		$entity->clearCapabilities();
		$entity->clearHypervisors();
		$entity->clearGuests();
		parent::delete($entity);
	}
}
