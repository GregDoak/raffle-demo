default: help
SHELL := /bin/bash

.PHONY: can-release
can-release: test lint static ## Runs all checks required for release

.PHONY: test
test: test/unit test/integration test/functional ## Runs the full test suite

.PHONY: test/functional
test/functional: ## Runs the functional test suite
	APP_ENV=test bin/console doctrine:database:create --if-not-exists
	APP_ENV=test bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
	APP_ENV=test vendor/bin/phpunit --testsuite Functional --order-by=random

.PHONY: test/integration
test/integration: ## Runs the integration test suite
	APP_ENV=test bin/console doctrine:database:create --if-not-exists
	APP_ENV=test bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
	APP_ENV=test vendor/bin/phpunit --testsuite Integration --order-by=random

.PHONY: test/behat
test/behat: ## Runs the behaviour test suite
	APP_ENV=test vendor/bin/behat

.PHONY: test/unit
test/unit: ## Runs the unit test suite
	vendor/bin/phpunit --testsuite Unit --order-by=random

.PHONY: test/%
test/%: ## Runs the unit test suite can filter using test/some-filter
	vendor/bin/phpunit --filter $*

.PHONY: lint
lint: ## Enforces the code standards checks using preconfigured rules
	@vendor/bin/ecs
	@vendor/bin/yaml-lint $(shell find . -type f -regex ".*\.ya?ml" -not -path "./vendor/*" -not -path "./node_modules/*") --parse-tags

.PHONY: lint/fix
lint/fix: ## Fixes all linter errors
	@vendor/bin/ecs --fix

.PHONY: static
static: static/stan ## Runs all static analysis tools

.PHONY: static/stan
static/stan: ## Runs the PHPStan static analysis tool
	@vendor/bin/phpstan

.PHONY: help
help:
	@printf "Available targets:\n"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { \
		printf "  \x1b[32;01m%-35s\x1b[0m %s\n", $$1, $$2 \
		} /^##@/ { printf "\n\033[1m%s\033[0m\n", $$0 } ' \
		$(MAKEFILE_LIST) | sort -u
	@printf "\n"
