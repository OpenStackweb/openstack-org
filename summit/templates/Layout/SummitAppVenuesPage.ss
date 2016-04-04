<div id="map"></div>
<div id="venue-accordion">
    <% loop $Summit.PrimaryVenues() %>
        <div class="venue">
            <div class="header" id="{$ID}">
                <div class="overlay"></div>
                <img class="image" src="<% if $getFirstPicture %> $getFirstPicture.croppedImage(1500,350).getURL() <% end_if %>" />
                <div class="labelbox">
                    <div class="title"> $Name </div>
                    <div class="address"> $Address </div>
                </div>
            </div>
            <div id="carousel_{$ID}" class="carousel slide" data-ride="carousel" >
                <ol class="carousel-indicators">
                    <% loop $Images() %>
                    <li data-target="#carousel_{$Up.ID}" data-slide-to="$Pos(0)" class="<% if First() %>active<% end_if %>"></li>
                    <% end_loop %>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <% loop $Images() %>
                    <div class="item <% if First() %>active<% end_if %>">
                        <img class="carousel_image" src="$Picture.getURL()" style="width:auto;margin:0 auto;height:500px" />
                    </div>
                    <% end_loop %>
                </div>

                <!-- Left and right controls -->
                <a class="left carousel-control" href="#carousel_{$ID}" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel_{$ID}" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    <% end_loop %>
</div>
<script type="text/javascript">
    var coordinates = [];
    <% loop $Summit.PrimaryVenues() %>
        <% if $Lat && $Lng %>
            coordinates.push({id: {$ID}, lat: {$Lat}, lng: {$Lng}, title: "{$Name.JS}"});
        <% end_if %>
    <% end_loop %>
</script>
