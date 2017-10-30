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
import SearchForm from './components/containers/SearchForm';
import AllItems from './components/containers/AllItems';
import { AjaxLoader } from "~core-components/ajaxloader";
import Message from "~core-components/message";
import GMap from '~core-components/map';
import { connect } from 'react-redux';
import { changeActiveView, changeActiveDist, loadItems } from './actions';

class PassportsApp extends React.Component {

    constructor (props) {
        super(props);

        this.changeView = this.changeView.bind(this);
        this.changeDistribution = this.changeDistribution.bind(this);
        this.onMapChange = this.onMapChange.bind(this);

        this.state = {
            items: []
        }
    }

    componentDidMount () {
        if(!this.props.items.length) {
            this.props.loadItems();
        }

    }

    componentWillReceiveProps(nextProps) {
        // will update the map when the items are different
        let old_item_ids = this.props.items.map(i => i.id).join();
        let new_item_ids = nextProps.items.map(i => i.id).join();

        if (old_item_ids !== new_item_ids)
            this.setState({ items: nextProps.items });
    }

    changeView (e) {
        e.preventDefault();
        let view   = e.target.dataset.view;
        let fetch_items = (this.props.active_view == 'search');

        this.props.changeActiveView(view, fetch_items);
    }

    changeDistribution (e) {
        e.preventDefault();
        let dist   = e.target.dataset.distribution;

        this.props.changeActiveDist(dist);
    }

    onMapChange(markers){
        if (markers.length > 0 ) {
            let item_ids = markers.map(m => m.item_id);
            let filtered_items = this.props.items.filter((v, i) => item_ids.indexOf(v.id) !== -1);
            this.setState({ items: filtered_items });
        }
    }

	render () {
		const {
            items,
            loading,
            distribution,
            active_view,
            views
            } = this.props;

        let locations = items.map(item => item.locations );
        locations = [].concat(...locations);

		return (
		<div>
            <Message />
            {loading &&
                <AjaxLoader show={ loading } relative={ true } color='white' size={ 75 } />
            }
            <div className="container">
                <div className="row map">
                    <div className="col-xs-12">
                        <h5>OpenStack Public Cloud Passport Providers</h5>
                        <GMap
                            markers={locations}
                            onChangeCallback={ this.onMapChange }
                            zoom={2}
                            center={{lat: 32, lng: 14}}
                        />
                    </div>
                </div>
            </div>
            <div className="container catalog">
                <div className="row filters">
                    <div className="col-sm-2 col-xs-12 view-all">
                        <button className="link cancel" onClick={this.changeView} data-view="date">
                            View All
                        </button>
                    </div>
                    <div className="col-sm-4 col-xs-12">
                        <div className="search-wrapper">
                            <SearchForm />
                        </div>
                    </div>
                    <div className="col-sm-6 col-xs-12 distribution-wrapper">
                        <a
                            className={"dist-tiles " + (distribution == 'tiles' ? 'active' : '')}
                            onClick={this.changeDistribution}
                        >
                            <i className="fa fa-th-large" data-distribution="tiles"></i>
                        </a>
                        <a
                            className={"dist-list " + (distribution == 'list' ? 'active' : '')}
                            onClick={this.changeDistribution}
                        >
                            <i className="fa fa-th-list" data-distribution="list"></i>
                        </a>
                    </div>
                </div>
                <div className="app-layout">
                    <AllItems
                        items={ this.state.items }
                        views={views}
                        loading={loading}
                        distribution={distribution}
                        active_view={active_view}
                    />
                </div>
            </div>
		</div>
		);
	}
}

export default connect (
	state => {
		return {
			msg: state.msg,
            loading: state.loading,
            items: state.items,
            distribution: state.distribution,
            active_view: state.active_view
		}
	},
	dispatch => ({
        changeActiveView(active_view, fetch_items){
            return dispatch(changeActiveView(active_view, 'first', fetch_items));
        },
        changeActiveDist(distribution){
            return dispatch(changeActiveDist(distribution));
        },
        loadItems () {
            dispatch(loadItems());
        }
	})
)(PassportsApp);