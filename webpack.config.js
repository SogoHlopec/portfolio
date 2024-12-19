const path = require('path');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const PugPlugin = require('pug-plugin');

module.exports = {
    entry: {
        index: './src/index.pug',
        // 'pages/daikin': './src/pages/daikin.pug',
    },
    output: {
        path: path.join(__dirname, 'dist/'),
        publicPath: '/',
    },
    resolve: {
        alias: {
            Images: path.join(__dirname, './images/'),
            Fonts: path.join(__dirname, './fonts/'),
        },
    },
    plugins: [
        new PugPlugin({
            pretty: true,
            js: {
                filename: 'assets/js/[name].js',
            },
            css: {
                filename: 'assets/css/[name].css',
            },
        }),
    ],
    module: {
        rules: [
            {
                test: /\.pug$/,
                loader: PugPlugin.loader,
            },
            {
                test: /\.(css|sass|scss)$/,
                use: [
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: true,
                        },
                    },
                    ,
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true,
                            api: 'modern',
                        },
                    },
                ],
            },
            {
                test: /\.(png|jpg|jpeg|ico)/,
                type: 'asset/resource',
                generator: {
                    filename: 'assets/img/[name][ext]',
                },
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf|svg)$/i,
                type: 'asset/resource',
                generator: {
                    filename: 'assets/fonts/[name][ext][query]',
                },
            },
        ],
    },

    devServer: {
        watchFiles: path.join(__dirname, 'src'),
        port: 9000,
    },
};
