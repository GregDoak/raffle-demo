default: help
SHELL := /bin/bash
DOCKER_COMPOSE := docker compose --project-name "raffle-demo" --file "docker-compose.yaml"

.PHONY: can-release
can-release: ## Runs all checks required for release
	@${DOCKER_COMPOSE} run -it --rm backend-php make can-release

.PHONY: build
build: ## Build the local development environment
	@${DOCKER_COMPOSE} run -it --rm backend-php composer install --no-interaction
	@${DOCKER_COMPOSE} run -it --rm backend-php bin/console doctrine:database:create --if-not-exists
	@${DOCKER_COMPOSE} run -it --rm backend-php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --all-or-nothing

.PHONY: start
start: build ## Starts the development environment
	@${DOCKER_COMPOSE} up --build --remove-orphans

.PHONY: shell
shell: ## Shell into the default service
	@${DOCKER_COMPOSE} run -it --rm backend-php /bin/bash

.PHONY: help
help:
	@printf "Available targets:\n"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { \
		printf "  \x1b[32;01m%-35s\x1b[0m %s\n", $$1, $$2 \
		} /^##@/ { printf "\n\033[1m%s\033[0m\n", $$0 } ' \
		$(MAKEFILE_LIST) | sort -u
	@printf "\n"
