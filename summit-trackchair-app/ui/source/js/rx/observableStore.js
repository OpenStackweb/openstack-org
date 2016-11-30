import {observableFromStore} from 'redux-rx';
import store from '../store';

const state$ = observableFromStore(store);

export default state$;
