const path = require('path')

module.exports = async ({ config, mode }) => {

        config.module.rules.push( {
            test: /\.vue$/, // vue loader for the template files

            loader: 'vue-loader',
            options: {
                loaders: {
                    scss: 'vue-style-loader!css-loader!sass-loader',
                    sass:
                        'vue-style-loader!css-loader!sass-loader?indentedSyntax'
                }
            }
        })
        config.module.rules.push(     {
            test: /\.tsx?$/, // typescript loader for the .ts and .tsx files
            use: [
                { loader: 'babel-loader' },
                {
                    loader: 'ts-loader',
                    options: {
                        appendTsSuffixTo: [/\.vue$/], // needed to import vue's template files in .ts files
                        configFile: path.resolve(
                            // path to the tsconfig.json file
                            __dirname,
                            '../tsconfig.json'
                        )
                    }
                }
            ],

            exclude: /node_modules/
        })
        config.module.rules.push({
            test: /\.woff($|\?)|\.woff2($|\?)|\.ttf($|\?)|\.eot($|\?)|\.svg($|\?)|\.(png|jpg|gif)$/, // loader for the fonts/images , makes import of this files possible
            loader: 'url-loader'
        })
        config.module.rules.push({
            test: /\.less$/,
            use: [
                {
                    loader: 'style-loader'
                },
                {
                    loader: 'css-loader'
                },
                {
                    loader: 'less-loader'
                }
            ]
        })
        config.module.rules.push({
            test: /\.css$/, // normal css loader
            use: ['style-loader', 'css-loader']
        })
        config.resolve.modules.push(path.resolve(__dirname, '../node_modules')) 
    return config
    
}
