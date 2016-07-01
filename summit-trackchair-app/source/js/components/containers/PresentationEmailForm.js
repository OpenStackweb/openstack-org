import React from 'react';
import {connect} from 'react-redux';
import {postEmail} from '../../actions';

class PresentationEmailForm extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			value: null
		}

		this.updateValue = this.updateValue.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
	}

	updateValue(e) {
		this.setState({
			value: e.target.value
		});
	}

	handleSubmit(e) {
		e.preventDefault();
		this.props.postEmail(
			this.props.presentation.id, 
			{
				body: this.state.value,
				name: window.TrackChairAppConfig.userinfo.name			
			}
		);

		this.setState({
			value: ''
		});
	}

	render() {
		const {presentation} = this.props;

		return (
	        <div className="chat-form">
	        	{presentation.emailSuccess &&
	        		<div className="alert alert-success">
	        			Email sent!
	        		</div>
	        	}
	           <form role="form" onSubmit={this.handleSubmit}>
	              <div className="form-group">
	                 <textarea 
	                 	placeholder="Write your message..."
	                 	value={this.state.value}
	                 	onChange={this.updateValue}
	                 	className="form-control"
	                 	rows={10} />
	              </div>
	              <div className="text-right">
	                 <button type="submit" className="btn btn-sm btn-primary m-t-n-xs">
	                 	{presentation.sending &&
	                 		<strong>Sending...</strong>
	                 	}
	                 	{!presentation.sending &&
	                 		<strong>
	                 			Send email to {presentation.speakers.length}&nbsp;
	                 			{presentation.speakers.length === 1 ? 'speaker' : 'speakers'}
	                 		</strong>
	                 	}
	                 </button>
	              </div>
	           </form>
	        </div>
	    );
	}
}

export default connect (
	state => ({
		presentation: state.detailPresentation
	}),
	dispatch => ({
		postEmail(presentationID, data) {
			dispatch(postEmail(presentationID, data));
		}
	})
)(PresentationEmailForm);