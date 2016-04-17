import React from 'react';
import { connect } from 'react-redux';
import { clearError } from '../../actions';
import VerticalSlide from '../ui/SlideAnimation';

class ErrorMessage extends React.Component {

	constructor (props) {
		super(props);
		this._timeout = null;
		this.clearErrorTimeout = this.clearErrorTimeout.bind(this);
	}
	
	clearErrorTimeout () {
		if(this._timeout) {
			window.clearTimeout(this._timeout);
		}
		this._timeout = window.setTimeout(this.props.clearError, 5000);
	}

	render () {
		const {error} = this.props;
		return (
			<VerticalSlide>
			{error && 
				<div className="video-error" ref={this.clearErrorTimeout}> 
					{error}
					<a onClick={this.props.clearError}>&times;</a>
				</div>
			}
			</VerticalSlide>		
		);
	}

}

export default connect (
	state => ({
		error: state.main.errorMsg
	}),

	dispatch => ({
		clearError () {
			dispatch(clearError());
		}
	})
)(ErrorMessage);