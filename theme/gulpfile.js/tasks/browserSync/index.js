if (global.production) return;

const
    browserSync = require('browser-sync'),
    gulp = require('gulp'),
    config = require('../../lib/configLoader'),
    pathToUrl = require('../../lib/pathToUrl'),

    browserSyncTask = () => {

        const
            proxyConfig = config.tasks.browserSync.proxy || null;

        if (typeof(proxyConfig) === 'string') {
            config.tasks.browserSync.proxy = {
                target: proxyConfig
            };
        }

        const server = config.tasks.browserSync.proxy || config.tasks.browserSync.server;

        console.log('server', server);

        browserSync.init(config.tasks.browserSync);

    };

gulp.task('browserSync', browserSyncTask);
module.exports = browserSyncTask;
