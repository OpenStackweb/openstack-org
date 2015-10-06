<openstack-releases-ddl>
    <div class="col-sm-3 col-xs-6 all-projects-select">
    <select id="openstack_releases" class="form-control" onchange={ releaseChanged }  >
        <option each="{ releases }" value="{ id }">{ name }</option>
    </select>
    <i class="fa fa-sort"></i>
    </div>

    this.releases   = opts.releases;
    this.api        = opts.api;
    this.release_id = null;
    var self        = this;

    releaseChanged(e) {
        self.release_id = e.target.value;
        console.log(self.release_id);
        self.api.load_components_by_release(self.release_id);
    }

    this.on('mount', function(){
        self.release_id =  $('#openstack_releases').val();
        console.log(self.release_id);
    });

    getCurrentRelease() {
        return self.release_id;
    }


</openstack-releases-ddl>