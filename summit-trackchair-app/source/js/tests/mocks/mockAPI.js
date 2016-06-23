import request from 'superagent';
import mock from 'superagent-mock';

const config = [{
    /**
     * regular expression of URL
     */
    pattern: '/api/(.*)',

    /**
     * returns the data
     *
     * @param match array Result of the resolution of the regular expression
     * @param params object sent by 'send' function
     * @param headers object set by 'set' function
     */
    fixtures: function (match, params = {}, headers) {
      /**
       * Returning error codes example:
       *   request.get('https://domain.example/404').end(function(err, res){
       *     console.log(err); // 404
       *     console.log(res.notFound); // true
       *   })
       */      
      switch(match[1]) {
      	case 'videos?speaker=123':       	
      		return {
      			...videosData,
      			speaker: {
      				...speakerData.results[0]
      			}
      		}
      	case 'videos?summit=123':
      		return {
      			...videosData,
      			summit: {
      				...summitData.results[0]
      			}
      		};

      	case 'videos?search=test':
      		return {
      			results: {
      				titleMatches: [
      					...videosData.results
      				],
      				speakerMatches: [
      					...videosData.results
      				],
      				topicMatches: [
      					...videosData.results
      				]
      			}
      		}

      	case 'videos?start=0':
	      	return videosData;

	     case 'summits':
	     	return summitData;

	     case 'speakers?start=0':
	     	return speakerData

	     case 'video/featured':
	     case 'video/latest':
	     case 'video/123':    
     		return {     			
     			...videosData.results[0]
     		};
      }

    },

    /**
     * returns the result of the GET request
     *
     * @param match array Result of the resolution of the regular expression
     * @param data  mixed Data returns by `fixtures` attribute
     */
    get: function (match, data) {
      return {
      	body: data,
      	code: 200,
      	ok: true
      };
    }
}];

mock(request, config);

export default request;