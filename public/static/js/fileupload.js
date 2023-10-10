// 2023-07-07
! function(e) {
    "use strict";
    var r = function(e, n) {
        var t = /[^\w\-.:]/.test(e) ? new Function(r.arg + ",tmpl", "var _e=tmpl.encode" + r.helper + ",_s='" + e.replace(r.regexp, r.func) + "';return _s;") : r.cache[e] = r.cache[e] || r(r.load(e));
        return n ? t(n, r) : function(e) {
            return t(e, r)
        }
    };
    r.cache = {}, r.load = function(e) {
        return document.getElementById(e).innerHTML
    }, r.regexp = /([\s'\\])(?!(?:[^{]|\{(?!%))*%\})|(?:\{%(=|#)([\s\S]+?)%\})|(\{%)|(%\})/g, r.func = function(e, n, t, r, c, u) {
        return n ? {
            "\n": "\\n",
            "\r": "\\r",
            "\t": "\\t",
            " ": " "
        } [n] || "\\" + n : t ? "=" === t ? "'+_e(" + r + ")+'" : "'+(" + r + "==null?'':" + r + ")+'" : c ? "';" : u ? "_s+='" : void 0
    }, r.encReg = /[<>&"'\x00]/g, r.encMap = {
        "<": "&lt;",
        ">": "&gt;",
        "&": "&amp;",
        '"': "&quot;",
        "'": "&#39;"
    }, r.encode = function(e) {
        return (null == e ? "" : "" + e).replace(r.encReg, function(e) {
            return r.encMap[e] || ""
        })
    }, r.arg = "o", r.helper = ",print=function(s,e){_s+=e?(s==null?'':s):_e(s);},include=function(s,d){_s+=tmpl(s,d);}", "function" == typeof define && define.amd ? define(function() {
        return r
    }) : "object" == typeof module && module.exports ? module.exports = r : e.tmpl = r
}(this);
/*! jQuery UI - v1.12.1+CommonJS - 2018-02-10
 * http://jqueryui.com
 * Includes: widget.js
 * Copyright jQuery Foundation and other contributors; Licensed MIT */
(function(factory) {
    if (typeof define === "function" && define.amd) {
        define(["jquery"], factory)
    } else if (typeof exports === "object") {
        factory(require("jquery"))
    } else {
        factory(jQuery)
    }
})(function($) {
    $.ui = $.ui || {};
    var version = $.ui.version = "1.12.1";
    /*!
     * jQuery UI Widget 1.12.1
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */
    var widgetUuid = 0;
    var widgetSlice = Array.prototype.slice;
    $.cleanData = function(orig) {
        return function(elems) {
            var events, elem, i;
            for (i = 0;
                (elem = elems[i]) != null; i++) {
                try {
                    events = $._data(elem, "events");
                    if (events && events.remove) {
                        $(elem).triggerHandler("remove")
                    }
                } catch (e) {}
            }
            orig(elems)
        }
    }($.cleanData);
    $.widget = function(name, base, prototype) {
        var existingConstructor, constructor, basePrototype;
        var proxiedPrototype = {};
        var namespace = name.split(".")[0];
        name = name.split(".")[1];
        var fullName = namespace + "-" + name;
        if (!prototype) {
            prototype = base;
            base = $.Widget
        }
        if ($.isArray(prototype)) {
            prototype = $.extend.apply(null, [{}].concat(prototype))
        }
        $.expr[":"][fullName.toLowerCase()] = function(elem) {
            return !!$.data(elem, fullName)
        };
        $[namespace] = $[namespace] || {};
        existingConstructor = $[namespace][name];
        constructor = $[namespace][name] = function(options, element) {
            if (!this._createWidget) {
                return new constructor(options, element)
            }
            if (arguments.length) {
                this._createWidget(options, element)
            }
        };
        $.extend(constructor, existingConstructor, {
            version: prototype.version,
            _proto: $.extend({}, prototype),
            _childConstructors: []
        });
        basePrototype = new base;
        basePrototype.options = $.widget.extend({}, basePrototype.options);
        $.each(prototype, function(prop, value) {
            if (!$.isFunction(value)) {
                proxiedPrototype[prop] = value;
                return
            }
            proxiedPrototype[prop] = function() {
                function _super() {
                    return base.prototype[prop].apply(this, arguments)
                }

                function _superApply(args) {
                    return base.prototype[prop].apply(this, args)
                }
                return function() {
                    var __super = this._super;
                    var __superApply = this._superApply;
                    var returnValue;
                    this._super = _super;
                    this._superApply = _superApply;
                    returnValue = value.apply(this, arguments);
                    this._super = __super;
                    this._superApply = __superApply;
                    return returnValue
                }
            }()
        });
        constructor.prototype = $.widget.extend(basePrototype, {
            widgetEventPrefix: existingConstructor ? basePrototype.widgetEventPrefix || name : name
        }, proxiedPrototype, {
            constructor: constructor,
            namespace: namespace,
            widgetName: name,
            widgetFullName: fullName
        });
        if (existingConstructor) {
            $.each(existingConstructor._childConstructors, function(i, child) {
                var childPrototype = child.prototype;
                $.widget(childPrototype.namespace + "." + childPrototype.widgetName, constructor, child._proto)
            });
            delete existingConstructor._childConstructors
        } else {
            base._childConstructors.push(constructor)
        }
        $.widget.bridge(name, constructor);
        return constructor
    };
    $.widget.extend = function(target) {
        var input = widgetSlice.call(arguments, 1);
        var inputIndex = 0;
        var inputLength = input.length;
        var key;
        var value;
        for (; inputIndex < inputLength; inputIndex++) {
            for (key in input[inputIndex]) {
                value = input[inputIndex][key];
                if (input[inputIndex].hasOwnProperty(key) && value !== undefined) {
                    if ($.isPlainObject(value)) {
                        target[key] = $.isPlainObject(target[key]) ? $.widget.extend({}, target[key], value) : $.widget.extend({}, value)
                    } else {
                        target[key] = value
                    }
                }
            }
        }
        return target
    };
    $.widget.bridge = function(name, object) {
        var fullName = object.prototype.widgetFullName || name;
        $.fn[name] = function(options) {
            var isMethodCall = typeof options === "string";
            var args = widgetSlice.call(arguments, 1);
            var returnValue = this;
            if (isMethodCall) {
                if (!this.length && options === "instance") {
                    returnValue = undefined
                } else {
                    this.each(function() {
                        var methodValue;
                        var instance = $.data(this, fullName);
                        if (options === "instance") {
                            returnValue = instance;
                            return false
                        }
                        if (!instance) {
                            return $.error("cannot call methods on " + name + " prior to initialization; " + "attempted to call method '" + options + "'")
                        }
                        if (!$.isFunction(instance[options]) || options.charAt(0) === "_") {
                            return $.error("no such method '" + options + "' for " + name + " widget instance")
                        }
                        methodValue = instance[options].apply(instance, args);
                        if (methodValue !== instance && methodValue !== undefined) {
                            returnValue = methodValue && methodValue.jquery ? returnValue.pushStack(methodValue.get()) : methodValue;
                            return false
                        }
                    })
                }
            } else {
                if (args.length) {
                    options = $.widget.extend.apply(null, [options].concat(args))
                }
                this.each(function() {
                    var instance = $.data(this, fullName);
                    if (instance) {
                        instance.option(options || {});
                        if (instance._init) {
                            instance._init()
                        }
                    } else {
                        $.data(this, fullName, new object(options, this))
                    }
                })
            }
            return returnValue
        }
    };
    $.Widget = function() {};
    $.Widget._childConstructors = [];
    $.Widget.prototype = {
        widgetName: "widget",
        widgetEventPrefix: "",
        defaultElement: "<div>",
        options: {
            classes: {},
            disabled: false,
            create: null
        },
        _createWidget: function(options, element) {
            element = $(element || this.defaultElement || this)[0];
            this.element = $(element);
            this.uuid = widgetUuid++;
            this.eventNamespace = "." + this.widgetName + this.uuid;
            this.bindings = $();
            this.hoverable = $();
            this.focusable = $();
            this.classesElementLookup = {};
            if (element !== this) {
                $.data(element, this.widgetFullName, this);
                this._on(true, this.element, {
                    remove: function(event) {
                        if (event.target === element) {
                            this.destroy()
                        }
                    }
                });
                this.document = $(element.style ? element.ownerDocument : element.document || element);
                this.window = $(this.document[0].defaultView || this.document[0].parentWindow)
            }
            this.options = $.widget.extend({}, this.options, this._getCreateOptions(), options);
            this._create();
            if (this.options.disabled) {
                this._setOptionDisabled(this.options.disabled)
            }
            this._trigger("create", null, this._getCreateEventData());
            this._init()
        },
        _getCreateOptions: function() {
            return {}
        },
        _getCreateEventData: $.noop,
        _create: $.noop,
        _init: $.noop,
        destroy: function() {
            var that = this;
            this._destroy();
            $.each(this.classesElementLookup, function(key, value) {
                that._removeClass(value, key)
            });
            this.element.off(this.eventNamespace).removeData(this.widgetFullName);
            this.widget().off(this.eventNamespace).removeAttr("aria-disabled");
            this.bindings.off(this.eventNamespace)
        },
        _destroy: $.noop,
        widget: function() {
            return this.element
        },
        option: function(key, value) {
            var options = key;
            var parts;
            var curOption;
            var i;
            if (arguments.length === 0) {
                return $.widget.extend({}, this.options)
            }
            if (typeof key === "string") {
                options = {};
                parts = key.split(".");
                key = parts.shift();
                if (parts.length) {
                    curOption = options[key] = $.widget.extend({}, this.options[key]);
                    for (i = 0; i < parts.length - 1; i++) {
                        curOption[parts[i]] = curOption[parts[i]] || {};
                        curOption = curOption[parts[i]]
                    }
                    key = parts.pop();
                    if (arguments.length === 1) {
                        return curOption[key] === undefined ? null : curOption[key]
                    }
                    curOption[key] = value
                } else {
                    if (arguments.length === 1) {
                        return this.options[key] === undefined ? null : this.options[key]
                    }
                    options[key] = value
                }
            }
            this._setOptions(options);
            return this
        },
        _setOptions: function(options) {
            var key;
            for (key in options) {
                this._setOption(key, options[key])
            }
            return this
        },
        _setOption: function(key, value) {
            if (key === "classes") {
                this._setOptionClasses(value)
            }
            this.options[key] = value;
            if (key === "disabled") {
                this._setOptionDisabled(value)
            }
            return this
        },
        _setOptionClasses: function(value) {
            var classKey, elements, currentElements;
            for (classKey in value) {
                currentElements = this.classesElementLookup[classKey];
                if (value[classKey] === this.options.classes[classKey] || !currentElements || !currentElements.length) {
                    continue
                }
                elements = $(currentElements.get());
                this._removeClass(currentElements, classKey);
                elements.addClass(this._classes({
                    element: elements,
                    keys: classKey,
                    classes: value,
                    add: true
                }))
            }
        },
        _setOptionDisabled: function(value) {
            this._toggleClass(this.widget(), this.widgetFullName + "-disabled", null, !!value);
            if (value) {
                this._removeClass(this.hoverable, null, "ui-state-hover");
                this._removeClass(this.focusable, null, "ui-state-focus")
            }
        },
        enable: function() {
            return this._setOptions({
                disabled: false
            })
        },
        disable: function() {
            return this._setOptions({
                disabled: true
            })
        },
        _classes: function(options) {
            var full = [];
            var that = this;
            options = $.extend({
                element: this.element,
                classes: this.options.classes || {}
            }, options);

            function processClassString(classes, checkOption) {
                var current, i;
                for (i = 0; i < classes.length; i++) {
                    current = that.classesElementLookup[classes[i]] || $();
                    if (options.add) {
                        current = $($.unique(current.get().concat(options.element.get())))
                    } else {
                        current = $(current.not(options.element).get())
                    }
                    that.classesElementLookup[classes[i]] = current;
                    full.push(classes[i]);
                    if (checkOption && options.classes[classes[i]]) {
                        full.push(options.classes[classes[i]])
                    }
                }
            }
            this._on(options.element, {
                remove: "_untrackClassesElement"
            });
            if (options.keys) {
                processClassString(options.keys.match(/\S+/g) || [], true)
            }
            if (options.extra) {
                processClassString(options.extra.match(/\S+/g) || [])
            }
            return full.join(" ")
        },
        _untrackClassesElement: function(event) {
            var that = this;
            $.each(that.classesElementLookup, function(key, value) {
                if ($.inArray(event.target, value) !== -1) {
                    that.classesElementLookup[key] = $(value.not(event.target).get())
                }
            })
        },
        _removeClass: function(element, keys, extra) {
            return this._toggleClass(element, keys, extra, false)
        },
        _addClass: function(element, keys, extra) {
            return this._toggleClass(element, keys, extra, true)
        },
        _toggleClass: function(element, keys, extra, add) {
            add = typeof add === "boolean" ? add : extra;
            var shift = typeof element === "string" || element === null,
                options = {
                    extra: shift ? keys : extra,
                    keys: shift ? element : keys,
                    element: shift ? this.element : element,
                    add: add
                };
            options.element.toggleClass(this._classes(options), add);
            return this
        },
        _on: function(suppressDisabledCheck, element, handlers) {
            var delegateElement;
            var instance = this;
            if (typeof suppressDisabledCheck !== "boolean") {
                handlers = element;
                element = suppressDisabledCheck;
                suppressDisabledCheck = false
            }
            if (!handlers) {
                handlers = element;
                element = this.element;
                delegateElement = this.widget()
            } else {
                element = delegateElement = $(element);
                this.bindings = this.bindings.add(element)
            }
            $.each(handlers, function(event, handler) {
                function handlerProxy() {
                    if (!suppressDisabledCheck && (instance.options.disabled === true || $(this).hasClass("ui-state-disabled"))) {
                        return
                    }
                    return (typeof handler === "string" ? instance[handler] : handler).apply(instance, arguments)
                }
                if (typeof handler !== "string") {
                    handlerProxy.guid = handler.guid = handler.guid || handlerProxy.guid || $.guid++
                }
                var match = event.match(/^([\w:-]*)\s*(.*)$/);
                var eventName = match[1] + instance.eventNamespace;
                var selector = match[2];
                if (selector) {
                    delegateElement.on(eventName, selector, handlerProxy)
                } else {
                    element.on(eventName, handlerProxy)
                }
            })
        },
        _off: function(element, eventName) {
            eventName = (eventName || "").split(" ").join(this.eventNamespace + " ") + this.eventNamespace;
            element.off(eventName).off(eventName);
            this.bindings = $(this.bindings.not(element).get());
            this.focusable = $(this.focusable.not(element).get());
            this.hoverable = $(this.hoverable.not(element).get())
        },
        _delay: function(handler, delay) {
            function handlerProxy() {
                return (typeof handler === "string" ? instance[handler] : handler).apply(instance, arguments)
            }
            var instance = this;
            return setTimeout(handlerProxy, delay || 0)
        },
        _hoverable: function(element) {
            this.hoverable = this.hoverable.add(element);
            this._on(element, {
                mouseenter: function(event) {
                    this._addClass($(event.currentTarget), null, "ui-state-hover")
                },
                mouseleave: function(event) {
                    this._removeClass($(event.currentTarget), null, "ui-state-hover")
                }
            })
        },
        _focusable: function(element) {
            this.focusable = this.focusable.add(element);
            this._on(element, {
                focusin: function(event) {
                    this._addClass($(event.currentTarget), null, "ui-state-focus")
                },
                focusout: function(event) {
                    this._removeClass($(event.currentTarget), null, "ui-state-focus")
                }
            })
        },
        _trigger: function(type, event, data) {
            var prop, orig;
            var callback = this.options[type];
            data = data || {};
            event = $.Event(event);
            event.type = (type === this.widgetEventPrefix ? type : this.widgetEventPrefix + type).toLowerCase();
            event.target = this.element[0];
            orig = event.originalEvent;
            if (orig) {
                for (prop in orig) {
                    if (!(prop in event)) {
                        event[prop] = orig[prop]
                    }
                }
            }
            this.element.trigger(event, data);
            return !($.isFunction(callback) && callback.apply(this.element[0], [event].concat(data)) === false || event.isDefaultPrevented())
        }
    };
    $.each({
        show: "fadeIn",
        hide: "fadeOut"
    }, function(method, defaultEffect) {
        $.Widget.prototype["_" + method] = function(element, options, callback) {
            if (typeof options === "string") {
                options = {
                    effect: options
                }
            }
            var hasOptions;
            var effectName = !options ? method : options === true || typeof options === "number" ? defaultEffect : options.effect || defaultEffect;
            options = options || {};
            if (typeof options === "number") {
                options = {
                    duration: options
                }
            }
            hasOptions = !$.isEmptyObject(options);
            options.complete = callback;
            if (options.delay) {
                element.delay(options.delay)
            }
            if (hasOptions && $.effects && $.effects.effect[effectName]) {
                element[method](options)
            } else if (effectName !== method && element[effectName]) {
                element[effectName](options.duration, options.easing, callback)
            } else {
                element.queue(function(next) {
                    $(this)[method]();
                    if (callback) {
                        callback.call(element[0])
                    }
                    next()
                })
            }
        }
    });
    var widget = $.widget
});
/*
 * jQuery Iframe Transport Plugin
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2011, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */
(function(factory) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define(["jquery"], factory)
    } else if (typeof exports === "object") {
        factory(require("jquery"))
    } else {
        factory(window.jQuery)
    }
})(function($) {
    "use strict";
    var counter = 0,
        jsonAPI = $,
        jsonParse = "parseJSON";
    if ("JSON" in window && "parse" in JSON) {
        jsonAPI = JSON;
        jsonParse = "parse"
    }
    $.ajaxTransport("iframe", function(options) {
        if (options.async) {
            var initialIframeSrc = options.initialIframeSrc || "javascript:false;",
                form, iframe, addParamChar;
            return {
                send: function(_, completeCallback) {
                    form = $('<form style="display:none;"></form>');
                    form.attr("accept-charset", options.formAcceptCharset);
                    addParamChar = /\?/.test(options.url) ? "&" : "?";
                    if (options.type === "DELETE") {
                        options.url = options.url + addParamChar + "_method=DELETE";
                        options.type = "POST"
                    } else if (options.type === "PUT") {
                        options.url = options.url + addParamChar + "_method=PUT";
                        options.type = "POST"
                    } else if (options.type === "PATCH") {
                        options.url = options.url + addParamChar + "_method=PATCH";
                        options.type = "POST"
                    }
                    counter += 1;
                    iframe = $('<iframe src="' + initialIframeSrc + '" name="iframe-transport-' + counter + '"></iframe>').bind("load", function() {
                        var fileInputClones, paramNames = $.isArray(options.paramName) ? options.paramName : [options.paramName];
                        iframe.unbind("load").bind("load", function() {
                            var response;
                            try {
                                response = iframe.contents();
                                if (!response.length || !response[0].firstChild) {
                                    throw new Error
                                }
                            } catch (e) {
                                response = undefined
                            }
                            completeCallback(200, "success", {
                                iframe: response
                            });
                            $('<iframe src="' + initialIframeSrc + '"></iframe>').appendTo(form);
                            window.setTimeout(function() {
                                form.remove()
                            }, 0)
                        });
                        form.prop("target", iframe.prop("name")).prop("action", options.url).prop("method", options.type);
                        if (options.formData) {
                            $.each(options.formData, function(index, field) {
                                $('<input type="hidden"/>').prop("name", field.name).val(field.value).appendTo(form)
                            })
                        }
                        if (options.fileInput && options.fileInput.length && options.type === "POST") {
                            fileInputClones = options.fileInput.clone();
                            options.fileInput.after(function(index) {
                                return fileInputClones[index]
                            });
                            if (options.paramName) {
                                options.fileInput.each(function(index) {
                                    $(this).prop("name", paramNames[index] || options.paramName)
                                })
                            }
                            form.append(options.fileInput).prop("enctype", "multipart/form-data").prop("encoding", "multipart/form-data");
                            options.fileInput.removeAttr("form")
                        }
                        form.submit();
                        if (fileInputClones && fileInputClones.length) {
                            options.fileInput.each(function(index, input) {
                                var clone = $(fileInputClones[index]);
                                $(input).prop("name", clone.prop("name")).attr("form", clone.attr("form"));
                                clone.replaceWith(input)
                            })
                        }
                    });
                    form.append(iframe).appendTo(document.body)
                },
                abort: function() {
                    if (iframe) {
                        iframe.unbind("load").prop("src", initialIframeSrc)
                    }
                    if (form) {
                        form.remove()
                    }
                }
            }
        }
    });
    $.ajaxSetup({
        converters: {
            "iframe text": function(iframe) {
                return iframe && $(iframe[0].body).text()
            },
            "iframe json": function(iframe) {
                return iframe && jsonAPI[jsonParse]($(iframe[0].body).text())
            },
            "iframe html": function(iframe) {
                return iframe && $(iframe[0].body).html()
            },
            "iframe xml": function(iframe) {
                var xmlDoc = iframe && iframe[0];
                return xmlDoc && $.isXMLDoc(xmlDoc) ? xmlDoc : $.parseXML(xmlDoc.XMLDocument && xmlDoc.XMLDocument.xml || $(xmlDoc.body).html())
            },
            "iframe script": function(iframe) {
                return iframe && $.globalEval($(iframe[0].body).text())
            }
        }
    })
});
/*
 * jQuery File Upload Plugin
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */
(function(factory) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define(["jquery", "jquery-ui/ui/widget"], factory)
    } else if (typeof exports === "object") {
        factory(require("jquery"), require("./vendor/jquery.ui.widget"))
    } else {
        factory(window.jQuery)
    }
})(function($) {
    "use strict";
    $.support.fileInput = !(new RegExp("(Android (1\\.[0156]|2\\.[01]))" + "|(Windows Phone (OS 7|8\\.0))|(XBLWP)|(ZuneWP)|(WPDesktop)" + "|(w(eb)?OSBrowser)|(webOS)" + "|(Kindle/(1\\.0|2\\.[05]|3\\.0))").test(window.navigator.userAgent) || $('<input type="file"/>').prop("disabled"));
    $.support.xhrFileUpload = !!(window.ProgressEvent && window.FileReader);
    $.support.xhrFormDataFileUpload = !!window.FormData;
    $.support.blobSlice = window.Blob && (Blob.prototype.slice || Blob.prototype.webkitSlice || Blob.prototype.mozSlice);

    function getDragHandler(type) {
        var isDragOver = type === "dragover";
        return function(e) {
            e.dataTransfer = e.originalEvent && e.originalEvent.dataTransfer;
            var dataTransfer = e.dataTransfer;
            if (dataTransfer && $.inArray("Files", dataTransfer.types) !== -1 && this._trigger(type, $.Event(type, {
                    delegatedEvent: e
                })) !== false) {
                e.preventDefault();
                if (isDragOver) {
                    dataTransfer.dropEffect = "copy"
                }
            }
        }
    }
    $.widget("blueimp.fileupload", {
        options: {
            dropZone: $(document),
            pasteZone: undefined,
            fileInput: undefined,
            replaceFileInput: true,
            paramName: undefined,
            singleFileUploads: true,
            limitMultiFileUploads: undefined,
            limitMultiFileUploadSize: undefined,
            limitMultiFileUploadSizeOverhead: 512,
            sequentialUploads: false,
            limitConcurrentUploads: undefined,
            forceIframeTransport: false,
            redirect: undefined,
            redirectParamName: undefined,
            postMessage: undefined,
            multipart: true,
            maxChunkSize: undefined,
            uploadedBytes: undefined,
            recalculateProgress: true,
            progressInterval: 100,
            bitrateInterval: 500,
            autoUpload: true,
            uniqueFilenames: undefined,
            messages: {
                uploadedBytes: "Uploaded bytes exceed file size"
            },
            i18n: function(message, context) {
                message = this.messages[message] || message.toString();
                if (context) {
                    $.each(context, function(key, value) {
                        message = message.replace("{" + key + "}", value)
                    })
                }
                return message
            },
            formData: function(form) {
                return form.serializeArray()
            },
            add: function(e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                if (data.autoUpload || data.autoUpload !== false && $(this).fileupload("option", "autoUpload")) {
                    data.process().done(function() {
                        data.submit()
                    })
                }
            },
            processData: false,
            contentType: false,
            cache: false,
            timeout: 0
        },
        _specialOptions: ["fileInput", "dropZone", "pasteZone", "multipart", "forceIframeTransport"],
        _blobSlice: $.support.blobSlice && function() {
            var slice = this.slice || this.webkitSlice || this.mozSlice;
            return slice.apply(this, arguments)
        },
        _BitrateTimer: function() {
            this.timestamp = Date.now ? Date.now() : (new Date).getTime();
            this.loaded = 0;
            this.bitrate = 0;
            this.getBitrate = function(now, loaded, interval) {
                var timeDiff = now - this.timestamp;
                if (!this.bitrate || !interval || timeDiff > interval) {
                    this.bitrate = (loaded - this.loaded) * (1e3 / timeDiff) * 8;
                    this.loaded = loaded;
                    this.timestamp = now
                }
                return this.bitrate
            }
        },
        _isXHRUpload: function(options) {
            return !options.forceIframeTransport && (!options.multipart && $.support.xhrFileUpload || $.support.xhrFormDataFileUpload)
        },
        _getFormData: function(options) {
            var formData;
            if ($.type(options.formData) === "function") {
                return options.formData(options.form)
            }
            if ($.isArray(options.formData)) {
                return options.formData
            }
            if ($.type(options.formData) === "object") {
                formData = [];
                $.each(options.formData, function(name, value) {
                    formData.push({
                        name: name,
                        value: value
                    })
                });
                return formData
            }
            return []
        },
        _getTotal: function(files) {
            var total = 0;
            $.each(files, function(index, file) {
                total += file.size || 1
            });
            return total
        },
        _initProgressObject: function(obj) {
            var progress = {
                loaded: 0,
                total: 0,
                bitrate: 0
            };
            if (obj._progress) {
                $.extend(obj._progress, progress)
            } else {
                obj._progress = progress
            }
        },
        _initResponseObject: function(obj) {
            var prop;
            if (obj._response) {
                for (prop in obj._response) {
                    if (obj._response.hasOwnProperty(prop)) {
                        delete obj._response[prop]
                    }
                }
            } else {
                obj._response = {}
            }
        },
        _onProgress: function(e, data) {
            if (e.lengthComputable) {
                var now = Date.now ? Date.now() : (new Date).getTime(),
                    loaded;
                if (data._time && data.progressInterval && now - data._time < data.progressInterval && e.loaded !== e.total) {
                    return
                }
                data._time = now;
                loaded = Math.floor(e.loaded / e.total * (data.chunkSize || data._progress.total)) + (data.uploadedBytes || 0);
                this._progress.loaded += loaded - data._progress.loaded;
                this._progress.bitrate = this._bitrateTimer.getBitrate(now, this._progress.loaded, data.bitrateInterval);
                data._progress.loaded = data.loaded = loaded;
                data._progress.bitrate = data.bitrate = data._bitrateTimer.getBitrate(now, loaded, data.bitrateInterval);
                this._trigger("progress", $.Event("progress", {
                    delegatedEvent: e
                }), data);
                this._trigger("progressall", $.Event("progressall", {
                    delegatedEvent: e
                }), this._progress)
            }
        },
        _initProgressListener: function(options) {
            var that = this,
                xhr = options.xhr ? options.xhr() : $.ajaxSettings.xhr();
            if (xhr.upload) {
                $(xhr.upload).bind("progress", function(e) {
                    var oe = e.originalEvent;
                    e.lengthComputable = oe.lengthComputable;
                    e.loaded = oe.loaded;
                    e.total = oe.total;
                    that._onProgress(e, options)
                });
                options.xhr = function() {
                    return xhr
                }
            }
        },
        _deinitProgressListener: function(options) {
            var xhr = options.xhr ? options.xhr() : $.ajaxSettings.xhr();
            if (xhr.upload) {
                $(xhr.upload).unbind("progress")
            }
        },
        _isInstanceOf: function(type, obj) {
            return Object.prototype.toString.call(obj) === "[object " + type + "]"
        },
        _getUniqueFilename: function(name, map) {
            name = String(name);
            if (map[name]) {
                name = name.replace(/(?: \(([\d]+)\))?(\.[^.]+)?$/, function(_, p1, p2) {
                    var index = p1 ? Number(p1) + 1 : 1;
                    var ext = p2 || "";
                    return " (" + index + ")" + ext
                });
                return this._getUniqueFilename(name, map)
            }
            map[name] = true;
            return name
        },
        _initXHRData: function(options) {
            var that = this,
                formData, file = options.files[0],
                multipart = options.multipart || !$.support.xhrFileUpload,
                paramName = $.type(options.paramName) === "array" ? options.paramName[0] : options.paramName;
            options.headers = $.extend({}, options.headers);
            if (options.contentRange) {
                options.headers["Content-Range"] = options.contentRange
            }
            if (!multipart || options.blob || !this._isInstanceOf("File", file)) {
                options.headers["Content-Disposition"] = 'attachment; filename="' + encodeURI(file.uploadName || file.name) + '"'
            }
            if (!multipart) {
                options.contentType = file.type || "application/octet-stream";
                options.data = options.blob || file
            } else if ($.support.xhrFormDataFileUpload) {
                if (options.postMessage) {
                    formData = this._getFormData(options);
                    if (options.blob) {
                        formData.push({
                            name: paramName,
                            value: options.blob
                        })
                    } else {
                        $.each(options.files, function(index, file) {
                            formData.push({
                                name: $.type(options.paramName) === "array" && options.paramName[index] || paramName,
                                value: file
                            })
                        })
                    }
                } else {
                    if (that._isInstanceOf("FormData", options.formData)) {
                        formData = options.formData
                    } else {
                        formData = new FormData;
                        $.each(this._getFormData(options), function(index, field) {
                            formData.append(field.name, field.value)
                        })
                    }
                    if (options.blob) {
                        formData.append(paramName, options.blob, file.uploadName || file.name)
                    } else {
                        $.each(options.files, function(index, file) {
                            if (that._isInstanceOf("File", file) || that._isInstanceOf("Blob", file)) {
                                var fileName = file.uploadName || file.name;
                                if (options.uniqueFilenames) {
                                    fileName = that._getUniqueFilename(fileName, options.uniqueFilenames)
                                }
                                formData.append($.type(options.paramName) === "array" && options.paramName[index] || paramName, file, fileName)
                            }
                        })
                    }
                }
                options.data = formData
            }
            options.blob = null
        },
        _initIframeSettings: function(options) {
            var targetHost = $("<a></a>").prop("href", options.url).prop("host");
            options.dataType = "iframe " + (options.dataType || "");
            options.formData = this._getFormData(options);
            if (options.redirect && targetHost && targetHost !== location.host) {
                options.formData.push({
                    name: options.redirectParamName || "redirect",
                    value: options.redirect
                })
            }
        },
        _initDataSettings: function(options) {
            if (this._isXHRUpload(options)) {
                if (!this._chunkedUpload(options, true)) {
                    if (!options.data) {
                        this._initXHRData(options)
                    }
                    this._initProgressListener(options)
                }
                if (options.postMessage) {
                    options.dataType = "postmessage " + (options.dataType || "")
                }
            } else {
                this._initIframeSettings(options)
            }
        },
        _getParamName: function(options) {
            var fileInput = $(options.fileInput),
                paramName = options.paramName;
            if (!paramName) {
                paramName = [];
                fileInput.each(function() {
                    var input = $(this),
                        name = input.prop("name") || "files[]",
                        i = (input.prop("files") || [1]).length;
                    while (i) {
                        paramName.push(name);
                        i -= 1
                    }
                });
                if (!paramName.length) {
                    paramName = [fileInput.prop("name") || "files[]"]
                }
            } else if (!$.isArray(paramName)) {
                paramName = [paramName]
            }
            return paramName
        },
        _initFormSettings: function(options) {
            if (!options.form || !options.form.length) {
                options.form = $(options.fileInput.prop("form"));
                if (!options.form.length) {
                    options.form = $(this.options.fileInput.prop("form"))
                }
            }
            options.paramName = this._getParamName(options);
            if (!options.url) {
                options.url = options.form.prop("action") || location.href
            }
            options.type = (options.type || $.type(options.form.prop("method")) === "string" && options.form.prop("method") || "").toUpperCase();
            if (options.type !== "POST" && options.type !== "PUT" && options.type !== "PATCH") {
                options.type = "POST"
            }
            if (!options.formAcceptCharset) {
                options.formAcceptCharset = options.form.attr("accept-charset")
            }
        },
        _getAJAXSettings: function(data) {
            var options = $.extend({}, this.options, data);
            this._initFormSettings(options);
            this._initDataSettings(options);
            return options
        },
        _getDeferredState: function(deferred) {
            if (deferred.state) {
                return deferred.state()
            }
            if (deferred.isResolved()) {
                return "resolved"
            }
            if (deferred.isRejected()) {
                return "rejected"
            }
            return "pending"
        },
        _enhancePromise: function(promise) {
            promise.success = promise.done;
            promise.error = promise.fail;
            promise.complete = promise.always;
            return promise
        },
        _getXHRPromise: function(resolveOrReject, context, args) {
            var dfd = $.Deferred(),
                promise = dfd.promise();
            context = context || this.options.context || promise;
            if (resolveOrReject === true) {
                dfd.resolveWith(context, args)
            } else if (resolveOrReject === false) {
                dfd.rejectWith(context, args)
            }
            promise.abort = dfd.promise;
            return this._enhancePromise(promise)
        },
        _addConvenienceMethods: function(e, data) {
            var that = this,
                getPromise = function(args) {
                    return $.Deferred().resolveWith(that, args).promise()
                };
            data.process = function(resolveFunc, rejectFunc) {
                if (resolveFunc || rejectFunc) {
                    data._processQueue = this._processQueue = (this._processQueue || getPromise([this])).then(function() {
                        if (data.errorThrown) {
                            return $.Deferred().rejectWith(that, [data]).promise()
                        }
                        return getPromise(arguments)
                    }).then(resolveFunc, rejectFunc)
                }
                return this._processQueue || getPromise([this])
            };
            data.submit = function() {
                if (this.state() !== "pending") {
                    data.jqXHR = this.jqXHR = that._trigger("submit", $.Event("submit", {
                        delegatedEvent: e
                    }), this) !== false && that._onSend(e, this)
                }
                return this.jqXHR || that._getXHRPromise()
            };
            data.abort = function() {
                if (this.jqXHR) {
                    return this.jqXHR.abort()
                }
                this.errorThrown = "abort";
                that._trigger("fail", null, this);
                return that._getXHRPromise(false)
            };
            data.state = function() {
                if (this.jqXHR) {
                    return that._getDeferredState(this.jqXHR)
                }
                if (this._processQueue) {
                    return that._getDeferredState(this._processQueue)
                }
            };
            data.processing = function() {
                return !this.jqXHR && this._processQueue && that._getDeferredState(this._processQueue) === "pending"
            };
            data.progress = function() {
                return this._progress
            };
            data.response = function() {
                return this._response
            }
        },
        _getUploadedBytes: function(jqXHR) {
            var range = jqXHR.getResponseHeader("Range"),
                parts = range && range.split("-"),
                upperBytesPos = parts && parts.length > 1 && parseInt(parts[1], 10);
            return upperBytesPos && upperBytesPos + 1
        },
        _chunkedUpload: function(options, testOnly) {
            options.uploadedBytes = options.uploadedBytes || 0;
            var that = this,
                file = options.files[0],
                fs = file.size,
                ub = options.uploadedBytes,
                mcs = options.maxChunkSize || fs,
                slice = this._blobSlice,
                dfd = $.Deferred(),
                promise = dfd.promise(),
                jqXHR, upload;
            if (!(this._isXHRUpload(options) && slice && (ub || ($.type(mcs) === "function" ? mcs(options) : mcs) < fs)) || options.data) {
                return false
            }
            if (testOnly) {
                return true
            }
            if (ub >= fs) {
                file.error = options.i18n("uploadedBytes");
                return this._getXHRPromise(false, options.context, [null, "error", file.error])
            }
            upload = function() {
                var o = $.extend({}, options),
                    currentLoaded = o._progress.loaded;
                o.blob = slice.call(file, ub, ub + ($.type(mcs) === "function" ? mcs(o) : mcs), file.type);
                o.chunkSize = o.blob.size;
                o.contentRange = "bytes " + ub + "-" + (ub + o.chunkSize - 1) + "/" + fs;
                that._trigger("chunkbeforesend", null, o);
                that._initXHRData(o);
                that._initProgressListener(o);
                jqXHR = (that._trigger("chunksend", null, o) !== false && $.ajax(o) || that._getXHRPromise(false, o.context)).done(function(result, textStatus, jqXHR) {
                    ub = that._getUploadedBytes(jqXHR) || ub + o.chunkSize;
                    if (currentLoaded + o.chunkSize - o._progress.loaded) {
                        that._onProgress($.Event("progress", {
                            lengthComputable: true,
                            loaded: ub - o.uploadedBytes,
                            total: ub - o.uploadedBytes
                        }), o)
                    }
                    options.uploadedBytes = o.uploadedBytes = ub;
                    o.result = result;
                    o.textStatus = textStatus;
                    o.jqXHR = jqXHR;
                    that._trigger("chunkdone", null, o);
                    that._trigger("chunkalways", null, o);
                    if (ub < fs) {
                        upload()
                    } else {
                        dfd.resolveWith(o.context, [result, textStatus, jqXHR])
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    o.jqXHR = jqXHR;
                    o.textStatus = textStatus;
                    o.errorThrown = errorThrown;
                    that._trigger("chunkfail", null, o);
                    that._trigger("chunkalways", null, o);
                    dfd.rejectWith(o.context, [jqXHR, textStatus, errorThrown])
                }).always(function() {
                    that._deinitProgressListener(o)
                })
            };
            this._enhancePromise(promise);
            promise.abort = function() {
                return jqXHR.abort()
            };
            upload();
            return promise
        },
        _beforeSend: function(e, data) {
            if (this._active === 0) {
                this._trigger("start");
                this._bitrateTimer = new this._BitrateTimer;
                this._progress.loaded = this._progress.total = 0;
                this._progress.bitrate = 0
            }
            this._initResponseObject(data);
            this._initProgressObject(data);
            data._progress.loaded = data.loaded = data.uploadedBytes || 0;
            data._progress.total = data.total = this._getTotal(data.files) || 1;
            data._progress.bitrate = data.bitrate = 0;
            this._active += 1;
            this._progress.loaded += data.loaded;
            this._progress.total += data.total
        },
        _onDone: function(result, textStatus, jqXHR, options) {
            var total = options._progress.total,
                response = options._response;
            if (options._progress.loaded < total) {
                this._onProgress($.Event("progress", {
                    lengthComputable: true,
                    loaded: total,
                    total: total
                }), options)
            }
            response.result = options.result = result;
            response.textStatus = options.textStatus = textStatus;
            response.jqXHR = options.jqXHR = jqXHR;
            this._trigger("done", null, options)
        },
        _onFail: function(jqXHR, textStatus, errorThrown, options) {
            var response = options._response;
            if (options.recalculateProgress) {
                this._progress.loaded -= options._progress.loaded;
                this._progress.total -= options._progress.total
            }
            response.jqXHR = options.jqXHR = jqXHR;
            response.textStatus = options.textStatus = textStatus;
            response.errorThrown = options.errorThrown = errorThrown;
            this._trigger("fail", null, options)
        },
        _onAlways: function(jqXHRorResult, textStatus, jqXHRorError, options) {
            this._trigger("always", null, options)
        },
        _onSend: function(e, data) {
            if (!data.submit) {
                this._addConvenienceMethods(e, data)
            }
            var that = this,
                jqXHR, aborted, slot, pipe, options = that._getAJAXSettings(data),
                send = function() {
                    that._sending += 1;
                    options._bitrateTimer = new that._BitrateTimer;
                    jqXHR = jqXHR || ((aborted || that._trigger("send", $.Event("send", {
                        delegatedEvent: e
                    }), options) === false) && that._getXHRPromise(false, options.context, aborted) || that._chunkedUpload(options) || $.ajax(options)).done(function(result, textStatus, jqXHR) {
                        that._onDone(result, textStatus, jqXHR, options)
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        that._onFail(jqXHR, textStatus, errorThrown, options)
                    }).always(function(jqXHRorResult, textStatus, jqXHRorError) {
                        that._deinitProgressListener(options);
                        that._onAlways(jqXHRorResult, textStatus, jqXHRorError, options);
                        that._sending -= 1;
                        that._active -= 1;
                        if (options.limitConcurrentUploads && options.limitConcurrentUploads > that._sending) {
                            var nextSlot = that._slots.shift();
                            while (nextSlot) {
                                if (that._getDeferredState(nextSlot) === "pending") {
                                    nextSlot.resolve();
                                    break
                                }
                                nextSlot = that._slots.shift()
                            }
                        }
                        if (that._active === 0) {
                            that._trigger("stop")
                        }
                    });
                    return jqXHR
                };
            this._beforeSend(e, options);
            if (this.options.sequentialUploads || this.options.limitConcurrentUploads && this.options.limitConcurrentUploads <= this._sending) {
                if (this.options.limitConcurrentUploads > 1) {
                    slot = $.Deferred();
                    this._slots.push(slot);
                    pipe = slot.then(send)
                } else {
                    this._sequence = this._sequence.then(send, send);
                    pipe = this._sequence
                }
                pipe.abort = function() {
                    aborted = [undefined, "abort", "abort"];
                    if (!jqXHR) {
                        if (slot) {
                            slot.rejectWith(options.context, aborted)
                        }
                        return send()
                    }
                    return jqXHR.abort()
                };
                return this._enhancePromise(pipe)
            }
            return send()
        },
        _onAdd: function(e, data) {
            var that = this,
                result = true,
                options = $.extend({}, this.options, data),
                files = data.files,
                filesLength = files.length,
                limit = options.limitMultiFileUploads,
                limitSize = options.limitMultiFileUploadSize,
                overhead = options.limitMultiFileUploadSizeOverhead,
                batchSize = 0,
                paramName = this._getParamName(options),
                paramNameSet, paramNameSlice, fileSet, i, j = 0;
            if (!filesLength) {
                return false
            }
            if (limitSize && files[0].size === undefined) {
                limitSize = undefined
            }
            if (!(options.singleFileUploads || limit || limitSize) || !this._isXHRUpload(options)) {
                fileSet = [files];
                paramNameSet = [paramName]
            } else if (!(options.singleFileUploads || limitSize) && limit) {
                fileSet = [];
                paramNameSet = [];
                for (i = 0; i < filesLength; i += limit) {
                    fileSet.push(files.slice(i, i + limit));
                    paramNameSlice = paramName.slice(i, i + limit);
                    if (!paramNameSlice.length) {
                        paramNameSlice = paramName
                    }
                    paramNameSet.push(paramNameSlice)
                }
            } else if (!options.singleFileUploads && limitSize) {
                fileSet = [];
                paramNameSet = [];
                for (i = 0; i < filesLength; i = i + 1) {
                    batchSize += files[i].size + overhead;
                    if (i + 1 === filesLength || batchSize + files[i + 1].size + overhead > limitSize || limit && i + 1 - j >= limit) {
                        fileSet.push(files.slice(j, i + 1));
                        paramNameSlice = paramName.slice(j, i + 1);
                        if (!paramNameSlice.length) {
                            paramNameSlice = paramName
                        }
                        paramNameSet.push(paramNameSlice);
                        j = i + 1;
                        batchSize = 0
                    }
                }
            } else {
                paramNameSet = paramName
            }
            data.originalFiles = files;
            $.each(fileSet || files, function(index, element) {
                var newData = $.extend({}, data);
                newData.files = fileSet ? element : [element];
                newData.paramName = paramNameSet[index];
                that._initResponseObject(newData);
                that._initProgressObject(newData);
                that._addConvenienceMethods(e, newData);
                result = that._trigger("add", $.Event("add", {
                    delegatedEvent: e
                }), newData);
                return result
            });
            return result
        },
        _replaceFileInput: function(data) {
            var input = data.fileInput,
                inputClone = input.clone(true),
                restoreFocus = input.is(document.activeElement);
            data.fileInputClone = inputClone;
            $("<form></form>").append(inputClone)[0].reset();
            input.after(inputClone).detach();
            if (restoreFocus) {
                inputClone.focus()
            }
            $.cleanData(input.unbind("remove"));
            this.options.fileInput = this.options.fileInput.map(function(i, el) {
                if (el === input[0]) {
                    return inputClone[0]
                }
                return el
            });
            if (input[0] === this.element[0]) {
                this.element = inputClone
            }
        },
        _handleFileTreeEntry: function(entry, path) {
            var that = this,
                dfd = $.Deferred(),
                entries = [],
                dirReader, errorHandler = function(e) {
                    if (e && !e.entry) {
                        e.entry = entry
                    }
                    dfd.resolve([e])
                },
                successHandler = function(entries) {
                    that._handleFileTreeEntries(entries, path + entry.name + "/").done(function(files) {
                        dfd.resolve(files)
                    }).fail(errorHandler)
                },
                readEntries = function() {
                    dirReader.readEntries(function(results) {
                        if (!results.length) {
                            successHandler(entries)
                        } else {
                            entries = entries.concat(results);
                            readEntries()
                        }
                    }, errorHandler)
                };
            path = path || "";
            if (entry.isFile) {
                if (entry._file) {
                    entry._file.relativePath = path;
                    dfd.resolve(entry._file)
                } else {
                    entry.file(function(file) {
                        file.relativePath = path;
                        dfd.resolve(file)
                    }, errorHandler)
                }
            } else if (entry.isDirectory) {
                dirReader = entry.createReader();
                readEntries()
            } else {
                dfd.resolve([])
            }
            return dfd.promise()
        },
        _handleFileTreeEntries: function(entries, path) {
            var that = this;
            return $.when.apply($, $.map(entries, function(entry) {
                return that._handleFileTreeEntry(entry, path)
            })).then(function() {
                return Array.prototype.concat.apply([], arguments)
            })
        },
        _getDroppedFiles: function(dataTransfer) {
            dataTransfer = dataTransfer || {};
            var items = dataTransfer.items;
            if (items && items.length && (items[0].webkitGetAsEntry || items[0].getAsEntry)) {
                return this._handleFileTreeEntries($.map(items, function(item) {
                    var entry;
                    if (item.webkitGetAsEntry) {
                        entry = item.webkitGetAsEntry();
                        if (entry) {
                            entry._file = item.getAsFile()
                        }
                        return entry
                    }
                    return item.getAsEntry()
                }))
            }
            return $.Deferred().resolve($.makeArray(dataTransfer.files)).promise()
        },
        _getSingleFileInputFiles: function(fileInput) {
            fileInput = $(fileInput);
            var entries = fileInput.prop("webkitEntries") || fileInput.prop("entries"),
                files, value;
            if (entries && entries.length) {
                return this._handleFileTreeEntries(entries)
            }
            files = $.makeArray(fileInput.prop("files"));
            if (!files.length) {
                value = fileInput.prop("value");
                if (!value) {
                    return $.Deferred().resolve([]).promise()
                }
                files = [{
                    name: value.replace(/^.*\\/, "")
                }]
            } else if (files[0].name === undefined && files[0].fileName) {
                $.each(files, function(index, file) {
                    file.name = file.fileName;
                    file.size = file.fileSize
                })
            }
            return $.Deferred().resolve(files).promise()
        },
        _getFileInputFiles: function(fileInput) {
            if (!(fileInput instanceof $) || fileInput.length === 1) {
                return this._getSingleFileInputFiles(fileInput)
            }
            return $.when.apply($, $.map(fileInput, this._getSingleFileInputFiles)).then(function() {
                return Array.prototype.concat.apply([], arguments)
            })
        },
        _onChange: function(e) {
            var that = this,
                data = {
                    fileInput: $(e.target),
                    form: $(e.target.form)
                };
            this._getFileInputFiles(data.fileInput).always(function(files) {
                data.files = files;
                if (that.options.replaceFileInput) {
                    that._replaceFileInput(data)
                }
                if (that._trigger("change", $.Event("change", {
                        delegatedEvent: e
                    }), data) !== false) {
                    that._onAdd(e, data)
                }
            })
        },
        _onPaste: function(e) {
            var items = e.originalEvent && e.originalEvent.clipboardData && e.originalEvent.clipboardData.items,
                data = {
                    files: []
                };
            if (items && items.length) {
                $.each(items, function(index, item) {
                    var file = item.getAsFile && item.getAsFile();
                    if (file) {
                        data.files.push(file)
                    }
                });
                if (this._trigger("paste", $.Event("paste", {
                        delegatedEvent: e
                    }), data) !== false) {
                    this._onAdd(e, data)
                }
            }
        },
        _onDrop: function(e) {
            e.dataTransfer = e.originalEvent && e.originalEvent.dataTransfer;
            var that = this,
                dataTransfer = e.dataTransfer,
                data = {};
            if (dataTransfer && dataTransfer.files && dataTransfer.files.length) {
                e.preventDefault();
                this._getDroppedFiles(dataTransfer).always(function(files) {
                    data.files = files;
                    if (that._trigger("drop", $.Event("drop", {
                            delegatedEvent: e
                        }), data) !== false) {
                        that._onAdd(e, data)
                    }
                })
            }
        },
        _onDragOver: getDragHandler("dragover"),
        _onDragEnter: getDragHandler("dragenter"),
        _onDragLeave: getDragHandler("dragleave"),
        _initEventHandlers: function() {
            if (this._isXHRUpload(this.options)) {
                this._on(this.options.dropZone, {
                    dragover: this._onDragOver,
                    drop: this._onDrop,
                    dragenter: this._onDragEnter,
                    dragleave: this._onDragLeave
                });
                this._on(this.options.pasteZone, {
                    paste: this._onPaste
                })
            }
            if ($.support.fileInput) {
                this._on(this.options.fileInput, {
                    change: this._onChange
                })
            }
        },
        _destroyEventHandlers: function() {
            this._off(this.options.dropZone, "dragenter dragleave dragover drop");
            this._off(this.options.pasteZone, "paste");
            this._off(this.options.fileInput, "change")
        },
        _destroy: function() {
            this._destroyEventHandlers()
        },
        _setOption: function(key, value) {
            var reinit = $.inArray(key, this._specialOptions) !== -1;
            if (reinit) {
                this._destroyEventHandlers()
            }
            this._super(key, value);
            if (reinit) {
                this._initSpecialOptions();
                this._initEventHandlers()
            }
        },
        _initSpecialOptions: function() {
            var options = this.options;
            if (options.fileInput === undefined) {
                options.fileInput = this.element.is('input[type="file"]') ? this.element : this.element.find('input[type="file"]')
            } else if (!(options.fileInput instanceof $)) {
                options.fileInput = $(options.fileInput)
            }
            if (!(options.dropZone instanceof $)) {
                options.dropZone = $(options.dropZone)
            }
            if (!(options.pasteZone instanceof $)) {
                options.pasteZone = $(options.pasteZone)
            }
        },
        _getRegExp: function(str) {
            var parts = str.split("/"),
                modifiers = parts.pop();
            parts.shift();
            return new RegExp(parts.join("/"), modifiers)
        },
        _isRegExpOption: function(key, value) {
            return key !== "url" && $.type(value) === "string" && /^\/.*\/[igm]{0,3}$/.test(value)
        },
        _initDataAttributes: function() {
            var that = this,
                options = this.options,
                data = this.element.data();
            $.each(this.element[0].attributes, function(index, attr) {
                var key = attr.name.toLowerCase(),
                    value;
                if (/^data-/.test(key)) {
                    key = key.slice(5).replace(/-[a-z]/g, function(str) {
                        return str.charAt(1).toUpperCase()
                    });
                    value = data[key];
                    if (that._isRegExpOption(key, value)) {
                        value = that._getRegExp(value)
                    }
                    options[key] = value
                }
            })
        },
        _create: function() {
            this._initDataAttributes();
            this._initSpecialOptions();
            this._slots = [];
            this._sequence = this._getXHRPromise(true);
            this._sending = this._active = 0;
            this._initProgressObject(this);
            this._initEventHandlers()
        },
        active: function() {
            return this._active
        },
        progress: function() {
            return this._progress
        },
        add: function(data) {
            var that = this;
            if (!data || this.options.disabled) {
                return
            }
            if (data.fileInput && !data.files) {
                this._getFileInputFiles(data.fileInput).always(function(files) {
                    data.files = files;
                    that._onAdd(null, data)
                })
            } else {
                data.files = $.makeArray(data.files);
                this._onAdd(null, data)
            }
        },
        send: function(data) {
            if (data && !this.options.disabled) {
                if (data.fileInput && !data.files) {
                    var that = this,
                        dfd = $.Deferred(),
                        promise = dfd.promise(),
                        jqXHR, aborted;
                    promise.abort = function() {
                        aborted = true;
                        if (jqXHR) {
                            return jqXHR.abort()
                        }
                        dfd.reject(null, "abort", "abort");
                        return promise
                    };
                    this._getFileInputFiles(data.fileInput).always(function(files) {
                        if (aborted) {
                            return
                        }
                        if (!files.length) {
                            dfd.reject();
                            return
                        }
                        data.files = files;
                        jqXHR = that._onSend(null, data);
                        jqXHR.then(function(result, textStatus, jqXHR) {
                            dfd.resolve(result, textStatus, jqXHR)
                        }, function(jqXHR, textStatus, errorThrown) {
                            dfd.reject(jqXHR, textStatus, errorThrown)
                        })
                    });
                    return this._enhancePromise(promise)
                }
                data.files = $.makeArray(data.files);
                if (data.files.length) {
                    return this._onSend(null, data)
                }
            }
            return this._getXHRPromise(false, data && data.context)
        }
    })
});
/*
 * jQuery File Upload Processing Plugin
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2012, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */
(function(factory) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define(["jquery", "./jquery.fileupload"], factory)
    } else if (typeof exports === "object") {
        factory(require("jquery"), require("./jquery.fileupload"))
    } else {
        factory(window.jQuery)
    }
})(function($) {
    "use strict";
    var originalAdd = $.blueimp.fileupload.prototype.options.add;
    $.widget("blueimp.fileupload", $.blueimp.fileupload, {
        options: {
            processQueue: [],
            add: function(e, data) {
                var $this = $(this);
                data.process(function() {
                    return $this.fileupload("process", data)
                });
                originalAdd.call(this, e, data)
            }
        },
        processActions: {},
        _processFile: function(data, originalData) {
            var that = this,
                dfd = $.Deferred().resolveWith(that, [data]),
                chain = dfd.promise();
            this._trigger("process", null, data);
            $.each(data.processQueue, function(i, settings) {
                var func = function(data) {
                    if (originalData.errorThrown) {
                        return $.Deferred().rejectWith(that, [originalData]).promise()
                    }
                    return that.processActions[settings.action].call(that, data, settings)
                };
                chain = chain.then(func, settings.always && func)
            });
            chain.done(function() {
                that._trigger("processdone", null, data);
                that._trigger("processalways", null, data)
            }).fail(function() {
                that._trigger("processfail", null, data);
                that._trigger("processalways", null, data)
            });
            return chain
        },
        _transformProcessQueue: function(options) {
            var processQueue = [];
            $.each(options.processQueue, function() {
                var settings = {},
                    action = this.action,
                    prefix = this.prefix === true ? action : this.prefix;
                $.each(this, function(key, value) {
                    if ($.type(value) === "string" && value.charAt(0) === "@") {
                        settings[key] = options[value.slice(1) || (prefix ? prefix + key.charAt(0).toUpperCase() + key.slice(1) : key)]
                    } else {
                        settings[key] = value
                    }
                });
                processQueue.push(settings)
            });
            options.processQueue = processQueue
        },
        processing: function() {
            return this._processing
        },
        process: function(data) {
            var that = this,
                options = $.extend({}, this.options, data);
            if (options.processQueue && options.processQueue.length) {
                this._transformProcessQueue(options);
                if (this._processing === 0) {
                    this._trigger("processstart")
                }
                $.each(data.files, function(index) {
                    var opts = index ? $.extend({}, options) : options,
                        func = function() {
                            if (data.errorThrown) {
                                return $.Deferred().rejectWith(that, [data]).promise()
                            }
                            return that._processFile(opts, data)
                        };
                    opts.index = index;
                    that._processing += 1;
                    that._processingQueue = that._processingQueue.then(func, func).always(function() {
                        that._processing -= 1;
                        if (that._processing === 0) {
                            that._trigger("processstop")
                        }
                    })
                })
            }
            return this._processingQueue
        },
        _create: function() {
            this._super();
            this._processing = 0;
            this._processingQueue = $.Deferred().resolveWith(this).promise()
        }
    })
});
/*
 * jQuery File Upload User Interface Plugin
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */
(function(factory) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define(["jquery", "blueimp-tmpl", "./jquery.fileupload-image", "./jquery.fileupload-audio", "./jquery.fileupload-video", "./jquery.fileupload-validate"], factory)
    } else if (typeof exports === "object") {
        factory(require("jquery"), require("blueimp-tmpl"), require("./jquery.fileupload-image"), require("./jquery.fileupload-audio"), require("./jquery.fileupload-video"), require("./jquery.fileupload-validate"))
    } else {
        factory(window.jQuery, window.tmpl)
    }
})(function($, tmpl) {
    "use strict";
    $.blueimp.fileupload.prototype._specialOptions.push("filesContainer", "uploadTemplateId", "downloadTemplateId");
    $.widget("blueimp.fileupload", $.blueimp.fileupload, {
        options: {
            autoUpload: false,
            uploadTemplateId: "template-upload",
            downloadTemplateId: "template-download",
            filesContainer: undefined,
            prependFiles: false,
            dataType: "json",
            messages: {
                unknownError: "Unknown error"
            },
            getNumberOfFiles: function() {
                return this.filesContainer.children().not(".processing").length
            },
            getFilesFromResponse: function(data) {
                if (data.result && $.isArray(data.result.files)) {
                    return data.result.files
                }
                return []
            },
            add: function(e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var $this = $(this),
                    that = $this.data("blueimp-fileupload") || $this.data("fileupload"),
                    options = that.options;
                data.context = that._renderUpload(data.files).data("data", data).addClass("processing");
                options.filesContainer[options.prependFiles ? "prepend" : "append"](data.context);
                that._forceReflow(data.context);
                that._transition(data.context);
                data.process(function() {
                    return $this.fileupload("process", data)
                }).always(function() {
                    data.context.each(function(index) {
                        $(this).find(".size").text(that._formatFileSize(data.files[index].size))
                    }).removeClass("processing");
                    that._renderPreviews(data)
                }).done(function() {
                    data.context.find(".start").prop("disabled", false);
                    if (that._trigger("added", e, data) !== false && (options.autoUpload || data.autoUpload) && data.autoUpload !== false) {
                        data.submit()
                    }
                }).fail(function() {
                    if (data.files.error) {
                        data.context.each(function(index) {
                            var error = data.files[index].error;
                            if (error) {
                                $(this).find(".error").text(error)
                            }
                        })
                    }
                })
            },
            send: function(e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that = $(this).data("blueimp-fileupload") || $(this).data("fileupload");
                if (data.context && data.dataType && data.dataType.substr(0, 6) === "iframe") {
                    data.context.find(".progress").addClass(!$.support.transition && "progress-animated").attr("aria-valuenow", 100).children().first().css("width", "100%")
                }
                return that._trigger("sent", e, data)
            },
            done: function(e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that = $(this).data("blueimp-fileupload") || $(this).data("fileupload"),
                    getFilesFromResponse = data.getFilesFromResponse || that.options.getFilesFromResponse,
                    files = getFilesFromResponse(data),
                    template, deferred;
                if (data.context) {
                    
                    data.context.each(function(index) {
                        var file = files[index] || {
                            error: "Empty file upload result"
                        };
                        deferred = that._addFinishedDeferreds();
                        that._transition($(this)).done(function() {
                            var node = $(this);
                            template = that._renderDownload([file]).replaceAll(node);
                            that._forceReflow(template);
                            that._transition(template).done(function() {
                                data.context = $(this);
                                that._trigger("completed", e, data);
                                that._trigger("finished", e, data);
                                deferred.resolve()
                            })


                            //set input data
                            var files_val = document.getElementById("files").value;//get value
                            var files_json_data = JSON.parse(files_val);//string to json
                            console.log(files_json_data);
                            files_json_data.push(data.result.files[0]);//set key:value
                            document.getElementById("files").value = JSON.stringify(files_json_data);//json to string

                            //console.log(document.getElementById("files").value);


                        })
                    })
                    
                } else {
                    template = that._renderDownload(files)[that.options.prependFiles ? "prependTo" : "appendTo"](that.options.filesContainer);
                    that._forceReflow(template);
                    deferred = that._addFinishedDeferreds();
                    that._transition(template).done(function() {
                        data.context = $(this);
                        that._trigger("completed", e, data);
                        that._trigger("finished", e, data);
                        deferred.resolve()
                    })
                }
            },
            fail: function(e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that = $(this).data("blueimp-fileupload") || $(this).data("fileupload"),
                    template, deferred;
                if (data.context) {
                    data.context.each(function(index) {
                        if (data.errorThrown !== "abort") {
                            var file = data.files[index];
                            file.error = file.error || data.errorThrown || data.i18n("unknownError");
                            deferred = that._addFinishedDeferreds();
                            that._transition($(this)).done(function() {
                                var node = $(this);
                                template = that._renderDownload([file]).replaceAll(node);
                                that._forceReflow(template);
                                that._transition(template).done(function() {
                                    data.context = $(this);
                                    that._trigger("failed", e, data);
                                    that._trigger("finished", e, data);
                                    deferred.resolve()
                                })
                            })
                        } else {
                            deferred = that._addFinishedDeferreds();
                            that._transition($(this)).done(function() {
                                $(this).remove();
                                that._trigger("failed", e, data);
                                that._trigger("finished", e, data);
                                deferred.resolve()
                            })
                        }
                    })
                } else if (data.errorThrown !== "abort") {
                    data.context = that._renderUpload(data.files)[that.options.prependFiles ? "prependTo" : "appendTo"](that.options.filesContainer).data("data", data);
                    that._forceReflow(data.context);
                    deferred = that._addFinishedDeferreds();
                    that._transition(data.context).done(function() {
                        data.context = $(this);
                        that._trigger("failed", e, data);
                        that._trigger("finished", e, data);
                        deferred.resolve()
                    })
                } else {
                    that._trigger("failed", e, data);
                    that._trigger("finished", e, data);
                    that._addFinishedDeferreds().resolve()
                }
            },
            progress: function(e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var progress = Math.floor(data.loaded / data.total * 100);
                if (data.context) {
                    data.context.each(function() {
                        $(this).find(".progress").attr("aria-valuenow", progress).children().first().css("width", progress + "%")
                    })
                }
            },
            progressall: function(e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var $this = $(this),
                    progress = Math.floor(data.loaded / data.total * 100),
                    globalProgressNode = $this.find(".fileupload-progress"),
                    extendedProgressNode = globalProgressNode.find(".progress-extended");
                if (extendedProgressNode.length) {
                    extendedProgressNode.html(($this.data("blueimp-fileupload") || $this.data("fileupload"))._renderExtendedProgress(data))
                }
                globalProgressNode.find(".progress").attr("aria-valuenow", progress).children().first().css("width", progress + "%")
            },
            start: function(e) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that = $(this).data("blueimp-fileupload") || $(this).data("fileupload");
                that._resetFinishedDeferreds();
                that._transition($(this).find(".fileupload-progress")).done(function() {
                    that._trigger("started", e)
                })
            },
            stop: function(e) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that = $(this).data("blueimp-fileupload") || $(this).data("fileupload"),
                    deferred = that._addFinishedDeferreds();
                $.when.apply($, that._getFinishedDeferreds()).done(function() {
                    that._trigger("stopped", e)
                });
                that._transition($(this).find(".fileupload-progress")).done(function() {
                    $(this).find(".progress").attr("aria-valuenow", "0").children().first().css("width", "0%");
                    $(this).find(".progress-extended").html("&nbsp;");
                    deferred.resolve()
                })
            },
            processstart: function(e) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                $(this).addClass("fileupload-processing")
            },
            processstop: function(e) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                $(this).removeClass("fileupload-processing")
            },
            destroy: function(e, data) {
                if (e.isDefaultPrevented()) {
                    return false
                }
                var that = $(this).data("blueimp-fileupload") || $(this).data("fileupload"),
                    removeNode = function() {
                        that._transition(data.context).done(function() {
                            $(this).remove();
                            that._trigger("destroyed", e, data)
                        })
                    };
                if (data.url) {
                    data.dataType = data.dataType || that.options.dataType;
                    $.ajax(data).done(removeNode).fail(function() {
                        that._trigger("destroyfailed", e, data)
                    })
                } else {
                    removeNode()
                }
            }
        },
        _resetFinishedDeferreds: function() {
            this._finishedUploads = []
        },
        _addFinishedDeferreds: function(deferred) {
            if (!deferred) {
                deferred = $.Deferred()
            }
            this._finishedUploads.push(deferred);
            return deferred
        },
        _getFinishedDeferreds: function() {
            return this._finishedUploads
        },
        _enableDragToDesktop: function() {
            var link = $(this),
                url = link.prop("href"),
                name = link.prop("download"),
                type = "application/octet-stream";
            link.bind("dragstart", function(e) {
                try {
                    e.originalEvent.dataTransfer.setData("DownloadURL", [type, name, url].join(":"))
                } catch (ignore) {}
            })
        },
        _formatFileSize: function(bytes) {
            if (typeof bytes !== "number") {
                return ""
            }
            if (bytes >= 1e9) {
                return (bytes / 1e9).toFixed(2) + " GB"
            }
            if (bytes >= 1e6) {
                return (bytes / 1e6).toFixed(2) + " MB"
            }
            return (bytes / 1e3).toFixed(2) + " KB"
        },
        _formatBitrate: function(bits) {
            if (typeof bits !== "number") {
                return ""
            }
            if (bits >= 1e9) {
                return (bits / 1e9).toFixed(2) + " Gbit/s"
            }
            if (bits >= 1e6) {
                return (bits / 1e6).toFixed(2) + " Mbit/s"
            }
            if (bits >= 1e3) {
                return (bits / 1e3).toFixed(2) + " kbit/s"
            }
            return bits.toFixed(2) + " bit/s"
        },
        _formatTime: function(seconds) {
            var date = new Date(seconds * 1e3),
                days = Math.floor(seconds / 86400);
            days = days ? days + "d " : "";
            return days + ("0" + date.getUTCHours()).slice(-2) + ":" + ("0" + date.getUTCMinutes()).slice(-2) + ":" + ("0" + date.getUTCSeconds()).slice(-2)
        },
        _formatPercentage: function(floatValue) {
            return (floatValue * 100).toFixed(2) + " %"
        },
        _renderExtendedProgress: function(data) {
            return this._formatBitrate(data.bitrate) + " | " + this._formatTime((data.total - data.loaded) * 8 / data.bitrate) + " | " + this._formatPercentage(data.loaded / data.total) + " | " + this._formatFileSize(data.loaded) + " / " + this._formatFileSize(data.total)
        },
        _renderTemplate: function(func, files) {
            if (!func) {
                return $()
            }
            //console.log(files);
            var result = func({
                files: files,
                formatFileSize: this._formatFileSize,
                options: this.options
            });
            if (result instanceof $) {
                return result
            }
            return $(this.options.templatesContainer).html(result).children()
        },
        _renderPreviews: function(data) {
            data.context.find(".preview").each(function(index, elm) {
                $(elm).append(data.files[index].preview)
            })
        },
        _renderUpload: function(files) {
            return this._renderTemplate(this.options.uploadTemplate, files)
        },
        _renderDownload: function(files) {
            return this._renderTemplate(this.options.downloadTemplate, files).find("a[download]").each(this._enableDragToDesktop).end()
        },
        _startHandler: function(e) {
            e.preventDefault();
            var button = $(e.currentTarget),
                template = button.closest(".template-upload"),
                data = template.data("data");
            button.prop("disabled", true);
            if (data && data.submit) {
                data.submit()
            }
        },
        _cancelHandler: function(e) {
            e.preventDefault();
            var template = $(e.currentTarget).closest(".template-upload,.template-download"),
                data = template.data("data") || {};
            data.context = data.context || template;
            if (data.abort) {
                data.abort()
            } else {
                data.errorThrown = "abort";
                this._trigger("fail", e, data)
            }
        },
        _deleteHandler: function(e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            this._trigger("destroy", e, $.extend({
                context: button.closest(".template-download"),
                type: "DELETE"
            }, button.data()))
        },
        _forceReflow: function(node) {
            return $.support.transition && node.length && node[0].offsetWidth
        },
        _transition: function(node) {
            var dfd = $.Deferred();
            if ($.support.transition && node.hasClass("fade") && node.is(":visible")) {
                node.bind($.support.transition.end, function(e) {
                    if (e.target === node[0]) {
                        node.unbind($.support.transition.end);
                        dfd.resolveWith(node)
                    }
                }).toggleClass("in")
            } else {
                node.toggleClass("in");
                dfd.resolveWith(node)
            }
            return dfd
        },
        _initButtonBarEventHandlers: function() {
            var fileUploadButtonBar = this.element.find(".fileupload-buttonbar"),
                filesList = this.options.filesContainer;
            this._on(fileUploadButtonBar.find(".start"), {
                click: function(e) {
                    e.preventDefault();
                    filesList.find(".start").click()
                }
            });
            this._on(fileUploadButtonBar.find(".cancel"), {
                click: function(e) {
                    e.preventDefault();
                    filesList.find(".cancel").click()
                }
            });
            this._on(fileUploadButtonBar.find(".delete"), {
                click: function(e) {
                    e.preventDefault();
                    filesList.find(".toggle:checked").closest(".template-download").find(".delete").click();
                    fileUploadButtonBar.find(".toggle").prop("checked", false)
                }
            });
            this._on(fileUploadButtonBar.find(".toggle"), {
                change: function(e) {
                    filesList.find(".toggle").prop("checked", $(e.currentTarget).is(":checked"))
                }
            })
        },
        _destroyButtonBarEventHandlers: function() {
            this._off(this.element.find(".fileupload-buttonbar").find(".start, .cancel, .delete"), "click");
            this._off(this.element.find(".fileupload-buttonbar .toggle"), "change.")
        },
        _initEventHandlers: function() {
            this._super();
            this._on(this.options.filesContainer, {
                "click .start": this._startHandler,
                "click .cancel": this._cancelHandler,
                "click .delete": this._deleteHandler
            });
            this._initButtonBarEventHandlers()
        },
        _destroyEventHandlers: function() {
            this._destroyButtonBarEventHandlers();
            this._off(this.options.filesContainer, "click");
            this._super()
        },
        _enableFileInputButton: function() {
            this.element.find(".fileinput-button input").prop("disabled", false).parent().removeClass("disabled")
        },
        _disableFileInputButton: function() {
            this.element.find(".fileinput-button input").prop("disabled", true).parent().addClass("disabled")
        },
        _initTemplates: function() {
            var options = this.options;
            options.templatesContainer = this.document[0].createElement(options.filesContainer.prop("nodeName"));
            if (tmpl) {
                if (options.uploadTemplateId) {
                    options.uploadTemplate = tmpl(options.uploadTemplateId)
                }
                if (options.downloadTemplateId) {
                    options.downloadTemplate = tmpl(options.downloadTemplateId)
                }
            }
        },
        _initFilesContainer: function() {
            var options = this.options;
            if (options.filesContainer === undefined) {
                options.filesContainer = this.element.find(".files")
            } else if (!(options.filesContainer instanceof $)) {
                options.filesContainer = $(options.filesContainer)
            }
        },
        _initSpecialOptions: function() {
            this._super();
            this._initFilesContainer();
            this._initTemplates()
        },
        _create: function() {
            this._super();
            this._resetFinishedDeferreds();
            if (!$.support.fileInput) {
                this._disableFileInputButton()
            }
        },
        enable: function() {
            var wasDisabled = false;
            if (this.options.disabled) {
                wasDisabled = true
            }
            this._super();
            if (wasDisabled) {
                this.element.find("input, button").prop("disabled", false);
                this._enableFileInputButton()
            }
        },
        disable: function() {
            if (!this.options.disabled) {
                this.element.find("input, button").prop("disabled", true);
                this._disableFileInputButton()
            }
            this._super()
        }
    })
});
var LoadingMask = {
    show: function(container) {
        $(container).after('<div class="mask"><div class="loader"></div></div>')
    },
    hide: function() {
        $("div.mask").remove()
    }
};
$(document).ajaxSend(function(event, jqxhr, settings) {
    if (settings.triggerLoadingMask !== false && $(".mask").length === 0) {
        LoadingMask.show("#wrap")
    }
});
$(document).ajaxStop(function() {
    LoadingMask.hide()
});
(function($) {
    $.dialog = function(options) {
        var title = options.title || translate("modal notice title");
        var confirmButtonText = options.confirmButtonText || translate("default modal confirmButtonText");
        var dialog = $('<div class="modal fade"> <div class="modal-dialog"> <div class="modal-content"> <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> <h4 class="modal-title"></h4> </div><div class="modal-body"> <p></p></div><div class="modal-footer"><button type="button" class="btn btn-prio" autofocus></button> </div></div></div></div>');
        dialog.appendTo("body");
        dialog.find(".modal-title").html(title);
        dialog.find(".modal-body p").html(options.body);
        dialog.find(".modal-footer button").text(confirmButtonText);
        dialog.find(".btn-prio").click(function() {
            if (options.confirmFunction) {
                options.confirmFunction(dialog)
            }
            dialog.modal("hide")
        });
        dialog.on("shown.bs.modal", function() {
            $(this).find("[autofocus]").first().focus()
        });
        if (options.hideFunction) {
            dialog.on("hide.bs.modal", function() {
                options.hideFunction()
            })
        }
        dialog.modal("show")
    }
})(jQuery);

