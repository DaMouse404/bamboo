module.exports = function (creator, fixtureName) {
    return creator.createFixture('status').then(function (fixture) {
        fixture.data = {
            "param": {
                "sub-param": "This is a sub-param"
            }
        };

        fixture.save(fixtureName);
    });
};
