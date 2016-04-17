import expect from 'expect';
import {main as reducer} from '../reducers/main';
import * as Actions from '../actions';

const initialState = reducer(undefined, {});

describe('Main tests', () => {
	it('should update the search query', () => {
		expect(reducer(initialState, Actions.updateSearchText('test'))).toEqual({
			...initialState,
			search: 'test'
		})
	});
	it('should display errors', () => {
		expect(reducer(initialState, Actions.throwError('Big problem'))).toEqual({
			...initialState,
			errorMsg: 'Big problem'
		});
	});
	it('should clear errors', () => {		
		expect(reducer({
			...initialState,
			errorMsg: 'Error'
		}, Actions.clearError()))
		.toEqual({
			...initialState,
			errorMsg: null
		});
	});

});