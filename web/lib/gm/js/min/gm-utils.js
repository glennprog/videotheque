
// Get the size of an object
Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function gm_searchParamsUrl(url) {
    var startParamsIndicator = '?';
    var separatorParamsIndicator = '&';
    var query_params = null;
    var params = null;
    try {
        query_params = url.split(startParamsIndicator);
    } catch (error) { }
    if (query_params != null || query_params != undefined) {
        try {
            params = query_params[1].split(separatorParamsIndicator);
        } catch (error) { }
    }
    if (params != null || params != undefined) {
        params_array = [];
        for (let index = 0; index < params.length; index++) {
            const element = params[index].split("=");
            params_array[element[0]] = element[1];
        }
        return params_array;
    }
    return false;
}

function replaceParamsUrl(param, value, url) {
    var startParamsIndicator = '?';
    var separatorParamsIndicator = '&';
    params = gm_searchParamsUrl(url);
    if (url.includes(param) == false) {
        if (Object.size(params) > 0) { // there are already at less one param in the URI => add & front of the param
            url = url + separatorParamsIndicator + param + "=" + value;
        }
        else { // The aren't no parameters in the URI
            url = (url.includes(startParamsIndicator)) ? url + param + "=" + value : url + startParamsIndicator + param + "=" + value;
        }
    }
    else { // Just replace the value of the parameters in the URI
        url = url.replace(param + "=" + params[param], param + "=" + value);
    }
    return url;
}