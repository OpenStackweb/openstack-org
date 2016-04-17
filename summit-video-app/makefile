BIN = node_modules/.bin

clean:
	rm -rf production
build: clean
	NODE_ENV=production webpack --progress --colors
watch:
	NODE_ENV=dev node server $(filter-out $@,$(MAKECMDGOALS))
lint:
	NODE_ENV=dev $(BIN)/eslint ./source/index.js
