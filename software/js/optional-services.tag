<optional-services>
    <div class="row">
    <div class="col-sm-12">
    <p class="service-section-title"><strong>Optional Services</strong>{ ' ( '+components.length+' Results )' }</p>
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
            <th>Name</th>
            <th>Service</th>
            <th>Adoption <a href="#" id='sort-adoption' onclick={ sortAdoption }><i class="fa fa-sort"></i></a></th>
            <th>Age <a href="#" id='sort-age' onclick={ sortAge }><i class="fa fa-sort"></i></a></th>
            <th>Maturity <a href="#" id='sort-maturity' onclick={ sortMaturity }><i class="fa fa-sort"></i></a></th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr each="{ components }">
        <td>{ code_name }</td>
        <td>{ name }</td>
        <td><div class="service-stat-pill { adoption >= 0  && adoption <= 50 ? 'red': (adoption >= 50  && adoption <= 75 ? 'orange' : 'green') }">{ adoption } %</div></td>
        <td><div class="service-stat-pill { age >= 0  && age <= 1 ? 'red': (age > 1  && age <= 2 ? 'orange' : 'green') }">{ age } Yrs</div></td>
        <td><div class="service-stat-pill { maturity_points >= 0  && maturity_points <= 1 ? 'red': (maturity_points > 1  && maturity_points <= 3 ? 'orange' : 'green') }">{ maturity_points } <span>of</span> { this.max_maturity_points }</div></td>
        <td><a href="#" onclick={ optionalServiceDetails }>More Details</a></td>
        </tr>
    </tbody>
    </table>
    </div>
    </div>
    </div>

    <script>

    this.components          = opts.components;
    this.api                 = opts.api;x
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