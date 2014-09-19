<?php
class TrainingCourseLevel
	extends DataObject
	implements ITrainingCourseLevel
{

    private static $singular_name="Course Level";

	private static $db = array(
        'Level' => 'Text',
    );

	private static $summary_fields = array(
        'Level' => 'Course Level',
    );

    public function getLowerLevel(){
        return strtolower($this->Level);
    }

    public function getCMSFields() {
        $fields = new FieldList();
        $fields->push(new LiteralField("Title","<h2>Training Course Level</h2>"));
        $fields->push(new TextField("Level","Level"));
        return $fields;
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator= new RequiredFields(array('Level'));
        return $validator;
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
	 * @param string $level
	 * @return void
	 */
	public function setLevel($level)
	{
		$this->setField('Level',$level);
	}

	/**
	 * @return string
	 */
	public function getLevel()
	{
		return $this->getField('Level');
	}
}