import {createSelector} from 'reselect';

const getPresentationFilter = state => state.presentations.filter;
const getPresentations = state => state.presentations.results || [];

export const getFilteredPresentations = createSelector(
	[getPresentationFilter, getPresentations],
	(filter, presentations) => {
		let filterFunc;
		switch(filter) {
			case 'all':
				filterFunc = p => p.selected !== 'pass';
				break;
			case 'unseen':
				filterFunc = p => !p.viewed;
				break;
			case 'seen':
				filterFunc = p => !!p.viewed;
				break;
			case 'selected':			
				filterFunc = p => p.selected === 'selected';
				break;
			case 'maybe':
				filterFunc = p => p.selected === 'maybe';
				break;
			case 'pass':
				filterFunc = p => p.selected === 'pass';
				break;
			case 'moved':		
				filterFunc = p => !!p.moved_to_category && !p.viewed;
				break;
			case 'team':
				filterFunc = p => p.group_selected;
				break;
			case 'untouched':
				filterFunc = p => !p.selected;
				break;
			default:
				filterFunc = p => p
		}				

		return presentations.filter(filterFunc);
	}
);