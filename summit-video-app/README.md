# Video app for Summits

This module provides a robust frontend view of all videos for summit presentations, allowing filtering by summit, speaker, search, and featured/latest videos, with passive pushes from the server to the client.

## Getting Started

### Migration

`framework/sake dev/build flush=1`

It is also critical to run a migration task to populate the `PresentationVideo` table and infill all of the legacy `Presentation` records that were not created for early summits. 

`framework/sake dev/tasks/VideoPresentationMigration`

## Installation

* Create a `SummitVideoApp` page somewhere in the SiteTree, and title the page appropriately (e.g. `/summit/videos`)
* In the _Summit Videos_ admin tab, choose one video to be featured by clicking the "Set as Featured" button on its edit form.
* Select a few videos to be highlighted by checking their "highlighed" checkbox

## Configuration

There are several configurable settings made available to the developer to tailor the user experience.

In `summit-video-app/_config/config.yml`:
<table>
<thead>
<tr><th>Setting name</th><th>Description</th><th>Default value</th></tr>
</thead>
<tbody>
<tr><td>default_speaker_limit</td><td>Number of speakers returned by default</td><td>20</td></tr>
<tr><td>popular_video_view_threshold</td><td>Number of views a video needs to be in the "popular" section</td><td>10</td></tr>
<tr><td>popular_video_limit</td><td>Number of popular videos</td><td>10</td></tr>
<tr><td>video_poll_interval</td><td>Frequency, in milliseconds, that the app passively refreshes state</td><td>15000</td></tr>
<tr><td>video_view_throttle</td><td>How long, in seconds, a user has to wait before adding a view the same video</td><td>10</td></tr>
<tr><td>video_view_delay</td><td>How long, in milliseconds, a user has to watch a video before it's considered a view</td><td>10000</td></tr>
</tbody>
</table>

## Contributing

* Run `$ npm install` in the `source/` directory
* Run `$ npm run start` in the `source/` directory to start the Webpack dev server

In dev mode, the Javascript and CSS assets will be loaded from the local webpack dev server instead of the web server.  This provides hot reloads of CSS and Javascript, meaning most of the time, reloading the browser is unnecessary. Further, changes to React components will hot reload in the browser and persist their state.

The determination to use the dev server is based on the outcome of the `$WebpackDevServer` template accessor.

## Releasing

All assets that will be shipped to production, including images, need to be declared in `index.js`, which serves as a manifest the production build. Ideally, the `source/` folder should not be deployed to production.

To run a production build:

`npm run build`

## Tests

Client side tests for Redux stores, reducers, and actions:

`npm run test`

Integration and unit tests for `PresentationVideo` and `SummitVideoApp_Controller`:

`framework/sake dev/tests/SummitVideoAppTest`


## API endpoints

All video result sets are limited to `default_video_limit` in the config (50).

### GET api/videos
Provides all videos, across all summits, ordered by `DateUploaded DESC`.

**Optional parameters**:
* `?highlighted=1`: Shows only videos marked highlighted
* `?popular=1`: Shows popular videos (`Views DESC`). Filtered by `popular_video_view_threshold` in the config.

**Example response**:
```js
{  
   "summit":null,
   "speaker":null,
   "results":[  
      {  
         "id":1533,
         "title":"Video 1",
         ...
      }
   ]
}
```


### GET api/videos?search=foo
Searches video titles, speakers, and topics (categories) for keywords, and provides three separate result sets.

**Example response**
```js
{
	results: {
		titleMatches: [
	      {  
	         "id":1533,
	         "title":"Video 1",
	         ...
	      }		
		],
		speakerMatches: [
	      {  
	         "id":1534,
	         "title":"Video 2",
	         ...
	      }			
		],
		topicMatches: [
	      {  
	         "id":1535,
	         "title":"Video 3",
	         ...
	      }		
		]
	}
}
```

### GET api/videos?speaker=[ID]

Gets the videos for a speaker with given `ID`.

**Example response**:
```js
{  
   "summit": null,
   "speaker": {
		"id": 123,
		"name": "Uncle Cheese"
   },
   "results":[  
      {  
         "id":1533,
         "title":"Video 1",
         ...
      }
   ]
}
```

### GET api/videos?summit=[ID]

Gets the videos for a summit with given `ID`.

**Example response**:
```
{
	"summit": {
		"id": 123,
	   	"title": "Paris"
   	},
   	"speaker": null,
   	"results": [
      {  
         "id":1533,
         "title":"Video 1",
         ...
      }
   	]

}
```

### GET api/video/latest

**Example response**:
```js
      {  
         "id":1533,
         "title":"Video 1",
         ...
      }
```
Gets the latest uploaded video.

### GET api/video/featured

Gets the featured video

**Example response**:
```js
      {  
         "id":1533,
         "title":"Video 1",
         ...
      }
```

### GET api/video/[ID]

Gets a video with given `ID`.

**Example response**:
```js
      {  
         "id":1533,
         "title":"Video 1",
         ...
      }
```

### GET api/summits

Gets all of the summits

**Example response**
```js
{  
	"results":[  
		{  
			"id":5,
			"title":"Tokyo",
			"dates":"Oct 26 - 29, 2015",
			"videoCount":"327",
			"imageURL":"http:\/\/placehold.it\/200x100"
		},
		{
			...
		}
	]
}
```

### GET api/speakers

Gets a list of the speakers, ordered by most aggregate videos, descending. Limited by `default_speaker_limit` in the config (20).

**Example response**:
```js
{  
   "results":[  
      {  
         "id":1,
         "name":"Speaker 1",
         "jobTitle":"Chief Technologist",
         "imageURL":"http:\/\/placehold.it\/200x100",
         "videoCount":"23"
      },
      {
       	 ...
  	  }
}
```
