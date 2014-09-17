module.exports = function (creator, fixtureName) {
   return creator.createFixture('categories/arts/programmes').then(function (fixture) {
        fixture.data.category_programmes.count = 20;
        fixture.save(fixtureName);
    });
};
