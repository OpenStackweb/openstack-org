(function($){

	$.entwine('ss', function($) {
		$('.ss-gridfield.field.bettergrid').entwine({
			/**
			 * @param {Object} Additional options for jQuery.ajax() call
			 * @param {successCallback} callback to call after reloading succeeded.
			 */

			reload: function(ajaxOpts, successCallback) {
                var self = this, form = this.closest('form'),
                    data = form.find(':input').serializeArray();


                if(!ajaxOpts) ajaxOpts = {};
                if(!ajaxOpts.data) ajaxOpts.data = [];
                ajaxOpts.data = ajaxOpts.data.concat(data);


                // Include any GET parameters from the current URL, as the view state might depend on it.
                // For example, a list prefiltered through external search criteria might be passed to GridField.
                if(window.location.search) {
                    ajaxOpts.data = window.location.search.replace(/^\?/, '') + '&' + $.param(ajaxOpts.data);
                }

                // For browsers which do not support history.pushState like IE9, ss framework uses hash to track
                // the current location for PJAX, so for them we pass the query string stored in the hash instead
                if(!window.history || !window.history.pushState){
                    if(window.location.hash && window.location.hash.indexOf('?') != -1){
                        ajaxOpts.data = window.location.hash.substring(window.location.hash.indexOf('?') + 1) + '&' + $.param(ajaxOpts.data);
                    }
                }

                form.addClass('loading');

                $.ajax($.extend({}, {
                    headers: {"X-Pjax" : 'CurrentField'},
                    type: "POST",
                    url: this.data('url'),
                    dataType: 'html',
                    success: function(data) {
                        // Replace the grid field with response, not the form.
                        // TODO Only replaces all its children, to avoid replacing the current scope
                        // of the executing method. Means that it doesn't retrigger the onmatch() on the main container.
                        self.empty().append($(data).children());

                        // Refocus previously focused element. Useful e.g. for finding+adding
                        // multiple relationships via keyboard.
                        self.attr("tabindex", -1).css('outline', 'none').focus();

                        // Update filter
                        if(self.find('.filter-header').length) {
                            var content;
                            if(ajaxOpts.data[0].filter=="show") {
                                content = '<span class="non-sortable"></span>';
                                self.addClass('show-filter').find('.filter-header').show();
                            } else {
                                content = '<button name="showFilter" class="ss-gridfield-button-filter trigger"></button>';
                                self.removeClass('show-filter').find('.filter-header').hide();
                            }

                            self.find('.sortable-header th:last').html(content);
                        }

                        form.removeClass('loading');
                        if(successCallback) successCallback.apply(this, arguments);
                        self.trigger('reload', self);
                    },
                    error: function(e) {
                        alert(ss.i18n._t('GRIDFIELD.ERRORINTRANSACTION'));
                        form.removeClass('loading');
                    }
                }, ajaxOpts));
			}
		});


	});
}(jQuery));
