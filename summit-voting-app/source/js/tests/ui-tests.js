import expect from 'expect';
import ui from '../reducers/ui';
import * as Actions from '../action-creators';

describe('Global UI state', () => {
  describe('XHRs', () => {
    it('should show a loading state', () => {
    	const action = Actions.beginXHR();

    	const result = ui({loading: false}, action);
    	expect(result.loading).toBe(true);
    });
  });
  describe('Error messages', () => {
    it('should throw an error', () => {
    	const action = Actions.throwError('Broken');
    	const result = ui(undefined, action);

    	expect(result.errorMsg).toBe('Broken');
    });
    it('should clear an error', () => {
    	const action = Actions.clearError();
    	const result = ui(undefined, action);
    	
    	expect(result.errorMsg).toBeFalsy();
    })
  })
});
