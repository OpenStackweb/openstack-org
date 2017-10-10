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


export default class MarketplaceItem extends React.Component {

	constructor (props) {
		super(props);

	}

	render () {
		return (
            <div className="company browse col-xs-12 col-sm-6 col-lg-4">
                <div className="content">
                    <div className="col-xs-12 col-sm-2 logo">
                        <img src="/marketplace/code/ui/frontend/images/mp.svg" />
                    </div>
                    <div className="col-xs-12 col-sm-9 body">
                        <h5>Marketplace</h5>
                        <p className="description">The OpenStack Marketplace will help you make an informed decision, whether building a cloud, looking to use one, or pursuing a hybrid model. Find more providers in the public cloud marketplace.</p>
                    </div>
                    <div className="col-xs-12 col-sm-1 cta">
                        <a href="/marketplace" className="btn btn-primary">
                            <i className="fa fa-search"></i>
                        </a>
                    </div>
                </div>
            </div>
		);
	}
}
