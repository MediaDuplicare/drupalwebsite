
const
    gulp = require('gulp'),
    gulpSequence = require('gulp-sequence'),
    getEnabledTasks = require('../../lib/getEnabledTasks'),
    parallel = require('gulp'),
    cleanTask = require('../clean'),
    eslintTask = require('../eslint'),
    stylelintTask = require('../stylelint'),
    imagesTask = require('../images'),
    svgSpriteTask = require('../svgsprite'),
    twigTask = require('../themeTwig'),
    cssTask = require('../css'),
    scriptTask = require('../scripts'),
    staticTask = require('../static'),
    fontTask = require('../fonts'),
    watchTask = require('../watch'),
    browserSyncTask = require('../browserSync'),
    jsonDataTask = require('../jsonData'),
    defaultTask = (cb) => {
        // const tasks = getEnabledTasks('watch');
        // gulp.series(cleanTask, gulp.parallel(eslintTask, stylelintTask), gulp.parallel(imagesTask, svgSpriteTask), gulp.parallel(templatesTask, cssTask), staticTask, fontTask, watchTask)
        cb();
    };

gulp.task('default', gulp.series(cleanTask, gulp.parallel(eslintTask, stylelintTask), gulp.parallel(imagesTask, svgSpriteTask), gulp.parallel(cssTask, twigTask, jsonDataTask, scriptTask), staticTask, fontTask, gulp.parallel(browserSyncTask, watchTask)));
module.exports = defaultTask;
