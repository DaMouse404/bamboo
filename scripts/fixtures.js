var fs = require('fs'),
    path = require('path'),
    blowupOnFeedError = process.argv[2] !== '--ignoreErrors',
    config = {
        savePath: __dirname + '/../tests/fixtures/',
        cacheDir: __dirname + '/../tests/fixtures/cache/',
        fixturePath: __dirname + '/../tests/Bamboo/Fixtures/',
        apiKey: process.env.BAMBOO_APIKEY,
        iblUrl: process.env.BAMBOO_URL,
        proxy: process.env.http_proxy,
        debug: true,
        spaces: '  '
    },
    Fixtures = require('fixturator'),
    creator = new Fixtures(config);

function deleteFilesRecursive(filePath) {
    if(fs.existsSync(filePath)) {
        fs.readdirSync(filePath).forEach(function(file,index){
            var curPath = filePath + "/" + file;
            if (file == '.gitkeep')
                return true;

            if (path.resolve(curPath) === path.resolve(config.cacheDir))
                return true;

            if(fs.lstatSync(curPath).isDirectory()) {
                // Recurse
                deleteFilesRecursive(curPath);
            } else {
                // Delete file
                fs.unlinkSync(curPath);
            }
        });
    }
};

function failHandler(err) {
    if (blowupOnFeedError) {
        throw err;
    } else {
        console.error([
            '\n\n',
            Array(81).join('!'),
            '\n',
            Array(29).join('!'),
            ' FIXTURES NOT GENERATED ',
            Array(29).join('!'),
            '\n',
            Array(81).join('!'),
            '\n\n',
            'Fixture script was ran with --ignoreErrors flag so I\'ve skipped over all the errors and exited nicely for you'
            ].join('')
        )
    }
}

deleteFilesRecursive(config.savePath);

creator.prefetch.then(function () {
        files = fs.readdirSync(config.fixturePath)

        files.forEach(function (file) {
            console.time(file);

            stat = fs.statSync(config.fixturePath + file);
            if (stat.isFile()) {
                var func = require(config.fixturePath + file);
                fixtureName = file.substr(0, file.length - 3);

                func(creator, fixtureName).then(function () {
                    console.timeEnd(file);
                }).fail(failHandler).done();
            }

        })
    }).fail(failHandler).done();
