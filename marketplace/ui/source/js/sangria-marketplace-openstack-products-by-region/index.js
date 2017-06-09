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
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import SangriaOpenStackProductsByRegionApp from './SangriaOpenStackProductsByRegionApp'
import "../../css/sangria.css";
import store from './store';

const containerId      = 'openstack-products-by-region-app';

document.addEventListener('DOMContentLoaded', function init() {
    if (document.getElementById(containerId)) {
        render(
            <Provider store={store}>
                <SangriaOpenStackProductsByRegionApp page_size={pageSize} program_versions={program_versions}/>
            </Provider>,
            document.getElementById(containerId)
        );
    }
});


