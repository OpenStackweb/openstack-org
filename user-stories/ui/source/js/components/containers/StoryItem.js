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
import { fetchSearchTagStories } from '../../actions';


class StoryItem extends React.Component {

	constructor (props) {
		super(props);

        this.tagClick = this.tagClick.bind(this);
	}

    tagClick (e) {
        e.preventDefault();
        let tag = e.target.dataset.tag;

        this.props.searchTag(tag);
    }

	render () {
		let { story } = this.props;
        let image_url = story.image;

		return (
            <div className="col-lg-4 col-md-6 ">
                <a href={story.link}  className="user-story-square" style={{ backgroundImage: `url(${image_url})` }}>
                    <span className="company">
                        {story.name}
                        <span className="stat" dangerouslySetInnerHTML={{__html: story.short_desc}} />
                        <span className="tags">
                            {story.tags.slice(0,3).map((tag, i) => (
                                <span key={i} className="tag">{tag}</span>
                            ))}
                            {story.tags.length > 3 &&
                                <span className="tag">+{story.tags.length - 3}</span>
                            }
                        </span>

                    </span>
                    <span className="user-story-overlay">
                        <span dangerouslySetInnerHTML={{__html: story.description}} />
                        <div className="tags">
                            {story.tags.map((tag, i) => (
                                <span key={i} className="tag" data-tag={tag} onClick={this.tagClick}>{tag}</span>
                            ))}
                        </div>
                        <div className="user-story-more-btn">Read More</div>
                    </span>
                </a>
            </div>
		);
	}
}


export default connect (
    state => ({

    }),
    dispatch => ({
        searchTag(tag){
            return dispatch(fetchSearchTagStories(tag));
        }
    })
)(StoryItem);