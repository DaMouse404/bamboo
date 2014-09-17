module.exports = function (creator, fixtureName) {
    return creator.createFixture('categories/arts/highlights').then(function (fixture) {
        fixture.save(fixtureName);
    });
};
