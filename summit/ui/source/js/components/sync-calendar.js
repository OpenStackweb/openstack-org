import React, {Component} from 'react';
import { connect } from 'react-redux';
import {Collapse} from 'react-collapse';
import 'sweetalert2/dist/sweetalert2.css';
import swal from 'sweetalert2';
import {
    ScheduleProps,
    syncCalendar,
} from '../actions';

class SyncCal extends React.Component {
    constructor(props) {
        super(props);

        ScheduleProps.summit = props.summit

        this.state = {
            cal_type: 'google',
            ios_user: '',
            ios_pass: '',
        };
    }

    handleCalChange(event, cal_type) {

        this.setState({cal_type: cal_type, ios_user: '', ios_pass: ''});
    }

    handleUserChange(event) {
        this.setState({ios_user: event.target.value});
    }

    handlePassChange(event) {
        this.setState({ios_pass: event.target.value});
    }

    handleSubmit(event) {
        event.preventDefault();
        let cal_type = this.state.cal_type;
        console.log(`handleSubmit - cal_type ${cal_type}`);
        if (cal_type) {
            if (cal_type != 'apple' || (this.state.ios_user && this.state.ios_pass)) {
                this.props.syncCalendar(cal_type, this.state.ios_user, this.state.ios_pass);
            }
        } else {
            swal('Validation error', 'Please complete all fields', 'warning');
        }
    }

    render() {
        return (
            <div>
                <h4>
                All events from your calendar will be automatically synched in the background with your calendar of choice.
                If a room, event time, name changes, your calendar will update in the background.
                </h4>
                <form onSubmit={e => this.handleSubmit(e)}>
                    <div className="row">
                        <div className="form-group col-md-4">
                            <label>Please select your calendar type:</label>
                            <br/>
                            <div className="cal_box">
                                <div title="Google" className={"cal_image google " + (this.state.cal_type == 'google' ? 'selected' : '')}
                                    onClick={e => this.handleCalChange(e, 'google')}>
                                </div>
                                <div title="iCloud" className={"cal_image apple " + (this.state.cal_type == 'apple' ? 'selected' : '')}
                                    onClick={e => this.handleCalChange(e, 'apple')}>
                                </div>
                                <div title="Outlook.com" className={"cal_image outlook " + (this.state.cal_type == 'outlook' ? 'selected' : '')}
                                    onClick={e => this.handleCalChange(e, 'outlook')}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <Collapse isOpened={ this.state.cal_type == 'apple' }>
                        <div className="row">
                            <div className="form-group col-md-4">
                                <label>Apple ID:</label>
                                <input type="email"
                                    className={"form-control " + (this.state.ios_user ? '' : 'error')}
                                    value={this.state.ios_user}
                                    onChange={e => this.handleUserChange(e)}
                                    autoComplete="off"
                                />
                            </div>
                            <div className="form-group col-md-4">
                                <label>App Password:</label>
                                <input type="password"
                                    className={"form-control " + (this.state.ios_pass ? '' : 'error')}
                                    value={this.state.ios_pass}
                                    onChange={e => this.handlePassChange(e)}
                                    autoComplete="off"
                                />
                            </div>
                            <div className="col-md-8 apple_note">
                                You will need to create an
                                <a href="http://support.apple.com/kb/ht6186" target="_blank"> app-specific password </a>
                                to link your account.
                            </div>
                        </div>
                    </Collapse>
                    <div className="row">
                        <div className="form-group col-md-4">
                            <input type="submit" className="btn btn-default" value="Sync" />
                        </div>
                    </div>
                </form>
            </div>
        );
    }
}

SyncCal.propTypes = {
    summit: React.PropTypes.object.isRequired,
}

export default connect(null, { syncCalendar } )(SyncCal);