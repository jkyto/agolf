var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('styles', function() {
    gulp.src('../drupal/themes/custom/agolf/sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('../drupal/themes/custom/agolf/css/base/'))
});

//Watch task
gulp.task('default',function() {
    gulp.watch('../drupal/themes/custom/agolf/sass/**/*.scss',['styles']);
});