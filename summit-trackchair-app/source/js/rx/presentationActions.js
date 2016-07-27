import state$ from './observableStore';
import {q, isPath} from './utils';
import {fetchPresentations} from '../actions';
import store from '../store';

const {dispatch} = store;

const presentationsNeedMore$ = state$
	.filter(isPath('/browse'))
	.filter(state => !state.presentations.loading)
	.filter(state => state.presentations.has_more);

presentationsNeedMore$.subscribe(state => {
	dispatch(fetchPresentations({
		category: state.routing.locationBeforeTransitions.query.category,
		page: +state.presentations.page+1
	}))
})