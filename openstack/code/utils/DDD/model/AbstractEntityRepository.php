<?php
/**
 * Class AbstractEntityRepository
 */
abstract class AbstractEntityRepository implements IEntityRepository {

	/**
	 * @var string
	 */
	protected $entity_class;

	/**
	 * @param IEntity $entity
	 */
	public function __construct(IEntity $entity){
		if($entity instanceof DataExtension)
			$this->entity_class = get_class($entity->getOwner());
		else
			$this->entity_class = get_class($entity);
	}
} 