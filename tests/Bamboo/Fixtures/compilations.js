module.exports = function (creator, fixtureName) {
    return creator.createFixture('compilations/bbc-four-archive/groups').then(function (fixture) {
        fixture.removeAllItems();
        fixture.insertGroup();
        fixture.save(fixtureName);
    });
};
