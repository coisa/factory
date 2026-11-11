.DEFAULT_GOAL := check

.PHONY: install check test docs

install:
	composer install --no-plugins

check:
	composer check

test:
	composer test

docs:
	composer docs
