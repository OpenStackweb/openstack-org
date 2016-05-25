import React from 'react';
import {connect} from 'react-redux';
import {fetchPresentationDetail} from '../../actions';

class BrowseDetail extends React.Component {

	componentDidMount() {
		this.props.fetch(this.props.params.id);
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.params.id !== this.props.params.id) {
			this.props.fetch(nextProps.params.id);
		}
	}

    render () {
    	const p = this.props.presentation;
    	
    	if(p.loading) {
    		return <div>loading...</div>
    	}
        return (
            <div>
                <h3>viewing presentation {p.title}</h3>
            </div>
        );
    }
}

export default connect(
	state => ({
		presentation: state.detailPresentation,		
	}),
	dispatch => ({
		fetch(id) {
			dispatch(fetchPresentationDetail(id));
		}
	})
)(BrowseDetail);