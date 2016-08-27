import expect from 'expect';
import presentations from '../reducers/presentations';
import * as Actions from '../action-creators';
require('array.prototype.find');

const presentationList1 = [
	{
		id: 1,
		title: "Presentation 1"
	},
	{
		id: 2,
		title: "Presentation 2"
	}
];
const presentationList2 = [
	{
		id: 3,
		title: "Presentation 3"
	},
	{
		id: 4,
		title: "Presentation 4"
	}
];

const response1 = {
	presentations: presentationList1,
	selectedPresentation: null,
	total: null,
	initialised: true
};

const response2 = {
	presentations: presentationList2,
	selectedPresentation: null,
	total: null,
	initialised: true
};

const presentation1 = {
	...presentationList1[0],
	abstract: "abstract 1",
	speakers: []
};

describe('Presentations', () => {
  describe('List actions', () => {
    it('should add presentations to an empty list', () => {
      const initialState = {
      	...response1,
      	initialised: false,
      	presentations: []
      };
      const action = Actions.receivePresentations(response1);
      const expectedState = {
      	...response1
      };
      
      expect (
      	presentations(undefined, action)
      ).toEqual(expectedState);
    });
    it('should add presentations to an existing list', () => {
    	const action = Actions.receivePresentations(response2);
    	const initialState = response1;
    	const expectedState = {
    		...initialState,
    		presentations: [
    			...presentationList1,
    			...presentationList2
    		]
    	};
    	
    	expect (
    		presentations(initialState, action)
    	).toEqual(expectedState);
    });
    it('Should clear presentations', () => {
    	const initialState = response1;
    	const action = Actions.clearPresentations();
    	expect (
    		presentations(initialState, action)
    	).toEqual({
    		...response1,
    		presentations: []
    	});
    });
  	it('Should return a default state', () => {
  		const action = {
  			type: 'WATERMELON'
  		};
  		expect (
  			presentations(response1, action)
  		).toEqual(response1);
  	});

  });

  describe('Single presentation actions', () => {
  	it('Should set a presentation vote', () => {
  		const action = Actions.votePresentation(2, 4);
  		const result = presentations(response1, action);

  		expect (result.presentations.length).toBe(2);
  		expect (result.presentations[1].user_vote).toBe(4);
  		expect (result.presentations[0].user_vote).toBe(undefined);
  	});
  	it('Should receive a presentation and cache it in the list', () => {
  		const action = Actions.receivePresentation(presentation1);
  		const result = presentations(response1, action);

  		expect(result.selectedPresentation).toEqual(presentation1);
  		expect(result.presentations[0].abstract).toBe("abstract 1");
  	});
  	it('Should add a comment', () => {
  		const action = Actions.commentPresentation(presentation1.id, 'Test comment');
  		const result = presentations({
  			...response1,
  			selectedPresentation: {...presentation1}
  		}, action);

  		expect(result.selectedPresentation.user_comment).toBeA('object');
  		expect(result.selectedPresentation.user_comment.comment).toBe('Test comment');
  		expect(result.selectedPresentation.showForm).toBe(false);
  	});
  	it('Should edit a comment', () => {
  		const action = Actions.commentPresentation(presentation1.id, 'New comment');
  		const result = presentations({
  			...response1,
  			selectedPresentation: {
  				...presentation1,
  				user_comment: {
  					comment: 'Old comment'
  				}
  			}
  		}, action);

  		expect(result.selectedPresentation.user_comment).toBeA('object');
  		expect(result.selectedPresentation.user_comment.comment).toBe('New comment');
  		expect(result.selectedPresentation.showForm).toBe(false);
  	});
  	it('Should remove a comment', () => {
  		const action = Actions.removeUserComment(presentation1.id);
  		const result = presentations({
  			...response1,
  			selectedPresentation: {
  				...presentation1,
  				user_comment: {
  					comment: 'Old comment'
  				}
  			}
  		}, action);

  		expect(result.selectedPresentation.user_comment).toNotExist();
  	});
  	it('Should not cache a presentation that is not in the list', () => {
  		const newPresentation = {
  			...presentation1,
  			id: 100
  		};
  		const action = Actions.receivePresentation(newPresentation);
  		const result = presentations(response1, action);

  		expect(result.selectedPresentation).toEqual(newPresentation);
  		expect(result.presentations.length).toBe(2);
  		expect(result.presentations.find(p => p.id == 100)).toBeFalsy();

  	});
  	it('Should return a default state', () => {
  		const action = {
  			type: 'WATERMELON',
  			id: 1
  		};
  		expect (
  			presentations(response1.presentations[0], action)
  		).toEqual(response1.presentations[0]);
  	});
  });
});