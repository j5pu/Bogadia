// ## Globals
// GULP variable declarations
var gulp = require('gulp'),
    //gutil = require('gulp-util'),
    autoprefixer = require('gulp-autoprefixer'),
    minifycss = require('gulp-minify-css'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    del = require('del');
    uglify = require('gulp-uglify');


// File path variable declarations
var stylePath = "";
var scriptPath = "";

// Paths array
var path = {
    css: [
        'assets/imageLightbox/*.css',
    ],
    imageLightboxJs: [
        'assets/imageLightbox/.js',
    ]
};


gulp.task('fv-minify', ['fv-minify-js', 'fv-minify-css']);

gulp.task('fv-minify-js', function(callback) {
    gulp.start('compile-modal-js');
    gulp.start('compile-lib-js');
    gulp.start('compile-upload-js');
    gulp.start('compile-lib-imageLightbox');
    gulp.start('compile-lib-evercookie');
    gulp.start('compile-lib-jquery-unveil');
    gulp.start('compile-lib-countdown-default');
});


gulp.task('fv-minify-css', function(callback) {
    gulp.start('compile-imageLightbox');
    gulp.start('compile-main-css');
    //gulp.start('complie-font-css');
});


// Process imageLightbox css
gulp.task('compile-imageLightbox', function() {
    del(['assets/imageLightbox/jQuery.imageLightbox.min.css']);

    return gulp.src('assets/imageLightbox/jQuery.imageLightbox.css')
        .pipe(concat('jQuery.imageLightbox.css'))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss())
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});

// Process MAIN css
gulp.task('compile-main-css', function() {
    del(['assets/css/fv_main.min.css']);

    return gulp.src('assets/css/fv_main.css')
        .pipe(concat('fv_main.css'))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss({keepSpecialComments :1}))
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});


// Process  JS
gulp.task('compile-modal-js', function() {
    del(['assets/js/fv_modal.min.js']);

    return gulp.src('assets/js/fv_modal.js')
        .pipe(concat('fv_modal.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});
gulp.task('compile-upload-js', function() {
    del(['assets/js/fv_upload.min.js']);

    return gulp.src('assets/js/fv_upload.js')
        .pipe(concat('fv_upload.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});
gulp.task('compile-lib-js', function() {
    del(['assets/js/fv_lib.min.js']);

    return gulp.src('assets/js/fv_lib.js')
        .pipe(concat('fv_lib.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});
gulp.task('compile-lib-evercookie', function() {
    del(['assets/evercookie/js/evercookie.min.js']);

    return gulp.src('assets/evercookie/js/evercookie.js')
        .pipe(concat('evercookie.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});
gulp.task('compile-lib-imageLightbox', function() {
    del(['assets/imageLightbox/jQuery.imageLightbox.min.js']);

    return gulp.src('assets/imageLightbox/jQuery.imageLightbox.js')
        .pipe(concat('jQuery.imageLightbox.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});
gulp.task('compile-lib-jquery-unveil', function() {
    del(['assets/vendor/jquery.unveil.min.js']);

    return gulp.src('assets/vendor/jquery.unveil.js')
        .pipe(concat('jquery.unveil.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});

gulp.task('compile-lib-countdown-default', function() {
    del(['addons/coutdown-deafult/assets/fv-countdown-default.min.js']);

    return gulp.src('addons/coutdown-deafult/assets/fv-countdown-default.js')
        .pipe(concat('fv-countdown-default.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});

/*
// Process FONTS css
gulp.task('complie-font-css', function() {
    del(['assets/icommon/fv_fonts.min.css']);

    return gulp.src('assets/icommon/fv_fonts.css')
        .pipe(concat('fv_fonts.css'))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss({keepSpecialComments :1}))
        .pipe(gulp.dest(function(file) {
            return file.base;
        }));
});
*/
/*
// ### Gulp
// `gulp` - Run a complete build. To compile for production run `gulp --production`.
gulp.task('default', ['complie-imageLightbox'], function() {
    gulp.start('build');
});
*/
