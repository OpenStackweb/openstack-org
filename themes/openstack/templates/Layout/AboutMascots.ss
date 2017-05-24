</div>
    <!-- Begin Page Content -->
    <div class="intro-header mascots-hero">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-push-2">
                    <div class="intro-message">
                        <h1>A great project deserves a great mascot</h1>
                        <h4>Get your OpenStack project logos here</h4>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container -->
    </div>
    <div class="container">
    	<div class="row">
    		<div class="col-sm-10 col-sm-push-1 center">
				<p>We are OpenStack. We’re also passionately developing more than 60 projects within OpenStack. To support each project’s unique identity and visually demonstrate our cohesive, connected community, we’ve created project logos to help us promote projects and their benefits.</p>
				<p>Project teams first selected a mascot that best represents their project’s capabilities and personality. Then a team of professional illustrators worked to create a family of logos that are unique to each team, yet immediately identifiable as part of OpenStack. The logos are used to promote projects on the OpenStack website, at the Summit and in marketing materials.
				<p>The value of creating this family of logos is communicating that OpenStack projects work together—we’re part of a larger team. When this project began, less than one-third of projects had logos, none looked like they were part of the same family, and some needed professional tuning. Now, as a whole, they’re a vibrant way to represent our projects over 15 releases of OpenStack software. 
				</p>
		    </div>
    	</div>
    </div>
    <div class="mascots-intro">
    	<div class="container">
			<h2>
				Watch how the logos were created
			</h2>
    		<div class="col-lg-4 col-md-6 col-sm-6">
			<p>See all of the OpenStack logos <em>(1 minute)</em></p>
			<iframe width="350" height="197" src="https://www.youtube.com/embed/wO1R8TZR_Lc?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
    		</div>
    		<div class="col-lg-4 col-md-6 col-sm-6">
			<p>See the mascots in development <em>(1 minute)</em></p>
			<iframe width="350" height="197" src="https://www.youtube.com/embed/JmMTCWyY8Y4?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
    		</div>
    		<div class="col-lg-4 col-md-6 col-sm-6">
			<p>Hear from the project and creative leads <em>(8 minutes)</em></p>
			<iframe width="350" height="197" src="https://www.youtube.com/embed/LOdsuNr2T-o?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
    		</div>
    	</div>
    </div>
    <div class="container">
    	<div class="col-sm-12">
			<h5 class="section-title">Download your project’s logo package</h5>
			<ul>
			    <% loop $Mascots() %>
			        <li class="col-lg-6 col-md-6 col-sm-6">
			            <% if $Name && $MascotFiles %>
                            <a href="#" data-toggle="modal" data-target="#mascots_modal" data-component="{$CodeNameString}" data-images="{$MascotFiles}">
                                <strong>$CodeNameString:</strong> $Name
                            </a>
                        <% else %>
                            <strong>$CodeNameString:</strong> <% if $Name %> $Name (in progress) <% else %> (not chosen) <% end_if %>
                        <% end_if %>
			        </li>
			    <% end_loop %>
			</ul>
		</div>
		<div class="col-lg-10 col-sm-12 center">
			<p>&nbsp;</p>
			<p><a href="//www.openstack.org/assets/software/mascots/OS-Mascot-Key.pdf" target="_blank">View Project Mascot Key</a></p>
		</div>
    	<div class="col-sm-12">
			<h5 class="section-title">FAQs - Using Your Mascot Logo</h5>
			<p class="question">
				What kind of options will my logo have?
			</p>
			<p class="answer">
				Teams receive 10 versions of logo files (including JPG, PNG, and EPS vector files) as well as versions for horizontal and square orientation, a mascot-only version, and a one-color version (black and white). If you find these don’t meet your needs, please contact Heidi Joy Tretheway.
			</p>
			<p class="question">
				Can I change my new logo? How does licensing work?
			</p>
			<p class="answer">
				Logos are licensed under the Creative Commons CC-BY-ND, similar to other Foundation materials. That means you can use them freely, but can’t make derivative work, such as adding a for-profit company’s logo into your community project’s logo.
			</p> 
			<p class="question">
				Can I still use my old logo?
			</p>
			<p class="answer">
				Yes. Feel free to print vintage swag, such as stickers and shirts. For official channels, such as the website, project navigator, and signage at OpenStack events, we’ll use official logos exclusively.
			</p>
			<h5 class="section-title">FAQs - Selecting Your Mascot</h5>
			<p class="question">
				Who can get a mascot?
			</p>
			<p class="answer">
				Any OpenStack project that has been <a href="https://governance.openstack.org/reference/projects/" target="_blank">approved by the Technical Committee</a> may request a mascot logo. 
			</p>
			<p class="question">
				How should we select our mascot?
			</p>
			<p class="answer">
				Consider the qualities and characteristics of your project. For example, if you work on Keystone and decide that "safe" and "smart" are its two key attributes, a German Shepherd dog might be a great animal to express those characteristics. Similarly, if Swift is defined by "flexible object storage," a squirrel might be a great representation because it spends much of its life storing and retrieving nuts.
			</p>
			<p class="question">
				What if two projects want the same animal?
			</p>
			<p class="answer">
				Mascots are assigned are on a first-come, first-served basis. For example, if a cat has already been selected by one project, then another project interested in a cat-like mascot should choose a significantly different type of cat, such as a leopard or tiger.
			</p>
			<p class="question">
				What if my project already has a logo?
			</p>
			<p class="answer">
				We’ve reached out to your Project Team Leaders already to alert them and discuss your options. For a handful of projects with existing mascots from the natural world, you’ll have priority to keep these animals, and our illustrator will restyle them for consistency across projects. For projects that have a logo or a graphic that shows a human or human-made object, we recommend choosing a mascot from the natural world so that we can create a logo for you that can be featured alongside the other project logos.

			</p>
			<p class="question">
				What if our project doesn't want to use an animal or natural feature?
			</p>
			<p class="answer">
				We've limited project mascots to real creatures and natural features because we believe they are generally globally recognizable and offer rich flexibility for artistic interpretation. We also note that animals are often personified by characteristics and personality traits, which can help connect to the themes and functionality of the project.
			</p>
			<p class="question">
				How generic/specific should my mascot be? 
			</p>
			<p class="answer">
				Projects could select multiple breeds/species within a category if they are significantly different, such one project using a Chihuahua and another using a German Shepherd, or one project selecting a clownfish and another selecting a shark. Mascots should be specific enough for reasonably consistent identification. 
			</p>
			<p class="question">
				Can my mascot be an imaginary animal/feature?
			</p>
			<p class="answer">
				No. (Dragons, unicorns, centaurs, etc., are excluded.)
			</p>
			<p class="question">
				Can my mascot be a human, part of a human (such as a hand), or a human-made object?
			</p>
			<p class="answer">
				No. Only things from the natural world, such as animals, plants, fish, bugs, and natural features, may be chosen as a mascot.
			</p>
			<p class="question">
				Can we draw our own mascot?
			</p>
			<p class="answer">
				No. Project teams select their own mascot, but for the purposes of harmony across all projects, a single illustration style will be used. It will be executed by a designer under direction from the foundation staff.
			</p>
			<p class="question">
				We picked a few good mascot candidates. Now what?
			</p>
			<p class="answer">
				Send your candidates to <a href="mailto:heidijoy@openstack.org">Heidi Joy Tretheway</a> by July 27 for priority consideration. She’ll work with project leaders to avoid duplicates across projects and answer questions. Once she works with the project branding team to confirm your candidates, your team can vote to select your final mascot.
			</p>
			<p class="question">
				What if my mascot candidate is disqualified?
			</p>
			<p class="answer">
				The OpenStack Foundation vets the proposed candidates to ensure uniqueness from other projects and from other companies in the ecosystem, and to abide by the other requirements listed here. If one of your choices is disqualified, we can discuss other options or make recommendations, and work with you to ensure your team selects a mascot they’re proud of.
			</p>
			<p class="question">
				How do we organize voting?
			</p>
			<p class="answer">
				Depending on the size of your project and the number of contributors interested in giving their opinion, you might want to do it informally at a meeting (+1 on an etherpad list, for example), or more formally using a polling or survey tool (such as Google Forms). <a href="mailto:heidijoy@openstack.org">Contact Heidi Joy</a> at the foundation if you need help.
			</p>
			<p class="question">
				Whom should I contact with more questions?
			</p>
			<p class="answer">
				Ask <a href="mailto:heidijoy@openstack.org">Heidi Joy Tretheway</a> from the OpenStack Foundation, who is the liaison for the project teams in this branding effort.
			</p>
    	</div>
    </div>

    <div class="modal fade" id="mascots_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
