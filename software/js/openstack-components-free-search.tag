<openstack-components-free-search>

    <div class="col-sm-4 col-xs-6 all-projects-search-wrapper">
        <input type="search" placeholder="Enter a keyword" id="all-projects-search" onkeyup={ doFreeTextSearch }>
        <i class="fa fa-search"></i>
    </div>

    <script>

    this.api              = opts.api;
    var self              = this;
    var last_ajax_request = null;

    doFreeTextSearch(e) {
        var txt = e.target.value;
        console.log(txt);
        var release_id = $('#openstack_releases').val();
        if(self.last_ajax_request != null )
            self.last_ajax_request.abort();
        self.last_ajax_request = self.api.load_components_by_release(release_id, txt);
    }

    </script>

</openstack-components-free-search>