import expect from 'expect';
import {main as reducer} from '../reducers/main';
import * as Actions from '../actions';

const initialState = reducer(undefined, {});

describe('Main tests', () => {

	it('throws an error', () => {
		const initialState = reducer();
		const nextState = reducer(initialState, Actions.throwError('test'));

		expect(nextState.errorMsg).toBe('test');

	});
});