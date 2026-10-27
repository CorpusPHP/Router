SRC_FILES = $(shell find example src -type f -name '*.php')

README.md: $(SRC_FILES)
	vendor/bin/mddoc

.PHONY: fix
fix: cbf
	vendor/bin/php-cs-fixer fix

.PHONY: test
test: cs
	vendor/bin/phpunit

.PHONY: test-docs
test-docs:
	vendor/bin/mddoc --dry-run=true

.PHONY: test-fixer
test-fixer:
	vendor/bin/php-cs-fixer fix --dry-run --diff

.PHONY: cs
cs:
	vendor/bin/phpcs

.PHONY: cbf
cbf:
	vendor/bin/phpcbf
