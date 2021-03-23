// Imports
const WebpackNotifierPlugin = require('webpack-notifier');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (mainDir, assetsDir) => {
	return {
		output: {
			filename: `${assetsDir}/front/js/[name].js`,
			path: mainDir
		},
		devtool: "source-map",
		plugins: [
			new WebpackNotifierPlugin({ alwaysNotify: true }),
			new MiniCssExtractPlugin({
				// Options similar to the same options in webpackOptions.output
				// both options are optional
				filename: `${assetsDir}/front/css/[name].css`
			})
		]
	};
};