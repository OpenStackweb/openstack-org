import React from 'react';
import ReactDOM from 'react-dom';
import PresentationItem from '../ui/PresentationItem';
import FullHeightScroller from '../ui/FullHeightScroller';
import { connect } from 'react-redux';
import { goToPresentation, requestPresentations } from '../../action-creators';
import Config from '../../utils/Config';
import shallowEqual from 'shallowequal';

class PresentationList extends React.Component {

	constructor (props) {
		super(props);
		this.onPresentationClicked = this.onPresentationClicked.bind(this);
		this.loadMore = this.loadMore.bind(this);
	}


	loadMore (e) {
		e.preventDefault();

		this.props.dispatch(requestPresentations({
			search: this.props.searchQuery || null,
			category: this.props.category ? this.props.category.id : null,
			offset: this.props.presentations.length
		}));
	}


	onPresentationClicked (id) {
		this.props.dispatch(goToPresentation(id));
	}


	componentDidUpdate (prevProps) {		
		// if(
		// 	((this.props.selectedPresentation && prevProps.selectedPresentation) &&
		// 	(this.props.selectedPresentation.id !== prevProps.selectedPresentation.id)) ||
		// 	(this.props.selectedPresentation && !prevProps.selectedPresentation)
		// ) {		
			
		// 	const scroller = ReactDOM.findDOMNode(this.refs.scroller);			
		// 	const item = document.querySelector('.presentation-list li.active').getBoundingClientRect();
		// 	const scrollerTop = scroller.getBoundingClientRect().top;
		// 	if((item.bottom-scrollerTop) > scroller.offsetHeight) {
		// 		console.log('yes', item.top, scroller.offsetHeight, item.offsetHeight,  (item.top + scroller.offsetHeight - item.offsetHeight));
		// 		scroller.scrollTop = (item.top + scroller.offsetHeight - (item.bottom-item.top));
		// 	}
		// 	else {
		// 		console.log('negative', item.bottom-scrollerTop, scroller.offsetHeight);
		// 	}			
		// }
	}


	render () {
		const {
			presentations, 
			total,
			selectedPresentation,
			searchQuery,
			category,
			initialised,
			loading
		} = this.props;
		
		if(!initialised) return <div />;

		const nextCount = Math.min(
			total - presentations.length,
			Config.get('presentationLimit')
		);


		let children = presentations.map(p => (
		    <PresentationItem 
		    	key={p.id} 
		    	selected={selectedPresentation && selectedPresentation.id == p.id}
		    	onPresentationClicked={this.onPresentationClicked}		    	
		    	presentation={p} />	
		));
		if(nextCount > 0) {
			children = children.concat(
	    		<li key="more"><a onClick={this.loadMore}>
	    			Load {nextCount} more ({presentations.length} of {total} total)
	    		</a></li>
		    );
		}  

		return (
			<div>
				{category &&
					<h5>Presentations in "{category.title}"</h5>
				}
				{searchQuery &&
					<h5>Search results for "{searchQuery}"</h5>
				}
			    <FullHeightScroller ref="scroller" pad={30} component="ul" className="presentation-list">
			    	{!!children.length && children}
			    	{!children.length && !loading &&
			    		<li>There are no presentations that match your criteria.</li>
			    	}
			    </FullHeightScroller>
		    </div>
		);
	}
}

export default connect (
	state => {
		
		return {
			...state.presentations,
			category: state.categories.selectedCategory,
			searchQuery: state.router.location.query.q,
			loading: state.ui.loading
		};
	}
)(PresentationList);