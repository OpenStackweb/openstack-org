/**
 * Copyright 2020 Open Infrastructure Foundation
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
import { fetchPage, queryMembers, resolveMissMatch, deleteError } from './actions';
import BaseReport from "~core-components/base_report";
import 'sweetalert2/dist/sweetalert2.css';
import swal from 'sweetalert2';

class AucMetricsFixUserConflictsApp extends BaseReport {

    constructor(props) {
        super(props);
        this.state = {
            ...this.state,
            membersAutoComplete: [],
            selectedMember: null,
            selectedIdentifier:null,
        };
    }

    clearSolveState(){
        this.setState({...this.state,
            membersAutoComplete: [],
            selectedMember: null,
            selectedIdentifier:null});
        this.memberIdentifier.value = '';
    }

    componentDidMount() {
        let _this = this;
        if (!this.props.items.length) {
            this.props.fetchPage({
                page: this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                order: this.buildSort(),
            });
        }
        $('#modal_resolve').on('hidden.bs.modal', function (e) {
          _this.clearSolveState();
        })
    }

    componentDidUpdate(prevProps, prevState) {
        if
        (
            prevState.sort_direction != this.state.sort_direction ||
            prevState.sort_field != this.state.sort_field ||
            prevState.current_page != this.state.current_page ||
            prevState.type != this.state.type ||
            prevState.search_term != this.state.search_term ||
            prevState.page_size != this.state.page_size
        )
            this.props.fetchPage({
                page: this.state.current_page,
                page_size: this.state.page_size,
                search_term: this.state.search_term,
                type: this.state.type,
                order: this.buildSort(),
            });
    }

    onSelectedMember(e, item){
        console.log(item);
        this.setState({...this.state, membersAutoComplete:[], selectedMember: item});
        this.memberIdentifier.value = item.name;
        this.memberIdentifier.focus();
    }

    renderOnFooter(){

        return (
            <div id="modal_resolve" className="modal fade" role="dialog">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <button type="button" className="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 className="modal-title">AUC Metrics - Solve User MissMatch</h4>
                        </div>
                        <div className="modal-body">
                            <form>
                                <div className="form-group">
                                    <label htmlFor="user-identifer" className="control-label">User Identifier:</label>
                                    <input type="text" className="form-control" id="user-identifier" readOnly="true" value={this.getUserIdentifier()}/>
                                </div>
                                <div className="form-group">
                                    <label htmlFor="member-identifier" className="control-label">Member:</label>
                                    <input ref={(input) => { this.memberIdentifier = input; }}  type="text" onChange={(e) => this.onChangeAutoComplete(e)} className="form-control" id="member-identifier"/>
                                    {
                                        this.state.membersAutoComplete.length > 0 &&
                                        <ul className="member-autocomplete-menu">
                                            {
                                                this.state.membersAutoComplete.map((item, index) => (
                                                    <li key={index} onClick={(e) => this.onSelectedMember(e, item)}>{item.name}</li>
                                                ))
                                            }
                                        </ul>
                                    }
                                </div>
                            </form>
                        </div>
                        <div className="modal-footer">
                            <button type="button" className="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="button" className="btn btn-primary" onClick={(e) => this.onDoSolve(e)}>Solve</button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    // to override if needed
    renderColumn(item, col){
        switch(col.name){
            case 'action_buttons':
                return (
                    <div>
                        <button className="btn btn-sm btn-default btn-solve" onClick={(e) => this.onShowSolveModal(e, item)}>
                            Solve
                        </button>
                        <button className="btn btn-sm btn-default btn-delete" onClick={(e) => this.onDeleteError(e, item)}>
                            Delete
                        </button>
                    </div>
                );
            default:
                return item[col.name];
        }
    }

    onChangeAutoComplete(e){
        let _this = this;
        queryMembers(e.target.value).then(function(items){
            _this.setState({..._this.state, membersAutoComplete: items, selectedMember: null})
        })
    }

    // to override if needed
    renderCustomPrimaryFilter() {
        return null;
    }

    onShowSolveModal(e, item) {
        let _this = this;
        e.preventDefault();
        $('#modal_resolve').modal('show');
        this.setState({...this.state, selectedIdentifier:item});
    }

    onDeleteError(e, item){
        e.preventDefault();
        swal({
            title: 'Are you sure?',
            text: 'This action is not undoable',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Remove it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result) {
                this.remove(item);
            }
        })
    }

    remove(item){
        this.props.deleteError(item).then( () => {
            this.fetchPage();
        });
    }

    onDoSolve(e){
        e.preventDefault();
        let { selectedIdentifier, selectedMember } = this.state;
        if(selectedIdentifier == null || selectedMember == null ) return;
        swal({
            title: 'Are you sure?',
            text: 'This action is not undoable',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, fix it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result) {
                this.solve();
            }
        })
    }

    solve(){
        let { selectedIdentifier, selectedMember } = this.state;
        if(selectedIdentifier == null || selectedMember == null ) return;

        $('#modal_resolve').modal('hide');
        this.clearSolveState();
        this.props.resolveMissMatch(selectedIdentifier, selectedMember).then( () => {
            this.fetchPage();
        });
    }

    getUserIdentifier(){
        return this.state.selectedIdentifier != null ? this.state.selectedIdentifier.user_identifier : '';
    }
}

function mapStateToProps(state) {
    return {
        items:      state.items,
        page_count: state.page_count,
        loading:    state.loading
    }
}

export default connect(mapStateToProps, {
    fetchPage,
    resolveMissMatch,
    deleteError
})(AucMetricsFixUserConflictsApp)
