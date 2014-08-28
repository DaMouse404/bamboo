module.exports = function (creator, fixtureName) {
    return creator.createFixture('status').then(function (fixture) {
        fixture.data = '';
        fixture.save(fixtureName);
    });
};