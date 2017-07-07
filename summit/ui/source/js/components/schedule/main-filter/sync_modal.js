import React, { Component } from 'react';
import { connect } from 'react-redux';
import {
    unSyncCalendar,
} from '../../../actions';
import {
    Modal,
    ModalHeader,
    ModalTitle,
    ModalClose,
    ModalBody,
    ModalFooter
} from 'react-modal-bootstrap';

class SyncModal extends Component {

    constructor(props) {
        super(props);

        this.state = {
            showModal  : false,
        }
    }

    unSyncCalendar(e){
        this.props.hideModal();
        this.props.unSyncCalendar();
    }

    componentWillReceiveProps(nextProps) {
        if (this.state.showModal != nextProps.showModal) {
            this.setState({
                showModal: nextProps.showModal
            });
        }
    }


    render() {
        const {hideModal} = this.props

        return (
            <div className="sync_modal">
                <Modal isOpen={this.state.showModal} onRequestHide={hideModal}>
                    <ModalHeader>
                        <ModalClose onClick={hideModal}/>
                        <ModalTitle>Un Sync Schedule</ModalTitle>
                    </ModalHeader>
                    <ModalBody>
                        Are you sure you want to un-sync your schedule with your calendar?
                    </ModalBody>
                    <ModalFooter>
                        <button className='btn btn-default' onClick={hideModal}> Close </button>
                        <button className='btn btn-primary' onClick={(e)=> this.unSyncCalendar(e)}> Un Sync </button>
                    </ModalFooter>
                </Modal>
            </div>
        )
    }
}

export default connect(null,{ unSyncCalendar })(SyncModal);