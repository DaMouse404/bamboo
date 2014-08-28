module.exports = function (creator, fixtureName) {
    return creator.createFixture('atoz/a/programmes').then(function (fixture) {
        fixture.save(fixtureName);
    });
};
