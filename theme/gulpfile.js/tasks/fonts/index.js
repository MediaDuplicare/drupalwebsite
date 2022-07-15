const
    config = require('../../lib/configLoader'),
    changed = require('gulp-changed'),
    customNotifier = require('../../lib/customNotifier'),
    gulp = require('gulp'),
    path = require('path'),
    paths = {
        src: [
            path.join(config.root.src, config.tasks.fonts.src, '/**/*')
        ],
        dest: path.join(config.root.dest, config.tasks.fonts.dest)
    },
    fontTask = () => {
        return gulp.src(paths.src)
            .pipe(changed(paths.dest)) // Ignore unchanged files
            .pipe(gulp.dest(paths.dest))
            .pipe(customNotifier({ title: 'Font files copied' }));
    };

gulp.task('fonts', fontTask);
module.exports = fontTask;
