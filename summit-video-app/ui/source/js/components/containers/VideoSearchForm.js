import React from 'react';
import { connect } from 'react-redux';
import SimpleSearchForm from '../ui/SimpleSearchForm';
import URL from '../../utils/url';
import { updateSearchText } from '../../actions';
import { routeActions } from 'react-router-redux';

export default connect (
	state => ({
		action: URL.create('search'),
		currentSearch: state.main.search,
		placeholder: 'Search by title, topic, presenter, or event',
		className: 'video-search-form'
	}),
	dispatch => ({
		onSearchTyped (e) {
			dispatch(updateSearchText(e.target.value));
		},
		onSearch (search) {
			dispatch(routeActions.push(URL.create('search', {search})));
		}
	})
)(SimpleSearchForm);