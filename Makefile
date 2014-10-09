PHPCS_STANDARD = conf/BBC
PHPCS_PATH = src/
PHPCS_OPTIONS = -n -v --extensions=php

setup: fixtures vendor
test: fixtures phpcs phpunit

clean:
	rm -f composer.phar
	rm -rf vendor
	rm -rf node_modules

composer.phar:
	curl -sS https://getcomposer.org/installer | php -d detect_unicode=Off

vendor: composer.phar
	COMPOSER=composer.json COMPOSER_HOME=.composer php -d disable_functions= composer.phar update

	# Delete temp lock file for branch switch
	rm composer.lock

node_modules:
	npm install

fixtures: node_modules
	@echo "Generating Fixtures"
	node scripts/fixtures.js $(FIXTURATOR_FLAGS)

phpcs: vendor
	@echo "PHP Codesniffer"
	./vendor/bin/phpcs --standard=$(PHPCS_STANDARD) $(PHPCS_OPTIONS) $(PHPCS_PATH)


phpunit: vendor
	@echo "PHP Unit"
	./vendor/bin/phpunit

codeclimate: test
	@echo "Sending data to Code Climate"
	./vendor/bin/test-reporter --coverage-report=./coverage/clover.xml
