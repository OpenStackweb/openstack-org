<?php
 
// Include SimplePie
// Located in the parent directory
include_once('SimplePieAutoloader.php');
include_once('idn/idna_convert.class.php');
 
// We'll process this feed with all of the default options.
$feed = new SimplePie();
 
// Set which feed to process.
$feed->set_feed_url('http://pipes.yahoo.com/pipes/pipe.run?_id=7479b77882a68cdf5a7143374b51cf30&_render=rss');
 
// Run SimplePie.
$feed->init();
 
// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
$feed->handle_content_type();
 
// Let's begin our XHTML webpage code.  The DOCTYPE is supposed to be the very first thing, so we'll keep it on the same line as the closing-PHP tag.
?>
	<?php
	/*
	Here, we'll loop through all of the items in the feed, and $item represents the current item in the loop.
	*/
	foreach ($feed->get_items() as $item):
	
		$url = $item->get_permalink();
		
		if (strpos($url,'twitter')) {
			$source = 'twitter';
		} else if (strpos($url,'openstack.org/blog')) {
			$source = 'blog';
		} else {
			$source = 'web';
		}
	
	?>
 
			<div class="feedItem Web">
				<div class="span-3">
					<div class="itemIcon <?php echo $source; ?>"><strong><?php echo $source; ?></strong></div>
				</div>
				<div class="span-12 last">
					<div class="itemContent">
						<a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?> <span class="itemTimeStamp"><?php echo $item->get_date('j F Y g:i a'); ?></span></a>
					</div>
				</div>
			</div>
 
	<?php endforeach; ?>