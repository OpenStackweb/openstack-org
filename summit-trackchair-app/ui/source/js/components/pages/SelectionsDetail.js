import React from 'react';
import {connect} from 'react-redux';
import SelectionsList from '../views/SelectionsList';
import StaticSelectionsList from '../views/StaticSelectionsList';
import Wave from '../ui/loaders/Wave';
import AnimateCSS from '../ui/animate/AnimateCSS';
import SlideInLeft from '../ui/animate/SlideInLeft';
import SlideOutRight from '../ui/animate/SlideOutRight';
import {toggleMaybeDrawer, postReorganise} from '../../actions';
import {Maybe, Selected, Team} from '../ui/Icons';
import SelectionStats from '../ui/SelectionStats';

class SelectionsDetail extends React.Component {

	constructor (props) {
		super(props);
		this.handleColumnChange = this.handleColumnChange.bind(this);
	}

	handleColumnChange(item, fromList, fromIndex, toList, toIndex) {
		// can't drag out of the team
		if(fromList === 'team') {return;}

		let existing = this.props[fromList].find(i => +i.id === +item.id);
		
		// Team is a clone. Shouldn't remove from the other piles
		if(existing && toList !== 'team') {			
			// Remove from the old list
			this.props.reorganiseSelections(
				this.props.list.id,
				fromList,
				this.props[fromList].filter(i => i.id !== existing.id)
			);
		}


		
		if(this.props[toList].find(i => +i.id === +item.id)) {
			// List already has it.
			return;
		}

		const newList = [
			...this.props[toList], 
			{
				id: item.id,
				presentation: {
					...item.presentation
				},
				order: item.order
			}
		];
		this.props.reorganiseSelections(
			toList === 'team' ? this.props.teamList.id : this.props.list.id,
			toList,
			newList.move((newList.length-1), toIndex)
		);
	}

    render () {
    	const {
    		selections,
    		maybes,
    		team,
    		list,    		
    		teamList,
    		sessionLimit,
    		altLimit,
    		canEditIndividual,
    		canEditTeam,
    		toggleDrawer,
    		updateDragState,
    		myListFull,
    		teamListFull
    	} = this.props;

        if(this.props.loading) {
            return <Wave />
        }

    	if(!list) {
    		return (
                <div className="row">
                    <div className="col-md-12 empty-selections">
                        There are no track chairs lists for this category and class.
                    </div>
                </div>
            );
    	}

    	const IndividualListComponent = canEditIndividual ? SelectionsList : StaticSelectionsList;
    	const TeamListComponent = canEditTeam ? SelectionsList : StaticSelectionsList;

        return (
        	<div className="row">        	
        		<div className="col-md-4 column-maybe">
					<div className="wrapper wrapper-content">
					   <div className="ibox">
					      <div className="ibox-content">
					         <div className="row">
					            <div className="col-lg-12">
					            	<h3><Maybe /> Interested ({maybes.length})</h3>
					            	{maybes.length > 0 && <SelectionStats selections={maybes} />}
					            	<IndividualListComponent 
					            		onColumnChange={this.handleColumnChange}
					            		selections={maybes} 
					            		list={list} 
					            		showRank={false}					            		
					            		column='maybes' />
					            </div>
					         </div>
					      </div>
					   </div>
					</div>
        		</div>
        		<div className="col-md-4 column-selected">
					<div className="wrapper wrapper-content">
					   <div className="ibox">
					      <div className="ibox-content">
					         <div className="row">
					            <div className="col-lg-12">
					            	<h3><Selected /> Selections ({selections.length} / {sessionLimit})</h3>
					            	{myListFull && <span className="label label-full label-danger">FULL</span>}
					            	{selections.length > 0 && <SelectionStats selections={selections} />}
					            	<IndividualListComponent
					            		onColumnChange={this.handleColumnChange} 
					            		selections={selections} 
					            		list={list}
					            		acceptNew={!myListFull}
					            		column='selections' />
					            </div>
					         </div>
					      </div>
					   </div>
					</div>
        		</div>
        		<div className="col-md-4 column-team">
					<div className="wrapper wrapper-content">
					   <div className="ibox">
					      <div className="ibox-content">
					         <div className="row">
					            <div className="col-lg-12">
					            	<h3><Team /> Team list ({team.length} / {sessionLimit})</h3>
					            	{teamListFull && <span className="label label-full label-danger">FULL</span>}
					            	<SelectionStats selections={team} />
					            	<TeamListComponent 
					            		onColumnChange={this.handleColumnChange}
					            		selections={team} 
					            		list={teamList}
					            		acceptNew={!teamListFull}
					            		column='team'/>
					            </div>
					         </div>
					      </div>
					   </div>
					</div>
        		</div>
        	</div>
        );
    }
}

export default connect(
	(state, ownProps) => {

        const category = state.summit.data.categories.find(c => (
			c.id == state.routing.locationBeforeTransitions.query.category
		));
		const list = state.lists.results.find(l => l.member_id == ownProps.params.member_id);
		const teamList = state.lists.results.find(l => l.is_group);
		const sessionLimit = (list) ? list.slots : 0;
		const altLimit = (list) ? list.alternates : 0;
		const canEditIndividual = list && list.can_edit;
		const canEditTeam = category && category.user_is_chair;

		return {
			selections: list ? list.selections : null,
			team: teamList ? teamList.selections : null,
			maybes: list ? list.maybes : null,
			sessionLimit,
			altLimit,
			list,
			teamList,
			canEditIndividual,
			canEditTeam,
			teamListFull: (teamList.selections.length >= sessionLimit),
			myListFull: (list) ? (list.selections.length >= sessionLimit) : false,
            loading: state.lists.loading
		}
		
	},

	dispatch => ({
		reorganiseSelections(listID, collection, newOrder) {
			dispatch(postReorganise(listID, collection, newOrder));
		}
	})
)(SelectionsDetail);