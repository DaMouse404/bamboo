module.exports = function (creator, fixtureName) {
    return creator.createFixture('programmes/b006m86d/episodes').then(function (fixture) {
        fixture.data.programme_episodes.count = 20
        fixture.save(fixtureName);
    });
};
