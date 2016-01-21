require('array.prototype.find');

export default (state = {
	selectedCategory: null,
	categories: [],
	initialised: false
}, action) => {
	switch(action.type) {
		case 'RECEIVE_CATEGORIES':			
			return {
				...state,
				initialised: true,
				categories: action.payload
			};

		case '@@reduxReactRouter/routerDidChange':
			const {query} = action.payload.location;
			const {selectedCategory} = state;

			if(!selectedCategory || selectedCategory.id !== query.category) {
				return {
					...state,
					selectedCategory: state.categories.find(c => c.id == query.category)
				}
			}
			
			return state;			
		default:
			return state;
	}
};