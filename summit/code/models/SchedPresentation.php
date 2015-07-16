<?php


class SchedPresentation extends DataObject {


	private static $db = array (
		'Title' => 'Varchar(255)',
		'Description' => 'HTMLText',
		'EventEnd' => 'SS_Datetime',
		'EventStart' => 'SS_Datetime',
		'EventKey' => 'Varchar',
		'EventType' => 'Varchar',
		'Goers' => 'Int',
		'SchedID' => 'Varchar',
		'InviteOnly' => 'Boolean',
		'Seats' => 'Int',
		'Speakers' => 'Text',
		'Venue' => 'Varchar',
		'VenueID' => 'Int',
		'YouTubeID' => 'Varchar',
		'DisplayOnSite' => 'Boolean'
	);


    private static $has_one = array (      
        'Category' => 'PresentationCategory',
        'Summit' => 'Summit',
        'VideoThumbnail' => 'Image',
        'RelatedMedia' => 'File'
    );	


    private static $many_many = array (
    	'Tags' => 'Tag',
    	'PresentationSpeakers' => 'PresentationSpeaker'
    );

	private static $indexes = array (
		'SchedID' => true
	);


	private static $summary_fields = array (
		'Title' => 'Title'
	);


	public static function get_by_sched_id($id) {
		return self::get()->filter('SchedID', $id)->first();
	}


	public function getCMSFields() {
		$fields = FieldList::create(TabSet::create('Root'));
		
		if($this->SchedID) {
			return $fields
				->tab('SchedData')
					->readonly('Title')
					->readonly('Description')
					->readonly('EventStart')
					->readonly('EventEnd')
					->readonly('EventKey')
					->readonly('EventType')
					->readonly('Goers')
					->readonly('SchedID')
					->readonly('InviteOnly')
					->readonly('Seats')
					->readonly('Venue')
					->readonly('VenueID')
				->tab('EditableData')
					->checkbox('DisplayOnSite')				
					->text('YouTubeID')
					->dropdown('CategoryID','Category', PresentationCategory::get()->map('ID','Title'))
					->listbox('PresentationSpeakers')
						->configure()
							->setSource(PresentationSpeaker::get()->map('ID','Title')->toArray())
							->setMultiple(true)
						->end()
					
					->imageUpload('VideoThumbnail')
					->upload('RelatedMedia')
					->tag('Tags','Tags', Tag::get(), $this->Tags())
			;
		}

		else {
			return $fields	
				->text('Title')
				->dropdown('CategoryID','Category', PresentationCategory::get()->map('ID','Title'))
				->tag('Tags','Tags', Tag::get(), $this->Tags())
				->date('EventStart')
					->configure()
						->setConfig('showcalendar', true)
					->end()
				->date('EventEnd')
					->configure()
						->setConfig('showcalendar', true)
					->end()

				->htmlEditor('Description')

				->checkbox('InviteOnly')
				->numeric('Seats')
				->text('Venue')				
				->text('YouTubeID')
				->imageUpload('VideoThumbnail')
				->upload('RelatedMedia')
				->checkbox('DisplayOnSite')				
			;			
		}
	}



	public function Link() {
		if($page = PresentationVideoPage::get()->first()) {
			return $page->Link('show/'.$this->Slug);
		}
	}

	public function onBeforeWrite() {
		parent::onBeforeWrite();
		if($this->isChanged('YouTubeID') && $this->YouTubeID) {
	
			// if($this->VideoThumbnail()->exists()) {
			// 	$this->VideoThumbnail()->delete();
			// }

			$img = @file_get_contents("http://img.youtube.com/vi/{$this->YouTubeID}/0.jpg");
			if($img) {

				$folder = Folder::find_or_make('video-thumbnails');
				$path = $folder->Filename."presentation-{$this->ID}-".uniqid().".jpg";

				$fh = fopen(Director::baseFolder()."/".$path, 'wb');
				fwrite($fh, $img);
				fclose($fh);

				$image = Image::create(array(
					'Filename' => $path,
					'Name' => "presentation-{$this->ID}.jpg",
					'Title' => "presentation-{$this->ID}"
				));

				$image->write();
				$this->VideoThumbnailID = $image->ID;
			}
		}
	}


	public function canEdit($member = null) {
		return Permission::check("CMS_ACCESS_CMSMain");
	}
	
	public function canCreate($member = null) { 
		return Permission::check("CMS_ACCESS_CMSMain");
	}
	
	public function canView($member = null) { 
		return true; 
	}

	public function canDelete($member = null) {
		return Permission::check("CMS_ACCESS_CMSMain");
	}

}