var gulp = require('gulp');
var less = require('gulp-less');
var minifycss = require('gulp-minify-css');
var notify = require('gulp-notify');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var path = require('path');
var prefix = require('gulp-autoprefixer');

gulp.task('less', function() {
    return gulp.src(['less/main.less'])
            .pipe(less({
                paths: [ path.join(__dirname, 'bower_components', 'bootstrap', 'less') ]
            }))
            .pipe(prefix("last 3 version", "> 1%", "ie 8", "ie 7"))
            .pipe(minifycss())
            .pipe(gulp.dest('css'))
            .pipe(notify({"message": "LESS compiled!"}));
});

var third_party = [
    'bower_components/jquery/dist/jquery.min.js',
    'bower_components/bootstrap/dist/js/bootstrap.min.js',
];

// The JavaScript Task is currently unused, but will be used soon for Angular
gulp.task('js', function() {
    return gulp.src(third_party.concat(['js/**/*.js']))
            .pipe(concat('main.js'))
            .pipe(uglify())
            .pipe(gulp.dest('js'))
            .pipe(notify({"message": "JavaScript compiled!"}));
});

gulp.task('build', ['less'], function() {});

gulp.task('default', ['build'], function() {

    gulp.watch('less/*.less', ['less']);

    //gulp.watch('src/js/**/*.js', ['js']);
});
