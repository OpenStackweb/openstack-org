<?php
global $searchResponse;
global $videoResponse;
global $statusResponse;

$searchResponse = [
	[
		'nextPageToken' => 'NEXT_PAGE_TOKEN',
		'items' => [
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 1
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 2
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 3
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 4
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 5
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 6
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 7
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 8
				]
			]
		]
	],
	[
		'nextPageToken' => null,
		'items' => [
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 123
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 123
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 9
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 10
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 11
				]
			],
			[
				'id' => [
					'kind' => 'youtube#video',
					'videoId' => 12
				]
			]
		]
	]
];

$videoResponse = [
	[
		'items' => [
			[
				'id' => 1,
				'statistics' => [
					'viewCount' => 100
				]
			],
			[		
				'id' => 2,
				'statistics' => [
					'viewCount' => 200
				]
			],
			[		
				'id' => 3,
				'statistics' => [
					'viewCount' => 300
				]
			],
			[		
				'id' => 4,
				'statistics' => [
					'viewCount' => 400
				]
			],
			[		
				'id' => 5,
				'statistics' => [
					'viewCount' => 500
				]
			]

		]
	],
	[
		'items' => [
			[
				'id' => 6,
				'statistics' => [
					'viewCount' => 600
				]
			],
			[		
				'id' => 7,
				'statistics' => [
					'viewCount' => 700
				]
			],
			[		
				'id' => 8,
				'statistics' => [
					'viewCount' => 800
				]
			],
			[		
				'id' => 9,
				'statistics' => [
					'viewCount' => 900
				]
			],
			[		
				'id' => 10,
				'statistics' => [
					'viewCount' => 1000
				]
			],
			[		
				'id' => 11,
				'statistics' => [
					'viewCount' => 1100
				]
			],
			[		
				'id' => 12,
				'statistics' => [
					'viewCount' => 1200
				]
			],
			[		
				'id' => 123,
				'statistics' => [
					'viewCount' => 0
				]
			],
			[		
				'id' => 13,
				'statistics' => [
					'viewCount' => 0
				]
			]

		]
	]

];

$statusResponse = [
	'items' => [
		[
			'id' => 2,
			'status' => [
				'uploadStatus' => 'processed'
			]
		],
		[
			'id' => 4,
			'status' => [
				'uploadStatus' => 'dummy'
			]
		],
		[
			'id' => 6,
			'status' => [
				'uploadStatus' => 'processed'
			]
		],
		[
			'id' => 8,
			'status' => [
				'uploadStatus' => 'dummy'
			]
		],
		[
			'id' => 10,
			'status' => [
				'uploadStatus' => 'processed'
			]
		],
		[
			'id' => 12,
			'status' => [
				'uploadStatus' => 'dummy'
			]
		]



	]
];