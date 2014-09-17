module.exports = function (creator, fixtureName) {
    return creator.createFixture('groups/p00zw1jd/episodes').then(function (fixture) {
        fixture.data.group_episodes.count = 20
        fixture.save(fixtureName);
    });
};
