var gulp = require('gulp'),
    uglifyJS = require('gulp-uglify'),
    concat = require('gulp-concat');

gulp.task('script', function () {
    gulp.src('web/assets_client/js/app.js')
        .pipe(concat('app.min.js'))
        .pipe(uglifyJS())
        .pipe(gulp.dest('./web/assets_client/js/'))
});