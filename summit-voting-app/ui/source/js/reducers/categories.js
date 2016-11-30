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

		case 'UPDATE_CATEGORY':			
			const {selectedCategory} = state;
			const id = +action.payload;

			if(!selectedCategory || selectedCategory.id !== id) {
				return {
					...state,
					selectedCategory: state.categories.find(c => c.id === id)
				}
			}
			
			return state;			
		default:
			return state;
	}
};