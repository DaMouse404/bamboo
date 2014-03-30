var fs = require('fs'),
    iniLib = require('ini'),
    iniFile = __dirname + '/../webapp/conf/tviplayer.ini',
    ini = iniLib.parse(fs.readFileSync(iniFile, {encoding: 'utf-8'})),
    config = {
        savePath: __dirname + '/../webapp/php/lib/test/fixtures/generated/bamboo/',
        fixturePath: __dirname + '/../tests/',
        apiKey: ini.live['bamboo.key'],
        iblUrl: 'http://d.bbc.co.uk/ibl/v1/',
        cacheDir: __dirname + '/../tests/feedCache/',
        proxy: process.env.http_proxy,
        debug: true,
        spaces: '  '
    },
    Fixtures = require('fixturator'),
    creator = new Fixtures(config);

    var deleteFilesRecursive = function(path) {
        if( fs.existsSync(path) ) {
            fs.readdirSync(path).forEach(function(file,index){
                var curPath = path + "/" + file;
                if (file == '.gitkeep')
                    return true;
                if(fs.lstatSync(curPath).isDirectory()) { // recurse
                    deleteFilesRecursive(curPath);
                } else { // delete file
                    fs.unlinkSync(curPath);
                }
            });
        }
    };

    deleteFilesRecursive(config.savePath);

    creator.prefetch.done(function () {
        files = fs.readdirSync(config.fixturePath)

        files.forEach(function (file) {
            console.time(file);

            stat = fs.statSync(config.fixturePath + file);
            if (stat.isFile()) {
                var func = require(config.fixturePath + file);
                fixtureName = file.substr(0, file.length - 3);
                func(creator, fixtureName).done(function () {
                    console.timeEnd(file);
                });
            }

        })
    })
