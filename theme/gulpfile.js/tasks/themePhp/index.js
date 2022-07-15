const config = require('../../lib/configLoader');

if (!config.tasks.themePhp) return;

const
    gulp = require('gulp'),
    changed = require('gulp-changed'),
    plugins = require('gulp-load-plugins')({ camelize: true }),
    browserSync = require('browser-sync'),
    path = require('path'),
    handleErrors = require('../../lib/handleErrors'),
    customNotifier = require('../../lib/customNotifier'),
    paths = {
        srcPhp: path.join(config.root.src, config.tasks.themePhp.src, '/**/*.' + config.tasks.themePhp.extensions),
        destPhp: path.join(config.root.dest, config.tasks.themePhp.dest),
    },
    phpTask = () => {
        return gulp
            .src(paths.srcPhp)
            .pipe(changed(paths.destPhp))
            .on('error', handleErrors)
            .pipe(gulp.dest(paths.destPhp))
            .on('end', browserSync.reload)
            .pipe(customNotifier({ title: 'Template compiled' }));
    };

gulp.task('themePhp', phpTask);
module.exports = phpTask;
