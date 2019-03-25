import React, { Component } from 'react';
import { connect } from 'react-redux';
import {
    Modal,
    ModalHeader,
    ModalTitle,
    ModalClose,
    ModalBody,
    ModalFooter
} from 'react-modal-bootstrap';

import {
    deleteSCalendarShareableLink,
} from '../../../actions';

class ShareableLinkModal extends Component {

    constructor(props) {
        super(props);
        this.state = {
            showModal  : false,
        }
        this.unShare = this.unShare.bind(this);
        this.copyLink = this.copyLink.bind(this);
    }

    copyLink(e){
        const {shareableLink} = this.props
        const el = document.createElement('textarea');
        el.value = shareableLink;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        window.alert("link copied!");
    }

    unShare(e){
        this.props.hideModal();
        this.props.deleteSCalendarShareableLink();
    }

    componentWillReceiveProps(nextProps) {
        if (this.state.showModal != nextProps.showModal) {
            this.setState({
                showModal: nextProps.showModal
            });
        }
    }

    render() {
        const {hideModal, shareableLink} = this.props

        return (
            <div className="sync_modal">
                <Modal isOpen={this.state.showModal} onRequestHide={hideModal}>
                    <ModalHeader>
                        <ModalClose onClick={hideModal}/>
                        <ModalTitle>Shareable link to your calendar</ModalTitle>
                    </ModalHeader>
                    <ModalBody>
                        With this link, anyone will be able to see any of your events<br/>
                        <a href={shareableLink}>{shareableLink}</a>
                    </ModalBody>
                    <ModalFooter>
                        <button className='btn btn-default' onClick={hideModal}> Close </button>
                        <button className='btn btn-primary' onClick={(e)=> this.copyLink(e)}> Copy Link </button>
                        <button className='btn btn-danger' onClick={(e)=> this.unShare(e)}> Delete it </button>
                    </ModalFooter>
                </Modal>
            </div>
        )
    }
}

export default connect(null,{deleteSCalendarShareableLink})(ShareableLinkModal);