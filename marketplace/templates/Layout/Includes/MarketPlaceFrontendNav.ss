<div class="container">
    <div class="row marketplace-top-wrapper">
        <div class="row">
		<div class="col-sm-12 marketplace-brand">
			<h1 class="marketplace">
				The OpenStack Marketplace
			</h1>
		</div>

            <% with Top %>
            <div class="col-sm-12">
                <ul class="marketplace-nav">
                    <% if canViewTab(1) %>
                        <li id="training">
                            <a href="{$getMarketPlaceTypeLink(1)}">
                                <span></span>
                                Training
                                <br>
                                &nbsp;
                            </a>
                        </li>
                    <% end_if %>
                    <% if canViewTab(2) %>
                        <li id="distros">
                            <a href="{$getMarketPlaceTypeLink(2)}">
                                <span></span>
                                Distros &
                                Appliances
                            </a>
                        </li>
                    <% end_if %>
                    <% if canViewTab(3) %>
                        <li id="public-clouds">
                            <a href="{$getMarketPlaceTypeLink(3)}">
                                <span></span>
                                Public Clouds
                            </a>
                        </li>
                    <% end_if %>
                    <span id="pcaas-wrapper"> <!--Wrapper for PCaaS graphic-->
                    <% if canViewTab(6) %>
                        <li id="private-clouds">
                            <a href="{$getMarketPlaceTypeLink(6)}">
                                <span></span>
                                Hosted Private Clouds
                            </a>
                        </li>
                    <% end_if %>
                    <% if canViewTab(7) %>
                        <li id="remote-clouds">
                            <a href="{$getMarketPlaceTypeLink(7)}">
                                <span></span>
                                Remotely Managed Private Clouds
                            </a>
                        </li>
                    <% end_if %>
                    </span>
                    <% if canViewTab(4) %>
                        <li id="consulting">
                            <a href="{$getMarketPlaceTypeLink(4)}">
                                <span></span>
                                Consulting &
                                Integrators
                            </a>
                        </li>
                    <% end_if %>
                    <% if canViewTab(5) %>
                        <li id="drivers">
                            <a href="{$getMarketPlaceTypeLink(5)}">
                                <span></span>
                                 Drivers
                            </a>
                        </li>
                    <% end_if %>
                </ul>
            </div>
        </div>
        <% end_with %>
    </div>
</div>