function redirect(url) {
    if (navigator.userAgent.match(/Android/i)) document.location = url;
    else window.location.href = url
}(function($) {
    $.fn.ajaxForm = function(options) {
        var $form = $(this);
        $(this).submit(function(event) {
            event.preventDefault();
            $form.find("button[type=submit]").attr("disabled", "disabled");
            var sendForm = function() {
                $.ajax({
                    url: $form.attr("action"),
                    type: "POST",
                    dataType: "json",
                    data: $form.serializeArray()
                }).done(function(response, textStatus, xhr) {
                    $form.find("button[type=submit]").removeAttr("disabled");
                    $form.find(".error").removeClass("error");
                    $form.find(".error-msg").remove();
                    if (false === response.success) {
                        if (response.msg) {
                            $.dialog({
                                title: translate("notice"),
                                body: response.msg,
                                confirmButtonText: translate("default dialog confirm")
                            })
                        }
                        $.each(response.error, function(field, msg) {
                            var $field = $form.find("[name*=\\[" + field + "\\]]");
                            $field.addClass("error");
                            $field.before('<p class="red error-msg">' + msg + "</p>")
                        });
                        $.each(response.data, function(field, value) {
                            var $field = $form.find("[name*=\\[" + field + "\\]]");
                            $field.val(value)
                        })
                    }
                    if (options.success && response.success) {
                        options.success(response)
                    } else if (options.fail && !response.success) {
                        options.fail(response)
                    }
                }).always(function(data, textStatus, xhr) {
                    if (options.always) options.always(data, textStatus, xhr)
                })
            };
            if (options.preCondition) {
                options.preCondition().then(function(result) {
                    if (false === result && $.isFunction(options.preConditionFailed)) options.preConditionFailed();
                    else sendForm()
                })
            } else {
                sendForm()
            }
        });
        return this
    }
})(jQuery);
$(function() {
    var offset = (new Date).getTimezoneOffset();
    $(this).find("input[name=timezoneoffset]").val(offset)
});

