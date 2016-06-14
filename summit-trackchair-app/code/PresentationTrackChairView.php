<?php

class PresentationTrackChairView extends DataObject
{
	
	private static $has_one = [
		'TrackChair' => 'Member',
		'Presentation' => 'Presentation'
	];

}