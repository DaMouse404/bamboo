module.exports = function (creator, fixtureName) {
    return creator.createFixture('episodes/p014mxpr').then(function (fixture) {
        fixture.save(fixtureName);
    });
};
