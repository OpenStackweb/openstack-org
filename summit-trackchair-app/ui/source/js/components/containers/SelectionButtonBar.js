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
        const {myList, myLightningList} = this.props;
        let canAdd,canAddLightning;

        if(myLightningList) {
            canAddLightning = (myLightningList.slots > myLightningList.selections.length);
        }

        if(myList) {
            canAdd = (myList.slots > myList.selections.length);
        }

		if((key === this.props.presentation.selected || !canAdd) &&
            (key === this.props.presentation.lightning_selected || !canAddLightning)) return;

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
		let activeKey, activeKeyLightning, successClass, warningClass;
		let canAdd = true;
		let canAddLightning = true;
        let canAddGlobal = true;
		const {myList, myLightningList} = this.props;
		const {id, selected, lightning_selected, lightning, lightning_wannabe} = this.props.presentation;
		if(myList) {
			activeKey = selected;
			canAdd = (myList.slots > myList.selections.length);
		}

        if(myLightningList) {
            activeKeyLightning = lightning_selected;
            canAddLightning = (myLightningList.slots > myLightningList.selections.length);
        }

        canAddGlobal = (lightning) ? canAddLightning : (lightning_wannabe ? (canAdd || canAddLightning) : canAdd);

        successClass = 'success';
        warningClass = 'warning';
        if (lightning_wannabe) {
            if ((activeKey == 'selected') ^ (activeKeyLightning == 'selected')) {
                successClass = 'success partial';
                activeKey = false;
            }
            if ((activeKey == 'maybe') ^ (activeKeyLightning == 'maybe')) {
                warningClass = 'warning partial';
                activeKey = false;
            }
        }


		return (
			<ButtonGroup onSelect={this.handleSelect} activeKey={activeKey}>
				<ButtonOption disabled={!canAddGlobal} eventKey='selected' className={successClass} ><Selected /> Yes</ButtonOption>
				<ButtonOption eventKey='maybe' className={warningClass}><Maybe /> Interested</ButtonOption>
				<ButtonOption eventKey='pass' className='damyListnger'><Pass /> No thanks</ButtonOption>
			</ButtonGroup>
		);		
	}
}

export default connect(
	state => ({		
		presentation: state.detailPresentation,
		myList: state.lists.results && state.lists.results.find(l => l.mine && !l.is_lightning),
		myLightningList: state.lists.results && state.lists.results.find(l => l.mine && l.is_lightning)
	}),
	dispatch => ({
		onSelect(presentationID, key, name) {
			dispatch(postMySelection(presentationID, key, name));
		}
	})
)(SelectionButtonBar);