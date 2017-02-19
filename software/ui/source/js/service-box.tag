require('./t.tag');
<service-box>
    <div class="col-md-4 col-sm-6">
    <div class="core-services-single-full">
    <div class="core-top">
    <div class="core-title" style="background: url(/software/images/mascots/{slug}.png) no-repeat center center;">
    { code_name }
    </div>
    <div class="core-service">
    { name }
    </div>
    </div>
    <div class="core-mid component-description">
    <p>
    { description }
    </p>
    </div>
    <div class="core-stats-wrapper">
    <div class="row">
    <div class="col-sm-4 col-xs-4">
    <div class="core-stat-graphic">
    { adoption } %
    </div>
    <div class="core-stat-title">
    <t entity="Software.ADOPTION" text="ADOPTION" />
    </div>
    </div>
    <div class="col-sm-4 col-xs-4">
    <div class="core-stat-graphic">
    { maturity_points } <span>of</span> { parent.max_maturity_points }
    </div>
    <div class="core-stat-title">
    <t entity="Software.MATURITY" text="MATURITY" />
    </div>
    </div>
    <div class="col-sm-4 col-xs-4">
    <div class="core-stat-graphic">
    { age } <span>yrs</span>
    </div>
    <div class="core-stat-title">
    <t entity="Software.AGE" text="AGE" />
    </div>
    </div>
    </div>
    </div>
    <div class="core-bottom">
    <a class="core-service-btn" href="#" onclick={ coreServiceDetails }>
    	<t entity="Software.MORE_DETAILS" text="More Details" />
    </a>
    </div>
    </div>
    </div>

    <script>

        var self = this;

        coreServiceDetails(e) {
            var slug  = e.item.slug;
            var url = self.parent.base_url+'releases/'+self.parent.parent.getCurrentReleaseId()+'/components/'+slug;
            window.location = url;
        }
    </script>
</service-box>
