<% if CityIntro %>
<div class="white city-intro">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                $CityIntro
            </div>
        </div>
    </div>
</div>
<% end_if %>
<div class="light city-nav city" id="nav-bar">
    <div class="container">
        <ul class="city-nav-list">
            <li>
                <a href="/summit/austin-2016/tokyo-and-travel/#venue">
                    <i class="fa fa-plane"></i>
                    Venue
                </a>
            </li>
            <li>
                <a href="/summit/tokyo-2015/tokyo-and-travel/#hotels">
                    <i class="fa fa-map-marker"></i>
                    Hotels &amp; Airport
                </a>
            </li>
            <% if GettingAround  %>
            <li>
                <a href="/summit/tokyo-2015/tokyo-and-travel/#getting-around">
                    <i class="fa fa-map"></i>
                    Campus Maps
                </a>
            </li>
            <% end_if %>
            <% if TravelSupport  %>
                <li>
                    <a href="#travel-support">
                        <i class="fa fa-globe"></i>
                        Travel Support Program
                    </a>
                </li>
            <% end_if %>
            <% if VisaInformation  %>
            <li>
                <a href="#visa">
                    <i class="fa fa-plane"></i>
                    Visa Info
                </a>
            </li>
            <% end_if %>
            <% if Locals  %>
            <li>
                <a href="#locals">
                    <i class="fa fa-heart"></i>
                    Locals
                </a>
            </li>
            <% end_if %>
        </ul>
    </div>
</div>
<% with $Venue %>
    <div id="venue">
        <div class="venue-row tokyo" style="background: rgba(0, 0, 0, 0) url('{$Top.VenueBackgroundImageUrl}') no-repeat scroll left top / cover ;">
            <div class="container">
                <h1>$Top.VenueTitleText</h1>
                <p>
                    <strong>Austin Convention Center</strong>
                    500 East Cesar Chavez Street<br>
                    Austin, Texas 78701
                </p>
            </div>
            <a href="{$Top.VenueBackgroundImageHeroSource}" class="photo-credit" data-toggle="tooltip" data-placement="left" title="{$Top.VenueBackgroundImageHero}" target="_blank">
                <i class="fa fa-info-circle"></i>
            </a>
        </div>
    </div>
