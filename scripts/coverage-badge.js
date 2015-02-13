var xml2js = require('xml2js'),
    fs = require('fs'),
    https = require('https'),
    parser = new xml2js.Parser(),
    xml = fs.readFileSync(process.argv[2]),
    svgFile = process.argv[3];

function pickColour(coverage) {
    var colours = [
        [90, 'brightgreen'],
        [70, 'green'],
        [60, 'yellowgreen'],
        [40, 'orange'],
        [0,  'red']
    ];

    return colours.filter(function(colour) {
        return colour[0] <= coverage;
    }).shift()[1];
}

function fetchSvg(subject, status, colour, cb) {
    return https.get({
        host: 'img.shields.io',
        path: [ '/badge/', subject, '-', status, '-', colour, '.svg'].join('')
    }, function(response) {
        // Continuously update stream with data
        var body = '';
        response.on('error', function(err) {
            cb(err);
        });
        response.on('data', function(d) {
            body += d;
        });
        response.on('end', function() {
            cb(null, body);
        });
    });
}

parser.parseString(xml, function(err, data) {
    if (err) {
        throw err;
    }

    var metrics = data.coverage.project[0].metrics[0].$,
        coverage = Math.round((metrics.coveredstatements/metrics.statements) * 10000) / 100,
        colour = pickColour(coverage);

    fetchSvg('coverage', coverage + '%', colour, function(err, svg) {
        if (err) {
            throw err;
        }

        fs.writeFileSync(svgFile, svg);
    });
});
