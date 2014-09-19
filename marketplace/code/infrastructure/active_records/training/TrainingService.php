<?php

/**
 * Class TrainingService
 * @active_record
 * @remark: replaces Training Program
 */
class TrainingService
	extends CompanyService
	implements ITraining {

	protected function onBeforeWrite() {
		$this->setMarketplace($this->getDefaultMarketPlaceType());
		parent::onBeforeWrite();
	}
	/**
	 * @return IMarketPlaceType
	 */
	protected function getDefaultMarketPlaceType(){
		$name = ITraining::MarketPlaceType;
		return MarketPlaceType::get()->filter('Name',$name)->first();
	}

	static $has_many = array(
		"Courses" => 'TrainingCourse'
	);


	//Fields to show in ModelAdmin table
	static $summary_fields = array(
		'Name' => 'Name',
		'RawDescription' => 'Description',
	);

	static $casting = array(
		"Description" => 'Text',
	);

	function getRawDescription(){
		return $this->RAW_val('Description');
	}

	protected function getMarketPlaceTypeName(){
		return ITraining::MarketPlaceType;
	}

	//model implementation

	public function getCourses(){
		return $this->getComponents('Courses');
	}

	public function getName(){
		return $this->getField('Name');
	}

	public function setName($name)
	{
		$this->setField('Name',trim( $name));
	}

	public function getDescription()
	{
		$res = $this->getField('Overview');
		return $res;
	}

	public function setDescription($description)
	{
		$this->setField('Overview', $description);
	}

	public function isActive()
	{
		return (bool)$this->getField('Active');
	}

	public function activate()
	{
		$this->setField('Active', true);
	}

	public function deactivate()
	{
		$this->setField('Active', false);
	}

	/**
	 * @return ICourse[]
	 */
	public function getAssociatedCourses()
	{
		return AssociationFactory::getInstance()->getOne2ManyAssociation($this,'Courses')->toArray();
	}

	/**
	 * @param ICourse $course
	 * @return void
	 */
	public function addAssociatedCourse(ICourse $course)
	{
		AssociationFactory::getInstance()->getOne2ManyAssociation($this,'Courses')->add($course);
	}

	/**
	 * @return void
	 */
	public function clearAssociatedCourses()
	{
		AssociationFactory::getInstance()->getOne2ManyAssociation($this,'Courses')->removeAll();
	}
}