require('./t.tag');
<optional-services>
    <div class="row">
    <div class="col-sm-12">
    <p class="service-section-title">
    	<strong><t entity="Software.OPTIONAL_SERVICES">Optional Services</t></strong>
    	{` ( ${components.length} ${ss.i18n._t('Openstack.RESULTS','Results')} )`}
    </p>
    </div>
    </div>
    <div class="row">
    <div class="col-sm-12">
    <div class="table-responsive optional-services-table">
    <div class="sample-optional-hidden">
    </div>
    <table class="table">
    <thead>
        <tr>
            <th><t entity="Software.NAME">Name</t></th>
            <th><t entity="Software.SERVICE">Service</t></th>
            <th><t entity="Software.MATURITY">Maturity</t> <a href="#" id='sort-maturity' onclick={ sortMaturity }><i class="fa fa-sort"></i></a></th>
            <th><t entity="Software.AGE">Age</tr> <a href="#" id='sort-age' onclick={ sortAge }><i class="fa fa-sort"></i></a></th>
            <th><t entity="Software.ADOPTION">Adoption</t> <a href="#" id='sort-adoption' onclick={ sortAdoption }><i class="fa fa-sort"></i></a></th>
            <th><t entity="Software.DETAILS">Details</t></th>
        </tr>
    </thead>
    <tbody>
        <tr each="{ components }">
        <td>{ code_name }</td>
        <td>{ name }</td>
        <td><div class="service-stat-pill { maturity_points >= 0  && maturity_points <= 1 ? 'red': (maturity_points > 1  && maturity_points <= 3 ? 'orange' : 'green') }">{ maturity_points } <span>of</span> { this.max_maturity_points }</div></td>
        <td><div>{ age } Yrs</div></td>
        <td><div>{ adoption } %</div></td>
        <td>
        	<a href="#" onclick={ optionalServiceDetails }>
        		<t entity="Software.MORE_DETAILS">More Details</t>
        	</a>
        </td>
        </tr>
    </tbody>
    </table>
    </div>
    </div>
    </div>

    <script>

    this.components          = opts.components;
    this.api                 = opts.api;
    this.adoption_dir        = 'desc';
    this.maturity_dir        = 'desc';
    this.age_dir             = 'desc';
    this.max_maturity_points = opts.max_maturity_points;
    this.base_url            = opts.base_url;
    var self                 = this;

    optionalServiceDetails(e) {
        var slug  = e.item.slug;
        var url = self.base_url+'releases/'+self.getCurrentReleaseId()+'/components/'+slug;
        window.location = url;
    }

    getCurrentReleaseId() {
        return $('#openstack_releases option:selected').text().toLowerCase();
    }

    this.api.on('loaded-components-by-release',function(data) {
        self.components =  data.optional_components;
        self.update();
    });

    sortAdoption(e) {

        var adoption   = $("#all-projects-adoption").slider('getValue');
        var maturity   = $("#all-projects-maturity").slider('getValue');
        var age        = $("#all-projects-age").slider('getValue');
        var txt        = $('#all-projects-search').val();
        var release_id = $('#openstack_releases').val();

        self.api.load_components_by_release(release_id, txt, adoption, maturity, age,'adoption', self.adoption_dir);
        self.adoption_dir = self.adoption_dir === 'desc' ? 'asc' : 'desc';
    }

    sortMaturity(e) {
        var adoption   = $("#all-projects-adoption").slider('getValue');
        var maturity   = $("#all-projects-maturity").slider('getValue');
        var age        = $("#all-projects-age").slider('getValue');
        var txt        = $('#all-projects-search').val();
        var release_id = $('#openstack_releases').val();

        self.api.load_components_by_release(release_id, txt, adoption, maturity, age,'maturity', self.maturity_dir);
        self.maturity_dir = self.maturity_dir === 'desc' ? 'asc' : 'desc';
    }

    sortAge(e) {
        var adoption   = $("#all-projects-adoption").slider('getValue');
        var maturity   = $("#all-projects-maturity").slider('getValue');
        var age        = $("#all-projects-age").slider('getValue');
        var txt        = $('#all-projects-search').val();
        var release_id = $('#openstack_releases').val();

        self.api.load_components_by_release(release_id, txt, adoption, maturity, age,'age', self.age_dir);
        self.age_dir = self.age_dir === 'desc' ? 'asc' : 'desc';
    }

    </script>
</optional-services>