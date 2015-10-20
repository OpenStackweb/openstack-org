<openstack-config-samples>

        <div class="row">
            <div class="col-sm-12 sample-configs-wrapper">
            <div class="open-sample-config-tip"><i class="fa fa-question-circle"></i></div>
            <div class="sample-config-choices">
            <ul>
                    <li each={ configurations }>
                         <a class="config-button" id="{ 'config_button_'+id }" data-id={ id } href="#" onclick={ onConfigurationSelected}>{ title}</a>
                    </li>
            </ul>
            </div>
            <h3>{ current_config.title }</h3>
            <p>
                { current_config.summary }
            </p>
            <p>
                    <strong>Curated by:</strong> <a href="community/members/profile/{ current_config.curator.id }" target="_blank"> { current_config.curator.name }</a> - { current_config.curator.position }
            </p>
            <p if={ current_config.description !== null &&  current_config.description !=='' }>
               <a class="more-about-config" href="#">More about this configuration [+]</a>
            </p>
            <div class="more-sample-config" if={ current_config.description !== null &&  current_config.description !=='' }>
                { current_config.description }
            </div>
            <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                 <p class="service-section-title"><strong>Core Services</strong> included in this configuration ({ current_config.core_components.length } of { release_core_component_count })</p>
            </div>
        </div>
        <div class="row">
            <div each={ current_config.core_components } class="col-md-4 col-sm-6">
                <div class="core-services-single-full">
                    <div class="core-top">
                        <div class="core-title">
                        { code_name }
                        </div>
                        <div class="core-service">
                        { name }
                        </div>
                        <div class="core-service-icon">
                            <i class="fa { icon_class }"></i>
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
                { adoption }%
                </div>
                <div class="core-stat-title">
                Adoption
                </div>
                </div>
                <div class="col-sm-4 col-xs-4">
                <div class="core-stat-graphic">
                { maturity_points } <span>of</span> { this.max_maturity_points }
                </div>
                <div class="core-stat-title">
                Maturity
                </div>
                </div>
                <div class="col-sm-4 col-xs-4">
                <div class="core-stat-graphic">
                { age} <span>yrs</span>
                </div>
                <div class="core-stat-title">
                Age
                </div>
                </div>
                </div>
                </div>
                <div class="core-bottom">
                    <a class="core-service-btn" href="#" onclick={ onComponentDetails }>More Details</a>
                </div>
                </div>
            </div>
            <div each={ current_config.missing_core_components } class="col-md-4 col-sm-6">
                <div class="core-services-single-full core-off">
                <div class="core-top">
                <div class="core-title">
                { code_name }
                </div>
                <div class="core-service">
                { name }
                </div>
                <div class="core-service-icon">
                <i class="fa { icon_class }"></i>
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
                { adoption }%
                </div>
                <div class="core-stat-title">
                Adoption
                </div>
                </div>
                <div class="col-sm-4 col-xs-4">
                <div class="core-stat-graphic">
                { maturity_points } <span>of</span> { this.max_maturity_points }
                </div>
                <div class="core-stat-title">
                Maturity
                </div>
                </div>
                <div class="col-sm-4 col-xs-4">
                <div class="core-stat-graphic">
                { age} <span>yrs</span>
                </div>
                <div class="core-stat-title">
                Age
                </div>
                </div>
                </div>
                </div>
                <div class="core-bottom">
                <a class="core-service-btn" href="#" onclick={ onComponentDetails }>More Details</a>
                </div>
                </div>
                </div>
        </div>
        <div class="row" if={ current_config.optional_components.length >0 }>
            <div class="col-sm-12">
               <p class="service-section-title"><strong>Optional Services</strong> included in this configuration ({ current_config.optional_components.length } <span>of</span> { release_optional_component_count })</p>
            </div>
        </div>
        <div class="row" if={ current_config.optional_components.length > 0 }>
            <div class="col-sm-12">
            <div class="table-responsive optional-services-table">
            <div class="sample-optional-hidden">
            </div><table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Service</th>
                <th>Maturity <a href="#" id='sort-maturity' onclick={ sortMaturity }><i class="fa fa-sort"></i></a></th>
                <th>Age <a href="#" id='sort-age' onclick={ sortAge }><i class="fa fa-sort"></i></a></th>
                <th>Adoption <a href="#" id='sort-adoption' onclick={ sortAdoption }><i class="fa fa-sort"></i></a></th>
                <th>Details</th>
            </tr>
            </thead>
            <tbody>
                <tr each={ current_config.optional_components }>
                <td>{ code_name }</td>
                <td>{ name }</td>
                <td><div class="service-stat-pill { maturity_points >= 0  && maturity_points <= 1 ? 'red': (maturity_points > 1  && maturity_points <= 3 ? 'orange' : 'green') }">{ maturity_points } <span>of</span> { this.max_maturity_points }</div></td>
                <td><div>{ age } Yrs</div></td>
                <td><div>{ adoption } %</div></td>
                <td><a href="#" onclick={ onComponentDetails }>More Details</a></td>
                </tr>
            </tbody>
            </table>
            </div>
            </div>
        </div>

        <script>

            this.config_samples_types_nav         = opts.config_samples_types_nav;
            this.configuration_types              = opts.configuration_types;
            this.configurations                   = [];
            this.current_config                   = null;
            this.max_maturity_points              = opts.max_maturity_points;
            this.release_core_component_count     = opts.release_core_component_count;
            this.release_optional_component_count = opts.release_optional_component_count;
            this.base_url                         = opts.base_url;
            this.maturity_dir                     = 'desc';
            this.age_dir                          = 'desc';
            this.adoption_dir                     = 'desc';
            var self                              = this;

            this.on('mount', function(){

                self.config_samples_types_nav.on('selected-config-sample-type',function(type_id) {
                      var type = self.configuration_types[type_id];
                      self.configurations = type.configurations;
                      self.current_config = type.configurations[0];
                      $('#config_button_'+self.current_config.id).addClass('active');
                      self.update();
                });
            });

            onConfigurationSelected(e) {

                var configuration_id = $(e.target).attr('data-id');
                $('.config-button').removeClass('active');
                $('#config_button_'+configuration_id).addClass('active');
                for(var i = 0; i < self.configurations.length ; i ++) {
                    var config = self.configurations[i];
                    if( parseInt(config.id) === parseInt(configuration_id) ) {
                        self.current_config = config;
                        self.update();
                        break;
                    }
                }
            }

            onComponentDetails(e) {
                var slug          = e.item.slug;
                var release_slug = e.item.release_slug;
                var url = self.base_url+'releases/'+release_slug+'/components/'+slug;
                window.location = url;
            }

            sortMaturity(e) {
                self.current_config.optional_components.sort(
                    function (a,b) {
                        if(a.maturity_points > b.maturity_points) return self.maturity_dir == 'desc' ? 1 :-1;
                        if(a.maturity_points < b.maturity_points) return self.maturity_dir == 'desc' ? -1 :1;
                        return 0;
                    }
                );
                self.maturity_dir = self.maturity_dir === 'desc' ? 'asc' : 'desc';
                self.update();
            }

            sortAge(e) {
                self.current_config.optional_components.sort(
                    function (a,b) {
                        if(a.age > b.age) return self.age_dir == 'desc' ? 1 :-1;
                        if(a.age < b.age) return self.age_dir  == 'desc' ? -1 :1;
                        return 0;
                    }
                );
                self.age_dir = self.age_dir  === 'desc' ? 'asc' : 'desc';
                self.update();
            }

            sortAdoption(e) {
                self.current_config.optional_components.sort(
                    function (a,b) {
                        if(a.adoption > b.adoption) return self.adoption_dir == 'desc' ? 1 :-1;
                        if(a.adoption < b.adoption) return self.adoption_dir == 'desc' ? -1 :1;
                        return 0;
                    }
                );
                self.adoption_dir = self.adoption_dir === 'desc' ? 'asc' : 'desc';
                self.update();
            }

        </script>

</openstack-config-samples>