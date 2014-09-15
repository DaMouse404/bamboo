var util = require('util');

module.exports = function (creator, fixtureName) {

    var date = new Date();
    var today = date.getFullYear() + "-";
    var month = date.getMonth() + 1;
    today += month < 10 ? '0' + month : month;
    var date = date.getDate();
    today += "-";
    today += date < 10 ? '0' + date : date;

    return creator.createFixture('channels/cbeebies/schedule/' + today).then(function (fixture) {

       var broadcast = JSON.parse(JSON.stringify(fixture.data.schedule.elements[0]));
       fixture.data.schedule.elements = [];

       function createBroadcastsFromTimeSeries() {
           var broadcasts = [];
           var i = 0;
           for (var hour in hours) {
               var newBroadcast = JSON.parse(JSON.stringify(broadcast));
               newBroadcast.scheduled_start = today + "T"+hours[hour][0]+":00.000Z";
               newBroadcast.scheduled_end = today + "T"+hours[hour][1]+":00.000Z";
               broadcasts.push(newBroadcast);
           }
           return broadcasts;
       }

       var hours = [
           ['06:00', '07:00'],
           ['07:00', '08:00'],
           ['08:00', '09:00'],
           ['09:00', '10:00'],
           ['10:00', '11:00'],
           ['11:00', '12:00'],
           ['12:00', '13:00'],
           ['13:00', '14:00']
       ];

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

    });

};