function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,8})$/;
    return regex.test(email)
}

function validateEmailString(emailString) {
    var emails = emailString.split(",");
    var validEmails = [];
    var invalidEmails = [];
    $.each(emails, function(key, email) {
        email = email.trim();
        if ("" !== email) {
            if (isEmail(email)) {
                validEmails.push(email)
            } else {
                invalidEmails.push(email)
            }
        }
    });
    return {
        validEmails: validEmails,
        invalidEmails: invalidEmails
    }
}
$.fn.isGrid = function(params) {
    var grid = this;
    $.ajax({
        url: params.url,
        type: "POST",
        dataType: "json",
        data: params.data ? params.data : null,
        beforeSend: function(xhr) {}
    }).done(function(response, textStatus, xhr) {
        grid.text(null);
        var table = $("<table></table>").addClass(params.tableCls);
        var tableHead = $("<thead></thead>");
        var tableBody = $("<tbody></tbody>");
        var tableRow = $("<tr></tr>").addClass(params.trCls);
        $.each(params.columns, function(key, column) {
            var tableColumn = $("<th></th>").addClass(params.thCls).addClass(column.cls).html(column.name);
            if (column.sortby == true) {
                tableColumn.attr("data-sortby", true).append('<i class="fa fa-sort"></i>').append('<i class="fa fa-sort-asc"></i>').append('<i class="fa fa-sort-desc"></i>')
            }
            tableRow.append(tableColumn)
        });
        tableHead.append(tableRow);
        table.append(tableHead);
        $.each(response.data, function(key, row) {
            var tableRow = $("<tr></tr>").addClass(params.trCls);
            if (row.key) {
                tableRow.attr("data-sortable", true)
            }
            $.each(params.columns, function(key, column) {
                var tableColumn = $("<td></td>").addClass(column.cls);
                var renderResult = column.renderer(row, tableColumn, grid, params);
                if ($.isFunction(column.raw)) {
                    tableColumn.attr("data-value", column.raw(row))
                }
                if (typeof renderResult !== "object") {
                    tableRow.append(tableColumn.html(renderResult))
                } else {
                    tableRow.append(renderResult)
                }
            });
            tableBody.append(tableRow)
        });
        table.append(tableBody);
        $(grid).append(table);
        $(".fa-sort-asc, .fa-sort-desc").hide();
        tooltip();
        var clips = document.querySelectorAll(".clipboard");
        var clipboard = new Clipboard(clips);
        clipboard.on("success", function(e) {
            $(e.trigger).fadeOut("fast", function() {
                $(this).fadeIn("fast")
            })
        })
    });
    return grid
};
$(document).on("click", "[data-sortby]", function() {
    var orderArray = [];
    var $searchBody = $(this).closest("table").find("tbody");
    var index = $(this).closest("tr").children().index($(this));
    $searchBody.find("tr[data-sortable]").each(function(idx) {
        var $orderRow = $(this);
        var $orderCell = $orderRow.find("td").eq(index);
        var $valueAttr = $orderCell.attr("data-value");
        var orderValue = typeof $valueAttr !== typeof undefined && $valueAttr !== false ? $valueAttr : $orderCell.text();
        orderArray.push({
            key: orderValue.toLowerCase().trim(),
            value: $orderRow.detach()
        })
    });
    orderArray.sort(function(a, b) {
        if (!isNaN(parseFloat(a.key)) && isFinite(a.key)) {
            return a.key - b.key
        } else if (moment(a.key, "L", true).isValid()) {
            return moment(a.key, "L", true).unix() - moment(b.key, "L", true).unix()
        } else {
            if (a.key < b.key) return -1;
            if (a.key > b.key) return 1;
            return 0
        }
    });
    $searchBody.find("[data-sortable]").remove();
    $(this).closest("tr").find(".fa-sort-asc, .fa-sort-desc").hide();
    $(this).closest("tr").find(".fa-sort").show();
    if ($(this).attr("data-dir")) {
        $(this).removeAttr("data-dir");
        $(this).find(".fa-sort").hide();
        $(this).find(".fa-sort-asc").show()
    } else {
        $(this).attr("data-dir", true);
        $(this).find(".fa-sort").hide();
        $(this).find(".fa-sort-desc").show();
        orderArray.reverse()
    }
    $.each(orderArray, function(key, record) {
        $searchBody.append(record.value)
    })
});
var locale = window.navigator.userLanguage || window.navigator.language;


