#PHP client library for iBL

[![](http://hubson.cloud.bbc.co.uk/badges/bamboo)](https://ci-pal.int.bbc.co.uk/hudson/view/iPlayer/job/bamboo/)
[![Code Climate](https://codeclimate.com/github/iplayer/bamboo/badges/gpa.svg)](https://codeclimate.com/github/iplayer/bamboo)
[![Test Coverage](https://codeclimate.com/github/iplayer/bamboo/badges/coverage.svg)](https://codeclimate.com/github/iplayer/bamboo)

Setup dependencies.

    composer install

Run Unit tests.

    ./vendor/bin/phpunit

Usage

    use Bamboo\Feeds;
    try {
        $highlightsObject = new Feeds\Highlights\Home();
    } catch (Feeds\Exception $e) {
      //exception
    }
    echo $highlightsObject->getEpisode('b03x19tb')->getSubtitle();
