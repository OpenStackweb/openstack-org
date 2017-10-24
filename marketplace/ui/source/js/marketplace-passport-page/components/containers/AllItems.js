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
import { connect } from 'react-redux';
import Item from './Item';
import { AjaxLoader } from "~core-components/ajaxloader";
import GroupedView from '../views/GroupedView';
import UnGroupedView from '../views/UnGroupedView';

export default class AllItems extends React.Component {
	
	constructor (props) {
		super(props);
	}

    componentDidUpdate(prevProps, prevState) {
        // if is first load and is tab location or industry
        if (this.props.section && this.props.section != 'first' && this.props.items.length > 0) {
            $('html, body').animate({
                scrollTop: $("#"+this.props.section).offset().top - 30
            }, 2000);
        }
    }

	render () {

        const {
            items,
            loading,
            distribution,
            active_view
        } = this.props;

		return (
			<div>
				{this.props.views.filter(v => ( v.grouped )).map((v,i) => v.view).find(x => x == active_view) &&
                    <GroupedView items={ items } distribution={ distribution } group_by={ active_view } />
                }
                {this.props.views.filter(v => ( !v.grouped )).map((v,i) => v.view).find(x => x == active_view) &&
                    <UnGroupedView items={ items } distribution={ distribution } group_by={ active_view } />
                }
			</div>
		);
	}
}