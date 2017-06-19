import React from 'react';
import { connect } from 'react-redux';
import { clearMessage } from "~core-utils/actions";
import './message.less';

class Message extends React.Component {

    constructor (props) {
        super(props);
        this._timeout = null;
        this.clearMessageTimeout = this.clearMessageTimeout.bind(this);
    }

    clearMessageTimeout () {
        if(this._timeout) {
            window.clearTimeout(this._timeout);
        }
        this._timeout = window.setTimeout(this.props.clearMessage, 5000);
    }

    render () {
        const {msg} = this.props;
        const {msg_type} = this.props;
        return (
            <div>
                {msg &&
                    <div className={"app-msg "+ msg_type} ref={this.clearMessageTimeout}>
                        {msg}
                        <a onClick={this.props.clearMessage}>&times;</a>
                    </div>
                }
            </div>
        );
    }

}

export default connect (
    state => ({
        msg: state.msg,
        msg_type: state.msg_type
    }),

    dispatch => ({
        clearMessage () {
            dispatch(clearMessage());
        }
    })
)(Message);
