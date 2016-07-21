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
		this.handleKey = this.handleKey.bind(this);
	}

	componentDidMount() {
		document.addEventListener('keyup', this.handleKey);
	}

	componentWillUnmount() {
		document.removeEventListener('keyup', this.handleKey);
	}

	handleSelect(key) {		
		if(key === this.props.presentation.selected) return;
		this.select(key);
	}

	handleKey(e) {
		const {tagName} = e.target;

		if(tagName === 'TEXTAREA' || tagName === 'INPUT') return;

		let key;
		switch (e.keyCode) {
			case 89:
				key = 'selected';
				break;
			case 78:
				key = 'pass';
				break;
			case 73: 
				key = 'maybe'
				break;
		}

		key && this.select(key);
	}

	select(key) {
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
		myList: state.lists.results && state.lists.results.find(l => l.mine)
	}),
	dispatch => ({
		onSelect(presentationID, key, name) {
			dispatch(postMySelection(presentationID, key, name));
		}
	})
)(SelectionButtonBar);