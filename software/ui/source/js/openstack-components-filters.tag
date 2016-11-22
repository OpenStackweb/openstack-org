require('./t.tag');
<openstack-components-filters>
    <div id="all-projects-filter-wrapper">
    <div class="row">
    <div class="col-md-2 col-sm-3 col-xs-10 single-filter-wrapper first">
    <div class="filter-stats-label">
    	<t entity="Software.ADOPTION_OF_AT_LEAST">
    		<strong>ADOPTION</strong> of at least
    	</t>
    	<span id="adoptionSliderVal">0</span>%
    </div>
    <input id="all-projects-adoption" data-slider-id='adoptionSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-value="0"/>
    </div>
    <div class="col-md-2 col-sm-3 col-xs-10 single-filter-wrapper">
    <div class="filter-stats-label">
    	<t entity="Software.MATURITY_OF_AT_LEAST">
    		<strong>MATURITY</strong> of at least
    	</t> 
    	<span id="maturitySliderVal">0</span> 
    	<t entity="Software.MATUIRY_OF">of</t> 
    	{ this.max_maturity_points }
    </div>
    <input id="all-projects-maturity" data-slider-id='maturitySlider' type="text" data-slider-min="0" data-slider-max="{ this.max_maturity_points }" data-slider-step="1" data-slider-value="0"/>
    </div>
    <div class="col-md-2 col-sm-3 col-xs-10 single-filter-wrapper">
    <div class="filter-stats-label">
    	<t entity="Software.AGE_OF_AT_LEAST">
    		<strong>AGE</strong> of at least 
    	</t>
    	<span id="ageSliderVal">0</span> 
    	<t entity="Software.YEARS">years</t>
    </div>
    <input id="all-projects-age" data-slider-id='ageSlider' type="text" data-slider-min="0" data-slider-max="10" data-slider-step="1" data-slider-value="0"/>
    </div>
    </div>
    </div>

    <script>    
        this.api                 = opts.api;
        this.last_ajax_request   = null;
        this.max_maturity_points = opts.max_maturity_points;
        var self                 = this;

        this.on('mount', function(){

            // Project filter sliders
            $("#all-projects-adoption").slider({
                ticks: [0, 100],
                ticks_positions: [0, 100],
                ticks_labels: ['0', '100'],
                ticks_snap_bounds: 1
            });

            $("#all-projects-adoption").on("slide", function(slideEvt) {
                $("#adoptionSliderVal").text(slideEvt.value);

                var adoption   = $("#all-projects-adoption").slider('getValue');
                var maturity   = $("#all-projects-maturity").slider('getValue');
                var age        = $("#all-projects-age").slider('getValue');
                var txt        = $('#all-projects-search').val();
                var release_id = $('#openstack_releases').val();

                if(self.last_ajax_request != null )
                    self.last_ajax_request.abort();
                self.last_ajax_request = self.api.load_components_by_release(release_id, txt, adoption, maturity, age);
            });

            // maturity filter
            $("#all-projects-maturity").slider({
                ticks: [0, 3, self.max_maturity_points],
                ticks_labels: ['0', '3',  self.max_maturity_points],
                ticks_snap_bounds: 0
            });

            $("#all-projects-maturity").on("slide", function(slideEvt) {
                $("#maturitySliderVal").text(slideEvt.value);

                var adoption   = $("#all-projects-adoption").slider('getValue');
                var maturity   = $("#all-projects-maturity").slider('getValue');
                var age        = $("#all-projects-age").slider('getValue');
                var txt        = $('#all-projects-search').val();
                var release_id = $('#openstack_releases').val();

                if(self.last_ajax_request != null )
                    self.last_ajax_request.abort();
                self.last_ajax_request = self.api.load_components_by_release(release_id, txt, adoption, maturity, age);
            });

            // age filter
            $("#all-projects-age").slider({
                ticks: [0, 10],
                ticks_labels: ['0', '10'],
                ticks_snap_bounds: 0
            });

            $("#all-projects-age").on("slide", function(slideEvt) {
                $("#ageSliderVal").text(slideEvt.value);

                var adoption   = $("#all-projects-adoption").slider('getValue');
                var maturity   = $("#all-projects-maturity").slider('getValue');
                var age        = $("#all-projects-age").slider('getValue');
                var txt        = $('#all-projects-search').val();
                var release_id = $('#openstack_releases').val();

                if(self.last_ajax_request != null )
                     self.last_ajax_request.abort();
                self.last_ajax_request = self.api.load_components_by_release(release_id, txt, adoption, maturity, age);
            });
        });
    </script>
</openstack-components-filters>