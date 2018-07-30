import React from 'react';
import {connect} from 'react-redux';
import ButtonGroup from '../ui/ButtonGroup';
import ButtonOption from '../ui/ButtonOption';
import {postMySelection} from '../../actions';
import {Selected, Maybe, Pass, Clear} from '../ui/Icons';

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
        const {myList} = this.props;
        let canAdd;

        if(myList) {
            canAdd = (myList.slots > myList.selections.length);
        }

		if((key === this.props.presentation.selected || !canAdd) ) return;

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
		let activeKey, successClass, warningClass;
		let canAdd = true;
		let canClear = true;
		const {myList} = this.props;
		const {id, selected} = this.props.presentation;

		if(myList) {
			activeKey = selected;
			canAdd = (myList.slots > myList.selections.length);
			canClear = !!selected;
		}

        successClass = 'success';
        warningClass = 'warning';

		return (
			<ButtonGroup onSelect={this.handleSelect} activeKey={activeKey}>
				<ButtonOption disabled={!canAdd} eventKey='selected' className={successClass} ><Selected /> Yes</ButtonOption>
				<ButtonOption eventKey='maybe' className={warningClass}><Maybe /> Interested</ButtonOption>
				<ButtonOption eventKey='pass' className='damyListnger'><Pass /> No thanks</ButtonOption>
				<ButtonOption disabled={!canClear} eventKey='clear' className='damyListnger'><Clear /> Clear</ButtonOption>
			</ButtonGroup>
		);		
	}
}

export default connect(
	state => ({		
		presentation: state.detailPresentation,
		myList: state.lists.results && state.lists.results.find( l => l.mine )
	}),
	dispatch => ({
		onSelect(presentationID, key, name) {
			dispatch(postMySelection(presentationID, key, name));
		}
	})
)(SelectionButtonBar);