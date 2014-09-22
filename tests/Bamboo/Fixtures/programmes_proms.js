module.exports = function (creator, fixtureName) {
    return creator.createFixture('programmes/b007v097').then(function (fixture) {
    	fixture.data.programmes.push(fixture.data.programmes[0]);
        fixture.save(fixtureName);
    });
};
