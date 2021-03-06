const config = require('../../lib/configLoader');
if (!config.tasks.stylelint) return;

const
    gulp = require('gulp'),
    stylelint = require('gulp-stylelint'),
    path = require('path'),
    paths = {
        src: path.join(config.root.src, config.tasks.stylelint.src, '/**/*.{' + config.tasks.stylelint.extensions + '}')
    },
    stylelintTask = () => {
        return gulp.src([paths.src])
            .pipe(stylelint({
                failAfterError: config.root.env === 'production' ? true : false,
                reporters: [{
                    formatter: 'string',
                    console: true
                }]
            }));
    };

gulp.task('stylelint', stylelintTask);
module.exports = stylelintTask;
