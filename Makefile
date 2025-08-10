default: help
SHELL := /bin/bash
MAKE := make --no-print-directory

.PHONY: can-release
can-release: ## Runs all checks required for release
	@cd backend && ${MAKE} can-release

.PHONY: start
start: ## Starts the development environment of the full stack
	@cd backend && ${MAKE} start

.PHONY: backend/shell
backend/shell: ## Shell into the default backend service
	@cd backend && ${MAKE} shell

records = 1
state = "all"
.PHONY: fixture/raffle
fixture/raffle: ## Creates raffle fixtures with optional arguments, for example: make fixture/raffle records=10 state=draw
	@cd backend && ${MAKE} fixture/raffle records=$(records) state=$(state)

.PHONY: help
help:
	@printf "Available targets:\n"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { \
		printf "  \x1b[32;01m%-35s\x1b[0m %s\n", $$1, $$2 \
		} /^##@/ { printf "\n\033[1m%s\033[0m\n", $$0 } ' \
		$(MAKEFILE_LIST) | sort -u
	@printf "\n"
