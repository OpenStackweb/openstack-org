<?php

/*
 *  The SchedEvent object is designed to be replaced / overwritten on each import from Sched.
 *  It should never contain fields that don't map 1:1 to what's available from the Sched API.
 *
 *  This object extends SchedEvent to include OpenStack-specific metadata for our site.
 *  It's stored here in a companion object to prevent it from being destroyed on each import.
 *  The foreign key linking the two is event_key (set for each event by Sched & guaranteed to be consistent)
 *
 */
	
class SchedEventMetadata extends DataObject {

	static $db = array(
		'event_key' => 'Varchar',
		'BeenEmailed' => 'Boolean',
		'YouTubeVideoID' => 'Varchar',
		'HostedMediaURL' => 'Text',
		'MediaType' => "Enum('URL, File')"
	);

	static $has_one = array(
		'UploadedMedia' => 'File'
	);

	static $singular_name = 'EventMetadata';
	static $plural_name = 'EventMetadata';
	
}