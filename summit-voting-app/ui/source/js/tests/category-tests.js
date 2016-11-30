import expect from 'expect';
import categories from '../reducers/categories';
import * as Actions from '../action-creators';

const categoryList = {
	selectedCategory: null,
	initialised: true,
	categories: [
		{
			id: 1,
			title: "Category 1"
		},
		{
			id: 2,
			title: "Category 2"
		}
	]
}
describe('Categories', () => {
  describe('List actions', () => {
    it('should add categories to an empty list', () => {
    	const action = Actions.receiveCategories(categoryList.categories)

    	expect(
    		categories(undefined, action)
    	).toEqual(categoryList);
    });

    it('should return a default state', () => {
    	const action = {
    		type: 'RICK_ROLL',
    		data: []
    	};

    	expect (
    		categories(categoryList, action)
    	).toEqual(categoryList);
    });
  });

  describe('Single category actions', () => {

  	it('Should return a default state', () => {
  		const action = {
  			type: 'RICK_ROLL'
  		}

  		expect (
  			categories(categoryList.categories[0], action)
  		).toEqual(categoryList.categories[0]);
  	});
  })

});
