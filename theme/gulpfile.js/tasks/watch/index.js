const
    config = require('../../lib/configLoader'),
    gulp = require('gulp'),
    series = require('gulp').series,
    path = require('path'),
    watch = require('gulp-watch'),
    browserSyncTask = require('../browserSync'),
    watchTask = () => {
        const watchableTasks = ['images', 'svgsprite', 'themeTwig', 'css', 'scripts','eslint', 'stylelint', 'static', 'fonts', 'jsonData'];

        watchableTasks.forEach((taskName) => {
            const task = config.tasks[taskName];
            console.log(task.extensions, Array.isArray(task.extensions));
            if (task) {
                const glob = Array.isArray(task.extensions) ? path.join(config.root.src, task.src, '**/*.{' + task.extensions.join(',') + '}') : path.join(config.root.src, task.src, '**/*.' + task.extensions);
                watch(glob, gulp.series(taskName));
            }
        });
    };

gulp.task('watch', watchTask);
module.exports = watchTask;
