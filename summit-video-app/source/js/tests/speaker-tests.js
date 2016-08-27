import expect from 'expect';
import {speakers as reducer} from '../reducers/speakers';
import * as Actions from '../actions';
import speakerData from './fixtures/speakers';
import mockAPI from './mocks/mockAPI';
import mockStore from './mocks/mockStore';

Actions.setHTTPClient(mockAPI);

describe('Speaker tests', () => {
	const initialState = reducer(undefined);	
	it('adds a loading state when speakers are requested', () => {
		const result = reducer(initialState, Actions.requestSpeakers({start: 0}))		
		expect(result).toEqual({
			...initialState,
			loading: true
		});

	});

	it('will not deflate the speaker state', () => {
		const result = reducer(initialState, Actions.requestSpeakers({start: 0}));

		expect(result).toEqual({
			...initialState,
			loading: true
		})
	});

	it('adds speakers from a server response', () => {
		const result = reducer(initialState, Actions.receiveSpeakers({
			response: speakerData
		}));

		expect(result).toEqual({
			...initialState,
			...speakerData,
			loading: false
		});
	});

	it('fetches speakers asyncrhonously', (done) => {
		const expectedActions = [
			Actions.requestSpeakers({start: 0}),
			Actions.receiveSpeakers({
				response: speakerData
			})
		];
				
		const store = mockStore(initialState);
		store.dispatch(Actions.fetchSpeakers({start: 0}));
		setTimeout(() => {
			expect(store.getActions()).toEqual(expectedActions);
			done();
		}, 0);
	});

	it('will paginate speakers', () => {
		let start = reducer(initialState, Actions.receiveSpeakers({
			response: {
				...speakerData
			}
		}));

		expect(start.results.length).toBe(4);
		
		start = {
			...start,
			has_more: true
		};
		
		let result = reducer(start, Actions.receiveSpeakers({
			response: {
				...speakerData
			}
		}));

		expect(result.results.length).toBe(8);

		start = {
			...start,
			has_more: false
		};

		result = reducer(start, Actions.receiveSpeakers({
			response: {
				...speakerData
			}
		}));

		expect(result.results.length).toBe(4);
	});


	it('will clear the state when the letter changes', () => {
		let start = {
			...initialState,
			...speakerData,
			letter: 'Z',
			has_more: true
		};		

		let result = reducer(start, Actions.requestSpeakers({ letter: 'Z', start: 0 }));

		expect(result.letter).toBe('Z');
		expect(result.results.length).toBe(4);

		result = reducer(start, Actions.requestSpeakers({ letter: 'A', start: 0 }));

		expect(result.letter).toBe('A');
		expect(result.results.length).toBe(0);

	});

});