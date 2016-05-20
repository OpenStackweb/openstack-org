
/*
* The track chairs API listeners and triggers to fetch server-side data
*/

// Requirements and globals
var reqwest = require('reqwest');
var api = riot.observable();
var url = '/trackchairs/api/v1/';

var load_presentation_req = null;
var load_summit_req       = null;
var load_selections_req   = null;
var load_all_comments_req = null;
/*
*	Listeners
*/


api.on('load-summit-details', function(id){

	var append = 'summit/'
	id = typeof id !== 'undefined' ? id : 'active'
	var append = append + id
	console.log('load-summit-details - id '+id);
	if(load_summit_req !== null) return;
	load_summit_req = reqwest({
	    url: url + append
	  , method: 'get'
	  , success: function (resp) {
			load_summit_req = null;
			console.log('summit-details-loaded');
			api.trigger('summit-details-loaded', resp)
	    }
	})

})


// Request to track chair selections for a particular category
api.on('load-selections', function(categoryId){
	if(load_selections_req !== null) return;
	load_selections_req = reqwest({
	    url: url + 'selections/' + categoryId + '/'
	  , method: 'get'
	  , success: function (resp) {
			load_selections_req = null;
			api.trigger('selections-loaded', resp)
	    }
	})
})


// Request to load presenations
api.on('load-presentations', function(query, categoryId, page){
	if(load_presentation_req !== null) return;
	console.log('load-presentations - query '+query+' categoryId '+categoryId);
	var append = '?'
	if(query) { append = append + 'keyword=' + encodeURI(query) }
	if(categoryId) { append = append + '&category=' + encodeURI(categoryId) }
	if(!page) { page = 1 }
	append = append + '&page=' + page
	load_presentation_req = reqwest({
	    url: url + append
	  , method: 'get'
	  , success: function (resp) {
			load_presentation_req = null;
			console.log('presentations-loaded');
			api.trigger('presentations-loaded', resp)
	    }
	})

})

// Request to pull details for a particular presenation
api.on('load-presentation-details', function(id){

	reqwest({
	    url: url + 'presentation/' + id + '/'
	  , method: 'get'
	  , success: function (resp) {
			api.trigger('presentation-details-loaded', resp)
	    }
	})
})

// Add a comment to the current presentation
api.on('add-comment', function(id, comment){

	reqwest({
	    url: url + 'presentation/' + id + '/comment'
	  , method: 'post'
	  , data: { comment: comment }
	  , success: function (resp) {
			api.trigger('comment-added', resp)
	    }
	})


})

// Select a presentation for a personal list
api.on('select-presentation', function(id){

	reqwest({
	    url: url + 'presentation/' + id + '/select'
	  , method: 'get'
	  , success: function (resp) {
			api.trigger('presentation-selected', resp)
	    }
	})

})

// Unselect (remove presentation from personal list)
api.on('unselect-presentation', function(id){

	reqwest({
	    url: url + 'presentation/' + id + '/unselect'
	  , method: 'get'
	  , success: function (resp) {
			api.trigger('presentation-unselected', resp)
	    }
	})

})

// Select a presentation for a personal list
api.on('group-select-presentation', function(id){

	reqwest({
	    url: url + 'presentation/' + id + '/group/select'
	  , method: 'get'
	  , success: function (resp) {
			api.trigger('presentation-group-selected', resp)
	    }
	})

})

// Unselect (remove presentation from personal list)
api.on('group-unselect-presentation', function(id){


	reqwest({
	    url: url + 'presentation/' + id + '/group/unselect'
	  , method: 'get'
	  , success: function (resp) {
			api.trigger('presentation-group-unselected', resp)
	    }
	})

})

api.on('save-sort-order', function(list_id, sort_order){


	reqwest({
	    url: url + 'reorder/'
	  , method: 'post'
	  , data: {sort_order: sort_order, list_id: list_id}
	  , success: function (resp) {
	  		api.trigger('sort-order-saved', resp)
	    }
	})

})

api.on('suggest-category-change', function(suggestedChange){

	reqwest({
	    url: url + 'presentation/' + suggestedChange.presentation_id + '/category_change/new/?new_cat=' + suggestedChange.new_category
	  , method: 'get'
	  , success: function (resp) {
	  		api.trigger('category-change-suggested', resp)
	    }
	})

})

api.on('approve-change', function(id){

	reqwest({
	    url: url + 'category_change/accept/' + id
	  , method: 'get'
	  , success: function (resp) {
	  		api.trigger('change-approved', resp)
	    }
	})
})

api.on('change-approved', function(){
	api.trigger('load-change-requests')
})

api.on('load-change-requests', function(page){
	if(!page) { page = 1 }
	reqwest({
	    url: url + 'change_requests?page=' + page
	  , method: 'get'
	  , success: function (response) {
	  		api.trigger('change-requests-loaded', response)
	    }
	})
})

api.on('load-all-comments', function(page){
	if(load_all_comments_req !== null) return;
	load_all_comments_req = reqwest({
	    url: url + 'presentation-comments?page='+page
	  , method: 'get'
	  , success: function (resp) {
			load_all_comments_req = null;
	  		api.trigger('all-comments-loaded', resp)
	    }
	})
})


module.exports = api;
