const path = require("path");
const webpack = require("webpack");

var Dashboard = require("webpack-dashboard");
var DashboardPlugin = require("webpack-dashboard/plugin");
var dashboard = new Dashboard();

const nodeEnv = (process.env.NODE_ENV || "development").trim();
const isProd = nodeEnv === "production";

const web = path.join(__dirname, "../backend/web");

const config = {
  stats: false,
  devtool: isProd ? null : "cheap-module-source-map",
  context: path.join(__dirname, "src"),
  entry: {
    app: path.join(__dirname, "src/index.js")
  },
  output: {
    path: path.join(web, "build"),
    filename: "[name].js"
  },
  module: {
    loaders: [
      {
        test: /\.html$/,
        loader: "file",
        query: {
          name: "[name].[ext]"
        }
      },
      {
        test: /\.css$/,
        loaders: ["style", "css"]
      },
      {
        // transpiles JSX and ES6
        test: /\.js$/,
        include: /src/,
        loader: "babel",
        query: {
          plugins: [
            "transform-object-rest-spread",
            "transform-class-properties"
          ]
        }
      }
    ]
  },
  resolve: {
    modules: [
      path.join(__dirname, "src"),
      path.join(__dirname, "node_modules")
    ], // WebPack 2 merged WP1 root, moduleDirections and fallback props
    root: path.join(__dirname, "src") // Webpack2 ignores this! Needed for eslint no-unresolved rule to work properly.
  },
  plugins: [
    new DashboardPlugin(dashboard.setData),
    new webpack.LoaderOptionsPlugin({
      minimize: true,
      debug: false
    }),
    new webpack.DefinePlugin({
      "process.env": {
        NODE_ENV: JSON.stringify(nodeEnv)
      }
    })
  ]
};
if (isProd) {
  config.plugins.push(
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        warnings: false
      },
      output: {
        comments: false
      },
      sourceMap: false
    })
  );
}

module.exports = config;
