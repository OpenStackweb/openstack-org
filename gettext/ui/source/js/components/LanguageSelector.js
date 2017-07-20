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

class LanguageSelector extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            currentLanguage : this.props.currentLanguage
        }
    }

    hoverEvent() {

    }

    render() {
        return <div className="btn-group dropdown lang-dropdown">
            <button type="button" className="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <div className="lang-sm lang-lbl-full" lang={this.state.currentLanguage}></div>
                <span className="caret"></span>
            </button>
            <ul className="dropdown-menu lang-dropdown-menu" role="menu">
                {this.props.languages.map((lang, i) => (
                    <li className="lang-item"
                        onMouseEnter={this.hoverEvent}
                        onMouseLeave={this.hoverEvent}
                        key={i}>
                        <a href="#" onClick={(evt) => { evt.preventDefault(); this.selectedLanguage(lang); }}>
                            <div className="lang-sm lang-lbl-full" lang={lang}></div>
                        </a>
                    </li>
                ))}
            </ul>
        </div>
    }

    selectedLanguage(lang){
        this.setState(
            {
                currentLanguage : lang
            }
        );

        var url = new this.props.URI(window.location);
        url.query({ 'lang': lang});

        window.location = url.toString();
    }
}

export default LanguageSelector;