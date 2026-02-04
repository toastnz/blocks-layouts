const path = require('path');
const fs = require('fs');
const glob = require('glob');
const postcssUrl = require('postcss-url');
const TerserPlugin = require("terser-webpack-plugin");
const postcssCriticalCSS = require('postcss-critical-css');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const FriendlyErrorsWebpackPlugin = require('@soda/friendly-errors-webpack-plugin');

function generateSassIndexFiles(directories) {
  directories.forEach((directory) => {
    const subdirectories = fs.readdirSync(directory, { withFileTypes: true })
      .filter(dirent => dirent.isDirectory())
      .map(dirent => path.join(directory, dirent.name));

    subdirectories.forEach((subdirectory) => {
      const files = glob.sync(`${subdirectory}/*.scss`, {
        ignore: [`${subdirectory}/_index.scss`],
      });

      const indexPath = path.resolve(subdirectory, '_index.scss');
      const content = files.map((file) => `@forward '${path.basename(file, '.scss')}';`).join('\n');

      // Ensure the directory exists
      fs.mkdirSync(path.dirname(indexPath), { recursive: true });

      fs.writeFileSync(indexPath, content);
    });
  });
}

const stats = {
  colors: true,
  hash: false,
  version: false,
  timings: false,
  assets: false,
  chunks: false,
  modules: false,
  reasons: false,
  children: false,
  source: false,
  errors: false,
  errorDetails: false,
  warnings: false,
  publicPath: false,
};

// The main directory (outside the webpack folder)
const dir = path.resolve(__dirname, '../');
// Our aliases
const aliases = {
  'styles': path.resolve(dir, './client/source/styles/'),
  'scripts': path.resolve(dir, './client/source/scripts/'),
};

// Glob all JS files in components using path.join for consistency
const componentFiles = glob.sync(path.join(dir, './client/source/scripts', '*.js'));
const entries = {};

componentFiles.forEach(filePath => {
  // Get the filename without extension
  const name = path.basename(filePath, '.js');
  entries[name] = filePath;
});

// Add cms.js as its own entry
// entries['cms'] = path.resolve(dir, './client/source/scripts/cms.js');

const app = {
  dir,
  entries,
  output: {
    publicPath: '/client/dist/scripts/',
    path: path.resolve(dir, './client/dist/scripts'),
    filename: '[name].js',
    chunkFilename: 'components/[chunkhash].js',
    clean: true,
  },
  resolve: { alias: aliases },
};

const configs = [];

// Create index files
generateSassIndexFiles([aliases.styles]);

[app].forEach((config) => {
  configs.push((env, argv) => {
    const isProduction = argv.mode === 'production';
    const isDevelopment = !isProduction;

    return {
      mode: isProduction ? 'production' : 'development',
      stats,
      devtool: false,
      entry: config.entries,
      output: config.output,
      resolve: config.resolve,
      module: {
        rules: [
          {
            test: /\.js$/,
            exclude: /node_modules/,
            use: [
              { loader: 'babel-loader', options: { sourceMap: isDevelopment } },
            ],
          },
          {
            test: /\.scss$/,
            use: [
              MiniCssExtractPlugin.loader,
              {
                loader: 'css-loader',
                options: {
                  sourceMap: isDevelopment,
                  url: false,
                }
              },
              {
                loader: 'postcss-loader',
                options: {
                  sourceMap: isDevelopment,
                  postcssOptions: {
                    plugins: [
                      postcssUrl({
                        url: (asset) => asset.url.startsWith('data:') ? asset.url : `/_resources/app/client/${asset.url}?${Date.now()}`,
                      }),
                      postcssCriticalCSS({
                        outputPath: config.criticalCSSOutput,
                        preserve: false,
                      }),
                    ],
                  },
                },
              },
              {
                loader: 'sass-loader', options: {
                  sourceMap: isDevelopment,
                }
              },
            ]
          },
        ]
      },
      optimization: {
        minimizer: [
          new TerserPlugin({
            minify: TerserPlugin.uglifyJsMinify,
          }),
          new CssMinimizerPlugin()
        ]
      },
      plugins: [
        new RemoveEmptyScriptsPlugin(),
        new FriendlyErrorsWebpackPlugin(),
        new MiniCssExtractPlugin({
          filename: '../styles/[name].css',
          chunkFilename: '[name].css'
        }),
      ].filter(Boolean),
    }
  });
});

module.exports = configs;
