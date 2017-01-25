import React from 'react';
import Dropdown from '../ui/Dropdown';
import DropdownItem from '../ui/DropdownItem';
import {connect} from 'react-redux';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';

class ListDropdown extends React.Component {

	componentDidMount() {
		if(this.props.lists.length && this.props.autoSelect && !this.props.lists.find(l => l.member_id == this.props.member_id)) {
			let mine = this.props.lists.find(l => l.mine);

			this.props.goToList(mine ? mine.member_id : this.props.lists[0].member_id);
		}
	}

	componentWillReceiveProps(nextProps) {
        let next_ids = nextProps.lists.map(l => l.id);
        let this_ids = this.props.lists.map(l => l.id);

        if (nextProps.lists.length && next_ids.join(',') != this_ids.join(',')) {
            if(nextProps.autoSelect && !nextProps.lists.find(l => l.member_id == nextProps.member_id)) {
                let mine = nextProps.lists.find(l => l.mine);
                nextProps.goToList(mine ? mine.member_id : nextProps.lists[0].member_id);
            }
        }
	}

	render() {
		const {
			lists,
			member_id,
			selectedText,
			goToList
		} = this.props;
		
		return (
			<Dropdown onItemSelected={goToList} selectedText={selectedText} className="list-dropdown">
				{lists.map(l => (
					<DropdownItem eventKey={l.member_id} key={l.member_id}>{l.list_name}</DropdownItem>
				))}
			</Dropdown>
		);		
	}	
}

export default connect(
	(state, ownProps) => {
        let lists = state.lists.results || [];
        lists = lists.filter(l => !l.is_group);
		let selectedText = lists.filter(l => l.member_id == ownProps.member_id);

		return {
			lists,
			selectedText: selectedText.length === 1 ? selectedText[0].list_name : '--- Select a track chair ---' 
		}
	},

    (dispatch, ownProps) => ({
		goToList(member_id) {
            browserHistory.push(URL.create(`selections/${member_id}`, true));
		}
	})
)(ListDropdown);