module.exports = function (creator, fixtureName) {
    return creator.createFixture('channels/bbc_one_london/schedule/2014-08-10').then(function (fixture) {
        fixture.save(fixtureName);
    });
};