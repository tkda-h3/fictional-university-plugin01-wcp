const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
  mode: 'development',
  entry: './src/js/scripts.js',
  output: {
    filename: 'scripts.js',
    path: path.join(__dirname, 'wp/js')
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [
                '@babel/preset-env', // デフォルトでES5のはず
              ]
            }
          }
        ]
      }
    ]
  },
};
