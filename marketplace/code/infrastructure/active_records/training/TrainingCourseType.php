<?php
/**
 * Class TrainingCourseType
 */
class TrainingCourseType
	extends DataObject
	implements ITrainingCourseType
{

    private  static $singular_name="Course Type";

	private static $db = array(
        'Type' => 'Text',
    );

	private static $summary_fields = array(
        'Type' => 'Course Type',
    );

    public function getCMSFields() {
        $fields = new FieldList();

        $fields->push(new LiteralField("Title","<h2>Training Course Type</h2>"));
        $fields->push(new TextField("Type","Type"));

        return $fields;
    }


    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator= new RequiredFields(array('Type'));
        return $validator;
    }

    function canCreate($member = null) {
        if (!Permission::check(ITraining::MarketPlacePermissionSlug)) {
            return false;
        }
        return true;
    }
    function canEdit($member = null) {
        if (!Permission::check(ITraining::MarketPlacePermissionSlug)) {
            return false;
        }
        return true;
    }

    function canDelete($member = null) {
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
	public function getType()
	{
		return $this->getField('Type');
	}

	/**
	 * @param string $type
	 * @return void
	 */
	public function setType($type)
	{
		$this->setField('Type',$type);
	}
}