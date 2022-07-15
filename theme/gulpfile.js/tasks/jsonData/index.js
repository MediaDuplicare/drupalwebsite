const
    config = require('../../lib/configLoader'),
    changed = require('gulp-changed'),
    customNotifier = require('../../lib/customNotifier'),
    gulp = require('gulp'),
    path = require('path'),
    browserSync = require('browser-sync'),
    paths = {
        src: path.join(config.root.src, config.tasks.jsonData.src, '/**/*'),
        dest: path.join(config.root.dest, config.tasks.jsonData.dest)
    },
    jsonDataTask = () => {
        return gulp.src(paths.src)
            .pipe(changed(paths.dest)) // Ignore unchanged files
            .pipe(gulp.dest(paths.dest))
            .on('end', browserSync.reload)
            .pipe(customNotifier({ title: 'Static files copied' }));
    };

gulp.task('jsonData', jsonDataTask);
module.exports = jsonDataTask;

