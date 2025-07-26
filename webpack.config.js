const path = require('path');

module.exports = {
    entry: './public/scripts/editor.js',
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'public/scripts'),
    },
    module: {
        rules: [
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
        ],
    },
    mode: 'development'
};
