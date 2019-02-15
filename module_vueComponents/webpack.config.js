const path = require("path");
const webpack = require("webpack");
module.exports = {
  entry: {
    DbBrowser: "./components/DbBrowser/DbBrowser.ts"
  },
  output: {
    filename: "./components/[name]/[name]-bundle.js",
    path: path.resolve(__dirname, "./")
  },

  module: {
    rules: [
      {
        test: /\.vue$/,
        loader: "vue-loader",
        options: {
          loaders: {
            scss: "vue-style-loader!css-loader!sass-loader",
            sass: "vue-style-loader!css-loader!sass-loader?indentedSyntax"
          }
        }
      },
      {
        test: /\.tsx?$/,
        loader: "ts-loader",
        exclude: /node_modules/,
        options: {
          appendTsSuffixTo: [/\.vue$/]
        }
      },
      {
        test: /\.scss$/,
        use: ["style-loader", "css-loader", "sass-loader"]
      }
    ]
  },
  resolve: {
    extensions: [".ts", ".js", ".vue", ".json"],
    alias: {
      vue$: "vue/dist/vue.esm.js"
    }
  }
};
