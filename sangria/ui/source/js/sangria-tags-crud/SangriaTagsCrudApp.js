import React from 'react';
import { connect } from 'react-redux';
import { fetchAll, saveItem, deleteItem } from './actions';
import Message from "~core-components/message";
import { AjaxLoader } from '~core-components/ajaxloader';
import {
    Modal,
    ModalHeader,
    ModalTitle,
    ModalClose,
    ModalBody,
    ModalFooter
} from 'react-modal-bootstrap';

class SangriaTagsCrudApp extends React.Component
{
    constructor(props) {
        super(props);

        this.state = {
            loading: true,
            modalOpen: false,
            search: '',
            edit_tag_id: 0,
            edit_tag: ''
        }

        this.onSearchTyped = this.onSearchTyped.bind(this);
        this.onClear = this.onClear.bind(this);
        this.onTagEdit = this.onTagEdit.bind(this);
        this.onSave = this.onSave.bind(this);
        this.onDelete = this.onDelete.bind(this);
    }

    componentDidMount() {
        this.searchInput.focus();

        if(!this.props.items.length) {
            this.props.fetchItems('');
        }
    }

    openModal(tag_id, tag) {
        this.setState({
            modalOpen: true,
            edit_tag_id: tag_id,
            edit_tag: tag
        });
    };

    hideModal = () => {
        this.setState({
            modalOpen: false,
            edit_tag_id: 0,
            edit_tag: ''
        });
    };

    onSearchTyped(e) {
        this.setState({
            search: e.target.value
        });

        this.props.fetchItems(e.target.value);
    };

    onClear(e) {
        this.setState({
            search: ''
        });

        this.props.fetchItems('');
    };

    onTagEdit(e) {
        this.setState({
            edit_tag: e.target.value
        });
    };

    onSave() {
        this.props.saveTag(this.state.edit_tag_id, this.state.edit_tag, this.state.search);

        this.setState({
            modalOpen: false,
            edit_tag_id: 0,
            edit_tag: ''
        });
    };

    onDelete() {
        this.props.deleteTag(this.state.edit_tag_id);

        this.setState({
            modalOpen: false,
            edit_tag_id: 0,
            edit_tag: '',
            search: ''
        });
    };

    render() {
        let {items} = this.props;

        return (
            <div>
                <Message />
                <AjaxLoader show={this.props.loading} size={ 75 } />

                <Modal isOpen={this.state.modalOpen} onRequestHide={this.hideModal}>
                    <ModalHeader>
                        <ModalClose onClick={this.hideModal}/>
                        <ModalTitle>Edit Tag</ModalTitle>
                    </ModalHeader>
                    <ModalBody>
                        <label> Tag: </label>
                        <input
                            value={this.state.edit_tag}
                            onChange={this.onTagEdit}
                            className="form-control"
                            autoFocus
                        />
                        <input type="hidden" id="tag-id" value={this.state.edit_tag_id} />
                    </ModalBody>
                    <ModalFooter>
                        { this.state.edit_tag_id != 0 &&
                            <button className='btn btn-danger pull-left' onClick={this.onDelete}> Delete Tag </button>
                        }
                        <button className='btn btn-default' onClick={this.hideModal}> Close </button>
                        <button className='btn btn-primary' onClick={this.onSave}> Save changes </button>
                    </ModalFooter>
                </Modal>

                <div className="row">
                    <div className="col-md-4">
                        <input
                            className="form-control"
                            ref={(input) => { this.searchInput = input; }}
                            value={this.state.search}
                            onChange={this.onSearchTyped}
                        />
                    </div>
                    <div className="col-md-2">
                        <button className="btn btn-default" onClick={this.onClear}>Clear</button>
                    </div>
                    <div className="col-md-2">
                        <button className="btn btn-primary" onClick={() => this.openModal(0, '')}>Add New</button>
                    </div>
                </div>

                {items.length > 0 &&
                    <div className="tag-container row">
                        {
                            items.map
                            (
                                tag =>
                                <div key={tag.id} className="col-md-2 tag-wrapper">
                                    <span onClick={() => this.openModal(tag.id, tag.tag)}>{tag.tag}</span>
                                </div>
                            )
                        }
                    </div>
                }

                {items.length == 0 &&
                    <p className="no-tags"> No tags found </p>
                }
            </div>
        );
    }
}

export default connect (
    state => {
        return {
            items:      state.items,
            loading:    state.loading
        }
    },
    dispatch => ({
        fetchItems (search = '') {
            return dispatch(fetchAll({search}));
        },
        saveTag (tag_id, tag, search) {
            return dispatch(saveItem({tag_id, tag, search}));
        },
        deleteTag (tag_id) {
            return dispatch(deleteItem({tag_id}));
        }
    })
)(SangriaTagsCrudApp);
