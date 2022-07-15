const
    config = require('../config'),
    compact = require('lodash/compact'),
    cleanTask = require('../tasks/clean'),
    eslintTask = require('../tasks/eslint'),
    stylelintTask = require('../tasks/stylelint'),
    imagesTask = require('../tasks/images'),
    svgSpriteTask = require('../tasks/svgsprite'),
    twigTask = require('../tasks/themeTwig'),
    phpTask = require('../tasks/themePhp'),
    cssTask = require('../tasks/css'),
    scriptTask = require('../tasks/scripts'),
    jsonDataTask = require('../tasks/jsonData'),
    // Grouped by what can run in parallel
    assetTasks = ['images', 'svgsprite'],
    codeTasks = ['css', 'scripts', 'themeTwig', 'jsonData'],
    lintTasks = ['eslint', 'stylelint'];

module.exports = (env) => {
    const
        matchFilter = (task) => {

            if (config.tasks[task]) {
                let t = config.tasks[task].taskName || task;
                if (t === 'js') {
                    t = env === 'production' ? 'scripts' : 'scripts';
                }
                return t;
            }

            return undefined;
        },
        exists = (value) => {
            return !!value;
        };
    return {
        assetTasks: compact(assetTasks.map(matchFilter).filter(exists)),
        codeTasks: compact(codeTasks.map(matchFilter).filter(exists)),
        lintTasks: compact(lintTasks.map(matchFilter).filter(exists))
    };
};
