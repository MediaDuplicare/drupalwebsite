const config = require('../../lib/configLoader');

if (!config.tasks.css) return;

const
    gulp = require('gulp'),
    gulpif = require('gulp-if'),
    browserSync = require('browser-sync'),
    path = require('path'),
    sourcemaps = require('gulp-sourcemaps'),
    handleErrors = require('../../lib/handleErrors'),
    customNotifier = require('../../lib/customNotifier'),
    uglify = require('gulp-uglify'),
    babel = require( 'gulp-babel' ),
    concat = require('gulp-concat'),
    browserify = require('browserify'),
    shim = require('browserify-shim'),
    babelify = require('babelify'),
    buffer = require('vinyl-buffer'),
    source = require('vinyl-source-stream'),
    notify = require('gulp-notify'),
    paths = {
        src: path.join(config.root.src, config.tasks.scripts.src, 'app.' + config.tasks.scripts.extensions),
        dest: path.join(config.root.dest, config.tasks.scripts.dest)
    },
    scriptsTask =  async (cb) => {
        console.log('scripts', paths.src, path.join(paths.dest, 'js/'));
        return await browserify({
            'entries': [`${paths.src}`],
            'debug': true,
            'transform': [
                babelify.configure({presets: ["@babel/preset-env"]})
            ]
        })
            .bundle()
            .on('error', function (err) {
                console.log(err);
                return notify(err);
            })
            .pipe(source('app.js'))
            .pipe(buffer())
            .pipe(gulpif(config.root.env !== 'production', sourcemaps.init({loadMaps: true})))
            .pipe(gulpif(config.root.env !== 'production', sourcemaps.write()))
            .pipe(gulpif(config.root.env === 'production', uglify()))
            .pipe(gulp.dest(path.join(paths.dest, 'js/')))
            .pipe(customNotifier({ title: `paths: ${paths.src}` }))
            .pipe(browserSync.stream());
    };

gulp.task('scripts', scriptsTask);
module.exports = scriptsTask;
