const gulp = require('gulp');
var del = require('del');
const config = require('../config');

gulp.task('clean', function () {
    return del([config.wp.dist + '/**']);
});
