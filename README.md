#PHP client library for iBL

[![Build Status](https://secure.travis-ci.org/craigtaub/bamboo2?branch=master)](http://travis-ci.org/craigtaub/bamboo2)

Setup dependencies.

    composer install

Run Unit tests.

	./vendor/bin/phpunit

Usage

    try {
        $highlightsObject = new Bamboo_Feeds_Highlights_Home();
    } catch (IblClient_Exception $e) {
      //exception
    }
    echo $highlightsObject->getEpisode('b03x19tb')->getSubtitle();