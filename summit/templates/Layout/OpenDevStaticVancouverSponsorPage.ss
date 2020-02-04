<script type="application/javascript">
    var urls = {
        emitPackagePurchaseOrder: '{$Top.Link(emitPackagePurchaseOrder)}',
        searchOrg               : '{$Top.Link(searchOrg)}'
    };

    var summit_id = $Top.Summit.ID ;
</script>
<div class="white sponsor-page-wrapper">
    <div class="container">
        <% if $SponsorAlert %>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-info sponsor-alert">
                        $SponsorAlert
                    </div>
                </div>
            </div>
        <% end_if %>
        <div class="row">
            <div class="col-lg-8 col-lg-push-2 sponsor-intro">
                $SponsorIntro
            </div>
        </div>
    </div>
</div>

<div class="light secondary-nav sponsor">
    <div class="container">
        <ul class="secondary-nav-list">
            <% if ShowSponsorShipPackages %>
                <li>
                    <a href="#packages">
                        <i class="fa fa-tags"></i>
                        Packages
                    </a>
                </li>
            <% end_if %>
            <% if HasSponsors %>
                <li>
                    <a href="#sponsors">
                        <i class="fa fa-heart"></i>
                        Sponsors
                    </a>
                </li>
            <% end_if %>
            <% if VenueMapContent %>
                <li>
                    <a href="#venue-map">
                        <i class="fa fa-map-marker"></i>
                        Venue Maps
                    </a>
                </li>
            <% end_if %>
            <% if ShowAudience %>
                <li>
                    <a href="#audience">
                        <i class="fa fa-group"></i>
                        Audience
                    </a>
                </li>
            <% end_if %>
            <% if HowToSponsorContent %>
                <li>
                    <a href="#how-to-sponsor">
                        <i class="fa fa-question-circle"></i>
                        How To Sponsor
                    </a>
                </li>
            <% end_if %>
        </ul>
    </div>
</div>
<% if HasSponsors %>
<div class="white sponsor-list" id="sponsors">
    <div class="container">
        <% loop getSponsorshipTypes() %>
        <% if $Top.getSponsorsByType($Name) %>
        <div class="row">
            <div class="col-lg-8 col-lg-push-2">
                <h5 class="section-title">
                    $Label
                </h5>
            </div>
            <div class="col-lg-12">
                <div class="row" style="display: flex;justify-content: center;">
                    <% loop $Top.getSponsorsByType($Name) %>
                    <div class="{$SponsorshipType.getSizeClass()} centered-column">
                        <a rel="nofollow" href="{$SubmitPageUrl}">$Company.SubmitLogo</a>
                    </div>
                    <% if $Top.sponsorHasReachedRowLimit($SponsorshipType.getItemsPerRow(), $Pos) %>
                </div>
                <div class="row" style="display: flex;justify-content: center;">
                    <% end_if %>
                    <% end_loop %>
                </div>
            </div>
        </div>
        <% end_if %>
        <% end_loop %>
    </div>
</div>
<% end_if %>

