<component-capabilities-ddl>
    <!-- Projects Subnav -->
    <div class="row component-capability-filter">
        <div class="col-md-6 form-group form-inline">
            <label> Filter by Capability: </label>&nbsp;&nbsp;
            <select class="form-control form-control-sm" onchange={ selectCapability } value={ filter_tag }>
                <option value="">None</option>
                <optgroup each="{ cap in capabilities }" label="{ cap.name }">
                    <option each="{ tag in cap.tags }">{ tag.name }</option>
                </optgroup>
            </select>
        </div>
    </div>

    <script>

        this.ctrl           = riot.observable();
        this.api            = opts.api;
        this.filter_tag     = "";
        var self            = this;

        this.on('mount', function(){

            $( document ).ready(function() {
                self.handleDeepLink();
            });

        });

        selectCapability(e) {
            e.preventDefault();
            self.clickCapability(e.target.value);
        }

        clickCapability(tag_name) {
            window.location.hash = tag_name;
            self.api.trigger('filter-capability', tag_name)
        }

        handleDeepLink() {
            var hash = window.location.hash.replace('#', '');
            if(!$.isEmptyObject(hash) ) {
                self.clickCapability(hash);
            }
        }

        self.api.on('filter-capability', function(tag_name) {
              self.filter_tag = tag_name;
              self.update();
        });

        module.exports = this.ctrl;
    </script>

</component-capabilities-ddl>
