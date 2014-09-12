module.exports = function (creator, fixtureName) {
    var today = new Date(),
        day = today.getDate(),
        month = today.getMonth() + 1,
        year = today.getFullYear(),
        timestamp;

    if (day < 10) {
        day = '0' + day;
    }

    if (month < 10) {
        month = '0' + month;
    }

    timestamp = [year, month, day].join('-');

    return creator.createFixture('channels/bbc_one_london/schedule/' + timestamp).then(function (fixture) {
        fixture.save(fixtureName);
    });
};