<!-- sponsorship packages -->
<% if $Top.ShowSponsorShipPackages %>
    <div class="light" id="packages" style="margin-bottom: -60px;" >
        <div class="container sponsor-wrapper">
            <% if $Top.SortedPackages %>
                <div class="row">
                    <h1>Packages</h1>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-lg-push-2">
                        <h5 class="section-title">
                            $Top.SponsorshipPackagesTitle
                        </h5>
                        <div class="row" id="packages_container">
                            <% loop $SortedPackages %>
                                <div class="sponsor_package col-lg-4 col-md-4 col-sm-4" <% if not $SoldOut %>title="Buy me"<% end_if %>>
                                    <div class="sponsor-spots <% if $SoldOut %>sold-out<% end_if %>">
                                        <h3>$Title <span>$SubTitle</span></h3>
                                        <div class="sponsor-cost">
                                            $Cost.Nice
                                        </div>
                                        <div class="sponsor-count">
                                            <% if $SoldOut %>
                                                Sold Out
                                            <% else %>
                                                <% if $ShowQuantity %>
                                                    <td>Available: $CurrentlyAvailable of $MaxAvailable</td>
                                                <% else %>
                                                    <td>Still Available</td>
                                                <% end_if %>
                                            <% end_if %>
                                        </div>
                                    </div>
                                    <!--
                                <div class="package-actions">
                                    <button type="button" id="package_{$ID}" data-title="{$Title}" data-id="{$ID}" class="btn btn-primary buy-package<% if $SoldOut %> hide<% end_if %>"  data-available="{$CurrentlyAvailable}">Buy Me</button>
                                </div>
                                -->
                                </div>
                            <% end_loop %>
                        </div>
                        <% if $Top.HasDiscountPackages %>
                            <div class="row" id="discount_packages_container">
                                <p>
                                    $Top.ConditionalSponsorshipPackagesTitle
                                </p>
                                <% loop $SortedPackages %>
                                    <% if $DiscountPackages %>
                                        <% loop $DiscountPackages %>
                                        <div class="sponsor_package col-lg-4 col-md-4 col-sm-4" <% if not $SoldOut %>title="Buy me"<% end_if %>>
                                            <div class="sponsor-spots <% if $SoldOut %>sold-out<% end_if %>">
                                                <h3>$Title <span>$SubTitle</span></h3>
                                                <div class="sponsor-cost">
                                                    $ReducedCost.Nice
                                                </div>
                                                <div class="sponsor-count">
                                                    <% if $SoldOut %>
                                                        Sold Out
                                                    <% else %>
                                                        <% if $ShowQuantity %>
                                                            <td>Available: $CurrentlyAvailable of $MaxAvailable</td>
                                                        <% else %>
                                                            <td>Still Available</td>
                                                        <% end_if %>
                                                    <% end_if %>
                                                </div>
                                            </div>
                                        <% end_loop %>
                                    <% end_if %>
                                    <!--
                                    <div class="package-actions">
                                        <button type="button" id="package_{$ID}" data-title="{$Title}" data-id="{$ID}" class="btn btn-primary buy-package<% if $SoldOut %> hide<% end_if %>"  data-available="{$CurrentlyAvailable}">Buy Me</button>
                                    </div>
                                    -->
                                </div>
                                <% end_loop %>
                            </div>
                        <% end_if %>
                    </div>
                </div>
                <div class="sponsor-note">
                    * In order to qualify for a Startup sponsorship a company must be in business for less than 3
                    years and have less than $5 million USD in revenue.
                </div>
            <% end_if %>
            <% if $SortedAddOns %>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <h5 class="section-title">$Top.SponsorshipAddOnsTitle</h5>
                    </div>
                    <div class="table-responsive sponsor-table">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Add-On Package</th>
                                <th>Cost</th>
                                <th>Available</th>
                            </tr>
                            </thead>
                            <tbody id="add_ons">

                                <% loop $SortedAddOns %>
                                <tr id="addon_{$ID}" class="sponsor_add_on <% if $SoldOut %>sold-out<% end_if %>">
                                    <td>$Title</td>
                                    <td>$Cost</td>
                                    <% if $SoldOut %>
                                        <td>Sold Out</td>
                                    <% else %>
                                        <% if $ShowQuantity %>
                                            <td>$CurrentlyAvailable of $MaxAvailable</td>
                                        <% else %>
                                            <td>Available</td>
                                        <% end_if %>
                                    <% end_if %>
                                </tr>
                                <% end_loop %>
                            </tbody>
                        </table>
                    </div>
                </div>
            <% end_if %>
        </div>
        </div>
<% end_if %>
    <!-- end sponsorship packages -->
<% if VenueMapContent %>
        <div class="sponsor-bkgd">
            <div class="fixed-image exhibit" style="background-image: url('{$ExhibitImageUrl}');"></div>
        </div>
        <div class="white" id="venue-map">
            <div class="container">
                $VenueMapContent
            </div>
        </div>
