#PHP client library for iBL

[![Build Status](https://travis-ci.org/craigtaub/bamboo2.svg?branch=develop)](https://travis-ci.org/craigtaub/bamboo2)

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