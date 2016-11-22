import {createRoutes} from 'react-router/lib/RouteUtils';
import matchRoutes from 'react-router/lib/matchRoutes';
import store from '../store';

export default (routeChildren) => {
	const routeArray = createRoutes(routeChildren);
	return (location) => {
	    matchRoutes(routeArray, location, (error, state) => {
	      if (!error) {
	        store.dispatch({
	          type: 'ROUTER_PARAMS_CHANGE',
	          payload: {
	            location: location,
	            params: state ? state.params : {}
	          }
	        })
	      }
	    })
	};
};