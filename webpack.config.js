const path = require("path");

module.exports = {
	entry: {
		app: ["./src/script.js"],
	},
	output: {
		path: path.resolve(__dirname),
		filename: "script.min.js",
	},
	mode: "development",
	// module: {
	// 	rules: [
	// 		{
	// 			test: /\.js?$/,
	// 			exclude: /node_modules/,
	// 			loader: "babel-loader",
	// 			query: {
	// 				presets: ["@babel/preset-env"],
	// 			},
	// 		},
	// 	],
	// },
};
