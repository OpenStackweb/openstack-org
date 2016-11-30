import expect from 'expect';
import {videoDetail as reducer} from '../reducers/videoDetail';
import * as Actions from '../actions';
import videoData from './fixtures/videos';
import mockAPI from './mocks/mockAPI';
import mockStore from './mocks/mockStore';

Actions.setHTTPClient(mockAPI);

describe('Video detail tests', () => {
	const initialState = reducer(undefined);
	const videoResult = {
		...videoData.results[0]
	};
	it('adds a loading state when video detail video is requested', () => {
		const result = reducer(initialState, Actions.requestVideoDetail(123))

		expect(result).toEqual({
			...initialState,
			loading: true
		});

	});

	it('will not wipe out the latest video if its the same id', () => {
		const result = reducer({
			...initialState,
			video: {
				...videoResult,
				id: 123
			}
		}, Actions.requestLatestVideo(123));

		expect(result).toEqual({
			...initialState,
			video: {
				...videoResult,
				id: 123
			},
			loading: false
		});
	});

	it('will wipe out the latest video if its a different id', () => {		
		const result = reducer({
			...initialState,
			video: {
				...videoResult,
				id: 123
			}
		}, Actions.requestVideoDetail(124));

		expect(result).toEqual({
			...initialState,
			video: null,
			loading: true
		});
	});

	it('adds video detail from a server response', () => {
		const result = reducer(initialState, Actions.receiveVideoDetail({
			response: videoResult
		}));

		expect(result).toEqual({
			...initialState,
			video: {
				...videoResult				
			},
			loading: false
		});
	});

	it('fetches video detail asyncrhonously', (done) => {
		const expectedActions = [
			Actions.requestVideoDetail(123),
			Actions.receiveVideoDetail({
				response: videoResult
			})
		];
				
		const store = mockStore(initialState);
		store.dispatch(Actions.fetchVideoDetail(123));
		setTimeout(() => {
			expect(store.getActions()).toEqual(expectedActions);
			done();
		}, 0);
	})
});
