const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const merge = require('webpack-merge');
const webpackBaseConfig = require('./webpack.base.config.js');
const fs = require('fs');
const package = require('../package.json');

const buf = 'export default "development";';
fs.readFile('./build/env.js', function(err, data){
    if (data.toString() === buf) return;
    fs.open('./build/env.js', 'w', function(err, fd) {
        fs.write(fd, buf, 0, buf.length, 0, function(err, written, buffer) {});
    });
});

module.exports = merge(webpackBaseConfig, {
    devtool: '#source-map',
    devServer: {
        clientLogLevel: 'warning',
        historyApiFallback: true,
        hot: true,
        compress: true,
        host: 'localhost',
        port: 8000,
        open: true,
        overlay: {
            errors: true
        },
        headers: {
            'Access-Control-Allow-Origin': 'http://www.mylaravel.com'
        },
        proxy: {
            "/api": {
                target: "http://www.mylaravel.com",
                changeOrigin: true,
                secure: false
            },
            '/storage': {
                target: 'http://www.mylaravel.com',
                changeOrigin: true,
                secure: false
            }
        }
    },
    output: {
        publicPath: 'http://localhost:8000/',
        filename: '[name].js',
        chunkFilename: '[name].chunk.js'
    },
    plugins: [
        new ExtractTextPlugin({
            filename: '[name].css',
            allChunks: true
        }),
        new webpack.optimize.CommonsChunkPlugin({
            name: ['vender-exten', 'vender-base'],
            minChunks: Infinity
        }),
        new webpack.optimize.CommonsChunkPlugin({
            name: 'runtime'
        }),
        new HtmlWebpackPlugin({
            title: 'Lottery Admin' + package.version,
            favicon: './favicon.ico',
            filename: '../../resources/views/index.blade.php',
            template: './resources/template/index.ejs',
            inject: 'body'
        }),
        new CopyWebpackPlugin([
            {
                from: './resources/components/theme-switch/theme'
            },
            {
                from: './resources/components/text-editor/tinymce'
            }
        ], {
            ignore: [
                'text-editor.vue'
            ]
        })
    ]
});
