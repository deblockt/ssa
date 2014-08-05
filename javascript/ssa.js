/**
 * ssa api
 */
var ssa = {
    rbracket : /\[\]$/,
    r20 : /%20/g,
    supportFileUpload : function(){
        if ('undefined' !== typeof window.FormData) {
            fd = new FormData;
            return 'undefined' !== typeof fd.append;
        }
        return false;
    },
    call : function(url, parameter, synchronius, useFormData) {
       var data = parameter || {};
       
       if (synchronius === undefined) {
           synchronius = false;
       }
       
        return this.ajaxRequest({
            'url' : url,
            'data' : data,
            'synchronous' : synchronius,
            'method' : 'post',
            'useFormData' : useFormData
        });  
    },
    ajaxRequest: function(ops) {
        if(typeof ops === 'string') ops = { url: ops };
        ops.url = ops.url || '';
        ops.method = ops.method || 'get';
        ops.useFormData = ops.useFormData || false;
        ops.data = ops.data || {};
        ops.json = true;
        ops.synchronous = false;
        
        var extractGetParameters = function (url) {
            var query_string = {};
            var indexOf = url.indexOf('?');
            if (indexOf == -1) {
                // no get parameters
                return {'baseUrl' : url, 'parameters' : []};
            }
            var baseUrl = url.substring(0, indexOf);
            var query = url.substring(indexOf + 1);
            
            var vars = query.split("&");
            for (var i=0; i<vars.length; i++) {
               var pair = vars[i].split("=");
               query_string[pair[0]] = pair[1];
            } 
            
            return {'baseUrl' : baseUrl, 'parameters' : query_string};
        };
        
        var getParams = function(data, url) {
            if (ops.useFormData) {
                var formData = new FormData();
                var arrayParam = ssa.paramAsArray(data);
                for (var i in arrayParam) {
                    var param = arrayParam[i];    
                    formData.append(param[0], param[1]);
                }
                return formData;
            }
            
            var str = ssa.param(data);
            
            if(str != '') {
                return url ? (url.indexOf('?') < 0 ? '?' + str : '&' + str) : str;
            }
            return '';
        };
        
        // extract existing parameters into url
        var otherData = extractGetParameters(ops.url);
        ops.url = otherData.baseUrl;
        for (var index in otherData.parameters) {
            ops.data[index] = otherData.parameters[index];
        }
        
        var api = {
            host: {},
            process: function(ops) {
                var self = this;
                this.xhr = null;
                if(window.ActiveXObject) { this.xhr = new ActiveXObject('Microsoft.XMLHTTP'); }
                else if(window.XMLHttpRequest) { this.xhr = new XMLHttpRequest(); }
                if(this.xhr) {
                    this.xhr.onreadystatechange = function() {
                        if(self.xhr.readyState === 4 && self.xhr.status === 200) {
                            var result = self.xhr.responseText;
                            if(ops.json === true && typeof JSON !== 'undefined') {
                                result = JSON.parse(result);
                            }
                            self.successCall(result);
                        } else if(self.xhr.readyState === 4) {
                            self.failCallback && self.failCallback.apply(self.host, [self.xhr]);
                        }
                        self.alwaysCallback && self.alwaysCallback.apply(self.host, [self.xhr]);
                    };
                }
                
                if(ops.method === 'get') {
                    this.xhr.open("GET", ops.url + getParams(ops.data, ops.url), !ops.synchronous);
                } else {
                    this.xhr.open(ops.method, ops.url, !ops.synchronous);
                    if (!ops.useFormData) {
                        this.setHeaders({
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-type': 'application/x-www-form-urlencoded'
                        });
                    }
                }
                if(ops.headers && typeof ops.headers === 'object') {
                    this.setHeaders(ops.headers);
                }       
                // bug IE with header definition
                if (!ops.synchronous) {
                    setTimeout(function() { 
                        ops.method === 'get' ? self.xhr.send() : self.xhr.send(getParams(ops.data)); 
                    }, 20);
                    return this;
                } else {
                    ops.method === 'get' ? self.xhr.send() : self.xhr.send(getParams(ops.data));
                    return self.xhr.responseText;
                }
            },
            done: function(callback) {
                this.successCallback = callback;                
                return this;
            },
            formDataError : function(callback){
                if (this.host.errorFormData) {
                    callback();
                }
                return this;
            },
            phpError : function(callback) {
                this.phpErrorCallback = callback;
                return this;
            },
            fail: function(callback) {
                this.failCallback = callback;
                return this;
            },
            always: function(callback) {
                this.alwaysCallback = callback;
                return this;
            },
            successCall : function(data){
                if (data.errorCode) {
                    if (data.debug === true) {
                        // affichage de l'erreur
                        if (console && console.error) {
                            console.error(data.errorMessage, data);
                        } else {
                            alert(data.errorCode + '\n' + data.errorMessage);
                        }
                    }
                    if (this.phpErrorCallback) {
                        this.phpErrorCallback(data);
                    }
                } else {
                    this.successCallback && this.successCallback.apply(this.host, [data, this.xhr]);
                }
            },
            setHeaders: function(headers) {
                for(var name in headers) {
                    this.xhr && this.xhr.setRequestHeader(name, headers[name]);
                }
            }
        };
       
        // if it want use form data but the navigator doesn't support FormData
        if (ops.useFormData && !ssa.supportFileUpload()) {
            api.host.errorFormData = true;
            return api;
        }
        
        return api.process(ops);
    },
    buildParams : function( prefix, obj, add ) {        
        if (obj instanceof FileList) {
            for (var i = 0; i < obj.length; i++) { 
               add( prefix+'[]', obj[i] );
            }
        } else if (Array.isArray( obj ) ) {
            for (var i in obj) {
                var v = obj[i];
                if (ssa.rbracket.test( prefix ) ) {
                    add( prefix, v );
                } else {
                    ssa.buildParams( prefix + "[" + ( typeof v === "object" ? i : "" ) + "]", v, add );
                }
            }
        } else if (typeof obj === "object" ) {
            // Serialize object item.
            for (var name in obj ) {
                ssa.buildParams( prefix + "[" + name + "]", obj[ name ], add );
            }
        } else {
            add( prefix, obj );
        }
    },    
    param : function(a) {
	var s = this.paramAsArray(a);
        var sString = [];
        for (var i in s) {
            s[i][0] = encodeURIComponent(s[i][0]);
            s[i][1] = encodeURIComponent(s[i][1]);
            sString.push(s[i].join("="));
        }
	// Return the resulting serialization
	return sString.join( "&" ).replace(ssa.r20, "+" );
    },
    paramAsArray : function(a) {
        var prefix;
        var s = [];
        var add = function( key, value ) {
                if (value === undefined) {
                    return;
                }
                // If value is a function, invoke it and return its value
                value = "function" === typeof value ? value() : ( value == null ? "" : value );
                s[ s.length ] = [ key , value];
            };


	// If an array was passed in, assume that it is an array of form elements.
	if (a instanceof FileList) {
            for (var i = 0; i < files.length; i++) { 
                add( i, a[i]);
            }
        } else if (Array.isArray( a )) {
            for (var i in a) {
                add( i, a[i]);
            }
	} else {
            // recurcive parse 
            for (var prefix in a ) {
                ssa.buildParams( prefix, a[ prefix ], add );
            }
	}
        
        return s;
    }
};

/** redefinition array.isArray */
(function () {
    var toString = Object.prototype.toString,
        strArray = Array.toString(),
        jscript  = /*@cc_on @_jscript_version @*/ +0;

    // jscript will be 0 for browsers other than IE
    if (!jscript) {
        Array.isArray = Array.isArray || function (obj) {
            return toString.call(obj) == "[object Array]";
        };
    }
    else {
        Array.isArray = function (obj) {
            return "constructor" in obj && String(obj.constructor) == strArray;
        };
    }
})();

