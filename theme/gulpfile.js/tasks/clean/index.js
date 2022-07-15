const
    gulp = require('gulp'),
    del = require('del'),
    config = require('../../lib/configLoader'),
    cleanTask = (cb) => {
        del([`${config.root.dest}/**/*`, `!${config.root.dest}/templates`, `!${config.root.dest}/templates/*.twig`, `!${config.root.dest}/templates/fe_templates`, `!${config.root.dest}/templates/fe_templates/*.twig`], {force: true}).then(() => {
            cb();
        });
    };

gulp.task('clean', cleanTask);
module.exports = cleanTask;
// export {cleanTask};
