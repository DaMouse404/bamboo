module.exports = function (creator, fixtureName) {
    return creator.createFixture('tv/highlights').then(function (fixture) {
        fixture.addEpisode(0).set({
            type: "promotion",
            labels: {
                "promotion": "promotion"
            }
        });
        fixture.save(fixtureName);
    });
};
