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
import SimpleSearchForm from "~core-components/simple-search-form";

import { updateSearchText, fetchSearchItems } from '../../actions';

export default connect (
	state => ({
		action: 'search',
		currentSearch: state.search,
		placeholder: '',
		className: 'search-form',
        buttonText: <i className="fa fa-search"></i>
	}),
	dispatch => ({
		onSearchTyped (e) {
			dispatch(updateSearchText(e.target.value));
		},
		onSearch (search) {
            if (search) {
                dispatch(fetchSearchItems(search));
            }
		}
	})
)(SimpleSearchForm);