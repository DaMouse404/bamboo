PHPCS_STANDARD = conf/BBC
PHPCS_PATH = src/
PHPCS_OPTIONS = -n -v --extensions=php

phpcs:
	@echo "PHP Codesniffer"
	phpcs --standard=$(PHPCS_STANDARD) $(PHPCS_OPTIONS) $(PHPCS_PATH)


phpunit:
	@echo "PHP Unit"
	./vendor/bin/phpunit --coverage-html ./coverage 

test: phpcs phpunit
