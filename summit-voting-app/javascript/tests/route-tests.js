import url from '../utils/url';
import Config from '../utils/Config';
import expect from 'expect';
Config.set('baseURL','/one/two/three');

let window = {
	location: {
		pathname: '/one/two/three/four/five'
	}
};

describe('URL functions', () => {
	it('should go to a relative URL with an array', () => {
		let result = url(['six','seven'], null, window);
		expect(result).toBe('one/two/three/six/seven');		
	});
	it('should go to a relative URL with a string', () => {
		let result = url('six/seven', null, window);
		expect(result).toBe('one/two/three/six/seven');
	});
	it('should add a query string to a url', () => {
		let result = url(['six','seven'], {foo: 'bar'}, window);
		expect(result).toBe('one/two/three/six/seven?foo=bar');		
	});
	it('should add a query string to the current path', () => {
		let result = url(null, {foo: 'bar'}, window);
		expect(result).toBe('one/two/three/four/five?foo=bar');		
	});
});
