import React from 'react';
import { connect } from 'react-redux';
import {
    Modal,
    ModalHeader,
    ModalTitle,
    ModalClose,
    ModalBody,
    ModalFooter
} from 'react-modal-bootstrap';

export default class Confirm extends React.Component {

    constructor (props) {
        super(props);

        this.state = {
            open: this.props.open
        }
    }

    componentWillReceiveProps(nextProps) {
        this.setState({
           open: nextProps.open
        });
    }

    hide = () => {
        this.setState({
            open: false
        });
    };

    styleText = {
        fontSize: '16px',
        color: 'black',
        textAlign: 'center'
    };

    render () {
        return (
            <Modal isOpen={this.state.open} onRequestHide={this.hide}>
                <ModalHeader>
                    <ModalClose onClick={this.hide}/>
                    <ModalTitle>Attention</ModalTitle>
                </ModalHeader>
                <ModalBody>
                        <p style={this.styleText}>{this.props.text}</p>
                </ModalBody>
                <ModalFooter>
                    <button className='btn btn-default' onClick={this.hide}> Cancel </button>
                    <button className='btn btn-primary' onClick={() => this.props.onConfirm()}> YES ! </button>
                </ModalFooter>
            </Modal>
        );
    }

}
