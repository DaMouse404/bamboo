module.exports = function (creator, fixtureName) {
    return creator.createFixture('programmes/b007v097').then(function (fixture) {
        fixture.save(fixtureName);
    });
};
