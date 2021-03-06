const browserSync = require('browser-sync').create();
const gulp = require('gulp');

const config = require('../config');


gulp.task('reload', function(done){
    browserSync.reload();
    done();
});

gulp.task('watch', function () {
    browserSync.init(config.browserSync.wp);

    gulp.watch(
        [config.src.scss + '/**/*.scss'],
        gulp.series(['scss', 'reload']),
    );

    gulp.watch(
        [config.src.js + '/**/*.js'],
        gulp.series(['webpack:dev', 'reload']),
    );

    gulp.watch(
        [config.src.php + '/**/*.php'],
        gulp.series(['php', 'reload']),
    );

    gulp.watch(
        [config.src.img + '/**'],
        gulp.series(['img', 'reload']),
    );
});

