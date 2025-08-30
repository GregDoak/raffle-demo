default: help
SHELL := /bin/sh

.PHONY: can-release
can-release: test lint ## Runs all checks required for release

.PHONY: test
test: ## Runs the full test suite
	@npm run test

.PHONY: lint
lint: ## Enforces the code standards checks using preconfigured rules
	@npm run lint

.PHONY: lint/fix
lint/fix: ## Fixes all linter errors
	@npm run format

.PHONY: help
help:
	@printf "Available targets:\n"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { \
		printf "  \x1b[32;01m%-35s\x1b[0m %s\n", $$1, $$2 \
		} /^##@/ { printf "\n\033[1m%s\033[0m\n", $$0 } ' \
		$(MAKEFILE_LIST) | sort -u
	@printf "\n"
