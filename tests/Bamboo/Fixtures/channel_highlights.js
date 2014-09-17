module.exports = function (creator, fixtureName) {
    return creator.createFixture('channels/bbc_one_london/highlights').then(function (fixture) {
        fixture.save(fixtureName);
    });
};
