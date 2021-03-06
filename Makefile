php=php
perl=perl
composer=composer
phpcs=$(php) vendor/bin/phpcs
phpunit=$(php) vendor/bin/phpunit
yaml2json=$(perl) bin/yaml2json.pl

all: | vendor test

clean:
	git clean -xdf -e vendor

vendor: composer.json
	$(composer) --prefer-dist --optimize-autoloader --profile install
	rm composer.lock

%.json: %.yaml
	$(yaml2json) < $? > $@

tests/generated: test.json
	rm -rf $@
	$(php) bin/enum-generator test.json $@

test: | tests/generated lint
	$(phpcs) --warning-severity=0 --standard=PSR2 src
	@echo "(No code coverage report available)" > coverage.txt
	rm -rf coverage/ tmp/
	$(phpunit) \
		--coverage-filter=src/ \
		--path-coverage \
		--coverage-text=coverage.txt \
		--coverage-html=coverage \
		--verbose \
		tests/
	@cat coverage.txt

lint:
	for file in `find src tests -name '*.php' | sort`; do $(php) -l $$file || exit 1; done

archive: | clean composer.json
	$(composer) archive

.PHONY: all clean test lint archive
