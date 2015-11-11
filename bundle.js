(function (modules) {
    var installedModules = {};

    function __webpack_require__(moduleId) {
        if (installedModules[moduleId])return installedModules[moduleId].exports;
        var module = installedModules[moduleId] = {exports: {}, id: moduleId, loaded: false};
        modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
        module.loaded = true;
        return module.exports
    }

    __webpack_require__.m = modules;
    __webpack_require__.c = installedModules;
    __webpack_require__.p = "";
    return __webpack_require__(0)
})([function (module, exports, __webpack_require__) {
    (function (riot) {
        __webpack_require__(2);
        riot.mount("highlights")
    }).call(exports, __webpack_require__(1))
}, function (module, exports, __webpack_require__) {
    (function (window) {
        "use strict";
        var riot = {version: "v2.2.1", settings: {}};
        var T_STRING = "string";
        var T_OBJECT = "object";
        var isArray = Array.isArray || function () {
                var _ts = Object.prototype.toString;
                return function (v) {
                    return _ts.call(v) === "[object Array]"
                }
            }();
        var ieVersion = function (win) {
            return (win && win.document || {}).documentMode | 0
        }(window);
        riot.observable = function (el) {
            el = el || {};
            var callbacks = {}, _id = 0;
            el.on = function (events, fn) {
                if (isFunction(fn)) {
                    fn._id = typeof fn._id == "undefined" ? _id++ : fn._id;
                    events.replace(/\S+/g, function (name, pos) {
                        (callbacks[name] = callbacks[name] || []).push(fn);
                        fn.typed = pos > 0
                    })
                }
                return el
            };
            el.off = function (events, fn) {
                if (events == "*")callbacks = {}; else {
                    events.replace(/\S+/g, function (name) {
                        if (fn) {
                            var arr = callbacks[name];
                            for (var i = 0, cb; cb = arr && arr[i]; ++i) {
                                if (cb._id == fn._id) {
                                    arr.splice(i, 1);
                                    i--
                                }
                            }
                        } else {
                            callbacks[name] = []
                        }
                    })
                }
                return el
            };
            el.one = function (name, fn) {
                function on() {
                    el.off(name, on);
                    fn.apply(el, arguments)
                }

                return el.on(name, on)
            };
            el.trigger = function (name) {
                var args = [].slice.call(arguments, 1), fns = callbacks[name] || [];
                for (var i = 0, fn; fn = fns[i]; ++i) {
                    if (!fn.busy) {
                        fn.busy = 1;
                        fn.apply(el, fn.typed ? [name].concat(args) : args);
                        if (fns[i] !== fn) {
                            i--
                        }
                        fn.busy = 0
                    }
                }
                if (callbacks.all && name != "all") {
                    el.trigger.apply(el, ["all", name].concat(args))
                }
                return el
            };
            return el
        };
        riot.mixin = function () {
            var mixins = {};
            return function (name, mixin) {
                if (!mixin)return mixins[name];
                mixins[name] = mixin
            }
        }();
        (function (riot, evt, window) {
            if (!window)return;
            var loc = window.location, fns = riot.observable(), win = window, started = false, current;

            function hash() {
                return loc.href.split("#")[1] || ""
            }

            function parser(path) {
                return path.split("/")
            }

            function emit(path) {
                if (path.type)path = hash();
                if (path != current) {
                    fns.trigger.apply(null, ["H"].concat(parser(path)));
                    current = path
                }
            }

            var r = riot.route = function (arg) {
                if (arg[0]) {
                    loc.hash = arg;
                    emit(arg)
                } else {
                    fns.on("H", arg)
                }
            };
            r.exec = function (fn) {
                fn.apply(null, parser(hash()))
            };
            r.parser = function (fn) {
                parser = fn
            };
            r.stop = function () {
                if (!started)return;
                win.removeEventListener ? win.removeEventListener(evt, emit, false) : win.detachEvent("on" + evt, emit);
                fns.off("*");
                started = false
            };
            r.start = function () {
                if (started)return;
                win.addEventListener ? win.addEventListener(evt, emit, false) : win.attachEvent("on" + evt, emit);
                started = true
            };
            r.start()
        })(riot, "hashchange", window);
        var brackets = function (orig) {
            var cachedBrackets, r, b, re = /[{}]/g;
            return function (x) {
                var s = riot.settings.brackets || orig;
                if (cachedBrackets !== s) {
                    cachedBrackets = s;
                    b = s.split(" ");
                    r = b.map(function (e) {
                        return e.replace(/(?=.)/g, "\\")
                    })
                }
                return x instanceof RegExp ? s === orig ? x : new RegExp(x.source.replace(re, function (b) {
                    return r[~~(b === "}")]
                }), x.global ? "g" : "") : b[x]
            }
        }("{ }");
        var tmpl = function () {
            var cache = {}, reVars = /(['"\/]).*?[^\\]\1|\.\w*|\w*:|\b(?:(?:new|typeof|in|instanceof) |(?:this|true|false|null|undefined)\b|function *\()|([a-z_$]\w*)/gi;
            return function (str, data) {
                return str && (cache[str] = cache[str] || tmpl(str))(data)
            };
            function tmpl(s, p) {
                s = (s || brackets(0) + brackets(1)).replace(brackets(/\\{/g), "￰").replace(brackets(/\\}/g), "￱");
                p = split(s, extract(s, brackets(/{/), brackets(/}/)));
                return new Function("d", "return " + (!p[0] && !p[2] && !p[3] ? expr(p[1]) : "[" + p.map(function (s, i) {
                        return i % 2 ? expr(s, true) : '"' + s.replace(/\n/g, "\\n").replace(/"/g, '\\"') + '"'
                    }).join(",") + '].join("")').replace(/\uFFF0/g, brackets(0)).replace(/\uFFF1/g, brackets(1)) + ";")
            }

            function expr(s, n) {
                s = s.replace(/\n/g, " ").replace(brackets(/^[{ ]+|[ }]+$|\/\*.+?\*\//g), "");
                return /^\s*[\w- "']+ *:/.test(s) ? "[" + extract(s, /["' ]*[\w- ]+["' ]*:/, /,(?=["' ]*[\w- ]+["' ]*:)|}|$/).map(function (pair) {
                    return pair.replace(/^[ "']*(.+?)[ "']*: *(.+?),? *$/, function (_, k, v) {
                        return v.replace(/[^&|=!><]+/g, wrap) + '?"' + k + '":"",'
                    })
                }).join("") + '].join(" ").trim()' : wrap(s, n)
            }

            function wrap(s, nonull) {
                s = s.trim();
                return !s ? "" : "(function(v){try{v=" + (s.replace(reVars, function (s, _, v) {
                    return v ? "(d." + v + "===undefined?" + (typeof window == "undefined" ? "global." : "window.") + v + ":d." + v + ")" : s
                }) || "x") + "}catch(e){" + "}finally{return " + (nonull === true ? '!v&&v!==0?"":v' : "v") + "}}).call(d)"
            }

            function split(str, substrings) {
                var parts = [];
                substrings.map(function (sub, i) {
                    i = str.indexOf(sub);
                    parts.push(str.slice(0, i), sub);
                    str = str.slice(i + sub.length)
                });
                return parts.concat(str)
            }

            function extract(str, open, close) {
                var start, level = 0, matches = [], re = new RegExp("(" + open.source + ")|(" + close.source + ")", "g");
                str.replace(re, function (_, open, close, pos) {
                    if (!level && open)start = pos;
                    level += open ? 1 : -1;
                    if (!level && close != null)matches.push(str.slice(start, pos + close.length))
                });
                return matches
            }
        }();

        function loopKeys(expr) {
            var b0 = brackets(0), els = expr.slice(b0.length).match(/\s*(\S+?)\s*(?:,\s*(\S)+)?\s+in\s+(.+)/);
            return els ? {key: els[1], pos: els[2], val: b0 + els[3]} : {val: expr}
        }

        function mkitem(expr, key, val) {
            var item = {};
            item[expr.key] = key;
            if (expr.pos)item[expr.pos] = val;
            return item
        }

        function _each(dom, parent, expr) {
            remAttr(dom, "each");
            var template = dom.outerHTML, root = dom.parentNode, placeholder = document.createComment("riot placeholder"), tags = [], child = getTag(dom), checksum;
            root.insertBefore(placeholder, dom);
            expr = loopKeys(expr);
            parent.one("premount", function () {
                if (root.stub)root = parent.root;
                dom.parentNode.removeChild(dom)
            }).on("update", function () {
                var items = tmpl(expr.val, parent), test;
                if (!isArray(items)) {
                    test = checksum;
                    checksum = items ? JSON.stringify(items) : "";
                    if (checksum === test)return;
                    items = !items ? [] : Object.keys(items).map(function (key) {
                        return mkitem(expr, key, items[key])
                    })
                }
                var frag = document.createDocumentFragment(), i = tags.length, j = items.length;
                while (i > j)tags[--i].unmount();
                tags.length = j;
                test = !checksum && !!expr.key;
                for (i = 0; i < j; ++i) {
                    var _item = test ? mkitem(expr, items[i], i) : items[i];
                    if (!tags[i]) {
                        (tags[i] = new Tag({tmpl: template}, {
                            parent: parent,
                            isLoop: true,
                            root: root,
                            item: _item
                        })).mount();
                        frag.appendChild(tags[i].root)
                    }
                    tags[i]._item = _item;
                    tags[i].update(_item)
                }
                root.insertBefore(frag, placeholder);
                if (child)parent.tags[getTagName(dom)] = tags
            }).one("updated", function () {
                var keys = Object.keys(parent);
                walk(root, function (node) {
                    if (node.nodeType == 1 && !node.isLoop && !node._looped) {
                        node._visited = false;
                        node._looped = true;
                        setNamed(node, parent, keys)
                    }
                })
            })
        }

        function parseNamedElements(root, parent, childTags) {
            walk(root, function (dom) {
                if (dom.nodeType == 1) {
                    dom.isLoop = dom.parentNode && dom.parentNode.isLoop || dom.getAttribute("each") ? 1 : 0;
                    var child = getTag(dom);
                    if (child && !dom.isLoop) {
                        var tag = new Tag(child, {
                            root: dom,
                            parent: parent
                        }, dom.innerHTML), tagName = getTagName(dom), ptag = parent, cachedTag;
                        while (!getTag(ptag.root)) {
                            if (!ptag.parent)break;
                            ptag = ptag.parent
                        }
                        tag.parent = ptag;
                        cachedTag = ptag.tags[tagName];
                        if (cachedTag) {
                            if (!isArray(cachedTag))ptag.tags[tagName] = [cachedTag];
                            ptag.tags[tagName].push(tag)
                        } else {
                            ptag.tags[tagName] = tag
                        }
                        dom.innerHTML = "";
                        childTags.push(tag)
                    }
                    if (!dom.isLoop)setNamed(dom, parent, [])
                }
            })
        }

        function parseExpressions(root, tag, expressions) {
            function addExpr(dom, val, extra) {
                if (val.indexOf(brackets(0)) >= 0) {
                    var expr = {dom: dom, expr: val};
                    expressions.push(extend(expr, extra))
                }
            }

            walk(root, function (dom) {
                var type = dom.nodeType;
                if (type == 3 && dom.parentNode.tagName != "STYLE")addExpr(dom, dom.nodeValue);
                if (type != 1)return;
                var attr = dom.getAttribute("each");
                if (attr) {
                    _each(dom, tag, attr);
                    return false
                }
                each(dom.attributes, function (attr) {
                    var name = attr.name, bool = name.split("__")[1];
                    addExpr(dom, attr.value, {attr: bool || name, bool: bool});
                    if (bool) {
                        remAttr(dom, name);
                        return false
                    }
                });
                if (getTag(dom))return false
            })
        }

        function Tag(impl, conf, innerHTML) {
            var self = riot.observable(this), opts = inherit(conf.opts) || {}, dom = mkdom(impl.tmpl), parent = conf.parent, isLoop = conf.isLoop, item = conf.item, expressions = [], childTags = [], root = conf.root, fn = impl.fn, tagName = root.tagName.toLowerCase(), attr = {}, loopDom, TAG_ATTRIBUTES = /([\w\-]+)\s?=\s?['"]([^'"]+)["']/gim;
            if (fn && root._tag) {
                root._tag.unmount(true)
            }
            this.isMounted = false;
            if (impl.attrs) {
                var attrs = impl.attrs.match(TAG_ATTRIBUTES);
                each(attrs, function (a) {
                    var kv = a.split(/\s?=\s?/);
                    root.setAttribute(kv[0], kv[1].replace(/['"]/g, ""))
                })
            }
            root._tag = this;
            this._id = fastAbs(~~((new Date).getTime() * Math.random()));
            extend(this, {parent: parent, root: root, opts: opts, tags: {}}, item);
            each(root.attributes, function (el) {
                var val = el.value;
                if (brackets(/\{.*\}/).test(val))attr[el.name] = val
            });
            if (dom.innerHTML && !/select|select|optgroup|tbody|tr/.test(tagName))dom.innerHTML = replaceYield(dom.innerHTML, innerHTML);
            function updateOpts() {
                each(root.attributes, function (el) {
                    opts[el.name] = tmpl(el.value, parent || self)
                });
                each(Object.keys(attr), function (name) {
                    opts[name] = tmpl(attr[name], parent || self)
                })
            }

            this.update = function (data) {
                extend(self, data);
                updateOpts();
                self.trigger("update", data);
                update(expressions, self, data);
                self.trigger("updated")
            };
            this.mixin = function () {
                each(arguments, function (mix) {
                    mix = typeof mix == "string" ? riot.mixin(mix) : mix;
                    each(Object.keys(mix), function (key) {
                        if (key != "init")self[key] = typeof mix[key] == "function" ? mix[key].bind(self) : mix[key]
                    });
                    if (mix.init)mix.init.bind(self)()
                })
            };
            this.mount = function () {
                updateOpts();
                fn && fn.call(self, opts);
                toggle(true);
                parseExpressions(dom, self, expressions);
                if (!self.parent)self.update();
                self.trigger("premount");
                if (isLoop) {
                    self.root = root = loopDom = dom.firstChild
                } else {
                    while (dom.firstChild)root.appendChild(dom.firstChild);
                    if (root.stub)self.root = root = parent.root
                }
                if (!self.parent || self.parent.isMounted) {
                    self.isMounted = true;
                    self.trigger("mount")
                } else self.parent.one("mount", function () {
                    if (!isInStub(self.root)) {
                        self.parent.isMounted = self.isMounted = true;
                        self.trigger("mount")
                    }
                })
            };
            this.unmount = function (keepRootTag) {
                var el = loopDom || root, p = el.parentNode;
                if (p) {
                    if (parent) {
                        if (isArray(parent.tags[tagName])) {
                            each(parent.tags[tagName], function (tag, i) {
                                if (tag._id == self._id)parent.tags[tagName].splice(i, 1)
                            })
                        } else parent.tags[tagName] = undefined
                    } else {
                        while (el.firstChild)el.removeChild(el.firstChild)
                    }
                    if (!keepRootTag)p.removeChild(el)
                }
                self.trigger("unmount");
                toggle();
                self.off("*");
                root._tag = null
            };
            function toggle(isMount) {
                each(childTags, function (child) {
                    child[isMount ? "mount" : "unmount"]()
                });
                if (parent) {
                    var evt = isMount ? "on" : "off";
                    if (isLoop)parent[evt]("unmount", self.unmount); else parent[evt]("update", self.update)[evt]("unmount", self.unmount)
                }
            }

            parseNamedElements(dom, this, childTags)
        }

        function setEventHandler(name, handler, dom, tag, item) {
            dom[name] = function (e) {
                e = e || window.event;
                if (!e.which)e.which = e.charCode || e.keyCode;
                if (!e.target)e.target = e.srcElement;
                try {
                    e.currentTarget = dom
                } catch (ignored) {
                    ""
                }
                e.item = tag._item ? tag._item : item;
                if (handler.call(tag, e) !== true && !/radio|check/.test(dom.type)) {
                    e.preventDefault && e.preventDefault();
                    e.returnValue = false
                }
                if (!e.preventUpdate) {
                    var el = item ? tag.parent : tag;
                    el.update()
                }
            }
        }

        function insertTo(root, node, before) {
            if (root) {
                root.insertBefore(before, node);
                root.removeChild(node)
            }
        }

        function update(expressions, tag, item) {
            each(expressions, function (expr, i) {
                var dom = expr.dom, attrName = expr.attr, value = tmpl(expr.expr, tag), parent = expr.dom.parentNode;
                if (value == null)value = "";
                if (parent && parent.tagName == "TEXTAREA")value = value.replace(/riot-/g, "");
                if (expr.value === value)return;
                expr.value = value;
                if (!attrName)return dom.nodeValue = value.toString();
                remAttr(dom, attrName);
                if (typeof value == "function") {
                    setEventHandler(attrName, value, dom, tag, item)
                } else if (attrName == "if") {
                    var stub = expr.stub;
                    if (value) {
                        if (stub) {
                            insertTo(stub.parentNode, stub, dom);
                            dom.inStub = false;
                            if (!isInStub(dom)) {
                                walk(dom, function (el) {
                                    if (el._tag && !el._tag.isMounted)el._tag.isMounted = !!el._tag.trigger("mount")
                                })
                            }
                        }
                    } else {
                        stub = expr.stub = stub || document.createTextNode("");
                        insertTo(dom.parentNode, dom, stub);
                        dom.inStub = true
                    }
                } else if (/^(show|hide)$/.test(attrName)) {
                    if (attrName == "hide")value = !value;
                    dom.style.display = value ? "" : "none"
                } else if (attrName == "value") {
                    dom.value = value
                } else if (attrName.slice(0, 5) == "riot-" && attrName != "riot-tag") {
                    attrName = attrName.slice(5);
                    value ? dom.setAttribute(attrName, value) : remAttr(dom, attrName)
                } else {
                    if (expr.bool) {
                        dom[attrName] = value;
                        if (!value)return;
                        value = attrName
                    }
                    if (typeof value != "object")dom.setAttribute(attrName, value)
                }
            })
        }

        function each(els, fn) {
            for (var i = 0, len = (els || []).length, el; i < len; i++) {
                el = els[i];
                if (el != null && fn(el, i) === false)i--
            }
            return els
        }

        function isFunction(v) {
            return typeof v === "function" || false
        }

        function remAttr(dom, name) {
            dom.removeAttribute(name)
        }

        function fastAbs(nr) {
            return (nr ^ nr >> 31) - (nr >> 31)
        }

        function getTagName(dom) {
            var child = getTag(dom), namedTag = dom.getAttribute("name"), tagName = namedTag && namedTag.indexOf(brackets(0)) < 0 ? namedTag : child.name;
            return tagName
        }

        function extend(src) {
            var obj, args = arguments;
            for (var i = 1; i < args.length; ++i) {
                if (obj = args[i]) {
                    for (var key in obj) {
                        src[key] = obj[key]
                    }
                }
            }
            return src
        }

        function mkdom(template) {
            var checkie = ieVersion && ieVersion < 10, matches = /^\s*<([\w-]+)/.exec(template), tagName = matches ? matches[1].toLowerCase() : "", rootTag = tagName === "th" || tagName === "td" ? "tr" : tagName === "tr" ? "tbody" : "div", el = mkEl(rootTag);
            el.stub = true;
            if (checkie) {
                if (tagName === "optgroup")optgroupInnerHTML(el, template); else if (tagName === "option")optionInnerHTML(el, template); else if (rootTag !== "div")tbodyInnerHTML(el, template, tagName); else checkie = 0
            }
            if (!checkie)el.innerHTML = template;
            return el
        }

        function walk(dom, fn) {
            if (dom) {
                if (fn(dom) === false)walk(dom.nextSibling, fn); else {
                    dom = dom.firstChild;
                    while (dom) {
                        walk(dom, fn);
                        dom = dom.nextSibling
                    }
                }
            }
        }

        function isInStub(dom) {
            while (dom) {
                if (dom.inStub)return true;
                dom = dom.parentNode
            }
            return false
        }

        function mkEl(name) {
            return document.createElement(name)
        }

        function replaceYield(tmpl, innerHTML) {
            return tmpl.replace(/<(yield)\/?>(<\/\1>)?/gim, innerHTML || "")
        }

        function $$(selector, ctx) {
            return (ctx || document).querySelectorAll(selector)
        }

        function inherit(parent) {
            function Child() {
            }

            Child.prototype = parent;
            return new Child
        }

        function setNamed(dom, parent, keys) {
            each(dom.attributes, function (attr) {
                if (dom._visited)return;
                if (attr.name === "id" || attr.name === "name") {
                    dom._visited = true;
                    var p, v = attr.value;
                    if (~keys.indexOf(v))return;
                    p = parent[v];
                    if (!p)parent[v] = dom; else isArray(p) ? p.push(dom) : parent[v] = [p, dom]
                }
            })
        }

        function tbodyInnerHTML(el, html, tagName) {
            var div = mkEl("div"), loops = /td|th/.test(tagName) ? 3 : 2, child;
            div.innerHTML = "<table>" + html + "</table>";
            child = div.firstChild;
            while (loops--) {
                child = child.firstChild
            }
            el.appendChild(child)
        }

        function optionInnerHTML(el, html) {
            var opt = mkEl("option"), valRegx = /value=[\"'](.+?)[\"']/, selRegx = /selected=[\"'](.+?)[\"']/, eachRegx = /each=[\"'](.+?)[\"']/, ifRegx = /if=[\"'](.+?)[\"']/, innerRegx = />([^<]*)</, valuesMatch = html.match(valRegx), selectedMatch = html.match(selRegx), innerValue = html.match(innerRegx), eachMatch = html.match(eachRegx), ifMatch = html.match(ifRegx);
            if (innerValue) {
                opt.innerHTML = innerValue[1]
            } else {
                opt.innerHTML = html
            }
            if (valuesMatch) {
                opt.value = valuesMatch[1]
            }
            if (selectedMatch) {
                opt.setAttribute("riot-selected", selectedMatch[1])
            }
            if (eachMatch) {
                opt.setAttribute("each", eachMatch[1])
            }
            if (ifMatch) {
                opt.setAttribute("if", ifMatch[1])
            }
            el.appendChild(opt)
        }

        function optgroupInnerHTML(el, html) {
            var opt = mkEl("optgroup"), labelRegx = /label=[\"'](.+?)[\"']/, elementRegx = /^<([^>]*)>/, tagRegx = /^<([^ \>]*)/, labelMatch = html.match(labelRegx), elementMatch = html.match(elementRegx), tagMatch = html.match(tagRegx), innerContent = html;
            if (elementMatch) {
                var options = html.slice(elementMatch[1].length + 2, -tagMatch[1].length - 3).trim();
                innerContent = options
            }
            if (labelMatch) {
                opt.setAttribute("riot-label", labelMatch[1])
            }
            if (innerContent) {
                var innerOpt = mkEl("div");
                optionInnerHTML(innerOpt, innerContent);
                opt.appendChild(innerOpt.firstChild)
            }
            el.appendChild(opt)
        }

        var virtualDom = [], tagImpl = {}, styleNode;
        var RIOT_TAG = "riot-tag";

        function getTag(dom) {
            return tagImpl[dom.getAttribute(RIOT_TAG) || dom.tagName.toLowerCase()]
        }

        function injectStyle(css) {
            styleNode = styleNode || mkEl("style");
            if (!document.head)return;
            if (styleNode.styleSheet)styleNode.styleSheet.cssText += css; else styleNode.innerHTML += css;
            if (!styleNode._rendered)if (styleNode.styleSheet) {
                document.body.appendChild(styleNode)
            } else {
                var rs = $$("style[type=riot]")[0];
                if (rs) {
                    rs.parentNode.insertBefore(styleNode, rs);
                    rs.parentNode.removeChild(rs)
                } else {
                    document.head.appendChild(styleNode)
                }
            }
            styleNode._rendered = true
        }

        function mountTo(root, tagName, opts) {
            var tag = tagImpl[tagName], innerHTML = root._innerHTML = root._innerHTML || root.innerHTML;
            root.innerHTML = "";
            if (tag && root)tag = new Tag(tag, {root: root, opts: opts}, innerHTML);
            if (tag && tag.mount) {
                tag.mount();
                virtualDom.push(tag);
                return tag.on("unmount", function () {
                    virtualDom.splice(virtualDom.indexOf(tag), 1)
                })
            }
        }

        riot.tag = function (name, html, css, attrs, fn) {
            if (isFunction(attrs)) {
                fn = attrs;
                if (/^[\w\-]+\s?=/.test(css)) {
                    attrs = css;
                    css = ""
                } else attrs = ""
            }
            if (css) {
                if (isFunction(css))fn = css; else injectStyle(css)
            }
            tagImpl[name] = {name: name, tmpl: html, attrs: attrs, fn: fn};
            return name
        };
        riot.mount = function (selector, tagName, opts) {
            var els, allTags, tags = [];

            function addRiotTags(arr) {
                var list = "";
                each(arr, function (e) {
                    list += ', *[riot-tag="' + e.trim() + '"]'
                });
                return list
            }

            function selectAllTags() {
                var keys = Object.keys(tagImpl);
                return keys + addRiotTags(keys)
            }

            function pushTags(root) {
                if (root.tagName) {
                    if (tagName && !root.getAttribute(RIOT_TAG))root.setAttribute(RIOT_TAG, tagName);
                    var tag = mountTo(root, tagName || root.getAttribute(RIOT_TAG) || root.tagName.toLowerCase(), opts);
                    if (tag)tags.push(tag)
                } else if (root.length) {
                    each(root, pushTags)
                }
            }

            if (typeof tagName === T_OBJECT) {
                opts = tagName;
                tagName = 0
            }
            if (typeof selector === T_STRING) {
                if (selector === "*") {
                    selector = allTags = selectAllTags()
                } else {
                    selector += addRiotTags(selector.split(","))
                }
                els = $$(selector)
            } else els = selector;
            if (tagName === "*") {
                tagName = allTags || selectAllTags();
                if (els.tagName) {
                    els = $$(tagName, els)
                } else {
                    var nodeList = [];
                    each(els, function (_el) {
                        nodeList.push($$(tagName, _el))
                    });
                    els = nodeList
                }
                tagName = 0
            }
            if (els.tagName)pushTags(els); else each(els, pushTags);
            return tags
        };
        riot.update = function () {
            return each(virtualDom, function (tag) {
                tag.update()
            })
        };
        riot.mountTo = riot.mount;
        riot.util = {brackets: brackets, tmpl: tmpl};
        if (true)module.exports = riot; else if (typeof define === "function" && define.amd)define(function () {
            return riot
        }); else window.riot = riot
    })(typeof window != "undefined" ? window : undefined)
}, function (module, exports, __webpack_require__) {
    var riot = __webpack_require__(1);
    riot.tag("highlights", '<div class="keynote-highlights"> <div class="container"> <div class="row"> <div class="col-sm-12"> <h1>Highlights from the Keynotes</h1> </div> </div> <div class="row"> <div class="col-sm-12"> <div class="clicked-keynote-highlight" riot-style="background-image:url(\'{ url(featureditem.fields.backgroundImage) }\')"> <div class="clicked-keynote-description"> <h4>{ featureditem.fields.title }</h4> <p> { featureditem.fields.description } </p> </div> </div> </div> </div> <div class="row"> <div class="keynote-highlight-row"> <div class="keynote-highlight-day"> { collection.name } </div> <div class="col-sm-3" each="{ collection, i }"> <a href="#" class="keynote-highlight-single" onclick="{ parent.setFeatured }"> <div class="keynote-highlight-thumb { active: featured }"> <img riot-src="{ parent.url(fields.previewImage) }" alt=""> </div> <div class="keynote-highlight-title"> { fields.title } </div> </a> </div> </div> </div> <div class="row"> <div class="col-sm-8 col-sm-push-2 keynote-highlights-action"> <p> Now you can watch videos of these keynotes and almost every other session! </p> <p> <a href="http://www.openstack.org/summit/vancouver-2015/summit-videos/" class="red-btn">Watch Summit Videos Now</a> </p> </div> </div> </div> </div>', 'collection="summit-highlights"', function (opts) {
        var json = __webpack_require__(3)("./" + this.opts.collection + ".json");
        this.collection = json.items;
        this.collection.images = json.includes.Asset;
        this.collection.name = "Day 1";
        this.featureditem = this.collection[0];
        this.featureditem.featured = true;
        var self = this;
        this.setFeatured = function (e) {
            self.featureditem.featured = false;
            e.item.featured = true;
            self.featureditem = e.item
        }.bind(this);
        this.url = function (asset) {
            var imageId = asset.sys.id;
            var result = this.collection.images.filter(function (obj) {
                return obj.sys.id == imageId
            });
            return result[0].fields.file.url
        }.bind(this)
    })
}, function (module, exports, __webpack_require__) {
    var map = {"./summit-highlights.json": 4};

    function webpackContext(req) {
        return __webpack_require__(webpackContextResolve(req))
    }

    function webpackContextResolve(req) {
        return map[req] || function () {
                throw new Error("Cannot find module '" + req + "'.")
            }()
    }

    webpackContext.keys = function webpackContextKeys() {
        return Object.keys(map)
    };
    webpackContext.resolve = webpackContextResolve;
    module.exports = webpackContext;
    webpackContext.id = 3
}, function (module, exports) {
    module.exports = {
        sys: {type: "Array"},
        total: 4,
        skip: 0,
        limit: 100,
        items: [{
            sys: {
                space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                type: "Entry",
                contentType: {sys: {type: "Link", linkType: "ContentType", id: "5x2Oyw2QZGukOsOGi2GEC6"}},
                id: "3SqxpdaEK4mC0uIwu8w80M",
                revision: 2,
                createdAt: "2015-06-29T20:31:04.000Z",
                updatedAt: "2015-06-30T05:02:39.618Z",
                locale: "en-US"
            },
            fields: {
                backgroundImage: {sys: {type: "Link", linkType: "Asset", id: "1deFOJ81ZmuIo4OocOWiSw"}},
                description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quam velit, vulputate eu pharetra nec, mattis ac neque. Duis vulputate commodo lectus, ac blandit elit tincidunt id. Sed rhoncus, tortor sed eleifend tristique, tortor mauris molestie elit, et lacinia ipsum quam nec dui. Quisque nec mauris sit amet elit iaculis pretium sit amet quis magna. Aenean velit odio, elementum in tempus ut, vehicula eu diam.",
                title: "Comcast’s living room demo",
                previewImage: {sys: {type: "Link", linkType: "Asset", id: "44ajfa4kuc0Uua86ym6Gqs"}}
            }
        }, {
            fields: {
                backgroundImage: {sys: {type: "Link", linkType: "Asset", id: "5l14cZM6asKeiUy04OemQK"}},
                description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quam velit, vulputate eu pharetra nec, mattis ac neque. Duis vulputate commodo lectus, ac blandit elit tincidunt id. Sed rhoncus, tortor sed eleifend tristique, tortor mauris molestie elit, et lacinia ipsum quam nec dui. Quisque nec mauris sit amet elit iaculis pretium sit amet quis magna. Aenean velit odio, elementum in tempus ut, vehicula eu diam.",
                title: "Introducing the OpenStack Powered Cloud",
                previewImage: {sys: {type: "Link", linkType: "Asset", id: "5PiBwAmaIgqscw6UkWoKIe"}}
            },
            sys: {
                space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                type: "Entry",
                contentType: {sys: {type: "Link", linkType: "ContentType", id: "5x2Oyw2QZGukOsOGi2GEC6"}},
                id: "jHNdEaGp2g0eUWW4I0gwa",
                revision: 4,
                createdAt: "2015-06-29T20:31:04.204Z",
                updatedAt: "2015-06-30T20:43:35.917Z",
                locale: "en-US"
            }
        }, {
            fields: {
                backgroundImage: {sys: {type: "Link", linkType: "Asset", id: "PbyDrmsREs8YiQ8iIUceE"}},
                description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quam velit, vulputate eu pharetra nec, mattis ac neque. Duis vulputate commodo lectus, ac blandit elit tincidunt id. Sed rhoncus, tortor sed eleifend tristique, tortor mauris molestie elit, et lacinia ipsum quam nec dui. Quisque nec mauris sit amet elit iaculis pretium sit amet quis magna. Aenean velit odio, elementum in tempus ut, vehicula eu diam.",
                title: "DigitalFilm Treem abducts audience",
                previewImage: {sys: {type: "Link", linkType: "Asset", id: "5qZWXl2QTuMAcGakGS2Gs2"}}
            },
            sys: {
                space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                type: "Entry",
                contentType: {sys: {type: "Link", linkType: "ContentType", id: "5x2Oyw2QZGukOsOGi2GEC6"}},
                id: "1tMj4lJiT24m4mgUeUU0G6",
                revision: 4,
                createdAt: "2015-06-29T20:31:04.206Z",
                updatedAt: "2015-06-30T20:58:06.037Z",
                locale: "en-US"
            }
        }, {
            fields: {
                backgroundImage: {sys: {type: "Link", linkType: "Asset", id: "3pPtt77hdmIUc4GeGMoc80"}},
                description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quam velit, vulputate eu pharetra nec, mattis ac neque. Duis vulputate commodo lectus, ac blandit elit tincidunt id. Sed rhoncus, tortor sed eleifend tristique, tortor mauris molestie elit, et lacinia ipsum quam nec dui. Quisque nec mauris sit amet elit iaculis pretium sit amet quis magna. Aenean velit odio, elementum in tempus ut, vehicula eu diam.",
                title: "Walmart adopts OpenStack in a big way",
                previewImage: {sys: {type: "Link", linkType: "Asset", id: "nyr9WzudbwEYuM8wMam4A"}}
            },
            sys: {
                space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                type: "Entry",
                contentType: {sys: {type: "Link", linkType: "ContentType", id: "5x2Oyw2QZGukOsOGi2GEC6"}},
                id: "4Ov6RVHR3iO6CccKuSomsG",
                revision: 4,
                createdAt: "2015-06-30T04:55:14.003Z",
                updatedAt: "2015-06-30T21:00:13.707Z",
                locale: "en-US"
            }
        }],
        includes: {
            Asset: [{
                fields: {
                    file: {
                        fileName: "2.jpg",
                        contentType: "image/jpeg",
                        details: {image: {width: 340, height: 230}, size: 18777},
                        url: "//images.contentful.com/elg19wezyouu/5qZWXl2QTuMAcGakGS2Gs2/9883555cb5a74f1570e07c9ad733afe7/2.jpg"
                    }, title: "2"
                },
                sys: {
                    space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                    type: "Asset",
                    id: "5qZWXl2QTuMAcGakGS2Gs2",
                    revision: 1,
                    createdAt: "2015-06-30T05:00:42.517Z",
                    updatedAt: "2015-06-30T05:00:42.517Z",
                    locale: "en-US"
                }
            }, {
                fields: {
                    file: {
                        fileName: "3.jpg",
                        contentType: "image/jpeg",
                        details: {image: {width: 287, height: 190}, size: 13754},
                        url: "//images.contentful.com/elg19wezyouu/44ajfa4kuc0Uua86ym6Gqs/ee896dbf0801b7044f4cd948a5846a42/3.jpg"
                    }, title: "3"
                },
                sys: {
                    space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                    type: "Asset",
                    id: "44ajfa4kuc0Uua86ym6Gqs",
                    revision: 1,
                    createdAt: "2015-06-30T05:00:42.708Z",
                    updatedAt: "2015-06-30T05:00:42.709Z",
                    locale: "en-US"
                }
            }, {
                fields: {
                    file: {
                        fileName: "1.jpg",
                        contentType: "image/jpeg",
                        details: {image: {width: 280, height: 186}, size: 12013},
                        url: "//images.contentful.com/elg19wezyouu/5PiBwAmaIgqscw6UkWoKIe/c02fa89a0cd157f50d9fbc0520b9131d/1.jpg"
                    }, title: "1"
                },
                sys: {
                    space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                    type: "Asset",
                    id: "5PiBwAmaIgqscw6UkWoKIe",
                    revision: 1,
                    createdAt: "2015-06-30T05:00:42.725Z",
                    updatedAt: "2015-06-30T05:00:42.725Z",
                    locale: "en-US"
                }
            }, {
                fields: {
                    file: {
                        fileName: "4.jpg",
                        contentType: "image/jpeg",
                        details: {image: {width: 287, height: 190}, size: 16505},
                        url: "//images.contentful.com/elg19wezyouu/nyr9WzudbwEYuM8wMam4A/59816ac497fee6410b489e684c4eedcc/4.jpg"
                    }, title: "4"
                },
                sys: {
                    space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                    type: "Asset",
                    id: "nyr9WzudbwEYuM8wMam4A",
                    revision: 1,
                    createdAt: "2015-06-30T05:00:42.713Z",
                    updatedAt: "2015-06-30T05:00:42.713Z",
                    locale: "en-US"
                }
            }, {
                sys: {
                    space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                    type: "Asset",
                    id: "3pPtt77hdmIUc4GeGMoc80",
                    revision: 2,
                    createdAt: "2015-06-30T21:00:01.973Z",
                    updatedAt: "2015-07-01T01:30:13.525Z",
                    locale: "en-US"
                },
                fields: {
                    title: "Walmart-adopts-os",
                    file: {
                        fileName: "walmart2.jpg",
                        contentType: "image/jpeg",
                        details: {image: {width: 1140, height: 450}, size: 68399},
                        url: "//images.contentful.com/elg19wezyouu/3pPtt77hdmIUc4GeGMoc80/04a345474e9c7a455fb4d3af7fd1aa91/walmart2.jpg"
                    }
                }
            }, {
                sys: {
                    space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                    type: "Asset",
                    id: "PbyDrmsREs8YiQ8iIUceE",
                    revision: 3,
                    createdAt: "2015-06-30T20:55:06.749Z",
                    updatedAt: "2015-07-01T01:30:31.076Z",
                    locale: "en-US"
                },
                fields: {
                    title: "DFT-abducts-audience",
                    file: {
                        fileName: "dft-abducts2.jpg",
                        contentType: "image/jpeg",
                        details: {image: {width: 1140, height: 450}, size: 86492},
                        url: "//images.contentful.com/elg19wezyouu/PbyDrmsREs8YiQ8iIUceE/ea628c2e2198147648485bddb85349e1/dft-abducts2.jpg"
                    }
                }
            }, {
                sys: {
                    space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                    type: "Asset",
                    id: "5l14cZM6asKeiUy04OemQK",
                    revision: 2,
                    createdAt: "2015-06-30T20:43:22.365Z",
                    updatedAt: "2015-07-01T01:30:48.840Z",
                    locale: "en-US"
                },
                fields: {
                    title: "OpenStack-powered-planet-highlight",
                    file: {
                        fileName: "planet.jpg",
                        contentType: "image/jpeg",
                        details: {image: {width: 1140, height: 450}, size: 53759},
                        url: "//images.contentful.com/elg19wezyouu/5l14cZM6asKeiUy04OemQK/d753ea04a0a727ea26203ba9243bc83b/planet.jpg"
                    }
                }
            }, {
                sys: {
                    space: {sys: {type: "Link", linkType: "Space", id: "elg19wezyouu"}},
                    type: "Asset",
                    id: "1deFOJ81ZmuIo4OocOWiSw",
                    revision: 2,
                    createdAt: "2015-06-29T20:29:47.681Z",
                    updatedAt: "2015-07-01T01:31:19.781Z",
                    locale: "en-US"
                },
                fields: {
                    file: {
                        fileName: "couch.jpg",
                        contentType: "image/jpeg",
                        details: {image: {width: 1140, height: 450}, size: 84470},
                        url: "//images.contentful.com/elg19wezyouu/1deFOJ81ZmuIo4OocOWiSw/d34e49d2c6409dcd85b9f7a90e32fd0c/couch.jpg"
                    }, title: "clicked-keynote"
                }
            }]
        }
    }
}]);
(function () {
    var $, Lightbox, LightboxOptions;
    $ = jQuery;
    LightboxOptions = function () {
        function LightboxOptions() {
            this.fileLoadingImage = "data:image/gif;base64,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";
            this.resizeDuration = 700;
            this.fadeDuration = 500;
            this.labelImage = "Image";
            this.labelOf = "of"
        }

        return LightboxOptions
    }();
    Lightbox = function () {
        function Lightbox(options) {
            this.options = options;
            this.album = [];
            this.currentImageIndex = void 0;
            this.init()
        }

        Lightbox.prototype.init = function () {
            this.enable();
            return this.build()
        };
        Lightbox.prototype.enable = function () {
            var _this = this;
            return $("body").on("click", ".thumbnails[data-toggle^=lightbox] .thumbnail", function (e) {
                _this.start($(e.currentTarget));
                return false
            })
        };
        Lightbox.prototype.build = function () {
            var $lightbox, _this = this;
            $("<div/>", {id: "lightboxOverlay"}).appendTo($("body"));
            $("<div/>", {id: "lightbox"}).append($("<div/>", {"class": "lb-outerContainer"}).append($("<button/>", {
                "class": "close",
                type: "button",
                "aria-hidden": "true"
            }).html("&times;"), $("<div/>", {"class": "lb-container"}).append($("<img/>", {"class": "lb-image"}), $("<div/>", {"class": "lb-nav"}).append($("<a/>", {"class": "lb-prev"}), $("<a/>", {"class": "lb-next"})), $("<div/>", {"class": "lb-loader"}).append($("<a/>", {"class": "lb-cancel"}).append($("<img/>", {src: this.options.fileLoadingImage})))), $("<div/>", {"class": "lb-dataContainer"}).append($("<div/>", {"class": "lb-data"}).append($("<h4/>", {"class": "lb-caption"}), $("<p/>", {"class": "lb-description"}), $("<p/>", {"class": "close"}).text("close"), $("<p/>", {"class": "lb-number"}))))).appendTo($("body"));
            $("#lightboxOverlay").hide().on("click", function (e) {
                _this.end();
                return false
            });
            $lightbox = $("#lightbox");
            $lightbox.hide().on("click", function (e) {
                if ($(e.target).attr("id") === "lightbox") {
                    _this.end()
                }
                return false
            });
            $lightbox.find(".lb-outerContainer").on("click", function (e) {
                if ($(e.target).attr("id") === "lightbox") {
                    _this.end()
                }
                return false
            });
            $lightbox.find(".lb-prev").on("click", function (e) {
                _this.changeImage(_this.currentImageIndex - 1);
                return false
            });
            $lightbox.find(".lb-next").on("click", function (e) {
                _this.changeImage(_this.currentImageIndex + 1);
                return false
            });
            $lightbox.find(".lb-loader, .close").on("click", function (e) {
                _this.end();
                return false
            });
            $lightbox.find(".lb-caption").on("click", "a", function (e) {
                if (_this.album[_this.currentImageIndex].titleLink[0] === "#") {
                    _this.end();
                    window.location.hash = "";
                    window.location.hash = _this.album[_this.currentImageIndex].titleLink
                } else {
                    window.open(_this.album[_this.currentImageIndex].titleLink)
                }
                return false
            })
        };
        Lightbox.prototype.start = function ($link) {
            var $lightbox, $window, a, current, i, imageNumber, left, top, _i, _len, _ref;
            if (!$link.attr("href") && !$link.attr("data-target")) {
                return
            }
            $(window).on("resize", this.sizeOverlay);
            $("select, object, embed").css({visibility: "hidden"});
            $("#lightboxOverlay").width($(document).width()).height($(document).height()).fadeIn(this.options.fadeDuration);
            this.album = [];
            imageNumber = 0;
            current = 0;
            if ($link.parents(".thumbnails").attr("data-toggle") === "lightbox" && $link.parents(".thumbnails").find(".thumbnail").length) {
                _ref = $link.parents(".thumbnails").find(".thumbnail");
                for (i = _i = 0, _len = _ref.length; _i < _len; i = ++_i) {
                    a = _ref[i];
                    if (!$(a).attr("href") && !$(a).attr("data-target")) {
                        continue
                    }
                    this.album.push({
                        link: $(a).attr("href") || $(a).attr("data-target"),
                        title: $(a).attr("title") || $(a).attr("data-title"),
                        titleLink: $(a).attr("data-title-link"),
                        description: $(a).attr("data-description")
                    });
                    if ($link.attr("href") && $(a).attr("href") === $link.attr("href") || $link.attr("data-target") && $(a).attr("data-target") === $link.attr("data-target")) {
                        imageNumber = current
                    }
                    ++current
                }
            } else {
                this.album.push({
                    link: $link.attr("href") || $link.attr("data-target"),
                    title: $link.attr("title") || $link.attr("data-title"),
                    titleLink: $(a).attr("data-title-link"),
                    description: $link.attr("data-description")
                })
            }
            $window = $(window);
            top = $window.scrollTop() + $window.height() / 10;
            left = $window.scrollLeft();
            $lightbox = $("#lightbox");
            $lightbox.css({top: top + "px", left: left + "px"}).fadeIn(this.options.fadeDuration);
            this.changeImage(imageNumber)
        };
        Lightbox.prototype.changeImage = function (imageNumber) {
            var $image, $lightbox, preloader, _this = this;
            this.disableKeyboardNav();
            $lightbox = $("#lightbox");
            $image = $lightbox.find(".lb-image");
            this.sizeOverlay();
            $("#lightboxOverlay").fadeIn(this.options.fadeDuration);
            $(".lb-loader").fadeIn("slow");
            $lightbox.find(".lb-image, .lb-nav, .lb-prev, .lb-next, .lb-dataContainer, .lb-numbers, .lb-caption, .lb-description").hide();
            $lightbox.find(".lb-outerContainer").addClass("animating");
            preloader = new Image;
            preloader.onload = function () {
                $image.attr("src", _this.album[imageNumber].link);
                $image.width = preloader.width;
                $image.height = preloader.height;
                return _this.sizeContainer(preloader.width, preloader.height)
            };
            preloader.src = this.album[imageNumber].link;
            this.currentImageIndex = imageNumber
        };
        Lightbox.prototype.setTitle = function ($title, $titleLink) {
            if (typeof $titleLink !== "undefined" && $titleLink !== "") {
                $title = $("<a/>").attr({href: $titleLink, title: $title}).text($title)
            }
            return $title
        };
        Lightbox.prototype.sizeOverlay = function () {
            return $("#lightboxOverlay").width($(document).width()).height($(document).height())
        };
        Lightbox.prototype.sizeContainer = function (imageWidth, imageHeight) {
            var $container, $lightbox, $outerContainer, containerBottomPadding, containerLeftPadding, containerRightPadding, containerTopPadding, newHeight, newWidth, oldWidth, _this = this;
            $lightbox = $("#lightbox");
            $outerContainer = $lightbox.find(".lb-outerContainer");
            oldWidth = $outerContainer.outerWidth();
            $container = $lightbox.find(".lb-container");
            containerTopPadding = parseInt($container.css("padding-top"), 10);
            containerRightPadding = parseInt($container.css("padding-right"), 10);
            containerBottomPadding = parseInt($container.css("padding-bottom"), 10);
            containerLeftPadding = parseInt($container.css("padding-left"), 10);
            newWidth = imageWidth + containerLeftPadding + containerRightPadding;
            newHeight = imageHeight + containerTopPadding + containerBottomPadding;
            if (newWidth !== oldWidth) {
                $outerContainer.animate({width: newWidth}, this.options.resizeDuration, "swing")
            }
            setTimeout(function () {
                $lightbox.find(".lb-prevLink").height(newHeight);
                $lightbox.find(".lb-nextLink").height(newHeight);
                _this.showImage()
            }, this.options.resizeDuration)
        };
        Lightbox.prototype.showImage = function () {
            var $lightbox;
            $lightbox = $("#lightbox");
            $lightbox.find(".lb-loader").hide();
            $lightbox.find(".lb-image").fadeIn("slow");
            this.updateNav();
            this.updateDetails();
            this.preloadNeighboringImages();
            this.enableKeyboardNav()
        };
        Lightbox.prototype.updateNav = function () {
            var $lightbox;
            $lightbox = $("#lightbox");
            $lightbox.find(".lb-nav").show();
            if (this.currentImageIndex > 0) {
                $lightbox.find(".lb-prev").show()
            }
            if (this.currentImageIndex < this.album.length - 1) {
                $lightbox.find(".lb-next").show()
            }
        };
        Lightbox.prototype.updateDetails = function () {
            var $lightbox, _this = this;
            $lightbox = $("#lightbox");
            if (typeof this.album[this.currentImageIndex].title !== "undefined" && this.album[this.currentImageIndex].title !== "") {
                $lightbox.find("h4").html(this.setTitle(this.album[this.currentImageIndex].title, this.album[this.currentImageIndex].titleLink)).fadeIn("fast")
            }
            if (typeof this.album[this.currentImageIndex].description !== "undefined" && this.album[this.currentImageIndex].description !== "") {
                $lightbox.find(".lb-description").html(this.album[this.currentImageIndex].description).fadeIn("fast")
            }
            if (this.album.length > 1) {
                $lightbox.find(".lb-number").html(this.options.labelImage + " " + (this.currentImageIndex + 1) + " " + this.options.labelOf + "  " + this.album.length).fadeIn("fast")
            } else {
                $lightbox.find(".lb-number").hide()
            }
            $lightbox.find(".lb-outerContainer").removeClass("animating");
            $lightbox.find(".lb-dataContainer").fadeIn(this.resizeDuration, function () {
                return _this.sizeOverlay()
            })
        };
        Lightbox.prototype.preloadNeighboringImages = function () {
            var preloadNext, preloadPrev;
            if (this.album.length > this.currentImageIndex + 1) {
                preloadNext = new Image;
                preloadNext.src = this.album[this.currentImageIndex + 1].link
            }
            if (this.currentImageIndex > 0) {
                preloadPrev = new Image;
                preloadPrev.src = this.album[this.currentImageIndex - 1].link
            }
        };
        Lightbox.prototype.enableKeyboardNav = function () {
            $(document).on("keyup.keyboard", $.proxy(this.keyboardAction, this))
        };
        Lightbox.prototype.disableKeyboardNav = function () {
            $(document).off(".keyboard")
        };
        Lightbox.prototype.keyboardAction = function (event) {
            var KEYCODE_ESC, KEYCODE_LEFTARROW, KEYCODE_RIGHTARROW, key, keycode;
            KEYCODE_ESC = 27;
            KEYCODE_LEFTARROW = 37;
            KEYCODE_RIGHTARROW = 39;
            keycode = event.keyCode;
            key = String.fromCharCode(keycode).toLowerCase();
            if (keycode === KEYCODE_ESC || key.match(/x|o|c/)) {
                this.end()
            } else if (key === "p" || keycode === KEYCODE_LEFTARROW) {
                if (this.currentImageIndex !== 0) {
                    this.changeImage(this.currentImageIndex - 1)
                }
            } else if (key === "n" || keycode === KEYCODE_RIGHTARROW) {
                if (this.currentImageIndex !== this.album.length - 1) {
                    this.changeImage(this.currentImageIndex + 1)
                }
            }
        };
        Lightbox.prototype.end = function () {
            this.disableKeyboardNav();
            $(window).off("resize", this.sizeOverlay);
            $("#lightbox").fadeOut(this.options.fadeDuration);
            $("#lightboxOverlay").fadeOut(this.options.fadeDuration);
            return $("select, object, embed").css({visibility: "visible"})
        };
        return Lightbox
    }();
    $(function () {
        var lightbox, options;
        options = new LightboxOptions;
        return lightbox = new Lightbox(options)
    })
}).call(this);
