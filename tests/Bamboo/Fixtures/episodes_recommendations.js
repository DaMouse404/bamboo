module.exports = function (creator, fixtureName) {
    return creator.createFixture('/episodes/p00y1h7j/recommendations').then(function (fixture) {
        fixture.addEpisode();

        fixture.save(fixtureName);
    });
};