function byte2human(byte, addUnit, getOnlyUnit) {
    var result = 0;
    var kilobyte = 1024;
    var megabyte = kilobyte * 1024;
    var gigabyte = megabyte * 1024;
    var terabyte = gigabyte * 1024;
    var unit = "";
    var byte = parseInt(byte);
    if (byte) {
        if (byte < kilobyte) {
            unit = " B";
            result = byte.toFixed(2);
            if (addUnit !== false) result += unit
        } else if (byte < megabyte) {
            unit = " KB";
            result = (byte / kilobyte).toFixed(2);
            if (addUnit !== false) result += unit
        } else if (byte < gigabyte) {
            unit = " MB";
            result = (byte / megabyte).toFixed(2);
            if (addUnit !== false) result += unit
        } else if (byte < terabyte) {
            unit = " GB";
            result = (byte / gigabyte).toFixed(2);
            if (addUnit !== false) result += unit
        } else {
            unit = " TB";
            result = (byte / terabyte).toFixed(2);
            if (addUnit !== false) result += unit
        }
        if (getOnlyUnit === true) return unit
    }
    return result
}
(function($) {
    $.fn.ajaxButton = function(options) {
        $(this).click(function(event) {
            var $that = $(this);
            if ($that.attr("disabled") === "disabled") return false;
            $that.attr("disabled", "disabled");
            event.preventDefault();

            function send(options) {
                var data;
                if ($.isFunction(options.data)) {
                    data = options.data()
                } else {
                    data = options.data
                }
                $.ajax({
                    url: options.url,
                    type: "POST",
                    dataType: "json",
                    data: data,
                    beforeSend: function(xhr, settings) {
                        if (options.preCondition && options.preCondition() === false) {
                            options.preConditionFailed();
                            return false
                        }
                    }
                }).always(function(data, textStatus, xhr) {
                    if (options.always) options.always(data, textStatus, xhr);
                    $that.removeAttr("disabled")
                }).done(function(data, textStatus, xhr) {
                    if (options.success && data.success) {
                        options.success(data)
                    } else if (options.fail && !data.success) {
                        options.fail(data)
                    }
                    $that.removeAttr("disabled")
                })
            }
            if (options.confirm) {
                $.dialog({
                    title: translate("please confirm"),
                    body: options.confirm,
                    confirmButtonText: translate("confirm"),
                    confirmFunction: function(modal) {
                        send(options)
                    },
                    hideFunction: function() {
                        $that.removeAttr("disabled")
                    }
                })
            } else {
                send(options)
            }
        });
        return this
    }
})(jQuery);

function setCookie(cname, cvalue, exdays) {
    var d = new Date;
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1e3);
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length)
    }
    return ""
}

function tooltip() {
    $('[data-toggle="tooltip"]').popover({
        placement: function(context, source) {
            if (typeof $(source).attr("data-placement") != "undefined") return $(source).attr("data-placement");
            if ($(window).width() >= 865) {
                return "left"
            } else {
                return "top"
            }
        },
        trigger: "hover",
        container: "body",
        html: true
    })
}
$(function() {
    tooltip()
});