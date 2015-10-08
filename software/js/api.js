// Requirements and globals
var api            = riot.observable();
var api_base_url   = 'api/v1/software';
var query_template = 'releases/@RELEASE_ID/components';

// events

api.load_components_by_release = function(release_id, term, adoption, maturity, age)
{
    console.log('release id '+release_id+' - term '+term+' - adoption '+ adoption+ ' - maturity '+maturity+' - age ' +age );

    var url = api_base_url+'/'+ query_template.replace('@RELEASE_ID', release_id)+'?';

    var filters = '';
    if(term !== null && term !== '' && term !== undefined ) {
        if(filters !== '') filters += '&';
        filters += 'term=' + term;
    }

    if(adoption !== null && adoption !== '' && adoption !== undefined ) {
        if(filters !== '') filters += '&';
        filters += 'adoption=' + adoption;
    }

    if(maturity !== null && maturity !== '' && maturity !== undefined ) {
        if(filters !== '') filters += '&';
        filters += 'maturity=' + maturity;
    }

    if(age !== null && age !== '' && age !== undefined ) {
        if(filters !== '') filters += '&';
        filters += 'age=' + age;
    }

    return $.get(url+filters,function (resp) {
        api.trigger('loaded-components-by-release', resp)
    });

}

module.exports = api;