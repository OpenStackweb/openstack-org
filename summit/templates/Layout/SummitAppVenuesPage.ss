<div id="map"></div>
<div id="venue-accordion">
    <% loop $Summit.Venues() %>
        <div class="venue">
            <div class="header" id="{$ID}">
                <div class="overlay"></div>
                <img class="image" src="$FirstImage.getURL()" />
                <div class="labelbox">
                    <div class="title"> $Name </div>
                    <div class="address"> $Address </div>
                </div>
            </div>
            <div id="carousel_{$ID}" class="carousel slide" data-ride="carousel" >
                <ol class="carousel-indicators">
                <li data-target="#carousel_{$ID}" data-slide-to="0" class="active"></li>
                <li data-target="#carousel_{$ID}" data-slide-to="1"></li>
                <li data-target="#carousel_{$ID}" data-slide-to="2"></li>
                <li data-target="#carousel_{$ID}" data-slide-to="3"></li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <% loop $Images %>
                    <div class="item <% if First() %>active<% end_if %>">
                        <img class="carousel_image" src="$Picture.getURL()" style="width:70%;margin:0 auto;" />
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
    <% loop $Summit.Venues() %>
        <% if $Lat && $Lng %>
            coordinates.push({id: {$ID}, lat: {$Lat}, lng: {$Lng}, title: "{$Name.JS}"});
        <% end_if %>
    <% end_loop %>
</script>
