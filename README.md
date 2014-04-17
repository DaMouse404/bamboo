#PHP client library for iBL

[![](http://hubson.cloud.bbc.co.uk/badges/bamboo)](https://ci-pal.int.bbc.co.uk/hudson/view/iPlayer/job/bamboo/)

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
