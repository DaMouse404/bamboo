#PHP client library for iBL

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