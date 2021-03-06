const
    config = require('../../lib/configLoader'),
    gulp = require('gulp'),
    path = require('path'),
    rev = require('gulp-rev');

// 1) Add md5 hashes to assets referenced by CSS and JS files
gulp.task('rev-assets', () => {
    // Ignore files that may reference assets. We'll rev them next.
    const ignoreThese = '!' + path.join(config.root.dest, '/**/*+(css|js|json|html|ico)');

    return gulp.src([path.join(config.root.dest, '/**/*'), ignoreThese])
        .pipe(rev())
        .pipe(gulp.dest(config.root.dest))
        .pipe(rev.manifest(path.join(config.root.dest, 'rev-manifest.json'), {
            merge: true
        }))
        .pipe(gulp.dest(''));
});
