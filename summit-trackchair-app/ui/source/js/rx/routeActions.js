import store from '../store';
import {
	fetchPresentations,
	fetchPresentationDetail,
	fetchLists,
	clearPresentations
} from '../actions';
import {browserHistory} from 'react-router';
import {shallowEqual} from 'react-pure-render';
import URL from '../utils/url';
import {q, path, isPath} from './utils';
import state$ from './observableStore';

const {dispatch} = store;

// Default views
const isDefaultView$ = state$
	.filter(state => (
		!q(state, 'category') && state.summit.defaultCategory
	))
	.filter(state => !state.main.params.id);

// Query params
const queryParamsDidChange$ = state$
	.startWith(store.getState())
	.distinctUntilChanged(
		state => state.routing.locationBeforeTransitions.query,
		(queryA, queryB) => {
			return shallowEqual(queryA, queryB)
		}
	);

const browseQueryParamsDidChange$ = queryParamsDidChange$
	.filter(isPath('/browse'));

const selectionsQueryParamsDidChange$ = queryParamsDidChange$
	.filter(isPath('/selections'));

const browseQueryParamsDidChangeCategoryExists$ = browseQueryParamsDidChange$
	.filter(state => !!q(state, 'category'));

const selectionsQueryParamsDidChangeCategoryExists$ = selectionsQueryParamsDidChange$
	.filter(state => !!q(state, 'category'));

// URL Params
const urlParamsDidChange$ = state$
	.startWith(store.getState())
	.distinctUntilChanged(
		state => state.main.params,
		(paramsA, paramsB) => shallowEqual(paramsA, paramsB)
	);

// On default browse page, forward to category
isDefaultView$
	.filter(state => (
		path(state).match(/track-chairs\/$/) ||
		path(state).match(/track-chairs\/browse\/$/)
	))
	.filter(state => !q(state, 'category') && !q(state, 'search'))
	.map(state => state.summit.defaultCategory.id)
	.subscribe((category) => {	
		browserHistory.push(URL.create('browse', {
			category
		}));
	});

// On default selections page, forward to category
isDefaultView$
	.filter(isPath('/selections'))	
	.map(state => state.summit.defaultCategory.id)
	.subscribe((category) => {		
		browserHistory.push(URL.create('selections', {
			category
		}));
	});

// Generic handler for query params changing. Refresh the list.
browseQueryParamsDidChange$.subscribe(state => {
	dispatch(fetchPresentations({
		category: q(state, 'category'),
		keyword: q(state, 'search'),
		page: 1
	}));
});

// Query params change on a detail view. Go back to list and clear it out.
browseQueryParamsDidChange$
	.filter(state => !!state.main.params.id)
	.filter(state => (
		state.detailPresentation && state.detailPresentation.category_id != q(state,'category')
	))
	.subscribe(state => {
		browserHistory.push(URL.create('browse/', {
			...state.routing.locationBeforeTransitions.query
		}));
	});	

// If a category is set, fetch the list
browseQueryParamsDidChangeCategoryExists$
	.subscribe(state => {
		dispatch(fetchLists(q(state, 'category')));
	});

// If the user has set the category to something other than the detail presentation, reset.
browseQueryParamsDidChangeCategoryExists$
	.filter(state => (		
		state.detailPresentation.category_id != q(state, 'category')
	))
	.subscribe(state => {
		dispatch(clearPresentations());
	})

// Category change on selections
selectionsQueryParamsDidChangeCategoryExists$	
	.subscribe(state => {
		dispatch(fetchLists(q(state,'category')));
	});

// On a detail presentation with no ?category, add one.
state$
	.filter(isPath('/browse'))
	.filter(state => !!state.main.params.id)
	.filter(state => !q(state, 'category') && !q(state, 'search'))
	.filter(state => !!state.detailPresentation.id)
	.filter(state => state.detailPresentation.id == state.main.params.id)
	.map(state => state.detailPresentation)
	.subscribe(presentation => {		
		browserHistory.push(URL.create(`browse/${presentation.id}`, {
			category: presentation.category_id
		}));
	});

// Stale detail presentation. Fetch.
urlParamsDidChange$
	.filter(isPath('/browse'))
	.filter(state => !!state.main.params.id)
	.filter(state => state.main.params.id !== state.detailPresentation.id)
	.map(state => state.main.params.id)
	.subscribe(id => {
		dispatch(fetchPresentationDetail(id));
	});