import React from 'react';
import { connect } from 'react-redux';
import { fetchAll, saveItem, deleteItems, mergeItems } from './actions';
import Message from "~core-components/message";
import Confirm from "~core-components/confirm";
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
            confirmOpen: false,
            selected_tags: [],
            search: '',
            edit_tag_id: 0,
            edit_tag: '',
            merge_tag: ''
        }

        this.onSearchTyped = this.onSearchTyped.bind(this);
        this.onClear = this.onClear.bind(this);
        this.onTagEdit = this.onTagEdit.bind(this);
        this.onMergeEdit = this.onMergeEdit.bind(this);
        this.onMerge = this.onMerge.bind(this);
        this.onDeleteMulti = this.onDeleteMulti.bind(this);
        this.onDelete = this.onDelete.bind(this);
    }

    componentDidMount() {
        this.searchInput.focus();

        if(!this.props.items.length) {
            this.props.fetchItems('');
        }
    }

    toggleTag(tag_id) {
        let selected_tags = this.state.selected_tags;

        if(selected_tags.find(t => t == tag_id)) {
            selected_tags = selected_tags.filter(t => t != tag_id);
        } else {
            selected_tags.push(tag_id);
        }

        this.setState({
            selected_tags: selected_tags,
        });
    };

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
            search: e.target.value,
            selected_tags: []
        });

        this.props.fetchItems(e.target.value);
    };

    onClear(e) {
        this.setState({
            search: '',
            selected_tags: []
        });

        this.props.fetchItems('');
    };

    onTagEdit(e) {
        this.setState({
            edit_tag: e.target.value
        });
    };

    onMergeEdit(e) {
        this.setState({
            merge_tag: e.target.value
        });
    };

    onMerge(e) {
        this.props.mergeTags(this.state.search, this.state.selected_tags, this.state.merge_tag);

        this.setState({
            merge_tag: '',
            selected_tags: [],
            confirmOpen: false
        });

    };

    onSave(is_split) {
        this.props.saveTag(this.state.edit_tag_id, this.state.edit_tag, this.state.search, is_split);

        this.setState({
            modalOpen: false,
            edit_tag_id: 0,
            edit_tag: ''
        });
    };

    openConfirm(text, handle) {
        this.setState({
            confirmOpen: true,
            confirmText: text,
            confirmHandle: handle
        });
    };

    onDelete() {
        this.props.deleteTags([this.state.edit_tag_id]);

        this.setState({
            modalOpen: false,
            confirmOpen: false,
            edit_tag_id: 0,
            edit_tag: '',
            selected_tags: [],
            search: ''
        });
    };

    onDeleteMulti() {
        this.props.deleteTags(this.state.selected_tags);

        this.setState({
            confirmOpen: false,
            selected_tags: [],
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
                        <div>
                            <button
                                className='btn btn-danger btn-sm pull-left'
                                onClick={() => this.openConfirm('Are you sure you want to delete this tag?', this.onDelete )}
                            > Delete Tag </button>
                            <button className='btn btn-default btn-sm pull-left' onClick={() => this.onSave(true)}> Save & Split </button>
                        </div>
                        }

                        <button className='btn btn-primary' onClick={() => this.onSave(false)}> Save changes </button>
                        <p className="split-info">
                            * On split the tag will be splitted by spaces into multiple tags
                        </p>
                    </ModalFooter>
                </Modal>

                <Confirm open={this.state.confirmOpen} text={this.state.confirmText} onConfirm={this.state.confirmHandle} />

                <div className="row">
                    <div className="col-md-4 form-inline">
                        <input
                            className="form-control search-input"
                            ref={(input) => { this.searchInput = input; }}
                            value={this.state.search}
                            onChange={this.onSearchTyped}
                        />
                        <button className="btn btn-default" onClick={this.onClear}>Clear</button>
                    </div>
                    <div className="col-md-2">
                        <button className="btn btn-primary" onClick={() => this.openModal(0, '')}>Add New</button>
                    </div>
                    {this.state.selected_tags.length > 1 &&
                    <div className="actions-container">
                        <div className="col-md-4 form-inline">
                            <input value={this.state.merge_tag} onChange={this.onMergeEdit} className="form-control merge-input"/>
                            <button
                                className="btn btn-success"
                                onClick={() => this.openConfirm('Are you sure you want to merge selected tags?', this.onMerge )}
                            > Merge </button>
                        </div>
                        <div className="col-md-2">
                            <button
                                className="btn btn-danger"
                                onClick={() => this.openConfirm('Are you sure you want to delete selected tags?', this.onDeleteMulti )}
                            > Delete </button>
                        </div>
                    </div>
                    }
                </div>

                {items.length > 0 &&
                    <div className="tag-container row">
                        {
                            items.map
                            (
                                tag =>
                                <div key={tag.id} className="col-md-2 tag-wrapper">
                                    <input
                                        className="tag-select"
                                        type="checkbox"
                                        onClick={() => this.toggleTag(tag.id)}
                                        checked={this.state.selected_tags.find(t => t == tag.id)}
                                    />
                                    <span onClick={() => this.openModal(tag.id, tag.tag)}>
                                        {tag.tag} ({tag.count})
                                    </span>
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
        saveTag (tag_id, tag, search, is_split) {
            return dispatch(saveItem({tag_id, tag, search, is_split}));
        },
        deleteTags (tag_ids) {
            return dispatch(deleteItems({tag_ids}));
        },
        mergeTags (search, selected_tags, merge_tag) {
            return dispatch(mergeItems({search, selected_tags, merge_tag}));
        }
    })
)(SangriaTagsCrudApp);
