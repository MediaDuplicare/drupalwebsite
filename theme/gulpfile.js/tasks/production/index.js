const
    config = require('../../lib/configLoader'),
    gulp = require('gulp'),
    gulpSequence = require('gulp-sequence'),
    getEnabledTasks = require('../../lib/getEnabledTasks'),
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
    revTask = require('../rev'),
    sizeReportTask = require('../sizereport'),
    jsonDataTask = require('../jsonData'),
    productionTask = (cb) => {
        global.production = true;

        // const tasks = getEnabledTasks('production');
        // gulp.series('clean', tasks.lintTasks, tasks.assetTasks, tasks.codeTasks, 'static', config.tasks.production.rev ? 'rev' : false, 'size-report', cb);
        cb();
    };

// gulp.task('production', gulp.series(cleanTask, gulp.parallel(eslintTask, stylelintTask), gulp.parallel(imagesTask, svgSpriteTask), gulp.parallel(cssTask, twigTask, jsonDataTask, scriptTask), staticTask, fontTask, config.tasks.production.rev ? revTask : false, sizeReportTask));
gulp.task('production', gulp.series(cleanTask, gulp.parallel(eslintTask, stylelintTask), gulp.parallel(imagesTask, svgSpriteTask), gulp.parallel(cssTask, twigTask, jsonDataTask, scriptTask), staticTask, fontTask, sizeReportTask));
module.exports = productionTask;
