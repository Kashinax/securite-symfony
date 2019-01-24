// JS
var gulp = require('gulp');
var sass = require('gulp-sass');
var browserSync = require('browser-sync');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var runSequence = require('run-sequence');
// var minifyCSS = require('gulp-minify-css');

// Tâche gulp pour dire bonjour ;)
gulp.task('hello', function(){
    console.log('Hello Equipe U Make !');
});

// Tâche pour "watcher" les fichiers scss
gulp.task('watch', ['sass'], function(){
    gulp.watch('assets/scss/**/*.scss', ['sass']);
    gulp.watch('templates/**/*.twig', browserSync.reload);
    gulp.watch('templates/*.twig', browserSync.reload);
    gulp.watch('src/**/*.php', browserSync.reload);
});

// Tâche gulp pour compiler scss en css
gulp.task('sass', function(){
    return gulp.src('assets/scss/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sourcemaps.write({
            includeContent: false
        }))
        .pipe(sass({
            outputStyle: 'expanded'
        }).on('error', sass.logError))
        .pipe(gulp.dest('./public/build/css'))
        .pipe(browserSync.reload({
            stream: true
        }))
});


// Tâche pour browserSync avec Wamp
gulp.task('browser-sync', function() {
    browserSync({
        proxy: 'http://127.0.0.1:8000/',
        port: 8080,
        open: true,
        notify: false
    })
});

// Tâche pour automatiser la compilation, le refresh et le watch
gulp.task('default', ['sass', 'watch', 'browser-sync'], function(){});