<% end_if %>
<% if ShowAudience %>
        <div class="sponsor-bkgd">
            <div class="fixed-image crowd" style="background-image: url('{$CrowdImageUrl}');"></div>
        </div>
        <div class="white" id="audience">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-push-2">
                        <h1>Audience</h1>
                        <p class="audience-intro">
                            $AudienceIntro
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h5 class="section-title"></h5>
                        $AudienceMetricsTitle
                        <div class="row">
                            <div class="col-lg-8 col-lg-push-2 stats-highlight">
                                <h3>$AudienceTotalSummitAttendees<span>Total Summit Attendees</span></h3>
                                <h3>$AudienceCompaniesRepresented<span>Companies Represented</span></h3>
                                <h3>$AudienceCountriesRepresented<span>Countries Represented</span></h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5 col-lg-push-1 col-md-6 col-sm-6">
                                <div class="attendees-region">
                                    <h4>Attendees By Region</h4>
                                    <!-- Region Chart -->
                                    <canvas id="attendeesRegion" width="250" height="250"
                                            style="width: 250px; height: 250px;"></canvas>
                                </div>
                            </div>
                            <div class="col-lg-5 col-lg-push-1 col-md-6 col-sm-6">
                                <div class="attendees-region">
                                    <h4>Attendees By Role</h4>
                                    <!-- Role Chart -->
                                    <canvas id="attendeesRole" width="250" height="250"
                                            style="width: 250px; height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<% end_if %>
<% if HowToSponsorContent %>
        <!-- <div class="sponsor-bkgd">
    <div class="fixed-image exhibit" style="background-image: url('{$ExhibitImageUrl}');"></div>
</div> -->
        <div class="light sponsor-instructions negative-bottom" id="how-to-sponsor">
            <div class="container">
                $HowToSponsorContent
            </div>
        </div>
<% end_if %>
<% if ShowSponsorShipPackages %>
        <!-- Modal -->
        <div class="modal fade" id="summit_package_purchase_order_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Sponsorship Package Purchase Order</h4>
                    </div>
                    <div class="modal-body">
                        <form id="summit_package_purchase_order_form">
                            <fieldset>
                                <div class="form-group">
                                    <label for="summit_package_purchase_order_fname">First name</label>
                                    <input type="text" class="form-control" id="summit_package_purchase_order_fname" name="summit_package_purchase_order_fname" placeholder="Enter first name">
                                </div>
                                <div class="form-group">
                                    <label for="summit_package_purchase_order_lname">Last name</label>
                                    <input type="text" class="form-control" id="summit_package_purchase_order_lname" name="summit_package_purchase_order_lname" placeholder="Enter last name">
                                </div>
                                <div class="form-group">
                                    <label for="summit_package_purchase_order_email">Email address</label>
                                    <input type="email" class="form-control" id="summit_package_purchase_order_email" name="summit_package_purchase_order_email" placeholder="Enter email">
                                </div>
                                <div class="form-group">
                                    <label for="summit_package_purchase_order_org">Organization</label>
                                    <input type="text" class="form-control" id="summit_package_purchase_order_org" name="summit_package_purchase_order_org" placeholder="Enter Organization">
                                </div>
                                <div class="checkbox">
                                    <label class="checkbox-inline"><input style="margin-left: -25px !important;" type="checkbox" value="" name="summit_package_purchase_order_terms" id="summit_package_purchase_order_terms"><a href="#">Accept terms & conditions</a></label>
                                </div>
                                <input type="hidden" id="summit_package_purchase_order_org_id" name="summit_package_purchase_order_org_id" value="0"/>
                                $PackagePurchaseOrderSecurityID
                            </fieldset>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" id="summit_package_purchase_order_buy_btn" class="btn btn-primary">Buy</button>
                    </div>
                </div>
            </div>
        </div>
<% end_if %>
