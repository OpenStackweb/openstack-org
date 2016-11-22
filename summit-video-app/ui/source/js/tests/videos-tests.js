import expect from 'expect';
import {videos as reducer} from '../reducers/videos';
import * as Actions from '../actions';
import videoData from './fixtures/videos';
import summitData from './fixtures/summits';
import speakerData from './fixtures/speakers';
import mockAPI from './mocks/mockAPI';
import mockStore from './mocks/mockStore';

Actions.setHTTPClient(mockAPI);

describe('Videos tests', () => {
	const initialState = reducer(undefined);
	describe('All videos', () => {
		it('adds a loading state when all videos are requested', () => {		
			const result = reducer(initialState, Actions.requestAllVideos());

			expect(result).toEqual({
				...initialState,
				allVideos: {
					...initialState.allVideos,
					loading: true
				}			
			});

		});

		it('receives all videos from a server response', () => {
			const result = reducer(initialState, Actions.receiveAllVideos({
				response: {
					...videoData
				}
			}));

			expect(result).toEqual({
				...initialState,
				allVideos: {
					...initialState.allVideos,
					...videoData,
					loading: false
				}
			});
		});

		it('fetches all videos asyncrhonously', () => {
			const expectedActions = [
				Actions.requestAllVideos(),
				Actions.receiveAllVideos({
					response: {
						...videoData
					}
				})
			];
					
			const store = mockStore(initialState);
			store.dispatch(Actions.fetchAllVideos());
			setTimeout(() => {
				expect(store.getActions()).toEqual(expectedActions);
				done();
			}, 0);

		});

		it('will paginate videos', () => {
			let start = reducer(initialState, Actions.receiveAllVideos({
				response: {
					...videoData
				}
			}));
			expect(start.allVideos.results.length).toBe(4);
			start = {
				...start,
				allVideos: {
					...start.allVideos,
					has_more: true
				}
			};
			let result = reducer(start, Actions.receiveAllVideos({
				response: {
					...videoData
				}
			}));

			expect(result.allVideos.results.length).toBe(8);

			start = {
				...start,
				allVideos: {
					...start.allVideos,
					has_more: false
				}
			};

			result = reducer(start, Actions.receiveAllVideos({
				response: {
					...videoData
				}
			}));

			expect(result.allVideos.results.length).toBe(4);
		});
	});


	describe('Popular videos', () => {
		it('adds a loading state when popular videos are requested', () => {		
			const result = reducer(initialState, Actions.requestPopularVideos());

			expect(result).toEqual({
				...initialState,
				popularVideos: {
					results: [...initialState.popularVideos.results],
					loading: true
				}			
			});

		});

		it('receives popular videos from a server response', () => {
			const result = reducer(initialState, Actions.receivePopularVideos({
				response: {
					...videoData
				}
			}));

			expect(result).toEqual({
				...initialState,
				popularVideos: {
					results: [...videoData.results],
					loading: false
				}
			});
		});

		it('fetches popular videos asyncrhonously', () => {
			const expectedActions = [
				Actions.requestPopularVideos(),
				Actions.receivePopularVideos({
					response: {
						...videoData
					}
				})
			];
					
			const store = mockStore(initialState);
			store.dispatch(Actions.fetchPopularVideos());
			setTimeout(() => {
				expect(store.getActions()).toEqual(expectedActions);
				done();
			}, 0);

		});

	});


	describe('Highlighted videos', () => {
		it('adds a loading state when highlighted videos are requested', () => {		
			const result = reducer(initialState, Actions.requestHighlightVideos());

			expect(result).toEqual({
				...initialState,
				highlightedVideos: {
					results: [...initialState.highlightedVideos.results],
					loading: true
				}			
			});

		});

		it('receives highlighted videos from a server response', () => {
			const result = reducer(initialState, Actions.receiveHighlightVideos({
				response: {
					...videoData
				}
			}));

			expect(result).toEqual({
				...initialState,
				highlightedVideos: {
					results: [...videoData.results],
					loading: false
				}
			});
		});

		it('fetches highlighted videos asyncrhonously', () => {
			const expectedActions = [
				Actions.requestHighlightVideos(),
				Actions.receiveHighlightVideos({
					response: {
						...videoData
					}
				})
			];
					
			const store = mockStore(initialState);
			store.dispatch(Actions.fetchHighlightVideos());
			setTimeout(() => {
				expect(store.getActions()).toEqual(expectedActions);
				done();
			}, 0);

		});
	});

	describe('Videos for a summit', () => {
		it('adds a loading state when videos for a summit are requested', () => {		
			const result = reducer(initialState, Actions.requestSummitVideos(123));

			expect(result).toEqual({
				...initialState,
				summitVideos: {
					...initialState.summitVideos,
					results: [...initialState.summitVideos.results],					
					loading: true
				}			
			});
		});

		it('receives videos for a summit from a server response', () => {
			const result = reducer(initialState, Actions.receiveSummitVideos({
				response: {
					...videoData,
					summit: {
						...summitData.results[0]
					}
				}
			}));

			expect(result).toEqual({
				...initialState,
				summitVideos: {
					...initialState.summitVideos,
					...videoData,
					summit: {
						...summitData.results[0]
					},
					loading: false
				}
			});
		});

		it('fetches videos for a summit asyncrhonously', () => {
			const expectedActions = [
				Actions.requestSummitVideos(123),
				Actions.receiveSummitVideos({
					response: {
						...videoData,
						summit: {
							...summitData.results[0]
						}
					}
				})
			];
					
			const store = mockStore(initialState);
			store.dispatch(Actions.fetchSummitVideos(123));
			setTimeout(() => {
				expect(store.getActions()).toEqual(expectedActions);
				done();
			}, 0);

		});

		it('will paginate videos', () => {
			let start = reducer(initialState, Actions.receiveSummitVideos({
				response: {
					...videoData
				}
			}));
			expect(start.summitVideos.results.length).toBe(4);
			start = {
				...start,
				summitVideos: {
					...start.summitVideos,
					has_more: true
				}
			};
			let result = reducer(start, Actions.receiveSummitVideos({
				response: {
					...videoData
				}
			}));

			expect(result.summitVideos.results.length).toBe(8);

			start = {
				...start,
				summitVideos: {
					...start.summitVideos,
					has_more: false
				}
			};

			result = reducer(start, Actions.receiveSummitVideos({
				response: {
					...videoData
				}
			}));

			expect(result.summitVideos.results.length).toBe(4);
		});
	});

	describe('Videos for a speaker', () => {
		it('adds a loading state when videos for a speaker are requested', () => {		
			const result = reducer(initialState, Actions.requestSpeakerVideos(123));

			expect(result).toEqual({
				...initialState,
				speakerVideos: {
					...initialState.speakerVideos,
					results: [...initialState.speakerVideos.results],					
					loading: true
				}			
			});
		});

		it('receives videos for a speaker from a server response', () => {
			const result = reducer(initialState, Actions.receiveSpeakerVideos({
				response: {
					...videoData,
					speaker: {
						...speakerData.results[0]
					}
				}
			}));

			expect(result).toEqual({
				...initialState,
				speakerVideos: {
					...initialState.speakerVideos,
					...videoData,									
					speaker: {
						...speakerData.results[0]
					},
					loading: false
				}
			});
		});

		it('fetches videos for a speaker asyncrhonously', () => {
			const expectedActions = [
				Actions.requestSpeakerVideos(123),
				Actions.receiveSpeakerVideos({
					response: {
						...videoData,
						speaker: {
							...speakerData.results[0]
						}
					}
				})
			];
					
			const store = mockStore(initialState);
			store.dispatch(Actions.fetchSpeakerVideos(123));
			setTimeout(() => {
				expect(store.getActions()).toEqual(expectedActions);
				done();
			}, 0);

		});


		it('will paginate videos', () => {
			let start = reducer(initialState, Actions.receiveSpeakerVideos({
				response: {
					...videoData
				}
			}));
			expect(start.speakerVideos.results.length).toBe(4);
			start = {
				...start,
				speakerVideos: {
					...start.speakerVideos,
					has_more: true
				}
			};
			let result = reducer(start, Actions.receiveSpeakerVideos({
				response: {
					...videoData
				}
			}));

			expect(result.speakerVideos.results.length).toBe(8);

			start = {
				...start,
				speakerVideos: {
					...start.speakerVideos,
					has_more: false
				}
			};

			result = reducer(start, Actions.receiveSpeakerVideos({
				response: {
					...videoData
				}
			}));

			expect(result.speakerVideos.results.length).toBe(4);
		});

	});


	describe('Search videos', () => {
		const searchState = {
			...initialState,
			searchVideos: {
				...initialState.searchVideos,
				results: {
					titleMatches: [...videoData.results],
					speakerMatches: [...videoData.results],
					topicMatches: [...videoData.results]
				}
			}
		};
		const searchResponse = {
			results: {
				...searchState.searchVideos.results
			},
			summit: null,
			speaker: null
		};

		it('adds a loading state when a video search is requested', () => {		
			const result = reducer(initialState, Actions.requestSearchVideos('test'));

			expect(result).toEqual({
				...initialState,
				searchVideos: {
					...initialState.searchVideos,
					loading: true
				}			
			});
		});

		it('clears state when a new search is run', () => {
			const result = reducer(searchState, Actions.requestSearchVideos('test'));

			expect(result).toEqual({
				...initialState,
				searchVideos: {
					...initialState.searchVideos,
					results: null,
					loading: true
				}			
			});			
		})

		it('receives videos for a search from a server response', () => {
			const result = reducer(initialState, Actions.receiveSearchVideos({
				response: {
					...searchResponse
				}
			}));

			expect(result).toEqual({
				...initialState,
				searchVideos: {	
					...initialState.searchVideos,				
					results: {
						...searchResponse.results
					},
					loading: false
				}
			});
		});

		it('fetches videos for a search asyncrhonously', () => {
			const expectedActions = [
				Actions.requestSearchVideos('test'),
				Actions.receiveSearchVideos({
					response: {
						...searchResponse
					}
				})
			];
					
			const store = mockStore(initialState);
			store.dispatch(Actions.fetchSearchVideos('test'));
			setTimeout(() => {
				expect(store.getActions()).toEqual(expectedActions);
				done();
			}, 0);

		});

		it('changes search tabs', () => {
			let start = {
				...initialState,
				searchVideos: {
					...searchState,
					activeTab: 'titleMatches'
				}
			};

			let result = reducer(start, Actions.updateSearchTab('topicMatches'));
			expect(result.searchVideos.activeTab).toBe('topicMatches');
		});

		it('will persist the tab', () => {
			let start = {
				...initialState,
				searchVideos: {
					...searchState,
					activeTab: 'topicMatches'
				}
			};

			let result = reducer(start, Actions.receiveSearchVideos({
				response: {
					...searchResponse
				}
			}));

			expect(result.searchVideos.activeTab).toBe('topicMatches');
		});

		it('will automatically choose a new tab if the selected tab is empty', () => {
			let start = {
				...initialState,
				searchVideos: {
					...searchState,
					activeTab: 'topicMatches'
				}
			};

			let result = reducer(start, Actions.receiveSearchVideos({
				response: {
					...searchResponse,
					results: {
						...searchResponse.results,
						topicMatches: []
					}
				}
			}));

			expect(result.searchVideos.activeTab).toBe('titleMatches');
		})

	});
});