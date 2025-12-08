default: help
SHELL := /bin/bash
DOCKER_COMPOSE := docker compose --project-name "raffle-demo-shared" --file "docker-compose.yaml" --env-file=./../.env

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

.PHONY: help
help:
	@printf "Available targets:\n"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { \
		printf "  \x1b[32;01m%-35s\x1b[0m %s\n", $$1, $$2 \
		} /^##@/ { printf "\n\033[1m%s\033[0m\n", $$0 } ' \
		$(MAKEFILE_LIST) | sort -u
	@printf "\n"