<% end_with %>
<div class="white hotels-row" id="hotels">
    <div class="venue-map" id="map-canvas" style="position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"><div class="gm-style" style="position: absolute; left: 0px; top: 0px; overflow: hidden; width: 100%; height: 100%; z-index: 0;"><div style="position: absolute; left: 0px; top: 0px; overflow: hidden; width: 100%; height: 100%; z-index: 0; cursor: url(&quot;https://maps.gstatic.com/mapfiles/openhand_8_8.cur&quot;) 8 8, default;"><div style="position: absolute; left: 0px; top: 0px; z-index: 1; width: 100%; transform-origin: 0px 0px 0px; transform: matrix(1, 0, 0, 1, 0, 0);"><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 100; width: 100%;"><div style="position: absolute; left: 0px; top: 0px; z-index: 0;"><div aria-hidden="true" style="position: absolute; left: 0px; top: 0px; z-index: 1; visibility: inherit;"><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 619px; top: -40px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 363px; top: -40px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 363px; top: 216px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 619px; top: 216px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 107px; top: -40px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 107px; top: 216px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 875px; top: -40px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 875px; top: 216px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: -149px; top: 216px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: -149px; top: -40px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 1131px; top: -40px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 1131px; top: 216px;"></div></div></div></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 101; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 102; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 103; width: 100%;"><div style="position: absolute; left: 0px; top: 0px; z-index: -1;"><div aria-hidden="true" style="position: absolute; left: 0px; top: 0px; z-index: 1; visibility: inherit;"><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 619px; top: -40px;"><canvas draggable="false" height="512" width="512" style="-webkit-user-select: none; position: absolute; left: 0px; top: 0px; height: 256px; width: 256px;"></canvas></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 363px; top: -40px;"><canvas draggable="false" height="512" width="512" style="-webkit-user-select: none; position: absolute; left: 0px; top: 0px; height: 256px; width: 256px;"></canvas></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 363px; top: 216px;"><canvas draggable="false" height="512" width="512" style="-webkit-user-select: none; position: absolute; left: 0px; top: 0px; height: 256px; width: 256px;"></canvas></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 619px; top: 216px;"><canvas draggable="false" height="512" width="512" style="-webkit-user-select: none; position: absolute; left: 0px; top: 0px; height: 256px; width: 256px;"></canvas></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 107px; top: -40px;"><canvas draggable="false" height="512" width="512" style="-webkit-user-select: none; position: absolute; left: 0px; top: 0px; height: 256px; width: 256px;"></canvas></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 107px; top: 216px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 875px; top: -40px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 875px; top: 216px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: -149px; top: 216px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: -149px; top: -40px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 1131px; top: -40px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 1131px; top: 216px;"></div></div></div></div><div style="position: absolute; left: 0px; top: 0px; z-index: 0;"><div aria-hidden="true" style="position: absolute; left: 0px; top: 0px; z-index: 1; visibility: inherit;"><div style="transform: translateZ(0px); position: absolute; left: 619px; top: -40px; transition: opacity 200ms ease-out;"><img src="https://mts1.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14975!3i26982!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: 619px; top: 216px; transition: opacity 200ms ease-out;"><img src="https://mts1.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14975!3i26983!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: 107px; top: -40px; transition: opacity 200ms ease-out;"><img src="https://mts1.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14973!3i26982!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: 363px; top: -40px; transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14974!3i26982!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: 363px; top: 216px; transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14974!3i26983!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: 107px; top: 216px; transition: opacity 200ms ease-out;"><img src="https://mts1.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14973!3i26983!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: 875px; top: -40px; transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14976!3i26982!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: 875px; top: 216px; transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14976!3i26983!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: -149px; top: 216px; transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14972!3i26983!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: -149px; top: -40px; transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14972!3i26982!4i256!2m3!1e0!2sm!3i333089337!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: 1131px; top: -40px; transition: opacity 200ms ease-out;"><img src="https://mts1.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14977!3i26982!4i256!2m3!1e0!2sm!3i333068469!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div style="transform: translateZ(0px); position: absolute; left: 1131px; top: 216px; transition: opacity 200ms ease-out;"><img src="https://mts1.googleapis.com/maps/vt?pb=!1m5!1m4!1i16!2i14977!3i26983!4i256!2m3!1e0!2sm!3i333068469!3m9!2sen-US!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0!5m1!5f2" draggable="false" alt="" style="position: absolute; left: 0px; top: 0px; width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div></div></div></div><div style="position: absolute; left: 0px; top: 0px; z-index: 2; width: 100%; height: 100%;"></div><div style="position: absolute; left: 0px; top: 0px; z-index: 3; width: 100%; transform-origin: 0px 0px 0px; transform: matrix(1, 0, 0, 1, 0, 0);"><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 104; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 105; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 106; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 107; width: 100%;"></div></div></div><div style="padding: 15px 21px; border: 1px solid rgb(171, 171, 171); font-family: Roboto, Arial, sans-serif; color: rgb(34, 34, 34); box-shadow: rgba(0, 0, 0, 0.2) 0px 4px 16px; z-index: 10000002; display: none; width: 256px; height: 148px; position: absolute; left: 482px; top: 135px; background-color: white;"><div style="padding: 0px 0px 10px; font-size: 16px;">Map Data</div><div style="font-size: 13px;">Map data ©2015 Google</div><div style="width: 13px; height: 13px; overflow: hidden; position: absolute; opacity: 0.7; right: 12px; top: 12px; z-index: 10000; cursor: pointer;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/mapcnt6.png" draggable="false" style="position: absolute; left: -2px; top: -336px; width: 59px; height: 492px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div></div><div class="gmnoprint" style="z-index: 1000001; position: absolute; right: 167px; bottom: 0px; width: 121px;"><div draggable="false" class="gm-style-cc" style="-webkit-user-select: none; height: 14px; line-height: 14px;"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="width: auto; height: 100%; margin-left: 1px; background-color: rgb(245, 245, 245);"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right; vertical-align: middle; display: inline-block;"><a style="color: rgb(68, 68, 68); text-decoration: none; cursor: pointer; display: none;">Map Data</a><span>Map data ©2015 Google</span></div></div></div><div class="gmnoscreen" style="position: absolute; right: 0px; bottom: 0px;"><div style="font-family: Roboto, Arial, sans-serif; font-size: 11px; color: rgb(68, 68, 68); direction: ltr; text-align: right; background-color: rgb(245, 245, 245);">Map data ©2015 Google</div></div><div class="gmnoprint gm-style-cc" draggable="false" style="z-index: 1000001; -webkit-user-select: none; height: 14px; line-height: 14px; position: absolute; right: 95px; bottom: 0px;"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="width: auto; height: 100%; margin-left: 1px; background-color: rgb(245, 245, 245);"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right; vertical-align: middle; display: inline-block;"><a href="https://www.google.com/intl/en-US_US/help/terms_maps.html" target="_blank" style="text-decoration: none; cursor: pointer; color: rgb(68, 68, 68);">Terms of Use</a></div></div><div draggable="false" class="gm-style-cc" style="-webkit-user-select: none; height: 14px; line-height: 14px; position: absolute; right: 0px; bottom: 0px;"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="width: auto; height: 100%; margin-left: 1px; background-color: rgb(245, 245, 245);"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right; vertical-align: middle; display: inline-block;"><a target="_new" title="Report errors in the road map or imagery to Google" href="https://www.google.com/maps/@30.26365,-97.7396,16z/data=!10m1!1e1!12b1?source=apiv3&amp;rapsrc=apiv3" style="font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); text-decoration: none; position: relative;">Report a map error</a></div></div><div class="gmnoprint" draggable="false" controlwidth="28" controlheight="55" style="margin: 10px; -webkit-user-select: none; position: absolute; bottom: 81px; left: 0px;"><div class="gmnoprint" controlwidth="28" controlheight="55" style="position: absolute; left: 0px; top: 0px;"><div draggable="false" style="-webkit-user-select: none; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; border-radius: 2px; cursor: pointer; width: 28px; height: 55px; background-color: rgb(255, 255, 255);"><div title="Zoom in" style="position: relative; width: 28px; height: 27px; left: 0px; top: 0px;"><div style="overflow: hidden; position: absolute; width: 15px; height: 15px; left: 7px; top: 6px;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/tmapctrl_hdpi.png" draggable="false" style="position: absolute; left: 0px; top: 0px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none; width: 120px; height: 54px;"></div></div><div style="position: relative; overflow: hidden; width: 67%; height: 1px; left: 16%; top: 0px; background-color: rgb(230, 230, 230);"></div><div title="Zoom out" style="position: relative; width: 28px; height: 27px; left: 0px; top: 0px;"><div style="overflow: hidden; position: absolute; width: 15px; height: 15px; left: 7px; top: 6px;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/tmapctrl_hdpi.png" draggable="false" style="position: absolute; left: 0px; top: -15px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none; width: 120px; height: 54px;"></div></div></div></div></div><div class="gmnoprint" draggable="false" controlwidth="0" controlheight="0" style="margin: 10px; -webkit-user-select: none; position: absolute; display: none; bottom: 14px; right: 0px;"><div class="gmnoprint" controlwidth="28" controlheight="0" style="display: none; position: absolute;"><div title="Rotate map 90 degrees" style="width: 28px; height: 28px; overflow: hidden; position: absolute; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; cursor: pointer; display: none; background-color: rgb(255, 255, 255);"><img src="https://maps.gstatic.com/mapfiles/api-3/images/tmapctrl4_hdpi.png" draggable="false" style="position: absolute; left: -141px; top: 6px; width: 170px; height: 54px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div><div class="gm-tilt" style="width: 28px; height: 28px; overflow: hidden; position: absolute; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; top: 0px; cursor: pointer; background-color: rgb(255, 255, 255);"><img src="https://maps.gstatic.com/mapfiles/api-3/images/tmapctrl4_hdpi.png" draggable="false" style="position: absolute; left: -141px; top: -13px; width: 170px; height: 54px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; max-width: none;"></div></div></div><div style="margin-left: 5px; margin-right: 5px; z-index: 1000000; position: absolute; left: 0px; bottom: 0px;"><a target="_blank" href="https://maps.google.com/maps?ll=30.26365,-97.7396&amp;z=16&amp;t=m&amp;hl=en-US&amp;gl=US&amp;mapclient=apiv3" title="Click to see this area on Google Maps" style="position: static; overflow: visible; float: none; display: inline;"><div style="width: 66px; height: 26px; cursor: pointer;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/google4_hdpi.png" draggable="false" style="position: absolute; left: 0px; top: 0px; width: 66px; height: 26px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;"></div></a></div></div></div>
    <div class="container">
        <% if AlternateHotels %>
            <div class="row">
                <div class="col-sm-8 col-sm-push-2">
                    <h5 class="section-title">Hotels</h5>
                    <p style="margin-bottom:30px;">
                        <i class="fa fa-hotel fa-4x"></i>
                    </p>
                    <div class="alert alert-danger" role="alert">
                        <p class="center">
                            All of the discounted Summit hotel room blocks are now sold out. Here are some other hotels where you may reserve a room near the Summit venue. Note that OpenStack does NOT have a contracted room block at any of these hotels.
                        </p>
                    </div>
                </div>
            </div>
        <% end_if %>
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h5 class="section-title">Official Summit Hotels</h5>
                <% if not $Top.AlternateHotels %>
                    <p style="margin-bottom:30px;">
                        <i class="fa fa-hotel fa-4x"></i>
                    </p>
                <% end_if %>
                $LocationsTextHeader
            </div>
        </div>
        <% loop Hotels %>
            <% if $First() %>
            <div class="row">
            <% end_if %>
            <div class="col-lg-4 col-md-4 col-sm-4 hotel-block">
                <h3>{$Pos}. $Name</h3>
                <p>
                    $Address
                </p>

                <% if $LocationMessage %>
                    <p class="summit-location-message">
                        $LocationMessage
                    </p>
                <% end_if %>

                <p<% if $IsSoldOut %> class="sold-out-hotel" <% end_if%>>
                    <% if $IsSoldOut %>
                        SOLD OUT
                    <% else %> 

                        <% if not $Top.CampusGraphic %>
                            <a href="$Top.Link#map-canvas" class="marker-link"  data-location-id="{$ID}"  alt="View On Map"><i class="fa fa-map-marker"></i> Map</a>
                        <% end_if %>

                        <% if $DetailsPage %>
                            <a href="{$Top.Link}details/$ID" alt="Visit Bookings Site"><i class="fa fa-home"></i>
                                Booking Info</a>
                        <% else_if $BookingLink %>
                            <a href="{$BookingLink}" target="_blank" alt="Visit Bookings Site"><i class="fa fa-home"></i>
                                Book a Room</a>
                        <% else %>
                            <a href="#" data-toggle="modal" data-target="#Hotel{$ID}"><i class="fa fa-home"></i> Website</a>
                        <% end_if %>
                    <% end_if %>
                </p>
            </div>
            <% if Last() %>
            </div>
            <% else_if $MultipleOf(3) %>
            </div>
            <div class="row">
            <% end_if %>


        <% end_loop %>
    <!--     <div class="row">
            <div class="col-sm-10 col-sm-push-1">
                <h5 class="section-title">More Hotel Details</h5>
                <div class="more-hotel-details">
                    <p>
                        <i class="fa fa-users fa-2x"></i>
                    </p>
                    <p>
                        Booking for 10 or more rooms for the Summit?
                    </p>
                    <p>
                        Contact <a href="mailto:sarah@fntech.com">sarah@fntech.com</a>
                    </p>
                </div>
            </div>
        </div> -->
        <% if $Airports %>
            <% if $AirportsTitle %>
                <div class="row">
                    <div class="col-lg-8 col-lg-push-2">
                        <h5 class="section-title">$AirportsTitle</h5>
                        <p>
                            $AirportsSubTitle
                        </p>
                    </div>
                </div>
            <% end_if %>
            <div class="row">
                <div class="col-sm-4 col-sm-push-4 hotel-block">
                    <h3>Austin-Bergstrom International Airport</h3>
                    <p>
                        3600 Presidential Blvd<br>Austin, TX 78719
                    </p>
                    <p>
                        <a href="#map-canvas" onclick="myClick(12);" alt="View On Map"><i class="fa fa-map-marker"></i> Map</a>
                        <a href="http://www.austintexas.gov/airport/" target="_blank" alt="Visit Website"><i class="fa fa-home"></i> Website</a>
                    </p>  
                </div>
            </div>
        <% end_if %>
        <% if OtherLocations  %>
        <div class="row">
            <div class="col-lg-8 col-lg-push-2 other-hotel-options">
                <h5 class="section-title">House Sharing</h5>
                <p>If you plan to bring your family with you to Austin or if you would like to have more space than a hotel room offers, then you may want to rent an apartment or condo during your stay. The following sites are available for short-term property rentals.</p>
                $OtherLocations
            </div>
        </div>
        <% end_if %>
    </div>
