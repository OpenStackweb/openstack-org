import React from 'react';
import {connect} from 'react-redux';
import ButtonGroup from '../ui/ButtonGroup';
import ButtonOption from '../ui/ButtonOption';
import {postMySelection} from '../../actions';
import {Selected, Maybe, Pass} from '../ui/Icons';

class SelectionButtonBar extends React.Component {

	constructor(props) {
		super(props);
		this.handleSelect = this.handleSelect.bind(this);
	}

	handleSelect(key) {		
		if(key === this.props.presentation.selected) return;

		this.props.onSelect(
			this.props.presentation.id,
			key,
			window.TrackChairAppConfig.userinfo.name
		);
	}

	render() {
		let activeKey;
		let canAdd = true;
		const {myList} = this.props;
		const {id, selected} = this.props.presentation;
		if(myList) {
			activeKey = myList.selections.find(s => +s.id === +id) ? 'selected' :
						(myList.maybes.find(s => +s.id === +id) ? 'maybe' : selected);
			canAdd = myList.slots > myList.selections.length;
		}

		return (
			<ButtonGroup onSelect={this.handleSelect} activeKey={activeKey}>
				<ButtonOption disabled={!canAdd} eventKey='selected' className='success'><Selected /> Yes</ButtonOption>
				<ButtonOption eventKey='maybe' className='warning'><Maybe /> Interested</ButtonOption>
				<ButtonOption eventKey='pass' className='danger'><Pass /> No thanks</ButtonOption>
			</ButtonGroup>
		);		
	}
}

export default connect(
	state => ({		
		presentation: state.detailPresentation,
		myList: state.lists.results.find(l => l.mine)
	}),
	dispatch => ({
		onSelect(presentationID, key, name) {
			dispatch(postMySelection(presentationID, key, name));
		}
	})
)(SelectionButtonBar);