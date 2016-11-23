const path = require('path');

module.exports = {

    entry: {
        automount: './source/js/utils/automount',
        AwesomeButton: './source/js/components/AwesomeButton'
    },

    output: {
        filename: 'js/[name].js',
        path: path.join(__dirname, 'production/'),
        publicPath: '/themes/openstack/ui/production',
        library: '[name]',
        libraryTarget: 'var'
    }
};
