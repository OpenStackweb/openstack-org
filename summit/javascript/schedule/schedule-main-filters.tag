<schedule-main-filters>
    <div class="row filter-row">
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search for...">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    </button>
                </span>
            </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
        <div class="col-md-4">
            <select multiple id="ddl_event_types" data-placeholder="Choose an event type">
                <option each={ id, obj in summit.event_types } value="{ id }">{ obj.type }</option>
            </select>
        </div>
        <div class="col-md-4">
            <select multiple id="ddl_tags" data-placeholder="Choose a tag">
                  <option each={ id, obj in summit.tags } value="{ id }">{ obj.name }</option>
            </select>
        </div>
    </div>

    <script>
        this.summit = opts.summit;
        this.api    = opts.api;

        var self    = this;

        this.on('mount', function(){

            $('#ddl_event_types').chosen({  width: '100%'});
            $('#ddl_tags').chosen({ width: '100%'});
        });
    </script>
</schedule-main-filters>