<% require themedCSS(carousel) %>

 <div id="midpage-promo" class="span-24 last">
 	<div class="span-12">
 		<a href="/marketplace/"><strong>New!</strong> See The OpenStack Marketplace</a>
 	</div>
 	<div class="span-12 last">
 		<a href="http://superuser.openstack.org/"><strong>New!</strong> Check Out SuperUser Magazine</a>
 	</div>
 </div>


 <div class="opener span-24 last">
  	<h1>Open source software for building<br /> private and public clouds.</h1>  	
  	<div id="projects" class="span-8">
  		<h3 class="subhead">Software</h3>
  		  		<p>OpenStack Software delivers a massively scalable cloud operating system.</p>
  		
  		<p><a href="/software/"><img src="/themes/openstack/images/homepage/openstack-mini-homepage-diagram.png"/></a></p>

  	</div>
  	<div id="people" class="span-8">
  		<h3 class="subhead">Community</h3>
  		Join our global community of technologists, developers, researchers,  corporations and cloud computing experts.
  		<div class="clear"></div>
  		<div class="statBlock" id="members">
  			<p class="number">$MembersCount</p>
  			<p>People</p>
  		</div>
  		<div class="statBlock" id="countries">
  			<p class="number">$CountryCount</p>
  			<p>Countries</p>
  		</div>		
  		
  		
  	</div>

  	<div class="span-8 last">
	
	<div id="promo-area">
		<!-- TO BE SWITCHED BACK AT 5:30pm Eastern - 10/24/14 -->
		<!-- 
		<p class="promo-subhead">A major automaker is</p>
		<p class="promo-main">Turning Big Data in Huge Insights with OpenStack</p>
		<a class="promo-buttom" href="/enterprise/auto/">Read The Story</a> -->
		<p class="promo-subhead">Register by 5:30pm EDT, Oct 24</p>
		<p class="promo-main">Save $200 on the OpenStack Summit</p>
		<a class="promo-buttom" href="https://www.eventbrite.com/e/openstack-summit-november-2014-paris-tickets-12051477293?aff=summit11" target="_blank">Register now</a>
	</div>

	</div>
  	
  	
  </div>

<div class="span-8"><a href="/software/" class="roundedButton">About OpenStack Software...</a></div>
<div class="span-8"><a href="/community/" class="roundedButton">Meet Our Community</a></div>	
<div class="span-8 last"><div id="sliderPager"></div></div>


<div class="tabSet span-24 last">
    
<ul class="tabs">
	<% if ReturningVisitor %>
		<li class="active">
			<a href="#tabActivity">Latest Activity</a>
		</li>
		<li>
			<a href="#tabWhatIs">What is OpenStack?</a>
		</li>
	<% else %>
		<li>
			<a href="#tabActivity">Latest Activity</a>
		</li>
		<li class="active">
			<a href="#tabWhatIs">What is OpenStack?</a>
		</li>
	<% end_if %>
</ul>
    
	    	<div id="tabActivity" class="tabContent">
	    
			    <div class="feeds span-15">
				    <div id="openStackFeed">
				    	<% loop RssItems %>
				    			<div class="feedItem Web">
								<div class="span-14 prepend-1 last">
									<div class="itemContent">
										<a href="{$link}">$title <span class="itemTimeStamp">$pubDate</span></a>
									</div>
								</div>
							</div>
						<% end_loop %>
				    </div>
			    </div>
				
				<div class="events prepend-1 span-6 last"><!-- Events Container -->
				
					<% if UpcomingEvents %>
				
						<h2>Come See Us</h2>
						
						<% loop UpcomingEvents %>
						
						<p><strong>NEXT UP:</strong> <a href="$EventLink">{$Title}</a>, $formatDateRange in {$EventLocation}.</p>
						
						<% end_loop %>
					
					<% else %>
						
						<h2>Did you see us? We just attended...</h2>
						
							<% loop PastEvents %>
							
							<p><a href="$EventLink">{$Title}</a>, $formatDateRange in {$EventLocation}.</p>
							
							<% end_loop %>
					
					<% end_if %>
					
									
					
					
					<a href="/events/" class="roundedButton">More Events...</a>
				
				</div><!-- Events Container -->
				
		    
		    
		    </div><!-- tabActivity -->
		    
	    	<div id="tabWhatIs" class="tabContent"><!-- tabWhatIs -->
	    	
	    		<h2 class="prepend-1">OpenStack: The 5-minute Overview</h2>
	    				    <div class="overview span-10 prepend-1"><!-- overview -->		    
	    				    <p class="point"><strong>OpenStack</strong> OpenStack is a global collaboration of developers and cloud computing technologists producing the ubiquitous open source cloud computing platform for public and private clouds. The project aims to deliver solutions for all types of clouds by being simple to implement, massively scalable, and feature rich.  The technology consists of a series of <a href="/projects/">interrelated projects</a> delivering various components for a cloud infrastructure solution.
	    				    </p>
	    				    
	    				    
	    				    <p class="point"><strong>Who's behind OpenStack?</strong> Founded by Rackspace Hosting and NASA, OpenStack has grown to be a <a href="/community/">global software community</a> of developers collaborating on a standard and massively scalable open source cloud operating system. Our mission is to enable any organization to create and offer cloud computing services running on standard hardware.</p>
	    				    
	    					 </div>
	    				    
	    				    <div class="overview span-10 prepend-1"><!-- overview -->
	    				    
	    				    <p class="point"><strong>Who uses OpenStack?</strong> Corporations, service providers,  VARS, SMBs, researchers, and global data centers looking to deploy large-scale cloud deployments for private or public clouds leveraging the support and resulting technology of a global open source community.</p>
	    				    	    				    		    
	    				    <p class="point"><strong>Why open matters:</strong> All of the code for OpenStack is freely available under the Apache 2.0 license. Anyone can run it, build on it, or submit changes back to the project. We strongly believe that an open development model is the only way to foster badly-needed cloud standards, remove the fear of proprietary lock-in for cloud customers, and create a large ecosystem that spans cloud providers.</p>
	    				    
	    				    <p class="point">For more information, visit the <a href="/projects/openstack-faq/">OpenStack Community Q&amp;A</a>.</p>
	    				    </div><!-- overview -->
	    	
	    	</div><!-- tabWhatIs -->
    
    	<p class="clear"></p>
</div><!-- tabSet -->