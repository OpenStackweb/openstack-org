    <div class="main-panel-divider-label">
                Presentation
    </div>

			<% with $Presentation %>                   	
           	
            <div class="main-panel-section confirm-block">
          	    <div class="confirm-label">Title</div>
          	    <div class="confirm-title">$Title</div>
          	    <div class="confirm-label">Abstract</div>
          	    <div class="confirm-item">$Description</div>
            </div>
            <div class="main-panel-section confirm-block">
          	    <div class="confirm-label">Level</div>
          	    <div class="confirm-item">$Level</div>
          	    <div class="confirm-label">Topic</div>
          	    <div class="confirm-item">$Category.Title</div>
            </div>
            
           	<div class="main-panel-divider-label">
                Speakers
            </div>
                                
                                                                        
  					<% loop $Speakers %>
                      <div class="main-panel-section confirm-block">
                  	    <div class="row">
                  	        <div class="col-lg-2">
                                   <p class="user-img" <% if $Photo %>style="background-image: url($Photo.URL);"<% end_if %>></p> 
                              </div>
                              <div class="col-lg-10"> 
                                  <div class="confirm-label">Speaker</div>
                                  <div class="confirm-title">$FirstName $LastName</div>
                                  <div class="confirm-item">$Title</div>
                                  <div class="confirm-label">Bio</div>
                                  <div class="confirm-item">$Bio</div>
                  	        </div>
                  	    </div>
                      </div>					    
  					<% end_loop %>
	   <% end_with %>
