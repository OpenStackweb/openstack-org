<openstack-releases-ddl>
    <div class="col-sm-3 col-xs-6 all-projects-select">
    <select id="openstack_releases" class="form-control" onchange={ releaseChanged }  >
        <option each="{ releases }" value="{ id }">{ name }</option>
    </select>
    <i class="fa fa-sort"></i>
    </div>

    <script>

        this.releases   = opts.releases;
        this.api        = opts.api;
        this.release_id = null;
        var self        = this;

        releaseChanged(e) {
            self.release_id = e.target.value;
            console.log(self.release_id);
            var adoption   = $("#all-projects-adoption").slider('getValue');
            var maturity   = $("#all-projects-maturity").slider('getValue');
            var age        = $("#all-projects-age").slider('getValue');
            var term       = $('#all-projects-search').val();

            self.api.load_components_by_release(self.release_id, term, adoption, maturity, age);
        }

        this.on('mount', function(){
            self.release_id =  $('#openstack_releases').val();
            console.log(self.release_id);
        });

        getCurrentRelease() {
            return self.release_id;
        }
    </script>

</openstack-releases-ddl>