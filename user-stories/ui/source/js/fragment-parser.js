
class FragmentParser {

    constructor(){
        this.originalHash = '';
        this.hash         = {};
    }

    convertToHash(strHash)
    {
        strHash    = strHash.substr(1).toLowerCase();
        let params = strHash.split('&');
        let res = {};
        for(let param of params)
        {
            param = param.split('=');
            if(param.length !==  2) continue;
            let val = param[1].trim();
            if(val === '') continue;

            if (val === 'true' || val === 'false')
                param[1] = val == 'true';

            res[param[0]] = param[1];
        }
        return res;
    }

    clearParams(){
        this.originalHash = '';
        this.hash         = {};
    }

    getParam(key){
        if(this.originalHash !== window.location.hash){
            this.originalHash = window.location.hash;
            this.hash = this.convertToHash(this.originalHash);
        }

        if(!this.hash.hasOwnProperty(key) ) return null;
        return this.hash[key];
    }

    getParams(){
        if(this.originalHash !==  window.location.hash){
            this.originalHash = window.location.hash;
            this.hash = this.convertToHash(this.originalHash);
        }

        return { ... this.hash };
    }

    deleteParam(param){
        var hash    = this.getParams();
        this.clearParams();
        for(let key in hash) {
            if(key == param) continue;
            this.hash[key] = hash[key];
        }
    }

    deleteParams(params){
        var hash    = this.getParams();
        this.clearParams();
        for(let key in hash) {
            if(params.includes(key)) continue;
            this.hash[key] = hash[key];
        }
    }

    setParam(key, value){
        if(this.originalHash !==  window.location.hash){
            this.originalHash = window.location.hash;
            this.hash = this.convertToHash(this.originalHash);
        }
        if(value !== null && value !== '')
            this.hash[key] = value;
        else
            delete this.hash[key];
        return this;
    }

    serialize(){
        let res = '';
        for(let key in this.hash)
        {
            let val = this.hash[key];
            if(res !== '') res += '&';
            res += key+'='+val;
        }
        return res;
    }
}

export default FragmentParser;