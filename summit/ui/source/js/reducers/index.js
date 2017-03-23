import { combineReducers } from 'redux';
import ScheduleReducer from './schedule'

const rootReducer = combineReducers({
    schedule: ScheduleReducer
});

export default rootReducer
