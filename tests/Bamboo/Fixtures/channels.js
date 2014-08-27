module.exports = function (creator, fixtureName) {
    return creator.createFixture('channels').then(function (fixture) {
        fixture.save(fixtureName);
    });
};