import React from 'react';
import {connect} from 'react-redux';
import CategorySelector from './CategorySelector';
import {checkChair, updateAddChairCategory, submitAddChair, toggleAddChair} from '../../actions';

class AddChairForm extends React.Component {

	constructor (props) {
		super(props);
		this.handleAddChair = this.handleAddChair.bind(this);
	}

	isValid() {
		return this.props.emailCheck && this.props.chairEmail && this.props.chairCategory;
	}

	handleAddChair(e) {
		e.preventDefault();
		if(this.isValid()) {
			this.props.submitAddChair(
				this.props.chairEmail,
				this.props.chairCategory
			);
		}
	}

	render () {
		const {
			emailCheck, 
			checkChair, 
			updateAddChairCategory, 
			chairCategory,
			chairEmail,
			toggleAddChair, 
			loading, 
			message
		} = this.props;
		const valid = this.isValid();
		return (
    	<div className="directory-add-chair">
    		<h3>Add a new chair</h3>
    		<form onSubmit={this.handleAddChair}>
   				<span className="close" onClick={toggleAddChair}>&times;</span>

    		{message &&
    			<div className={`form-message ${message.type}`}>
    				{message.text}
    			</div>
    		}
				<div className="add-chair-field add-chair-email">
					{emailCheck === true && <i className="fa fa-check" />}
					{emailCheck === false && <i className="fa fa-exclamation-circle" />}
					<input type="text" disabled={loading} placeholder="Email address" onChange={checkChair} value={chairEmail} />
				</div>
				<div className="add-chair-field add-chair-category">
					<CategorySelector activeCategory={chairCategory} onSelect={updateAddChairCategory} />
				</div>
				<div className="add-chair-field add-chair-actions">				            			
    				<button disabled={!valid} type="submit">{loading && 'Please wait...'} {!loading && 'Add'}</button>    				
				</div>			            			
    		</form>
    	</div>
    	);
	}
}

export default connect(
	state => ({
		chairEmail: state.directory.chairEmail,
		chairCategory: state.directory.chairCategory,
		emailCheck: state.directory.emailCheck,
		message: state.directory.formMessage,
		loading: state.directory.formLoading

	}),

	dispatch => ({
		checkChair(e) {
			dispatch(checkChair(e.target.value));
		},
		updateAddChairCategory(category) {
			dispatch(updateAddChairCategory(category));
		},
		submitAddChair(email, category) {
			dispatch(submitAddChair({email, category}));
		},
		toggleAddChair(e) {
			e.preventDefault();
			dispatch(toggleAddChair());
		}
	})
)(AddChairForm);