import expect from 'expect';
import {summits as reducer} from '../reducers/summits';
import * as Actions from '../actions';
import summitData from './fixtures/summits';
import mockAPI from './mocks/mockAPI';
import mockStore from './mocks/mockStore';

Actions.setHTTPClient(mockAPI);

describe('Summit tests', () => {
	const initialState = reducer(undefined);
	it('adds a loading state when summits are requested', () => {
		const result = reducer(initialState, Actions.requestSummits())

		expect(result).toEqual({
			...initialState,
			loading: true
		});

	});

	it('will not deflate the summit state', () => {
		const result = reducer(initialState, Actions.requestSummits());

		expect(result).toEqual({
			...initialState,
			loading: true
		})
	});


	it('adds summits from a server response', () => {
		const result = reducer(initialState, Actions.receiveSummits({
			response: summitData
		}));

		expect(result).toEqual({
			...summitData,
			loading: false
		});
	});

	it('fetches summits asyncrhonously', (done) => {
		const expectedActions = [
			Actions.requestSummits(),
			Actions.receiveSummits({
				response: summitData
			})
		];
				
		const store = mockStore(initialState);
		store.dispatch(Actions.fetchSummits());
		setTimeout(() => {
			expect(store.getActions()).toEqual(expectedActions);
			done();
		}, 0);
	})

});