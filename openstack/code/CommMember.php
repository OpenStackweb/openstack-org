<?php
class CommMember extends DataObject
{
	static $db = array (
		'Name' => 'Varchar(255)',
		'Description' => 'Text'
	);
	
	static $has_one = array (
		'CommPage' => 'CommPage',
		'Photo' => 'Image'
	);
	
	//Fields to show in the DOM
	static $summary_fields = array(
		'Thumb' => 'Photo',  
		'Name' => 'Name'
	);
	
	public function getCMSFields()
	{
		$photo = new CustomUploadField('Photo', 'Photo');
		$photo->setFolderName('Uploads/community-photos/');
		$photo->setAllowedFileCategories('image');
		return new FieldList(
			new TextField('Name'),
			new TextareaField('Description', 'Description'),
			$photo
		);
	}

	//Needed for sidebar to work
	function canView($member = null)
	{
		return true;
	}

	//Return the Name as a menu title
	public function MenuTitle(){
		return $this->Name;
	}
	
	//Generate our thumbnail for the DOM
	public function getThumb()
	{
		if($this->PhotoID && $this->Photo())
			return $this->Photo()->CMSThumbnail();
		else	
			return '(No Image)';
	}
	
	//Return the link to view this staff member
	public function Link()
	{
		if($CommPage = $this->CommPage())
		{
			return $CommPage->Link('show/') . $this->ID;	
		}
	}
	
	public function LinkingMode()
	{
		//Check that we have a controller to work with and that it is a StaffPage
		if($Controller = Controller::CurrentPage() && Controller::CurrentPage()->ClassName == 'CommPage')
		{
			//check that the action is 'show' and that we have a StaffMember to work with
			if(Controller::CurrentPage()->getAction() == 'show' && $CommMember = Controller::CurrentPage()->getCommMember())
			{
				//If the current StaffMember is the same as this return 'current' class
				return ($CommMember->ID == $this->ID) ? 'current' : 'link';
			}
		}
	}
}