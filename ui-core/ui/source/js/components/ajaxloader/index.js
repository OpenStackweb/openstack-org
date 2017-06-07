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

export const AjaxLoader =  ({
                         show,
                         children,
                     }) => {

    let styles = {
        display : show ? 'block': 'none',
        width:'100%',
        height:'100%',
        position:'fixed',
        zIndex:10000000,
        top:0,
        left:0,
        margin:'auto',
        cursor: 'wait'
    };

    let styleSpinner = {
        fontSize: '150px',
        color: 'black',
    };

    let styleSpinnerContainer = {
        width: '250px',
        height:'75px',
        textAlign: 'center',
        position: 'fixed',
        top:'0',
        left:'0',
        right:'0',
        bottom:'0',
        margin:'auto',
        zIndex:10,
        color:'#ffffff'
    };

    let styleBackground = {
        background:'#000000',
        opacity:'0.8',
        width:'100%',
        height:'100%',
        position:'absolute',
        top:0
    };

    return (
        <div style={styles}>
            <div style={styleSpinnerContainer}>
                <i className="fa fa-spinner fa-spin" style={styleSpinner}></i>
                <div>
                    {children}
                </div>
            </div>
            <div style={styleBackground} className="loader-background"></div>
        </div>
    );
};
