
const
    getArg = require('./lib/getArg'),
    destFolder = getArg('--build') ? '../web/web/themes/custom/duplicare' : '../web/web/themes/custom/duplicare',
    env = getArg('--build') ? 'production' : 'development';

module.exports = {
    'root': {
        'src': './',
        'dest': destFolder,
        'env': env
    },

    'tasks': {
        'browserSync': {
            'proxy': 'http://duplicare.local/',
            'host': 'duplicare.local',
            'open': 'external'
        },
        'static': {},
        'jsonData': {},
        'fonts': {},
        'css': {},
        'themeTwig': {},
        'images': {},
        'svgsprite': {},
        'production': {},
        'eslint': {},
        'stylelint': {},
        'scripts': {
            'babel': {
                'presets': [['es2015', { 'modules': false }]],
                'plugins': []
            },
        }
    }
};
