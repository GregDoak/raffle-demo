default: help
SHELL := /bin/bash
DOCKER_COMPOSE := docker compose --project-name "raffle-demo-frontend" --file "docker-compose.yaml"

.PHONY: can-release
can-release: ## Runs all checks required for release
	@${DOCKER_COMPOSE} run -it --rm admin-node make can-release

.PHONY: destroy
destroy: ## Destroys the development environment
	@${DOCKER_COMPOSE} down --volumes

.PHONY: restart
restart: stop start ## Restarts the development environment

.PHONY: start
start: ## Starts the development environment
	@${DOCKER_COMPOSE} up --build --remove-orphans

.PHONY: stop
stop: ## Stops the development environment
	@${DOCKER_COMPOSE} stop

.PHONY: shell
shell: ## Shell into the default service
	@${DOCKER_COMPOSE} run -it --rm admin-node /bin/sh

.PHONY: help
help:
	@printf "Available targets:\n"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { \
		printf "  \x1b[32;01m%-35s\x1b[0m %s\n", $$1, $$2 \
		} /^##@/ { printf "\n\033[1m%s\033[0m\n", $$0 } ' \
		$(MAKEFILE_LIST) | sort -u
	@printf "\n"
