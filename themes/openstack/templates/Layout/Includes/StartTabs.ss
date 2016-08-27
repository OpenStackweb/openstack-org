<% require themedCSS(starttabs) %>	
<% require themedCSS(start) %>

	<h1><%t Openstack.HOW_TO_GET_STARTED 'How To Get Started With OpenStack' %></h1>

	$StartOverview

	<ul class="start-tabs">		
 	<% loop Menu(3) %>
  		<li class="$LinkingMode"><a href="$Link" title="Go to the $Title.XML page">$MenuTitle.XML <span>$Summary</span></a></li>
   	<% end_loop %>
   </ul>