</div>

<% if GettingAround  %>
<div class="blue" id="getting-around">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1>Getting Around In Austin</h1>
                <p>
                    There are several safe and reliable transportation options in Austin. Here are a few options to consider.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="getting-options">
                    <div class="getting-around-item">
                        <a href="//www.capmetro.org/airport/" target="_blank"><i class="fa fa-bus"></i>MetroAirport<span>(bus)</span></a>
                    </div>
                    <div class="getting-around-item">
                        <a href="//www.uber.com/cities/austin" target="_blank"><i class="fa fa-car"></i>Uber</a>
                    </div>
                    <div class="getting-around-item">
                        <a href="//www.lyft.com/cities/austin" target="_blank"><i class="fa fa-car"></i>Lyft</a>
                    </div>
                    <div class="getting-around-item">
                        <a href="//www.austintexas.gov/department/ground-transportation" target="_blank"><i class="fa fa-plane"></i>Airport Transportation</a>
                    </div>
                    <div class="getting-around-item">
                        <a href="#" target="_blank"><i class="fa fa-car"></i>Rental Cars</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<% end_if %>
<% if TravelSupport  %>
    <div class="light" id="travel-support">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-push-2">
                    $TravelSupport
                </div>
            </div>
        </div>
    </div>
<% end_if %>
<% if VisaInformation  %>
<div class="white visa-row" id="visa">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h1>Visa Information</h1>
                <div class="visa-steps-wrapper">
                    <h3>Get your Tokyo Summit Visa in 5 Steps</h3>
                    <h5><i class="fa fa-exclamation-circle"></i>The entire visa process can take up to 5 weeks, so <strong>apply now</strong>.</h5>
                    <div class="visa-steps-row">
                        <div class="steps-count">
                            <div class="visa-step">
                                <img src="/themes/openstack/images/summit/tokyo/visa-steps/1.png" alt="">
                                <p>
                                    Start now: Book your hotel, plane ticket &amp; summit registration
                                </p>
                            </div>
                            <div class="visa-step">
                                <img src="/themes/openstack/images/summit/tokyo/visa-steps/2.png" alt="">
                                <p>
                                    Complete the <a href="https://openstack.formstack.com/forms/visa_request_form" target="_blank">visa request form here</a>
                                </p>
                            </div>
                            <div class="visa-step">
                                <img src="/themes/openstack/images/summit/tokyo/visa-steps/3.png" alt="">
                                <p>
                                    Receive your visa invitation documents in the mail
                                </p>
                            </div>
                            <div class="visa-step">
                                <img src="/themes/openstack/images/summit/tokyo/visa-steps/4.png" alt="">
                                <p>
                                    Apply for your visa at your Japanese embassy or consulate
                                </p>
                            </div>
                            <div class="visa-step">
                                <img src="/themes/openstack/images/summit/tokyo/visa-steps/5.png" alt="">
                                <p>
                                    Wait for your visa to be issued, which may take up to 10 business days and pick it up from the embassy.
                                </p>
                            </div>
                        </div>
                        <div class="visa-docs">
                            <h4>Bring these documents when you apply for your visa:</h4>
                            <ul>
                                <li>Valid passort.</li>
                                <li>Two 45mm x 45mm photos taken within the last 6 months.</li>
                                <li>Copies of hotel &amp; flight reservations from your travel agent.</li>
                                <li>Summit invitation.</li>
                                <li>Documentation showing permission to travel from your company.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                $VisaInformation
            </div>
        </div>
    </div>
</div>
<% end_if %>
<div class="about-city-row austin">
    <p>
        Legendary music, epic BBQ, history, food trucks and neon...
    </p>
    <h1>Come Join Us In Austin</h1>
</div>
<% if Locals %>
<div class="white locals-row" id="locals">
    <div class="container">
        $Locals
    </div>
</div>
<% end_if %>
<% if AboutTheCity %>
<div class="about-city-row" style="background: rgba(0, 0, 0, 0) url('{$AboutTheCityBackgroundImageUrl}') no-repeat scroll left top / cover ">
    $AboutTheCity
    <p>
        <% if $Summit.RegistrationLink %>
            <a href="$Summit.RegistrationLink" class="btn register-btn-lrg">Register Now</a>
        <% end_if %>
    </p>
    <a href="{$AboutTheCityBackgroundImageHeroSource}" class="photo-credit" data-toggle="tooltip" data-placement="left" title="{$AboutTheCityBackgroundImageHero}" target="_blank"><i class="fa fa-info-circle"></i></a>
</div>
<% end_if %>
    <!-- End Other Hotels Modal -->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
