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

export class TagInput extends React.Component {

    componentDidMount() {

        let _this = this;

        this._build();

        $(this.textInput).on('itemAdded', function(event) {
            // event.item: contains the item
            if(event.options && event.options.preventPost)
                return;
            if(_this.props.onTagAdded)
                 _this.props.onTagAdded(event);
        });

        $(this.textInput).on('itemRemoved', function(event) {
            // event.item: contains the item
            if( _this.props.onTagRemoved)
                _this.props.onTagRemoved(event);
        });

        this._load();
    }

    _build(){
        let options = {
            allowDuplicates: false,
            trimValue: true,
            freeInput: this.props.freeInput
        };

        if(this.props.source){
            options = { ...options, typeahead: {
                source: this.props.source,
                // https://github.com/bootstrap-tagsinput/bootstrap-tagsinput/issues/236
                afterSelect: function(val) { this.$element.val(""); },
            }};
        }

        $(this.textInput).tagsinput(options);
    }

    _load(){
        $(this.textInput).tagsinput('removeAll');
        let { tags } = this.props;
        for(let tag of tags){
            $(this.textInput).tagsinput('add', tag.value, { preventPost: true});
        }
    }

    componentWillUnmount() {
        $(this.textInput).tagsinput('destroy');
    }

    componentWillUpdate(){
        $(this.textInput).tagsinput('refresh');
    }

    componentDidUpdate(prevProps, prevState){

        if(prevProps.source && prevProps.source.length == 0 && this.props.source.length > 0){

            $(this.textInput).tagsinput('destroy');

            this._build();
        }

        this._load();
    }

    render() {
        return (<input data-role="tagsinput" ref={(input) => { this.textInput = input; }}  type="text"/>)
    }

    getVal(){
        return $(this.textInput).val();
    }
}

TagInput.defaultProps = {
    freeInput: true,
    tags: [],
};