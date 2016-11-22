import expect from 'expect';
import {video as reducer} from '../reducers/video';
import * as Actions from '../actions';
import videoData from './fixtures/videos';
import mockAPI from './mocks/mockAPI';
import mockStore from './mocks/mockStore';

Actions.setHTTPClient(mockAPI);

describe('Video tests', () => {
	const initialState = reducer(undefined);
	describe('Featured video', () => {
		const videoResult = {
			...videoData.results[0]
		};
		it('adds a loading state when the featured video is requested', () => {
			const result = reducer(initialState, Actions.requestFeaturedVideo())

			expect(result).toEqual({
				...initialState,
				featuredVideo: {
					...initialState.featuredVideo,
					loading: true
				}
			});

		});

		it('will not wipe out the featured video state once inflated', () => {
			const result = reducer({
				...initialState,
				featuredVideo: {
					...videoResult
				}
			}, Actions.requestFeaturedVideo());

			expect(result).toEqual({
				...initialState,
				featuredVideo: {
					...videoResult,
					loading: true
				}			
			});
		});

		it('adds a featured video from a server response', () => {
			const result = reducer(initialState, Actions.receiveFeaturedVideo({
				response: videoResult
			}));

			expect(result).toEqual({
				...initialState,
				featuredVideo: {
					...videoResult,
					loading: false
				}			
			});
		});

		it('fetches a featured video asyncrhonously', (done) => {
			const expectedActions = [
				Actions.requestFeaturedVideo(),
				Actions.receiveFeaturedVideo({
					response: videoResult
				})
			];
					
			const store = mockStore(initialState);
			store.dispatch(Actions.fetchFeaturedVideo());
			setTimeout(() => {
				expect(store.getActions()).toEqual(expectedActions);
				done();
			}, 0);
		})
	});



	describe('Latest video', () => {
		const videoResult = {
			...videoData.results[0]
		};
		it('adds a loading state when the latest video is requested', () => {
			const result = reducer(initialState, Actions.requestLatestVideo())

			expect(result).toEqual({
				...initialState,
				latestVideo: {
					...initialState.latestVideo,
					loading: true
				}
			});

		});

		it('will not wipe out the latest video state once inflated', () => {
			const result = reducer({
				...initialState,
				latestVideo: {
					...videoResult
				}
			}, Actions.requestLatestVideo());

			expect(result).toEqual({
				...initialState,
				latestVideo: {
					...videoResult,
					loading: true
				}			
			});
		});

		it('adds the latest video from a server response', () => {
			const result = reducer(initialState, Actions.receiveLatestVideo({
				response: videoResult
			}));

			expect(result).toEqual({
				...initialState,
				latestVideo: {
					...videoResult,
					loading: false
				}			
			});
		});

		it('fetches the latest video asyncrhonously', (done) => {
			const expectedActions = [
				Actions.requestLatestVideo(),
				Actions.receiveLatestVideo({
					response: videoResult
				})
			];
					
			const store = mockStore(initialState);
			store.dispatch(Actions.fetchLatestVideo());
			setTimeout(() => {
				expect(store.getActions()).toEqual(expectedActions);
				done();
			}, 0);
		})
	});

});