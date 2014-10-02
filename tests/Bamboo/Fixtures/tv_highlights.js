module.exports = function (creator, fixtureName) {
    return creator.createFixture('tv/highlights').then(function (fixture) {
        fixture.save(fixtureName);
    });
};
