module.exports = function (creator, fixtureName) {
    return creator.createFixture('status').then(function (fixture) {
        fixture.data = {
            "fault": {
                "faultstring": "The Service is temporarily unavailable",
                "detail": {
                    "errorcode": "messaging.adaptors.http.flow.ServiceUnavailable"
                }
            }
        };

        fixture.save(fixtureName);
    });
};