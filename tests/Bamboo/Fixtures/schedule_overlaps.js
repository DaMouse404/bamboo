var util = require('util');

module.exports = function (creator, fixtureName) {

    var date = new Date(),
        year = date.getFullYear(),
        month = date.getMonth() + 1,
        day = date.getDate(),
        today;
    month = month < 10 ? '0' + month : month;
    day = day < 10 ? '0' + day : day;
    today = [year, month, day].join("-");

    return creator.createFixture('channels/cbeebies/schedule/' + today).then(function (fixture) {

       var broadcast = JSON.parse(JSON.stringify(fixture.data.schedule.elements[0]));
           fixture.data.schedule.elements = [];
           hours = [
               ['06:00', '07:00'],
               ['07:00', '08:00'],
               ['08:00', '09:00'],
               ['09:00', '10:00'],
               ['10:00', '11:00'],
               ['11:00', '12:00'],
               ['12:00', '13:00'],
               ['13:00', '14:00']
           ];

       function createBroadcastsFromTimeSeries() {
           var broadcasts = [],
               i = 0, hour, newBroadcast;
           for (hour in hours) {
               newBroadcast = JSON.parse(JSON.stringify(broadcast));
               newBroadcast.scheduled_start = today + "T"+hours[hour][0]+":00.000Z";
               newBroadcast.scheduled_end = today + "T"+hours[hour][1]+":00.000Z";
               broadcasts.push(newBroadcast);
           }
           return broadcasts;
       }

       fixture.data.schedule.elements = createBroadcastsFromTimeSeries();
       fixture.save(fixtureName + '_correct');

       hours[2] = ['08:15', '09:00'];
       fixture.data.schedule.elements = createBroadcastsFromTimeSeries();
       fixture.save(fixtureName + '_gap');

       hours[2] = ['07:55', '09:00'];
       fixture.data.schedule.elements = createBroadcastsFromTimeSeries();
       fixture.save(fixtureName + '_overlap');

       hours[1] = ['07:00', '09:00'];
       hours[2] = ['07:10', '08:50'];
       fixture.data.schedule.elements = createBroadcastsFromTimeSeries();
       fixture.save(fixtureName + '_inside');

       hours[1] = ['07:00', '09:00'];
       hours[2] = ['07:00', '09:00'];
       fixture.data.schedule.elements = createBroadcastsFromTimeSeries();
       fixture.save(fixtureName + '_duplicated');

       hours = [
           ['06:00', '06:10'],
           ['06:00', '08:00'],
           ['06:10', '06:20'],
           ['06:20', '06:35'],
           ['06:35', '06:45'],
           ['06:45', '06:55'],
           ['06:55', '07:00'],
           ['07:00', '07:05'],
           ['07:05', '07:20'],
           ['07:20', '07:30'],
           ['07:30', '07:45'],
           ['07:45', '08:00'],
           ['08:00', '10:00'],
           ['08:00', '08:10'],
           ['08:10', '08:30'],
           ['08:30', '08:35'],
       ];
       fixture.data.schedule.elements = createBroadcastsFromTimeSeries();
       fixture.save(fixtureName + '_multi_overlap');

    });

};
