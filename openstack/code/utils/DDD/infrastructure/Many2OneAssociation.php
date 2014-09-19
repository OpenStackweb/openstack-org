<?php

/**
 * Class Many2OneAssociation
 */
final class Many2OneAssociation {

	/**
	 * @var DataObject
	 */
	private $owner;
	/**
	 * @var DataObject
	 */
	private $target;

	/**
	 * @var array|string
	 */
	private $target_class;
	/**
	 * @var DataObject
	 */
	private $snapshot;
	/**
	 * @var string
	 */
	private $association_name;
	/**
	 * @var bool
	 */
	private $dirty;

	/**
	 * @var string
	 */
	private $inversed_by;

	private $target_query;

	/**
	 * @param DataObject $owner
	 * @param string $association_name
	 * @param string $inversed_by
	 */
	public function __construct(DataObject $owner, $association_name, $inversed_by = null, QueryObject $target_query = null){
		$this->owner            = $owner;
		$this->association_name = $association_name;
		$this->inversed_by      = $inversed_by;
		$this->target_class     = $this->owner->has_one($association_name);
		$this->snapshot         = $this->owner->getComponent($this->association_name);
		$this->target           = $this->snapshot;
		if(is_null($target_query)) $target_query = new QueryObject;
		$this->target_query     = $target_query;
		UnitOfWork::getInstance()->loadMany2OneAssociation($this);
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->association_name;
	}

	/**
	 * @return DataObject
	 */
	public function getOwner(){
		return $this->owner;
	}

	/**
	 * @return array|string
	 */
	public function getTargetClass(){
		return $this->target_class;
	}

	/**
	 * @param DataObject $target
	 */
	public function setTarget(DataObject $target){
		$this->target = $target;
		$this->dirty  = true;
	}

	/**
	 * @return DataObject
	 */
	public function getTarget(){
		return $this->target;
	}

	/**
	 * @return bool
	 */
	public function isDirty(){
		return $this->dirty;
	}

	/**
	 * @return bool
	 */
	public function isInverseSide(){
		return !empty($this->inversed_by);
	}

	public function getInversedBy(){
		return $this->inversed_by;
	}

	public function getTargetCriteria(){
		return $this->target_query;
	}
}