module.exports = function (creator, fixtureName) {
    return creator.createFixture('home/highlights').then(function (fixture) {
        fixture.save(fixtureName);
    });
};
