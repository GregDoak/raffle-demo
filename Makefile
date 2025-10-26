default: help
SHELL := /bin/bash
MAKE := make --no-print-directory

.PHONY: can-release
can-release: ## Runs all checks required for release
	@cd backend && ${MAKE} can-release
	@cd frontend && ${MAKE} can-release

.PHONY: destroy
destroy: stop ## Destroys the development environment of the full stack
	${MAKE} -j2 backend/destroy frontend/destroy

.PHONY: restart
restart: stop start ## Restarts the development environment of the full stack

.PHONY: start
start: ## Starts the development environment of the full stack
	${MAKE} -j2 backend/start frontend/start

.PHONY: stop
stop: ## Stops the development environment of the full stack
	${MAKE} -j2 backend/stop frontend/stop

.PHONY: backend/shell
backend/shell: ## Shell into the default backend service
	@cd backend && ${MAKE} shell

.PHONY: backend/destroy
backend/destroy: ## Destroys the development environment of the backend service
	@cd backend && ${MAKE} destroy

.PHONY: backend/start
backend/start: ## Starts the development environment of the backend service
	@cd backend && ${MAKE} start

.PHONY: backend/stop
backend/stop: ## Stops the development environment of the backend service
	@cd backend && ${MAKE} stop

.PHONY: frontend/destroy
frontend/destroy: ## Destroys the development environment of the frontend service
	@cd frontend && ${MAKE} destroy

.PHONY: frontend/shell
frontend/shell: ## Shell into the default backend service
	@cd frontend && ${MAKE} shell

.PHONY: frontend/start
frontend/start: ## Starts the development environment of the frontend service
	@cd frontend && ${MAKE} start

.PHONY: frontend/stop
frontend/stop: ## Stops the development environment of the frontend service
	@cd frontend && ${MAKE} stop

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
