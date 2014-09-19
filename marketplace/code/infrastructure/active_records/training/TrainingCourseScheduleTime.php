<?php

/**
 * Class TrainingCourseScheduleTime
 * @active_record
 */
class TrainingCourseScheduleTime
	extends DataObject
	implements IScheduleTime {

    static $db = array(
        'StartDate' => 'Date',
        'EndDate'   => 'Date',
        'Link'      => 'Text'
    );

    function getCMSValidator()
    {
       return $this->getValidator();
    }

    function getValidator()
    {
        $validator= new RequiredFields(array('StartDate','EndDate','Link'));
        return $validator;
    }

    static $has_one = array(
        'Location' => 'TrainingCourseSchedule',
    );

    public function getCMSFields() {
        $fields = new FieldList();

        $fields->push(new LiteralField("Title","<h2>Schedule Time</h2>"));

        $start_date = new JQueryUIDatePickerField("StartDate","Start Date","",null,"DataObjectManager_Popup_AddForm_EndDate");
        $fields->push($start_date);

        $end_date = new JQueryUIDatePickerField("EndDate","End Date");
        $fields->push($end_date);

        $fields->push(new TextField("Link","Link"));

        $fields->push(new HiddenField("LocationID","Location"));

        return $fields;
    }

    function canCreate($member=null) {
        if (!Permission::check(ITraining::MarketPlacePermissionSlug)) {
            return false;
        }
        return true;
    }
    function canEdit($member=null) {
        if (!Permission::check(ITraining::MarketPlacePermissionSlug)) {
            return false;
        }
        return true;
    }

    function canDelete($member=null) {
        if (!Permission::check(ITraining::MarketPlacePermissionSlug)) {
            return false;
        }
        return true;
    }

	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

	/**
	 * @return string
	 */
	public function getStartDate()
	{
		return $this->getField('StartDate');
	}

	/**
	 * @param string $start_date
	 * @return void
	 */
	public function setStartDate($start_date)
	{
		$this->setField('StartDate',$start_date);
	}

	/**
	 * @return string
	 */
	public function getEndDate()
	{
		return $this->getField('EndDate');
	}

	/**
	 * @param string $end_date
	 * @return void
	 */
	public function setEndDate($end_date)
	{
		$this->setField('EndDate',$end_date);
	}

	/**
	 * @return string
	 */
	public function getLink()
	{
		return $this->getField('Link');
	}

	/**
	 * @param string $link
	 * @return void
	 */
	public function setLink($link)
	{
		$this->setField('Link',$link);
	}


	/**
	 * @return ICourseLocation
	 */
	public function getAssociatedLocation()
	{
		return AssociationFactory::getInstance()->getMany2OneAssociation($this,'Location','Times')->getTarget();
	}

	/**
	 * @param ICourseLocation $location
	 * @return void
	 */
	public function setAssociatedLocation(ICourseLocation $location)
	{
		AssociationFactory::getInstance()->getMany2OneAssociation($this,'Location','Times')->setTarget($location);
	}
}