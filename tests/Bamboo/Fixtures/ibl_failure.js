module.exports = function (creator, fixtureName) {
    return creator.createFixture('status').then(function (fixture) {
        fixture.data = {
            "error": {
                "id": 7006,
                "details": "Command execution exception: Command execution exception: uk.co.bbc.iplayer.common.concurrency.MoreFuturesException: Timed out"
            }
        };

        fixture.save(fixtureName);
    });
};