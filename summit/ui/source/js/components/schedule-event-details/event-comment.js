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
import React from 'react'
import StarRatingComponent from 'react-star-rating-component';

const EventComment = ({
                          comment,
                      }) => {

    return(
        <div className="comment">
            <div className="comment_info">
                <div style={{fontSize: 24}}>
                    <StarRatingComponent
                        name={comment.id.toString()}
                        value={comment.rate}
                        editing={false}
                        starCount={5}
                        starColor="#fde16d"
                        emptyStarColor="#e3e3e3"
                        renderStarIcon={(index, value) => {
                            return <span className={index <= value ? 'fa fa-star' : 'fa fa-star-o'} />;
                        }}
                    />
                </div>
                <div className="comment_date">
                    <b> Posted: </b>
                    <span> {comment.date} </span>
                </div>
            </div>
            <div className="comment_text"> {comment.note} </div>
            <div className="comment_actions">
                <div className=""></div>
            </div>
        </div>
    );
};

export default EventComment;