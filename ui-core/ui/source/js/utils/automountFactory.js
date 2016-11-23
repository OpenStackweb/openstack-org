export default(React, ReactDOM) => {
    const mount = function() {
        const nodes = document.querySelectorAll('[data-component]');
        const nodeList = [].slice.call(nodes);
        nodeList.forEach(el => {
            const componentName = el.getAttribute('data-component');
            if (!window[componentName]) {
                console.error(`Tried to mount component ${componentName}, but none was found in the global namespace.`);
                return;
            }

            let component = window[componentName];

            if (typeof component === 'object') {
                if (component.__esModule) {
                    component = component[componentName] || component.default;
                } else {
                    console.error(`${componentName} is in the global namespace as an object, but it is not a module`);
                    return;
                }
            }

            if (typeof component !== 'function') {
                console.error(`${componentName} is in the global name space, but it is a ${typeof component}`);
                return;
            }

            const props = {};
            const attrList = [].slice.call(el.attributes);

            attrList.filter(attr => (/^data-/.test(attr.name) && attr.name !== 'data-component')).forEach(attr => {
                const camelCaseName = attr.name.substr(5).replace(/-(.)/g, ($0, $1) => $1.toUpperCase());

                let propValue = attr.value;
                if (attr.value === '' || attr.value === 'true') {
                    propValue = true;
                }
                if (attr.value === 'false') {
                    propValue = false;
                }

                props[camelCaseName] = propValue;

            });

            props.children = el.textContent;

            ReactDOM.render(React.createElement(component, props), el);
        });
    };

    document.addEventListener('DOMContentLoaded', mount);
};
