// ***********************************************************
// This example plugins/index.js can be used to load plugins
//
// You can change the location of this file or turn off loading
// the plugins file with the 'pluginsFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/plugins-guide
// ***********************************************************

const webpack = require('@cypress/webpack-preprocessor')
const { VueLoaderPlugin } = require('vue-loader')
const path = require('path')
const webpackOptions = {
    module: {
        rules: [
            {
                test: /\.vue$/,

                loader: 'vue-loader',
                options: {
                    loaders: {
                        scss: 'vue-style-loader!css-loader!sass-loader',
                        sass:
                    'vue-style-loader!css-loader!sass-loader?indentedSyntax'
                    }
                }
            },
            {
                test: /\.tsx?$/,
                use: [
                    { loader: 'babel-loader' },
                    {
                        loader: 'ts-loader'
                    }
                ],

                exclude: /node_modules/
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin()
    ],
    resolve: {
        modules: [path.resolve(__dirname, '../../node_modules')], // necessary to resolve npm packages
        extensions: ['.ts', '.js', '.vue', '.json', '.less'], // necessary to build files with these extensions
        alias: {
            vue$: 'vue/dist/vue.esm.js', // necessary to work rith vue.js properly
            'load-image': 'blueimp-load-image/js/load-image.js', // necessary to load jquery file upload properly
            'load-image-meta': 'blueimp-load-image/js/load-image-meta.js', // necessary to load jquery file upload properly
            'load-image-exif': 'blueimp-load-image/js/load-image-exif.js', // necessary to load jquery file upload properly
            'load-image-scale': 'blueimp-load-image/js/load-image-scale.js', // necessary to load jquery file upload properly
            'canvas-to-blob': 'blueimp-canvas-to-blob/js/canvas-to-blob.js', // necessary to load jquery file upload properly
            'jquery-ui/ui/widget':
              'blueimp-file-upload/js/vendor/jquery.ui.widget.js', // necessary to load jquery file upload properly
            '@': path.resolve(__dirname, '../../../../'), // define root directory as @ : makes typecript imports easier / avoid long relative path input
            core: path.resolve(__dirname, '../../../'), // define core directory as core : makes typecript imports easier / avoid long relative path input
            core_agp: path.resolve(__dirname, '../../../../core_agp'), // define core_agp directory as core_agp : makes typecript imports easier / avoid long relative path input
            core_customer: path.resolve(__dirname, '../../../../core_customer') // define core_customer directory as core_customer : makes typecript imports easier / avoid long relative path input
            // !important all the aliases definitions needs to be defined in the tsconfig.json as well
        }
    }
}

const options = {
    webpackOptions,
    watchOptions: {}
}
module.exports = on => {
    on('file:preprocessor', webpack(options))
}
