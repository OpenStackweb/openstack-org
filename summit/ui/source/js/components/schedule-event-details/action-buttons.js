/**
 * Copyright 2017 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
import React from 'react';
import { connect } from 'react-redux';
import {
    toggleScheduleState,
    toggleFavoriteState,
    removeEventFromRsvp,
    toggleRSVPState
} from '../../actions';

const  LOGIN_REQ_MODAL_TITLE     = "Login Required";
const  LOGIN_REQ_MODAL_BODY      = "You must be logged in to use this function";
const  LOGIN_REQ_MODAL_CANCEL    = "Dismiss";
const  LOGIN_REQ_MODAL_OK        = "Login Now";

class ActionButtons extends React.Component {

    requireLogin(){
        swal({
            title: LOGIN_REQ_MODAL_TITLE,
            text: LOGIN_REQ_MODAL_BODY,
            type:"warning",
            showCloseButton: true,
            showCancelButton: true,
            cancelButtonText: LOGIN_REQ_MODAL_CANCEL,
            confirmButtonText: LOGIN_REQ_MODAL_OK
        }).then(function () {
            window.location = "/Security/login/?BackURL="+encodeURIComponent(window.location);
        });
        return false;
    }

    toggleExternalRSVP(e){
        this.toggleScheduleState(e);
    }

    toggleRSVPState(e){
        const { event, currentUser, toggleRSVPState, removeEventFromRsvp } = this.props;
        var former_state = event.going;
        if(currentUser == null){
          return this.requireLogin();
        }
        if(!former_state && event.rsvp_seat_type == 'FULL'){
            return false;
        }

        if(former_state){
            //unRSVP
            swal({
                title: "Are you sure you want to delete this RSVP?",
                type:"warning",
                showCloseButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete RSVP"
            }).then(function () {
                toggleRSVPState(event);
                removeEventFromRsvp(event);
            });

            return false;
        }
        // RSVP
        // open modal
        this.props.toggleRSVPState(event);
        var modal       = $('#rsvpModal');
        var uri         = new URI( window.location);
        $('#rsvpModalBody').load(uri.segment('rsvp').toString(),function(result){
            initRSVPForm();
        });
        modal.modal('show');
        return false;
    }

    toggleScheduleState(e){
        const { event, currentUser } = this.props;
        if(currentUser == null){
            return this.requireLogin();
        }
        return this.props.toggleScheduleState(event);
    }

    toggleFavoriteState(e){
        const { event, currentUser } = this.props;
        if(currentUser == null){
            return this.requireLogin();
        }
        this.props.toggleFavoriteState(event);
    }

    getOwnRSVPText(){
        const { event } = this.props;
        if(!event.going && event.rsvp_seat_type == 'FULL') return 'RSVP FULL';
        if(!event.going && event.rsvp_seat_type == 'Regular') return 'RSVP';
        if(!event.going && event.rsvp_seat_type == 'WaitList') return 'RSVP (waitlist)';
        return (event.going) ? 'Delete RSVP' : 'RSVP';
    }

    getRSVPIcon(){
        const { event } = this.props;
        if(!event.going && event.rsvp_seat_type == 'FULL' ) return 'glyphicon-warning-sign';
        if(event.going) return 'glyphicon-remove-sign';
        // default (not going)
        return 'glyphicon-ok-circle';
    }

    getFavoriteIcon(){
        const { event } = this.props;
        if(event.favorite) return 'fa-bookmark';
        // default (not favorite)
        return 'fa-bookmark-o';
    }

    getScheduleIcon(){
        const { event } = this.props;
        if(event.going) return 'glyphicon-ok-sign';
        // default (not going)
        return 'glyphicon-ok-circle';
    }

    render() {
        const { event } = this.props;

        return (
            <div className="row info_item">
                <div className="event-actions">
                    { event.has_rsvp && event.rsvp_external &&
                    <button id="btn_rsvp_external"
                            title={ (event.going ? 'UnSchedule': 'RSVP' )}
                            type="button"
                            onClick={ (e) => this.toggleExternalRSVP(e)}
                            className={ "btn btn-primary btn-md active btn-rsvp-own-event btn-action " + ( event.going ? 'btn-action-pressed': 'btn-action-normal')} >
                        <span className="glyphicon glyphicon-ok-circle"></span>&nbsp;<span className="content">{ event.going ? 'Schedule' : 'RSVP'}</span>
                    </button>
                    }
                    { event.has_rsvp && !event.rsvp_external &&
                    <button id="btn_rsvp_own"
                            title={ ( event.going ? 'unRSVP': 'RSVP') }
                            type="button"
                            onClick={ (e) => this.toggleRSVPState(e) }
                            className={`btn btn-md btn-rsvp-own-event btn-action btn-primary ${(!event.going && event.rsvp_seat_type == 'FULL' ? ' btn-full-rsvp': '')}` }>
                        <span className={"glyphicon " + this.getRSVPIcon() }></span>&nbsp;<span
                        className="content">{this.getOwnRSVPText()}</span>
                    </button>
                    }
                    { !event.has_rsvp &&
                        <button
                            id="btn_schedule"
                            title={(event.going ? 'UnSchedule': 'Schedule')}
                            type="button"
                            onClick={ (e) => this.toggleScheduleState(e) }
                            className={"btn btn-primary btn-md active btn-schedule-event btn-action " + ( event.going ? 'btn-action-pressed': 'btn-action-normal')}>
                            <span className={"glyphicon " + this.getScheduleIcon()}></span>&nbsp;<span className="content">Schedule</span>
                        </button>
                    }
                    { event.to_record &&
                    <button id="btn_favorite"
                            title={( event.favorite ? 'Do not Watch Later': 'Watch Later' )}
                            type="button"
                            onClick={ (e) => this.toggleFavoriteState(e)}
                            className={"btn btn-primary btn-md active btn-favorite-event btn-action "+ ( event.favorite ? 'btn-action-pressed':'btn-action-normal' )}>
                        <span><i className={"fa " + this.getFavoriteIcon() } aria-hidden="true"></i></span>&nbsp;<span className="content">Watch Later</span>
                    </button>
                    }
                </div>
                <div id="rsvpModal" className="modal fade" role="dialog">
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header">
                                <button type="button" className="close" data-dismiss="modal">&times;</button>
                                <h4 className="modal-title">RSVP</h4>
                            </div>
                            <div id="rsvpModalBody" className="modal-body">
                                <span>Loading ...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}

function mapStateToProps(state) {
    return {
        event: state.event,
        currentUser: state.currentUser
    }
}

export default connect(mapStateToProps, {
    toggleScheduleState,
    toggleFavoriteState,
    removeEventFromRsvp,
    toggleRSVPState
})(ActionButtons)