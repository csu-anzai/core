const path = require('path')
const webpack = require('webpack')
const Dotenv = require('dotenv-webpack')
const glob = require('glob')
const tsPaths = glob.sync('../../{core,core_agp}/module_*/scripts', {
  realpath: true
})

module.exports = {
  entry: {
    // DbBrowser: './components/DbBrowser/DbBrowser.ts'
    agp: glob.sync('../../{core,core_agp}/module_*/scripts/*/*.ts')
    // agp: glob.sync('../../core/module_*/scripts/*/*.ts')
    // agp: glob.sync('../../core/module_*/scripts/*/*.ts')

    // agp: glob.sync('../../core/module_*/scripts/*/*.ts')
    // agp: glob.sync('../../core/_a/*.ts')
  },
  output: {
    // filename: './components/[name]/[name].min.js',
    filename: './[name].min.js',
    path: path.resolve(__dirname, '../module_system/scripts/')
    // path: path.resolve(__dirname, './')
  },

  module: {
    rules: [
      {
        test: /\.vue$/,

        loader: 'vue-loader',
        options: {
          loaders: {
            scss: 'vue-style-loader!css-loader!sass-loader',
            sass: 'vue-style-loader!css-loader!sass-loader?indentedSyntax'
          }
        }
      },
      {
        test: /\.tsx?$/,
        // include: tsPaths,
        loader: 'ts-loader',
        exclude: /node_modules/,
        options: {
          appendTsSuffixTo: [/\.vue$/]
        }
      },
      {
        test: /\.scss$/,
        use: ['style-loader', 'css-loader', 'sass-loader']
      }
    ]
  },
  resolve: {
    modules: [path.resolve(__dirname, './node_modules')],
    extensions: ['.ts', '.js', '.vue', '.json'],
    alias: {
      vue$: 'vue/dist/vue.esm.js'
    }
  },
  plugins: [
    new Dotenv({
      path: path.resolve(__dirname, '.env.dev')
    }),
    new webpack.ProvidePlugin({
      jQuery: 'jquery',
      $: 'jquery',
      jquery: 'jquery'
    })
  ]
}
