<?php
 
// Include SimplePie
// Located in the parent directory
include_once('SimplePieAutoloader.php');
include_once('idn/idna_convert.class.php');


function GetContentBetween($StringToParse,$StartMarker,$EndMarker) {

	// Look to see if our beginning marker is in the string	
	$StartMarkerPosition = strpos($StringToParse, $StartMarker);
	// Look to see if our end marker is in the string	
	$EndMarkerPosition = strpos($StringToParse, $EndMarker);
	
	// If we found both markers, proceed to truncate the string to just the content
	// between the markers
	if ($StartMarkerPosition === false && $EndMarkerPosition === false) {
	
		$Results = false;
	
	} else {
		
		$StartMarkerPosition = $StartMarkerPosition + strlen($StartMarker);
		$EndMarkerPosition = $EndMarkerPosition - $StartMarkerPosition;
		$Results = substr($StringToParse,$StartMarkerPosition,$EndMarkerPosition);
	}
	
	return $Results;

}

 
// We'll process this feed with all of the default options.
$feed = new SimplePie();
 
// Set which feed to process.
$feed->set_feed_url('feed://feeds.launchpad.net/openstack/revisions.atom');
 
// Run SimplePie.
$feed->init();
 
// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
$feed->handle_content_type();
 
?>

<ul id="developerActivity">

	<?php
	/*
	Here, we'll loop through all of the items in the feed, and $item represents the current item in the loop.
	*/
	foreach ($feed->get_items() as $item):
	
		$feedConent = "";
		$feedContent = $item->get_content();
		$feedContent = strip_tags($feedContent, '<a>');
		
		// Fix a bug in the Launchpad RSS that malforms links to users
		$feedContent = str_replace('https://launchpad.nethttps:0//launchpad.net/','http://launchpad.net/',$feedContent);
		
	?>
 			
			<li><?php echo GetContentBetween($feedContent,"Author:","Date:"); ?>recently made a contribution to <?php echo GetContentBetween($feedContent,"Project:","Log message:"); ?> <span>r<?php echo GetContentBetween($feedContent,"Revno:","Project:"); ?></span></li>
 
	<?php endforeach; ?>
	
</ul>