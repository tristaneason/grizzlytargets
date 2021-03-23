// Imports
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const WebpackShellPlugin = require('webpack-shell-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

module.exports = (mainDir, assetsDir, version, delete_min_files, travis_build) => {
	const productionDir = "./dist";
	const outPath = `${productionDir}/checkout-for-woocommerce`;
	const zipName = `checkout-for-woocommerce-${version}.zip`;

	let production = {
		optimization: {
			minimizer: [new UglifyJsPlugin()]
		},
		output: {
			filename: `${assetsDir}/front/js/[name].min.js`,
			path: mainDir
		},
		plugins: [
			new MiniCssExtractPlugin({
				// Options similar to the same options in webpackOptions.output
				// both options are optional
				filename: `${assetsDir}/front/css/[name].min.css`
			}),
			new OptimizeCssAssetsPlugin()
		]
	};

	if(version !== false && !travis_build) {
		let delete_files = [
			outPath + "/bin",
			outPath + "/dist",
			outPath + "/tests",
			outPath + "/cypress",
			outPath + "/**/node_modules",
			outPath + "/templates/**/style.css*",
			outPath + "/templates/**/theme.js*"
		];

		if ( delete_min_files ) {
			delete_files.push("./assets/front/**/*.min.*");
			delete_files.push("./templates/**/*.min.*");
		}

		production.plugins.push(
			new WebpackShellPlugin({
				safe: true,
				onBuildStart:[
					'rm -rf ' + productionDir + ' && mkdir -p ' + productionDir,
				],
				onBuildEnd:[
					"npx cpy --parents '.' '!./dist' '!./tests' '!./cypress' '!./**/node_modules' " + outPath
					+ " && cd " + productionDir
					+ " && zip --recurse-paths " + zipName + " ./checkout-for-woocommerce"
				]
			})
		)
	}

	return production;
};