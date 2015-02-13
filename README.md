#PHP client library for iBL

[![Build Status](https://ci-pal.int.bbc.co.uk/hudson/job/bamboo/ws/bamboo/badges/build.svg)](https://ci-pal.int.bbc.co.uk/hudson/view/iPlayer/job/bamboo/)
[![Test Coverage](https://ci-pal.int.bbc.co.uk/hudson/job/bamboo/ws/bamboo/badges/coverage.svg)](https://ci-pal.int.bbc.co.uk/hudson/job/bamboo/ws/bamboo/coverage/)

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
