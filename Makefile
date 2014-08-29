PHPCS_STANDARD = conf/BBC
PHPCS_PATH = src/
PHPCS_OPTIONS = -n -v --extensions=php

node_modules:
	npm install

fixtures: node_modules
	@echo "Generating Fixtures"
	node scripts/fixtures.js $(FIXTURATOR_FLAGS)

phpcs:
	@echo "PHP Codesniffer"
	./vendor/bin/phpcs --standard=$(PHPCS_STANDARD) $(PHPCS_OPTIONS) $(PHPCS_PATH)


phpunit:
	@echo "PHP Unit"
	./vendor/bin/phpunit

test: fixtures phpcs phpunit
