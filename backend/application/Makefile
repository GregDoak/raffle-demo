default: help
SHELL := /bin/bash

.PHONY: can-release
can-release: test lint static ## Runs all checks required for release

.PHONY: test
test: test/unit test/integration test/functional test/mutation ## Runs the full test suite

.PHONY: test/functional
test/functional: ## Runs the functional test suite
	APP_ENV=test bin/phpunit --testsuite Functional --order-by=random

.PHONY: test/integration
test/integration: ## Runs the integration test suite
	APP_ENV=test bin/phpunit --testsuite Integration --order-by=random

.PHONY: test/mutation
test/mutation: ## Runs the mutation test suite
	vendor/bin/infection --initial-tests-php-options="-d zend_extension=/opt/bref/extensions/xdebug.so" --threads=max

.PHONY: test/unit
test/unit: ## Runs the unit test suite
	bin/phpunit --testsuite Unit --order-by=random

.PHONY: test/%
test/%: ## Runs the unit test suite can filter using test/some-filter
	bin/phpunit --filter $*

.PHONY: lint
lint: ## Enforces the code standards checks using preconfigured rules
	@PHP_CS_FIXER_FUTURE_MODE=1 vendor/bin/php-cs-fixer check -v
	@vendor/bin/yaml-lint . --parse-tags

.PHONY: lint/fix
lint/fix: ## Fixes all linter errors
	@PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix -v

.PHONY: static
static: static/deptrac static/stan ## Runs all static analysis tools
	@composer audit

.PHONY: static/deptrac
static/deptrac: ## Runs the deptrac static analysis tool
	@vendor/bin/deptrac --fail-on-uncovered --report-uncovered

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
