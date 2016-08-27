<openstack-components-free-search>

    <div class="col-sm-4 col-xs-6 all-projects-search-wrapper">
        <input type="search" placeholder={ ss.i18n._t('Software.ENTER_KEYWORD','Enter a keyword') } id="all-projects-search" onkeyup={ doFreeTextSearch }>
        <i class="fa fa-search"></i>
    </div>

    <script>

    this.api               = opts.api;
    this.last_ajax_request = null;
    var self               = this;

    doFreeTextSearch(e) {
        var term = e.target.value;
        var release_id = $('#openstack_releases').val();
        var adoption   = $("#all-projects-adoption").slider('getValue');
        var maturity   = $("#all-projects-maturity").slider('getValue');
        var age        = $("#all-projects-age").slider('getValue');
        if(self.last_ajax_request != null )
            self.last_ajax_request.abort();
        self.last_ajax_request = self.api.load_components_by_release(release_id, term, adoption, maturity, age);
    }

    </script>

</openstack-components-free-search>