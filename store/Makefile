default: help
SHELL := /bin/bash
DOCKER_COMPOSE := docker compose --project-name "raffle-demo-store" --file "docker-compose.yaml" --env-file=./../.env

.PHONY: can-release
can-release: ## Runs all checks required for release
	@${DOCKER_COMPOSE} run -it --rm php make can-release

.PHONY: destroy
destroy: ## Destroys the development environment
	@${DOCKER_COMPOSE} down --volumes

.PHONY: restart
restart: stop start ## Restarts the development environment

.PHONY: build
build: ## Build the local development environment
	@${DOCKER_COMPOSE} run -T --rm php composer install --no-interaction
	@${DOCKER_COMPOSE} run -T --rm php bin/console doctrine:database:create --no-interaction --if-not-exists
	@${DOCKER_COMPOSE} run -T --rm php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
	@${DOCKER_COMPOSE} run -T --rm php bin/console lexik:jwt:generate-keypair --no-interaction --skip-if-exists
	@${DOCKER_COMPOSE} run -T --rm php bin/console sylius:payment:generate-key --no-interaction
	@${DOCKER_COMPOSE} run -T --rm php bin/console sylius:fixtures:load raffle_demo --no-interaction
	@${DOCKER_COMPOSE} run -T --rm php bin/console sylius:install:assets --no-interaction
	@${DOCKER_COMPOSE} run -T --rm php bin/console cache:clear --no-interaction

.PHONY: start
start: build ## Starts the development environment
	@${DOCKER_COMPOSE} up --build --remove-orphans

.PHONY: stop
stop: ## Stops the development environment
	@${DOCKER_COMPOSE} stop

.PHONY: shell
shell: ## Shell into the default service
	@${DOCKER_COMPOSE} run -it --rm php /bin/bash

.PHONY: help
help:
	@printf "Available targets:\n"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { \
		printf "  \x1b[32;01m%-35s\x1b[0m %s\n", $$1, $$2 \
		} /^##@/ { printf "\n\033[1m%s\033[0m\n", $$0 } ' \
		$(MAKEFILE_LIST) | sort -u
	@printf "\n"
