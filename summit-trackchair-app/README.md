# Track Chairs App

This module provides a web application used by track chairs to vet the list of submitted presentations and create lists of their favourites, ultimately producing a singular list for each category.

## Getting Started

### Migration

`framework/sake dev/build flush=1`

There are some minor updates to some of the tables. To apply those, run the migration task:

`framework/sake dev/tasks/TrackChairMigration`

## Installation

* Create a `TrackChairsPage` page somewhere in the SiteTree, and title the page appropriately (e.g. `/track-chairs/`)

## Set up your user

In order to use the app, you must be a track chair for one of the categories in the active summit. To do that, ensure you're logged in as an `ADMIN`, and then visit the following URL:

`/trackchairs/api/v1/chair/add?email=[me@example.com]&cat_id=123`

Where *me@example.com* is your email address in SilverStripe, and `cat_id` is the category you want to assign yourself to. To find a category, go to the `browse` section of the app, and change the category with the dropdown on the left. In the URL, you will see a category ID.


## Contributing

* Run `$ npm install` in the `source/` directory
* Run `$ npm run serve summit-trackchair-app` in the webroot to start the Webpack dev server

In dev mode, the Javascript and CSS assets will be loaded from the local webpack dev server instead of the web server.  This provides hot reloads of CSS and Javascript, meaning most of the time, reloading the browser is unnecessary. Further, changes to React components will hot reload in the browser and persist their state.

The determination to use the dev server is based on the outcome of the `$WebpackDevServer` template accessor.

## Releasing

All assets that will be shipped to production, including images, need to be declared in `index.js`, which serves as a manifest the production build. Ideally, the `source/` folder should not be deployed to production.

To run a production build:

`npm run build summit-trackchair-app`

## Tests

Client side tests for Redux stores, reducers, and actions:

`npm run test`