/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(riot) {__webpack_require__(2)
	var api = __webpack_require__(21)

	app = riot.mount('app', api)
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_RESULT__;/* Riot v2.2.2, @license MIT, (c) 2015 Muut Inc. + contributors */

	;(function(window, undefined) {
	  'use strict'
	  var riot = { version: 'v2.2.2', settings: {} }

	  // This globals 'const' helps code size reduction

	  // for typeof == '' comparisons
	  var T_STRING = 'string',
	      T_OBJECT = 'object',
	      T_UNDEF  = 'undefined'

	  // for IE8 and rest of the world
	  /* istanbul ignore next */
	  var isArray = Array.isArray || (function () {
	    var _ts = Object.prototype.toString
	    return function (v) { return _ts.call(v) === '[object Array]' }
	  })()

	  // Version# for IE 8-11, 0 for others
	  var ieVersion = (function (win) {
	    return (window && window.document || {}).documentMode | 0
	  })()

	riot.observable = function(el) {

	  el = el || {}

	  var callbacks = {},
	      _id = 0

	  el.on = function(events, fn) {
	    if (isFunction(fn)) {
	      if (typeof fn.id === T_UNDEF) fn._id = _id++

	      events.replace(/\S+/g, function(name, pos) {
	        (callbacks[name] = callbacks[name] || []).push(fn)
	        fn.typed = pos > 0
	      })
	    }
	    return el
	  }

	  el.off = function(events, fn) {
	    if (events == '*') callbacks = {}
	    else {
	      events.replace(/\S+/g, function(name) {
	        if (fn) {
	          var arr = callbacks[name]
	          for (var i = 0, cb; (cb = arr && arr[i]); ++i) {
	            if (cb._id == fn._id) arr.splice(i--, 1)
	          }
	        } else {
	          callbacks[name] = []
	        }
	      })
	    }
	    return el
	  }

	  // only single event supported
	  el.one = function(name, fn) {
	    function on() {
	      el.off(name, on)
	      fn.apply(el, arguments)
	    }
	    return el.on(name, on)
	  }

	  el.trigger = function(name) {
	    var args = [].slice.call(arguments, 1),
	        fns = callbacks[name] || []

	    for (var i = 0, fn; (fn = fns[i]); ++i) {
	      if (!fn.busy) {
	        fn.busy = 1
	        fn.apply(el, fn.typed ? [name].concat(args) : args)
	        if (fns[i] !== fn) { i-- }
	        fn.busy = 0
	      }
	    }

	    if (callbacks.all && name != 'all') {
	      el.trigger.apply(el, ['all', name].concat(args))
	    }

	    return el
	  }

	  return el

	}
	riot.mixin = (function() {
	  var mixins = {}

	  return function(name, mixin) {
	    if (!mixin) return mixins[name]
	    mixins[name] = mixin
	  }

	})()

	;(function(riot, evt, win) {

	  // browsers only
	  if (!win) return

	  var loc = win.location,
	      fns = riot.observable(),
	      started = false,
	      current

	  function hash() {
	    return loc.href.split('#')[1] || ''
	  }

	  function parser(path) {
	    return path.split('/')
	  }

	  function emit(path) {
	    if (path.type) path = hash()

	    if (path != current) {
	      fns.trigger.apply(null, ['H'].concat(parser(path)))
	      current = path
	    }
	  }

	  var r = riot.route = function(arg) {
	    // string
	    if (arg[0]) {
	      loc.hash = arg
	      emit(arg)

	    // function
	    } else {
	      fns.on('H', arg)
	    }
	  }

	  r.exec = function(fn) {
	    fn.apply(null, parser(hash()))
	  }

	  r.parser = function(fn) {
	    parser = fn
	  }

	  r.stop = function () {
	    if (!started) return
	    win.removeEventListener ? win.removeEventListener(evt, emit, false) : win.detachEvent('on' + evt, emit)
	    fns.off('*')
	    started = false
	  }

	  r.start = function () {
	    if (started) return
	    win.addEventListener ? win.addEventListener(evt, emit, false) : win.attachEvent('on' + evt, emit)
	    started = true
	  }

	  // autostart the router
	  r.start()

	})(riot, 'hashchange', window)
	/*

	//// How it works?


	Three ways:

	1. Expressions: tmpl('{ value }', data).
	   Returns the result of evaluated expression as a raw object.

	2. Templates: tmpl('Hi { name } { surname }', data).
	   Returns a string with evaluated expressions.

	3. Filters: tmpl('{ show: !done, highlight: active }', data).
	   Returns a space separated list of trueish keys (mainly
	   used for setting html classes), e.g. "show highlight".


	// Template examples

	tmpl('{ title || "Untitled" }', data)
	tmpl('Results are { results ? "ready" : "loading" }', data)
	tmpl('Today is { new Date() }', data)
	tmpl('{ message.length > 140 && "Message is too long" }', data)
	tmpl('This item got { Math.round(rating) } stars', data)
	tmpl('<h1>{ title }</h1>{ body }', data)


	// Falsy expressions in templates

	In templates (as opposed to single expressions) all falsy values
	except zero (undefined/null/false) will default to empty string:

	tmpl('{ undefined } - { false } - { null } - { 0 }', {})
	// will return: " - - - 0"

	*/


	var brackets = (function(orig) {

	  var cachedBrackets,
	      r,
	      b,
	      re = /[{}]/g

	  return function(x) {

	    // make sure we use the current setting
	    var s = riot.settings.brackets || orig

	    // recreate cached vars if needed
	    if (cachedBrackets !== s) {
	      cachedBrackets = s
	      b = s.split(' ')
	      r = b.map(function (e) { return e.replace(/(?=.)/g, '\\') })
	    }

	    // if regexp given, rewrite it with current brackets (only if differ from default)
	    return x instanceof RegExp ? (
	        s === orig ? x :
	        new RegExp(x.source.replace(re, function(b) { return r[~~(b === '}')] }), x.global ? 'g' : '')
	      ) :
	      // else, get specific bracket
	      b[x]
	  }
	})('{ }')


	var tmpl = (function() {

	  var cache = {},
	      reVars = /(['"\/]).*?[^\\]\1|\.\w*|\w*:|\b(?:(?:new|typeof|in|instanceof) |(?:this|true|false|null|undefined)\b|function *\()|([a-z_$]\w*)/gi
	              // [ 1               ][ 2  ][ 3 ][ 4                                                                                  ][ 5       ]
	              // find variable names:
	              // 1. skip quoted strings and regexps: "a b", 'a b', 'a \'b\'', /a b/
	              // 2. skip object properties: .name
	              // 3. skip object literals: name:
	              // 4. skip javascript keywords
	              // 5. match var name

	  // build a template (or get it from cache), render with data
	  return function(str, data) {
	    return str && (cache[str] = cache[str] || tmpl(str))(data)
	  }


	  // create a template instance

	  function tmpl(s, p) {

	    // default template string to {}
	    s = (s || (brackets(0) + brackets(1)))

	      // temporarily convert \{ and \} to a non-character
	      .replace(brackets(/\\{/g), '\uFFF0')
	      .replace(brackets(/\\}/g), '\uFFF1')

	    // split string to expression and non-expresion parts
	    p = split(s, extract(s, brackets(/{/), brackets(/}/)))

	    return new Function('d', 'return ' + (

	      // is it a single expression or a template? i.e. {x} or <b>{x}</b>
	      !p[0] && !p[2] && !p[3]

	        // if expression, evaluate it
	        ? expr(p[1])

	        // if template, evaluate all expressions in it
	        : '[' + p.map(function(s, i) {

	            // is it an expression or a string (every second part is an expression)
	          return i % 2

	              // evaluate the expressions
	              ? expr(s, true)

	              // process string parts of the template:
	              : '"' + s

	                  // preserve new lines
	                  .replace(/\n/g, '\\n')

	                  // escape quotes
	                  .replace(/"/g, '\\"')

	                + '"'

	        }).join(',') + '].join("")'
	      )

	      // bring escaped { and } back
	      .replace(/\uFFF0/g, brackets(0))
	      .replace(/\uFFF1/g, brackets(1))

	    + ';')

	  }


	  // parse { ... } expression

	  function expr(s, n) {
	    s = s

	      // convert new lines to spaces
	      .replace(/\n/g, ' ')

	      // trim whitespace, brackets, strip comments
	      .replace(brackets(/^[{ ]+|[ }]+$|\/\*.+?\*\//g), '')

	    // is it an object literal? i.e. { key : value }
	    return /^\s*[\w- "']+ *:/.test(s)

	      // if object literal, return trueish keys
	      // e.g.: { show: isOpen(), done: item.done } -> "show done"
	      ? '[' +

	          // extract key:val pairs, ignoring any nested objects
	          extract(s,

	              // name part: name:, "name":, 'name':, name :
	              /["' ]*[\w- ]+["' ]*:/,

	              // expression part: everything upto a comma followed by a name (see above) or end of line
	              /,(?=["' ]*[\w- ]+["' ]*:)|}|$/
	              ).map(function(pair) {

	                // get key, val parts
	                return pair.replace(/^[ "']*(.+?)[ "']*: *(.+?),? *$/, function(_, k, v) {

	                  // wrap all conditional parts to ignore errors
	                  return v.replace(/[^&|=!><]+/g, wrap) + '?"' + k + '":"",'

	                })

	              }).join('')

	        + '].join(" ").trim()'

	      // if js expression, evaluate as javascript
	      : wrap(s, n)

	  }


	  // execute js w/o breaking on errors or undefined vars

	  function wrap(s, nonull) {
	    s = s.trim()
	    return !s ? '' : '(function(v){try{v='

	        // prefix vars (name => data.name)
	        + (s.replace(reVars, function(s, _, v) { return v ? '(d.'+v+'===undefined?'+(typeof window == 'undefined' ? 'global.' : 'window.')+v+':d.'+v+')' : s })

	          // break the expression if its empty (resulting in undefined value)
	          || 'x')
	      + '}catch(e){'
	      + '}finally{return '

	        // default to empty string for falsy values except zero
	        + (nonull === true ? '!v&&v!==0?"":v' : 'v')

	      + '}}).call(d)'
	  }


	  // split string by an array of substrings

	  function split(str, substrings) {
	    var parts = []
	    substrings.map(function(sub, i) {

	      // push matched expression and part before it
	      i = str.indexOf(sub)
	      parts.push(str.slice(0, i), sub)
	      str = str.slice(i + sub.length)
	    })

	    // push the remaining part
	    return parts.concat(str)
	  }


	  // match strings between opening and closing regexp, skipping any inner/nested matches

	  function extract(str, open, close) {

	    var start,
	        level = 0,
	        matches = [],
	        re = new RegExp('('+open.source+')|('+close.source+')', 'g')

	    str.replace(re, function(_, open, close, pos) {

	      // if outer inner bracket, mark position
	      if (!level && open) start = pos

	      // in(de)crease bracket level
	      level += open ? 1 : -1

	      // if outer closing bracket, grab the match
	      if (!level && close != null) matches.push(str.slice(start, pos+close.length))

	    })

	    return matches
	  }

	})()

	// { key, i in items} -> { key, i, items }
	function loopKeys(expr) {
	  var b0 = brackets(0),
	      els = expr.slice(b0.length).match(/^\s*(\S+?)\s*(?:,\s*(\S+))?\s+in\s+(.+)$/)
	  return els ? { key: els[1], pos: els[2], val: b0 + els[3] } : { val: expr }
	}

	function mkitem(expr, key, val) {
	  var item = {}
	  item[expr.key] = key
	  if (expr.pos) item[expr.pos] = val
	  return item
	}


	/* Beware: heavy stuff */
	function _each(dom, parent, expr) {

	  remAttr(dom, 'each')

	  var tagName = getTagName(dom),
	      template = dom.outerHTML,
	      hasImpl = !!tagImpl[tagName],
	      impl = tagImpl[tagName] || {
	        tmpl: template
	      },
	      root = dom.parentNode,
	      placeholder = document.createComment('riot placeholder'),
	      tags = [],
	      child = getTag(dom),
	      checksum

	  root.insertBefore(placeholder, dom)

	  expr = loopKeys(expr)

	  // clean template code
	  parent
	    .one('premount', function () {
	      if (root.stub) root = parent.root
	      // remove the original DOM node
	      dom.parentNode.removeChild(dom)
	    })
	    .on('update', function () {
	      var items = tmpl(expr.val, parent)

	      // object loop. any changes cause full redraw
	      if (!isArray(items)) {

	        checksum = items ? JSON.stringify(items) : ''

	        items = !items ? [] :
	          Object.keys(items).map(function (key) {
	            return mkitem(expr, key, items[key])
	          })
	      }

	      var frag = document.createDocumentFragment(),
	          i = tags.length,
	          j = items.length

	      // unmount leftover items
	      while (i > j) {
	        tags[--i].unmount()
	        tags.splice(i, 1)
	      }

	      for (i = 0; i < j; ++i) {
	        var _item = !checksum && !!expr.key ? mkitem(expr, items[i], i) : items[i]

	        if (!tags[i]) {
	          // mount new
	          (tags[i] = new Tag(impl, {
	              parent: parent,
	              isLoop: true,
	              hasImpl: hasImpl,
	              root: hasImpl ? dom.cloneNode() : root,
	              item: _item
	            }, dom.innerHTML)
	          ).mount()

	          frag.appendChild(tags[i].root)
	        } else
	          tags[i].update(_item)

	        tags[i]._item = _item

	      }

	      root.insertBefore(frag, placeholder)

	      if (child) parent.tags[tagName] = tags

	    }).one('updated', function() {
	      var keys = Object.keys(parent)// only set new values
	      walk(root, function(node) {
	        // only set element node and not isLoop
	        if (node.nodeType == 1 && !node.isLoop && !node._looped) {
	          node._visited = false // reset _visited for loop node
	          node._looped = true // avoid set multiple each
	          setNamed(node, parent, keys)
	        }
	      })
	    })

	}


	function parseNamedElements(root, parent, childTags) {

	  walk(root, function(dom) {
	    if (dom.nodeType == 1) {
	      dom.isLoop = dom.isLoop || (dom.parentNode && dom.parentNode.isLoop || dom.getAttribute('each')) ? 1 : 0

	      // custom child tag
	      var child = getTag(dom)

	      if (child && !dom.isLoop) {
	        var tag = new Tag(child, { root: dom, parent: parent }, dom.innerHTML),
	            tagName = getTagName(dom),
	            ptag = parent,
	            cachedTag

	        while (!getTag(ptag.root)) {
	          if (!ptag.parent) break
	          ptag = ptag.parent
	        }

	        // fix for the parent attribute in the looped elements
	        tag.parent = ptag

	        cachedTag = ptag.tags[tagName]

	        // if there are multiple children tags having the same name
	        if (cachedTag) {
	          // if the parent tags property is not yet an array
	          // create it adding the first cached tag
	          if (!isArray(cachedTag))
	            ptag.tags[tagName] = [cachedTag]
	          // add the new nested tag to the array
	          ptag.tags[tagName].push(tag)
	        } else {
	          ptag.tags[tagName] = tag
	        }

	        // empty the child node once we got its template
	        // to avoid that its children get compiled multiple times
	        dom.innerHTML = ''
	        childTags.push(tag)
	      }

	      if (!dom.isLoop)
	        setNamed(dom, parent, [])
	    }

	  })

	}

	function parseExpressions(root, tag, expressions) {

	  function addExpr(dom, val, extra) {
	    if (val.indexOf(brackets(0)) >= 0) {
	      var expr = { dom: dom, expr: val }
	      expressions.push(extend(expr, extra))
	    }
	  }

	  walk(root, function(dom) {
	    var type = dom.nodeType

	    // text node
	    if (type == 3 && dom.parentNode.tagName != 'STYLE') addExpr(dom, dom.nodeValue)
	    if (type != 1) return

	    /* element */

	    // loop
	    var attr = dom.getAttribute('each')

	    if (attr) { _each(dom, tag, attr); return false }

	    // attribute expressions
	    each(dom.attributes, function(attr) {
	      var name = attr.name,
	        bool = name.split('__')[1]

	      addExpr(dom, attr.value, { attr: bool || name, bool: bool })
	      if (bool) { remAttr(dom, name); return false }

	    })

	    // skip custom tags
	    if (getTag(dom)) return false

	  })

	}
	function Tag(impl, conf, innerHTML) {

	  var self = riot.observable(this),
	      opts = inherit(conf.opts) || {},
	      dom = mkdom(impl.tmpl),
	      parent = conf.parent,
	      isLoop = conf.isLoop,
	      hasImpl = conf.hasImpl,
	      item = cleanUpData(conf.item),
	      expressions = [],
	      childTags = [],
	      root = conf.root,
	      fn = impl.fn,
	      tagName = root.tagName.toLowerCase(),
	      attr = {},
	      propsInSyncWithParent = [],
	      loopDom,
	      TAG_ATTRIBUTES = /([\w\-]+)\s?=\s?['"]([^'"]+)["']/gim


	  if (fn && root._tag) {
	    root._tag.unmount(true)
	  }

	  // not yet mounted
	  this.isMounted = false
	  root.isLoop = isLoop

	  if (impl.attrs) {
	    var attrs = impl.attrs.match(TAG_ATTRIBUTES)

	    each(attrs, function(a) {
	      var kv = a.split(/\s?=\s?/)
	      root.setAttribute(kv[0], kv[1].replace(/['"]/g, ''))
	    })

	  }

	  // keep a reference to the tag just created
	  // so we will be able to mount this tag multiple times
	  root._tag = this

	  // create a unique id to this tag
	  // it could be handy to use it also to improve the virtual dom rendering speed
	  this._id = fastAbs(~~(new Date().getTime() * Math.random()))

	  extend(this, { parent: parent, root: root, opts: opts, tags: {} }, item)

	  // grab attributes
	  each(root.attributes, function(el) {
	    var val = el.value
	    // remember attributes with expressions only
	    if (brackets(/\{.*\}/).test(val)) attr[el.name] = val
	  })

	  if (dom.innerHTML && !/select|select|optgroup|tbody|tr/.test(tagName))
	    // replace all the yield tags with the tag inner html
	    dom.innerHTML = replaceYield(dom.innerHTML, innerHTML)

	  // options
	  function updateOpts() {
	    var ctx = hasImpl && isLoop ? self : parent || self
	    // update opts from current DOM attributes
	    each(root.attributes, function(el) {
	      opts[el.name] = tmpl(el.value, ctx)
	    })
	    // recover those with expressions
	    each(Object.keys(attr), function(name) {
	      opts[name] = tmpl(attr[name], ctx)
	    })
	  }

	  function normalizeData(data) {
	    for (var key in item) {
	      if (typeof self[key] !== T_UNDEF)
	        self[key] = data[key]
	    }
	  }

	  function inheritFromParent () {
	    if (!self.parent || !isLoop) return
	    each(Object.keys(self.parent), function(k) {
	      // some properties must be always in sync with the parent tag
	      var mustSync = ~propsInSyncWithParent.indexOf(k)
	      if (typeof self[k] === T_UNDEF || mustSync) {
	        // track the property to keep in sync
	        // so we can keep it updated
	        if (!mustSync) propsInSyncWithParent.push(k)
	        self[k] = self.parent[k]
	      }
	    })
	  }

	  this.update = function(data) {
	    // make sure the data passed will not override
	    // the component core methods
	    data = cleanUpData(data)
	    // inherit properties from the parent
	    inheritFromParent()
	    // normalize the tag properties in case an item object was initially passed
	    if (typeof item === T_OBJECT || isArray(item)) {
	      normalizeData(data)
	      item = data
	    }
	    extend(self, data)
	    updateOpts()
	    self.trigger('update', data)
	    update(expressions, self)
	    self.trigger('updated')
	  }

	  this.mixin = function() {
	    each(arguments, function(mix) {
	      mix = typeof mix === T_STRING ? riot.mixin(mix) : mix
	      each(Object.keys(mix), function(key) {
	        // bind methods to self
	        if (key != 'init')
	          self[key] = isFunction(mix[key]) ? mix[key].bind(self) : mix[key]
	      })
	      // init method will be called automatically
	      if (mix.init) mix.init.bind(self)()
	    })
	  }

	  this.mount = function() {

	    updateOpts()

	    // initialiation
	    fn && fn.call(self, opts)

	    toggle(true)


	    // parse layout after init. fn may calculate args for nested custom tags
	    parseExpressions(dom, self, expressions)
	    if (!self.parent || hasImpl) parseExpressions(self.root, self, expressions) // top level before update, empty root

	    if (!self.parent || isLoop) self.update(item)

	    // internal use only, fixes #403
	    self.trigger('premount')

	    if (isLoop && !hasImpl) {
	      // update the root attribute for the looped elements
	      self.root = root = loopDom = dom.firstChild

	    } else {
	      while (dom.firstChild) root.appendChild(dom.firstChild)
	      if (root.stub) self.root = root = parent.root
	    }
	    // if it's not a child tag we can trigger its mount event
	    if (!self.parent || self.parent.isMounted) {
	      self.isMounted = true
	      self.trigger('mount')
	    }
	    // otherwise we need to wait that the parent event gets triggered
	    else self.parent.one('mount', function() {
	      // avoid to trigger the `mount` event for the tags
	      // not visible included in an if statement
	      if (!isInStub(self.root)) {
	        self.parent.isMounted = self.isMounted = true
	        self.trigger('mount')
	      }
	    })
	  }


	  this.unmount = function(keepRootTag) {
	    var el = loopDom || root,
	        p = el.parentNode

	    if (p) {

	      if (parent)
	        // remove this tag from the parent tags object
	        // if there are multiple nested tags with same name..
	        // remove this element form the array
	        if (isArray(parent.tags[tagName]))
	          each(parent.tags[tagName], function(tag, i) {
	            if (tag._id == self._id)
	              parent.tags[tagName].splice(i, 1)
	          })
	        else
	          // otherwise just delete the tag instance
	          parent.tags[tagName] = undefined
	      else
	        while (el.firstChild) el.removeChild(el.firstChild)

	      if (!keepRootTag)
	        p.removeChild(el)

	    }


	    self.trigger('unmount')
	    toggle()
	    self.off('*')
	    // somehow ie8 does not like `delete root._tag`
	    root._tag = null

	  }

	  function toggle(isMount) {

	    // mount/unmount children
	    each(childTags, function(child) { child[isMount ? 'mount' : 'unmount']() })

	    // listen/unlisten parent (events flow one way from parent to children)
	    if (parent) {
	      var evt = isMount ? 'on' : 'off'

	      // the loop tags will be always in sync with the parent automatically
	      if (isLoop)
	        parent[evt]('unmount', self.unmount)
	      else
	        parent[evt]('update', self.update)[evt]('unmount', self.unmount)
	    }
	  }

	  // named elements available for fn
	  parseNamedElements(dom, this, childTags)


	}

	function setEventHandler(name, handler, dom, tag) {

	  dom[name] = function(e) {

	    var item = tag._item,
	        ptag = tag.parent

	    if (!item)
	      while (ptag) {
	        item = ptag._item
	        ptag = item ? false : ptag.parent
	      }

	    // cross browser event fix
	    e = e || window.event

	    // ignore error on some browsers
	    try {
	      e.currentTarget = dom
	      if (!e.target) e.target = e.srcElement
	      if (!e.which) e.which = e.charCode || e.keyCode
	    } catch (ignored) { '' }

	    e.item = item

	    // prevent default behaviour (by default)
	    if (handler.call(tag, e) !== true && !/radio|check/.test(dom.type)) {
	      e.preventDefault && e.preventDefault()
	      e.returnValue = false
	    }

	    if (!e.preventUpdate) {
	      var el = item ? tag.parent : tag
	      el.update()
	    }

	  }

	}

	// used by if- attribute
	function insertTo(root, node, before) {
	  if (root) {
	    root.insertBefore(before, node)
	    root.removeChild(node)
	  }
	}

	function update(expressions, tag) {

	  each(expressions, function(expr, i) {

	    var dom = expr.dom,
	        attrName = expr.attr,
	        value = tmpl(expr.expr, tag),
	        parent = expr.dom.parentNode

	    if (value == null) value = ''

	    // leave out riot- prefixes from strings inside textarea
	    if (parent && parent.tagName == 'TEXTAREA') value = value.replace(/riot-/g, '')

	    // no change
	    if (expr.value === value) return
	    expr.value = value

	    // text node
	    if (!attrName) return dom.nodeValue = value.toString()

	    // remove original attribute
	    remAttr(dom, attrName)

	    // event handler
	    if (isFunction(value)) {
	      setEventHandler(attrName, value, dom, tag)

	    // if- conditional
	    } else if (attrName == 'if') {
	      var stub = expr.stub

	      // add to DOM
	      if (value) {
	        if (stub) {
	          insertTo(stub.parentNode, stub, dom)
	          dom.inStub = false
	          // avoid to trigger the mount event if the tags is not visible yet
	          // maybe we can optimize this avoiding to mount the tag at all
	          if (!isInStub(dom)) {
	            walk(dom, function(el) {
	              if (el._tag && !el._tag.isMounted) el._tag.isMounted = !!el._tag.trigger('mount')
	            })
	          }
	        }
	      // remove from DOM
	      } else {
	        stub = expr.stub = stub || document.createTextNode('')
	        insertTo(dom.parentNode, dom, stub)
	        dom.inStub = true
	      }
	    // show / hide
	    } else if (/^(show|hide)$/.test(attrName)) {
	      if (attrName == 'hide') value = !value
	      dom.style.display = value ? '' : 'none'

	    // field value
	    } else if (attrName == 'value') {
	      dom.value = value

	    // <img src="{ expr }">
	    } else if (attrName.slice(0, 5) == 'riot-' && attrName != 'riot-tag') {
	      attrName = attrName.slice(5)
	      value ? dom.setAttribute(attrName, value) : remAttr(dom, attrName)

	    } else {
	      if (expr.bool) {
	        dom[attrName] = value
	        if (!value) return
	        value = attrName
	      }

	      if (typeof value !== T_OBJECT) dom.setAttribute(attrName, value)

	    }

	  })

	}

	function each(els, fn) {
	  for (var i = 0, len = (els || []).length, el; i < len; i++) {
	    el = els[i]
	    // return false -> remove current item during loop
	    if (el != null && fn(el, i) === false) i--
	  }
	  return els
	}

	function isFunction(v) {
	  return typeof v === 'function' || false   // avoid IE problems
	}

	function remAttr(dom, name) {
	  dom.removeAttribute(name)
	}

	function fastAbs(nr) {
	  return (nr ^ (nr >> 31)) - (nr >> 31)
	}

	function getTag(dom) {
	  var tagName = dom.tagName.toLowerCase()
	  return tagImpl[dom.getAttribute(RIOT_TAG) || tagName]
	}

	function getTagName(dom) {
	  var child = getTag(dom),
	    namedTag = dom.getAttribute('name'),
	    tagName = namedTag && namedTag.indexOf(brackets(0)) < 0 ? namedTag : child ? child.name : dom.tagName.toLowerCase()

	  return tagName
	}

	function extend(src) {
	  var obj, args = arguments
	  for (var i = 1; i < args.length; ++i) {
	    if ((obj = args[i])) {
	      for (var key in obj) {      // eslint-disable-line guard-for-in
	        src[key] = obj[key]
	      }
	    }
	  }
	  return src
	}

	// with this function we avoid that the current Tag methods get overridden
	function cleanUpData(data) {
	  if (!(data instanceof Tag)) return data

	  var o = {},
	      blackList = ['update', 'root', 'mount', 'unmount', 'mixin', 'isMounted', 'isloop', 'tags', 'parent', 'opts']
	  for (var key in data) {
	    if (!~blackList.indexOf(key))
	      o[key] = data[key]
	  }
	  return o
	}

	function mkdom(template) {
	  var checkie = ieVersion && ieVersion < 10,
	      matches = /^\s*<([\w-]+)/.exec(template),
	      tagName = matches ? matches[1].toLowerCase() : '',
	      rootTag = (tagName === 'th' || tagName === 'td') ? 'tr' :
	                (tagName === 'tr' ? 'tbody' : 'div'),
	      el = mkEl(rootTag)

	  el.stub = true

	  if (checkie) {
	    if (tagName === 'optgroup')
	      optgroupInnerHTML(el, template)
	    else if (tagName === 'option')
	      optionInnerHTML(el, template)
	    else if (rootTag !== 'div')
	      tbodyInnerHTML(el, template, tagName)
	    else
	      checkie = 0
	  }
	  if (!checkie) el.innerHTML = template

	  return el
	}

	function walk(dom, fn) {
	  if (dom) {
	    if (fn(dom) === false) walk(dom.nextSibling, fn)
	    else {
	      dom = dom.firstChild

	      while (dom) {
	        walk(dom, fn)
	        dom = dom.nextSibling
	      }
	    }
	  }
	}

	function isInStub(dom) {
	  while (dom) {
	    if (dom.inStub) return true
	    dom = dom.parentNode
	  }
	  return false
	}

	function mkEl(name) {
	  return document.createElement(name)
	}

	function replaceYield (tmpl, innerHTML) {
	  return tmpl.replace(/<(yield)\/?>(<\/\1>)?/gim, innerHTML || '')
	}

	function $$(selector, ctx) {
	  return (ctx || document).querySelectorAll(selector)
	}

	function $(selector, ctx) {
	  return (ctx || document).querySelector(selector)
	}

	function inherit(parent) {
	  function Child() {}
	  Child.prototype = parent
	  return new Child()
	}

	function setNamed(dom, parent, keys) {
	  each(dom.attributes, function(attr) {
	    if (dom._visited) return
	    if (attr.name === 'id' || attr.name === 'name') {
	      dom._visited = true
	      var p, v = attr.value
	      if (~keys.indexOf(v)) return

	      p = parent[v]
	      if (!p)
	        parent[v] = dom
	      else
	        isArray(p) ? p.push(dom) : (parent[v] = [p, dom])
	    }
	  })
	}
	/**
	 *
	 * Hacks needed for the old internet explorer versions [lower than IE10]
	 *
	 */
	/* istanbul ignore next */
	function tbodyInnerHTML(el, html, tagName) {
	  var div = mkEl('div'),
	      loops = /td|th/.test(tagName) ? 3 : 2,
	      child

	  div.innerHTML = '<table>' + html + '</table>'
	  child = div.firstChild

	  while (loops--) child = child.firstChild

	  el.appendChild(child)

	}
	/* istanbul ignore next */
	function optionInnerHTML(el, html) {
	  var opt = mkEl('option'),
	      valRegx = /value=[\"'](.+?)[\"']/,
	      selRegx = /selected=[\"'](.+?)[\"']/,
	      eachRegx = /each=[\"'](.+?)[\"']/,
	      ifRegx = /if=[\"'](.+?)[\"']/,
	      innerRegx = />([^<]*)</,
	      valuesMatch = html.match(valRegx),
	      selectedMatch = html.match(selRegx),
	      innerValue = html.match(innerRegx),
	      eachMatch = html.match(eachRegx),
	      ifMatch = html.match(ifRegx)

	  if (innerValue) opt.innerHTML = innerValue[1]
	  else opt.innerHTML = html

	  if (valuesMatch) opt.value = valuesMatch[1]
	  if (selectedMatch) opt.setAttribute('riot-selected', selectedMatch[1])
	  if (eachMatch) opt.setAttribute('each', eachMatch[1])
	  if (ifMatch) opt.setAttribute('if', ifMatch[1])

	  el.appendChild(opt)
	}
	/* istanbul ignore next */
	function optgroupInnerHTML(el, html) {
	  var opt = mkEl('optgroup'),
	      labelRegx = /label=[\"'](.+?)[\"']/,
	      elementRegx = /^<([^>]*)>/,
	      tagRegx = /^<([^ \>]*)/,
	      labelMatch = html.match(labelRegx),
	      elementMatch = html.match(elementRegx),
	      tagMatch = html.match(tagRegx),
	      innerContent = html

	  if (elementMatch) {
	    var options = html.slice(elementMatch[1].length+2, -tagMatch[1].length-3).trim()
	    innerContent = options
	  }

	  if (labelMatch) opt.setAttribute('riot-label', labelMatch[1])

	  if (innerContent) {
	    var innerOpt = mkEl('div')

	    optionInnerHTML(innerOpt, innerContent)

	    opt.appendChild(innerOpt.firstChild)
	  }

	  el.appendChild(opt)
	}

	/*
	 Virtual dom is an array of custom tags on the document.
	 Updates and unmounts propagate downwards from parent to children.
	*/

	var virtualDom = [],
	    tagImpl = {},
	    styleNode

	var RIOT_TAG = 'riot-tag'

	function injectStyle(css) {

	  styleNode = styleNode || mkEl('style')

	  if (!document.head) return

	  if (styleNode.styleSheet)
	    styleNode.styleSheet.cssText += css
	  else
	    styleNode.innerHTML += css

	  if (!styleNode._rendered)
	    if (styleNode.styleSheet) {
	      document.body.appendChild(styleNode)
	    } else {
	      var rs = $('style[type=riot]')
	      if (rs) {
	        rs.parentNode.insertBefore(styleNode, rs)
	        rs.parentNode.removeChild(rs)
	      } else document.head.appendChild(styleNode)

	    }

	  styleNode._rendered = true

	}

	function mountTo(root, tagName, opts) {
	  var tag = tagImpl[tagName],
	      // cache the inner HTML to fix #855
	      innerHTML = root._innerHTML = root._innerHTML || root.innerHTML

	  // clear the inner html
	  root.innerHTML = ''

	  if (tag && root) tag = new Tag(tag, { root: root, opts: opts }, innerHTML)

	  if (tag && tag.mount) {
	    tag.mount()
	    virtualDom.push(tag)
	    return tag.on('unmount', function() {
	      virtualDom.splice(virtualDom.indexOf(tag), 1)
	    })
	  }

	}

	riot.tag = function(name, html, css, attrs, fn) {
	  if (isFunction(attrs)) {
	    fn = attrs
	    if (/^[\w\-]+\s?=/.test(css)) {
	      attrs = css
	      css = ''
	    } else attrs = ''
	  }
	  if (css) {
	    if (isFunction(css)) fn = css
	    else injectStyle(css)
	  }
	  tagImpl[name] = { name: name, tmpl: html, attrs: attrs, fn: fn }
	  return name
	}

	riot.mount = function(selector, tagName, opts) {

	  var els,
	      allTags,
	      tags = []

	  // helper functions

	  function addRiotTags(arr) {
	    var list = ''
	    each(arr, function (e) {
	      list += ', *[riot-tag="'+ e.trim() + '"]'
	    })
	    return list
	  }

	  function selectAllTags() {
	    var keys = Object.keys(tagImpl)
	    return keys + addRiotTags(keys)
	  }

	  function pushTags(root) {
	    if (root.tagName) {
	      if (tagName && !root.getAttribute(RIOT_TAG))
	        root.setAttribute(RIOT_TAG, tagName)

	      var tag = mountTo(root,
	        tagName || root.getAttribute(RIOT_TAG) || root.tagName.toLowerCase(), opts)

	      if (tag) tags.push(tag)
	    }
	    else if (root.length) {
	      each(root, pushTags)   // assume nodeList
	    }
	  }

	  // ----- mount code -----

	  if (typeof tagName === T_OBJECT) {
	    opts = tagName
	    tagName = 0
	  }

	  // crawl the DOM to find the tag
	  if (typeof selector === T_STRING) {
	    if (selector === '*')
	      // select all the tags registered
	      // and also the tags found with the riot-tag attribute set
	      selector = allTags = selectAllTags()
	    else
	      // or just the ones named like the selector
	      selector += addRiotTags(selector.split(','))

	    els = $$(selector)
	  }
	  else
	    // probably you have passed already a tag or a NodeList
	    els = selector

	  // select all the registered and mount them inside their root elements
	  if (tagName === '*') {
	    // get all custom tags
	    tagName = allTags || selectAllTags()
	    // if the root els it's just a single tag
	    if (els.tagName)
	      els = $$(tagName, els)
	    else {
	      // select all the children for all the different root elements
	      var nodeList = []
	      each(els, function (_el) {
	        nodeList.push($$(tagName, _el))
	      })
	      els = nodeList
	    }
	    // get rid of the tagName
	    tagName = 0
	  }

	  if (els.tagName)
	    pushTags(els)
	  else
	    each(els, pushTags)

	  return tags
	}

	// update everything
	riot.update = function() {
	  return each(virtualDom, function(tag) {
	    tag.update()
	  })
	}

	// @deprecated
	riot.mountTo = riot.mount


	  // share methods for other riot parts, e.g. compiler
	  riot.util = { brackets: brackets, tmpl: tmpl }

	  // support CommonJS, AMD & browser
	  /* istanbul ignore next */
	  if (typeof exports === T_OBJECT)
	    module.exports = riot
	  else if (true)
	    !(__WEBPACK_AMD_DEFINE_RESULT__ = function() { return window.riot = riot }.call(exports, __webpack_require__, exports, module), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__))
	  else
	    window.riot = riot

	})(typeof window != 'undefined' ? window : undefined);


/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	__webpack_require__(3)
	__webpack_require__(4)
	__webpack_require__(5)
	__webpack_require__(6)
	__webpack_require__(7)
	__webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"mousetrap\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()))
	__webpack_require__(8)
	__webpack_require__(9)
	__webpack_require__(10)
	__webpack_require__(13)
	__webpack_require__(14)
	__webpack_require__(15)
	__webpack_require__(16)
	__webpack_require__(17)
	__webpack_require__(18)


	riot.tag('app', '<modal presentation="{ currentPresentation }" categories="{ summit.categories }" api="{ this.opts }"></modal> <navbar admin="{ summit.track_chair.is_admin }"></navbar> <div class="container-fluid"> <rg-toast toasts="{ toasts }" position="bottomright"></rg-toast>  <div show="{ DisplayMode === \'tutorial\' }"> <tutorial></tutorial> </div>  <div show="{ DisplayMode === \'directory\' }"> <chairdirectory chairs="{ summit.chair_list }"></chairdirectory> </div>  <div show="{ DisplayMode === \'selections\' }"> <selection-manager categories="{ summit.categories }" api="{ this.opts }"></selection-manager> </div>   <div show="{ DisplayMode === \'requests\' }"> <change-requests api="{ this.opts }"></change-requests> </div>   <div show="{ DisplayMode === \'browse\' }" class="row"> <div class="{ col-lg-4: details } { col-lg-12: !details }"> <div class="well well-sm"> <h4>{ summit.title } Presentation Submissions</h4> <hr> <div class="input-group"> <span class="input-group-addon" id="sizing-addon2"> <i if="{ !searchmode }" class="fa fa-search"></i> <i if="{ searchmode }" onclick="{ clearSearch }" class="fa fa-times"></i> </span> <form onsubmit="{search}"> <input type="text" id="app-search" class="form-control" placeholder="search..." aria-describedby="sizing-addon2"> </form> </div> </div> <categorymenu categories="{ summit.categories }" active="{ activeCategory }" if="{ !searchmode }"></categorymenu> <div if="{ searchmode && quantity }">Showing { quantity } results</div> <div if="{ quantity }" class="list-group" id="presentation-list"> <div class="row" show="{ !details }"> <div class="col-lg-9 col-md-9 hidden-sm hidden-xs"> &nbsp; </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" > Ave </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs"> Count </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs"> Total </div> </div>  <div class="presentation-list" id="presentation-list"> <presentationitem each="{ presentation, i in presentations }" activekey="{ activekey }" key="{ i }" data="{ presentation }" details="{details}"></presentationitem> </div> </div> <div if="{ !quantity && searchmode }">No results were found</div> </div> <div class="col-lg-8" show="{ details }"> <div class="panel panel-default" name="presentation-details"> <div class="panel-heading"> <h3 class="panel-title">Presentation Details <a href="#" onclick="{ closeDetails }"><i class="fa fa-times pull-right"></i></a></h3> </div> <div class="panel-body">  <div class="row"> <div class="col-lg-6"> <strong>Category:</strong> { currentPresentation.category_name } <br><a data-toggle="modal" data-target="#myModal" href="#"><i class="fa fa-random"></i>&nbsp;Suggest Category Change</a> </div> <div class="col-lg-6"> <div class="btn-group pull-right" role="group" >  <button if="{ currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ unselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> My List</button> <button if="{ !currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ selectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> My List</button>  <button if="{ currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupUnselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> Team List</button> <button if="{ !currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupSelectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> Team List</button> </div> </div> </div> <hr> <h2>{ currentPresentation.title }</h2> <h4>{ currentPresentation.level }</h4> <hr> <span class="label label-primary">Vote Count <span class="badge">{ currentPresentation.vote_count }</span></span> <span class="label label-primary">Vote Ave <span class="badge">{ currentPresentation.vote_average }</span></span> <span class="label label-primary">Vote Total <span class="badge">{ currentPresentation.total_points }</span></span> <span class="label label-info" show="{currentPresentation.comments.length}">Chair Comments: { currentPresentation.comments.length }</span> <hr> <raw content="{ currentPresentation.description }"></raw> <div each="{ currentPresentation.speakers }"> <hr> <h4>{ first_name }&nbsp;{ last_name }</h5> <p>{ title }</p> <raw content="{ bio }"></raw> </div> <div> </div> <div if="{ currentPresentation.comments[0] }"> <hr> <h4>Comments</h4> <comment each="{ currentPresentation.comments }"></comment> <hr> </div> <addcommentform api="{ this.opts }" presentation="{ currentPresentation }"></addcommentform> </div> </div> </div> </div>  </div>', function(opts) {

			var self = this
			this.sortitems = []
			this.DisplayMode = 'browse'

			this.toasts = [];

			this.indexOf = function(id) {
	            var i = -1, index = -1

	            for(i = 0; i < self.presentations.length; i++) {
	                if(self.presentations[i].id == id) {
	                    index = i
	                    break
	                }
	            }

	            return index
	        }

			this.categoryIndex = function(id) {
	            var i = -1, index = -1

	            for(i = 0; i < self.summit.categories.length; i++) {
	                if(self.summit.categories[i].id == id) {
	                    index = i
	                    break
	                }
	            }

	            return index
	        }


			this.setActiveKey = function(key) {
				self.activekey = key
				id = self.presentations[key].id
				opts.trigger('load-presentation-details', id)			
				self.update()
			}.bind(this);

			this.setCategory = function(category) {
				self.activekey = null
				self.activeCategory = category
				var id
				if(category) id = category.id
				opts.trigger('load-presentations',null,id)
			}.bind(this);


			riot.route(function(mode, action, id) {
				if (mode === 'presentations') {

					self.DisplayMode = 'browse'

					if(action === 'show' && id) {
						opts.trigger('load-presentation-details', id)
						self.showDetails()
					}

					self.update()
				}

				if (mode === 'selections') {
					self.DisplayMode = 'selections'
					self.update()				
				}

				if (mode === 'directory') {
					self.DisplayMode = 'directory'
					self.update()				
				}

				if (mode === 'tutorial') {
					self.DisplayMode = 'tutorial'
					self.update()				
				}

				if (mode === 'requests') {
					self.DisplayMode = 'requests'
					self.update()				
				}

			})				

			this.on('mount', function(){

				console.log('window height', window.innerHeight)

				opts.trigger('load-summit-details')

				riot.route.exec(function(mode, action, id) {
					if (mode === 'presentations') {

						self.DisplayMode = 'browse'

						self.update()
					}

					if (mode === 'selections') {
						self.DisplayMode = 'selections'
						self.update()
					}
					
					if (mode === 'directory') {
						self.DisplayMode = 'directory'
						self.update()				
					}

					if (mode === 'tutorial') {
						self.DisplayMode = 'tutorial'
						self.update()				
					}

					if (mode === 'requests') {
						self.DisplayMode = 'requests'
						self.update()				
					}


				})			
			})

			opts.on('summit-details-loaded', function(result){
				self.summit = result
				if(self.summit.track_chair.categories) self.setCategory(self.summit.categories[0])
				self.update()
			})

			self.sortPresentations = function(set, sortBy, order) {
				
				if(order === 'desc') {
					set.sort(function(a,b) {
						return b[sortBy] - a[sortBy]
					})
				} else {
					set.sort(function(a,b) {
						return a[sortBy] - b[sortBy]
					})				
				}

				return set
			}		

			opts.on('presentations-loaded', function(result){

				self.presentations = result
				self.quantity = self.presentations.length

				if(self.currentPresentation) self.activekey = self.indexOf(self.currentPresentation.id)

				self.update()

			})


			opts.on('presentation-details-loaded', function(result){

				self.currentPresentation = []
				self.update()

				console.log('currentPresentation', result)
				self.currentPresentation = result

				if(!self.searchmode) {

					cat_index = self.categoryIndex(result.category_id)
					
					if(self.activeCategory != self.summit.categories[cat_index]) {
						self.activeCategory = self.summit.categories[cat_index]
						opts.trigger('load-presentations',null,result.category_id)
					} else {
						if(self.currentPresentation) self.activekey = self.indexOf(self.currentPresentation.id)
					}

				}

				self.update()
			})

			this.showDetails = function() {
				self.details = true
				self.update()
			}.bind(this);

			this.closeDetails = function() {
				self.details = false
			}.bind(this);

			this.search = function(e) {
				self.activekey = null
				self.details = false

				self.quantity = 0
				self.searchmode = true
				opts.trigger('load-presentations', e.target[0].value)
			}.bind(this);

			this.selectPresentation = function(e) {
				opts.trigger('select-presentation', self.currentPresentation.id)
			}.bind(this);

			this.unselectPresentation = function(e) {
				opts.trigger('unselect-presentation', self.currentPresentation.id)
			}.bind(this);

			this.groupSelectPresentation = function(e) {
				opts.trigger('group-select-presentation', self.currentPresentation.id)
			}.bind(this);

			this.groupUnselectPresentation = function(e) {
				opts.trigger('group-unselect-presentation', self.currentPresentation.id)
			}.bind(this);

			opts.on('presentation-selected', function(){
				self.currentPresentation.selected = true;
				self.opts.trigger('load-selections',self.currentPresentation.category_id)				
				self.toasts.push(
					{
					  text: 'The presentation was added to your selection list.',
					  timeout: 6000
					}
				)

				presIndex = self.indexOf(self.currentPresentation.id)
				self.presentations[presIndex].selected = true

				self.update();

			})

			opts.on('presentation-unselected', function(){
				self.currentPresentation.selected = false

				presIndex = self.indexOf(self.currentPresentation.id)

				self.presentations[presIndex].selected = false

				self.opts.trigger('load-selections',self.currentPresentation.category_id)
				self.toasts.push(
					{
					  text: 'The presentation was removed from your selection list.',
					  timeout: 6000
					}
				)
				self.update();							
			})		

			opts.on('presentation-group-selected', function(){
				self.currentPresentation.group_selected = true;
				self.opts.trigger('load-selections',self.currentPresentation.category_id)				
				self.toasts.push(
					{
					  text: 'The presentation was added to the team selection list.',
					  timeout: 6000
					}
				)

				self.update();

			})

			opts.on('presentation-group-unselected', function(){
				self.currentPresentation.group_selected = false

				self.opts.trigger('load-selections',self.currentPresentation.category_id)
				self.toasts.push(
					{
					  text: 'The presentation was removed from the team selection list.',
					  timeout: 6000
					}
				)
				self.update();							
			})	

			this.clearSearch = function() {
				document.getElementById('app-search').value='';
				self.quantity = 0
				self.searchmode = false
				opts.trigger('load-presentations', null, this.activeCategory.id)
				self.activekey = null			
				self.update()
			}.bind(this);

			Mousetrap.bind('n', function() {
				nextPresKey = self.activekey + 1
				if(nextPresKey + 1 > self.presentations.length) nextPresKey = 0
				self.setActiveKey(nextPresKey)
			})

			Mousetrap.bind('p', function() {
				nextPresKey = self.activekey - 1
				if(nextPresKey === -1) nextPresKey = self.presentations.length - 1
				self.setActiveKey(nextPresKey)
			})


			Mousetrap.bind('s', function() {
				if(
					self.currentPresentation.can_assign &&
					!self.currentPresentation.selected
				) 
				{
					self.selectPresentation()
				 }
			})		

		
	});

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	var Sortable = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"sortablejs\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()))
	riot.tag('sortable', '<ul id="simpleList" class="list-group"> <li each="{ item, i in opts.items }" class="list-group-item" data-id="{ item.id }" data-order="{ item.order }" >{ item.title }</li> </ul>', '.sortable-ghost { background-color: #E6E6E6; color: #E6E6E6!important; }', function(opts) {

			var self = this
			self.api = self.opts.api
			self.items = self.opts.items

			this.sendUpdatedSort = function(new_sort) {
				self.api.trigger('save-sort-order', self.parent.listID, new_sort)
			}.bind(this);

			this.on('mount', function(){

				var simpleList = document.getElementById('simpleList')
				var sortable = Sortable.create(simpleList,{
					onUpdate: function(evt){
						
						self.sendUpdatedSort(sortable.toArray())
						self.update()

					}
				});
			})

		
	});

/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('presentationitem', '<div class="presentation-row {active: isActive()} { selected: opts.data.selected }" onclick="{ setActive }"> <div class="row"> <div class="{ col-lg-9: !opts.details } { col-lg-11: opts.details } { col-md-9: !opts.details } { col-md-11: opts.details }" onclick="{ setActive }"> <span class="pull-left presentation-row-icon"><i class="fa fa-check-circle-o" show="{ opts.data.selected }"></i>&nbsp;</span> <div class="presentation-title"> { opts.data.title } <span if="{ opts.data.moved_to_category }" class="new-presentation"><i class="fa fa-star"></i>New</span> </div> </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show="{ !opts.details }" > { opts.data.vote_average } </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show="{ !opts.details }"> { opts.data.vote_count } </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show="{ !opts.details }"> { opts.data.total_points } </div> </div> </div>', 'presentationitem .presentation-row, [riot-tag="presentationitem"] .presentation-row{ border: 1px solid #D5D5D5; padding: 5px; margin-bottom: -1px; cursor: pointer; font-size: 1.3em; } presentationitem .new-presentation, [riot-tag="presentationitem"] .new-presentation,presentationitem .new-presentation .fa, [riot-tag="presentationitem"] .new-presentation .fa{ color: orange!important; } presentationitem .presentation-row.active .new-presentation, [riot-tag="presentationitem"] .presentation-row.active .new-presentation,presentationitem .presentation-row.active .new-presentation .fa, [riot-tag="presentationitem"] .presentation-row.active .new-presentation .fa{ color: white!important; } presentationitem .presentation-row.selected, [riot-tag="presentationitem"] .presentation-row.selected{ background-color: rgba(221, 239, 255, 0.50); } presentationitem .presentation-row a, [riot-tag="presentationitem"] .presentation-row a{ text-decoration: none; } presentationitem .presentation-row.active, [riot-tag="presentationitem"] .presentation-row.active,presentationitem .presentation-row.active a, [riot-tag="presentationitem"] .presentation-row.active a{ background-color: #3A89D3; color: white; } presentationitem .presentation-row .fa, [riot-tag="presentationitem"] .presentation-row .fa{ padding-top: 0.2em; color: #0078AE; } presentationitem .presentation-row.active .fa, [riot-tag="presentationitem"] .presentation-row.active .fa{ color: white; } presentationitem .presentation-row-icon, [riot-tag="presentationitem"] .presentation-row-icon{ display: block; width: 30px; padding-left: 4px; } presentationitem .presentation-title, [riot-tag="presentationitem"] .presentation-title{ margin-left: 30px; }', function(opts) {

		this.setActive = function(e) {
			this.parent.setActiveKey(this.opts.key)
			riot.route('presentations/show/' + this.opts.data.id)
		}.bind(this);
		
		this.isActive = function() {
			return this.parent.activekey == this.opts.key
		}.bind(this);



	});

/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('navbar', '<nav class="navbar navbar-default"> <div class="container-fluid">  <div class="navbar-header"> <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button> <a class="navbar-brand" href="#">OpenStack Track Chairs App </a> </div>  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1"> <ul class="nav navbar-nav"> <li class="{ active: self.parent.DisplayMode === \'tutorial\' }"><a href="#" onclick="{ setMode(\'tutorial\') }">Tutorial</a></li> <li class="{ active: self.parent.DisplayMode === \'browse\' }"><a href="#" onclick="{ setMode(\'presentations\') }">Browse Presentations <span class="sr-only">(current)</span></a></li> <li class="{ active: self.parent.DisplayMode === \'selections\' }"><a href="#" onclick="{ setMode(\'selections\') }">Your Selections</a></li> <li class="{ active: self.parent.DisplayMode === \'directory\' }"><a href="#" onclick="{ setMode(\'directory\') }">Chair Directory</a></li> <li class="{ active: self.parent.DisplayMode === \'requests\' }" show="{ opts.admin }"><a href="#" onclick="{ setMode(\'requests\') }">Change Requests</a></li> </ul> </div> </div> </nav>', function(opts) {
		
		self = this;

		this.setMode = function(mode) {
			return function(e) {
				riot.route(mode)
			}
		}.bind(this);


	});

/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('raw', '<span></span>', function(opts) {
	  
	  this.on('update', function(){
		if(opts.content) {
			this.root.innerHTML = opts.content
		}
	  })


	});

/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('categorymenu', '<div class="btn-group"> <button if="{ opts.active }" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> { opts.active.title } <span class="caret"></span> </button> <button if="{ !opts.active }" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" if="{ opts.active }"> All Categories <span class="caret"></span> </button> <ul class="dropdown-menu"> <li each="{category in opts.categories}"> <a href="#" if="{category.user_is_chair}" onclick="{ parent.setCategory }"><strong>{ category.title } (Chair)</strong></a> <a href="#" if="{!category.user_is_chair}" onclick="{ parent.setCategory }">{ category.title }</a> </li> <li role="separator" class="divider"></li> <li><a href="#" onclick="{ allCategories }">All Categories</a></li> </ul> </div>', function(opts) {

		this.setCategory = function(e) {
			this.parent.setCategory(e.item.category)
		}.bind(this);

		this.allCategories = function() {
			this.parent.setCategory()
		}.bind(this);


	});

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('addcommentform', '<form onsubmit="{addComment}"> <textarea id="commentBody" style="width: 100%;"></textarea><br> <button type="button" class="btn btn-default" onclick="{addComment}">Add Comment</button> </form>', function(opts) {

			this.addComment = function() {
				opts.api.trigger('add-comment',opts.presentation.id,commentBody.value)

				if (!opts.presentation.comments) opts.presentation.comments = new Array()

				opts.presentation.comments.push({ body: commentBody.value })

				commentBody.value = ''
				this.parent.update()
			}.bind(this);
		
	});

/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('comment', '<div class="media"> <div class="media-body"> <h4 class="media-heading">{ name }</h4> { body } </div> </div>', function(opts) {

	});

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	__webpack_require__(11)
	__webpack_require__(12)

	riot.tag('selection-manager', '<h2> { activeCategory.title } Track <selectionmenu categories="{ summit.track_chair.categories }" if="{ summit.track_chair.categories.length > 1 }" active="{ activeCategory }" /> </h2> <p>For this track, you should select <strong>{activeCategory.session_count} presentations</strong> plus at least two alternates.</p> <hr> <selection-list each="{ lists }" name="List" listname="{ list_name }" selections="{ selections }" listid="{ \'list\' + list_id }" selectionlist="{ list_id }" mine="{ mine }" listtype="{ list_type }" slots="{ slots }" category="{ activeCategory }"></selection-list>', function(opts) {
			var self = this
				self.lists = []

			this.calcWidth = function() {
				if (self.lists.length > 0) {
					return self.lists.length * 100 + 'px'
				} else {
					return "100%"
				}
			}.bind(this);

			opts.api.on('summit-details-loaded', function(result){
				self.summit = result
				if(self.summit.track_chair.categories) {
					self.setCategory(self.summit.categories[0])
					opts.api.trigger('load-selections',self.activeCategory.id)
				}
				self.update()
			})

			this.setCategory = function(category) {
				self.activeCategory = category
				var id
				if(category) id = category.id
				opts.api.trigger('load-selections',category.id)
				self.parent.details = false
				self.parent.setCategory(category)
			}.bind(this);

			opts.api.on('selections-loaded', function(result){

				if(result.category_id == self.activeCategory.id) {

					self.lists = []
					self.update()
					self.lists = result.lists
					self.update()
					opts.api.trigger('selections-ready')

				}

			})

			opts.api.on('sort-order-saved', function(){
				self.setCategory(self.activeCategory)
			})


		
	});

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	var Sortable = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"sortablejs\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()))

	riot.tag('selection-list', '<div class="col-lg-3"> <h3>{ opts.listname }</h3> <div if="{!opts.selections && opts.listtype != \'Group\'}"><i>This person has not made any selections yet.</i></div> <div if="{!opts.selections && opts.listtype == \'Group\'}"><i>There are no team selections yet. Drag one into here to create one.</i></div> <ul id="{ opts.listid }" class="list-group {empty: !opts.selections}"> <li each="{ item, i in opts.selections }" class="list-group-item { alternate: i >= soltsAvailble }" data-id="{ item.id }" data-order="{ item.order }" onclick="{ loadPresentation }" > <span class="pull-left slot-number" if="{ i < soltsAvailble }">{i+1}</span> <span class="pull-left slot-number" if="{ i >= soltsAvailble }">A</span> <div class="item-title">{ item.title } <span if="{ i >= soltsAvailble }">(Alternate)</span> </div> </li> </ul> <p if="{ selections.length > 0 }">{selections.length} of { slots } selected. <span if="{ (selections.length - soltsAvailble) > 0 }" >{selections.length - soltsAvailble } Alternates.</span> <span if="{ !((selections.length - soltsAvailble) > 0) }" >No alternates yet.</span> </p> </div>', '.list-group-item { overflow: hidden; cursor: arrow; } .sortable-ghost, .sortable-ghost .selection-front, .sortable-ghost .selection-back { background-color: #E6E6E6; color: #E6E6E6!important; } .alternate { background-color: rgba(184, 220, 253, 0.25); } .list-group.empty { min-height: 100px; } .selection-front, .selection-back { display: block; padding: 5px; min-height: 3em; } .selection-front { width: 100%; } .selection-back { background-color: #D5D5D5; width: 6em; position: absolute; top: 0px; right: -6em; bottom: 0px; } .selection-back.slide { right: -6em; -webkit-animation: slide 0.2s forwards; animation: slide 0.2s forwards; } .slot-number { display: block; } .item-title { margin-left: 20px; } @-webkit-keyframes slide { 100% { right: 0; } } @keyframes slide { 100% { right: 0; } }', function(opts) {

			var self = this
			var api = self.parent.parent.opts.api

			self.soltsAvailble = opts.slots

			this.sendUpdatedSort = function(new_sort) {
				api.trigger('save-sort-order', self.opts.selectionlist, new_sort)
			}.bind(this);

			self.indexOf = function(needle) {
	            var i = -1, index = -1

	            if (!self.opts.selections) return index

	            for(i = 0; i < self.opts.selections.length; i++) {
	                if(self.opts.selections[i].id == needle) {
	                    index = i
	                    break
	                }
	            }

	            return index
	        }


			api.on('selections-ready', function(result){			

				console.log('6a. selection list hears selections-ready.')

				var simpleList = document.getElementById(self.opts.listid)
				
				if(simpleList) {
					var sortable = Sortable.create(simpleList,{
						group: { name: "selection-list-group", pull: "clone", put: true },
						onUpdate: function(evt){

							if(!self.opts.mine && !(self.opts.listtype == 'Group')) {

								self.parent.parent.parent.toasts.push(
									{
									  text: 'Oops! You can\'t sort another chair\'s list .',
									  timeout: 4000
									}
								)
								api.trigger('sort-order-saved')
								self.parent.parent.parent.update()
								return						
							}

							if (evt.newIndex >= self.soltsAvailble) {
								evt.item.className = "list-group-item alternate"
							} else {
								evt.item.className = "list-group-item"
							}
							self.sendUpdatedSort(sortable.toArray())
							self.update()
						},
						onAdd: function(evt){

							if(!self.opts.mine && !(self.opts.listtype == 'Group')) {
								evt.item.parentNode.removeChild(evt.item)

								self.parent.parent.parent.toasts.push(
									{
									  text: 'Oops! You can\'t add a presentation to another chair\'s list .',
									  timeout: 4000
									}
								)
								self.parent.parent.parent.update()							
							}

							else if (self.indexOf(evt.item.dataset.id) > -1) {
								evt.item.parentNode.removeChild(evt.item)

								self.parent.parent.parent.toasts.push(
									{
									  text: 'This presentation is already in this list.',
									  timeout: 4000
									}
								)
								self.parent.parent.parent.update()

							}

							self.sendUpdatedSort(sortable.toArray())
							self.update()
						}
					});
				}

				console.log('6b. selection list updates.')
				self.update()

			})


			this.loadPresentation = function(e) {
				self.parent.parent.parent.clearSearch()
				riot.route('presentations/show/' + e.item.item.id)
			}.bind(this);


		
	});


/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('selectionmenu', '<div class="btn-group"> <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" if="{ opts.active }"> Switch Category <span class="caret"></span> </button> <ul class="dropdown-menu"> <li each="{category in opts.categories}"><a href="#" onclick="{ parent.setCategory }">{ category.title }</a></li> </ul> </div>', function(opts) {

		this.setCategory = function(e) {
			this.parent.setCategory(e.item.category)
		}.bind(this);

		this.allCategories = function() {
			this.parent.setCategory()
		}.bind(this);


	});

/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(riot) {riot.tag("rg-toast",'<div class="toasts { opts.position }" if="{ opts.toasts.length > 0 }"> <div class="toast" each="{ opts.toasts }" onclick="{ parent.toastClicked }"> { text } </div> </div>','rg-toast .toasts, [riot-tag="rg-toast"] .toasts{ position: fixed; width: 250px; max-height: 100%; overflow-y: auto; background-color: transparent; z-index: 101; } rg-toast .toasts.topleft, [riot-tag="rg-toast"] .toasts.topleft{ top: 0; left: 0; } rg-toast .toasts.topright, [riot-tag="rg-toast"] .toasts.topright{ top: 0; right: 0; } rg-toast .toasts.bottomleft, [riot-tag="rg-toast"] .toasts.bottomleft{ bottom: 0; left: 0; } rg-toast .toasts.bottomright, [riot-tag="rg-toast"] .toasts.bottomright{ bottom: 0; right: 0; } rg-toast .toast, [riot-tag="rg-toast"] .toast{ padding: 20px; margin: 20px; background-color: rgba(0, 0, 0, 0.8); color: white; font-size: 13px; cursor: pointer; }',function(opts){var _this=this;if(!opts.position)opts.position="topright";this.toastClicked=function(e){if(e.item.onclick)e.item.onclick(e);if(e.item.onclose)e.item.onclose();window.clearTimeout(e.item.timer);opts.toasts.splice(opts.toasts.indexOf(e.item),1)};this.on("update",function(){opts.toasts.forEach(function(toast){toast.id=Math.random().toString(36).substr(2,8);if(!toast.timer&&!toast.sticky){toast.startTimer=function(){toast.timer=window.setTimeout(function(){opts.toasts.splice(opts.toasts.indexOf(toast),1);if(toast.onclose)toast.onclose();_this.update()},toast.timeout||6e3)};toast.startTimer()}})})});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(riot) {riot.tag("rg-modal",'<div class="overlay { expanded: opts.modal.visible, ghost: opts.modal.ghost }" onclick="{ close }"></div> <div class="modal { ghost: opts.modal.ghost }" if="{ opts.modal.visible }"> <header class="header"> <button if="{ opts.modal.close != false }" type="button" class="close" aria-label="Close" onclick="{ close }"> <span aria-hidden="true">&times;</span> </button> <h3 class="heading">{ opts.modal.heading }</h3> </header> <div class="body"> <yield></yield> </div> <footer class="footer"> <button class="button" each="{ opts.modal.buttons }" type="button" onclick="{ action }" riot-style="{ style }"> { text } </button> <div class="clear"></div> </footer> </div>','rg-modal .overlay, [riot-tag="rg-modal"] .overlay,rg-modal .overlay.ghost, [riot-tag="rg-modal"] .overlay.ghost{ position: fixed; top: 0; left: -100%; right: 0; bottom: 0; width: 100%; height: 100%; background-color: transparent; cursor: pointer; -webkit-transition: background-color 0.8s ease, left 0s 0.8s; -moz-transition: background-color 0.8s ease, left 0s 0.8s; -ms-transition: background-color 0.8s ease, left 0s 0.8s; -o-transition: background-color 0.8s ease, left 0s 0.8s; transition: background-color 0.8s ease, left 0s 0.8s; z-index: 50; } rg-modal .overlay.expanded, [riot-tag="rg-modal"] .overlay.expanded,rg-modal .overlay.ghost.expanded, [riot-tag="rg-modal"] .overlay.ghost.expanded{ left: 0; background-color: rgba(0, 0, 0, 0.8); -webkit-transition: background-color 0.8s ease, left 0s; -moz-transition: background-color 0.8s ease, left 0s; -ms-transition: background-color 0.8s ease, left 0s; -o-transition: background-color 0.8s ease, left 0s; transition: background-color 0.8s ease, left 0s; } rg-modal .modal, [riot-tag="rg-modal"] .modal,rg-modal .modal.ghost, [riot-tag="rg-modal"] .modal.ghost{ position: fixed; width: 95%; max-width: 500px; font-size: 1.1em; top: 50%; left: 50%; -webkit-transform: translate3d(-50%, -75%, 0); -moz-transform: translate3d(-50%, -75%, 0); -ms-transform: translate3d(-50%, -75%, 0); -o-transform: translate3d(-50%, -75%, 0); transform: translate3d(-50%, -75%, 0); background-color: white; color: #252519; z-index: 101; } rg-modal .modal.ghost, [riot-tag="rg-modal"] .modal.ghost{ background-color: transparent; color: white; } rg-modal .header, [riot-tag="rg-modal"] .header{ position: relative; text-align: center; } rg-modal .heading, [riot-tag="rg-modal"] .heading{ padding: 20px 20px 0 20px; margin: 0; font-size: 1.2em; } rg-modal .modal.ghost .heading, [riot-tag="rg-modal"] .modal.ghost .heading{ color: white; } rg-modal .close, [riot-tag="rg-modal"] .close{ position: absolute; top: 5px; right: 5px; padding: 0; height: 25px; width: 25px; line-height: 25px; font-size: 25px; border: 0; background-color: transparent; color: #ef424d; cursor: pointer; outline: none; } rg-modal .modal.ghost .close, [riot-tag="rg-modal"] .modal.ghost .close{ color: white; } rg-modal .body, [riot-tag="rg-modal"] .body{ padding: 20px; } rg-modal .footer, [riot-tag="rg-modal"] .footer{ padding: 0 20px 20px 20px; } rg-modal .button, [riot-tag="rg-modal"] .button{ float: right; padding: 10px; margin: 0 5px 0 0; border: none; font-size: 0.9em; text-transform: uppercase; cursor: pointer; outline: none; background-color: white; } rg-modal .modal.ghost .button, [riot-tag="rg-modal"] .modal.ghost .button{ color: white; background-color: transparent; } rg-modal .clear, [riot-tag="rg-modal"] .clear{ clear: both; }',function(opts){this.close=function(e){opts.modal.visible=false;if(opts.modal.onclose)opts.modal.onclose(e)}});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	<!-- Modal -->
	riot.tag('modal', '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> <div class="modal-dialog" role="document"> <div class="modal-content"> <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> <h4 class="modal-title" id="myModalLabel">Suggest a category change</h4> </div> <div class="modal-body"> <div show="{ !finished }"> <p>I\'d like to suggest that this presentation:</p> <p><strong>{ opts.presentation.title }</strong></p> <p>Be moved to this category: <select name="catMoveSelector"> <option each="{ category in opts.categories }" value="{category.id}">{category.title}</option> </select></p> </div> <div show="{ finished }"> <p>Ok, thanks! We’ll notify the chairs of the other category that you’d like to make the switch. If the one of the chairs of the other category agrees to the change, the presentation will be moved.</p> </div> </div> <div class="modal-footer" show="{ !finished }"> <button type="button" class="btn btn-default" data-dismiss="modal" onclick="{ closeModal }">Close</button> <button type="button" class="btn btn-primary" onclick="{ suggestChange }">Suggest Change</button> </div> <div class="modal-footer" show="{ finished }"> <button type="button" class="btn btn-primary" onclick="{ closeModal }" data-dismiss="modal">OK</button> </div> </div> </div> </div>', function(opts) {

	    var self = this

	    this.on('mount', function(){
	      this.finished = false
	    })

	    this.suggestChange = function(e) {

	      var change = {}
	      change.new_category = this.catMoveSelector.value
	      change.presentation_id = this.opts.presentation.id

	      opts.api.trigger('suggest-category-change', change)

	    }.bind(this);

	    this.closeModal = function() {
	      this.finished = false
	    }.bind(this);

	    this.opts.api.on('category-change-suggested', function(){
	        self.finished = true
	        self.update()
	    })


	  
	});

/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('chairdirectory', '<h2>Chair Directory</h2> <table class="table"> <tr> <th>Track</th> <th>Name</th> <th>Email</th> </tr> <tr each="{opts.chairs}"> <td>{ category }</td> <td><i class="fa fa-user"></i> { first_name } { last_name }</td> <td><a href="mailto:{ email }">{ email }</a></td> </tr> </table>', function(opts) {


	});

/***/ },
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('tutorial', '<h2>A Quick Tutorial Video</h2> <iframe width="560" height="315" src="https://www.youtube.com/embed/_7A82b0Fp3A" frameborder="0" allowfullscreen></iframe>', function(opts) {


	});

/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);


	__webpack_require__(19)
	__webpack_require__(20)


	riot.tag('change-requests', '<approve-modal request="{ activeRequest }"></approve-modal> <change-error-modal request="{ activeRequest }"></change-error-modal> <h1>Change Requests</h1> <table class="table"> <tr> <th>Presentation</th> <th>Status</th> <th>Old Category</th> <th></th> <th>New Category</th> <th>Requester</th> <th></th> </tr> <tr each="{request in requests}" class="{ completed: request.done == \'1\' } { selected: request.has_selections == true }"> <td> <a href="#" data-toggle="modal" data-target="#approveModal" onclick="{ parent.setRequest(request) }" if="{ !request.has_selections }">{ request.presentation_title }</a> <a href="#" data-toggle="modal" data-target="#changeErrorModal" onclick="{ parent.setRequest(request) }" if="{ request.has_selections }">{ request.presentation_title }</a></td> <td> <span if="{ request.done == \'1\'}">Completed</span> <span if="{ request.done == \'0\' }">Requested</span> </td> <td>{ request.old_category.title }</td> <td><i class="fa fa-long-arrow-right"></i></td> <td>{ request.new_category.title }</td> <td>{ request.requester }</td> </tr> </table>', 'change-requests .completed, [riot-tag="change-requests"] .completed{ opacity: 0.4;} change-requests .selected, [riot-tag="change-requests"] .selected,change-requests .selected a, [riot-tag="change-requests"] .selected a{ color: red;}', function(opts) {

			self = this
			self.requests = []
			self.activeRequest = []

			this.on('mount', function(){
				opts.api.trigger('load-change-requests')
			})

			this.setRequest = function(request) {
				return function(e) {
					self.activeRequest = request
					self.update()
				}
			}.bind(this);

			opts.api.on('change-requests-loaded', function(response){
				self.requests = []
				self.update()
				self.requests = response
				self.update()
			})

		
	});

/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	<!-- Modal -->
	riot.tag('approve-modal', '<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> <div class="modal-dialog" role="document"> <div class="modal-content"> <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> <h4 class="modal-title" id="myModalLabel">Review Category Change</h4> </div> <div class="modal-body"> Click Approve Change to move the presentation <strong>{ opts.request.presentation_title }</strong> to the category "<strong>{ opts.request.new_category.title }</strong>". </div> <div class="modal-footer" show="{ !finished }"> <button type="button" class="btn btn-default" onclick="{ showPresentation(opts.request.presentation_id) }" data-dismiss="modal">See Presentation</button> <button type="button" class="btn btn-primary" onclick="{ approveChange(opts.request) }" data-dismiss="modal">Approve Change</button> </div> </div> </div> </div>', function(opts) {

	  var self = this

	  this.showPresentation = function(presId) {
	    return function(e) {
	      riot.route('presentations/show/' + presId)
	    }
	  }.bind(this);

	  this.approveChange = function(request) {
	    return function(e) {
	      self.parent.parent.opts.trigger('approve-change', request.id)
	    }
	  }.bind(this);



	  
	});

/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	<!-- Modal -->
	riot.tag('change-error-modal', '<div class="modal fade" id="changeErrorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> <div class="modal-dialog" role="document"> <div class="modal-content"> <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> <h4 class="modal-title" id="myModalLabel">Oops...</h4> </div> <div class="modal-body"> There was a request made to move this presentation to <strong>{ opts.request.new_category.title }</strong>, but it has already been selected by the track chairs of the current category, <strong>{ opts.request.old_category.title }</strong>. In order to move it, you\'ll need to ask if the chairs if they will unselect the presentation first. </div> <div class="modal-footer"> <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button> </div> </div> </div> </div>', function(opts) {


	});

/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(riot) {
	/*
	* The track chairs API listeners and triggers to fetch server-side data
	*/

	// Requirements and globals
	reqwest = __webpack_require__(22)
	var api = riot.observable()
	var url = '/trackchairs/api/v1/'

	/*
	*	Listeners
	*/


	api.on('load-summit-details', function(id){

		var append = 'summit/'
		id = typeof id !== 'undefined' ? id : 'active'
		var append = append + id

		reqwest({
		    url: url + append
		  , method: 'get'
		  , success: function (resp) {
				api.trigger('summit-details-loaded', resp)
		    }
		})	

	})


	// Request to track chair selections for a particular category
	api.on('load-selections', function(categoryId){

		console.log('4a. api hears load selections.');

		reqwest({
		    url: url + 'selections/' + categoryId + '/'
		  , method: 'get'
		  , success: function (resp) {
		  		console.log('response from server loading selctions: ', resp)
				console.log('4b. api fires selections loaded.');	  		
				api.trigger('selections-loaded', resp)
		    }
		})
	})


	// Request to load presenations
	api.on('load-presentations', function(query,categoryId){

		var append = '?'
		if(query) { append = append + 'keyword=' + encodeURI(query) }
		if(categoryId) { append = append + '&category=' + encodeURI(categoryId) }

		reqwest({
		    url: url + append
		  , method: 'get'
		  , success: function (resp) {
				api.trigger('presentations-loaded', resp.results)
		    }
		})

	})

	// Request to pull details for a particular presenation
	api.on('load-presentation-details', function(id){

		reqwest({
		    url: url + 'presentation/' + id + '/'
		  , method: 'get'
		  , success: function (resp) {
				api.trigger('presentation-details-loaded', resp)
		    }
		})
	})

	// Add a comment to the current presentation
	api.on('add-comment', function(id, comment){

		reqwest({
		    url: url + 'presentation/' + id + '/comment'
		  , method: 'post'
		  , data: { comment: comment }
		  , success: function (resp) {
				api.trigger('comment-added', resp)
		    }
		})


	})

	// Select a presentation for a personal list
	api.on('select-presentation', function(id){

		reqwest({
		    url: url + 'presentation/' + id + '/select'
		  , method: 'get'
		  , success: function (resp) {
		  		console.log('2b. API is firing presentation-selected');
				api.trigger('presentation-selected', resp)
		    }
		})

	})

	// Unselect (remove presentation from personal list)
	api.on('unselect-presentation', function(id){

		reqwest({
		    url: url + 'presentation/' + id + '/unselect'
		  , method: 'get'
		  , success: function (resp) {
				api.trigger('presentation-unselected', resp)
		    }
		})

	})

	// Select a presentation for a personal list
	api.on('group-select-presentation', function(id){

		reqwest({
		    url: url + 'presentation/' + id + '/group/select'
		  , method: 'get'
		  , success: function (resp) {
				api.trigger('presentation-group-selected', resp)
		    }
		})

	})

	// Unselect (remove presentation from personal list)
	api.on('group-unselect-presentation', function(id){


		reqwest({
		    url: url + 'presentation/' + id + '/group/unselect'
		  , method: 'get'
		  , success: function (resp) {
				api.trigger('presentation-group-unselected', resp)
		    }
		})

	})

	api.on('save-sort-order', function(list_id, sort_order){


		reqwest({
		    url: url + 'reorder/'
		  , method: 'post'
		  , data: {sort_order: sort_order, list_id: list_id}
		  , success: function (resp) {
		  		api.trigger('sort-order-saved', resp)
		    }
		})	

	})

	api.on('suggest-category-change', function(suggestedChange){

		console.log(url + 'presentation/' + suggestedChange.presentation_id + '/category_change/new/?new_cat=' + suggestedChange.new_category)

		reqwest({
		    url: url + 'presentation/' + suggestedChange.presentation_id + '/category_change/new/?new_cat=' + suggestedChange.new_category
		  , method: 'get'
		  , success: function (resp) {
		  		api.trigger('category-change-suggested', resp)
		    }
		})	

	})

	api.on('approve-change', function(id){

		console.log(url + 'category_change/accept/' + id)

		reqwest({
		    url: url + 'category_change/accept/' + id
		  , method: 'get'
		  , success: function (resp) {
		  		api.trigger('change-approved', resp)
		    }
		})	
	})

	api.on('change-approved', function(){
		api.trigger('load-change-requests')
	})

	api.on('load-change-requests', function(){
		reqwest({
		    url: url + 'change_requests'
		  , method: 'get'
		  , success: function (resp) {
		  		api.trigger('change-requests-loaded', resp)
		    }
		})	
	})

	module.exports = api;
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 22 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	  * Reqwest! A general purpose XHR connection manager
	  * license MIT (c) Dustin Diaz 2015
	  * https://github.com/ded/reqwest
	  */

	!function (name, context, definition) {
	  if (typeof module != 'undefined' && module.exports) module.exports = definition()
	  else if (true) !(__WEBPACK_AMD_DEFINE_FACTORY__ = (definition), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__))
	  else context[name] = definition()
	}('reqwest', this, function () {

	  var context = this

	  if ('window' in context) {
	    var doc = document
	      , byTag = 'getElementsByTagName'
	      , head = doc[byTag]('head')[0]
	  } else {
	    var XHR2
	    try {
	      // prevent browserify including xhr2
	      var xhr2 = 'xhr2'
	      XHR2 = __webpack_require__(23)(xhr2)
	    } catch (ex) {
	      throw new Error('Peer dependency `xhr2` required! Please npm install xhr2')
	    }
	  }


	  var httpsRe = /^http/
	    , protocolRe = /(^\w+):\/\//
	    , twoHundo = /^(20\d|1223)$/ //http://stackoverflow.com/questions/10046972/msie-returns-status-code-of-1223-for-ajax-request
	    , readyState = 'readyState'
	    , contentType = 'Content-Type'
	    , requestedWith = 'X-Requested-With'
	    , uniqid = 0
	    , callbackPrefix = 'reqwest_' + (+new Date())
	    , lastValue // data stored by the most recent JSONP callback
	    , xmlHttpRequest = 'XMLHttpRequest'
	    , xDomainRequest = 'XDomainRequest'
	    , noop = function () {}

	    , isArray = typeof Array.isArray == 'function'
	        ? Array.isArray
	        : function (a) {
	            return a instanceof Array
	          }

	    , defaultHeaders = {
	          'contentType': 'application/x-www-form-urlencoded'
	        , 'requestedWith': xmlHttpRequest
	        , 'accept': {
	              '*':  'text/javascript, text/html, application/xml, text/xml, */*'
	            , 'xml':  'application/xml, text/xml'
	            , 'html': 'text/html'
	            , 'text': 'text/plain'
	            , 'json': 'application/json, text/javascript'
	            , 'js':   'application/javascript, text/javascript'
	          }
	      }

	    , xhr = function(o) {
	        // is it x-domain
	        if (o['crossOrigin'] === true) {
	          var xhr = context[xmlHttpRequest] ? new XMLHttpRequest() : null
	          if (xhr && 'withCredentials' in xhr) {
	            return xhr
	          } else if (context[xDomainRequest]) {
	            return new XDomainRequest()
	          } else {
	            throw new Error('Browser does not support cross-origin requests')
	          }
	        } else if (context[xmlHttpRequest]) {
	          return new XMLHttpRequest()
	        } else if (XHR2) {
	          return new XHR2()
	        } else {
	          return new ActiveXObject('Microsoft.XMLHTTP')
	        }
	      }
	    , globalSetupOptions = {
	        dataFilter: function (data) {
	          return data
	        }
	      }

	  function succeed(r) {
	    var protocol = protocolRe.exec(r.url)
	    protocol = (protocol && protocol[1]) || context.location.protocol
	    return httpsRe.test(protocol) ? twoHundo.test(r.request.status) : !!r.request.response
	  }

	  function handleReadyState(r, success, error) {
	    return function () {
	      // use _aborted to mitigate against IE err c00c023f
	      // (can't read props on aborted request objects)
	      if (r._aborted) return error(r.request)
	      if (r._timedOut) return error(r.request, 'Request is aborted: timeout')
	      if (r.request && r.request[readyState] == 4) {
	        r.request.onreadystatechange = noop
	        if (succeed(r)) success(r.request)
	        else
	          error(r.request)
	      }
	    }
	  }

	  function setHeaders(http, o) {
	    var headers = o['headers'] || {}
	      , h

	    headers['Accept'] = headers['Accept']
	      || defaultHeaders['accept'][o['type']]
	      || defaultHeaders['accept']['*']

	    var isAFormData = typeof FormData === 'function' && (o['data'] instanceof FormData);
	    // breaks cross-origin requests with legacy browsers
	    if (!o['crossOrigin'] && !headers[requestedWith]) headers[requestedWith] = defaultHeaders['requestedWith']
	    if (!headers[contentType] && !isAFormData) headers[contentType] = o['contentType'] || defaultHeaders['contentType']
	    for (h in headers)
	      headers.hasOwnProperty(h) && 'setRequestHeader' in http && http.setRequestHeader(h, headers[h])
	  }

	  function setCredentials(http, o) {
	    if (typeof o['withCredentials'] !== 'undefined' && typeof http.withCredentials !== 'undefined') {
	      http.withCredentials = !!o['withCredentials']
	    }
	  }

	  function generalCallback(data) {
	    lastValue = data
	  }

	  function urlappend (url, s) {
	    return url + (/\?/.test(url) ? '&' : '?') + s
	  }

	  function handleJsonp(o, fn, err, url) {
	    var reqId = uniqid++
	      , cbkey = o['jsonpCallback'] || 'callback' // the 'callback' key
	      , cbval = o['jsonpCallbackName'] || reqwest.getcallbackPrefix(reqId)
	      , cbreg = new RegExp('((^|\\?|&)' + cbkey + ')=([^&]+)')
	      , match = url.match(cbreg)
	      , script = doc.createElement('script')
	      , loaded = 0
	      , isIE10 = navigator.userAgent.indexOf('MSIE 10.0') !== -1

	    if (match) {
	      if (match[3] === '?') {
	        url = url.replace(cbreg, '$1=' + cbval) // wildcard callback func name
	      } else {
	        cbval = match[3] // provided callback func name
	      }
	    } else {
	      url = urlappend(url, cbkey + '=' + cbval) // no callback details, add 'em
	    }

	    context[cbval] = generalCallback

	    script.type = 'text/javascript'
	    script.src = url
	    script.async = true
	    if (typeof script.onreadystatechange !== 'undefined' && !isIE10) {
	      // need this for IE due to out-of-order onreadystatechange(), binding script
	      // execution to an event listener gives us control over when the script
	      // is executed. See http://jaubourg.net/2010/07/loading-script-as-onclick-handler-of.html
	      script.htmlFor = script.id = '_reqwest_' + reqId
	    }

	    script.onload = script.onreadystatechange = function () {
	      if ((script[readyState] && script[readyState] !== 'complete' && script[readyState] !== 'loaded') || loaded) {
	        return false
	      }
	      script.onload = script.onreadystatechange = null
	      script.onclick && script.onclick()
	      // Call the user callback with the last value stored and clean up values and scripts.
	      fn(lastValue)
	      lastValue = undefined
	      head.removeChild(script)
	      loaded = 1
	    }

	    // Add the script to the DOM head
	    head.appendChild(script)

	    // Enable JSONP timeout
	    return {
	      abort: function () {
	        script.onload = script.onreadystatechange = null
	        err({}, 'Request is aborted: timeout', {})
	        lastValue = undefined
	        head.removeChild(script)
	        loaded = 1
	      }
	    }
	  }

	  function getRequest(fn, err) {
	    var o = this.o
	      , method = (o['method'] || 'GET').toUpperCase()
	      , url = typeof o === 'string' ? o : o['url']
	      // convert non-string objects to query-string form unless o['processData'] is false
	      , data = (o['processData'] !== false && o['data'] && typeof o['data'] !== 'string')
	        ? reqwest.toQueryString(o['data'])
	        : (o['data'] || null)
	      , http
	      , sendWait = false

	    // if we're working on a GET request and we have data then we should append
	    // query string to end of URL and not post data
	    if ((o['type'] == 'jsonp' || method == 'GET') && data) {
	      url = urlappend(url, data)
	      data = null
	    }

	    if (o['type'] == 'jsonp') return handleJsonp(o, fn, err, url)

	    // get the xhr from the factory if passed
	    // if the factory returns null, fall-back to ours
	    http = (o.xhr && o.xhr(o)) || xhr(o)

	    http.open(method, url, o['async'] === false ? false : true)
	    setHeaders(http, o)
	    setCredentials(http, o)
	    if (context[xDomainRequest] && http instanceof context[xDomainRequest]) {
	        http.onload = fn
	        http.onerror = err
	        // NOTE: see
	        // http://social.msdn.microsoft.com/Forums/en-US/iewebdevelopment/thread/30ef3add-767c-4436-b8a9-f1ca19b4812e
	        http.onprogress = function() {}
	        sendWait = true
	    } else {
	      http.onreadystatechange = handleReadyState(this, fn, err)
	    }
	    o['before'] && o['before'](http)
	    if (sendWait) {
	      setTimeout(function () {
	        http.send(data)
	      }, 200)
	    } else {
	      http.send(data)
	    }
	    return http
	  }

	  function Reqwest(o, fn) {
	    this.o = o
	    this.fn = fn

	    init.apply(this, arguments)
	  }

	  function setType(header) {
	    // json, javascript, text/plain, text/html, xml
	    if (header === null) return undefined; //In case of no content-type.
	    if (header.match('json')) return 'json'
	    if (header.match('javascript')) return 'js'
	    if (header.match('text')) return 'html'
	    if (header.match('xml')) return 'xml'
	  }

	  function init(o, fn) {

	    this.url = typeof o == 'string' ? o : o['url']
	    this.timeout = null

	    // whether request has been fulfilled for purpose
	    // of tracking the Promises
	    this._fulfilled = false
	    // success handlers
	    this._successHandler = function(){}
	    this._fulfillmentHandlers = []
	    // error handlers
	    this._errorHandlers = []
	    // complete (both success and fail) handlers
	    this._completeHandlers = []
	    this._erred = false
	    this._responseArgs = {}

	    var self = this

	    fn = fn || function () {}

	    if (o['timeout']) {
	      this.timeout = setTimeout(function () {
	        timedOut()
	      }, o['timeout'])
	    }

	    if (o['success']) {
	      this._successHandler = function () {
	        o['success'].apply(o, arguments)
	      }
	    }

	    if (o['error']) {
	      this._errorHandlers.push(function () {
	        o['error'].apply(o, arguments)
	      })
	    }

	    if (o['complete']) {
	      this._completeHandlers.push(function () {
	        o['complete'].apply(o, arguments)
	      })
	    }

	    function complete (resp) {
	      o['timeout'] && clearTimeout(self.timeout)
	      self.timeout = null
	      while (self._completeHandlers.length > 0) {
	        self._completeHandlers.shift()(resp)
	      }
	    }

	    function success (resp) {
	      var type = o['type'] || resp && setType(resp.getResponseHeader('Content-Type')) // resp can be undefined in IE
	      resp = (type !== 'jsonp') ? self.request : resp
	      // use global data filter on response text
	      var filteredResponse = globalSetupOptions.dataFilter(resp.responseText, type)
	        , r = filteredResponse
	      try {
	        resp.responseText = r
	      } catch (e) {
	        // can't assign this in IE<=8, just ignore
	      }
	      if (r) {
	        switch (type) {
	        case 'json':
	          try {
	            resp = context.JSON ? context.JSON.parse(r) : eval('(' + r + ')')
	          } catch (err) {
	            return error(resp, 'Could not parse JSON in response', err)
	          }
	          break
	        case 'js':
	          resp = eval(r)
	          break
	        case 'html':
	          resp = r
	          break
	        case 'xml':
	          resp = resp.responseXML
	              && resp.responseXML.parseError // IE trololo
	              && resp.responseXML.parseError.errorCode
	              && resp.responseXML.parseError.reason
	            ? null
	            : resp.responseXML
	          break
	        }
	      }

	      self._responseArgs.resp = resp
	      self._fulfilled = true
	      fn(resp)
	      self._successHandler(resp)
	      while (self._fulfillmentHandlers.length > 0) {
	        resp = self._fulfillmentHandlers.shift()(resp)
	      }

	      complete(resp)
	    }

	    function timedOut() {
	      self._timedOut = true
	      self.request.abort()
	    }

	    function error(resp, msg, t) {
	      resp = self.request
	      self._responseArgs.resp = resp
	      self._responseArgs.msg = msg
	      self._responseArgs.t = t
	      self._erred = true
	      while (self._errorHandlers.length > 0) {
	        self._errorHandlers.shift()(resp, msg, t)
	      }
	      complete(resp)
	    }

	    this.request = getRequest.call(this, success, error)
	  }

	  Reqwest.prototype = {
	    abort: function () {
	      this._aborted = true
	      this.request.abort()
	    }

	  , retry: function () {
	      init.call(this, this.o, this.fn)
	    }

	    /**
	     * Small deviation from the Promises A CommonJs specification
	     * http://wiki.commonjs.org/wiki/Promises/A
	     */

	    /**
	     * `then` will execute upon successful requests
	     */
	  , then: function (success, fail) {
	      success = success || function () {}
	      fail = fail || function () {}
	      if (this._fulfilled) {
	        this._responseArgs.resp = success(this._responseArgs.resp)
	      } else if (this._erred) {
	        fail(this._responseArgs.resp, this._responseArgs.msg, this._responseArgs.t)
	      } else {
	        this._fulfillmentHandlers.push(success)
	        this._errorHandlers.push(fail)
	      }
	      return this
	    }

	    /**
	     * `always` will execute whether the request succeeds or fails
	     */
	  , always: function (fn) {
	      if (this._fulfilled || this._erred) {
	        fn(this._responseArgs.resp)
	      } else {
	        this._completeHandlers.push(fn)
	      }
	      return this
	    }

	    /**
	     * `fail` will execute when the request fails
	     */
	  , fail: function (fn) {
	      if (this._erred) {
	        fn(this._responseArgs.resp, this._responseArgs.msg, this._responseArgs.t)
	      } else {
	        this._errorHandlers.push(fn)
	      }
	      return this
	    }
	  , 'catch': function (fn) {
	      return this.fail(fn)
	    }
	  }

	  function reqwest(o, fn) {
	    return new Reqwest(o, fn)
	  }

	  // normalize newline variants according to spec -> CRLF
	  function normalize(s) {
	    return s ? s.replace(/\r?\n/g, '\r\n') : ''
	  }

	  function serial(el, cb) {
	    var n = el.name
	      , t = el.tagName.toLowerCase()
	      , optCb = function (o) {
	          // IE gives value="" even where there is no value attribute
	          // 'specified' ref: http://www.w3.org/TR/DOM-Level-3-Core/core.html#ID-862529273
	          if (o && !o['disabled'])
	            cb(n, normalize(o['attributes']['value'] && o['attributes']['value']['specified'] ? o['value'] : o['text']))
	        }
	      , ch, ra, val, i

	    // don't serialize elements that are disabled or without a name
	    if (el.disabled || !n) return

	    switch (t) {
	    case 'input':
	      if (!/reset|button|image|file/i.test(el.type)) {
	        ch = /checkbox/i.test(el.type)
	        ra = /radio/i.test(el.type)
	        val = el.value
	        // WebKit gives us "" instead of "on" if a checkbox has no value, so correct it here
	        ;(!(ch || ra) || el.checked) && cb(n, normalize(ch && val === '' ? 'on' : val))
	      }
	      break
	    case 'textarea':
	      cb(n, normalize(el.value))
	      break
	    case 'select':
	      if (el.type.toLowerCase() === 'select-one') {
	        optCb(el.selectedIndex >= 0 ? el.options[el.selectedIndex] : null)
	      } else {
	        for (i = 0; el.length && i < el.length; i++) {
	          el.options[i].selected && optCb(el.options[i])
	        }
	      }
	      break
	    }
	  }

	  // collect up all form elements found from the passed argument elements all
	  // the way down to child elements; pass a '<form>' or form fields.
	  // called with 'this'=callback to use for serial() on each element
	  function eachFormElement() {
	    var cb = this
	      , e, i
	      , serializeSubtags = function (e, tags) {
	          var i, j, fa
	          for (i = 0; i < tags.length; i++) {
	            fa = e[byTag](tags[i])
	            for (j = 0; j < fa.length; j++) serial(fa[j], cb)
	          }
	        }

	    for (i = 0; i < arguments.length; i++) {
	      e = arguments[i]
	      if (/input|select|textarea/i.test(e.tagName)) serial(e, cb)
	      serializeSubtags(e, [ 'input', 'select', 'textarea' ])
	    }
	  }

	  // standard query string style serialization
	  function serializeQueryString() {
	    return reqwest.toQueryString(reqwest.serializeArray.apply(null, arguments))
	  }

	  // { 'name': 'value', ... } style serialization
	  function serializeHash() {
	    var hash = {}
	    eachFormElement.apply(function (name, value) {
	      if (name in hash) {
	        hash[name] && !isArray(hash[name]) && (hash[name] = [hash[name]])
	        hash[name].push(value)
	      } else hash[name] = value
	    }, arguments)
	    return hash
	  }

	  // [ { name: 'name', value: 'value' }, ... ] style serialization
	  reqwest.serializeArray = function () {
	    var arr = []
	    eachFormElement.apply(function (name, value) {
	      arr.push({name: name, value: value})
	    }, arguments)
	    return arr
	  }

	  reqwest.serialize = function () {
	    if (arguments.length === 0) return ''
	    var opt, fn
	      , args = Array.prototype.slice.call(arguments, 0)

	    opt = args.pop()
	    opt && opt.nodeType && args.push(opt) && (opt = null)
	    opt && (opt = opt.type)

	    if (opt == 'map') fn = serializeHash
	    else if (opt == 'array') fn = reqwest.serializeArray
	    else fn = serializeQueryString

	    return fn.apply(null, args)
	  }

	  reqwest.toQueryString = function (o, trad) {
	    var prefix, i
	      , traditional = trad || false
	      , s = []
	      , enc = encodeURIComponent
	      , add = function (key, value) {
	          // If value is a function, invoke it and return its value
	          value = ('function' === typeof value) ? value() : (value == null ? '' : value)
	          s[s.length] = enc(key) + '=' + enc(value)
	        }
	    // If an array was passed in, assume that it is an array of form elements.
	    if (isArray(o)) {
	      for (i = 0; o && i < o.length; i++) add(o[i]['name'], o[i]['value'])
	    } else {
	      // If traditional, encode the "old" way (the way 1.3.2 or older
	      // did it), otherwise encode params recursively.
	      for (prefix in o) {
	        if (o.hasOwnProperty(prefix)) buildParams(prefix, o[prefix], traditional, add)
	      }
	    }

	    // spaces should be + according to spec
	    return s.join('&').replace(/%20/g, '+')
	  }

	  function buildParams(prefix, obj, traditional, add) {
	    var name, i, v
	      , rbracket = /\[\]$/

	    if (isArray(obj)) {
	      // Serialize array item.
	      for (i = 0; obj && i < obj.length; i++) {
	        v = obj[i]
	        if (traditional || rbracket.test(prefix)) {
	          // Treat each array item as a scalar.
	          add(prefix, v)
	        } else {
	          buildParams(prefix + '[' + (typeof v === 'object' ? i : '') + ']', v, traditional, add)
	        }
	      }
	    } else if (obj && obj.toString() === '[object Object]') {
	      // Serialize object item.
	      for (name in obj) {
	        buildParams(prefix + '[' + name + ']', obj[name], traditional, add)
	      }

	    } else {
	      // Serialize scalar item.
	      add(prefix, obj)
	    }
	  }

	  reqwest.getcallbackPrefix = function () {
	    return callbackPrefix
	  }

	  // jQuery and Zepto compatibility, differences can be remapped here so you can call
	  // .ajax.compat(options, callback)
	  reqwest.compat = function (o, fn) {
	    if (o) {
	      o['type'] && (o['method'] = o['type']) && delete o['type']
	      o['dataType'] && (o['type'] = o['dataType'])
	      o['jsonpCallback'] && (o['jsonpCallbackName'] = o['jsonpCallback']) && delete o['jsonpCallback']
	      o['jsonp'] && (o['jsonpCallback'] = o['jsonp'])
	    }
	    return new Reqwest(o, fn)
	  }

	  reqwest.ajaxSetup = function (options) {
	    options = options || {}
	    for (var k in options) {
	      globalSetupOptions[k] = options[k]
	    }
	  }

	  return reqwest
	});


/***/ },
/* 23 */
/***/ function(module, exports, __webpack_require__) {

	var map = {
		"./make/bump": 27,
		"./make/bump.js": 27,
		"./make/tests": 29,
		"./make/tests.js": 29,
		"./phantom": 60,
		"./phantom.js": 60,
		"./reqwest": 22,
		"./reqwest.js": 22,
		"./reqwest.min": 61,
		"./reqwest.min.js": 61,
		"./src/copyright": 62,
		"./src/copyright.js": 62,
		"./src/ender": 63,
		"./src/ender.js": 63,
		"./src/reqwest": 64,
		"./src/reqwest.js": 64,
		"./test": 66,
		"./test.js": 66,
		"./tests/ender": 67,
		"./tests/ender.js": 67,
		"./tests/fixtures/fixtures": 69,
		"./tests/fixtures/fixtures.js": 69,
		"./tests/fixtures/fixtures_jsonp.jsonp": 73,
		"./tests/fixtures/fixtures_jsonp2.jsonp": 74,
		"./tests/fixtures/fixtures_jsonp3.jsonp": 75,
		"./tests/fixtures/fixtures_jsonp_multi.jsonp": 76,
		"./tests/fixtures/fixtures_jsonp_multi_b.jsonp": 77,
		"./tests/fixtures/fixtures_jsonp_multi_c.jsonp": 78,
		"./tests/tests": 81,
		"./tests/tests.js": 81
	};
	function webpackContext(req) {
		return __webpack_require__(webpackContextResolve(req));
	};
	function webpackContextResolve(req) {
		return map[req] || (function() { throw new Error("Cannot find module '" + req + "'.") }());
	};
	webpackContext.keys = function webpackContextKeys() {
		return Object.keys(map);
	};
	webpackContext.resolve = webpackContextResolve;
	module.exports = webpackContext;
	webpackContext.id = 23;


/***/ },
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */
/***/ function(module, exports, __webpack_require__) {

	var fs = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"fs\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()))
	  , version = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"../package.json\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())).version;

	['./reqwest.js', './reqwest.min.js'].forEach(function (file) {
	  var data = fs.readFileSync(file, 'utf8')
	  data = data.replace(/^\/\*\!/, '/*! version: ' + version)
	  fs.writeFileSync(file, data)
	})


/***/ },
/* 28 */,
/* 29 */
/***/ function(module, exports, __webpack_require__) {

	var exec = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"child_process\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())).exec
	  , fs = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"fs\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()))
	  , Connect = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"connect\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()))
	  , dispatch = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"dispatch\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()))
	  , mime = __webpack_require__(30)
	  , DelayedStream = __webpack_require__(34)

	  , getMime = function(ext) {
	      return mime.lookup(ext == 'jsonp' ? 'js' : ext)
	    }

	var routes = {
	  '/': function (req, res) {
	    res.write(fs.readFileSync('./tests/tests.html', 'utf8'))
	    res.end()
	  },
	  '/tests/timeout$': function (req, res) {
	      var delayed = DelayedStream.create(req)
	      setTimeout(function() {
	        res.writeHead(200, {
	            'Expires': 0
	          , 'Cache-Control': 'max-age=0, no-cache, no-store'
	        })
	        req.query.callback && res.write(req.query.callback + '(')
	        res.write(JSON.stringify({ method: req.method, query: req.query, headers: req.headers }))
	        req.query.callback && res.write(');')
	        delayed.pipe(res)
	      }, 2000)
	  },
	  '/tests/204': function(req, res) {
	    res.writeHead(204);
	    res.end();
	  },
	  '(([\\w\\-\\/\\.]+)\\.(css|js|json|jsonp|html|xml)$)': function (req, res, next, uri, file, ext) {
	    res.writeHead(200, {
	        'Expires': 0
	      , 'Cache-Control': 'max-age=0, no-cache, no-store'
	      , 'Content-Type': getMime(ext)
	    })
	    if (req.query.echo !== undefined) {
	      ext == 'jsonp' && res.write((req.query.callback || req.query.testCallback || 'echoCallback') + '(')
	      res.write(JSON.stringify({ method: req.method, query: req.query, headers: req.headers }))
	      ext == 'jsonp' && res.write(');')
	    } else {
	      res.write(fs.readFileSync('./' + file + '.' + ext))
	    }
	    res.end()
	  }
	}

	Connect.createServer(Connect.query(), dispatch(routes)).listen(1234)

	var otherOriginRoutes = {
	    '/get-value': function (req, res) {
	      res.writeHead(200, {
	        'Access-Control-Allow-Origin': req.headers.origin,
	        'Content-Type': 'text/plain'
	      })
	      res.end('hello')
	    },
	    '/set-cookie': function (req, res) {
	      res.writeHead(200, {
	        'Access-Control-Allow-Origin': req.headers.origin,
	        'Access-Control-Allow-Credentials': 'true',
	        'Content-Type': 'text/plain',
	        'Set-Cookie': 'cookie=hello'
	      })
	      res.end('Set a cookie!')
	    },
	    '/get-cookie-value': function (req, res) {
	      var cookies = {}
	        , value

	      req.headers.cookie && req.headers.cookie.split(';').forEach(function( cookie ) {
	        var parts = cookie.split('=')
	        cookies[ parts[ 0 ].trim() ] = ( parts[ 1 ] || '' ).trim()
	      })
	      value = cookies.cookie

	      res.writeHead(200, {
	          'Access-Control-Allow-Origin': req.headers.origin,
	          'Access-Control-Allow-Credentials': 'true',
	          'Content-Type': 'text/plain'
	      })
	      res.end(value)
	    }
	}

	Connect.createServer(Connect.query(), dispatch(otherOriginRoutes)).listen(5678)


/***/ },
/* 30 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(process) {var path = __webpack_require__(32);
	var fs = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"fs\""); e.code = 'MODULE_NOT_FOUND'; throw e; }()));

	function Mime() {
	  // Map of extension -> mime type
	  this.types = Object.create(null);

	  // Map of mime type -> extension
	  this.extensions = Object.create(null);
	}

	/**
	 * Define mimetype -> extension mappings.  Each key is a mime-type that maps
	 * to an array of extensions associated with the type.  The first extension is
	 * used as the default extension for the type.
	 *
	 * e.g. mime.define({'audio/ogg', ['oga', 'ogg', 'spx']});
	 *
	 * @param map (Object) type definitions
	 */
	Mime.prototype.define = function (map) {
	  for (var type in map) {
	    var exts = map[type];
	    for (var i = 0; i < exts.length; i++) {
	      if (process.env.DEBUG_MIME && this.types[exts]) {
	        console.warn(this._loading.replace(/.*\//, ''), 'changes "' + exts[i] + '" extension type from ' +
	          this.types[exts] + ' to ' + type);
	      }

	      this.types[exts[i]] = type;
	    }

	    // Default extension is the first one we encounter
	    if (!this.extensions[type]) {
	      this.extensions[type] = exts[0];
	    }
	  }
	};

	/**
	 * Load an Apache2-style ".types" file
	 *
	 * This may be called multiple times (it's expected).  Where files declare
	 * overlapping types/extensions, the last file wins.
	 *
	 * @param file (String) path of file to load.
	 */
	Mime.prototype.load = function(file) {
	  this._loading = file;
	  // Read file and split into lines
	  var map = {},
	      content = fs.readFileSync(file, 'ascii'),
	      lines = content.split(/[\r\n]+/);

	  lines.forEach(function(line) {
	    // Clean up whitespace/comments, and split into fields
	    var fields = line.replace(/\s*#.*|^\s*|\s*$/g, '').split(/\s+/);
	    map[fields.shift()] = fields;
	  });

	  this.define(map);

	  this._loading = null;
	};

	/**
	 * Lookup a mime type based on extension
	 */
	Mime.prototype.lookup = function(path, fallback) {
	  var ext = path.replace(/.*[\.\/\\]/, '').toLowerCase();

	  return this.types[ext] || fallback || this.default_type;
	};

	/**
	 * Return file extension associated with a mime type
	 */
	Mime.prototype.extension = function(mimeType) {
	  var type = mimeType.match(/^\s*([^;\s]*)(?:;|\s|$)/)[1].toLowerCase();
	  return this.extensions[type];
	};

	// Default instance
	var mime = new Mime();

	// Define built-in types
	mime.define(__webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"./types.json\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())));

	// Default type
	mime.default_type = mime.lookup('bin');

	//
	// Additional API specific to the default instance
	//

	mime.Mime = Mime;

	/**
	 * Lookup a charset based on mime type.
	 */
	mime.charsets = {
	  lookup: function(mimeType, fallback) {
	    // Assume text types are utf8
	    return (/^text\//).test(mimeType) ? 'UTF-8' : fallback;
	  }
	};

	module.exports = mime;

	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(31)))

/***/ },
/* 31 */
/***/ function(module, exports) {

	// shim for using process in browser

	var process = module.exports = {};
	var queue = [];
	var draining = false;
	var currentQueue;
	var queueIndex = -1;

	function cleanUpNextTick() {
	    draining = false;
	    if (currentQueue.length) {
	        queue = currentQueue.concat(queue);
	    } else {
	        queueIndex = -1;
	    }
	    if (queue.length) {
	        drainQueue();
	    }
	}

	function drainQueue() {
	    if (draining) {
	        return;
	    }
	    var timeout = setTimeout(cleanUpNextTick);
	    draining = true;

	    var len = queue.length;
	    while(len) {
	        currentQueue = queue;
	        queue = [];
	        while (++queueIndex < len) {
	            if (currentQueue) {
	                currentQueue[queueIndex].run();
	            }
	        }
	        queueIndex = -1;
	        len = queue.length;
	    }
	    currentQueue = null;
	    draining = false;
	    clearTimeout(timeout);
	}

	process.nextTick = function (fun) {
	    var args = new Array(arguments.length - 1);
	    if (arguments.length > 1) {
	        for (var i = 1; i < arguments.length; i++) {
	            args[i - 1] = arguments[i];
	        }
	    }
	    queue.push(new Item(fun, args));
	    if (queue.length === 1 && !draining) {
	        setTimeout(drainQueue, 0);
	    }
	};

	// v8 likes predictible objects
	function Item(fun, array) {
	    this.fun = fun;
	    this.array = array;
	}
	Item.prototype.run = function () {
	    this.fun.apply(null, this.array);
	};
	process.title = 'browser';
	process.browser = true;
	process.env = {};
	process.argv = [];
	process.version = ''; // empty string to avoid regexp issues
	process.versions = {};

	function noop() {}

	process.on = noop;
	process.addListener = noop;
	process.once = noop;
	process.off = noop;
	process.removeListener = noop;
	process.removeAllListeners = noop;
	process.emit = noop;

	process.binding = function (name) {
	    throw new Error('process.binding is not supported');
	};

	process.cwd = function () { return '/' };
	process.chdir = function (dir) {
	    throw new Error('process.chdir is not supported');
	};
	process.umask = function() { return 0; };


/***/ },
/* 32 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(process) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	// resolves . and .. elements in a path array with directory names there
	// must be no slashes, empty elements, or device names (c:\) in the array
	// (so also no leading and trailing slashes - it does not distinguish
	// relative and absolute paths)
	function normalizeArray(parts, allowAboveRoot) {
	  // if the path tries to go above the root, `up` ends up > 0
	  var up = 0;
	  for (var i = parts.length - 1; i >= 0; i--) {
	    var last = parts[i];
	    if (last === '.') {
	      parts.splice(i, 1);
	    } else if (last === '..') {
	      parts.splice(i, 1);
	      up++;
	    } else if (up) {
	      parts.splice(i, 1);
	      up--;
	    }
	  }

	  // if the path is allowed to go above the root, restore leading ..s
	  if (allowAboveRoot) {
	    for (; up--; up) {
	      parts.unshift('..');
	    }
	  }

	  return parts;
	}

	// Split a filename into [root, dir, basename, ext], unix version
	// 'root' is just a slash, or nothing.
	var splitPathRe =
	    /^(\/?|)([\s\S]*?)((?:\.{1,2}|[^\/]+?|)(\.[^.\/]*|))(?:[\/]*)$/;
	var splitPath = function(filename) {
	  return splitPathRe.exec(filename).slice(1);
	};

	// path.resolve([from ...], to)
	// posix version
	exports.resolve = function() {
	  var resolvedPath = '',
	      resolvedAbsolute = false;

	  for (var i = arguments.length - 1; i >= -1 && !resolvedAbsolute; i--) {
	    var path = (i >= 0) ? arguments[i] : process.cwd();

	    // Skip empty and invalid entries
	    if (typeof path !== 'string') {
	      throw new TypeError('Arguments to path.resolve must be strings');
	    } else if (!path) {
	      continue;
	    }

	    resolvedPath = path + '/' + resolvedPath;
	    resolvedAbsolute = path.charAt(0) === '/';
	  }

	  // At this point the path should be resolved to a full absolute path, but
	  // handle relative paths to be safe (might happen when process.cwd() fails)

	  // Normalize the path
	  resolvedPath = normalizeArray(filter(resolvedPath.split('/'), function(p) {
	    return !!p;
	  }), !resolvedAbsolute).join('/');

	  return ((resolvedAbsolute ? '/' : '') + resolvedPath) || '.';
	};

	// path.normalize(path)
	// posix version
	exports.normalize = function(path) {
	  var isAbsolute = exports.isAbsolute(path),
	      trailingSlash = substr(path, -1) === '/';

	  // Normalize the path
	  path = normalizeArray(filter(path.split('/'), function(p) {
	    return !!p;
	  }), !isAbsolute).join('/');

	  if (!path && !isAbsolute) {
	    path = '.';
	  }
	  if (path && trailingSlash) {
	    path += '/';
	  }

	  return (isAbsolute ? '/' : '') + path;
	};

	// posix version
	exports.isAbsolute = function(path) {
	  return path.charAt(0) === '/';
	};

	// posix version
	exports.join = function() {
	  var paths = Array.prototype.slice.call(arguments, 0);
	  return exports.normalize(filter(paths, function(p, index) {
	    if (typeof p !== 'string') {
	      throw new TypeError('Arguments to path.join must be strings');
	    }
	    return p;
	  }).join('/'));
	};


	// path.relative(from, to)
	// posix version
	exports.relative = function(from, to) {
	  from = exports.resolve(from).substr(1);
	  to = exports.resolve(to).substr(1);

	  function trim(arr) {
	    var start = 0;
	    for (; start < arr.length; start++) {
	      if (arr[start] !== '') break;
	    }

	    var end = arr.length - 1;
	    for (; end >= 0; end--) {
	      if (arr[end] !== '') break;
	    }

	    if (start > end) return [];
	    return arr.slice(start, end - start + 1);
	  }

	  var fromParts = trim(from.split('/'));
	  var toParts = trim(to.split('/'));

	  var length = Math.min(fromParts.length, toParts.length);
	  var samePartsLength = length;
	  for (var i = 0; i < length; i++) {
	    if (fromParts[i] !== toParts[i]) {
	      samePartsLength = i;
	      break;
	    }
	  }

	  var outputParts = [];
	  for (var i = samePartsLength; i < fromParts.length; i++) {
	    outputParts.push('..');
	  }

	  outputParts = outputParts.concat(toParts.slice(samePartsLength));

	  return outputParts.join('/');
	};

	exports.sep = '/';
	exports.delimiter = ':';

	exports.dirname = function(path) {
	  var result = splitPath(path),
	      root = result[0],
	      dir = result[1];

	  if (!root && !dir) {
	    // No dirname whatsoever
	    return '.';
	  }

	  if (dir) {
	    // It has a dirname, strip trailing slash
	    dir = dir.substr(0, dir.length - 1);
	  }

	  return root + dir;
	};


	exports.basename = function(path, ext) {
	  var f = splitPath(path)[2];
	  // TODO: make this comparison case-insensitive on windows?
	  if (ext && f.substr(-1 * ext.length) === ext) {
	    f = f.substr(0, f.length - ext.length);
	  }
	  return f;
	};


	exports.extname = function(path) {
	  return splitPath(path)[3];
	};

	function filter (xs, f) {
	    if (xs.filter) return xs.filter(f);
	    var res = [];
	    for (var i = 0; i < xs.length; i++) {
	        if (f(xs[i], i, xs)) res.push(xs[i]);
	    }
	    return res;
	}

	// String.prototype.substr - negative index don't work in IE8
	var substr = 'ab'.substr(-1) === 'b'
	    ? function (str, start, len) { return str.substr(start, len) }
	    : function (str, start, len) {
	        if (start < 0) start = str.length + start;
	        return str.substr(start, len);
	    }
	;

	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(31)))

/***/ },
/* 33 */,
/* 34 */
/***/ function(module, exports, __webpack_require__) {

	var Stream = __webpack_require__(35).Stream;
	var util = __webpack_require__(57);

	module.exports = DelayedStream;
	function DelayedStream() {
	  this.source = null;
	  this.dataSize = 0;
	  this.maxDataSize = 1024 * 1024;
	  this.pauseStream = true;

	  this._maxDataSizeExceeded = false;
	  this._released = false;
	  this._bufferedEvents = [];
	}
	util.inherits(DelayedStream, Stream);

	DelayedStream.create = function(source, options) {
	  var delayedStream = new this();

	  options = options || {};
	  for (var option in options) {
	    delayedStream[option] = options[option];
	  }

	  delayedStream.source = source;

	  var realEmit = source.emit;
	  source.emit = function() {
	    delayedStream._handleEmit(arguments);
	    return realEmit.apply(source, arguments);
	  };

	  source.on('error', function() {});
	  if (delayedStream.pauseStream) {
	    source.pause();
	  }

	  return delayedStream;
	};

	DelayedStream.prototype.__defineGetter__('readable', function() {
	  return this.source.readable;
	});

	DelayedStream.prototype.resume = function() {
	  if (!this._released) {
	    this.release();
	  }

	  this.source.resume();
	};

	DelayedStream.prototype.pause = function() {
	  this.source.pause();
	};

	DelayedStream.prototype.release = function() {
	  this._released = true;

	  this._bufferedEvents.forEach(function(args) {
	    this.emit.apply(this, args);
	  }.bind(this));
	  this._bufferedEvents = [];
	};

	DelayedStream.prototype.pipe = function() {
	  var r = Stream.prototype.pipe.apply(this, arguments);
	  this.resume();
	  return r;
	};

	DelayedStream.prototype._handleEmit = function(args) {
	  if (this._released) {
	    this.emit.apply(this, args);
	    return;
	  }

	  if (args[0] === 'data') {
	    this.dataSize += args[1].length;
	    this._checkIfMaxDataSizeExceeded();
	  }

	  this._bufferedEvents.push(args);
	};

	DelayedStream.prototype._checkIfMaxDataSizeExceeded = function() {
	  if (this._maxDataSizeExceeded) {
	    return;
	  }

	  if (this.dataSize <= this.maxDataSize) {
	    return;
	  }

	  this._maxDataSizeExceeded = true;
	  var message =
	    'DelayedStream#maxDataSize of ' + this.maxDataSize + ' bytes exceeded.'
	  this.emit('error', new Error(message));
	};


/***/ },
/* 35 */
/***/ function(module, exports, __webpack_require__) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	module.exports = Stream;

	var EE = __webpack_require__(36).EventEmitter;
	var inherits = __webpack_require__(37);

	inherits(Stream, EE);
	Stream.Readable = __webpack_require__(38);
	Stream.Writable = __webpack_require__(53);
	Stream.Duplex = __webpack_require__(54);
	Stream.Transform = __webpack_require__(55);
	Stream.PassThrough = __webpack_require__(56);

	// Backwards-compat with node 0.4.x
	Stream.Stream = Stream;



	// old-style streams.  Note that the pipe method (the only relevant
	// part of this class) is overridden in the Readable class.

	function Stream() {
	  EE.call(this);
	}

	Stream.prototype.pipe = function(dest, options) {
	  var source = this;

	  function ondata(chunk) {
	    if (dest.writable) {
	      if (false === dest.write(chunk) && source.pause) {
	        source.pause();
	      }
	    }
	  }

	  source.on('data', ondata);

	  function ondrain() {
	    if (source.readable && source.resume) {
	      source.resume();
	    }
	  }

	  dest.on('drain', ondrain);

	  // If the 'end' option is not supplied, dest.end() will be called when
	  // source gets the 'end' or 'close' events.  Only dest.end() once.
	  if (!dest._isStdio && (!options || options.end !== false)) {
	    source.on('end', onend);
	    source.on('close', onclose);
	  }

	  var didOnEnd = false;
	  function onend() {
	    if (didOnEnd) return;
	    didOnEnd = true;

	    dest.end();
	  }


	  function onclose() {
	    if (didOnEnd) return;
	    didOnEnd = true;

	    if (typeof dest.destroy === 'function') dest.destroy();
	  }

	  // don't leave dangling pipes when there are errors.
	  function onerror(er) {
	    cleanup();
	    if (EE.listenerCount(this, 'error') === 0) {
	      throw er; // Unhandled stream error in pipe.
	    }
	  }

	  source.on('error', onerror);
	  dest.on('error', onerror);

	  // remove all the event listeners that were added.
	  function cleanup() {
	    source.removeListener('data', ondata);
	    dest.removeListener('drain', ondrain);

	    source.removeListener('end', onend);
	    source.removeListener('close', onclose);

	    source.removeListener('error', onerror);
	    dest.removeListener('error', onerror);

	    source.removeListener('end', cleanup);
	    source.removeListener('close', cleanup);

	    dest.removeListener('close', cleanup);
	  }

	  source.on('end', cleanup);
	  source.on('close', cleanup);

	  dest.on('close', cleanup);

	  dest.emit('pipe', source);

	  // Allow for unix-like usage: A.pipe(B).pipe(C)
	  return dest;
	};


/***/ },
/* 36 */
/***/ function(module, exports) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	function EventEmitter() {
	  this._events = this._events || {};
	  this._maxListeners = this._maxListeners || undefined;
	}
	module.exports = EventEmitter;

	// Backwards-compat with node 0.10.x
	EventEmitter.EventEmitter = EventEmitter;

	EventEmitter.prototype._events = undefined;
	EventEmitter.prototype._maxListeners = undefined;

	// By default EventEmitters will print a warning if more than 10 listeners are
	// added to it. This is a useful default which helps finding memory leaks.
	EventEmitter.defaultMaxListeners = 10;

	// Obviously not all Emitters should be limited to 10. This function allows
	// that to be increased. Set to zero for unlimited.
	EventEmitter.prototype.setMaxListeners = function(n) {
	  if (!isNumber(n) || n < 0 || isNaN(n))
	    throw TypeError('n must be a positive number');
	  this._maxListeners = n;
	  return this;
	};

	EventEmitter.prototype.emit = function(type) {
	  var er, handler, len, args, i, listeners;

	  if (!this._events)
	    this._events = {};

	  // If there is no 'error' event listener then throw.
	  if (type === 'error') {
	    if (!this._events.error ||
	        (isObject(this._events.error) && !this._events.error.length)) {
	      er = arguments[1];
	      if (er instanceof Error) {
	        throw er; // Unhandled 'error' event
	      }
	      throw TypeError('Uncaught, unspecified "error" event.');
	    }
	  }

	  handler = this._events[type];

	  if (isUndefined(handler))
	    return false;

	  if (isFunction(handler)) {
	    switch (arguments.length) {
	      // fast cases
	      case 1:
	        handler.call(this);
	        break;
	      case 2:
	        handler.call(this, arguments[1]);
	        break;
	      case 3:
	        handler.call(this, arguments[1], arguments[2]);
	        break;
	      // slower
	      default:
	        args = Array.prototype.slice.call(arguments, 1);
	        handler.apply(this, args);
	    }
	  } else if (isObject(handler)) {
	    args = Array.prototype.slice.call(arguments, 1);
	    listeners = handler.slice();
	    len = listeners.length;
	    for (i = 0; i < len; i++)
	      listeners[i].apply(this, args);
	  }

	  return true;
	};

	EventEmitter.prototype.addListener = function(type, listener) {
	  var m;

	  if (!isFunction(listener))
	    throw TypeError('listener must be a function');

	  if (!this._events)
	    this._events = {};

	  // To avoid recursion in the case that type === "newListener"! Before
	  // adding it to the listeners, first emit "newListener".
	  if (this._events.newListener)
	    this.emit('newListener', type,
	              isFunction(listener.listener) ?
	              listener.listener : listener);

	  if (!this._events[type])
	    // Optimize the case of one listener. Don't need the extra array object.
	    this._events[type] = listener;
	  else if (isObject(this._events[type]))
	    // If we've already got an array, just append.
	    this._events[type].push(listener);
	  else
	    // Adding the second element, need to change to array.
	    this._events[type] = [this._events[type], listener];

	  // Check for listener leak
	  if (isObject(this._events[type]) && !this._events[type].warned) {
	    if (!isUndefined(this._maxListeners)) {
	      m = this._maxListeners;
	    } else {
	      m = EventEmitter.defaultMaxListeners;
	    }

	    if (m && m > 0 && this._events[type].length > m) {
	      this._events[type].warned = true;
	      console.error('(node) warning: possible EventEmitter memory ' +
	                    'leak detected. %d listeners added. ' +
	                    'Use emitter.setMaxListeners() to increase limit.',
	                    this._events[type].length);
	      if (typeof console.trace === 'function') {
	        // not supported in IE 10
	        console.trace();
	      }
	    }
	  }

	  return this;
	};

	EventEmitter.prototype.on = EventEmitter.prototype.addListener;

	EventEmitter.prototype.once = function(type, listener) {
	  if (!isFunction(listener))
	    throw TypeError('listener must be a function');

	  var fired = false;

	  function g() {
	    this.removeListener(type, g);

	    if (!fired) {
	      fired = true;
	      listener.apply(this, arguments);
	    }
	  }

	  g.listener = listener;
	  this.on(type, g);

	  return this;
	};

	// emits a 'removeListener' event iff the listener was removed
	EventEmitter.prototype.removeListener = function(type, listener) {
	  var list, position, length, i;

	  if (!isFunction(listener))
	    throw TypeError('listener must be a function');

	  if (!this._events || !this._events[type])
	    return this;

	  list = this._events[type];
	  length = list.length;
	  position = -1;

	  if (list === listener ||
	      (isFunction(list.listener) && list.listener === listener)) {
	    delete this._events[type];
	    if (this._events.removeListener)
	      this.emit('removeListener', type, listener);

	  } else if (isObject(list)) {
	    for (i = length; i-- > 0;) {
	      if (list[i] === listener ||
	          (list[i].listener && list[i].listener === listener)) {
	        position = i;
	        break;
	      }
	    }

	    if (position < 0)
	      return this;

	    if (list.length === 1) {
	      list.length = 0;
	      delete this._events[type];
	    } else {
	      list.splice(position, 1);
	    }

	    if (this._events.removeListener)
	      this.emit('removeListener', type, listener);
	  }

	  return this;
	};

	EventEmitter.prototype.removeAllListeners = function(type) {
	  var key, listeners;

	  if (!this._events)
	    return this;

	  // not listening for removeListener, no need to emit
	  if (!this._events.removeListener) {
	    if (arguments.length === 0)
	      this._events = {};
	    else if (this._events[type])
	      delete this._events[type];
	    return this;
	  }

	  // emit removeListener for all listeners on all events
	  if (arguments.length === 0) {
	    for (key in this._events) {
	      if (key === 'removeListener') continue;
	      this.removeAllListeners(key);
	    }
	    this.removeAllListeners('removeListener');
	    this._events = {};
	    return this;
	  }

	  listeners = this._events[type];

	  if (isFunction(listeners)) {
	    this.removeListener(type, listeners);
	  } else if (listeners) {
	    // LIFO order
	    while (listeners.length)
	      this.removeListener(type, listeners[listeners.length - 1]);
	  }
	  delete this._events[type];

	  return this;
	};

	EventEmitter.prototype.listeners = function(type) {
	  var ret;
	  if (!this._events || !this._events[type])
	    ret = [];
	  else if (isFunction(this._events[type]))
	    ret = [this._events[type]];
	  else
	    ret = this._events[type].slice();
	  return ret;
	};

	EventEmitter.prototype.listenerCount = function(type) {
	  if (this._events) {
	    var evlistener = this._events[type];

	    if (isFunction(evlistener))
	      return 1;
	    else if (evlistener)
	      return evlistener.length;
	  }
	  return 0;
	};

	EventEmitter.listenerCount = function(emitter, type) {
	  return emitter.listenerCount(type);
	};

	function isFunction(arg) {
	  return typeof arg === 'function';
	}

	function isNumber(arg) {
	  return typeof arg === 'number';
	}

	function isObject(arg) {
	  return typeof arg === 'object' && arg !== null;
	}

	function isUndefined(arg) {
	  return arg === void 0;
	}


/***/ },
/* 37 */
/***/ function(module, exports) {

	if (typeof Object.create === 'function') {
	  // implementation from standard node.js 'util' module
	  module.exports = function inherits(ctor, superCtor) {
	    ctor.super_ = superCtor
	    ctor.prototype = Object.create(superCtor.prototype, {
	      constructor: {
	        value: ctor,
	        enumerable: false,
	        writable: true,
	        configurable: true
	      }
	    });
	  };
	} else {
	  // old school shim for old browsers
	  module.exports = function inherits(ctor, superCtor) {
	    ctor.super_ = superCtor
	    var TempCtor = function () {}
	    TempCtor.prototype = superCtor.prototype
	    ctor.prototype = new TempCtor()
	    ctor.prototype.constructor = ctor
	  }
	}


/***/ },
/* 38 */
/***/ function(module, exports, __webpack_require__) {

	exports = module.exports = __webpack_require__(39);
	exports.Stream = __webpack_require__(35);
	exports.Readable = exports;
	exports.Writable = __webpack_require__(49);
	exports.Duplex = __webpack_require__(48);
	exports.Transform = __webpack_require__(51);
	exports.PassThrough = __webpack_require__(52);


/***/ },
/* 39 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(process) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	module.exports = Readable;

	/*<replacement>*/
	var isArray = __webpack_require__(40);
	/*</replacement>*/


	/*<replacement>*/
	var Buffer = __webpack_require__(41).Buffer;
	/*</replacement>*/

	Readable.ReadableState = ReadableState;

	var EE = __webpack_require__(36).EventEmitter;

	/*<replacement>*/
	if (!EE.listenerCount) EE.listenerCount = function(emitter, type) {
	  return emitter.listeners(type).length;
	};
	/*</replacement>*/

	var Stream = __webpack_require__(35);

	/*<replacement>*/
	var util = __webpack_require__(45);
	util.inherits = __webpack_require__(46);
	/*</replacement>*/

	var StringDecoder;


	/*<replacement>*/
	var debug = __webpack_require__(47);
	if (debug && debug.debuglog) {
	  debug = debug.debuglog('stream');
	} else {
	  debug = function () {};
	}
	/*</replacement>*/


	util.inherits(Readable, Stream);

	function ReadableState(options, stream) {
	  var Duplex = __webpack_require__(48);

	  options = options || {};

	  // the point at which it stops calling _read() to fill the buffer
	  // Note: 0 is a valid value, means "don't call _read preemptively ever"
	  var hwm = options.highWaterMark;
	  var defaultHwm = options.objectMode ? 16 : 16 * 1024;
	  this.highWaterMark = (hwm || hwm === 0) ? hwm : defaultHwm;

	  // cast to ints.
	  this.highWaterMark = ~~this.highWaterMark;

	  this.buffer = [];
	  this.length = 0;
	  this.pipes = null;
	  this.pipesCount = 0;
	  this.flowing = null;
	  this.ended = false;
	  this.endEmitted = false;
	  this.reading = false;

	  // a flag to be able to tell if the onwrite cb is called immediately,
	  // or on a later tick.  We set this to true at first, because any
	  // actions that shouldn't happen until "later" should generally also
	  // not happen before the first write call.
	  this.sync = true;

	  // whenever we return null, then we set a flag to say
	  // that we're awaiting a 'readable' event emission.
	  this.needReadable = false;
	  this.emittedReadable = false;
	  this.readableListening = false;


	  // object stream flag. Used to make read(n) ignore n and to
	  // make all the buffer merging and length checks go away
	  this.objectMode = !!options.objectMode;

	  if (stream instanceof Duplex)
	    this.objectMode = this.objectMode || !!options.readableObjectMode;

	  // Crypto is kind of old and crusty.  Historically, its default string
	  // encoding is 'binary' so we have to make this configurable.
	  // Everything else in the universe uses 'utf8', though.
	  this.defaultEncoding = options.defaultEncoding || 'utf8';

	  // when piping, we only care about 'readable' events that happen
	  // after read()ing all the bytes and not getting any pushback.
	  this.ranOut = false;

	  // the number of writers that are awaiting a drain event in .pipe()s
	  this.awaitDrain = 0;

	  // if true, a maybeReadMore has been scheduled
	  this.readingMore = false;

	  this.decoder = null;
	  this.encoding = null;
	  if (options.encoding) {
	    if (!StringDecoder)
	      StringDecoder = __webpack_require__(50).StringDecoder;
	    this.decoder = new StringDecoder(options.encoding);
	    this.encoding = options.encoding;
	  }
	}

	function Readable(options) {
	  var Duplex = __webpack_require__(48);

	  if (!(this instanceof Readable))
	    return new Readable(options);

	  this._readableState = new ReadableState(options, this);

	  // legacy
	  this.readable = true;

	  Stream.call(this);
	}

	// Manually shove something into the read() buffer.
	// This returns true if the highWaterMark has not been hit yet,
	// similar to how Writable.write() returns true if you should
	// write() some more.
	Readable.prototype.push = function(chunk, encoding) {
	  var state = this._readableState;

	  if (util.isString(chunk) && !state.objectMode) {
	    encoding = encoding || state.defaultEncoding;
	    if (encoding !== state.encoding) {
	      chunk = new Buffer(chunk, encoding);
	      encoding = '';
	    }
	  }

	  return readableAddChunk(this, state, chunk, encoding, false);
	};

	// Unshift should *always* be something directly out of read()
	Readable.prototype.unshift = function(chunk) {
	  var state = this._readableState;
	  return readableAddChunk(this, state, chunk, '', true);
	};

	function readableAddChunk(stream, state, chunk, encoding, addToFront) {
	  var er = chunkInvalid(state, chunk);
	  if (er) {
	    stream.emit('error', er);
	  } else if (util.isNullOrUndefined(chunk)) {
	    state.reading = false;
	    if (!state.ended)
	      onEofChunk(stream, state);
	  } else if (state.objectMode || chunk && chunk.length > 0) {
	    if (state.ended && !addToFront) {
	      var e = new Error('stream.push() after EOF');
	      stream.emit('error', e);
	    } else if (state.endEmitted && addToFront) {
	      var e = new Error('stream.unshift() after end event');
	      stream.emit('error', e);
	    } else {
	      if (state.decoder && !addToFront && !encoding)
	        chunk = state.decoder.write(chunk);

	      if (!addToFront)
	        state.reading = false;

	      // if we want the data now, just emit it.
	      if (state.flowing && state.length === 0 && !state.sync) {
	        stream.emit('data', chunk);
	        stream.read(0);
	      } else {
	        // update the buffer info.
	        state.length += state.objectMode ? 1 : chunk.length;
	        if (addToFront)
	          state.buffer.unshift(chunk);
	        else
	          state.buffer.push(chunk);

	        if (state.needReadable)
	          emitReadable(stream);
	      }

	      maybeReadMore(stream, state);
	    }
	  } else if (!addToFront) {
	    state.reading = false;
	  }

	  return needMoreData(state);
	}



	// if it's past the high water mark, we can push in some more.
	// Also, if we have no data yet, we can stand some
	// more bytes.  This is to work around cases where hwm=0,
	// such as the repl.  Also, if the push() triggered a
	// readable event, and the user called read(largeNumber) such that
	// needReadable was set, then we ought to push more, so that another
	// 'readable' event will be triggered.
	function needMoreData(state) {
	  return !state.ended &&
	         (state.needReadable ||
	          state.length < state.highWaterMark ||
	          state.length === 0);
	}

	// backwards compatibility.
	Readable.prototype.setEncoding = function(enc) {
	  if (!StringDecoder)
	    StringDecoder = __webpack_require__(50).StringDecoder;
	  this._readableState.decoder = new StringDecoder(enc);
	  this._readableState.encoding = enc;
	  return this;
	};

	// Don't raise the hwm > 128MB
	var MAX_HWM = 0x800000;
	function roundUpToNextPowerOf2(n) {
	  if (n >= MAX_HWM) {
	    n = MAX_HWM;
	  } else {
	    // Get the next highest power of 2
	    n--;
	    for (var p = 1; p < 32; p <<= 1) n |= n >> p;
	    n++;
	  }
	  return n;
	}

	function howMuchToRead(n, state) {
	  if (state.length === 0 && state.ended)
	    return 0;

	  if (state.objectMode)
	    return n === 0 ? 0 : 1;

	  if (isNaN(n) || util.isNull(n)) {
	    // only flow one buffer at a time
	    if (state.flowing && state.buffer.length)
	      return state.buffer[0].length;
	    else
	      return state.length;
	  }

	  if (n <= 0)
	    return 0;

	  // If we're asking for more than the target buffer level,
	  // then raise the water mark.  Bump up to the next highest
	  // power of 2, to prevent increasing it excessively in tiny
	  // amounts.
	  if (n > state.highWaterMark)
	    state.highWaterMark = roundUpToNextPowerOf2(n);

	  // don't have that much.  return null, unless we've ended.
	  if (n > state.length) {
	    if (!state.ended) {
	      state.needReadable = true;
	      return 0;
	    } else
	      return state.length;
	  }

	  return n;
	}

	// you can override either this method, or the async _read(n) below.
	Readable.prototype.read = function(n) {
	  debug('read', n);
	  var state = this._readableState;
	  var nOrig = n;

	  if (!util.isNumber(n) || n > 0)
	    state.emittedReadable = false;

	  // if we're doing read(0) to trigger a readable event, but we
	  // already have a bunch of data in the buffer, then just trigger
	  // the 'readable' event and move on.
	  if (n === 0 &&
	      state.needReadable &&
	      (state.length >= state.highWaterMark || state.ended)) {
	    debug('read: emitReadable', state.length, state.ended);
	    if (state.length === 0 && state.ended)
	      endReadable(this);
	    else
	      emitReadable(this);
	    return null;
	  }

	  n = howMuchToRead(n, state);

	  // if we've ended, and we're now clear, then finish it up.
	  if (n === 0 && state.ended) {
	    if (state.length === 0)
	      endReadable(this);
	    return null;
	  }

	  // All the actual chunk generation logic needs to be
	  // *below* the call to _read.  The reason is that in certain
	  // synthetic stream cases, such as passthrough streams, _read
	  // may be a completely synchronous operation which may change
	  // the state of the read buffer, providing enough data when
	  // before there was *not* enough.
	  //
	  // So, the steps are:
	  // 1. Figure out what the state of things will be after we do
	  // a read from the buffer.
	  //
	  // 2. If that resulting state will trigger a _read, then call _read.
	  // Note that this may be asynchronous, or synchronous.  Yes, it is
	  // deeply ugly to write APIs this way, but that still doesn't mean
	  // that the Readable class should behave improperly, as streams are
	  // designed to be sync/async agnostic.
	  // Take note if the _read call is sync or async (ie, if the read call
	  // has returned yet), so that we know whether or not it's safe to emit
	  // 'readable' etc.
	  //
	  // 3. Actually pull the requested chunks out of the buffer and return.

	  // if we need a readable event, then we need to do some reading.
	  var doRead = state.needReadable;
	  debug('need readable', doRead);

	  // if we currently have less than the highWaterMark, then also read some
	  if (state.length === 0 || state.length - n < state.highWaterMark) {
	    doRead = true;
	    debug('length less than watermark', doRead);
	  }

	  // however, if we've ended, then there's no point, and if we're already
	  // reading, then it's unnecessary.
	  if (state.ended || state.reading) {
	    doRead = false;
	    debug('reading or ended', doRead);
	  }

	  if (doRead) {
	    debug('do read');
	    state.reading = true;
	    state.sync = true;
	    // if the length is currently zero, then we *need* a readable event.
	    if (state.length === 0)
	      state.needReadable = true;
	    // call internal read method
	    this._read(state.highWaterMark);
	    state.sync = false;
	  }

	  // If _read pushed data synchronously, then `reading` will be false,
	  // and we need to re-evaluate how much data we can return to the user.
	  if (doRead && !state.reading)
	    n = howMuchToRead(nOrig, state);

	  var ret;
	  if (n > 0)
	    ret = fromList(n, state);
	  else
	    ret = null;

	  if (util.isNull(ret)) {
	    state.needReadable = true;
	    n = 0;
	  }

	  state.length -= n;

	  // If we have nothing in the buffer, then we want to know
	  // as soon as we *do* get something into the buffer.
	  if (state.length === 0 && !state.ended)
	    state.needReadable = true;

	  // If we tried to read() past the EOF, then emit end on the next tick.
	  if (nOrig !== n && state.ended && state.length === 0)
	    endReadable(this);

	  if (!util.isNull(ret))
	    this.emit('data', ret);

	  return ret;
	};

	function chunkInvalid(state, chunk) {
	  var er = null;
	  if (!util.isBuffer(chunk) &&
	      !util.isString(chunk) &&
	      !util.isNullOrUndefined(chunk) &&
	      !state.objectMode) {
	    er = new TypeError('Invalid non-string/buffer chunk');
	  }
	  return er;
	}


	function onEofChunk(stream, state) {
	  if (state.decoder && !state.ended) {
	    var chunk = state.decoder.end();
	    if (chunk && chunk.length) {
	      state.buffer.push(chunk);
	      state.length += state.objectMode ? 1 : chunk.length;
	    }
	  }
	  state.ended = true;

	  // emit 'readable' now to make sure it gets picked up.
	  emitReadable(stream);
	}

	// Don't emit readable right away in sync mode, because this can trigger
	// another read() call => stack overflow.  This way, it might trigger
	// a nextTick recursion warning, but that's not so bad.
	function emitReadable(stream) {
	  var state = stream._readableState;
	  state.needReadable = false;
	  if (!state.emittedReadable) {
	    debug('emitReadable', state.flowing);
	    state.emittedReadable = true;
	    if (state.sync)
	      process.nextTick(function() {
	        emitReadable_(stream);
	      });
	    else
	      emitReadable_(stream);
	  }
	}

	function emitReadable_(stream) {
	  debug('emit readable');
	  stream.emit('readable');
	  flow(stream);
	}


	// at this point, the user has presumably seen the 'readable' event,
	// and called read() to consume some data.  that may have triggered
	// in turn another _read(n) call, in which case reading = true if
	// it's in progress.
	// However, if we're not ended, or reading, and the length < hwm,
	// then go ahead and try to read some more preemptively.
	function maybeReadMore(stream, state) {
	  if (!state.readingMore) {
	    state.readingMore = true;
	    process.nextTick(function() {
	      maybeReadMore_(stream, state);
	    });
	  }
	}

	function maybeReadMore_(stream, state) {
	  var len = state.length;
	  while (!state.reading && !state.flowing && !state.ended &&
	         state.length < state.highWaterMark) {
	    debug('maybeReadMore read 0');
	    stream.read(0);
	    if (len === state.length)
	      // didn't get any data, stop spinning.
	      break;
	    else
	      len = state.length;
	  }
	  state.readingMore = false;
	}

	// abstract method.  to be overridden in specific implementation classes.
	// call cb(er, data) where data is <= n in length.
	// for virtual (non-string, non-buffer) streams, "length" is somewhat
	// arbitrary, and perhaps not very meaningful.
	Readable.prototype._read = function(n) {
	  this.emit('error', new Error('not implemented'));
	};

	Readable.prototype.pipe = function(dest, pipeOpts) {
	  var src = this;
	  var state = this._readableState;

	  switch (state.pipesCount) {
	    case 0:
	      state.pipes = dest;
	      break;
	    case 1:
	      state.pipes = [state.pipes, dest];
	      break;
	    default:
	      state.pipes.push(dest);
	      break;
	  }
	  state.pipesCount += 1;
	  debug('pipe count=%d opts=%j', state.pipesCount, pipeOpts);

	  var doEnd = (!pipeOpts || pipeOpts.end !== false) &&
	              dest !== process.stdout &&
	              dest !== process.stderr;

	  var endFn = doEnd ? onend : cleanup;
	  if (state.endEmitted)
	    process.nextTick(endFn);
	  else
	    src.once('end', endFn);

	  dest.on('unpipe', onunpipe);
	  function onunpipe(readable) {
	    debug('onunpipe');
	    if (readable === src) {
	      cleanup();
	    }
	  }

	  function onend() {
	    debug('onend');
	    dest.end();
	  }

	  // when the dest drains, it reduces the awaitDrain counter
	  // on the source.  This would be more elegant with a .once()
	  // handler in flow(), but adding and removing repeatedly is
	  // too slow.
	  var ondrain = pipeOnDrain(src);
	  dest.on('drain', ondrain);

	  function cleanup() {
	    debug('cleanup');
	    // cleanup event handlers once the pipe is broken
	    dest.removeListener('close', onclose);
	    dest.removeListener('finish', onfinish);
	    dest.removeListener('drain', ondrain);
	    dest.removeListener('error', onerror);
	    dest.removeListener('unpipe', onunpipe);
	    src.removeListener('end', onend);
	    src.removeListener('end', cleanup);
	    src.removeListener('data', ondata);

	    // if the reader is waiting for a drain event from this
	    // specific writer, then it would cause it to never start
	    // flowing again.
	    // So, if this is awaiting a drain, then we just call it now.
	    // If we don't know, then assume that we are waiting for one.
	    if (state.awaitDrain &&
	        (!dest._writableState || dest._writableState.needDrain))
	      ondrain();
	  }

	  src.on('data', ondata);
	  function ondata(chunk) {
	    debug('ondata');
	    var ret = dest.write(chunk);
	    if (false === ret) {
	      debug('false write response, pause',
	            src._readableState.awaitDrain);
	      src._readableState.awaitDrain++;
	      src.pause();
	    }
	  }

	  // if the dest has an error, then stop piping into it.
	  // however, don't suppress the throwing behavior for this.
	  function onerror(er) {
	    debug('onerror', er);
	    unpipe();
	    dest.removeListener('error', onerror);
	    if (EE.listenerCount(dest, 'error') === 0)
	      dest.emit('error', er);
	  }
	  // This is a brutally ugly hack to make sure that our error handler
	  // is attached before any userland ones.  NEVER DO THIS.
	  if (!dest._events || !dest._events.error)
	    dest.on('error', onerror);
	  else if (isArray(dest._events.error))
	    dest._events.error.unshift(onerror);
	  else
	    dest._events.error = [onerror, dest._events.error];



	  // Both close and finish should trigger unpipe, but only once.
	  function onclose() {
	    dest.removeListener('finish', onfinish);
	    unpipe();
	  }
	  dest.once('close', onclose);
	  function onfinish() {
	    debug('onfinish');
	    dest.removeListener('close', onclose);
	    unpipe();
	  }
	  dest.once('finish', onfinish);

	  function unpipe() {
	    debug('unpipe');
	    src.unpipe(dest);
	  }

	  // tell the dest that it's being piped to
	  dest.emit('pipe', src);

	  // start the flow if it hasn't been started already.
	  if (!state.flowing) {
	    debug('pipe resume');
	    src.resume();
	  }

	  return dest;
	};

	function pipeOnDrain(src) {
	  return function() {
	    var state = src._readableState;
	    debug('pipeOnDrain', state.awaitDrain);
	    if (state.awaitDrain)
	      state.awaitDrain--;
	    if (state.awaitDrain === 0 && EE.listenerCount(src, 'data')) {
	      state.flowing = true;
	      flow(src);
	    }
	  };
	}


	Readable.prototype.unpipe = function(dest) {
	  var state = this._readableState;

	  // if we're not piping anywhere, then do nothing.
	  if (state.pipesCount === 0)
	    return this;

	  // just one destination.  most common case.
	  if (state.pipesCount === 1) {
	    // passed in one, but it's not the right one.
	    if (dest && dest !== state.pipes)
	      return this;

	    if (!dest)
	      dest = state.pipes;

	    // got a match.
	    state.pipes = null;
	    state.pipesCount = 0;
	    state.flowing = false;
	    if (dest)
	      dest.emit('unpipe', this);
	    return this;
	  }

	  // slow case. multiple pipe destinations.

	  if (!dest) {
	    // remove all.
	    var dests = state.pipes;
	    var len = state.pipesCount;
	    state.pipes = null;
	    state.pipesCount = 0;
	    state.flowing = false;

	    for (var i = 0; i < len; i++)
	      dests[i].emit('unpipe', this);
	    return this;
	  }

	  // try to find the right one.
	  var i = indexOf(state.pipes, dest);
	  if (i === -1)
	    return this;

	  state.pipes.splice(i, 1);
	  state.pipesCount -= 1;
	  if (state.pipesCount === 1)
	    state.pipes = state.pipes[0];

	  dest.emit('unpipe', this);

	  return this;
	};

	// set up data events if they are asked for
	// Ensure readable listeners eventually get something
	Readable.prototype.on = function(ev, fn) {
	  var res = Stream.prototype.on.call(this, ev, fn);

	  // If listening to data, and it has not explicitly been paused,
	  // then call resume to start the flow of data on the next tick.
	  if (ev === 'data' && false !== this._readableState.flowing) {
	    this.resume();
	  }

	  if (ev === 'readable' && this.readable) {
	    var state = this._readableState;
	    if (!state.readableListening) {
	      state.readableListening = true;
	      state.emittedReadable = false;
	      state.needReadable = true;
	      if (!state.reading) {
	        var self = this;
	        process.nextTick(function() {
	          debug('readable nexttick read 0');
	          self.read(0);
	        });
	      } else if (state.length) {
	        emitReadable(this, state);
	      }
	    }
	  }

	  return res;
	};
	Readable.prototype.addListener = Readable.prototype.on;

	// pause() and resume() are remnants of the legacy readable stream API
	// If the user uses them, then switch into old mode.
	Readable.prototype.resume = function() {
	  var state = this._readableState;
	  if (!state.flowing) {
	    debug('resume');
	    state.flowing = true;
	    if (!state.reading) {
	      debug('resume read 0');
	      this.read(0);
	    }
	    resume(this, state);
	  }
	  return this;
	};

	function resume(stream, state) {
	  if (!state.resumeScheduled) {
	    state.resumeScheduled = true;
	    process.nextTick(function() {
	      resume_(stream, state);
	    });
	  }
	}

	function resume_(stream, state) {
	  state.resumeScheduled = false;
	  stream.emit('resume');
	  flow(stream);
	  if (state.flowing && !state.reading)
	    stream.read(0);
	}

	Readable.prototype.pause = function() {
	  debug('call pause flowing=%j', this._readableState.flowing);
	  if (false !== this._readableState.flowing) {
	    debug('pause');
	    this._readableState.flowing = false;
	    this.emit('pause');
	  }
	  return this;
	};

	function flow(stream) {
	  var state = stream._readableState;
	  debug('flow', state.flowing);
	  if (state.flowing) {
	    do {
	      var chunk = stream.read();
	    } while (null !== chunk && state.flowing);
	  }
	}

	// wrap an old-style stream as the async data source.
	// This is *not* part of the readable stream interface.
	// It is an ugly unfortunate mess of history.
	Readable.prototype.wrap = function(stream) {
	  var state = this._readableState;
	  var paused = false;

	  var self = this;
	  stream.on('end', function() {
	    debug('wrapped end');
	    if (state.decoder && !state.ended) {
	      var chunk = state.decoder.end();
	      if (chunk && chunk.length)
	        self.push(chunk);
	    }

	    self.push(null);
	  });

	  stream.on('data', function(chunk) {
	    debug('wrapped data');
	    if (state.decoder)
	      chunk = state.decoder.write(chunk);
	    if (!chunk || !state.objectMode && !chunk.length)
	      return;

	    var ret = self.push(chunk);
	    if (!ret) {
	      paused = true;
	      stream.pause();
	    }
	  });

	  // proxy all the other methods.
	  // important when wrapping filters and duplexes.
	  for (var i in stream) {
	    if (util.isFunction(stream[i]) && util.isUndefined(this[i])) {
	      this[i] = function(method) { return function() {
	        return stream[method].apply(stream, arguments);
	      }}(i);
	    }
	  }

	  // proxy certain important events.
	  var events = ['error', 'close', 'destroy', 'pause', 'resume'];
	  forEach(events, function(ev) {
	    stream.on(ev, self.emit.bind(self, ev));
	  });

	  // when we try to consume some more bytes, simply unpause the
	  // underlying stream.
	  self._read = function(n) {
	    debug('wrapped _read', n);
	    if (paused) {
	      paused = false;
	      stream.resume();
	    }
	  };

	  return self;
	};



	// exposed for testing purposes only.
	Readable._fromList = fromList;

	// Pluck off n bytes from an array of buffers.
	// Length is the combined lengths of all the buffers in the list.
	function fromList(n, state) {
	  var list = state.buffer;
	  var length = state.length;
	  var stringMode = !!state.decoder;
	  var objectMode = !!state.objectMode;
	  var ret;

	  // nothing in the list, definitely empty.
	  if (list.length === 0)
	    return null;

	  if (length === 0)
	    ret = null;
	  else if (objectMode)
	    ret = list.shift();
	  else if (!n || n >= length) {
	    // read it all, truncate the array.
	    if (stringMode)
	      ret = list.join('');
	    else
	      ret = Buffer.concat(list, length);
	    list.length = 0;
	  } else {
	    // read just some of it.
	    if (n < list[0].length) {
	      // just take a part of the first list item.
	      // slice is the same for buffers and strings.
	      var buf = list[0];
	      ret = buf.slice(0, n);
	      list[0] = buf.slice(n);
	    } else if (n === list[0].length) {
	      // first list is a perfect match
	      ret = list.shift();
	    } else {
	      // complex case.
	      // we have enough to cover it, but it spans past the first buffer.
	      if (stringMode)
	        ret = '';
	      else
	        ret = new Buffer(n);

	      var c = 0;
	      for (var i = 0, l = list.length; i < l && c < n; i++) {
	        var buf = list[0];
	        var cpy = Math.min(n - c, buf.length);

	        if (stringMode)
	          ret += buf.slice(0, cpy);
	        else
	          buf.copy(ret, c, 0, cpy);

	        if (cpy < buf.length)
	          list[0] = buf.slice(cpy);
	        else
	          list.shift();

	        c += cpy;
	      }
	    }
	  }

	  return ret;
	}

	function endReadable(stream) {
	  var state = stream._readableState;

	  // If we get here before consuming all the bytes, then that is a
	  // bug in node.  Should never happen.
	  if (state.length > 0)
	    throw new Error('endReadable called on non-empty stream');

	  if (!state.endEmitted) {
	    state.ended = true;
	    process.nextTick(function() {
	      // Check that we didn't get one last unshift.
	      if (!state.endEmitted && state.length === 0) {
	        state.endEmitted = true;
	        stream.readable = false;
	        stream.emit('end');
	      }
	    });
	  }
	}

	function forEach (xs, f) {
	  for (var i = 0, l = xs.length; i < l; i++) {
	    f(xs[i], i);
	  }
	}

	function indexOf (xs, x) {
	  for (var i = 0, l = xs.length; i < l; i++) {
	    if (xs[i] === x) return i;
	  }
	  return -1;
	}

	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(31)))

/***/ },
/* 40 */
/***/ function(module, exports) {

	module.exports = Array.isArray || function (arr) {
	  return Object.prototype.toString.call(arr) == '[object Array]';
	};


/***/ },
/* 41 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(Buffer, global) {/*!
	 * The buffer module from node.js, for the browser.
	 *
	 * @author   Feross Aboukhadijeh <feross@feross.org> <http://feross.org>
	 * @license  MIT
	 */
	/* eslint-disable no-proto */

	var base64 = __webpack_require__(42)
	var ieee754 = __webpack_require__(43)
	var isArray = __webpack_require__(44)

	exports.Buffer = Buffer
	exports.SlowBuffer = SlowBuffer
	exports.INSPECT_MAX_BYTES = 50
	Buffer.poolSize = 8192 // not used by this implementation

	var rootParent = {}

	/**
	 * If `Buffer.TYPED_ARRAY_SUPPORT`:
	 *   === true    Use Uint8Array implementation (fastest)
	 *   === false   Use Object implementation (most compatible, even IE6)
	 *
	 * Browsers that support typed arrays are IE 10+, Firefox 4+, Chrome 7+, Safari 5.1+,
	 * Opera 11.6+, iOS 4.2+.
	 *
	 * Due to various browser bugs, sometimes the Object implementation will be used even
	 * when the browser supports typed arrays.
	 *
	 * Note:
	 *
	 *   - Firefox 4-29 lacks support for adding new properties to `Uint8Array` instances,
	 *     See: https://bugzilla.mozilla.org/show_bug.cgi?id=695438.
	 *
	 *   - Safari 5-7 lacks support for changing the `Object.prototype.constructor` property
	 *     on objects.
	 *
	 *   - Chrome 9-10 is missing the `TypedArray.prototype.subarray` function.
	 *
	 *   - IE10 has a broken `TypedArray.prototype.subarray` function which returns arrays of
	 *     incorrect length in some situations.

	 * We detect these buggy browsers and set `Buffer.TYPED_ARRAY_SUPPORT` to `false` so they
	 * get the Object implementation, which is slower but behaves correctly.
	 */
	Buffer.TYPED_ARRAY_SUPPORT = global.TYPED_ARRAY_SUPPORT !== undefined
	  ? global.TYPED_ARRAY_SUPPORT
	  : (function () {
	      function Bar () {}
	      try {
	        var arr = new Uint8Array(1)
	        arr.foo = function () { return 42 }
	        arr.constructor = Bar
	        return arr.foo() === 42 && // typed array instances can be augmented
	            arr.constructor === Bar && // constructor can be set
	            typeof arr.subarray === 'function' && // chrome 9-10 lack `subarray`
	            arr.subarray(1, 1).byteLength === 0 // ie10 has broken `subarray`
	      } catch (e) {
	        return false
	      }
	    })()

	function kMaxLength () {
	  return Buffer.TYPED_ARRAY_SUPPORT
	    ? 0x7fffffff
	    : 0x3fffffff
	}

	/**
	 * Class: Buffer
	 * =============
	 *
	 * The Buffer constructor returns instances of `Uint8Array` that are augmented
	 * with function properties for all the node `Buffer` API functions. We use
	 * `Uint8Array` so that square bracket notation works as expected -- it returns
	 * a single octet.
	 *
	 * By augmenting the instances, we can avoid modifying the `Uint8Array`
	 * prototype.
	 */
	function Buffer (arg) {
	  if (!(this instanceof Buffer)) {
	    // Avoid going through an ArgumentsAdaptorTrampoline in the common case.
	    if (arguments.length > 1) return new Buffer(arg, arguments[1])
	    return new Buffer(arg)
	  }

	  this.length = 0
	  this.parent = undefined

	  // Common case.
	  if (typeof arg === 'number') {
	    return fromNumber(this, arg)
	  }

	  // Slightly less common case.
	  if (typeof arg === 'string') {
	    return fromString(this, arg, arguments.length > 1 ? arguments[1] : 'utf8')
	  }

	  // Unusual.
	  return fromObject(this, arg)
	}

	function fromNumber (that, length) {
	  that = allocate(that, length < 0 ? 0 : checked(length) | 0)
	  if (!Buffer.TYPED_ARRAY_SUPPORT) {
	    for (var i = 0; i < length; i++) {
	      that[i] = 0
	    }
	  }
	  return that
	}

	function fromString (that, string, encoding) {
	  if (typeof encoding !== 'string' || encoding === '') encoding = 'utf8'

	  // Assumption: byteLength() return value is always < kMaxLength.
	  var length = byteLength(string, encoding) | 0
	  that = allocate(that, length)

	  that.write(string, encoding)
	  return that
	}

	function fromObject (that, object) {
	  if (Buffer.isBuffer(object)) return fromBuffer(that, object)

	  if (isArray(object)) return fromArray(that, object)

	  if (object == null) {
	    throw new TypeError('must start with number, buffer, array or string')
	  }

	  if (typeof ArrayBuffer !== 'undefined') {
	    if (object.buffer instanceof ArrayBuffer) {
	      return fromTypedArray(that, object)
	    }
	    if (object instanceof ArrayBuffer) {
	      return fromArrayBuffer(that, object)
	    }
	  }

	  if (object.length) return fromArrayLike(that, object)

	  return fromJsonObject(that, object)
	}

	function fromBuffer (that, buffer) {
	  var length = checked(buffer.length) | 0
	  that = allocate(that, length)
	  buffer.copy(that, 0, 0, length)
	  return that
	}

	function fromArray (that, array) {
	  var length = checked(array.length) | 0
	  that = allocate(that, length)
	  for (var i = 0; i < length; i += 1) {
	    that[i] = array[i] & 255
	  }
	  return that
	}

	// Duplicate of fromArray() to keep fromArray() monomorphic.
	function fromTypedArray (that, array) {
	  var length = checked(array.length) | 0
	  that = allocate(that, length)
	  // Truncating the elements is probably not what people expect from typed
	  // arrays with BYTES_PER_ELEMENT > 1 but it's compatible with the behavior
	  // of the old Buffer constructor.
	  for (var i = 0; i < length; i += 1) {
	    that[i] = array[i] & 255
	  }
	  return that
	}

	function fromArrayBuffer (that, array) {
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    // Return an augmented `Uint8Array` instance, for best performance
	    array.byteLength
	    that = Buffer._augment(new Uint8Array(array))
	  } else {
	    // Fallback: Return an object instance of the Buffer class
	    that = fromTypedArray(that, new Uint8Array(array))
	  }
	  return that
	}

	function fromArrayLike (that, array) {
	  var length = checked(array.length) | 0
	  that = allocate(that, length)
	  for (var i = 0; i < length; i += 1) {
	    that[i] = array[i] & 255
	  }
	  return that
	}

	// Deserialize { type: 'Buffer', data: [1,2,3,...] } into a Buffer object.
	// Returns a zero-length buffer for inputs that don't conform to the spec.
	function fromJsonObject (that, object) {
	  var array
	  var length = 0

	  if (object.type === 'Buffer' && isArray(object.data)) {
	    array = object.data
	    length = checked(array.length) | 0
	  }
	  that = allocate(that, length)

	  for (var i = 0; i < length; i += 1) {
	    that[i] = array[i] & 255
	  }
	  return that
	}

	if (Buffer.TYPED_ARRAY_SUPPORT) {
	  Buffer.prototype.__proto__ = Uint8Array.prototype
	  Buffer.__proto__ = Uint8Array
	}

	function allocate (that, length) {
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    // Return an augmented `Uint8Array` instance, for best performance
	    that = Buffer._augment(new Uint8Array(length))
	    that.__proto__ = Buffer.prototype
	  } else {
	    // Fallback: Return an object instance of the Buffer class
	    that.length = length
	    that._isBuffer = true
	  }

	  var fromPool = length !== 0 && length <= Buffer.poolSize >>> 1
	  if (fromPool) that.parent = rootParent

	  return that
	}

	function checked (length) {
	  // Note: cannot use `length < kMaxLength` here because that fails when
	  // length is NaN (which is otherwise coerced to zero.)
	  if (length >= kMaxLength()) {
	    throw new RangeError('Attempt to allocate Buffer larger than maximum ' +
	                         'size: 0x' + kMaxLength().toString(16) + ' bytes')
	  }
	  return length | 0
	}

	function SlowBuffer (subject, encoding) {
	  if (!(this instanceof SlowBuffer)) return new SlowBuffer(subject, encoding)

	  var buf = new Buffer(subject, encoding)
	  delete buf.parent
	  return buf
	}

	Buffer.isBuffer = function isBuffer (b) {
	  return !!(b != null && b._isBuffer)
	}

	Buffer.compare = function compare (a, b) {
	  if (!Buffer.isBuffer(a) || !Buffer.isBuffer(b)) {
	    throw new TypeError('Arguments must be Buffers')
	  }

	  if (a === b) return 0

	  var x = a.length
	  var y = b.length

	  var i = 0
	  var len = Math.min(x, y)
	  while (i < len) {
	    if (a[i] !== b[i]) break

	    ++i
	  }

	  if (i !== len) {
	    x = a[i]
	    y = b[i]
	  }

	  if (x < y) return -1
	  if (y < x) return 1
	  return 0
	}

	Buffer.isEncoding = function isEncoding (encoding) {
	  switch (String(encoding).toLowerCase()) {
	    case 'hex':
	    case 'utf8':
	    case 'utf-8':
	    case 'ascii':
	    case 'binary':
	    case 'base64':
	    case 'raw':
	    case 'ucs2':
	    case 'ucs-2':
	    case 'utf16le':
	    case 'utf-16le':
	      return true
	    default:
	      return false
	  }
	}

	Buffer.concat = function concat (list, length) {
	  if (!isArray(list)) throw new TypeError('list argument must be an Array of Buffers.')

	  if (list.length === 0) {
	    return new Buffer(0)
	  }

	  var i
	  if (length === undefined) {
	    length = 0
	    for (i = 0; i < list.length; i++) {
	      length += list[i].length
	    }
	  }

	  var buf = new Buffer(length)
	  var pos = 0
	  for (i = 0; i < list.length; i++) {
	    var item = list[i]
	    item.copy(buf, pos)
	    pos += item.length
	  }
	  return buf
	}

	function byteLength (string, encoding) {
	  if (typeof string !== 'string') string = '' + string

	  var len = string.length
	  if (len === 0) return 0

	  // Use a for loop to avoid recursion
	  var loweredCase = false
	  for (;;) {
	    switch (encoding) {
	      case 'ascii':
	      case 'binary':
	      // Deprecated
	      case 'raw':
	      case 'raws':
	        return len
	      case 'utf8':
	      case 'utf-8':
	        return utf8ToBytes(string).length
	      case 'ucs2':
	      case 'ucs-2':
	      case 'utf16le':
	      case 'utf-16le':
	        return len * 2
	      case 'hex':
	        return len >>> 1
	      case 'base64':
	        return base64ToBytes(string).length
	      default:
	        if (loweredCase) return utf8ToBytes(string).length // assume utf8
	        encoding = ('' + encoding).toLowerCase()
	        loweredCase = true
	    }
	  }
	}
	Buffer.byteLength = byteLength

	// pre-set for values that may exist in the future
	Buffer.prototype.length = undefined
	Buffer.prototype.parent = undefined

	function slowToString (encoding, start, end) {
	  var loweredCase = false

	  start = start | 0
	  end = end === undefined || end === Infinity ? this.length : end | 0

	  if (!encoding) encoding = 'utf8'
	  if (start < 0) start = 0
	  if (end > this.length) end = this.length
	  if (end <= start) return ''

	  while (true) {
	    switch (encoding) {
	      case 'hex':
	        return hexSlice(this, start, end)

	      case 'utf8':
	      case 'utf-8':
	        return utf8Slice(this, start, end)

	      case 'ascii':
	        return asciiSlice(this, start, end)

	      case 'binary':
	        return binarySlice(this, start, end)

	      case 'base64':
	        return base64Slice(this, start, end)

	      case 'ucs2':
	      case 'ucs-2':
	      case 'utf16le':
	      case 'utf-16le':
	        return utf16leSlice(this, start, end)

	      default:
	        if (loweredCase) throw new TypeError('Unknown encoding: ' + encoding)
	        encoding = (encoding + '').toLowerCase()
	        loweredCase = true
	    }
	  }
	}

	Buffer.prototype.toString = function toString () {
	  var length = this.length | 0
	  if (length === 0) return ''
	  if (arguments.length === 0) return utf8Slice(this, 0, length)
	  return slowToString.apply(this, arguments)
	}

	Buffer.prototype.equals = function equals (b) {
	  if (!Buffer.isBuffer(b)) throw new TypeError('Argument must be a Buffer')
	  if (this === b) return true
	  return Buffer.compare(this, b) === 0
	}

	Buffer.prototype.inspect = function inspect () {
	  var str = ''
	  var max = exports.INSPECT_MAX_BYTES
	  if (this.length > 0) {
	    str = this.toString('hex', 0, max).match(/.{2}/g).join(' ')
	    if (this.length > max) str += ' ... '
	  }
	  return '<Buffer ' + str + '>'
	}

	Buffer.prototype.compare = function compare (b) {
	  if (!Buffer.isBuffer(b)) throw new TypeError('Argument must be a Buffer')
	  if (this === b) return 0
	  return Buffer.compare(this, b)
	}

	Buffer.prototype.indexOf = function indexOf (val, byteOffset) {
	  if (byteOffset > 0x7fffffff) byteOffset = 0x7fffffff
	  else if (byteOffset < -0x80000000) byteOffset = -0x80000000
	  byteOffset >>= 0

	  if (this.length === 0) return -1
	  if (byteOffset >= this.length) return -1

	  // Negative offsets start from the end of the buffer
	  if (byteOffset < 0) byteOffset = Math.max(this.length + byteOffset, 0)

	  if (typeof val === 'string') {
	    if (val.length === 0) return -1 // special case: looking for empty string always fails
	    return String.prototype.indexOf.call(this, val, byteOffset)
	  }
	  if (Buffer.isBuffer(val)) {
	    return arrayIndexOf(this, val, byteOffset)
	  }
	  if (typeof val === 'number') {
	    if (Buffer.TYPED_ARRAY_SUPPORT && Uint8Array.prototype.indexOf === 'function') {
	      return Uint8Array.prototype.indexOf.call(this, val, byteOffset)
	    }
	    return arrayIndexOf(this, [ val ], byteOffset)
	  }

	  function arrayIndexOf (arr, val, byteOffset) {
	    var foundIndex = -1
	    for (var i = 0; byteOffset + i < arr.length; i++) {
	      if (arr[byteOffset + i] === val[foundIndex === -1 ? 0 : i - foundIndex]) {
	        if (foundIndex === -1) foundIndex = i
	        if (i - foundIndex + 1 === val.length) return byteOffset + foundIndex
	      } else {
	        foundIndex = -1
	      }
	    }
	    return -1
	  }

	  throw new TypeError('val must be string, number or Buffer')
	}

	// `get` is deprecated
	Buffer.prototype.get = function get (offset) {
	  console.log('.get() is deprecated. Access using array indexes instead.')
	  return this.readUInt8(offset)
	}

	// `set` is deprecated
	Buffer.prototype.set = function set (v, offset) {
	  console.log('.set() is deprecated. Access using array indexes instead.')
	  return this.writeUInt8(v, offset)
	}

	function hexWrite (buf, string, offset, length) {
	  offset = Number(offset) || 0
	  var remaining = buf.length - offset
	  if (!length) {
	    length = remaining
	  } else {
	    length = Number(length)
	    if (length > remaining) {
	      length = remaining
	    }
	  }

	  // must be an even number of digits
	  var strLen = string.length
	  if (strLen % 2 !== 0) throw new Error('Invalid hex string')

	  if (length > strLen / 2) {
	    length = strLen / 2
	  }
	  for (var i = 0; i < length; i++) {
	    var parsed = parseInt(string.substr(i * 2, 2), 16)
	    if (isNaN(parsed)) throw new Error('Invalid hex string')
	    buf[offset + i] = parsed
	  }
	  return i
	}

	function utf8Write (buf, string, offset, length) {
	  return blitBuffer(utf8ToBytes(string, buf.length - offset), buf, offset, length)
	}

	function asciiWrite (buf, string, offset, length) {
	  return blitBuffer(asciiToBytes(string), buf, offset, length)
	}

	function binaryWrite (buf, string, offset, length) {
	  return asciiWrite(buf, string, offset, length)
	}

	function base64Write (buf, string, offset, length) {
	  return blitBuffer(base64ToBytes(string), buf, offset, length)
	}

	function ucs2Write (buf, string, offset, length) {
	  return blitBuffer(utf16leToBytes(string, buf.length - offset), buf, offset, length)
	}

	Buffer.prototype.write = function write (string, offset, length, encoding) {
	  // Buffer#write(string)
	  if (offset === undefined) {
	    encoding = 'utf8'
	    length = this.length
	    offset = 0
	  // Buffer#write(string, encoding)
	  } else if (length === undefined && typeof offset === 'string') {
	    encoding = offset
	    length = this.length
	    offset = 0
	  // Buffer#write(string, offset[, length][, encoding])
	  } else if (isFinite(offset)) {
	    offset = offset | 0
	    if (isFinite(length)) {
	      length = length | 0
	      if (encoding === undefined) encoding = 'utf8'
	    } else {
	      encoding = length
	      length = undefined
	    }
	  // legacy write(string, encoding, offset, length) - remove in v0.13
	  } else {
	    var swap = encoding
	    encoding = offset
	    offset = length | 0
	    length = swap
	  }

	  var remaining = this.length - offset
	  if (length === undefined || length > remaining) length = remaining

	  if ((string.length > 0 && (length < 0 || offset < 0)) || offset > this.length) {
	    throw new RangeError('attempt to write outside buffer bounds')
	  }

	  if (!encoding) encoding = 'utf8'

	  var loweredCase = false
	  for (;;) {
	    switch (encoding) {
	      case 'hex':
	        return hexWrite(this, string, offset, length)

	      case 'utf8':
	      case 'utf-8':
	        return utf8Write(this, string, offset, length)

	      case 'ascii':
	        return asciiWrite(this, string, offset, length)

	      case 'binary':
	        return binaryWrite(this, string, offset, length)

	      case 'base64':
	        // Warning: maxLength not taken into account in base64Write
	        return base64Write(this, string, offset, length)

	      case 'ucs2':
	      case 'ucs-2':
	      case 'utf16le':
	      case 'utf-16le':
	        return ucs2Write(this, string, offset, length)

	      default:
	        if (loweredCase) throw new TypeError('Unknown encoding: ' + encoding)
	        encoding = ('' + encoding).toLowerCase()
	        loweredCase = true
	    }
	  }
	}

	Buffer.prototype.toJSON = function toJSON () {
	  return {
	    type: 'Buffer',
	    data: Array.prototype.slice.call(this._arr || this, 0)
	  }
	}

	function base64Slice (buf, start, end) {
	  if (start === 0 && end === buf.length) {
	    return base64.fromByteArray(buf)
	  } else {
	    return base64.fromByteArray(buf.slice(start, end))
	  }
	}

	function utf8Slice (buf, start, end) {
	  end = Math.min(buf.length, end)
	  var res = []

	  var i = start
	  while (i < end) {
	    var firstByte = buf[i]
	    var codePoint = null
	    var bytesPerSequence = (firstByte > 0xEF) ? 4
	      : (firstByte > 0xDF) ? 3
	      : (firstByte > 0xBF) ? 2
	      : 1

	    if (i + bytesPerSequence <= end) {
	      var secondByte, thirdByte, fourthByte, tempCodePoint

	      switch (bytesPerSequence) {
	        case 1:
	          if (firstByte < 0x80) {
	            codePoint = firstByte
	          }
	          break
	        case 2:
	          secondByte = buf[i + 1]
	          if ((secondByte & 0xC0) === 0x80) {
	            tempCodePoint = (firstByte & 0x1F) << 0x6 | (secondByte & 0x3F)
	            if (tempCodePoint > 0x7F) {
	              codePoint = tempCodePoint
	            }
	          }
	          break
	        case 3:
	          secondByte = buf[i + 1]
	          thirdByte = buf[i + 2]
	          if ((secondByte & 0xC0) === 0x80 && (thirdByte & 0xC0) === 0x80) {
	            tempCodePoint = (firstByte & 0xF) << 0xC | (secondByte & 0x3F) << 0x6 | (thirdByte & 0x3F)
	            if (tempCodePoint > 0x7FF && (tempCodePoint < 0xD800 || tempCodePoint > 0xDFFF)) {
	              codePoint = tempCodePoint
	            }
	          }
	          break
	        case 4:
	          secondByte = buf[i + 1]
	          thirdByte = buf[i + 2]
	          fourthByte = buf[i + 3]
	          if ((secondByte & 0xC0) === 0x80 && (thirdByte & 0xC0) === 0x80 && (fourthByte & 0xC0) === 0x80) {
	            tempCodePoint = (firstByte & 0xF) << 0x12 | (secondByte & 0x3F) << 0xC | (thirdByte & 0x3F) << 0x6 | (fourthByte & 0x3F)
	            if (tempCodePoint > 0xFFFF && tempCodePoint < 0x110000) {
	              codePoint = tempCodePoint
	            }
	          }
	      }
	    }

	    if (codePoint === null) {
	      // we did not generate a valid codePoint so insert a
	      // replacement char (U+FFFD) and advance only 1 byte
	      codePoint = 0xFFFD
	      bytesPerSequence = 1
	    } else if (codePoint > 0xFFFF) {
	      // encode to utf16 (surrogate pair dance)
	      codePoint -= 0x10000
	      res.push(codePoint >>> 10 & 0x3FF | 0xD800)
	      codePoint = 0xDC00 | codePoint & 0x3FF
	    }

	    res.push(codePoint)
	    i += bytesPerSequence
	  }

	  return decodeCodePointsArray(res)
	}

	// Based on http://stackoverflow.com/a/22747272/680742, the browser with
	// the lowest limit is Chrome, with 0x10000 args.
	// We go 1 magnitude less, for safety
	var MAX_ARGUMENTS_LENGTH = 0x1000

	function decodeCodePointsArray (codePoints) {
	  var len = codePoints.length
	  if (len <= MAX_ARGUMENTS_LENGTH) {
	    return String.fromCharCode.apply(String, codePoints) // avoid extra slice()
	  }

	  // Decode in chunks to avoid "call stack size exceeded".
	  var res = ''
	  var i = 0
	  while (i < len) {
	    res += String.fromCharCode.apply(
	      String,
	      codePoints.slice(i, i += MAX_ARGUMENTS_LENGTH)
	    )
	  }
	  return res
	}

	function asciiSlice (buf, start, end) {
	  var ret = ''
	  end = Math.min(buf.length, end)

	  for (var i = start; i < end; i++) {
	    ret += String.fromCharCode(buf[i] & 0x7F)
	  }
	  return ret
	}

	function binarySlice (buf, start, end) {
	  var ret = ''
	  end = Math.min(buf.length, end)

	  for (var i = start; i < end; i++) {
	    ret += String.fromCharCode(buf[i])
	  }
	  return ret
	}

	function hexSlice (buf, start, end) {
	  var len = buf.length

	  if (!start || start < 0) start = 0
	  if (!end || end < 0 || end > len) end = len

	  var out = ''
	  for (var i = start; i < end; i++) {
	    out += toHex(buf[i])
	  }
	  return out
	}

	function utf16leSlice (buf, start, end) {
	  var bytes = buf.slice(start, end)
	  var res = ''
	  for (var i = 0; i < bytes.length; i += 2) {
	    res += String.fromCharCode(bytes[i] + bytes[i + 1] * 256)
	  }
	  return res
	}

	Buffer.prototype.slice = function slice (start, end) {
	  var len = this.length
	  start = ~~start
	  end = end === undefined ? len : ~~end

	  if (start < 0) {
	    start += len
	    if (start < 0) start = 0
	  } else if (start > len) {
	    start = len
	  }

	  if (end < 0) {
	    end += len
	    if (end < 0) end = 0
	  } else if (end > len) {
	    end = len
	  }

	  if (end < start) end = start

	  var newBuf
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    newBuf = Buffer._augment(this.subarray(start, end))
	  } else {
	    var sliceLen = end - start
	    newBuf = new Buffer(sliceLen, undefined)
	    for (var i = 0; i < sliceLen; i++) {
	      newBuf[i] = this[i + start]
	    }
	  }

	  if (newBuf.length) newBuf.parent = this.parent || this

	  return newBuf
	}

	/*
	 * Need to make sure that buffer isn't trying to write out of bounds.
	 */
	function checkOffset (offset, ext, length) {
	  if ((offset % 1) !== 0 || offset < 0) throw new RangeError('offset is not uint')
	  if (offset + ext > length) throw new RangeError('Trying to access beyond buffer length')
	}

	Buffer.prototype.readUIntLE = function readUIntLE (offset, byteLength, noAssert) {
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkOffset(offset, byteLength, this.length)

	  var val = this[offset]
	  var mul = 1
	  var i = 0
	  while (++i < byteLength && (mul *= 0x100)) {
	    val += this[offset + i] * mul
	  }

	  return val
	}

	Buffer.prototype.readUIntBE = function readUIntBE (offset, byteLength, noAssert) {
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) {
	    checkOffset(offset, byteLength, this.length)
	  }

	  var val = this[offset + --byteLength]
	  var mul = 1
	  while (byteLength > 0 && (mul *= 0x100)) {
	    val += this[offset + --byteLength] * mul
	  }

	  return val
	}

	Buffer.prototype.readUInt8 = function readUInt8 (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 1, this.length)
	  return this[offset]
	}

	Buffer.prototype.readUInt16LE = function readUInt16LE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 2, this.length)
	  return this[offset] | (this[offset + 1] << 8)
	}

	Buffer.prototype.readUInt16BE = function readUInt16BE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 2, this.length)
	  return (this[offset] << 8) | this[offset + 1]
	}

	Buffer.prototype.readUInt32LE = function readUInt32LE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)

	  return ((this[offset]) |
	      (this[offset + 1] << 8) |
	      (this[offset + 2] << 16)) +
	      (this[offset + 3] * 0x1000000)
	}

	Buffer.prototype.readUInt32BE = function readUInt32BE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)

	  return (this[offset] * 0x1000000) +
	    ((this[offset + 1] << 16) |
	    (this[offset + 2] << 8) |
	    this[offset + 3])
	}

	Buffer.prototype.readIntLE = function readIntLE (offset, byteLength, noAssert) {
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkOffset(offset, byteLength, this.length)

	  var val = this[offset]
	  var mul = 1
	  var i = 0
	  while (++i < byteLength && (mul *= 0x100)) {
	    val += this[offset + i] * mul
	  }
	  mul *= 0x80

	  if (val >= mul) val -= Math.pow(2, 8 * byteLength)

	  return val
	}

	Buffer.prototype.readIntBE = function readIntBE (offset, byteLength, noAssert) {
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkOffset(offset, byteLength, this.length)

	  var i = byteLength
	  var mul = 1
	  var val = this[offset + --i]
	  while (i > 0 && (mul *= 0x100)) {
	    val += this[offset + --i] * mul
	  }
	  mul *= 0x80

	  if (val >= mul) val -= Math.pow(2, 8 * byteLength)

	  return val
	}

	Buffer.prototype.readInt8 = function readInt8 (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 1, this.length)
	  if (!(this[offset] & 0x80)) return (this[offset])
	  return ((0xff - this[offset] + 1) * -1)
	}

	Buffer.prototype.readInt16LE = function readInt16LE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 2, this.length)
	  var val = this[offset] | (this[offset + 1] << 8)
	  return (val & 0x8000) ? val | 0xFFFF0000 : val
	}

	Buffer.prototype.readInt16BE = function readInt16BE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 2, this.length)
	  var val = this[offset + 1] | (this[offset] << 8)
	  return (val & 0x8000) ? val | 0xFFFF0000 : val
	}

	Buffer.prototype.readInt32LE = function readInt32LE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)

	  return (this[offset]) |
	    (this[offset + 1] << 8) |
	    (this[offset + 2] << 16) |
	    (this[offset + 3] << 24)
	}

	Buffer.prototype.readInt32BE = function readInt32BE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)

	  return (this[offset] << 24) |
	    (this[offset + 1] << 16) |
	    (this[offset + 2] << 8) |
	    (this[offset + 3])
	}

	Buffer.prototype.readFloatLE = function readFloatLE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)
	  return ieee754.read(this, offset, true, 23, 4)
	}

	Buffer.prototype.readFloatBE = function readFloatBE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 4, this.length)
	  return ieee754.read(this, offset, false, 23, 4)
	}

	Buffer.prototype.readDoubleLE = function readDoubleLE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 8, this.length)
	  return ieee754.read(this, offset, true, 52, 8)
	}

	Buffer.prototype.readDoubleBE = function readDoubleBE (offset, noAssert) {
	  if (!noAssert) checkOffset(offset, 8, this.length)
	  return ieee754.read(this, offset, false, 52, 8)
	}

	function checkInt (buf, value, offset, ext, max, min) {
	  if (!Buffer.isBuffer(buf)) throw new TypeError('buffer must be a Buffer instance')
	  if (value > max || value < min) throw new RangeError('value is out of bounds')
	  if (offset + ext > buf.length) throw new RangeError('index out of range')
	}

	Buffer.prototype.writeUIntLE = function writeUIntLE (value, offset, byteLength, noAssert) {
	  value = +value
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkInt(this, value, offset, byteLength, Math.pow(2, 8 * byteLength), 0)

	  var mul = 1
	  var i = 0
	  this[offset] = value & 0xFF
	  while (++i < byteLength && (mul *= 0x100)) {
	    this[offset + i] = (value / mul) & 0xFF
	  }

	  return offset + byteLength
	}

	Buffer.prototype.writeUIntBE = function writeUIntBE (value, offset, byteLength, noAssert) {
	  value = +value
	  offset = offset | 0
	  byteLength = byteLength | 0
	  if (!noAssert) checkInt(this, value, offset, byteLength, Math.pow(2, 8 * byteLength), 0)

	  var i = byteLength - 1
	  var mul = 1
	  this[offset + i] = value & 0xFF
	  while (--i >= 0 && (mul *= 0x100)) {
	    this[offset + i] = (value / mul) & 0xFF
	  }

	  return offset + byteLength
	}

	Buffer.prototype.writeUInt8 = function writeUInt8 (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 1, 0xff, 0)
	  if (!Buffer.TYPED_ARRAY_SUPPORT) value = Math.floor(value)
	  this[offset] = value
	  return offset + 1
	}

	function objectWriteUInt16 (buf, value, offset, littleEndian) {
	  if (value < 0) value = 0xffff + value + 1
	  for (var i = 0, j = Math.min(buf.length - offset, 2); i < j; i++) {
	    buf[offset + i] = (value & (0xff << (8 * (littleEndian ? i : 1 - i)))) >>>
	      (littleEndian ? i : 1 - i) * 8
	  }
	}

	Buffer.prototype.writeUInt16LE = function writeUInt16LE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 2, 0xffff, 0)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = value
	    this[offset + 1] = (value >>> 8)
	  } else {
	    objectWriteUInt16(this, value, offset, true)
	  }
	  return offset + 2
	}

	Buffer.prototype.writeUInt16BE = function writeUInt16BE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 2, 0xffff, 0)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value >>> 8)
	    this[offset + 1] = value
	  } else {
	    objectWriteUInt16(this, value, offset, false)
	  }
	  return offset + 2
	}

	function objectWriteUInt32 (buf, value, offset, littleEndian) {
	  if (value < 0) value = 0xffffffff + value + 1
	  for (var i = 0, j = Math.min(buf.length - offset, 4); i < j; i++) {
	    buf[offset + i] = (value >>> (littleEndian ? i : 3 - i) * 8) & 0xff
	  }
	}

	Buffer.prototype.writeUInt32LE = function writeUInt32LE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 4, 0xffffffff, 0)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset + 3] = (value >>> 24)
	    this[offset + 2] = (value >>> 16)
	    this[offset + 1] = (value >>> 8)
	    this[offset] = value
	  } else {
	    objectWriteUInt32(this, value, offset, true)
	  }
	  return offset + 4
	}

	Buffer.prototype.writeUInt32BE = function writeUInt32BE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 4, 0xffffffff, 0)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value >>> 24)
	    this[offset + 1] = (value >>> 16)
	    this[offset + 2] = (value >>> 8)
	    this[offset + 3] = value
	  } else {
	    objectWriteUInt32(this, value, offset, false)
	  }
	  return offset + 4
	}

	Buffer.prototype.writeIntLE = function writeIntLE (value, offset, byteLength, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) {
	    var limit = Math.pow(2, 8 * byteLength - 1)

	    checkInt(this, value, offset, byteLength, limit - 1, -limit)
	  }

	  var i = 0
	  var mul = 1
	  var sub = value < 0 ? 1 : 0
	  this[offset] = value & 0xFF
	  while (++i < byteLength && (mul *= 0x100)) {
	    this[offset + i] = ((value / mul) >> 0) - sub & 0xFF
	  }

	  return offset + byteLength
	}

	Buffer.prototype.writeIntBE = function writeIntBE (value, offset, byteLength, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) {
	    var limit = Math.pow(2, 8 * byteLength - 1)

	    checkInt(this, value, offset, byteLength, limit - 1, -limit)
	  }

	  var i = byteLength - 1
	  var mul = 1
	  var sub = value < 0 ? 1 : 0
	  this[offset + i] = value & 0xFF
	  while (--i >= 0 && (mul *= 0x100)) {
	    this[offset + i] = ((value / mul) >> 0) - sub & 0xFF
	  }

	  return offset + byteLength
	}

	Buffer.prototype.writeInt8 = function writeInt8 (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 1, 0x7f, -0x80)
	  if (!Buffer.TYPED_ARRAY_SUPPORT) value = Math.floor(value)
	  if (value < 0) value = 0xff + value + 1
	  this[offset] = value
	  return offset + 1
	}

	Buffer.prototype.writeInt16LE = function writeInt16LE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 2, 0x7fff, -0x8000)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = value
	    this[offset + 1] = (value >>> 8)
	  } else {
	    objectWriteUInt16(this, value, offset, true)
	  }
	  return offset + 2
	}

	Buffer.prototype.writeInt16BE = function writeInt16BE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 2, 0x7fff, -0x8000)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value >>> 8)
	    this[offset + 1] = value
	  } else {
	    objectWriteUInt16(this, value, offset, false)
	  }
	  return offset + 2
	}

	Buffer.prototype.writeInt32LE = function writeInt32LE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 4, 0x7fffffff, -0x80000000)
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = value
	    this[offset + 1] = (value >>> 8)
	    this[offset + 2] = (value >>> 16)
	    this[offset + 3] = (value >>> 24)
	  } else {
	    objectWriteUInt32(this, value, offset, true)
	  }
	  return offset + 4
	}

	Buffer.prototype.writeInt32BE = function writeInt32BE (value, offset, noAssert) {
	  value = +value
	  offset = offset | 0
	  if (!noAssert) checkInt(this, value, offset, 4, 0x7fffffff, -0x80000000)
	  if (value < 0) value = 0xffffffff + value + 1
	  if (Buffer.TYPED_ARRAY_SUPPORT) {
	    this[offset] = (value >>> 24)
	    this[offset + 1] = (value >>> 16)
	    this[offset + 2] = (value >>> 8)
	    this[offset + 3] = value
	  } else {
	    objectWriteUInt32(this, value, offset, false)
	  }
	  return offset + 4
	}

	function checkIEEE754 (buf, value, offset, ext, max, min) {
	  if (value > max || value < min) throw new RangeError('value is out of bounds')
	  if (offset + ext > buf.length) throw new RangeError('index out of range')
	  if (offset < 0) throw new RangeError('index out of range')
	}

	function writeFloat (buf, value, offset, littleEndian, noAssert) {
	  if (!noAssert) {
	    checkIEEE754(buf, value, offset, 4, 3.4028234663852886e+38, -3.4028234663852886e+38)
	  }
	  ieee754.write(buf, value, offset, littleEndian, 23, 4)
	  return offset + 4
	}

	Buffer.prototype.writeFloatLE = function writeFloatLE (value, offset, noAssert) {
	  return writeFloat(this, value, offset, true, noAssert)
	}

	Buffer.prototype.writeFloatBE = function writeFloatBE (value, offset, noAssert) {
	  return writeFloat(this, value, offset, false, noAssert)
	}

	function writeDouble (buf, value, offset, littleEndian, noAssert) {
	  if (!noAssert) {
	    checkIEEE754(buf, value, offset, 8, 1.7976931348623157E+308, -1.7976931348623157E+308)
	  }
	  ieee754.write(buf, value, offset, littleEndian, 52, 8)
	  return offset + 8
	}

	Buffer.prototype.writeDoubleLE = function writeDoubleLE (value, offset, noAssert) {
	  return writeDouble(this, value, offset, true, noAssert)
	}

	Buffer.prototype.writeDoubleBE = function writeDoubleBE (value, offset, noAssert) {
	  return writeDouble(this, value, offset, false, noAssert)
	}

	// copy(targetBuffer, targetStart=0, sourceStart=0, sourceEnd=buffer.length)
	Buffer.prototype.copy = function copy (target, targetStart, start, end) {
	  if (!start) start = 0
	  if (!end && end !== 0) end = this.length
	  if (targetStart >= target.length) targetStart = target.length
	  if (!targetStart) targetStart = 0
	  if (end > 0 && end < start) end = start

	  // Copy 0 bytes; we're done
	  if (end === start) return 0
	  if (target.length === 0 || this.length === 0) return 0

	  // Fatal error conditions
	  if (targetStart < 0) {
	    throw new RangeError('targetStart out of bounds')
	  }
	  if (start < 0 || start >= this.length) throw new RangeError('sourceStart out of bounds')
	  if (end < 0) throw new RangeError('sourceEnd out of bounds')

	  // Are we oob?
	  if (end > this.length) end = this.length
	  if (target.length - targetStart < end - start) {
	    end = target.length - targetStart + start
	  }

	  var len = end - start
	  var i

	  if (this === target && start < targetStart && targetStart < end) {
	    // descending copy from end
	    for (i = len - 1; i >= 0; i--) {
	      target[i + targetStart] = this[i + start]
	    }
	  } else if (len < 1000 || !Buffer.TYPED_ARRAY_SUPPORT) {
	    // ascending copy from start
	    for (i = 0; i < len; i++) {
	      target[i + targetStart] = this[i + start]
	    }
	  } else {
	    target._set(this.subarray(start, start + len), targetStart)
	  }

	  return len
	}

	// fill(value, start=0, end=buffer.length)
	Buffer.prototype.fill = function fill (value, start, end) {
	  if (!value) value = 0
	  if (!start) start = 0
	  if (!end) end = this.length

	  if (end < start) throw new RangeError('end < start')

	  // Fill 0 bytes; we're done
	  if (end === start) return
	  if (this.length === 0) return

	  if (start < 0 || start >= this.length) throw new RangeError('start out of bounds')
	  if (end < 0 || end > this.length) throw new RangeError('end out of bounds')

	  var i
	  if (typeof value === 'number') {
	    for (i = start; i < end; i++) {
	      this[i] = value
	    }
	  } else {
	    var bytes = utf8ToBytes(value.toString())
	    var len = bytes.length
	    for (i = start; i < end; i++) {
	      this[i] = bytes[i % len]
	    }
	  }

	  return this
	}

	/**
	 * Creates a new `ArrayBuffer` with the *copied* memory of the buffer instance.
	 * Added in Node 0.12. Only available in browsers that support ArrayBuffer.
	 */
	Buffer.prototype.toArrayBuffer = function toArrayBuffer () {
	  if (typeof Uint8Array !== 'undefined') {
	    if (Buffer.TYPED_ARRAY_SUPPORT) {
	      return (new Buffer(this)).buffer
	    } else {
	      var buf = new Uint8Array(this.length)
	      for (var i = 0, len = buf.length; i < len; i += 1) {
	        buf[i] = this[i]
	      }
	      return buf.buffer
	    }
	  } else {
	    throw new TypeError('Buffer.toArrayBuffer not supported in this browser')
	  }
	}

	// HELPER FUNCTIONS
	// ================

	var BP = Buffer.prototype

	/**
	 * Augment a Uint8Array *instance* (not the Uint8Array class!) with Buffer methods
	 */
	Buffer._augment = function _augment (arr) {
	  arr.constructor = Buffer
	  arr._isBuffer = true

	  // save reference to original Uint8Array set method before overwriting
	  arr._set = arr.set

	  // deprecated
	  arr.get = BP.get
	  arr.set = BP.set

	  arr.write = BP.write
	  arr.toString = BP.toString
	  arr.toLocaleString = BP.toString
	  arr.toJSON = BP.toJSON
	  arr.equals = BP.equals
	  arr.compare = BP.compare
	  arr.indexOf = BP.indexOf
	  arr.copy = BP.copy
	  arr.slice = BP.slice
	  arr.readUIntLE = BP.readUIntLE
	  arr.readUIntBE = BP.readUIntBE
	  arr.readUInt8 = BP.readUInt8
	  arr.readUInt16LE = BP.readUInt16LE
	  arr.readUInt16BE = BP.readUInt16BE
	  arr.readUInt32LE = BP.readUInt32LE
	  arr.readUInt32BE = BP.readUInt32BE
	  arr.readIntLE = BP.readIntLE
	  arr.readIntBE = BP.readIntBE
	  arr.readInt8 = BP.readInt8
	  arr.readInt16LE = BP.readInt16LE
	  arr.readInt16BE = BP.readInt16BE
	  arr.readInt32LE = BP.readInt32LE
	  arr.readInt32BE = BP.readInt32BE
	  arr.readFloatLE = BP.readFloatLE
	  arr.readFloatBE = BP.readFloatBE
	  arr.readDoubleLE = BP.readDoubleLE
	  arr.readDoubleBE = BP.readDoubleBE
	  arr.writeUInt8 = BP.writeUInt8
	  arr.writeUIntLE = BP.writeUIntLE
	  arr.writeUIntBE = BP.writeUIntBE
	  arr.writeUInt16LE = BP.writeUInt16LE
	  arr.writeUInt16BE = BP.writeUInt16BE
	  arr.writeUInt32LE = BP.writeUInt32LE
	  arr.writeUInt32BE = BP.writeUInt32BE
	  arr.writeIntLE = BP.writeIntLE
	  arr.writeIntBE = BP.writeIntBE
	  arr.writeInt8 = BP.writeInt8
	  arr.writeInt16LE = BP.writeInt16LE
	  arr.writeInt16BE = BP.writeInt16BE
	  arr.writeInt32LE = BP.writeInt32LE
	  arr.writeInt32BE = BP.writeInt32BE
	  arr.writeFloatLE = BP.writeFloatLE
	  arr.writeFloatBE = BP.writeFloatBE
	  arr.writeDoubleLE = BP.writeDoubleLE
	  arr.writeDoubleBE = BP.writeDoubleBE
	  arr.fill = BP.fill
	  arr.inspect = BP.inspect
	  arr.toArrayBuffer = BP.toArrayBuffer

	  return arr
	}

	var INVALID_BASE64_RE = /[^+\/0-9A-Za-z-_]/g

	function base64clean (str) {
	  // Node strips out invalid characters like \n and \t from the string, base64-js does not
	  str = stringtrim(str).replace(INVALID_BASE64_RE, '')
	  // Node converts strings with length < 2 to ''
	  if (str.length < 2) return ''
	  // Node allows for non-padded base64 strings (missing trailing ===), base64-js does not
	  while (str.length % 4 !== 0) {
	    str = str + '='
	  }
	  return str
	}

	function stringtrim (str) {
	  if (str.trim) return str.trim()
	  return str.replace(/^\s+|\s+$/g, '')
	}

	function toHex (n) {
	  if (n < 16) return '0' + n.toString(16)
	  return n.toString(16)
	}

	function utf8ToBytes (string, units) {
	  units = units || Infinity
	  var codePoint
	  var length = string.length
	  var leadSurrogate = null
	  var bytes = []

	  for (var i = 0; i < length; i++) {
	    codePoint = string.charCodeAt(i)

	    // is surrogate component
	    if (codePoint > 0xD7FF && codePoint < 0xE000) {
	      // last char was a lead
	      if (!leadSurrogate) {
	        // no lead yet
	        if (codePoint > 0xDBFF) {
	          // unexpected trail
	          if ((units -= 3) > -1) bytes.push(0xEF, 0xBF, 0xBD)
	          continue
	        } else if (i + 1 === length) {
	          // unpaired lead
	          if ((units -= 3) > -1) bytes.push(0xEF, 0xBF, 0xBD)
	          continue
	        }

	        // valid lead
	        leadSurrogate = codePoint

	        continue
	      }

	      // 2 leads in a row
	      if (codePoint < 0xDC00) {
	        if ((units -= 3) > -1) bytes.push(0xEF, 0xBF, 0xBD)
	        leadSurrogate = codePoint
	        continue
	      }

	      // valid surrogate pair
	      codePoint = leadSurrogate - 0xD800 << 10 | codePoint - 0xDC00 | 0x10000
	    } else if (leadSurrogate) {
	      // valid bmp char, but last char was a lead
	      if ((units -= 3) > -1) bytes.push(0xEF, 0xBF, 0xBD)
	    }

	    leadSurrogate = null

	    // encode utf8
	    if (codePoint < 0x80) {
	      if ((units -= 1) < 0) break
	      bytes.push(codePoint)
	    } else if (codePoint < 0x800) {
	      if ((units -= 2) < 0) break
	      bytes.push(
	        codePoint >> 0x6 | 0xC0,
	        codePoint & 0x3F | 0x80
	      )
	    } else if (codePoint < 0x10000) {
	      if ((units -= 3) < 0) break
	      bytes.push(
	        codePoint >> 0xC | 0xE0,
	        codePoint >> 0x6 & 0x3F | 0x80,
	        codePoint & 0x3F | 0x80
	      )
	    } else if (codePoint < 0x110000) {
	      if ((units -= 4) < 0) break
	      bytes.push(
	        codePoint >> 0x12 | 0xF0,
	        codePoint >> 0xC & 0x3F | 0x80,
	        codePoint >> 0x6 & 0x3F | 0x80,
	        codePoint & 0x3F | 0x80
	      )
	    } else {
	      throw new Error('Invalid code point')
	    }
	  }

	  return bytes
	}

	function asciiToBytes (str) {
	  var byteArray = []
	  for (var i = 0; i < str.length; i++) {
	    // Node's code seems to be doing this and not & 0x7F..
	    byteArray.push(str.charCodeAt(i) & 0xFF)
	  }
	  return byteArray
	}

	function utf16leToBytes (str, units) {
	  var c, hi, lo
	  var byteArray = []
	  for (var i = 0; i < str.length; i++) {
	    if ((units -= 2) < 0) break

	    c = str.charCodeAt(i)
	    hi = c >> 8
	    lo = c % 256
	    byteArray.push(lo)
	    byteArray.push(hi)
	  }

	  return byteArray
	}

	function base64ToBytes (str) {
	  return base64.toByteArray(base64clean(str))
	}

	function blitBuffer (src, dst, offset, length) {
	  for (var i = 0; i < length; i++) {
	    if ((i + offset >= dst.length) || (i >= src.length)) break
	    dst[i + offset] = src[i]
	  }
	  return i
	}

	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(41).Buffer, (function() { return this; }())))

/***/ },
/* 42 */
/***/ function(module, exports, __webpack_require__) {

	var lookup = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

	;(function (exports) {
		'use strict';

	  var Arr = (typeof Uint8Array !== 'undefined')
	    ? Uint8Array
	    : Array

		var PLUS   = '+'.charCodeAt(0)
		var SLASH  = '/'.charCodeAt(0)
		var NUMBER = '0'.charCodeAt(0)
		var LOWER  = 'a'.charCodeAt(0)
		var UPPER  = 'A'.charCodeAt(0)
		var PLUS_URL_SAFE = '-'.charCodeAt(0)
		var SLASH_URL_SAFE = '_'.charCodeAt(0)

		function decode (elt) {
			var code = elt.charCodeAt(0)
			if (code === PLUS ||
			    code === PLUS_URL_SAFE)
				return 62 // '+'
			if (code === SLASH ||
			    code === SLASH_URL_SAFE)
				return 63 // '/'
			if (code < NUMBER)
				return -1 //no match
			if (code < NUMBER + 10)
				return code - NUMBER + 26 + 26
			if (code < UPPER + 26)
				return code - UPPER
			if (code < LOWER + 26)
				return code - LOWER + 26
		}

		function b64ToByteArray (b64) {
			var i, j, l, tmp, placeHolders, arr

			if (b64.length % 4 > 0) {
				throw new Error('Invalid string. Length must be a multiple of 4')
			}

			// the number of equal signs (place holders)
			// if there are two placeholders, than the two characters before it
			// represent one byte
			// if there is only one, then the three characters before it represent 2 bytes
			// this is just a cheap hack to not do indexOf twice
			var len = b64.length
			placeHolders = '=' === b64.charAt(len - 2) ? 2 : '=' === b64.charAt(len - 1) ? 1 : 0

			// base64 is 4/3 + up to two characters of the original data
			arr = new Arr(b64.length * 3 / 4 - placeHolders)

			// if there are placeholders, only get up to the last complete 4 chars
			l = placeHolders > 0 ? b64.length - 4 : b64.length

			var L = 0

			function push (v) {
				arr[L++] = v
			}

			for (i = 0, j = 0; i < l; i += 4, j += 3) {
				tmp = (decode(b64.charAt(i)) << 18) | (decode(b64.charAt(i + 1)) << 12) | (decode(b64.charAt(i + 2)) << 6) | decode(b64.charAt(i + 3))
				push((tmp & 0xFF0000) >> 16)
				push((tmp & 0xFF00) >> 8)
				push(tmp & 0xFF)
			}

			if (placeHolders === 2) {
				tmp = (decode(b64.charAt(i)) << 2) | (decode(b64.charAt(i + 1)) >> 4)
				push(tmp & 0xFF)
			} else if (placeHolders === 1) {
				tmp = (decode(b64.charAt(i)) << 10) | (decode(b64.charAt(i + 1)) << 4) | (decode(b64.charAt(i + 2)) >> 2)
				push((tmp >> 8) & 0xFF)
				push(tmp & 0xFF)
			}

			return arr
		}

		function uint8ToBase64 (uint8) {
			var i,
				extraBytes = uint8.length % 3, // if we have 1 byte left, pad 2 bytes
				output = "",
				temp, length

			function encode (num) {
				return lookup.charAt(num)
			}

			function tripletToBase64 (num) {
				return encode(num >> 18 & 0x3F) + encode(num >> 12 & 0x3F) + encode(num >> 6 & 0x3F) + encode(num & 0x3F)
			}

			// go through the array every three bytes, we'll deal with trailing stuff later
			for (i = 0, length = uint8.length - extraBytes; i < length; i += 3) {
				temp = (uint8[i] << 16) + (uint8[i + 1] << 8) + (uint8[i + 2])
				output += tripletToBase64(temp)
			}

			// pad the end with zeros, but make sure to not forget the extra bytes
			switch (extraBytes) {
				case 1:
					temp = uint8[uint8.length - 1]
					output += encode(temp >> 2)
					output += encode((temp << 4) & 0x3F)
					output += '=='
					break
				case 2:
					temp = (uint8[uint8.length - 2] << 8) + (uint8[uint8.length - 1])
					output += encode(temp >> 10)
					output += encode((temp >> 4) & 0x3F)
					output += encode((temp << 2) & 0x3F)
					output += '='
					break
			}

			return output
		}

		exports.toByteArray = b64ToByteArray
		exports.fromByteArray = uint8ToBase64
	}( false ? (this.base64js = {}) : exports))


/***/ },
/* 43 */
/***/ function(module, exports) {

	exports.read = function (buffer, offset, isLE, mLen, nBytes) {
	  var e, m
	  var eLen = nBytes * 8 - mLen - 1
	  var eMax = (1 << eLen) - 1
	  var eBias = eMax >> 1
	  var nBits = -7
	  var i = isLE ? (nBytes - 1) : 0
	  var d = isLE ? -1 : 1
	  var s = buffer[offset + i]

	  i += d

	  e = s & ((1 << (-nBits)) - 1)
	  s >>= (-nBits)
	  nBits += eLen
	  for (; nBits > 0; e = e * 256 + buffer[offset + i], i += d, nBits -= 8) {}

	  m = e & ((1 << (-nBits)) - 1)
	  e >>= (-nBits)
	  nBits += mLen
	  for (; nBits > 0; m = m * 256 + buffer[offset + i], i += d, nBits -= 8) {}

	  if (e === 0) {
	    e = 1 - eBias
	  } else if (e === eMax) {
	    return m ? NaN : ((s ? -1 : 1) * Infinity)
	  } else {
	    m = m + Math.pow(2, mLen)
	    e = e - eBias
	  }
	  return (s ? -1 : 1) * m * Math.pow(2, e - mLen)
	}

	exports.write = function (buffer, value, offset, isLE, mLen, nBytes) {
	  var e, m, c
	  var eLen = nBytes * 8 - mLen - 1
	  var eMax = (1 << eLen) - 1
	  var eBias = eMax >> 1
	  var rt = (mLen === 23 ? Math.pow(2, -24) - Math.pow(2, -77) : 0)
	  var i = isLE ? 0 : (nBytes - 1)
	  var d = isLE ? 1 : -1
	  var s = value < 0 || (value === 0 && 1 / value < 0) ? 1 : 0

	  value = Math.abs(value)

	  if (isNaN(value) || value === Infinity) {
	    m = isNaN(value) ? 1 : 0
	    e = eMax
	  } else {
	    e = Math.floor(Math.log(value) / Math.LN2)
	    if (value * (c = Math.pow(2, -e)) < 1) {
	      e--
	      c *= 2
	    }
	    if (e + eBias >= 1) {
	      value += rt / c
	    } else {
	      value += rt * Math.pow(2, 1 - eBias)
	    }
	    if (value * c >= 2) {
	      e++
	      c /= 2
	    }

	    if (e + eBias >= eMax) {
	      m = 0
	      e = eMax
	    } else if (e + eBias >= 1) {
	      m = (value * c - 1) * Math.pow(2, mLen)
	      e = e + eBias
	    } else {
	      m = value * Math.pow(2, eBias - 1) * Math.pow(2, mLen)
	      e = 0
	    }
	  }

	  for (; mLen >= 8; buffer[offset + i] = m & 0xff, i += d, m /= 256, mLen -= 8) {}

	  e = (e << mLen) | m
	  eLen += mLen
	  for (; eLen > 0; buffer[offset + i] = e & 0xff, i += d, e /= 256, eLen -= 8) {}

	  buffer[offset + i - d] |= s * 128
	}


/***/ },
/* 44 */
/***/ function(module, exports) {

	
	/**
	 * isArray
	 */

	var isArray = Array.isArray;

	/**
	 * toString
	 */

	var str = Object.prototype.toString;

	/**
	 * Whether or not the given `val`
	 * is an array.
	 *
	 * example:
	 *
	 *        isArray([]);
	 *        // > true
	 *        isArray(arguments);
	 *        // > false
	 *        isArray('');
	 *        // > false
	 *
	 * @param {mixed} val
	 * @return {bool}
	 */

	module.exports = isArray || function (val) {
	  return !! val && '[object Array]' == str.call(val);
	};


/***/ },
/* 45 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(Buffer) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	// NOTE: These type checking functions intentionally don't use `instanceof`
	// because it is fragile and can be easily faked with `Object.create()`.
	function isArray(ar) {
	  return Array.isArray(ar);
	}
	exports.isArray = isArray;

	function isBoolean(arg) {
	  return typeof arg === 'boolean';
	}
	exports.isBoolean = isBoolean;

	function isNull(arg) {
	  return arg === null;
	}
	exports.isNull = isNull;

	function isNullOrUndefined(arg) {
	  return arg == null;
	}
	exports.isNullOrUndefined = isNullOrUndefined;

	function isNumber(arg) {
	  return typeof arg === 'number';
	}
	exports.isNumber = isNumber;

	function isString(arg) {
	  return typeof arg === 'string';
	}
	exports.isString = isString;

	function isSymbol(arg) {
	  return typeof arg === 'symbol';
	}
	exports.isSymbol = isSymbol;

	function isUndefined(arg) {
	  return arg === void 0;
	}
	exports.isUndefined = isUndefined;

	function isRegExp(re) {
	  return isObject(re) && objectToString(re) === '[object RegExp]';
	}
	exports.isRegExp = isRegExp;

	function isObject(arg) {
	  return typeof arg === 'object' && arg !== null;
	}
	exports.isObject = isObject;

	function isDate(d) {
	  return isObject(d) && objectToString(d) === '[object Date]';
	}
	exports.isDate = isDate;

	function isError(e) {
	  return isObject(e) &&
	      (objectToString(e) === '[object Error]' || e instanceof Error);
	}
	exports.isError = isError;

	function isFunction(arg) {
	  return typeof arg === 'function';
	}
	exports.isFunction = isFunction;

	function isPrimitive(arg) {
	  return arg === null ||
	         typeof arg === 'boolean' ||
	         typeof arg === 'number' ||
	         typeof arg === 'string' ||
	         typeof arg === 'symbol' ||  // ES6 symbol
	         typeof arg === 'undefined';
	}
	exports.isPrimitive = isPrimitive;

	function isBuffer(arg) {
	  return Buffer.isBuffer(arg);
	}
	exports.isBuffer = isBuffer;

	function objectToString(o) {
	  return Object.prototype.toString.call(o);
	}
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(41).Buffer))

/***/ },
/* 46 */
/***/ function(module, exports) {

	if (typeof Object.create === 'function') {
	  // implementation from standard node.js 'util' module
	  module.exports = function inherits(ctor, superCtor) {
	    ctor.super_ = superCtor
	    ctor.prototype = Object.create(superCtor.prototype, {
	      constructor: {
	        value: ctor,
	        enumerable: false,
	        writable: true,
	        configurable: true
	      }
	    });
	  };
	} else {
	  // old school shim for old browsers
	  module.exports = function inherits(ctor, superCtor) {
	    ctor.super_ = superCtor
	    var TempCtor = function () {}
	    TempCtor.prototype = superCtor.prototype
	    ctor.prototype = new TempCtor()
	    ctor.prototype.constructor = ctor
	  }
	}


/***/ },
/* 47 */
/***/ function(module, exports) {

	/* (ignored) */

/***/ },
/* 48 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(process) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	// a duplex stream is just a stream that is both readable and writable.
	// Since JS doesn't have multiple prototypal inheritance, this class
	// prototypally inherits from Readable, and then parasitically from
	// Writable.

	module.exports = Duplex;

	/*<replacement>*/
	var objectKeys = Object.keys || function (obj) {
	  var keys = [];
	  for (var key in obj) keys.push(key);
	  return keys;
	}
	/*</replacement>*/


	/*<replacement>*/
	var util = __webpack_require__(45);
	util.inherits = __webpack_require__(46);
	/*</replacement>*/

	var Readable = __webpack_require__(39);
	var Writable = __webpack_require__(49);

	util.inherits(Duplex, Readable);

	forEach(objectKeys(Writable.prototype), function(method) {
	  if (!Duplex.prototype[method])
	    Duplex.prototype[method] = Writable.prototype[method];
	});

	function Duplex(options) {
	  if (!(this instanceof Duplex))
	    return new Duplex(options);

	  Readable.call(this, options);
	  Writable.call(this, options);

	  if (options && options.readable === false)
	    this.readable = false;

	  if (options && options.writable === false)
	    this.writable = false;

	  this.allowHalfOpen = true;
	  if (options && options.allowHalfOpen === false)
	    this.allowHalfOpen = false;

	  this.once('end', onend);
	}

	// the no-half-open enforcer
	function onend() {
	  // if we allow half-open state, or if the writable side ended,
	  // then we're ok.
	  if (this.allowHalfOpen || this._writableState.ended)
	    return;

	  // no more data can be written.
	  // But allow more writes to happen in this tick.
	  process.nextTick(this.end.bind(this));
	}

	function forEach (xs, f) {
	  for (var i = 0, l = xs.length; i < l; i++) {
	    f(xs[i], i);
	  }
	}

	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(31)))

/***/ },
/* 49 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(process) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	// A bit simpler than readable streams.
	// Implement an async ._write(chunk, cb), and it'll handle all
	// the drain event emission and buffering.

	module.exports = Writable;

	/*<replacement>*/
	var Buffer = __webpack_require__(41).Buffer;
	/*</replacement>*/

	Writable.WritableState = WritableState;


	/*<replacement>*/
	var util = __webpack_require__(45);
	util.inherits = __webpack_require__(46);
	/*</replacement>*/

	var Stream = __webpack_require__(35);

	util.inherits(Writable, Stream);

	function WriteReq(chunk, encoding, cb) {
	  this.chunk = chunk;
	  this.encoding = encoding;
	  this.callback = cb;
	}

	function WritableState(options, stream) {
	  var Duplex = __webpack_require__(48);

	  options = options || {};

	  // the point at which write() starts returning false
	  // Note: 0 is a valid value, means that we always return false if
	  // the entire buffer is not flushed immediately on write()
	  var hwm = options.highWaterMark;
	  var defaultHwm = options.objectMode ? 16 : 16 * 1024;
	  this.highWaterMark = (hwm || hwm === 0) ? hwm : defaultHwm;

	  // object stream flag to indicate whether or not this stream
	  // contains buffers or objects.
	  this.objectMode = !!options.objectMode;

	  if (stream instanceof Duplex)
	    this.objectMode = this.objectMode || !!options.writableObjectMode;

	  // cast to ints.
	  this.highWaterMark = ~~this.highWaterMark;

	  this.needDrain = false;
	  // at the start of calling end()
	  this.ending = false;
	  // when end() has been called, and returned
	  this.ended = false;
	  // when 'finish' is emitted
	  this.finished = false;

	  // should we decode strings into buffers before passing to _write?
	  // this is here so that some node-core streams can optimize string
	  // handling at a lower level.
	  var noDecode = options.decodeStrings === false;
	  this.decodeStrings = !noDecode;

	  // Crypto is kind of old and crusty.  Historically, its default string
	  // encoding is 'binary' so we have to make this configurable.
	  // Everything else in the universe uses 'utf8', though.
	  this.defaultEncoding = options.defaultEncoding || 'utf8';

	  // not an actual buffer we keep track of, but a measurement
	  // of how much we're waiting to get pushed to some underlying
	  // socket or file.
	  this.length = 0;

	  // a flag to see when we're in the middle of a write.
	  this.writing = false;

	  // when true all writes will be buffered until .uncork() call
	  this.corked = 0;

	  // a flag to be able to tell if the onwrite cb is called immediately,
	  // or on a later tick.  We set this to true at first, because any
	  // actions that shouldn't happen until "later" should generally also
	  // not happen before the first write call.
	  this.sync = true;

	  // a flag to know if we're processing previously buffered items, which
	  // may call the _write() callback in the same tick, so that we don't
	  // end up in an overlapped onwrite situation.
	  this.bufferProcessing = false;

	  // the callback that's passed to _write(chunk,cb)
	  this.onwrite = function(er) {
	    onwrite(stream, er);
	  };

	  // the callback that the user supplies to write(chunk,encoding,cb)
	  this.writecb = null;

	  // the amount that is being written when _write is called.
	  this.writelen = 0;

	  this.buffer = [];

	  // number of pending user-supplied write callbacks
	  // this must be 0 before 'finish' can be emitted
	  this.pendingcb = 0;

	  // emit prefinish if the only thing we're waiting for is _write cbs
	  // This is relevant for synchronous Transform streams
	  this.prefinished = false;

	  // True if the error was already emitted and should not be thrown again
	  this.errorEmitted = false;
	}

	function Writable(options) {
	  var Duplex = __webpack_require__(48);

	  // Writable ctor is applied to Duplexes, though they're not
	  // instanceof Writable, they're instanceof Readable.
	  if (!(this instanceof Writable) && !(this instanceof Duplex))
	    return new Writable(options);

	  this._writableState = new WritableState(options, this);

	  // legacy.
	  this.writable = true;

	  Stream.call(this);
	}

	// Otherwise people can pipe Writable streams, which is just wrong.
	Writable.prototype.pipe = function() {
	  this.emit('error', new Error('Cannot pipe. Not readable.'));
	};


	function writeAfterEnd(stream, state, cb) {
	  var er = new Error('write after end');
	  // TODO: defer error events consistently everywhere, not just the cb
	  stream.emit('error', er);
	  process.nextTick(function() {
	    cb(er);
	  });
	}

	// If we get something that is not a buffer, string, null, or undefined,
	// and we're not in objectMode, then that's an error.
	// Otherwise stream chunks are all considered to be of length=1, and the
	// watermarks determine how many objects to keep in the buffer, rather than
	// how many bytes or characters.
	function validChunk(stream, state, chunk, cb) {
	  var valid = true;
	  if (!util.isBuffer(chunk) &&
	      !util.isString(chunk) &&
	      !util.isNullOrUndefined(chunk) &&
	      !state.objectMode) {
	    var er = new TypeError('Invalid non-string/buffer chunk');
	    stream.emit('error', er);
	    process.nextTick(function() {
	      cb(er);
	    });
	    valid = false;
	  }
	  return valid;
	}

	Writable.prototype.write = function(chunk, encoding, cb) {
	  var state = this._writableState;
	  var ret = false;

	  if (util.isFunction(encoding)) {
	    cb = encoding;
	    encoding = null;
	  }

	  if (util.isBuffer(chunk))
	    encoding = 'buffer';
	  else if (!encoding)
	    encoding = state.defaultEncoding;

	  if (!util.isFunction(cb))
	    cb = function() {};

	  if (state.ended)
	    writeAfterEnd(this, state, cb);
	  else if (validChunk(this, state, chunk, cb)) {
	    state.pendingcb++;
	    ret = writeOrBuffer(this, state, chunk, encoding, cb);
	  }

	  return ret;
	};

	Writable.prototype.cork = function() {
	  var state = this._writableState;

	  state.corked++;
	};

	Writable.prototype.uncork = function() {
	  var state = this._writableState;

	  if (state.corked) {
	    state.corked--;

	    if (!state.writing &&
	        !state.corked &&
	        !state.finished &&
	        !state.bufferProcessing &&
	        state.buffer.length)
	      clearBuffer(this, state);
	  }
	};

	function decodeChunk(state, chunk, encoding) {
	  if (!state.objectMode &&
	      state.decodeStrings !== false &&
	      util.isString(chunk)) {
	    chunk = new Buffer(chunk, encoding);
	  }
	  return chunk;
	}

	// if we're already writing something, then just put this
	// in the queue, and wait our turn.  Otherwise, call _write
	// If we return false, then we need a drain event, so set that flag.
	function writeOrBuffer(stream, state, chunk, encoding, cb) {
	  chunk = decodeChunk(state, chunk, encoding);
	  if (util.isBuffer(chunk))
	    encoding = 'buffer';
	  var len = state.objectMode ? 1 : chunk.length;

	  state.length += len;

	  var ret = state.length < state.highWaterMark;
	  // we must ensure that previous needDrain will not be reset to false.
	  if (!ret)
	    state.needDrain = true;

	  if (state.writing || state.corked)
	    state.buffer.push(new WriteReq(chunk, encoding, cb));
	  else
	    doWrite(stream, state, false, len, chunk, encoding, cb);

	  return ret;
	}

	function doWrite(stream, state, writev, len, chunk, encoding, cb) {
	  state.writelen = len;
	  state.writecb = cb;
	  state.writing = true;
	  state.sync = true;
	  if (writev)
	    stream._writev(chunk, state.onwrite);
	  else
	    stream._write(chunk, encoding, state.onwrite);
	  state.sync = false;
	}

	function onwriteError(stream, state, sync, er, cb) {
	  if (sync)
	    process.nextTick(function() {
	      state.pendingcb--;
	      cb(er);
	    });
	  else {
	    state.pendingcb--;
	    cb(er);
	  }

	  stream._writableState.errorEmitted = true;
	  stream.emit('error', er);
	}

	function onwriteStateUpdate(state) {
	  state.writing = false;
	  state.writecb = null;
	  state.length -= state.writelen;
	  state.writelen = 0;
	}

	function onwrite(stream, er) {
	  var state = stream._writableState;
	  var sync = state.sync;
	  var cb = state.writecb;

	  onwriteStateUpdate(state);

	  if (er)
	    onwriteError(stream, state, sync, er, cb);
	  else {
	    // Check if we're actually ready to finish, but don't emit yet
	    var finished = needFinish(stream, state);

	    if (!finished &&
	        !state.corked &&
	        !state.bufferProcessing &&
	        state.buffer.length) {
	      clearBuffer(stream, state);
	    }

	    if (sync) {
	      process.nextTick(function() {
	        afterWrite(stream, state, finished, cb);
	      });
	    } else {
	      afterWrite(stream, state, finished, cb);
	    }
	  }
	}

	function afterWrite(stream, state, finished, cb) {
	  if (!finished)
	    onwriteDrain(stream, state);
	  state.pendingcb--;
	  cb();
	  finishMaybe(stream, state);
	}

	// Must force callback to be called on nextTick, so that we don't
	// emit 'drain' before the write() consumer gets the 'false' return
	// value, and has a chance to attach a 'drain' listener.
	function onwriteDrain(stream, state) {
	  if (state.length === 0 && state.needDrain) {
	    state.needDrain = false;
	    stream.emit('drain');
	  }
	}


	// if there's something in the buffer waiting, then process it
	function clearBuffer(stream, state) {
	  state.bufferProcessing = true;

	  if (stream._writev && state.buffer.length > 1) {
	    // Fast case, write everything using _writev()
	    var cbs = [];
	    for (var c = 0; c < state.buffer.length; c++)
	      cbs.push(state.buffer[c].callback);

	    // count the one we are adding, as well.
	    // TODO(isaacs) clean this up
	    state.pendingcb++;
	    doWrite(stream, state, true, state.length, state.buffer, '', function(err) {
	      for (var i = 0; i < cbs.length; i++) {
	        state.pendingcb--;
	        cbs[i](err);
	      }
	    });

	    // Clear buffer
	    state.buffer = [];
	  } else {
	    // Slow case, write chunks one-by-one
	    for (var c = 0; c < state.buffer.length; c++) {
	      var entry = state.buffer[c];
	      var chunk = entry.chunk;
	      var encoding = entry.encoding;
	      var cb = entry.callback;
	      var len = state.objectMode ? 1 : chunk.length;

	      doWrite(stream, state, false, len, chunk, encoding, cb);

	      // if we didn't call the onwrite immediately, then
	      // it means that we need to wait until it does.
	      // also, that means that the chunk and cb are currently
	      // being processed, so move the buffer counter past them.
	      if (state.writing) {
	        c++;
	        break;
	      }
	    }

	    if (c < state.buffer.length)
	      state.buffer = state.buffer.slice(c);
	    else
	      state.buffer.length = 0;
	  }

	  state.bufferProcessing = false;
	}

	Writable.prototype._write = function(chunk, encoding, cb) {
	  cb(new Error('not implemented'));

	};

	Writable.prototype._writev = null;

	Writable.prototype.end = function(chunk, encoding, cb) {
	  var state = this._writableState;

	  if (util.isFunction(chunk)) {
	    cb = chunk;
	    chunk = null;
	    encoding = null;
	  } else if (util.isFunction(encoding)) {
	    cb = encoding;
	    encoding = null;
	  }

	  if (!util.isNullOrUndefined(chunk))
	    this.write(chunk, encoding);

	  // .end() fully uncorks
	  if (state.corked) {
	    state.corked = 1;
	    this.uncork();
	  }

	  // ignore unnecessary end() calls.
	  if (!state.ending && !state.finished)
	    endWritable(this, state, cb);
	};


	function needFinish(stream, state) {
	  return (state.ending &&
	          state.length === 0 &&
	          !state.finished &&
	          !state.writing);
	}

	function prefinish(stream, state) {
	  if (!state.prefinished) {
	    state.prefinished = true;
	    stream.emit('prefinish');
	  }
	}

	function finishMaybe(stream, state) {
	  var need = needFinish(stream, state);
	  if (need) {
	    if (state.pendingcb === 0) {
	      prefinish(stream, state);
	      state.finished = true;
	      stream.emit('finish');
	    } else
	      prefinish(stream, state);
	  }
	  return need;
	}

	function endWritable(stream, state, cb) {
	  state.ending = true;
	  finishMaybe(stream, state);
	  if (cb) {
	    if (state.finished)
	      process.nextTick(cb);
	    else
	      stream.once('finish', cb);
	  }
	  state.ended = true;
	}

	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(31)))

/***/ },
/* 50 */
/***/ function(module, exports, __webpack_require__) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	var Buffer = __webpack_require__(41).Buffer;

	var isBufferEncoding = Buffer.isEncoding
	  || function(encoding) {
	       switch (encoding && encoding.toLowerCase()) {
	         case 'hex': case 'utf8': case 'utf-8': case 'ascii': case 'binary': case 'base64': case 'ucs2': case 'ucs-2': case 'utf16le': case 'utf-16le': case 'raw': return true;
	         default: return false;
	       }
	     }


	function assertEncoding(encoding) {
	  if (encoding && !isBufferEncoding(encoding)) {
	    throw new Error('Unknown encoding: ' + encoding);
	  }
	}

	// StringDecoder provides an interface for efficiently splitting a series of
	// buffers into a series of JS strings without breaking apart multi-byte
	// characters. CESU-8 is handled as part of the UTF-8 encoding.
	//
	// @TODO Handling all encodings inside a single object makes it very difficult
	// to reason about this code, so it should be split up in the future.
	// @TODO There should be a utf8-strict encoding that rejects invalid UTF-8 code
	// points as used by CESU-8.
	var StringDecoder = exports.StringDecoder = function(encoding) {
	  this.encoding = (encoding || 'utf8').toLowerCase().replace(/[-_]/, '');
	  assertEncoding(encoding);
	  switch (this.encoding) {
	    case 'utf8':
	      // CESU-8 represents each of Surrogate Pair by 3-bytes
	      this.surrogateSize = 3;
	      break;
	    case 'ucs2':
	    case 'utf16le':
	      // UTF-16 represents each of Surrogate Pair by 2-bytes
	      this.surrogateSize = 2;
	      this.detectIncompleteChar = utf16DetectIncompleteChar;
	      break;
	    case 'base64':
	      // Base-64 stores 3 bytes in 4 chars, and pads the remainder.
	      this.surrogateSize = 3;
	      this.detectIncompleteChar = base64DetectIncompleteChar;
	      break;
	    default:
	      this.write = passThroughWrite;
	      return;
	  }

	  // Enough space to store all bytes of a single character. UTF-8 needs 4
	  // bytes, but CESU-8 may require up to 6 (3 bytes per surrogate).
	  this.charBuffer = new Buffer(6);
	  // Number of bytes received for the current incomplete multi-byte character.
	  this.charReceived = 0;
	  // Number of bytes expected for the current incomplete multi-byte character.
	  this.charLength = 0;
	};


	// write decodes the given buffer and returns it as JS string that is
	// guaranteed to not contain any partial multi-byte characters. Any partial
	// character found at the end of the buffer is buffered up, and will be
	// returned when calling write again with the remaining bytes.
	//
	// Note: Converting a Buffer containing an orphan surrogate to a String
	// currently works, but converting a String to a Buffer (via `new Buffer`, or
	// Buffer#write) will replace incomplete surrogates with the unicode
	// replacement character. See https://codereview.chromium.org/121173009/ .
	StringDecoder.prototype.write = function(buffer) {
	  var charStr = '';
	  // if our last write ended with an incomplete multibyte character
	  while (this.charLength) {
	    // determine how many remaining bytes this buffer has to offer for this char
	    var available = (buffer.length >= this.charLength - this.charReceived) ?
	        this.charLength - this.charReceived :
	        buffer.length;

	    // add the new bytes to the char buffer
	    buffer.copy(this.charBuffer, this.charReceived, 0, available);
	    this.charReceived += available;

	    if (this.charReceived < this.charLength) {
	      // still not enough chars in this buffer? wait for more ...
	      return '';
	    }

	    // remove bytes belonging to the current character from the buffer
	    buffer = buffer.slice(available, buffer.length);

	    // get the character that was split
	    charStr = this.charBuffer.slice(0, this.charLength).toString(this.encoding);

	    // CESU-8: lead surrogate (D800-DBFF) is also the incomplete character
	    var charCode = charStr.charCodeAt(charStr.length - 1);
	    if (charCode >= 0xD800 && charCode <= 0xDBFF) {
	      this.charLength += this.surrogateSize;
	      charStr = '';
	      continue;
	    }
	    this.charReceived = this.charLength = 0;

	    // if there are no more bytes in this buffer, just emit our char
	    if (buffer.length === 0) {
	      return charStr;
	    }
	    break;
	  }

	  // determine and set charLength / charReceived
	  this.detectIncompleteChar(buffer);

	  var end = buffer.length;
	  if (this.charLength) {
	    // buffer the incomplete character bytes we got
	    buffer.copy(this.charBuffer, 0, buffer.length - this.charReceived, end);
	    end -= this.charReceived;
	  }

	  charStr += buffer.toString(this.encoding, 0, end);

	  var end = charStr.length - 1;
	  var charCode = charStr.charCodeAt(end);
	  // CESU-8: lead surrogate (D800-DBFF) is also the incomplete character
	  if (charCode >= 0xD800 && charCode <= 0xDBFF) {
	    var size = this.surrogateSize;
	    this.charLength += size;
	    this.charReceived += size;
	    this.charBuffer.copy(this.charBuffer, size, 0, size);
	    buffer.copy(this.charBuffer, 0, 0, size);
	    return charStr.substring(0, end);
	  }

	  // or just emit the charStr
	  return charStr;
	};

	// detectIncompleteChar determines if there is an incomplete UTF-8 character at
	// the end of the given buffer. If so, it sets this.charLength to the byte
	// length that character, and sets this.charReceived to the number of bytes
	// that are available for this character.
	StringDecoder.prototype.detectIncompleteChar = function(buffer) {
	  // determine how many bytes we have to check at the end of this buffer
	  var i = (buffer.length >= 3) ? 3 : buffer.length;

	  // Figure out if one of the last i bytes of our buffer announces an
	  // incomplete char.
	  for (; i > 0; i--) {
	    var c = buffer[buffer.length - i];

	    // See http://en.wikipedia.org/wiki/UTF-8#Description

	    // 110XXXXX
	    if (i == 1 && c >> 5 == 0x06) {
	      this.charLength = 2;
	      break;
	    }

	    // 1110XXXX
	    if (i <= 2 && c >> 4 == 0x0E) {
	      this.charLength = 3;
	      break;
	    }

	    // 11110XXX
	    if (i <= 3 && c >> 3 == 0x1E) {
	      this.charLength = 4;
	      break;
	    }
	  }
	  this.charReceived = i;
	};

	StringDecoder.prototype.end = function(buffer) {
	  var res = '';
	  if (buffer && buffer.length)
	    res = this.write(buffer);

	  if (this.charReceived) {
	    var cr = this.charReceived;
	    var buf = this.charBuffer;
	    var enc = this.encoding;
	    res += buf.slice(0, cr).toString(enc);
	  }

	  return res;
	};

	function passThroughWrite(buffer) {
	  return buffer.toString(this.encoding);
	}

	function utf16DetectIncompleteChar(buffer) {
	  this.charReceived = buffer.length % 2;
	  this.charLength = this.charReceived ? 2 : 0;
	}

	function base64DetectIncompleteChar(buffer) {
	  this.charReceived = buffer.length % 3;
	  this.charLength = this.charReceived ? 3 : 0;
	}


/***/ },
/* 51 */
/***/ function(module, exports, __webpack_require__) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.


	// a transform stream is a readable/writable stream where you do
	// something with the data.  Sometimes it's called a "filter",
	// but that's not a great name for it, since that implies a thing where
	// some bits pass through, and others are simply ignored.  (That would
	// be a valid example of a transform, of course.)
	//
	// While the output is causally related to the input, it's not a
	// necessarily symmetric or synchronous transformation.  For example,
	// a zlib stream might take multiple plain-text writes(), and then
	// emit a single compressed chunk some time in the future.
	//
	// Here's how this works:
	//
	// The Transform stream has all the aspects of the readable and writable
	// stream classes.  When you write(chunk), that calls _write(chunk,cb)
	// internally, and returns false if there's a lot of pending writes
	// buffered up.  When you call read(), that calls _read(n) until
	// there's enough pending readable data buffered up.
	//
	// In a transform stream, the written data is placed in a buffer.  When
	// _read(n) is called, it transforms the queued up data, calling the
	// buffered _write cb's as it consumes chunks.  If consuming a single
	// written chunk would result in multiple output chunks, then the first
	// outputted bit calls the readcb, and subsequent chunks just go into
	// the read buffer, and will cause it to emit 'readable' if necessary.
	//
	// This way, back-pressure is actually determined by the reading side,
	// since _read has to be called to start processing a new chunk.  However,
	// a pathological inflate type of transform can cause excessive buffering
	// here.  For example, imagine a stream where every byte of input is
	// interpreted as an integer from 0-255, and then results in that many
	// bytes of output.  Writing the 4 bytes {ff,ff,ff,ff} would result in
	// 1kb of data being output.  In this case, you could write a very small
	// amount of input, and end up with a very large amount of output.  In
	// such a pathological inflating mechanism, there'd be no way to tell
	// the system to stop doing the transform.  A single 4MB write could
	// cause the system to run out of memory.
	//
	// However, even in such a pathological case, only a single written chunk
	// would be consumed, and then the rest would wait (un-transformed) until
	// the results of the previous transformed chunk were consumed.

	module.exports = Transform;

	var Duplex = __webpack_require__(48);

	/*<replacement>*/
	var util = __webpack_require__(45);
	util.inherits = __webpack_require__(46);
	/*</replacement>*/

	util.inherits(Transform, Duplex);


	function TransformState(options, stream) {
	  this.afterTransform = function(er, data) {
	    return afterTransform(stream, er, data);
	  };

	  this.needTransform = false;
	  this.transforming = false;
	  this.writecb = null;
	  this.writechunk = null;
	}

	function afterTransform(stream, er, data) {
	  var ts = stream._transformState;
	  ts.transforming = false;

	  var cb = ts.writecb;

	  if (!cb)
	    return stream.emit('error', new Error('no writecb in Transform class'));

	  ts.writechunk = null;
	  ts.writecb = null;

	  if (!util.isNullOrUndefined(data))
	    stream.push(data);

	  if (cb)
	    cb(er);

	  var rs = stream._readableState;
	  rs.reading = false;
	  if (rs.needReadable || rs.length < rs.highWaterMark) {
	    stream._read(rs.highWaterMark);
	  }
	}


	function Transform(options) {
	  if (!(this instanceof Transform))
	    return new Transform(options);

	  Duplex.call(this, options);

	  this._transformState = new TransformState(options, this);

	  // when the writable side finishes, then flush out anything remaining.
	  var stream = this;

	  // start out asking for a readable event once data is transformed.
	  this._readableState.needReadable = true;

	  // we have implemented the _read method, and done the other things
	  // that Readable wants before the first _read call, so unset the
	  // sync guard flag.
	  this._readableState.sync = false;

	  this.once('prefinish', function() {
	    if (util.isFunction(this._flush))
	      this._flush(function(er) {
	        done(stream, er);
	      });
	    else
	      done(stream);
	  });
	}

	Transform.prototype.push = function(chunk, encoding) {
	  this._transformState.needTransform = false;
	  return Duplex.prototype.push.call(this, chunk, encoding);
	};

	// This is the part where you do stuff!
	// override this function in implementation classes.
	// 'chunk' is an input chunk.
	//
	// Call `push(newChunk)` to pass along transformed output
	// to the readable side.  You may call 'push' zero or more times.
	//
	// Call `cb(err)` when you are done with this chunk.  If you pass
	// an error, then that'll put the hurt on the whole operation.  If you
	// never call cb(), then you'll never get another chunk.
	Transform.prototype._transform = function(chunk, encoding, cb) {
	  throw new Error('not implemented');
	};

	Transform.prototype._write = function(chunk, encoding, cb) {
	  var ts = this._transformState;
	  ts.writecb = cb;
	  ts.writechunk = chunk;
	  ts.writeencoding = encoding;
	  if (!ts.transforming) {
	    var rs = this._readableState;
	    if (ts.needTransform ||
	        rs.needReadable ||
	        rs.length < rs.highWaterMark)
	      this._read(rs.highWaterMark);
	  }
	};

	// Doesn't matter what the args are here.
	// _transform does all the work.
	// That we got here means that the readable side wants more data.
	Transform.prototype._read = function(n) {
	  var ts = this._transformState;

	  if (!util.isNull(ts.writechunk) && ts.writecb && !ts.transforming) {
	    ts.transforming = true;
	    this._transform(ts.writechunk, ts.writeencoding, ts.afterTransform);
	  } else {
	    // mark that we need a transform, so that any data that comes in
	    // will get processed, now that we've asked for it.
	    ts.needTransform = true;
	  }
	};


	function done(stream, er) {
	  if (er)
	    return stream.emit('error', er);

	  // if there's nothing in the write buffer, then that means
	  // that nothing more will ever be provided
	  var ws = stream._writableState;
	  var ts = stream._transformState;

	  if (ws.length)
	    throw new Error('calling transform done when ws.length != 0');

	  if (ts.transforming)
	    throw new Error('calling transform done when still transforming');

	  return stream.push(null);
	}


/***/ },
/* 52 */
/***/ function(module, exports, __webpack_require__) {

	// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	// a passthrough stream.
	// basically just the most minimal sort of Transform stream.
	// Every written chunk gets output as-is.

	module.exports = PassThrough;

	var Transform = __webpack_require__(51);

	/*<replacement>*/
	var util = __webpack_require__(45);
	util.inherits = __webpack_require__(46);
	/*</replacement>*/

	util.inherits(PassThrough, Transform);

	function PassThrough(options) {
	  if (!(this instanceof PassThrough))
	    return new PassThrough(options);

	  Transform.call(this, options);
	}

	PassThrough.prototype._transform = function(chunk, encoding, cb) {
	  cb(null, chunk);
	};


/***/ },
/* 53 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(49)


/***/ },
/* 54 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(48)


/***/ },
/* 55 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(51)


/***/ },
/* 56 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(52)


/***/ },
/* 57 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global, process) {// Copyright Joyent, Inc. and other Node contributors.
	//
	// Permission is hereby granted, free of charge, to any person obtaining a
	// copy of this software and associated documentation files (the
	// "Software"), to deal in the Software without restriction, including
	// without limitation the rights to use, copy, modify, merge, publish,
	// distribute, sublicense, and/or sell copies of the Software, and to permit
	// persons to whom the Software is furnished to do so, subject to the
	// following conditions:
	//
	// The above copyright notice and this permission notice shall be included
	// in all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
	// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
	// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
	// USE OR OTHER DEALINGS IN THE SOFTWARE.

	var formatRegExp = /%[sdj%]/g;
	exports.format = function(f) {
	  if (!isString(f)) {
	    var objects = [];
	    for (var i = 0; i < arguments.length; i++) {
	      objects.push(inspect(arguments[i]));
	    }
	    return objects.join(' ');
	  }

	  var i = 1;
	  var args = arguments;
	  var len = args.length;
	  var str = String(f).replace(formatRegExp, function(x) {
	    if (x === '%%') return '%';
	    if (i >= len) return x;
	    switch (x) {
	      case '%s': return String(args[i++]);
	      case '%d': return Number(args[i++]);
	      case '%j':
	        try {
	          return JSON.stringify(args[i++]);
	        } catch (_) {
	          return '[Circular]';
	        }
	      default:
	        return x;
	    }
	  });
	  for (var x = args[i]; i < len; x = args[++i]) {
	    if (isNull(x) || !isObject(x)) {
	      str += ' ' + x;
	    } else {
	      str += ' ' + inspect(x);
	    }
	  }
	  return str;
	};


	// Mark that a method should not be used.
	// Returns a modified function which warns once by default.
	// If --no-deprecation is set, then it is a no-op.
	exports.deprecate = function(fn, msg) {
	  // Allow for deprecating things in the process of starting up.
	  if (isUndefined(global.process)) {
	    return function() {
	      return exports.deprecate(fn, msg).apply(this, arguments);
	    };
	  }

	  if (process.noDeprecation === true) {
	    return fn;
	  }

	  var warned = false;
	  function deprecated() {
	    if (!warned) {
	      if (process.throwDeprecation) {
	        throw new Error(msg);
	      } else if (process.traceDeprecation) {
	        console.trace(msg);
	      } else {
	        console.error(msg);
	      }
	      warned = true;
	    }
	    return fn.apply(this, arguments);
	  }

	  return deprecated;
	};


	var debugs = {};
	var debugEnviron;
	exports.debuglog = function(set) {
	  if (isUndefined(debugEnviron))
	    debugEnviron = process.env.NODE_DEBUG || '';
	  set = set.toUpperCase();
	  if (!debugs[set]) {
	    if (new RegExp('\\b' + set + '\\b', 'i').test(debugEnviron)) {
	      var pid = process.pid;
	      debugs[set] = function() {
	        var msg = exports.format.apply(exports, arguments);
	        console.error('%s %d: %s', set, pid, msg);
	      };
	    } else {
	      debugs[set] = function() {};
	    }
	  }
	  return debugs[set];
	};


	/**
	 * Echos the value of a value. Trys to print the value out
	 * in the best way possible given the different types.
	 *
	 * @param {Object} obj The object to print out.
	 * @param {Object} opts Optional options object that alters the output.
	 */
	/* legacy: obj, showHidden, depth, colors*/
	function inspect(obj, opts) {
	  // default options
	  var ctx = {
	    seen: [],
	    stylize: stylizeNoColor
	  };
	  // legacy...
	  if (arguments.length >= 3) ctx.depth = arguments[2];
	  if (arguments.length >= 4) ctx.colors = arguments[3];
	  if (isBoolean(opts)) {
	    // legacy...
	    ctx.showHidden = opts;
	  } else if (opts) {
	    // got an "options" object
	    exports._extend(ctx, opts);
	  }
	  // set default options
	  if (isUndefined(ctx.showHidden)) ctx.showHidden = false;
	  if (isUndefined(ctx.depth)) ctx.depth = 2;
	  if (isUndefined(ctx.colors)) ctx.colors = false;
	  if (isUndefined(ctx.customInspect)) ctx.customInspect = true;
	  if (ctx.colors) ctx.stylize = stylizeWithColor;
	  return formatValue(ctx, obj, ctx.depth);
	}
	exports.inspect = inspect;


	// http://en.wikipedia.org/wiki/ANSI_escape_code#graphics
	inspect.colors = {
	  'bold' : [1, 22],
	  'italic' : [3, 23],
	  'underline' : [4, 24],
	  'inverse' : [7, 27],
	  'white' : [37, 39],
	  'grey' : [90, 39],
	  'black' : [30, 39],
	  'blue' : [34, 39],
	  'cyan' : [36, 39],
	  'green' : [32, 39],
	  'magenta' : [35, 39],
	  'red' : [31, 39],
	  'yellow' : [33, 39]
	};

	// Don't use 'blue' not visible on cmd.exe
	inspect.styles = {
	  'special': 'cyan',
	  'number': 'yellow',
	  'boolean': 'yellow',
	  'undefined': 'grey',
	  'null': 'bold',
	  'string': 'green',
	  'date': 'magenta',
	  // "name": intentionally not styling
	  'regexp': 'red'
	};


	function stylizeWithColor(str, styleType) {
	  var style = inspect.styles[styleType];

	  if (style) {
	    return '\u001b[' + inspect.colors[style][0] + 'm' + str +
	           '\u001b[' + inspect.colors[style][1] + 'm';
	  } else {
	    return str;
	  }
	}


	function stylizeNoColor(str, styleType) {
	  return str;
	}


	function arrayToHash(array) {
	  var hash = {};

	  array.forEach(function(val, idx) {
	    hash[val] = true;
	  });

	  return hash;
	}


	function formatValue(ctx, value, recurseTimes) {
	  // Provide a hook for user-specified inspect functions.
	  // Check that value is an object with an inspect function on it
	  if (ctx.customInspect &&
	      value &&
	      isFunction(value.inspect) &&
	      // Filter out the util module, it's inspect function is special
	      value.inspect !== exports.inspect &&
	      // Also filter out any prototype objects using the circular check.
	      !(value.constructor && value.constructor.prototype === value)) {
	    var ret = value.inspect(recurseTimes, ctx);
	    if (!isString(ret)) {
	      ret = formatValue(ctx, ret, recurseTimes);
	    }
	    return ret;
	  }

	  // Primitive types cannot have properties
	  var primitive = formatPrimitive(ctx, value);
	  if (primitive) {
	    return primitive;
	  }

	  // Look up the keys of the object.
	  var keys = Object.keys(value);
	  var visibleKeys = arrayToHash(keys);

	  if (ctx.showHidden) {
	    keys = Object.getOwnPropertyNames(value);
	  }

	  // IE doesn't make error fields non-enumerable
	  // http://msdn.microsoft.com/en-us/library/ie/dww52sbt(v=vs.94).aspx
	  if (isError(value)
	      && (keys.indexOf('message') >= 0 || keys.indexOf('description') >= 0)) {
	    return formatError(value);
	  }

	  // Some type of object without properties can be shortcutted.
	  if (keys.length === 0) {
	    if (isFunction(value)) {
	      var name = value.name ? ': ' + value.name : '';
	      return ctx.stylize('[Function' + name + ']', 'special');
	    }
	    if (isRegExp(value)) {
	      return ctx.stylize(RegExp.prototype.toString.call(value), 'regexp');
	    }
	    if (isDate(value)) {
	      return ctx.stylize(Date.prototype.toString.call(value), 'date');
	    }
	    if (isError(value)) {
	      return formatError(value);
	    }
	  }

	  var base = '', array = false, braces = ['{', '}'];

	  // Make Array say that they are Array
	  if (isArray(value)) {
	    array = true;
	    braces = ['[', ']'];
	  }

	  // Make functions say that they are functions
	  if (isFunction(value)) {
	    var n = value.name ? ': ' + value.name : '';
	    base = ' [Function' + n + ']';
	  }

	  // Make RegExps say that they are RegExps
	  if (isRegExp(value)) {
	    base = ' ' + RegExp.prototype.toString.call(value);
	  }

	  // Make dates with properties first say the date
	  if (isDate(value)) {
	    base = ' ' + Date.prototype.toUTCString.call(value);
	  }

	  // Make error with message first say the error
	  if (isError(value)) {
	    base = ' ' + formatError(value);
	  }

	  if (keys.length === 0 && (!array || value.length == 0)) {
	    return braces[0] + base + braces[1];
	  }

	  if (recurseTimes < 0) {
	    if (isRegExp(value)) {
	      return ctx.stylize(RegExp.prototype.toString.call(value), 'regexp');
	    } else {
	      return ctx.stylize('[Object]', 'special');
	    }
	  }

	  ctx.seen.push(value);

	  var output;
	  if (array) {
	    output = formatArray(ctx, value, recurseTimes, visibleKeys, keys);
	  } else {
	    output = keys.map(function(key) {
	      return formatProperty(ctx, value, recurseTimes, visibleKeys, key, array);
	    });
	  }

	  ctx.seen.pop();

	  return reduceToSingleString(output, base, braces);
	}


	function formatPrimitive(ctx, value) {
	  if (isUndefined(value))
	    return ctx.stylize('undefined', 'undefined');
	  if (isString(value)) {
	    var simple = '\'' + JSON.stringify(value).replace(/^"|"$/g, '')
	                                             .replace(/'/g, "\\'")
	                                             .replace(/\\"/g, '"') + '\'';
	    return ctx.stylize(simple, 'string');
	  }
	  if (isNumber(value))
	    return ctx.stylize('' + value, 'number');
	  if (isBoolean(value))
	    return ctx.stylize('' + value, 'boolean');
	  // For some reason typeof null is "object", so special case here.
	  if (isNull(value))
	    return ctx.stylize('null', 'null');
	}


	function formatError(value) {
	  return '[' + Error.prototype.toString.call(value) + ']';
	}


	function formatArray(ctx, value, recurseTimes, visibleKeys, keys) {
	  var output = [];
	  for (var i = 0, l = value.length; i < l; ++i) {
	    if (hasOwnProperty(value, String(i))) {
	      output.push(formatProperty(ctx, value, recurseTimes, visibleKeys,
	          String(i), true));
	    } else {
	      output.push('');
	    }
	  }
	  keys.forEach(function(key) {
	    if (!key.match(/^\d+$/)) {
	      output.push(formatProperty(ctx, value, recurseTimes, visibleKeys,
	          key, true));
	    }
	  });
	  return output;
	}


	function formatProperty(ctx, value, recurseTimes, visibleKeys, key, array) {
	  var name, str, desc;
	  desc = Object.getOwnPropertyDescriptor(value, key) || { value: value[key] };
	  if (desc.get) {
	    if (desc.set) {
	      str = ctx.stylize('[Getter/Setter]', 'special');
	    } else {
	      str = ctx.stylize('[Getter]', 'special');
	    }
	  } else {
	    if (desc.set) {
	      str = ctx.stylize('[Setter]', 'special');
	    }
	  }
	  if (!hasOwnProperty(visibleKeys, key)) {
	    name = '[' + key + ']';
	  }
	  if (!str) {
	    if (ctx.seen.indexOf(desc.value) < 0) {
	      if (isNull(recurseTimes)) {
	        str = formatValue(ctx, desc.value, null);
	      } else {
	        str = formatValue(ctx, desc.value, recurseTimes - 1);
	      }
	      if (str.indexOf('\n') > -1) {
	        if (array) {
	          str = str.split('\n').map(function(line) {
	            return '  ' + line;
	          }).join('\n').substr(2);
	        } else {
	          str = '\n' + str.split('\n').map(function(line) {
	            return '   ' + line;
	          }).join('\n');
	        }
	      }
	    } else {
	      str = ctx.stylize('[Circular]', 'special');
	    }
	  }
	  if (isUndefined(name)) {
	    if (array && key.match(/^\d+$/)) {
	      return str;
	    }
	    name = JSON.stringify('' + key);
	    if (name.match(/^"([a-zA-Z_][a-zA-Z_0-9]*)"$/)) {
	      name = name.substr(1, name.length - 2);
	      name = ctx.stylize(name, 'name');
	    } else {
	      name = name.replace(/'/g, "\\'")
	                 .replace(/\\"/g, '"')
	                 .replace(/(^"|"$)/g, "'");
	      name = ctx.stylize(name, 'string');
	    }
	  }

	  return name + ': ' + str;
	}


	function reduceToSingleString(output, base, braces) {
	  var numLinesEst = 0;
	  var length = output.reduce(function(prev, cur) {
	    numLinesEst++;
	    if (cur.indexOf('\n') >= 0) numLinesEst++;
	    return prev + cur.replace(/\u001b\[\d\d?m/g, '').length + 1;
	  }, 0);

	  if (length > 60) {
	    return braces[0] +
	           (base === '' ? '' : base + '\n ') +
	           ' ' +
	           output.join(',\n  ') +
	           ' ' +
	           braces[1];
	  }

	  return braces[0] + base + ' ' + output.join(', ') + ' ' + braces[1];
	}


	// NOTE: These type checking functions intentionally don't use `instanceof`
	// because it is fragile and can be easily faked with `Object.create()`.
	function isArray(ar) {
	  return Array.isArray(ar);
	}
	exports.isArray = isArray;

	function isBoolean(arg) {
	  return typeof arg === 'boolean';
	}
	exports.isBoolean = isBoolean;

	function isNull(arg) {
	  return arg === null;
	}
	exports.isNull = isNull;

	function isNullOrUndefined(arg) {
	  return arg == null;
	}
	exports.isNullOrUndefined = isNullOrUndefined;

	function isNumber(arg) {
	  return typeof arg === 'number';
	}
	exports.isNumber = isNumber;

	function isString(arg) {
	  return typeof arg === 'string';
	}
	exports.isString = isString;

	function isSymbol(arg) {
	  return typeof arg === 'symbol';
	}
	exports.isSymbol = isSymbol;

	function isUndefined(arg) {
	  return arg === void 0;
	}
	exports.isUndefined = isUndefined;

	function isRegExp(re) {
	  return isObject(re) && objectToString(re) === '[object RegExp]';
	}
	exports.isRegExp = isRegExp;

	function isObject(arg) {
	  return typeof arg === 'object' && arg !== null;
	}
	exports.isObject = isObject;

	function isDate(d) {
	  return isObject(d) && objectToString(d) === '[object Date]';
	}
	exports.isDate = isDate;

	function isError(e) {
	  return isObject(e) &&
	      (objectToString(e) === '[object Error]' || e instanceof Error);
	}
	exports.isError = isError;

	function isFunction(arg) {
	  return typeof arg === 'function';
	}
	exports.isFunction = isFunction;

	function isPrimitive(arg) {
	  return arg === null ||
	         typeof arg === 'boolean' ||
	         typeof arg === 'number' ||
	         typeof arg === 'string' ||
	         typeof arg === 'symbol' ||  // ES6 symbol
	         typeof arg === 'undefined';
	}
	exports.isPrimitive = isPrimitive;

	exports.isBuffer = __webpack_require__(58);

	function objectToString(o) {
	  return Object.prototype.toString.call(o);
	}


	function pad(n) {
	  return n < 10 ? '0' + n.toString(10) : n.toString(10);
	}


	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
	              'Oct', 'Nov', 'Dec'];

	// 26 Feb 16:19:34
	function timestamp() {
	  var d = new Date();
	  var time = [pad(d.getHours()),
	              pad(d.getMinutes()),
	              pad(d.getSeconds())].join(':');
	  return [d.getDate(), months[d.getMonth()], time].join(' ');
	}


	// log is just a thin wrapper to console.log that prepends a timestamp
	exports.log = function() {
	  console.log('%s - %s', timestamp(), exports.format.apply(exports, arguments));
	};


	/**
	 * Inherit the prototype methods from one constructor into another.
	 *
	 * The Function.prototype.inherits from lang.js rewritten as a standalone
	 * function (not on Function.prototype). NOTE: If this file is to be loaded
	 * during bootstrapping this function needs to be rewritten using some native
	 * functions as prototype setup using normal JavaScript does not work as
	 * expected during bootstrapping (see mirror.js in r114903).
	 *
	 * @param {function} ctor Constructor function which needs to inherit the
	 *     prototype.
	 * @param {function} superCtor Constructor function to inherit prototype from.
	 */
	exports.inherits = __webpack_require__(59);

	exports._extend = function(origin, add) {
	  // Don't do anything if add isn't an object
	  if (!add || !isObject(add)) return origin;

	  var keys = Object.keys(add);
	  var i = keys.length;
	  while (i--) {
	    origin[keys[i]] = add[keys[i]];
	  }
	  return origin;
	};

	function hasOwnProperty(obj, prop) {
	  return Object.prototype.hasOwnProperty.call(obj, prop);
	}

	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }()), __webpack_require__(31)))

/***/ },
/* 58 */
/***/ function(module, exports) {

	module.exports = function isBuffer(arg) {
	  return arg && typeof arg === 'object'
	    && typeof arg.copy === 'function'
	    && typeof arg.fill === 'function'
	    && typeof arg.readUInt8 === 'function';
	}

/***/ },
/* 59 */
/***/ function(module, exports) {

	if (typeof Object.create === 'function') {
	  // implementation from standard node.js 'util' module
	  module.exports = function inherits(ctor, superCtor) {
	    ctor.super_ = superCtor
	    ctor.prototype = Object.create(superCtor.prototype, {
	      constructor: {
	        value: ctor,
	        enumerable: false,
	        writable: true,
	        configurable: true
	      }
	    });
	  };
	} else {
	  // old school shim for old browsers
	  module.exports = function inherits(ctor, superCtor) {
	    ctor.super_ = superCtor
	    var TempCtor = function () {}
	    TempCtor.prototype = superCtor.prototype
	    ctor.prototype = new TempCtor()
	    ctor.prototype.constructor = ctor
	  }
	}


/***/ },
/* 60 */
/***/ function(module, exports, __webpack_require__) {

	var page = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"webpage\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())).create()
	page.open('http://localhost:1234', function() {

	  function f() {
	    setTimeout(function () {
	      var clsName = page.evaluate(function() {
	        var el = document.getElementById('tests')
	        return el.className
	      })
	      if (!clsName.match(/sink-done/)) f()
	      else {
	        var count = 0
	        var fail = page.evaluate(function () {
	          var t = ''
	          var els = document.querySelectorAll('ol#tests .fail .fail')
	          for (var i = 0; i < els.length; i++) {
	            t += els[i].textContent + '\n'
	          }
	          return {text: t, count: els.length}
	        })
	        var pass = !!clsName.match(/sink-pass/)
	        if (pass) console.log('All tests have passed!')
	        else {
	          console.log(fail.count + ' test(s) failed')
	          console.log(fail.text.trim())
	        }

	        phantom.exit(pass ? 0 : 1)
	      }
	    }, 10)
	  }
	  f()
	})


/***/ },
/* 61 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	  * Reqwest! A general purpose XHR connection manager
	  * license MIT (c) Dustin Diaz 2015
	  * https://github.com/ded/reqwest
	  */
	!function(e,t,n){typeof module!="undefined"&&module.exports?module.exports=n(): true?!(__WEBPACK_AMD_DEFINE_FACTORY__ = (n), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)):t[e]=n()}("reqwest",this,function(){function succeed(e){var t=protocolRe.exec(e.url);return t=t&&t[1]||context.location.protocol,httpsRe.test(t)?twoHundo.test(e.request.status):!!e.request.response}function handleReadyState(e,t,n){return function(){if(e._aborted)return n(e.request);if(e._timedOut)return n(e.request,"Request is aborted: timeout");e.request&&e.request[readyState]==4&&(e.request.onreadystatechange=noop,succeed(e)?t(e.request):n(e.request))}}function setHeaders(e,t){var n=t.headers||{},r;n.Accept=n.Accept||defaultHeaders.accept[t.type]||defaultHeaders.accept["*"];var i=typeof FormData=="function"&&t.data instanceof FormData;!t.crossOrigin&&!n[requestedWith]&&(n[requestedWith]=defaultHeaders.requestedWith),!n[contentType]&&!i&&(n[contentType]=t.contentType||defaultHeaders.contentType);for(r in n)n.hasOwnProperty(r)&&"setRequestHeader"in e&&e.setRequestHeader(r,n[r])}function setCredentials(e,t){typeof t.withCredentials!="undefined"&&typeof e.withCredentials!="undefined"&&(e.withCredentials=!!t.withCredentials)}function generalCallback(e){lastValue=e}function urlappend(e,t){return e+(/\?/.test(e)?"&":"?")+t}function handleJsonp(e,t,n,r){var i=uniqid++,s=e.jsonpCallback||"callback",o=e.jsonpCallbackName||reqwest.getcallbackPrefix(i),u=new RegExp("((^|\\?|&)"+s+")=([^&]+)"),a=r.match(u),f=doc.createElement("script"),l=0,c=navigator.userAgent.indexOf("MSIE 10.0")!==-1;return a?a[3]==="?"?r=r.replace(u,"$1="+o):o=a[3]:r=urlappend(r,s+"="+o),context[o]=generalCallback,f.type="text/javascript",f.src=r,f.async=!0,typeof f.onreadystatechange!="undefined"&&!c&&(f.htmlFor=f.id="_reqwest_"+i),f.onload=f.onreadystatechange=function(){if(f[readyState]&&f[readyState]!=="complete"&&f[readyState]!=="loaded"||l)return!1;f.onload=f.onreadystatechange=null,f.onclick&&f.onclick(),t(lastValue),lastValue=undefined,head.removeChild(f),l=1},head.appendChild(f),{abort:function(){f.onload=f.onreadystatechange=null,n({},"Request is aborted: timeout",{}),lastValue=undefined,head.removeChild(f),l=1}}}function getRequest(e,t){var n=this.o,r=(n.method||"GET").toUpperCase(),i=typeof n=="string"?n:n.url,s=n.processData!==!1&&n.data&&typeof n.data!="string"?reqwest.toQueryString(n.data):n.data||null,o,u=!1;return(n["type"]=="jsonp"||r=="GET")&&s&&(i=urlappend(i,s),s=null),n["type"]=="jsonp"?handleJsonp(n,e,t,i):(o=n.xhr&&n.xhr(n)||xhr(n),o.open(r,i,n.async===!1?!1:!0),setHeaders(o,n),setCredentials(o,n),context[xDomainRequest]&&o instanceof context[xDomainRequest]?(o.onload=e,o.onerror=t,o.onprogress=function(){},u=!0):o.onreadystatechange=handleReadyState(this,e,t),n.before&&n.before(o),u?setTimeout(function(){o.send(s)},200):o.send(s),o)}function Reqwest(e,t){this.o=e,this.fn=t,init.apply(this,arguments)}function setType(e){if(e===null)return undefined;if(e.match("json"))return"json";if(e.match("javascript"))return"js";if(e.match("text"))return"html";if(e.match("xml"))return"xml"}function init(o,fn){function complete(e){o.timeout&&clearTimeout(self.timeout),self.timeout=null;while(self._completeHandlers.length>0)self._completeHandlers.shift()(e)}function success(resp){var type=o.type||resp&&setType(resp.getResponseHeader("Content-Type"));resp=type!=="jsonp"?self.request:resp;var filteredResponse=globalSetupOptions.dataFilter(resp.responseText,type),r=filteredResponse;try{resp.responseText=r}catch(e){}if(r)switch(type){case"json":try{resp=context.JSON?context.JSON.parse(r):eval("("+r+")")}catch(err){return error(resp,"Could not parse JSON in response",err)}break;case"js":resp=eval(r);break;case"html":resp=r;break;case"xml":resp=resp.responseXML&&resp.responseXML.parseError&&resp.responseXML.parseError.errorCode&&resp.responseXML.parseError.reason?null:resp.responseXML}self._responseArgs.resp=resp,self._fulfilled=!0,fn(resp),self._successHandler(resp);while(self._fulfillmentHandlers.length>0)resp=self._fulfillmentHandlers.shift()(resp);complete(resp)}function timedOut(){self._timedOut=!0,self.request.abort()}function error(e,t,n){e=self.request,self._responseArgs.resp=e,self._responseArgs.msg=t,self._responseArgs.t=n,self._erred=!0;while(self._errorHandlers.length>0)self._errorHandlers.shift()(e,t,n);complete(e)}this.url=typeof o=="string"?o:o.url,this.timeout=null,this._fulfilled=!1,this._successHandler=function(){},this._fulfillmentHandlers=[],this._errorHandlers=[],this._completeHandlers=[],this._erred=!1,this._responseArgs={};var self=this;fn=fn||function(){},o.timeout&&(this.timeout=setTimeout(function(){timedOut()},o.timeout)),o.success&&(this._successHandler=function(){o.success.apply(o,arguments)}),o.error&&this._errorHandlers.push(function(){o.error.apply(o,arguments)}),o.complete&&this._completeHandlers.push(function(){o.complete.apply(o,arguments)}),this.request=getRequest.call(this,success,error)}function reqwest(e,t){return new Reqwest(e,t)}function normalize(e){return e?e.replace(/\r?\n/g,"\r\n"):""}function serial(e,t){var n=e.name,r=e.tagName.toLowerCase(),i=function(e){e&&!e.disabled&&t(n,normalize(e.attributes.value&&e.attributes.value.specified?e.value:e.text))},s,o,u,a;if(e.disabled||!n)return;switch(r){case"input":/reset|button|image|file/i.test(e.type)||(s=/checkbox/i.test(e.type),o=/radio/i.test(e.type),u=e.value,(!s&&!o||e.checked)&&t(n,normalize(s&&u===""?"on":u)));break;case"textarea":t(n,normalize(e.value));break;case"select":if(e.type.toLowerCase()==="select-one")i(e.selectedIndex>=0?e.options[e.selectedIndex]:null);else for(a=0;e.length&&a<e.length;a++)e.options[a].selected&&i(e.options[a])}}function eachFormElement(){var e=this,t,n,r=function(t,n){var r,i,s;for(r=0;r<n.length;r++){s=t[byTag](n[r]);for(i=0;i<s.length;i++)serial(s[i],e)}};for(n=0;n<arguments.length;n++)t=arguments[n],/input|select|textarea/i.test(t.tagName)&&serial(t,e),r(t,["input","select","textarea"])}function serializeQueryString(){return reqwest.toQueryString(reqwest.serializeArray.apply(null,arguments))}function serializeHash(){var e={};return eachFormElement.apply(function(t,n){t in e?(e[t]&&!isArray(e[t])&&(e[t]=[e[t]]),e[t].push(n)):e[t]=n},arguments),e}function buildParams(e,t,n,r){var i,s,o,u=/\[\]$/;if(isArray(t))for(s=0;t&&s<t.length;s++)o=t[s],n||u.test(e)?r(e,o):buildParams(e+"["+(typeof o=="object"?s:"")+"]",o,n,r);else if(t&&t.toString()==="[object Object]")for(i in t)buildParams(e+"["+i+"]",t[i],n,r);else r(e,t)}var context=this;if("window"in context)var doc=document,byTag="getElementsByTagName",head=doc[byTag]("head")[0];else{var XHR2;try{var xhr2="xhr2";XHR2=__webpack_require__(23)(xhr2)}catch(ex){throw new Error("Peer dependency `xhr2` required! Please npm install xhr2")}}var httpsRe=/^http/,protocolRe=/(^\w+):\/\//,twoHundo=/^(20\d|1223)$/,readyState="readyState",contentType="Content-Type",requestedWith="X-Requested-With",uniqid=0,callbackPrefix="reqwest_"+ +(new Date),lastValue,xmlHttpRequest="XMLHttpRequest",xDomainRequest="XDomainRequest",noop=function(){},isArray=typeof Array.isArray=="function"?Array.isArray:function(e){return e instanceof Array},defaultHeaders={contentType:"application/x-www-form-urlencoded",requestedWith:xmlHttpRequest,accept:{"*":"text/javascript, text/html, application/xml, text/xml, */*",xml:"application/xml, text/xml",html:"text/html",text:"text/plain",json:"application/json, text/javascript",js:"application/javascript, text/javascript"}},xhr=function(e){if(e.crossOrigin===!0){var t=context[xmlHttpRequest]?new XMLHttpRequest:null;if(t&&"withCredentials"in t)return t;if(context[xDomainRequest])return new XDomainRequest;throw new Error("Browser does not support cross-origin requests")}return context[xmlHttpRequest]?new XMLHttpRequest:XHR2?new XHR2:new ActiveXObject("Microsoft.XMLHTTP")},globalSetupOptions={dataFilter:function(e){return e}};return Reqwest.prototype={abort:function(){this._aborted=!0,this.request.abort()},retry:function(){init.call(this,this.o,this.fn)},then:function(e,t){return e=e||function(){},t=t||function(){},this._fulfilled?this._responseArgs.resp=e(this._responseArgs.resp):this._erred?t(this._responseArgs.resp,this._responseArgs.msg,this._responseArgs.t):(this._fulfillmentHandlers.push(e),this._errorHandlers.push(t)),this},always:function(e){return this._fulfilled||this._erred?e(this._responseArgs.resp):this._completeHandlers.push(e),this},fail:function(e){return this._erred?e(this._responseArgs.resp,this._responseArgs.msg,this._responseArgs.t):this._errorHandlers.push(e),this},"catch":function(e){return this.fail(e)}},reqwest.serializeArray=function(){var e=[];return eachFormElement.apply(function(t,n){e.push({name:t,value:n})},arguments),e},reqwest.serialize=function(){if(arguments.length===0)return"";var e,t,n=Array.prototype.slice.call(arguments,0);return e=n.pop(),e&&e.nodeType&&n.push(e)&&(e=null),e&&(e=e.type),e=="map"?t=serializeHash:e=="array"?t=reqwest.serializeArray:t=serializeQueryString,t.apply(null,n)},reqwest.toQueryString=function(e,t){var n,r,i=t||!1,s=[],o=encodeURIComponent,u=function(e,t){t="function"==typeof t?t():t==null?"":t,s[s.length]=o(e)+"="+o(t)};if(isArray(e))for(r=0;e&&r<e.length;r++)u(e[r].name,e[r].value);else for(n in e)e.hasOwnProperty(n)&&buildParams(n,e[n],i,u);return s.join("&").replace(/%20/g,"+")},reqwest.getcallbackPrefix=function(){return callbackPrefix},reqwest.compat=function(e,t){return e&&(e.type&&(e.method=e.type)&&delete e.type,e.dataType&&(e.type=e.dataType),e.jsonpCallback&&(e.jsonpCallbackName=e.jsonpCallback)&&delete e.jsonpCallback,e.jsonp&&(e.jsonpCallback=e.jsonp)),new Reqwest(e,t)},reqwest.ajaxSetup=function(e){e=e||{};for(var t in e)globalSetupOptions[t]=e[t]},reqwest})

/***/ },
/* 62 */
/***/ function(module, exports) {

	/*!
	  * Reqwest! A general purpose XHR connection manager
	  * license MIT (c) Dustin Diaz 2015
	  * https://github.com/ded/reqwest
	  */


/***/ },
/* 63 */
/***/ function(module, exports, __webpack_require__) {

	!function ($) {
	  var r = __webpack_require__(22)
	    , integrate = function (method) {
	        return function () {
	          var args = Array.prototype.slice.call(arguments, 0)
	            , i = (this && this.length) || 0
	          while (i--) args.unshift(this[i])
	          return r[method].apply(null, args)
	        }
	      }
	    , s = integrate('serialize')
	    , sa = integrate('serializeArray')

	  $.ender({
	      ajax: r
	    , serialize: r.serialize
	    , serializeArray: r.serializeArray
	    , toQueryString: r.toQueryString
	    , ajaxSetup: r.ajaxSetup
	  })

	  $.ender({
	      serialize: s
	    , serializeArray: sa
	  }, true)
	}(ender);


/***/ },
/* 64 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;!function (name, context, definition) {
	  if (typeof module != 'undefined' && module.exports) module.exports = definition()
	  else if (true) !(__WEBPACK_AMD_DEFINE_FACTORY__ = (definition), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__))
	  else context[name] = definition()
	}('reqwest', this, function () {

	  var context = this

	  if ('window' in context) {
	    var doc = document
	      , byTag = 'getElementsByTagName'
	      , head = doc[byTag]('head')[0]
	  } else {
	    var XHR2
	    try {
	      // prevent browserify including xhr2
	      var xhr2 = 'xhr2'
	      XHR2 = __webpack_require__(65)(xhr2)
	    } catch (ex) {
	      throw new Error('Peer dependency `xhr2` required! Please npm install xhr2')
	    }
	  }


	  var httpsRe = /^http/
	    , protocolRe = /(^\w+):\/\//
	    , twoHundo = /^(20\d|1223)$/ //http://stackoverflow.com/questions/10046972/msie-returns-status-code-of-1223-for-ajax-request
	    , readyState = 'readyState'
	    , contentType = 'Content-Type'
	    , requestedWith = 'X-Requested-With'
	    , uniqid = 0
	    , callbackPrefix = 'reqwest_' + (+new Date())
	    , lastValue // data stored by the most recent JSONP callback
	    , xmlHttpRequest = 'XMLHttpRequest'
	    , xDomainRequest = 'XDomainRequest'
	    , noop = function () {}

	    , isArray = typeof Array.isArray == 'function'
	        ? Array.isArray
	        : function (a) {
	            return a instanceof Array
	          }

	    , defaultHeaders = {
	          'contentType': 'application/x-www-form-urlencoded'
	        , 'requestedWith': xmlHttpRequest
	        , 'accept': {
	              '*':  'text/javascript, text/html, application/xml, text/xml, */*'
	            , 'xml':  'application/xml, text/xml'
	            , 'html': 'text/html'
	            , 'text': 'text/plain'
	            , 'json': 'application/json, text/javascript'
	            , 'js':   'application/javascript, text/javascript'
	          }
	      }

	    , xhr = function(o) {
	        // is it x-domain
	        if (o['crossOrigin'] === true) {
	          var xhr = context[xmlHttpRequest] ? new XMLHttpRequest() : null
	          if (xhr && 'withCredentials' in xhr) {
	            return xhr
	          } else if (context[xDomainRequest]) {
	            return new XDomainRequest()
	          } else {
	            throw new Error('Browser does not support cross-origin requests')
	          }
	        } else if (context[xmlHttpRequest]) {
	          return new XMLHttpRequest()
	        } else if (XHR2) {
	          return new XHR2()
	        } else {
	          return new ActiveXObject('Microsoft.XMLHTTP')
	        }
	      }
	    , globalSetupOptions = {
	        dataFilter: function (data) {
	          return data
	        }
	      }

	  function succeed(r) {
	    var protocol = protocolRe.exec(r.url)
	    protocol = (protocol && protocol[1]) || context.location.protocol
	    return httpsRe.test(protocol) ? twoHundo.test(r.request.status) : !!r.request.response
	  }

	  function handleReadyState(r, success, error) {
	    return function () {
	      // use _aborted to mitigate against IE err c00c023f
	      // (can't read props on aborted request objects)
	      if (r._aborted) return error(r.request)
	      if (r._timedOut) return error(r.request, 'Request is aborted: timeout')
	      if (r.request && r.request[readyState] == 4) {
	        r.request.onreadystatechange = noop
	        if (succeed(r)) success(r.request)
	        else
	          error(r.request)
	      }
	    }
	  }

	  function setHeaders(http, o) {
	    var headers = o['headers'] || {}
	      , h

	    headers['Accept'] = headers['Accept']
	      || defaultHeaders['accept'][o['type']]
	      || defaultHeaders['accept']['*']

	    var isAFormData = typeof FormData === 'function' && (o['data'] instanceof FormData);
	    // breaks cross-origin requests with legacy browsers
	    if (!o['crossOrigin'] && !headers[requestedWith]) headers[requestedWith] = defaultHeaders['requestedWith']
	    if (!headers[contentType] && !isAFormData) headers[contentType] = o['contentType'] || defaultHeaders['contentType']
	    for (h in headers)
	      headers.hasOwnProperty(h) && 'setRequestHeader' in http && http.setRequestHeader(h, headers[h])
	  }

	  function setCredentials(http, o) {
	    if (typeof o['withCredentials'] !== 'undefined' && typeof http.withCredentials !== 'undefined') {
	      http.withCredentials = !!o['withCredentials']
	    }
	  }

	  function generalCallback(data) {
	    lastValue = data
	  }

	  function urlappend (url, s) {
	    return url + (/\?/.test(url) ? '&' : '?') + s
	  }

	  function handleJsonp(o, fn, err, url) {
	    var reqId = uniqid++
	      , cbkey = o['jsonpCallback'] || 'callback' // the 'callback' key
	      , cbval = o['jsonpCallbackName'] || reqwest.getcallbackPrefix(reqId)
	      , cbreg = new RegExp('((^|\\?|&)' + cbkey + ')=([^&]+)')
	      , match = url.match(cbreg)
	      , script = doc.createElement('script')
	      , loaded = 0
	      , isIE10 = navigator.userAgent.indexOf('MSIE 10.0') !== -1

	    if (match) {
	      if (match[3] === '?') {
	        url = url.replace(cbreg, '$1=' + cbval) // wildcard callback func name
	      } else {
	        cbval = match[3] // provided callback func name
	      }
	    } else {
	      url = urlappend(url, cbkey + '=' + cbval) // no callback details, add 'em
	    }

	    context[cbval] = generalCallback

	    script.type = 'text/javascript'
	    script.src = url
	    script.async = true
	    if (typeof script.onreadystatechange !== 'undefined' && !isIE10) {
	      // need this for IE due to out-of-order onreadystatechange(), binding script
	      // execution to an event listener gives us control over when the script
	      // is executed. See http://jaubourg.net/2010/07/loading-script-as-onclick-handler-of.html
	      script.htmlFor = script.id = '_reqwest_' + reqId
	    }

	    script.onload = script.onreadystatechange = function () {
	      if ((script[readyState] && script[readyState] !== 'complete' && script[readyState] !== 'loaded') || loaded) {
	        return false
	      }
	      script.onload = script.onreadystatechange = null
	      script.onclick && script.onclick()
	      // Call the user callback with the last value stored and clean up values and scripts.
	      fn(lastValue)
	      lastValue = undefined
	      head.removeChild(script)
	      loaded = 1
	    }

	    // Add the script to the DOM head
	    head.appendChild(script)

	    // Enable JSONP timeout
	    return {
	      abort: function () {
	        script.onload = script.onreadystatechange = null
	        err({}, 'Request is aborted: timeout', {})
	        lastValue = undefined
	        head.removeChild(script)
	        loaded = 1
	      }
	    }
	  }

	  function getRequest(fn, err) {
	    var o = this.o
	      , method = (o['method'] || 'GET').toUpperCase()
	      , url = typeof o === 'string' ? o : o['url']
	      // convert non-string objects to query-string form unless o['processData'] is false
	      , data = (o['processData'] !== false && o['data'] && typeof o['data'] !== 'string')
	        ? reqwest.toQueryString(o['data'])
	        : (o['data'] || null)
	      , http
	      , sendWait = false

	    // if we're working on a GET request and we have data then we should append
	    // query string to end of URL and not post data
	    if ((o['type'] == 'jsonp' || method == 'GET') && data) {
	      url = urlappend(url, data)
	      data = null
	    }

	    if (o['type'] == 'jsonp') return handleJsonp(o, fn, err, url)

	    // get the xhr from the factory if passed
	    // if the factory returns null, fall-back to ours
	    http = (o.xhr && o.xhr(o)) || xhr(o)

	    http.open(method, url, o['async'] === false ? false : true)
	    setHeaders(http, o)
	    setCredentials(http, o)
	    if (context[xDomainRequest] && http instanceof context[xDomainRequest]) {
	        http.onload = fn
	        http.onerror = err
	        // NOTE: see
	        // http://social.msdn.microsoft.com/Forums/en-US/iewebdevelopment/thread/30ef3add-767c-4436-b8a9-f1ca19b4812e
	        http.onprogress = function() {}
	        sendWait = true
	    } else {
	      http.onreadystatechange = handleReadyState(this, fn, err)
	    }
	    o['before'] && o['before'](http)
	    if (sendWait) {
	      setTimeout(function () {
	        http.send(data)
	      }, 200)
	    } else {
	      http.send(data)
	    }
	    return http
	  }

	  function Reqwest(o, fn) {
	    this.o = o
	    this.fn = fn

	    init.apply(this, arguments)
	  }

	  function setType(header) {
	    // json, javascript, text/plain, text/html, xml
	    if (header === null) return undefined; //In case of no content-type.
	    if (header.match('json')) return 'json'
	    if (header.match('javascript')) return 'js'
	    if (header.match('text')) return 'html'
	    if (header.match('xml')) return 'xml'
	  }

	  function init(o, fn) {

	    this.url = typeof o == 'string' ? o : o['url']
	    this.timeout = null

	    // whether request has been fulfilled for purpose
	    // of tracking the Promises
	    this._fulfilled = false
	    // success handlers
	    this._successHandler = function(){}
	    this._fulfillmentHandlers = []
	    // error handlers
	    this._errorHandlers = []
	    // complete (both success and fail) handlers
	    this._completeHandlers = []
	    this._erred = false
	    this._responseArgs = {}

	    var self = this

	    fn = fn || function () {}

	    if (o['timeout']) {
	      this.timeout = setTimeout(function () {
	        timedOut()
	      }, o['timeout'])
	    }

	    if (o['success']) {
	      this._successHandler = function () {
	        o['success'].apply(o, arguments)
	      }
	    }

	    if (o['error']) {
	      this._errorHandlers.push(function () {
	        o['error'].apply(o, arguments)
	      })
	    }

	    if (o['complete']) {
	      this._completeHandlers.push(function () {
	        o['complete'].apply(o, arguments)
	      })
	    }

	    function complete (resp) {
	      o['timeout'] && clearTimeout(self.timeout)
	      self.timeout = null
	      while (self._completeHandlers.length > 0) {
	        self._completeHandlers.shift()(resp)
	      }
	    }

	    function success (resp) {
	      var type = o['type'] || resp && setType(resp.getResponseHeader('Content-Type')) // resp can be undefined in IE
	      resp = (type !== 'jsonp') ? self.request : resp
	      // use global data filter on response text
	      var filteredResponse = globalSetupOptions.dataFilter(resp.responseText, type)
	        , r = filteredResponse
	      try {
	        resp.responseText = r
	      } catch (e) {
	        // can't assign this in IE<=8, just ignore
	      }
	      if (r) {
	        switch (type) {
	        case 'json':
	          try {
	            resp = context.JSON ? context.JSON.parse(r) : eval('(' + r + ')')
	          } catch (err) {
	            return error(resp, 'Could not parse JSON in response', err)
	          }
	          break
	        case 'js':
	          resp = eval(r)
	          break
	        case 'html':
	          resp = r
	          break
	        case 'xml':
	          resp = resp.responseXML
	              && resp.responseXML.parseError // IE trololo
	              && resp.responseXML.parseError.errorCode
	              && resp.responseXML.parseError.reason
	            ? null
	            : resp.responseXML
	          break
	        }
	      }

	      self._responseArgs.resp = resp
	      self._fulfilled = true
	      fn(resp)
	      self._successHandler(resp)
	      while (self._fulfillmentHandlers.length > 0) {
	        resp = self._fulfillmentHandlers.shift()(resp)
	      }

	      complete(resp)
	    }

	    function timedOut() {
	      self._timedOut = true
	      self.request.abort()
	    }

	    function error(resp, msg, t) {
	      resp = self.request
	      self._responseArgs.resp = resp
	      self._responseArgs.msg = msg
	      self._responseArgs.t = t
	      self._erred = true
	      while (self._errorHandlers.length > 0) {
	        self._errorHandlers.shift()(resp, msg, t)
	      }
	      complete(resp)
	    }

	    this.request = getRequest.call(this, success, error)
	  }

	  Reqwest.prototype = {
	    abort: function () {
	      this._aborted = true
	      this.request.abort()
	    }

	  , retry: function () {
	      init.call(this, this.o, this.fn)
	    }

	    /**
	     * Small deviation from the Promises A CommonJs specification
	     * http://wiki.commonjs.org/wiki/Promises/A
	     */

	    /**
	     * `then` will execute upon successful requests
	     */
	  , then: function (success, fail) {
	      success = success || function () {}
	      fail = fail || function () {}
	      if (this._fulfilled) {
	        this._responseArgs.resp = success(this._responseArgs.resp)
	      } else if (this._erred) {
	        fail(this._responseArgs.resp, this._responseArgs.msg, this._responseArgs.t)
	      } else {
	        this._fulfillmentHandlers.push(success)
	        this._errorHandlers.push(fail)
	      }
	      return this
	    }

	    /**
	     * `always` will execute whether the request succeeds or fails
	     */
	  , always: function (fn) {
	      if (this._fulfilled || this._erred) {
	        fn(this._responseArgs.resp)
	      } else {
	        this._completeHandlers.push(fn)
	      }
	      return this
	    }

	    /**
	     * `fail` will execute when the request fails
	     */
	  , fail: function (fn) {
	      if (this._erred) {
	        fn(this._responseArgs.resp, this._responseArgs.msg, this._responseArgs.t)
	      } else {
	        this._errorHandlers.push(fn)
	      }
	      return this
	    }
	  , 'catch': function (fn) {
	      return this.fail(fn)
	    }
	  }

	  function reqwest(o, fn) {
	    return new Reqwest(o, fn)
	  }

	  // normalize newline variants according to spec -> CRLF
	  function normalize(s) {
	    return s ? s.replace(/\r?\n/g, '\r\n') : ''
	  }

	  function serial(el, cb) {
	    var n = el.name
	      , t = el.tagName.toLowerCase()
	      , optCb = function (o) {
	          // IE gives value="" even where there is no value attribute
	          // 'specified' ref: http://www.w3.org/TR/DOM-Level-3-Core/core.html#ID-862529273
	          if (o && !o['disabled'])
	            cb(n, normalize(o['attributes']['value'] && o['attributes']['value']['specified'] ? o['value'] : o['text']))
	        }
	      , ch, ra, val, i

	    // don't serialize elements that are disabled or without a name
	    if (el.disabled || !n) return

	    switch (t) {
	    case 'input':
	      if (!/reset|button|image|file/i.test(el.type)) {
	        ch = /checkbox/i.test(el.type)
	        ra = /radio/i.test(el.type)
	        val = el.value
	        // WebKit gives us "" instead of "on" if a checkbox has no value, so correct it here
	        ;(!(ch || ra) || el.checked) && cb(n, normalize(ch && val === '' ? 'on' : val))
	      }
	      break
	    case 'textarea':
	      cb(n, normalize(el.value))
	      break
	    case 'select':
	      if (el.type.toLowerCase() === 'select-one') {
	        optCb(el.selectedIndex >= 0 ? el.options[el.selectedIndex] : null)
	      } else {
	        for (i = 0; el.length && i < el.length; i++) {
	          el.options[i].selected && optCb(el.options[i])
	        }
	      }
	      break
	    }
	  }

	  // collect up all form elements found from the passed argument elements all
	  // the way down to child elements; pass a '<form>' or form fields.
	  // called with 'this'=callback to use for serial() on each element
	  function eachFormElement() {
	    var cb = this
	      , e, i
	      , serializeSubtags = function (e, tags) {
	          var i, j, fa
	          for (i = 0; i < tags.length; i++) {
	            fa = e[byTag](tags[i])
	            for (j = 0; j < fa.length; j++) serial(fa[j], cb)
	          }
	        }

	    for (i = 0; i < arguments.length; i++) {
	      e = arguments[i]
	      if (/input|select|textarea/i.test(e.tagName)) serial(e, cb)
	      serializeSubtags(e, [ 'input', 'select', 'textarea' ])
	    }
	  }

	  // standard query string style serialization
	  function serializeQueryString() {
	    return reqwest.toQueryString(reqwest.serializeArray.apply(null, arguments))
	  }

	  // { 'name': 'value', ... } style serialization
	  function serializeHash() {
	    var hash = {}
	    eachFormElement.apply(function (name, value) {
	      if (name in hash) {
	        hash[name] && !isArray(hash[name]) && (hash[name] = [hash[name]])
	        hash[name].push(value)
	      } else hash[name] = value
	    }, arguments)
	    return hash
	  }

	  // [ { name: 'name', value: 'value' }, ... ] style serialization
	  reqwest.serializeArray = function () {
	    var arr = []
	    eachFormElement.apply(function (name, value) {
	      arr.push({name: name, value: value})
	    }, arguments)
	    return arr
	  }

	  reqwest.serialize = function () {
	    if (arguments.length === 0) return ''
	    var opt, fn
	      , args = Array.prototype.slice.call(arguments, 0)

	    opt = args.pop()
	    opt && opt.nodeType && args.push(opt) && (opt = null)
	    opt && (opt = opt.type)

	    if (opt == 'map') fn = serializeHash
	    else if (opt == 'array') fn = reqwest.serializeArray
	    else fn = serializeQueryString

	    return fn.apply(null, args)
	  }

	  reqwest.toQueryString = function (o, trad) {
	    var prefix, i
	      , traditional = trad || false
	      , s = []
	      , enc = encodeURIComponent
	      , add = function (key, value) {
	          // If value is a function, invoke it and return its value
	          value = ('function' === typeof value) ? value() : (value == null ? '' : value)
	          s[s.length] = enc(key) + '=' + enc(value)
	        }
	    // If an array was passed in, assume that it is an array of form elements.
	    if (isArray(o)) {
	      for (i = 0; o && i < o.length; i++) add(o[i]['name'], o[i]['value'])
	    } else {
	      // If traditional, encode the "old" way (the way 1.3.2 or older
	      // did it), otherwise encode params recursively.
	      for (prefix in o) {
	        if (o.hasOwnProperty(prefix)) buildParams(prefix, o[prefix], traditional, add)
	      }
	    }

	    // spaces should be + according to spec
	    return s.join('&').replace(/%20/g, '+')
	  }

	  function buildParams(prefix, obj, traditional, add) {
	    var name, i, v
	      , rbracket = /\[\]$/

	    if (isArray(obj)) {
	      // Serialize array item.
	      for (i = 0; obj && i < obj.length; i++) {
	        v = obj[i]
	        if (traditional || rbracket.test(prefix)) {
	          // Treat each array item as a scalar.
	          add(prefix, v)
	        } else {
	          buildParams(prefix + '[' + (typeof v === 'object' ? i : '') + ']', v, traditional, add)
	        }
	      }
	    } else if (obj && obj.toString() === '[object Object]') {
	      // Serialize object item.
	      for (name in obj) {
	        buildParams(prefix + '[' + name + ']', obj[name], traditional, add)
	      }

	    } else {
	      // Serialize scalar item.
	      add(prefix, obj)
	    }
	  }

	  reqwest.getcallbackPrefix = function () {
	    return callbackPrefix
	  }

	  // jQuery and Zepto compatibility, differences can be remapped here so you can call
	  // .ajax.compat(options, callback)
	  reqwest.compat = function (o, fn) {
	    if (o) {
	      o['type'] && (o['method'] = o['type']) && delete o['type']
	      o['dataType'] && (o['type'] = o['dataType'])
	      o['jsonpCallback'] && (o['jsonpCallbackName'] = o['jsonpCallback']) && delete o['jsonpCallback']
	      o['jsonp'] && (o['jsonpCallback'] = o['jsonp'])
	    }
	    return new Reqwest(o, fn)
	  }

	  reqwest.ajaxSetup = function (options) {
	    options = options || {}
	    for (var k in options) {
	      globalSetupOptions[k] = options[k]
	    }
	  }

	  return reqwest
	});


/***/ },
/* 65 */
/***/ function(module, exports, __webpack_require__) {

	var map = {
		"./copyright": 62,
		"./copyright.js": 62,
		"./ender": 63,
		"./ender.js": 63,
		"./reqwest": 64,
		"./reqwest.js": 64
	};
	function webpackContext(req) {
		return __webpack_require__(webpackContextResolve(req));
	};
	function webpackContextResolve(req) {
		return map[req] || (function() { throw new Error("Cannot find module '" + req + "'.") }());
	};
	webpackContext.keys = function webpackContextKeys() {
		return Object.keys(map);
	};
	webpackContext.resolve = webpackContextResolve;
	module.exports = webpackContext;
	webpackContext.id = 65;


/***/ },
/* 66 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(process) {var spawn = __webpack_require__(!(function webpackMissingModule() { var e = new Error("Cannot find module \"child_process\""); e.code = 'MODULE_NOT_FOUND'; throw e; }())).spawn
	  , server  = spawn('node', ['make/tests.js'])
	  , phantom = spawn('./vendor/phantomjs', ['./phantom.js'])


	phantom.stdout.on('data', function (data) {
	  console.log('stdout: ' + data);
	})

	phantom.on('exit', function (code, signal) {
	  var outcome = code == 0 ? 'passed' : 'failed'
	  console.log('Reqwest tests have %s', outcome, code)
	  server.kill('SIGHUP')
	  process.exit(code)
	})

	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(31)))

/***/ },
/* 67 */
/***/ function(module, exports) {

	/*!
	  * Ender: open module JavaScript framework (client-lib)
	  * copyright Dustin Diaz & Jacob Thornton 2011-2012 (@ded @fat)
	  * http://ender.no.de
	  * License MIT
	  */
	(function (context) {

	  // a global object for node.js module compatiblity
	  // ============================================

	  context['global'] = context

	  // Implements simple module system
	  // losely based on CommonJS Modules spec v1.1.1
	  // ============================================

	  var modules = {}
	    , old = context['$']
	    , oldRequire = context['require']
	    , oldProvide = context['provide']

	  function require (identifier) {
	    // modules can be required from ender's build system, or found on the window
	    var module = modules['$' + identifier] || window[identifier]
	    if (!module) throw new Error("Ender Error: Requested module '" + identifier + "' has not been defined.")
	    return module
	  }

	  function provide (name, what) {
	    return (modules['$' + name] = what)
	  }

	  context['provide'] = provide
	  context['require'] = require

	  function aug(o, o2) {
	    for (var k in o2) k != 'noConflict' && k != '_VERSION' && (o[k] = o2[k])
	    return o
	  }

	  /**
	   * main Ender return object
	   * @constructor
	   * @param {Array|Node|string} s a CSS selector or DOM node(s)
	   * @param {Array.|Node} r a root node(s)
	   */
	  function Ender(s, r) {
	    var elements
	      , i

	    this.selector = s
	    // string || node || nodelist || window
	    if (typeof s == 'undefined') {
	      elements = []
	      this.selector = ''
	    } else if (typeof s == 'string' || s.nodeName || (s.length && 'item' in s) || s == window) {
	      elements = ender._select(s, r)
	    } else {
	      elements = isFinite(s.length) ? s : [s]
	    }
	    this.length = elements.length
	    for (i = this.length; i--;) this[i] = elements[i]
	  }

	  /**
	   * @param {function(el, i, inst)} fn
	   * @param {Object} opt_scope
	   * @returns {Ender}
	   */
	  Ender.prototype['forEach'] = function (fn, opt_scope) {
	    var i, l
	    // opt out of native forEach so we can intentionally call our own scope
	    // defaulting to the current item and be able to return self
	    for (i = 0, l = this.length; i < l; ++i) i in this && fn.call(opt_scope || this[i], this[i], i, this)
	    // return self for chaining
	    return this
	  }

	  Ender.prototype.$ = ender // handy reference to self


	  function ender(s, r) {
	    return new Ender(s, r)
	  }

	  ender['_VERSION'] = '0.4.3-dev'

	  ender.fn = Ender.prototype // for easy compat to jQuery plugins

	  ender.ender = function (o, chain) {
	    aug(chain ? Ender.prototype : ender, o)
	  }

	  ender._select = function (s, r) {
	    if (typeof s == 'string') return (r || document).querySelectorAll(s)
	    if (s.nodeName) return [s]
	    return s
	  }


	  // use callback to receive Ender's require & provide
	  ender.noConflict = function (callback) {
	    context['$'] = old
	    if (callback) {
	      context['provide'] = oldProvide
	      context['require'] = oldRequire
	      callback(require, provide, this)
	    }
	    return this
	  }

	  if (typeof module !== 'undefined' && module.exports) module.exports = ender
	  // use subscript notation as extern for Closure compilation
	  context['ender'] = context['$'] = context['ender'] || ender

	}(this));


/***/ },
/* 68 */,
/* 69 */
/***/ function(module, exports) {

	window.boosh = 'boosh';

/***/ },
/* 70 */,
/* 71 */,
/* 72 */,
/* 73 */
/***/ function(module, exports) {

	reqwest_0({ "boosh": "boosh" });

/***/ },
/* 74 */
/***/ function(module, exports) {

	bar({ "boosh": "boosh" });

/***/ },
/* 75 */
/***/ function(module, exports) {

	reqwest_2({ "boosh": "boosh" });


/***/ },
/* 76 */
/***/ function(module, exports) {

	reqwest_0({ "a": "a" });


/***/ },
/* 77 */
/***/ function(module, exports) {

	reqwest_0({ "b": "b" });


/***/ },
/* 78 */
/***/ function(module, exports) {

	reqwest_0({ "c": "c" });


/***/ },
/* 79 */,
/* 80 */,
/* 81 */
/***/ function(module, exports) {

	/*jshint maxlen:80*/
	/*global reqwest:true, sink:true, start:true, ender:true, v:true, boosh:true*/

	(function (ajax) {
	  var BIND_ARGS = 'bind'
	    , PASS_ARGS = 'pass'
	    , FakeXHR = (function () {
	        function FakeXHR () {
	          this.args = {}
	          FakeXHR.last = this
	        }
	        FakeXHR.setup = function () {
	          FakeXHR.oldxhr = window['XMLHttpRequest']
	          FakeXHR.oldaxo = window['ActiveXObject']
	          window['XMLHttpRequest'] = FakeXHR
	          window['ActiveXObject'] = FakeXHR
	          FakeXHR.last = null
	        }
	        FakeXHR.restore = function () {
	          window['XMLHttpRequest'] = FakeXHR.oldxhr
	          window['ActiveXObject'] = FakeXHR.oldaxo
	        }
	        FakeXHR.prototype.methodCallCount = function (name) {
	          return this.args[name] ? this.args[name].length : 0
	        }
	        FakeXHR.prototype.methodCallArgs = function (name, i, j) {
	          var a = this.args[name]
	              && this.args[name].length > i ? this.args[name][i] : null
	          if (arguments.length > 2) return a && a.length > j ? a[j] : null
	          return a
	        }
	        v.each(['open', 'send', 'setRequestHeader' ], function (f) {
	          FakeXHR.prototype[f] = function () {
	            if (!this.args[f]) this.args[f] = []
	            this.args[f].push(arguments)
	          }
	        })
	        return FakeXHR
	      }())

	  sink('Setup', function (test, ok, before, after) {
	    before(function () {
	      ajax.ajaxSetup({
	        dataFilter: function (resp, type) {
	          // example filter to prevent json hijacking
	          return resp.substring('])}while(1);</x>'.length)
	        }
	      })
	    })
	    after(function () {
	      ajax.ajaxSetup({
	        // reset to original data filter
	        dataFilter: function (resp, type) {
	          return resp
	        }
	      })
	    })
	    test('dataFilter', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures_with_prefix.json'
	        , type: 'json'
	        , success: function (resp) {
	            ok(resp, 'received response')
	            ok(
	                resp && resp.boosh == 'boosh'
	              , 'correctly evaluated response as JSON'
	            )
	            complete()
	          }
	      })
	    })
	  })

	  sink('Mime Types', function (test, ok) {
	    test('JSON', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures.json'
	        , type: 'json'
	        , success: function (resp) {
	            ok(resp, 'received response')
	            ok(
	                resp && resp.boosh == 'boosh'
	              , 'correctly evaluated response as JSON'
	            )
	            complete()
	          }
	      })
	    })

	    test('JSONP', function (complete) {
	      // stub callback prefix
	      reqwest.getcallbackPrefix = function (id) {
	        return 'reqwest_' + id
	      }
	      ajax({
	          url: '/tests/fixtures/fixtures_jsonp.jsonp?callback=?'
	        , type: 'jsonp'
	        , success: function (resp) {
	            ok(resp, 'received response for unique generated callback')
	            ok(
	                resp && resp.boosh == 'boosh'
	              , 'correctly evaled response for unique generated cb as JSONP'
	            )
	            complete()
	          }
	      })
	    })

	    test('JS', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures.js'
	        , type: 'js'
	        , success: function () {
	            ok(
	                typeof boosh !== 'undefined' && boosh == 'boosh'
	              , 'evaluated response as JavaScript'
	            )
	            complete()
	          }
	      })
	    })

	    test('HTML', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures.html'
	        , type: 'html'
	        , success: function (resp) {
	            ok(resp == '<p>boosh</p>', 'evaluated response as HTML')
	            complete()
	          }
	      })
	    })

	    test('XML', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures.xml'
	        , type: 'xml'
	        , success: function (resp) {
	            ok(resp
	                && resp.documentElement
	                && resp.documentElement.nodeName == 'root'
	              , 'XML Response root is <root>'
	            )
	            ok(resp
	                && resp.documentElement
	                && resp.documentElement.hasChildNodes
	                && resp.documentElement.firstChild.nodeName == 'boosh'
	                && resp.documentElement.firstChild.firstChild.nodeValue
	                    == 'boosh'
	              , 'Correct XML response'
	            )
	            complete()
	          }
	        , error: function (err) {
	            ok(false, err.responseText)
	            complete()
	          }
	      })
	    })

	    test('XML (404)', function (complete) {
	      ajax({
	          url:'/tests/fixtures/badfixtures.xml'
	        , type:'xml'
	        , success: function (resp) {
	            if (resp == null) {
	              ok(true, 'XML response is null')
	              complete()
	            } else {
	              ok(resp
	                  && resp.documentElement
	                  && resp.documentElement.firstChild
	                  && (/error/i).test(resp.documentElement.firstChild.nodeValue)
	                , 'XML response reports parsing error'
	              )
	              complete()
	            }
	          }
	        , error: function () {
	            ok(true, 'No XML response (error())')
	            complete()
	          }
	      })
	    })
	  })

	  sink('JSONP', function (test, ok) {
	    test('Named callback in query string', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures_jsonp2.jsonp?foo=bar'
	        , type: 'jsonp'
	        , jsonpCallback: 'foo'
	        , success: function (resp) {
	            ok(resp, 'received response for custom callback')
	            ok(
	                resp && resp.boosh == 'boosh'
	              , 'correctly evaluated response as JSONP with custom callback'
	            )
	            complete()
	          }
	      })
	    })

	    test('Unnamed callback in query string', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures_jsonp3.jsonp?foo=?'
	        , type: 'jsonp'
	        , jsonpCallback: 'foo'
	        , success: function (resp) {
	            ok(resp, 'received response for custom wildcard callback')
	            ok(
	                resp && resp.boosh == 'boosh'
	              , 'correctly evaled response as JSONP with custom wildcard cb'
	            )
	            complete()
	          }
	      })
	    })

	    test('No callback, no query string', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures_jsonp3.jsonp'
	        , type: 'jsonp'
	        , jsonpCallback: 'foo'
	        , success: function (resp) {
	            ok(resp, 'received response for custom wildcard callback')
	            ok(
	                resp && resp.boosh == 'boosh'
	              , 'correctly evaled response as JSONP with custom cb not in url'
	            )
	            complete()
	          }
	      })
	    })

	    test('No callback in existing query string', function (complete) {
	      ajax({
	          url: '/tests/none.jsonp?echo&somevar=some+long+str+here'
	        , type: 'jsonp'
	        , jsonpCallbackName: 'yohoho'
	        , success: function (resp) {
	            ok(resp && resp.query, 'received response from echo callback')
	            ok(
	                resp && resp.query && resp.query.somevar == 'some long str here'
	              , 'correctly evaluated response as JSONP with echo callback'
	            )
	            complete()
	          }
	      })
	    })

	    test('Append data to existing query string', function (complete) {
	      ajax({
	          url: '/tests/none.jsonp?echo' // should append &somevar...
	        , type: 'jsonp'
	        , data: { somevar: 'some long str here', anothervar: 'yo ho ho!' }
	        , success: function (resp) {
	            ok(resp && resp.query, 'received response from echo callback')
	            ok(
	                resp && resp.query && resp.query.somevar == 'some long str here'
	              , 'correctly sent and received data object from JSONP echo (1)'
	            )
	            ok(
	                resp && resp.query && resp.query.anothervar == 'yo ho ho!'
	              , 'correctly sent and received data object from JSONP echo (2)'
	            )
	            complete()
	          }
	      })
	    })

	    test('Generate complete query string from data', function (complete) {
	      ajax({
	          url: '/tests/none.jsonp' // should append ?echo...etc.
	        , type: 'jsonp'
	        , data: [
	              { name: 'somevar', value: 'some long str here' }
	            , { name: 'anothervar', value: 'yo ho ho!' }
	            , { name: 'echo', value: true }
	          ]
	        , success: function (resp) {
	            ok(resp && resp.query, 'received response from echo callback')
	            ok(
	                resp && resp.query && resp.query.somevar == 'some long str here'
	              , 'correctly sent and received data array from JSONP echo (1)'
	            )
	            ok(
	                resp && resp.query && resp.query.anothervar == 'yo ho ho!'
	              , 'correctly sent and received data array from JSONP echo (2)'
	            )
	            complete()
	          }
	      })
	    })

	    test('Append data to query string and insert callback name'
	        , function (complete) {

	      ajax({
	          // should append data and match callback correctly
	          url: '/tests/none.jsonp?callback=?'
	        , type: 'jsonp'
	        , jsonpCallbackName: 'reqwest_foo'
	        , data: { foo: 'bar', boo: 'baz', echo: true }
	        , success: function (resp) {
	            ok(resp && resp.query, 'received response from echo callback')
	            ok(
	                resp && resp.query && resp.query.callback == 'reqwest_foo'
	              , 'correctly matched callback in URL'
	            )
	            complete()
	          }
	      })
	    })
	  })

	  sink('Callbacks', function (test, ok) {

	    test('sync version', function (done) {
	      var r = ajax({
	        method: 'get'
	      , url: '/tests/fixtures/fixtures.json'
	      , type: 'json'
	      , async: false
	      })
	      var request = r.request,
	        responseText = request.response !== undefined ? request.response : request.responseText
	      ok(eval('(' + responseText + ')').boosh == 'boosh', 'can make sync calls')
	      done()
	    })

	    test('no callbacks', function (complete) {
	      var pass = true
	      try {
	        ajax('/tests/fixtures/fixtures.js')
	      } catch (ex) {
	        pass = false
	      } finally {
	        ok(pass, 'successfully doesnt fail without callback')
	        complete()
	      }
	    })

	    test('complete is called', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures.js'
	        , complete: function () {
	            ok(true, 'called complete')
	            complete()
	          }
	      })
	    })

	    test('invalid JSON sets error on resp object', function (complete) {
	      ajax({
	          url: '/tests/fixtures/invalidJSON.json'
	        , type: 'json'
	        , success: function () {
	            ok(false, 'success callback fired')
	            complete()
	          }
	        , error: function (resp, msg) {
	            ok(
	                msg == 'Could not parse JSON in response'
	              , 'error callback fired'
	            )
	            complete()
	          }
	      })
	    })

	    test('multiple parallel named JSONP callbacks', 8, function () {
	      ajax({
	          url: '/tests/fixtures/fixtures_jsonp_multi.jsonp?callback=reqwest_0'
	        , type: 'jsonp'
	        , success: function (resp) {
	            ok(resp, 'received response from call #1')
	            ok(
	                resp && resp.a == 'a'
	              , 'evaluated response from call #1 as JSONP'
	            )
	          }
	      })
	      ajax({
	          url: '/tests/fixtures/fixtures_jsonp_multi_b.jsonp?callback=reqwest_0'
	        , type: 'jsonp'
	        , success: function (resp) {
	            ok(resp, 'received response from call #2')
	            ok(
	                resp && resp.b == 'b'
	              , 'evaluated response from call #2 as JSONP'
	            )
	          }
	      })
	      ajax({
	          url: '/tests/fixtures/fixtures_jsonp_multi_c.jsonp?callback=reqwest_0'
	        , type: 'jsonp'
	        , success: function (resp) {
	            ok(resp, 'received response from call #2')
	            ok(
	                resp && resp.c == 'c'
	              , 'evaluated response from call #3 as JSONP'
	            )
	          }
	      })
	      ajax({
	          url: '/tests/fixtures/fixtures_jsonp_multi.jsonp?callback=reqwest_0'
	        , type: 'jsonp'
	        , success: function (resp) {
	            ok(resp, 'received response from call #2')
	            ok(
	                resp && resp.a == 'a'
	              , 'evaluated response from call #4 as JSONP'
	            )
	          }
	      })
	    })

	    test('JSONP also supports success promises', function (complete) {
	      ajax({
	          url: '/tests/none.jsonp?echo'
	        , type: 'jsonp'
	        , success: function (resp) {
	            ok(resp, 'received response in constructor success callback')
	          }
	      })
	        .then(function (resp) {
	            ok(resp, 'received response in promise success callback')
	            return resp;
	        })
	        .then(function (resp) {
	            ok(resp, 'received response in second promise success callback')
	            complete()
	        })
	    })

	    test('JSONP also supports error promises', function (complete) {
	      ajax({
	          url: '/tests/timeout/'
	        , type: 'jsonp'
	        , error: function (err) {
	            ok(err, 'received error response in constructor error callback')
	          }
	      })
	        .fail(function (err) {
	            ok(err, 'received error response in promise error callback')
	        })
	        .fail(function (err) {
	            ok(err, 'received error response in second promise error callback')
	            complete()
	        })
	        .abort()
	    })

	  })

	  if (window.XMLHttpRequest
	    && ('withCredentials' in new window.XMLHttpRequest())) {

	    sink('Cross-origin Resource Sharing', function (test, ok) {
	      test('make request to another origin', 1, function () {
	        ajax({
	            url: 'http://' + window.location.hostname + ':5678/get-value'
	          , type: 'text'
	          , method: 'get'
	          , crossOrigin: true
	          , complete: function (resp) {
	              ok(resp.responseText === 'hello', 'request made successfully')
	            }
	        })
	      })

	      test('set cookie on other origin', 2, function () {
	        ajax({
	            url: 'http://' + window.location.hostname + ':5678/set-cookie'
	          , type: 'text'
	          , method: 'get'
	          , crossOrigin: true
	          , withCredentials: true
	          , before: function (http) {
	              ok(
	                  http.withCredentials === true
	                , 'has set withCredentials on connection object'
	              )
	            }
	          , complete: function (resp) {
	              ok(resp.status === 200, 'cookie set successfully')
	            }
	        })
	      })

	      test('get cookie from other origin', 1, function () {
	        ajax({
	              url: 'http://'
	                  + window.location.hostname
	                  + ':5678/get-cookie-value'
	            , type: 'text'
	            , method: 'get'
	            , crossOrigin: true
	            , withCredentials: true
	            , complete: function (resp) {
	                ok(
	                    resp.responseText == 'hello'
	                  , 'cookie value retrieved successfully'
	                )
	              }
	        })
	      })

	    })
	  }

	  sink('Connection Object', function (test, ok) {

	    test('use xhr factory provided in the options', function (complete) {
	      var reqwest
	      , xhr

	      if (typeof XMLHttpRequest !== 'undefined') {
	          xhr = new XMLHttpRequest()
	      } else if (typeof ActiveXObject !== 'undefined') {
	          xhr = new ActiveXObject('Microsoft.XMLHTTP')
	      } else {
	        ok(false, 'browser not supported')
	      }

	      reqwest = ajax({
	          url: '/tests/fixtures/fixtures.html',
	          xhr: function () {
	            return xhr
	          }
	      })

	      ok(reqwest.request === xhr, 'uses factory')
	      complete()
	    })

	    test('fallbacks to own xhr factory if falsy is returned', function (complete) {
	      var reqwest

	      FakeXHR.setup()
	      try {
	        reqwest = ajax({
	            url: '/tests/fixtures/fixtures.html',
	            xhr: function () {
	              return null
	            }
	        })

	        ok(reqwest.request instanceof FakeXHR, 'fallbacks correctly')
	        complete()
	      } finally {
	        FakeXHR.restore()
	      }
	    })

	    test('setRequestHeaders', function (complete) {
	      ajax({
	          url: '/tests/fixtures/fixtures.html'
	        , data: 'foo=bar&baz=thunk'
	        , method: 'post'
	        , headers: {
	            'Accept': 'application/x-foo'
	          }
	        , success: function () {
	            ok(true, 'can post headers')
	            complete()
	          }
	      })
	    })

	    test('can inspect http before send', function (complete) {
	      var connection = ajax({
	          url: '/tests/fixtures/fixtures.js'
	        , method: 'post'
	        , type: 'js'
	        , before: function (http) {
	            ok(http.readyState == 1, 'received http connection object')
	          }
	        , success: function () {
	            // Microsoft.XMLHTTP appears not to run this async in IE6&7, it
	            // processes the request and triggers success() before ajax() even
	            // returns. Perhaps a better solution would be to defer the calls
	            // within handleReadyState()
	            setTimeout(function () {
	              ok(
	                  connection.request.readyState == 4
	                , 'success callback has readyState of 4'
	              )
	              complete()
	            }, 0)
	        }
	      })
	    })

	    test('ajax() encodes array `data`', function (complete) {
	      FakeXHR.setup()
	      try {
	       ajax({
	            url: '/tests/fixtures/fixtures.html'
	          , method: 'post'
	          , data: [
	                { name: 'foo', value: 'bar' }
	              , { name: 'baz', value: 'thunk' }
	            ]
	        })
	        ok(FakeXHR.last.methodCallCount('send') == 1, 'send called')
	        ok(
	            FakeXHR.last.methodCallArgs('send', 0).length == 1
	          , 'send called with 1 arg'
	        )
	        ok(
	            FakeXHR.last.methodCallArgs('send', 0, 0) == 'foo=bar&baz=thunk'
	          , 'send called with encoded array'
	        )
	        complete()
	      } finally {
	        FakeXHR.restore()
	      }
	    })

	    test('ajax() encodes hash `data`', function (complete) {
	      FakeXHR.setup()
	      try {
	        ajax({
	            url: '/tests/fixtures/fixtures.html'
	          , method: 'post'
	          , data: { bar: 'foo', thunk: 'baz' }
	        })
	        ok(FakeXHR.last.methodCallCount('send') == 1, 'send called')
	        ok(
	            FakeXHR.last.methodCallArgs('send', 0).length == 1
	          , 'send called with 1 arg'
	        )
	        ok(
	            FakeXHR.last.methodCallArgs('send', 0, 0) == 'bar=foo&thunk=baz'
	          , 'send called with encoded array'
	        )
	        complete()
	      } finally {
	        FakeXHR.restore()
	      }
	    })

	    test('ajax() obeys `processData`', function (complete) {
	      FakeXHR.setup()
	      try {
	        var d = { bar: 'foo', thunk: 'baz' }
	        ajax({
	            url: '/tests/fixtures/fixtures.html'
	          , processData: false
	          , method: 'post'
	          , data: d
	        })
	        ok(FakeXHR.last.methodCallCount('send') == 1, 'send called')
	        ok(
	            FakeXHR.last.methodCallArgs('send', 0).length == 1
	          , 'send called with 1 arg'
	        )
	        ok(
	            FakeXHR.last.methodCallArgs('send', 0, 0) === d
	          , 'send called with exact `data` object'
	        )
	        complete()
	      } finally {
	        FakeXHR.restore()
	      }
	    })

	    function testXhrGetUrlAdjustment(url, data, expectedUrl, complete) {
	      FakeXHR.setup()
	      try {
	        ajax({ url: url, data: data })
	        ok(FakeXHR.last.methodCallCount('open') == 1, 'open called')
	        ok(
	            FakeXHR.last.methodCallArgs('open', 0).length == 3
	          , 'open called with 3 args'
	        )
	        ok(
	            FakeXHR.last.methodCallArgs('open', 0, 0) == 'GET'
	          , 'first arg of open() is "GET"'
	        )
	        ok(FakeXHR.last.methodCallArgs('open', 0, 1) == expectedUrl
	          , 'second arg of open() is URL with query string')
	        ok(
	            FakeXHR.last.methodCallArgs('open', 0, 2) === true
	          , 'third arg of open() is `true`'
	        )
	        ok(FakeXHR.last.methodCallCount('send') == 1, 'send called')
	        ok(
	            FakeXHR.last.methodCallArgs('send', 0).length == 1
	          , 'send called with 1 arg'
	        )
	        ok(
	            FakeXHR.last.methodCallArgs('send', 0, 0) === null
	          , 'send called with null'
	        )
	        complete()
	      } finally {
	        FakeXHR.restore()
	      }
	    }

	    test('ajax() appends GET URL with ?`data`', function (complete) {
	      testXhrGetUrlAdjustment(
	          '/tests/fixtures/fixtures.html'
	        , 'bar=foo&thunk=baz'
	        , '/tests/fixtures/fixtures.html?bar=foo&thunk=baz'
	        , complete
	      )
	    })

	    test('ajax() appends GET URL with ?`data` (serialized object)'
	          , function (complete) {

	      testXhrGetUrlAdjustment(
	          '/tests/fixtures/fixtures.html'
	        , { bar: 'foo', thunk: 'baz' }
	        , '/tests/fixtures/fixtures.html?bar=foo&thunk=baz'
	        , complete
	      )
	    })

	    test('ajax() appends GET URL with &`data` (serialized array)'
	          , function (complete) {

	      testXhrGetUrlAdjustment(
	          '/tests/fixtures/fixtures.html?x=y'
	        , [ { name: 'bar', value: 'foo'}, {name: 'thunk', value: 'baz' } ]
	        , '/tests/fixtures/fixtures.html?x=y&bar=foo&thunk=baz'
	        , complete
	      )
	    })
	  })

	  sink('Standard vs compat mode', function (test, ok) {
	    function methodMatch(resp, method) {
	       return resp && resp.method === method
	    }
	    function headerMatch(resp, key, expected) {
	      return resp && resp.headers && resp.headers[key] === expected
	    }
	    function queryMatch(resp, key, expected) {
	      return resp && resp.query && resp.query[key] === expected
	    }

	    test('standard mode default', function (complete) {
	      ajax({
	          url: '/tests/none.json?echo'
	        , success: function (resp) {
	            ok(methodMatch(resp, 'GET'), 'correct request method (GET)')
	            ok(
	                headerMatch(
	                    resp
	                  , 'content-type'
	                  , 'application/x-www-form-urlencoded'
	                )
	              , 'correct Content-Type request header'
	            )
	            ok(
	                headerMatch(resp, 'x-requested-with', 'XMLHttpRequest')
	              , 'correct X-Requested-With header'
	            )
	            ok(
	                headerMatch(
	                    resp
	                  , 'accept'
	                  , 'text/javascript, text/html, application/xml, text/xml, */*'
	                )
	              , 'correct Accept header'
	            )
	            complete()
	          }
	      })
	    })

	    test('standard mode custom content-type', function (complete) {
	      ajax({
	          url: '/tests/none.json?echo'
	        , contentType: 'yapplication/foobar'
	        , success: function (resp) {
	            ok(methodMatch(resp, 'GET'), 'correct request method (GET)')
	            ok(
	                headerMatch(resp, 'content-type', 'yapplication/foobar')
	              , 'correct Content-Type request header'
	            )
	            ok(
	                headerMatch(resp, 'x-requested-with', 'XMLHttpRequest')
	              , 'correct X-Requested-With header'
	            )
	            ok(
	                headerMatch(
	                    resp
	                  , 'accept'
	                  , 'text/javascript, text/html, application/xml, text/xml, */*'
	                )
	              , 'correct Accept header'
	            )
	            complete()
	          }
	      })
	    })

	    test('standard mode on no content-type', function (complete) {
	      ajax({
	        url: '/tests/204'
	          , success: function (resp) {
	            ok(true, 'Nothing blew up.')
	          }
	        })
	    })

	    test('compat mode "dataType=json" headers', function (complete) {
	      ajax.compat({
	          url: '/tests/none.json?echo'
	        , dataType: 'json' // should map to 'type'
	        , success: function (resp) {
	            ok(methodMatch(resp, 'GET'), 'correct request method (GET)')
	            ok(
	                headerMatch(
	                    resp
	                  , 'content-type'
	                  , 'application/x-www-form-urlencoded'
	                )
	              , 'correct Content-Type request header'
	            )
	            ok(
	                headerMatch(resp, 'x-requested-with', 'XMLHttpRequest')
	              , 'correct X-Requested-With header'
	            )
	            ok(
	                headerMatch(resp, 'accept', 'application/json, text/javascript')
	              , 'correct Accept header'
	            )
	            complete()
	          }
	      })
	    })

	    test('compat mode "dataType=json" with "type=post" headers'
	        , function (complete) {
	      ajax.compat({
	          url: '/tests/none.json?echo'
	        , type: 'post'
	        , dataType: 'json' // should map to 'type'
	        , success: function (resp) {
	            ok(methodMatch(resp, 'POST'), 'correct request method (POST)')
	            ok(
	                headerMatch(
	                    resp
	                  , 'content-type'
	                  , 'application/x-www-form-urlencoded'
	                )
	              , 'correct Content-Type request header'
	            )
	            ok(
	                headerMatch(resp, 'x-requested-with', 'XMLHttpRequest')
	              , 'correct X-Requested-With header'
	            )
	            ok(
	                headerMatch(resp, 'accept', 'application/json, text/javascript')
	              , 'correct Accept header'
	            )
	            complete()
	          }
	      })
	    })

	    test('compat mode "dataType=json" headers (with additional headers)'
	        , function (complete) {

	      ajax.compat({
	          url: '/tests/none.json?echo'
	        , dataType: 'json' // should map to 'type'
	          // verify that these are left intact and nothing screwy
	          // happens with headers
	        , headers: { one: 1, two: 2 }
	        , success: function (resp) {
	            ok(
	                headerMatch(
	                    resp
	                  , 'content-type'
	                  , 'application/x-www-form-urlencoded'
	                )
	              , 'correct Content-Type request header'
	            )
	            ok(
	                headerMatch(resp, 'x-requested-with', 'XMLHttpRequest')
	              , 'correct X-Requested-With header'
	            )
	            ok(
	                headerMatch(resp, 'accept', 'application/json, text/javascript')
	              , 'correct Accept header'
	            )
	            ok(
	                headerMatch(resp, 'one', '1') && headerMatch(resp, 'two', '2')
	              , 'left additional headers intact'
	            )
	            complete()
	          }
	      })
	    })

	    test('compat mode "dataType=jsonp" query string', function (complete) {
	      ajax.compat({
	          url: '/tests/none.jsonp?echo'
	        , dataType: 'jsonp'
	        , jsonp: 'testCallback' // should map to jsonpCallback
	        , jsonpCallback: 'foobar' // should map to jsonpCallbackName
	        , success: function (resp) {
	            ok(
	                queryMatch(resp, 'echo', '')
	              , 'correct Content-Type request header'
	            )
	            ok(
	                queryMatch(resp, 'testCallback', 'foobar')
	              , 'correct X-Requested-With header'
	            )
	            complete()
	          }
	      })
	    })
	  })

	  /***************** SERIALIZER TESTS ***********************/

	  // define some helpers for the serializer tests that are used often and
	  // shared with the ender integration tests

	  function createSerializeHelper(ok) {
	    var forms = document.forms
	      , foo = forms[0].getElementsByTagName('input')[1]
	      , bar = forms[0].getElementsByTagName('input')[2]
	      , choices = forms[0].getElementsByTagName('select')[0]
	      , BIND_ARGS = 'bind'
	      , PASS_ARGS = 'pass'

	    function reset() {
	      forms[1].reset()
	    }

	    function formElements(formIndex, tagName, elementIndex) {
	      return forms[formIndex].getElementsByTagName(tagName)[elementIndex]
	    }

	    function isArray(a) {
	      return Object.prototype.toString.call(a) == '[object Array]'
	    }

	    function sameValue(value, expected) {
	      if (expected == null) {
	        return value === null
	      } else if (isArray(expected)) {
	        if (value.length !== expected.length) return false
	        for (var i = 0; i < expected.length; i++) {
	          if (value[i] != expected[i]) return false
	        }
	        return true
	      } else return value == expected
	    }

	    function testInput(input, name, value, str) {
	      var sa = ajax.serialize(input, { type: 'array' })
	        , sh = ajax.serialize(input, { type: 'map' })
	        , av, i

	      if (value != null) {
	        av = isArray(value) ? value : [ value ]

	        ok(
	            sa.length == av.length
	          ,   'serialize(' + str + ', {type:\'array\'}) returns array '
	            + '[{name,value}]'
	        )

	        for (i = 0; i < av.length; i++) {
	          ok(
	              name == sa[i].name
	            , 'serialize(' + str + ', {type:\'array\'})[' + i + '].name'
	          )
	          ok(
	              av[i] == sa[i].value
	            , 'serialize(' + str + ', {type:\'array\'})[' + i + '].value'
	          )
	        }

	        ok(sameValue(sh[name], value), 'serialize(' + str + ', {type:\'map\'})')
	      } else {
	        // the cases where an element shouldn't show up at all, checkbox not
	        // checked for example
	        ok(sa.length === 0, 'serialize(' + str + ', {type:\'array\'}) is []')
	        ok(
	            v.keys(sh).length === 0
	          , 'serialize(' + str + ', {type:\'map\'}) is {}'
	        )
	      }
	    }

	    function testFormSerialize(method, type) {
	      var expected =
	            'foo=bar&bar=baz&wha=1&wha=3&who=tawoo&%24escapable+name'
	          + '%24=escapeme&choices=two&opinions=world+peace+is+not+real'

	      ok(method, 'serialize() bound to context')
	      ok(
	          (method ? method(forms[0]) : null) == expected
	        , 'serialized form (' + type + ')'
	      )
	    }

	    function executeMultiArgumentMethod(method, argType, options) {
	      var els = [ foo, bar, choices ]
	        , ths = argType === BIND_ARGS ? ender(els) : null
	        , args = argType === PASS_ARGS ? els : []

	      if (!!options) args.push(options)

	      return method.apply(ths, args)
	    }

	    function testMultiArgumentSerialize(method, type, argType) {
	      ok(method, 'serialize() bound in context')
	      var result = method ? executeMultiArgumentMethod(method, argType) : null
	      ok(
	          result == 'foo=bar&bar=baz&choices=two'
	        , 'serialized all 3 arguments together'
	      )
	    }

	    function verifyFormSerializeArray(result, type) {
	      var expected = [
	              { name: 'foo', value: 'bar' }
	            , { name: 'bar', value: 'baz' }
	            , { name: 'wha', value: 1 }
	            , { name: 'wha', value: 3 }
	            , { name: 'who', value: 'tawoo' }
	            , { name: '$escapable name$', value: 'escapeme' }
	            , { name: 'choices', value: 'two' }
	            , { name: 'opinions', value: 'world peace is not real' }
	          ]
	        , i

	    for (i = 0; i < expected.length; i++) {
	        ok(v.some(result, function (v) {
	          return v.name == expected[i].name && v.value == expected[i].value
	        }), 'serialized ' + expected[i].name + ' (' + type + ')')
	      }
	    }

	    function testFormSerializeArray(method, type) {
	      ok(method, 'serialize(..., {type:\'array\'}) bound to context')

	      var result = method ? method(forms[0], { type: 'array' }) : []
	      if (!result) result = []

	      verifyFormSerializeArray(result, type)
	    }

	    function testMultiArgumentSerializeArray(method, type, argType) {
	        ok(method, 'serialize(..., {type:\'array\'}) bound to context')
	        var result = method
	          ? executeMultiArgumentMethod(method, argType, { type: 'array' })
	          : []

	        if (!result) result = []

	        ok(result.length == 3, 'serialized as array of 3')
	        ok(
	            result.length == 3
	            && result[0].name == 'foo'
	            && result[0].value == 'bar'
	          , 'serialized first element (' + type + ')'
	        )
	        ok(
	            result.length == 3
	            && result[1].name == 'bar'
	            && result[1].value == 'baz'
	          , 'serialized second element (' + type + ')'
	        )
	        ok(
	            result.length == 3
	            && result[2].name == 'choices'
	            && result[2].value == 'two'
	          , 'serialized third element (' + type + ')'
	        )
	      }

	    function testFormSerializeHash(method, type) {
	      var expected = {
	              foo: 'bar'
	            , bar: 'baz'
	            , wha: [ '1', '3' ]
	            , who: 'tawoo'
	            , '$escapable name$': 'escapeme'
	            , choices: 'two'
	            , opinions: 'world peace is not real'
	          }
	        , result

	      ok(method, 'serialize({type:\'map\'}) bound to context')

	      result = method ? method(forms[0], { type: 'map' }) : {}
	      if (!result) result = {}

	      ok(
	          v.keys(expected).length === v.keys(result).length
	        , 'same number of keys (' + type + ')'
	      )

	      v.each(v.keys(expected), function (k) {
	        ok(
	            sameValue(expected[k], result[k])
	          , 'same value for ' + k + ' (' + type + ')'
	        )
	      })
	    }

	    function testMultiArgumentSerializeHash(method, type, argType) {
	      ok(method, 'serialize({type:\'map\'}) bound to context')
	      var result = method
	        ? executeMultiArgumentMethod(method, argType, { type: 'map' })
	        : {}
	      if (!result) result = {}
	      ok(result.foo == 'bar', 'serialized first element (' + type + ')')
	      ok(result.bar == 'baz', 'serialized second element (' + type + ')')
	      ok(result.choices == 'two', 'serialized third element (' + type + ')')
	    }

	    return {
	      reset: reset
	      , formElements: formElements
	      , testInput: testInput
	      , testFormSerialize: testFormSerialize
	      , testMultiArgumentSerialize: testMultiArgumentSerialize
	      , testFormSerializeArray: testFormSerializeArray
	      , verifyFormSerializeArray: verifyFormSerializeArray
	      , testMultiArgumentSerializeArray: testMultiArgumentSerializeArray
	      , testFormSerializeHash: testFormSerializeHash
	      , testMultiArgumentSerializeHash: testMultiArgumentSerializeHash
	    }
	  }

	  sink('Serializing', function (test, ok) {

	    /*
	     * Serialize forms according to spec.
	     *  * reqwest.serialize(ele[, ele...]) returns a query string style
	     *    serialization
	     *  * reqwest.serialize(ele[, ele...], {type:'array'}) returns a
	     *    [ { name: 'name', value: 'value'}, ... ] style serialization,
	     *    compatible with jQuery.serializeArray()
	     *  * reqwest.serialize(ele[, ele...], {type:\'map\'}) returns a
	     *    { 'name': 'value', ... } style serialization, compatible with
	     *    Prototype Form.serializeElements({hash:true})
	     * Some tests based on spec notes here:
	     *    http://malsup.com/jquery/form/comp/test.html
	     */

	    var sHelper = createSerializeHelper(ok)
	    sHelper.reset()

	    test('correctly serialize textarea', function (complete) {
	      var textarea = sHelper.formElements(1, 'textarea', 0)
	        , sa

	      // the texarea has 2 different newline styles, should come out as
	      // normalized CRLF as per forms spec
	      ok(
	          'T3=%3F%0D%0AA+B%0D%0AZ' == ajax.serialize(textarea)
	        , 'serialize(textarea)'
	      )
	      sa = ajax.serialize(textarea, { type: 'array' })
	      ok(sa.length == 1, 'serialize(textarea, {type:\'array\'}) returns array')
	      sa = sa[0]
	      ok('T3' == sa.name, 'serialize(textarea, {type:\'array\'}).name')
	      ok(
	          '?\r\nA B\r\nZ' == sa.value
	        , 'serialize(textarea, {type:\'array\'}).value'
	      )
	      ok(
	          '?\r\nA B\r\nZ' == ajax.serialize(textarea, { type: 'map' }).T3
	        , 'serialize(textarea, {type:\'map\'})'
	      )
	      complete()
	    })

	    test('correctly serialize input[type=hidden]', function (complete) {
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 0)
	        , 'H1'
	        , 'x'
	        , 'hidden'
	      )
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 1)
	        , 'H2'
	        , ''
	        , 'hidden[no value]'
	      )
	      complete()
	    })

	    test('correctly serialize input[type=password]', function (complete) {
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 2)
	        , 'PWD1'
	        , 'xyz'
	        , 'password'
	      )
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 3)
	        , 'PWD2'
	        , ''
	        , 'password[no value]'
	      )
	      complete()
	    })

	    test('correctly serialize input[type=text]', function (complete) {
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 4)
	        , 'T1'
	        , ''
	        , 'text[no value]'
	      )
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 5)
	        , 'T2'
	        , 'YES'
	        , 'text[readonly]'
	      )
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 10)
	        , 'My Name'
	        , 'me'
	        , 'text[space name]'
	      )
	      complete()
	    })

	    test('correctly serialize input[type=checkbox]', function (complete) {
	      var cb1 = sHelper.formElements(1, 'input', 6)
	        , cb2 = sHelper.formElements(1, 'input', 7)
	      sHelper.testInput(cb1, 'C1', null, 'checkbox[not checked]')
	      cb1.checked = true
	      sHelper.testInput(cb1, 'C1', '1', 'checkbox[checked]')
	      // special case here, checkbox with no value='' should give you 'on'
	      // for cb.value
	      sHelper.testInput(cb2, 'C2', null, 'checkbox[no value, not checked]')
	      cb2.checked = true
	      sHelper.testInput(cb2, 'C2', 'on', 'checkbox[no value, checked]')
	      complete()
	    })

	    test('correctly serialize input[type=radio]', function (complete) {
	      var r1 = sHelper.formElements(1, 'input', 8)
	        , r2 = sHelper.formElements(1, 'input', 9)
	      sHelper.testInput(r1, 'R1', null, 'radio[not checked]')
	      r1.checked = true
	      sHelper.testInput(r1, 'R1', '1', 'radio[not checked]')
	      sHelper.testInput(r2, 'R1', null, 'radio[no value, not checked]')
	      r2.checked = true
	      sHelper.testInput(r2, 'R1', '', 'radio[no value, checked]')
	      complete()
	    })

	    test('correctly serialize input[type=reset]', function (complete) {
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 11)
	        , 'rst'
	        , null
	        , 'reset'
	      )
	      complete()
	    })

	    test('correctly serialize input[type=file]', function (complete) {
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 12)
	        , 'file'
	        , null
	        , 'file'
	      )
	      complete()
	    })

	    test('correctly serialize input[type=submit]', function (complete) {
	      // we're only supposed to serialize a submit button if it was clicked to
	      // perform this serialization:
	      // http://www.w3.org/TR/html401/interact/forms.html#h-17.13.2
	      // but we'll pretend to be oblivious to this part of the spec...
	      sHelper.testInput(
	          sHelper.formElements(1, 'input', 13)
	        , 'sub'
	        , 'NO'
	        , 'submit'
	      )
	      complete()
	    })

	    test('correctly serialize select with no options', function (complete) {
	      var select = sHelper.formElements(1, 'select', 0)
	      sHelper.testInput(select, 'S1', null, 'select, no options')
	      complete()
	    })

	    test('correctly serialize select with values', function (complete) {
	      var select = sHelper.formElements(1, 'select', 1)
	      sHelper.testInput(select, 'S2', 'abc', 'select option 1 (default)')
	      select.selectedIndex = 1
	      sHelper.testInput(select, 'S2', 'def', 'select option 2')
	      select.selectedIndex = 6
	      sHelper.testInput(select, 'S2', 'disco stu', 'select option 7')
	      // a special case where we have <option value=''>X</option>, should
	      // return '' rather than X which will happen if you just do a simple
	      // `value=(option.value||option.text)`
	      select.selectedIndex = 9
	      sHelper.testInput(
	          select
	        , 'S2'
	        , ''
	        , 'select option 9, value="" should yield ""'
	      )
	      select.selectedIndex = -1
	      sHelper.testInput(select, 'S2', null, 'select, unselected')
	      complete()
	    })

	    test('correctly serialize select without explicit values'
	        , function (complete) {

	      var select = sHelper.formElements(1, 'select', 2)
	      sHelper.testInput(select, 'S3', 'ABC', 'select option 1 (default)')
	      select.selectedIndex = 1
	      sHelper.testInput(select, 'S3', 'DEF', 'select option 2')
	      select.selectedIndex = 6
	      sHelper.testInput(select, 'S3', 'DISCO STU!', 'select option 7')
	      select.selectedIndex = -1
	      sHelper.testInput(select, 'S3', null, 'select, unselected')
	      complete()
	    })

	    test('correctly serialize select multiple', function (complete) {
	      var select = sHelper.formElements(1, 'select', 3)
	      sHelper.testInput(select, 'S4', null, 'select, unselected (default)')
	      select.options[1].selected = true
	      sHelper.testInput(select, 'S4', '2', 'select option 2')
	      select.options[3].selected = true
	      sHelper.testInput(select, 'S4', [ '2', '4' ], 'select options 2 & 4')
	      select.options[8].selected = true
	      sHelper.testInput(
	          select
	        , 'S4'
	        , [ '2', '4', 'Disco Stu!' ]
	        , 'select option 2 & 4 & 9'
	      )
	      select.options[3].selected = false
	      sHelper.testInput(
	          select
	        , 'S4'
	        , [ '2', 'Disco Stu!' ]
	        , 'select option 2 & 9'
	      )
	      select.options[1].selected = false
	      select.options[8].selected = false
	      sHelper.testInput(select, 'S4', null, 'select, all unselected')
	      complete()
	     })

	    test('correctly serialize options', function (complete) {
	      var option = sHelper.formElements(1, 'select', 1).options[6]
	      sHelper.testInput(
	          option
	        , '-'
	        , null
	        , 'just option (with value), shouldn\'t serialize'
	      )

	      option = sHelper.formElements(1, 'select', 2).options[6]
	      sHelper.testInput(
	          option
	        , '-'
	        , null
	        , 'option (without value), shouldn\'t serialize'
	      )

	      complete()
	    })

	    test('correctly serialize disabled', function (complete) {
	      var input = sHelper.formElements(1, 'input', 14)
	        , select

	      sHelper.testInput(input, 'D1', null, 'disabled text input')
	      input = sHelper.formElements(1, 'input', 15)
	      sHelper.testInput(input, 'D2', null, 'disabled checkbox')
	      input = sHelper.formElements(1, 'input', 16)
	      sHelper.testInput(input, 'D3', null, 'disabled radio')

	      select = sHelper.formElements(1, 'select', 4)
	      sHelper.testInput(select, 'D4', null, 'disabled select')
	      select = sHelper.formElements(1, 'select', 3)
	      sHelper.testInput(select, 'D5', null, 'disabled select option')
	      select = sHelper.formElements(1, 'select', 6)
	      sHelper.testInput(select, 'D6', null, 'disabled multi select')
	      select = sHelper.formElements(1, 'select', 7)
	      sHelper.testInput(select, 'D7', null, 'disabled multi select option')
	      complete()
	    })

	    test('serialize(form)', function (complete) {
	      sHelper.testFormSerialize(ajax.serialize, 'direct')
	      complete()
	    })

	    test('serialize(form, {type:\'array\'})', function (complete) {
	      sHelper.testFormSerializeArray(ajax.serialize, 'direct')
	      complete()
	    })

	    test('serialize(form, {type:\'map\'})', function (complete) {
	      sHelper.testFormSerializeHash(ajax.serialize, 'direct')
	      complete()
	    })

	    // mainly for Ender integration, so you can do this:
	    // $('input[name=T2],input[name=who],input[name=wha]').serialize()
	    test('serialize(element, element, element...)', function (complete) {
	      sHelper.testMultiArgumentSerialize(ajax.serialize, 'direct', PASS_ARGS)
	      complete()
	    })

	    // mainly for Ender integration, so you can do this:
	    // $('input[name=T2],input[name=who],input[name=wha]')
	    //    .serialize({type:'array'})
	    test('serialize(element, element, element..., {type:\'array\'})'
	        , function (complete) {
	      sHelper.testMultiArgumentSerializeArray(
	          ajax.serialize
	        , 'direct'
	        , PASS_ARGS
	      )
	      complete()
	    })

	    // mainly for Ender integration, so you can do this:
	    // $('input[name=T2],input[name=who],input[name=wha]')
	    //     .serialize({type:'map'})
	    test('serialize(element, element, element...)', function (complete) {
	      sHelper.testMultiArgumentSerializeHash(
	          ajax.serialize
	        , 'direct'
	        , PASS_ARGS
	      )
	      complete()
	    })

	    test('toQueryString([{ name: x, value: y }, ... ]) name/value array'
	        , function (complete) {

	      var arr = [
	          { name: 'foo', value: 'bar' }
	        , { name: 'baz', value: '' }
	        , { name: 'x', value: -20 }
	        , { name: 'x', value: 20 }
	      ]

	      ok(ajax.toQueryString(arr) == 'foo=bar&baz=&x=-20&x=20', 'simple')

	      arr = [
	          { name: 'dotted.name.intact', value: '$@%' }
	        , { name: '$ $', value: 20 }
	        , { name: 'leave britney alone', value: 'waa haa haa' }
	      ]

	      ok(
	          ajax.toQueryString(arr) ==
	              'dotted.name.intact=%24%40%25&%24+%24=20'
	            + '&leave+britney+alone=waa+haa+haa'
	        , 'escaping required'
	      )

	      complete()
	    })

	    test('toQueryString({name: value,...} complex object', function (complete) {
	      var obj = { 'foo': 'bar', 'baz': '', 'x': -20 }

	      ok(ajax.toQueryString(obj) == 'foo=bar&baz=&x=-20', 'simple')

	      obj = {
	          'dotted.name.intact': '$@%'
	        , '$ $': 20
	        , 'leave britney alone': 'waa haa haa'
	      }
	      ok(
	          ajax.toQueryString(obj) ==
	              'dotted.name.intact=%24%40%25&%24+%24=20'
	            + '&leave+britney+alone=waa+haa+haa'
	        , 'escaping required'
	      )

	      complete()
	    })

	    test('toQueryString({name: [ value1, value2 ...],...} object with arrays', function (complete) {
	      var obj = { 'foo': 'bar', 'baz': [ '', '', 'boo!' ], 'x': [ -20, 2.2, 20 ] }
	      ok(ajax.toQueryString(obj, true) == "foo=bar&baz=&baz=&baz=boo!&x=-20&x=2.2&x=20", "object with arrays")
	      ok(ajax.toQueryString(obj) == "foo=bar&baz%5B%5D=&baz%5B%5D=&baz%5B%5D=boo!&x%5B%5D=-20&x%5B%5D=2.2&x%5B%5D=20")
	      complete()
	    })

	    test('toQueryString({name: { nestedName: value },...} object with objects', function(complete) {
	      var obj = { 'foo': { 'bar': 'baz' }, 'x': [ { 'bar': 'baz' }, { 'boo': 'hiss' } ] }
	      ok(ajax.toQueryString(obj) == "foo%5Bbar%5D=baz&x%5B0%5D%5Bbar%5D=baz&x%5B1%5D%5Bboo%5D=hiss", "object with objects")
	      complete()
	    })

	  })

	  sink('Ender Integration', function (test, ok) {
	    var sHelper = createSerializeHelper(ok)
	    sHelper.reset()

	    test('$.ajax alias for reqwest, not bound to boosh', 1, function () {
	      ok(ender.ajax === ajax, '$.ajax is reqwest')
	    })

	    // sHelper.test that you can do $.serialize(form)
	    test('$.serialize(form)', function (complete) {
	      sHelper.testFormSerialize(ender.serialize, 'ender')
	      complete()
	    })

	    // sHelper.test that you can do $.serialize(form)
	    test('$.serialize(form, {type:\'array\'})', function (complete) {
	      sHelper.testFormSerializeArray(ender.serialize, 'ender')
	      complete()
	    })

	    // sHelper.test that you can do $.serialize(form)
	    test('$.serialize(form, {type:\'map\'})', function (complete) {
	      sHelper.testFormSerializeHash(ender.serialize, 'ender')
	      complete()
	    })

	    // sHelper.test that you can do $.serializeObject(form)
	    test('$.serializeArray(...) alias for serialize(..., {type:\'map\'}'
	        , function (complete) {
	      sHelper.verifyFormSerializeArray(
	          ender.serializeArray(document.forms[0])
	        , 'ender'
	      )
	      complete()
	    })

	    test('$.serialize(element, element, element...)', function (complete) {
	      sHelper.testMultiArgumentSerialize(ender.serialize, 'ender', PASS_ARGS)
	      complete()
	    })

	    test('$.serialize(element, element, element..., {type:\'array\'})'
	        , function (complete) {
	      sHelper.testMultiArgumentSerializeArray(
	          ender.serialize
	        , 'ender'
	        , PASS_ARGS
	      )
	      complete()
	    })

	    test('$.serialize(element, element, element..., {type:\'map\'})'
	        , function (complete) {
	      sHelper.testMultiArgumentSerializeHash(
	          ender.serialize
	        , 'ender'
	        , PASS_ARGS
	      )
	      complete()
	    })

	    test('$(element, element, element...).serialize()', function (complete) {
	      sHelper.testMultiArgumentSerialize(ender.fn.serialize, 'ender', BIND_ARGS)
	      complete()
	    })

	    test('$(element, element, element...).serialize({type:\'array\'})'
	        , function (complete) {
	      sHelper.testMultiArgumentSerializeArray(
	          ender.fn.serialize
	        , 'ender'
	        , BIND_ARGS
	      )
	      complete()
	    })

	    test('$(element, element, element...).serialize({type:\'map\'})'
	        , function (complete) {
	      sHelper.testMultiArgumentSerializeHash(
	          ender.fn.serialize
	        , 'ender'
	        , BIND_ARGS
	      )
	      complete()
	    })

	    test('$.toQueryString alias for reqwest.toQueryString, not bound to boosh'
	          , function (complete) {
	      ok(
	          ender.toQueryString === ajax.toQueryString
	        , '$.toQueryString is reqwest.toQueryString'
	      )
	      complete()
	    })
	  })


	  /**
	   * Promise tests for `then` `fail` and `always`
	   */
	  sink('Promises', function (test, ok) {

	    test('always callback is called', function (complete) {
	      ajax({
	        url: '/tests/fixtures/fixtures.js'
	      })
	        .always(function () {
	          ok(true, 'called complete')
	          complete()
	        })
	    })

	    test('success and error handlers are called', 3, function () {
	      ajax({
	          url: '/tests/fixtures/invalidJSON.json'
	        , type: 'json'
	      })
	        .then(
	            function () {
	              ok(false, 'success callback fired')
	            }
	          , function (resp, msg) {
	              ok(
	                  msg == 'Could not parse JSON in response'
	                , 'error callback fired'
	              )
	            }
	        )

	      ajax({
	          url: '/tests/fixtures/invalidJSON.json'
	        , type: 'json'
	      })
	        .fail(function (resp, msg) {
	          ok(msg == 'Could not parse JSON in response', 'fail callback fired')
	        })

	      ajax({
	          url: '/tests/fixtures/fixtures.json'
	        , type: 'json'
	      })
	        .then(
	            function () {
	              ok(true, 'success callback fired')
	            }
	          , function () {
	              ok(false, 'error callback fired')
	            }
	        )
	    })

	    test('then is chainable', 2, function () {
	      ajax({
	          url: '/tests/fixtures/fixtures.json'
	        , type: 'json'
	      })
	        .then(
	            function (resp) {
	              ok(true, 'first success callback fired')
	              return 'new value';
	            }
	        )
	        .then(
	            function (resp) {
	              ok(resp === 'new value', 'second success callback fired')
	            }
	        )
	    })

	    test('success does not chain with then', 2, function () {
	      ajax({
	          url: '/tests/fixtures/fixtures.json'
	        , type: 'json'
	        , success: function() {
	          ok(true, 'success callback fired')
	          return 'some independent value';
	        }
	      })
	        .then(
	            function (resp) {
	              ok(
	                resp && resp !== 'some independent value'
	                , 'then callback fired'
	              )
	            }
	        )
	    })

	    test('then & always handlers can be added after a response is received'
	          , 2
	          , function () {

	      var a = ajax({
	          url: '/tests/fixtures/fixtures.json'
	        , type: 'json'
	      })
	        .always(function () {
	          setTimeout(function () {
	            a.then(
	                  function () {
	                    ok(true, 'success callback called')
	                  }
	                , function () {
	                    ok(false, 'error callback called')
	                  }
	              ).always(function () {
	                ok(true, 'complete callback called')
	              })
	          }, 1)
	        })
	    })

	    test('then is chainable after a response is received'
	          , 2
	          , function () {

	      var a = ajax({
	          url: '/tests/fixtures/fixtures.json'
	        , type: 'json'
	      })
	        .always(function () {
	          setTimeout(function () {
	            a.then(function () {
	              ok(true, 'first success callback called')
	              return 'new value';
	            }).then(function (resp) {
	              ok(resp === 'new value', 'second success callback called')
	            })
	          }, 1)
	        })
	    })

	    test('failure handlers can be added after a response is received'
	        , function (complete) {

	      var a = ajax({
	          url: '/tests/fixtures/invalidJSON.json'
	        , type: 'json'
	      })
	        .always(function () {
	          setTimeout(function () {
	            a
	              .fail(function () {
	                ok(true, 'fail callback called')
	                complete()
	              })
	          }, 1)
	        })
	    })

	    test('.then success and fail are optional parameters', 1, function () {
	      try {
	        ajax({
	            url: '/tests/fixtures/invalidJSON.json'
	          , type: 'json'
	        })
	          .then()
	      } catch (ex) {
	        ok(false, '.then() parameters should be optional')
	      } finally {
	        ok(true, 'passed .then() optional parameters')
	      }
	    })

	  })



	  sink('Timeout', function (test, ok) {
	    test('xmlHttpRequest', function (complete) {
	      var ts = +new Date()
	      ajax({
	          url: '/tests/timeout'
	        , type: 'json'
	        , timeout: 250
	        , error: function (err, msg) {
	            ok(err, 'received error response')
	            try {
	              ok(err && err.status === 0, 'correctly caught timeout')
	              ok(msg && msg === 'Request is aborted: timeout', 'timeout message received')
	            } catch (e) {
	              ok(true, 'IE is a troll')
	            }
	            var tt = Math.abs(+new Date() - ts)
	            ok(
	                tt > 200 && tt < 300
	              , 'timeout close enough to 250 (' + tt + ')'
	            )
	            complete()
	          }
	      })
	    })

	    test('jsonpRequest', function (complete) {
	      var ts = +new Date()
	      ajax({
	          url: '/tests/timeout'
	        , type: 'jsonp'
	        , timeout: 250
	        , error: function (err) {
	            ok(err, 'received error response')
	            var tt = Math.abs(+new Date() - ts)
	            ok(
	                tt > 200 && tt < 300
	              , 'timeout close enough to 250 (' + tt + ')'
	            )
	            complete()
	          }
	      })
	    })
	  })

	  start()

	}(reqwest))


/***/ }
/******/ ]);