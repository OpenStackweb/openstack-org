var API = require('../api/api');
var Cortex = require('./Cortex');


var Backend = {


	findByID: function (dataWrapper, id) {
		if(!dataWrapper.getValue()) return false;

		return dataWrapper.find(function (p) {
			return p.id.getValue() == id;
		});	
	},


	getCategoryByID: function (id) {
		return Backend.findByID(Cortex.summit.categories, id);
	},


	getPresentationByID: function (id) {
		return Backend.findByID(Cortex.presentations, id);
	},


	getPresentations: function (params) {
		API.getPresentations(params, function (response) {
			var currentPresentations = Cortex.presentations.getValue() || [];
			Cortex.presentations.remove();
			Cortex.presentations.set(currentPresentations.concat(response.body.results));
			Cortex.presentationPagination.set({
				hasMore: response.body.has_more,
				total: response.body.total,
				remaining: response.body.remaining
			});
  		});
	},

	resetPresentations: function () {
		Cortex.presentations.remove();
		Cortex.presentations.set([]);
	},


	getSummitData: function (id) {
		API.getSummitData(id, function (response) {
	      Cortex.summit.set(response.body);
		});
	},


	setPresentationVote: function (presID, vote) {
		console.log('vote');
		var pres = Backend.getPresentationByID(presID);
		if(pres) {
			pres.user_vote.set(vote);
		}

		API.setPresentationVote(presID, vote);
	},


	setPresentationActive: function (presID) {
		// First look for it locally
		var pres = Backend.getPresentationByID(presID);

		if(pres) {			
			Cortex.activePresentation.set(pres.getValue());
		}

		// Now sync it with the remote
		API.getPresentation(presID, function (response) {
			if(pres) {
				pres.set(response.body);	
			}
			Cortex.activePresentation.set(response.body);			
		});

	},

	viewPresentation: function (presID) {
		API.addPresentationView(presID);
	}

};

module.exports = Backend;