<?php
/**
 * Class TrainingCoursePrerequisite
 */
class TrainingCoursePrerequisite
	extends DataObject
	implements ITrainingCoursePrerequisite
{

    public static $singular_name="Course Prerequisite";

    static $db = array(
        'Name' => 'Text',
    );

    public static $belongs_many_many = array (
        'TrainingCourse' => 'TrainingCourse',
    );

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator= new RequiredFields(array('Name'));
        return $validator;
    }

    public function getCMSFields() {
        $fields = new FieldList();
        $fields->push(new LiteralField("Course Prerequisite","<h2>Course Prerequisite</h2>"));
        $fields->push(new TextField("Name","Name"));
        return $fields;
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
	public function getName()
	{
		return $this->getField('Name');
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name)
	{
		$this->setField('Name',$name);
	}
}