const gulp = require('gulp');

const webpackStream = require("webpack-stream");
const webpack = require("webpack");
const devWebpackConfig = require("../../webpack.config.dev");
const prodWebpackConfig = require("../../webpack.config.prod");

const config = require('../config');

gulp.task("webpack:dev", function (done) {
  webpackStream(devWebpackConfig, webpack)
    .pipe(gulp.dest(config.wp.dist + '/js'));
  setTimeout(done, 3000);
});

gulp.task("webpack:prod", function (done) {
  webpackStream(prodWebpackConfig, webpack)
    .pipe(gulp.dest(config.wp.dist + '/js'));
  done();
});
