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

class ListItem extends React.Component {

	constructor (props) {
		super(props);

	}

	render () {
		let { item } = this.props;

		return (
            <div className="company col-xs-12 col-sm-6 col-lg-4">
                <div className="content">
                    <div className="col-xs-12 col-sm-2 logo">
                        <img src={item.logo} />
                    </div>
                    <div className="col-xs-12 col-sm-9 body">
                        <div className="row">
                            <div className="col-sm-12">
                                <h5>{item.name}</h5>
                            </div>
                            <div className="col-sm-12">
                                <p className="description">{item.description}</p>
                            </div>
                            <div className="col-sm-12">
                                <h6>Clouds In: <span>{item.location_string}</span></h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xs-12 col-sm-1 cta">
                        <a href={item.learn_more} className="btn btn-primary">
                            <i className="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
		);
	}
}


export default connect (
    state => ({

    }),
    dispatch => ({

    })
)(ListItem);