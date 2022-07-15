const
    config = require('../../lib/configLoader'),
    gulp = require('gulp'),
    sizereport = require('gulp-sizereport'),

    sizeReportTask = (cb) => {
        return gulp.src([config.root.dest + '/**/*', '*!rev-manifest.json'])
            .pipe(sizereport({
                gzip: true
            }));
    };

gulp.task('size-report', sizeReportTask);
module.exports = sizeReportTask;
