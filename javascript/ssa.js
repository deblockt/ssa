/**
 * ssa api
 */
var ssa = {
    rbracket : /\[\]$/,
    r20 : /%20/g,
    call : function(url, parameter, synchronius) {
        var data = parameter || {};
       
       if (synchronius === undefined) {
           synchronius = false;
       }
       
        // cas d'un appelle synchrone
        return this.ajaxRequest({
            'url' : url,
            'data' : data,
            'synchronous' : synchronius
        });
        
    },
    ajaxRequest: function(ops) {
        if(typeof ops === 'string') ops = { url: ops };
        ops.url = ops.url || '';
        ops.method = ops.method || 'get';
        ops.data = ops.data || {};
        ops.json = true;
        ops.synchronous = false;
        
        var getParams = function(data, url) {
            var str = ssa.param(data);
            
            if(str != '') {
                return url ? (url.indexOf('?') < 0 ? '?' + str : '&' + str) : str;
            }
            return '';
        };
        
        
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
                    this.setHeaders({
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-type': 'application/x-www-form-urlencoded'
                    });
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
       
        return api.process(ops);
    },
    buildParams : function( prefix, obj, add ) {
        if (Array.isArray( obj ) ) {
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
	var prefix;
        var s = [];
        var add = function( key, value ) {
                // If value is a function, invoke it and return its value
                value = "function" === typeof value ? value() : ( value == null ? "" : value );
                s[ s.length ] = encodeURIComponent( key ) + "=" + encodeURIComponent( value );
            };


	// If an array was passed in, assume that it is an array of form elements.
	if (Array.isArray( a )) {
            for (var i in a) {
                add( i, a[i]);
            }
	} else {
            // recurcive parse 
            for (var prefix in a ) {
                ssa.buildParams( prefix, a[ prefix ], add );
            }
	}
        
	// Return the resulting serialization
	return s.join( "&" ).replace(ssa.r20, "+" );
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

