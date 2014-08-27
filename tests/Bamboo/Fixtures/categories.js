module.exports = function (creator, fixtureName) {
    return creator.createFixture('categories').then(function (fixture) {
        fixture.save(fixtureName);
    });
};
