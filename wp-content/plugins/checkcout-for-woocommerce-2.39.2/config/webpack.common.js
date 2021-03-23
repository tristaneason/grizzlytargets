// Imports
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TsClassMetaGeneratorPlugin = require('ts-class-meta-generator');

module.exports = sourcesDir => {
	return {
        externals: {
            jquery: 'jQuery',
        },
		entry: {
			"checkoutwc-front": [
				`${sourcesDir}/js/vendor.js`,
				`${sourcesDir}/ts/checkout.ts`,
				`${sourcesDir}/scss/front/checkout.scss`
			],
			"checkoutwc-order-pay": [
				`${sourcesDir}/js/vendor.js`,
				`${sourcesDir}/ts/checkout.ts`,
				`${sourcesDir}/ts/order-pay.ts`,
				`${sourcesDir}/scss/front/order-pay.scss`,
			],
			"checkoutwc-thank-you": [
				`${sourcesDir}/js/vendor.js`,
				`${sourcesDir}/ts/thank-you.ts`,
				`${sourcesDir}/scss/front/thank-you.scss`
			],
		},
		resolve: {
			extensions: ['.ts', '.js', '.json', '.scss']
		},
		stats: {
			colors: true
		},
		module: {
			rules: [
				{
					test: /\.ts$/,
					loader: ['ts-loader']
				},
				{
					test: /\.(scss|css)$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {
								sourceMap: true,
								minimize: false,
                                url: false
							}
						},
						{
							loader: 'postcss-loader',
							options: {
								sourceMap: true
							}
						},
						{
							loader: 'sass-loader',
							options: {
								sourceMap: true
							}
						}
					]
				}
			]
		},
		plugins: [
			new TsClassMetaGeneratorPlugin({
				siteName: 'CompatibilityClasses',
				srcFolder: `${sourcesDir}/ts/front/CFW`,
				siteMetaFileName: 'compatibility-classes.ts',
				siteMetaPath: `${sourcesDir}/ts`,
				importPath: './front/CFW',
				ignoreFiles: ["Main", "Compatibility"],
				ignoreFolders: ["Actions", "Decorators", "Definitions", "Elements", "Enums", "Services", "Types"]
			})
		]
	}
};