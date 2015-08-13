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
	var api = __webpack_require__(22)

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

	__webpack_require__(4)
	__webpack_require__(6)
	__webpack_require__(7)
	__webpack_require__(8)
	__webpack_require__(9)
	__webpack_require__(10)
	__webpack_require__(11)
	__webpack_require__(3)
	__webpack_require__(12)
	__webpack_require__(15)
	__webpack_require__(16)
	__webpack_require__(17)
	__webpack_require__(18)
	__webpack_require__(19)
	__webpack_require__(20)


<<<<<<< HEAD
	riot.tag('app', '<modal presentation="{ currentPresentation }" categories="{ summit.categories }" api="{ this.opts }"></modal> <navbar></navbar> <div class="container-fluid"> <rg-toast toasts="{ toasts }" position="bottomright"></rg-toast>  <div show="{ DisplayMode === \'tutorial\' }"> <tutorial></tutorial> </div>  <div show="{ DisplayMode === \'directory\' }"> <chairdirectory chairs="{ summit.chair_list }"></chairdirectory> </div>  <div show="{ DisplayMode === \'selections\' }"> <selection-manager categories="{ summit.categories }" api="{ this.opts }"></selection-manager> </div>   <div show="{ DisplayMode === \'browse\' }" class="row"> <div class="{ col-lg-4: details } { col-lg-12: !details }"> <div class="well well-sm"> <h4>{ summit.title } Presentation Submissions</h4> <hr> <div class="input-group"> <span class="input-group-addon" id="sizing-addon2"> <i if="{ !searchmode }" class="fa fa-search"></i> <i if="{ searchmode }" onclick="{ clearSearch }" class="fa fa-times"></i> </span> <form onsubmit="{search}"> <input type="text" id="app-search" class="form-control" placeholder="search..." aria-describedby="sizing-addon2"> </form> </div> </div> <categorymenu categories="{ summit.categories }" active="{ activeCategory }" if="{ !searchmode }"></categorymenu> <div if="{ searchmode && quantity }">Showing { quantity } results</div> <div if="{ quantity }" class="list-group" id="presentation-list"> <div class="row" show="{ !details }"> <div class="col-lg-9 col-md-9 hidden-sm hidden-xs"> &nbsp; </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" > Ave </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs"> Count </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs"> Total </div> </div>  <div class="presentation-list" id="presentation-list"> <presentationitem each="{ presentation, i in presentations }" activekey="{ activekey }" key="{ i }" data="{ presentation }" details="{details}"></presentationitem> </div> </div> <div if="{ !quantity && searchmode }">No results were found</div> </div> <div class="col-lg-8" show="{ details }"> <div class="panel panel-default" name="presentation-details"> <div class="panel-heading"> <h3 class="panel-title">Presentation Details <a href="#" onclick="{ closeDetails }"><i class="fa fa-times pull-right"></i></a></h3> </div> <div class="panel-body">  <div class="row"> <div class="col-lg-6"> <strong>Category:</strong> { currentPresentation.category_name } <br><a data-toggle="modal" data-target="#myModal" href="#"><i class="fa fa-random"></i>&nbsp;Suggest Category Change</a> </div> <div class="col-lg-6"> <div class="btn-group pull-right" role="group" >  <button if="{ currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ unselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> My List</button> <button if="{ !currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ selectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> My List</button>  <button if="{ currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupUnselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> Team List</button> <button if="{ !currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupSelectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> Team List</button> </div> </div> </div> <hr> <h2>{ currentPresentation.title }</h2> <h4>{ currentPresentation.level }</h4> <hr> <span class="label label-primary">Vote Count <span class="badge">{ currentPresentation.vote_count }</span></span> <span class="label label-primary">Vote Ave <span class="badge">{ currentPresentation.vote_average }</span></span> <span class="label label-primary">Vote Total <span class="badge">{ currentPresentation.total_points }</span></span> <span class="label label-info" show="{currentPresentation.comments.length}">Chair Comments: { currentPresentation.comments.length }</span> <hr> <raw content="{ currentPresentation.description }"></raw> <div each="{ currentPresentation.speakers }"> <hr> <h4>{ first_name }&nbsp;{ last_name }</h5> <p>{ title }</p> <raw content="{ bio }"></raw> </div> <div> </div> <div if="{ currentPresentation.comments[0] }"> <hr> <h4>Comments</h4> <comment each="{ currentPresentation.comments }"></comment> <hr> </div> <addcommentform api="{ this.opts }" presentation="{ currentPresentation }"></addcommentform> </div> </div> </div> </div>  </div>', function(opts) {
=======
	riot.tag('app', '<modal presentation="{ currentPresentation }" categories="{ summit.categories }" api="{ this.opts }"></modal> <navbar admin="{ summit.track_chair.is_admin }"></navbar> <div class="container-fluid"> <rg-toast toasts="{ toasts }" position="bottomright"></rg-toast>  <div show="{ DisplayMode === \'tutorial\' }"> <tutorial></tutorial> </div>  <div show="{ DisplayMode === \'directory\' }"> <chairdirectory chairs="{ summit.chair_list }"></chairdirectory> </div>  <div show="{ DisplayMode === \'selections\' }"> <selection-manager categories="{ summit.categories }" api="{ this.opts }"></selection-manager> </div>   <div show="{ DisplayMode === \'requests\' }"> <change-requests api="{ this.opts }"></change-requests> </div>   <div show="{ DisplayMode === \'browse\' }" class="row"> <div class="{ col-lg-4: details } { col-lg-12: !details }"> <div class="well well-sm"> <h4>{ summit.title } Presentation Submissions</h4> <hr> <div class="input-group"> <span class="input-group-addon" id="sizing-addon2"> <i if="{ !searchmode }" class="fa fa-search"></i> <i if="{ searchmode }" onclick="{ clearSearch }" class="fa fa-times"></i> </span> <form onsubmit="{search}"> <input type="text" id="app-search" class="form-control" placeholder="search..." aria-describedby="sizing-addon2"> </form> </div> </div> <categorymenu categories="{ summit.categories }" active="{ activeCategory }" if="{ !searchmode }"></categorymenu> <div if="{ searchmode && quantity }">Showing { quantity } results</div> <div if="{ quantity }" class="list-group" id="presentation-list"> <div class="row" show="{ !details }"> <div class="col-lg-9 col-md-9 hidden-sm hidden-xs"> &nbsp; </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" > Ave </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs"> Count </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs"> Total </div> </div>  <div class="presentation-list" id="presentation-list"> <presentationitem each="{ presentation, i in presentations }" activekey="{ activekey }" key="{ i }" data="{ presentation }" details="{details}"></presentationitem> </div> </div> <div if="{ !quantity && searchmode }">No results were found</div> </div> <div class="col-lg-8" show="{ details }"> <div class="panel panel-default" name="presentation-details"> <div class="panel-heading"> <h3 class="panel-title">Presentation Details <a href="#" onclick="{ closeDetails }"><i class="fa fa-times pull-right"></i></a></h3> </div> <div class="panel-body">  <div class="row"> <div class="col-lg-6"> <strong>Category:</strong> { currentPresentation.category_name } <br><a data-toggle="modal" data-target="#myModal" href="#"><i class="fa fa-random"></i>&nbsp;Suggest Category Change</a> </div> <div class="col-lg-6"> <div class="btn-group pull-right" role="group" >  <button if="{ currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ unselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> My List</button> <button if="{ !currentPresentation.selected && currentPresentation.can_assign }" type="button" onclick="{ selectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> My List</button>  <button if="{ currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupUnselectPresentation }" class="btn btn-success select-button"><i class="fa fa-check-circle-o"></i> Team List</button> <button if="{ !currentPresentation.group_selected && currentPresentation.can_assign }" type="button" onclick="{ groupSelectPresentation }" class="btn btn-default select-button"><i class="fa fa-circle-o"></i> Team List</button> </div> </div> </div> <hr> <h2>{ currentPresentation.title }</h2> <h4>{ currentPresentation.level }</h4> <hr> <span class="label label-primary">Vote Count <span class="badge">{ currentPresentation.vote_count }</span></span> <span class="label label-primary">Vote Ave <span class="badge">{ currentPresentation.vote_average }</span></span> <span class="label label-primary">Vote Total <span class="badge">{ currentPresentation.total_points }</span></span> <span class="label label-info" show="{currentPresentation.comments.length}">Chair Comments: { currentPresentation.comments.length }</span> <hr> <raw content="{ currentPresentation.description }"></raw> <div each="{ currentPresentation.speakers }"> <hr> <h4>{ first_name }&nbsp;{ last_name }</h5> <p>{ title }</p> <raw content="{ bio }"></raw> </div> <div> </div> <div if="{ currentPresentation.comments[0] }"> <hr> <h4>Comments</h4> <comment each="{ currentPresentation.comments }"></comment> <hr> </div> <addcommentform api="{ this.opts }" presentation="{ currentPresentation }"></addcommentform> </div> </div> </div> </div>  </div>', function(opts) {
>>>>>>> 11ba413... Adding admin tools to approve changes and eavesdrop on other chair selections.

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

	riot.tag('comment', '<div class="media"> <div class="media-body"> <h4 class="media-heading">{ name }</h4> { body } </div> </div>', function(opts) {

	});

/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	var Sortable = __webpack_require__(5)
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
/* 5 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/**!
	 * Sortable
	 * @author	RubaXa   <trash@rubaxa.org>
	 * @license MIT
	 */


	(function (factory) {
		"use strict";

		if (true) {
			!(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		}
		else if (typeof module != "undefined" && typeof module.exports != "undefined") {
			module.exports = factory();
		}
		else if (typeof Package !== "undefined") {
			Sortable = factory();  // export for Meteor.js
		}
		else {
			/* jshint sub:true */
			window["Sortable"] = factory();
		}
	})(function () {
		"use strict";

		var dragEl,
			ghostEl,
			cloneEl,
			rootEl,
			nextEl,

			scrollEl,
			scrollParentEl,

			lastEl,
			lastCSS,

			oldIndex,
			newIndex,

			activeGroup,
			autoScroll = {},

			tapEvt,
			touchEvt,

			/** @const */
			RSPACE = /\s+/g,

			expando = 'Sortable' + (new Date).getTime(),

			win = window,
			document = win.document,
			parseInt = win.parseInt,

			supportDraggable = !!('draggable' in document.createElement('div')),

			_silent = false,

			_dispatchEvent = function (sortable, rootEl, name, targetEl, fromEl, startIndex, newIndex) {
				var evt = document.createEvent('Event'),
					options = (sortable || rootEl[expando]).options,
					onName = 'on' + name.charAt(0).toUpperCase() + name.substr(1);

				evt.initEvent(name, true, true);

				evt.item = targetEl || rootEl;
				evt.from = fromEl || rootEl;
				evt.clone = cloneEl;

				evt.oldIndex = startIndex;
				evt.newIndex = newIndex;

				if (options[onName]) {
					options[onName].call(sortable, evt);
				}

				rootEl.dispatchEvent(evt);
			},

			abs = Math.abs,
			slice = [].slice,

			touchDragOverListeners = [],

			_autoScroll = _throttle(function (/**Event*/evt, /**Object*/options, /**HTMLElement*/rootEl) {
				// Bug: https://bugzilla.mozilla.org/show_bug.cgi?id=505521
				if (rootEl && options.scroll) {
					var el,
						rect,
						sens = options.scrollSensitivity,
						speed = options.scrollSpeed,

						x = evt.clientX,
						y = evt.clientY,

						winWidth = window.innerWidth,
						winHeight = window.innerHeight,

						vx,
						vy
					;

					// Delect scrollEl
					if (scrollParentEl !== rootEl) {
						scrollEl = options.scroll;
						scrollParentEl = rootEl;

						if (scrollEl === true) {
							scrollEl = rootEl;

							do {
								if ((scrollEl.offsetWidth < scrollEl.scrollWidth) ||
									(scrollEl.offsetHeight < scrollEl.scrollHeight)
								) {
									break;
								}
								/* jshint boss:true */
							} while (scrollEl = scrollEl.parentNode);
						}
					}

					if (scrollEl) {
						el = scrollEl;
						rect = scrollEl.getBoundingClientRect();
						vx = (abs(rect.right - x) <= sens) - (abs(rect.left - x) <= sens);
						vy = (abs(rect.bottom - y) <= sens) - (abs(rect.top - y) <= sens);
					}


					if (!(vx || vy)) {
						vx = (winWidth - x <= sens) - (x <= sens);
						vy = (winHeight - y <= sens) - (y <= sens);

						/* jshint expr:true */
						(vx || vy) && (el = win);
					}


					if (autoScroll.vx !== vx || autoScroll.vy !== vy || autoScroll.el !== el) {
						autoScroll.el = el;
						autoScroll.vx = vx;
						autoScroll.vy = vy;

						clearInterval(autoScroll.pid);

						if (el) {
							autoScroll.pid = setInterval(function () {
								if (el === win) {
									win.scrollTo(win.pageXOffset + vx * speed, win.pageYOffset + vy * speed);
								} else {
									vy && (el.scrollTop += vy * speed);
									vx && (el.scrollLeft += vx * speed);
								}
							}, 24);
						}
					}
				}
			}, 30)
		;



		/**
		 * @class  Sortable
		 * @param  {HTMLElement}  el
		 * @param  {Object}       [options]
		 */
		function Sortable(el, options) {
			this.el = el; // root element
			this.options = options = _extend({}, options);


			// Export instance
			el[expando] = this;


			// Default options
			var defaults = {
				group: Math.random(),
				sort: true,
				disabled: false,
				store: null,
				handle: null,
				scroll: true,
				scrollSensitivity: 30,
				scrollSpeed: 10,
				draggable: /[uo]l/i.test(el.nodeName) ? 'li' : '>*',
				ghostClass: 'sortable-ghost',
				ignore: 'a, img',
				filter: null,
				animation: 0,
				setData: function (dataTransfer, dragEl) {
					dataTransfer.setData('Text', dragEl.textContent);
				},
				dropBubble: false,
				dragoverBubble: false,
				dataIdAttr: 'data-id',
				delay: 0
			};


			// Set default options
			for (var name in defaults) {
				!(name in options) && (options[name] = defaults[name]);
			}


			var group = options.group;

			if (!group || typeof group != 'object') {
				group = options.group = { name: group };
			}


			['pull', 'put'].forEach(function (key) {
				if (!(key in group)) {
					group[key] = true;
				}
			});


			options.groups = ' ' + group.name + (group.put.join ? ' ' + group.put.join(' ') : '') + ' ';


			// Bind all private methods
			for (var fn in this) {
				if (fn.charAt(0) === '_') {
					this[fn] = _bind(this, this[fn]);
				}
			}


			// Bind events
			_on(el, 'mousedown', this._onTapStart);
			_on(el, 'touchstart', this._onTapStart);

			_on(el, 'dragover', this);
			_on(el, 'dragenter', this);

			touchDragOverListeners.push(this._onDragOver);

			// Restore sorting
			options.store && this.sort(options.store.get(this));
		}


		Sortable.prototype = /** @lends Sortable.prototype */ {
			constructor: Sortable,

			_onTapStart: function (/** Event|TouchEvent */evt) {
				var _this = this,
					el = this.el,
					options = this.options,
					type = evt.type,
					touch = evt.touches && evt.touches[0],
					target = (touch || evt).target,
					originalTarget = target,
					filter = options.filter;


				if (type === 'mousedown' && evt.button !== 0 || options.disabled) {
					return; // only left button or enabled
				}

				target = _closest(target, options.draggable, el);

				if (!target) {
					return;
				}

				// get the index of the dragged element within its parent
				oldIndex = _index(target);

				// Check filter
				if (typeof filter === 'function') {
					if (filter.call(this, evt, target, this)) {
						_dispatchEvent(_this, originalTarget, 'filter', target, el, oldIndex);
						evt.preventDefault();
						return; // cancel dnd
					}
				}
				else if (filter) {
					filter = filter.split(',').some(function (criteria) {
						criteria = _closest(originalTarget, criteria.trim(), el);

						if (criteria) {
							_dispatchEvent(_this, criteria, 'filter', target, el, oldIndex);
							return true;
						}
					});

					if (filter) {
						evt.preventDefault();
						return; // cancel dnd
					}
				}


				if (options.handle && !_closest(originalTarget, options.handle, el)) {
					return;
				}


				// Prepare `dragstart`
				this._prepareDragStart(evt, touch, target);
			},

			_prepareDragStart: function (/** Event */evt, /** Touch */touch, /** HTMLElement */target) {
				var _this = this,
					el = _this.el,
					options = _this.options,
					ownerDocument = el.ownerDocument,
					dragStartFn;

				if (target && !dragEl && (target.parentNode === el)) {
					tapEvt = evt;

					rootEl = el;
					dragEl = target;
					nextEl = dragEl.nextSibling;
					activeGroup = options.group;

					dragStartFn = function () {
						// Delayed drag has been triggered
						// we can re-enable the events: touchmove/mousemove
						_this._disableDelayedDrag();

						// Make the element draggable
						dragEl.draggable = true;

						// Disable "draggable"
						options.ignore.split(',').forEach(function (criteria) {
							_find(dragEl, criteria.trim(), _disableDraggable);
						});

						// Bind the events: dragstart/dragend
						_this._triggerDragStart(touch);
					};

					_on(ownerDocument, 'mouseup', _this._onDrop);
					_on(ownerDocument, 'touchend', _this._onDrop);
					_on(ownerDocument, 'touchcancel', _this._onDrop);

					if (options.delay) {
						// If the user moves the pointer before the delay has been reached:
						// disable the delayed drag
						_on(ownerDocument, 'mousemove', _this._disableDelayedDrag);
						_on(ownerDocument, 'touchmove', _this._disableDelayedDrag);

						_this._dragStartTimer = setTimeout(dragStartFn, options.delay);
					} else {
						dragStartFn();
					}
				}
			},

			_disableDelayedDrag: function () {
				var ownerDocument = this.el.ownerDocument;

				clearTimeout(this._dragStartTimer);

				_off(ownerDocument, 'mousemove', this._disableDelayedDrag);
				_off(ownerDocument, 'touchmove', this._disableDelayedDrag);
			},

			_triggerDragStart: function (/** Touch */touch) {
				if (touch) {
					// Touch device support
					tapEvt = {
						target: dragEl,
						clientX: touch.clientX,
						clientY: touch.clientY
					};

					this._onDragStart(tapEvt, 'touch');
				}
				else if (!supportDraggable) {
					this._onDragStart(tapEvt, true);
				}
				else {
					_on(dragEl, 'dragend', this);
					_on(rootEl, 'dragstart', this._onDragStart);
				}

				try {
					if (document.selection) {
						document.selection.empty();
					} else {
						window.getSelection().removeAllRanges();
					}
				} catch (err) {
				}
			},

			_dragStarted: function () {
				if (rootEl && dragEl) {
					// Apply effect
					_toggleClass(dragEl, this.options.ghostClass, true);

					Sortable.active = this;

					// Drag start event
					_dispatchEvent(this, rootEl, 'start', dragEl, rootEl, oldIndex);
				}
			},

			_emulateDragOver: function () {
				if (touchEvt) {
					_css(ghostEl, 'display', 'none');

					var target = document.elementFromPoint(touchEvt.clientX, touchEvt.clientY),
						parent = target,
						groupName = ' ' + this.options.group.name + '',
						i = touchDragOverListeners.length;

					if (parent) {
						do {
							if (parent[expando] && parent[expando].options.groups.indexOf(groupName) > -1) {
								while (i--) {
									touchDragOverListeners[i]({
										clientX: touchEvt.clientX,
										clientY: touchEvt.clientY,
										target: target,
										rootEl: parent
									});
								}

								break;
							}

							target = parent; // store last element
						}
						/* jshint boss:true */
						while (parent = parent.parentNode);
					}

					_css(ghostEl, 'display', '');
				}
			},


			_onTouchMove: function (/**TouchEvent*/evt) {
				if (tapEvt) {
					var touch = evt.touches ? evt.touches[0] : evt,
						dx = touch.clientX - tapEvt.clientX,
						dy = touch.clientY - tapEvt.clientY,
						translate3d = evt.touches ? 'translate3d(' + dx + 'px,' + dy + 'px,0)' : 'translate(' + dx + 'px,' + dy + 'px)';

					touchEvt = touch;

					_css(ghostEl, 'webkitTransform', translate3d);
					_css(ghostEl, 'mozTransform', translate3d);
					_css(ghostEl, 'msTransform', translate3d);
					_css(ghostEl, 'transform', translate3d);

					evt.preventDefault();
				}
			},


			_onDragStart: function (/**Event*/evt, /**boolean*/useFallback) {
				var dataTransfer = evt.dataTransfer,
					options = this.options;

				this._offUpEvents();

				if (activeGroup.pull == 'clone') {
					cloneEl = dragEl.cloneNode(true);
					_css(cloneEl, 'display', 'none');
					rootEl.insertBefore(cloneEl, dragEl);
				}

				if (useFallback) {
					var rect = dragEl.getBoundingClientRect(),
						css = _css(dragEl),
						ghostRect;

					ghostEl = dragEl.cloneNode(true);

					_css(ghostEl, 'top', rect.top - parseInt(css.marginTop, 10));
					_css(ghostEl, 'left', rect.left - parseInt(css.marginLeft, 10));
					_css(ghostEl, 'width', rect.width);
					_css(ghostEl, 'height', rect.height);
					_css(ghostEl, 'opacity', '0.8');
					_css(ghostEl, 'position', 'fixed');
					_css(ghostEl, 'zIndex', '100000');

					rootEl.appendChild(ghostEl);

					// Fixing dimensions.
					ghostRect = ghostEl.getBoundingClientRect();
					_css(ghostEl, 'width', rect.width * 2 - ghostRect.width);
					_css(ghostEl, 'height', rect.height * 2 - ghostRect.height);

					if (useFallback === 'touch') {
						// Bind touch events
						_on(document, 'touchmove', this._onTouchMove);
						_on(document, 'touchend', this._onDrop);
						_on(document, 'touchcancel', this._onDrop);
					} else {
						// Old brwoser
						_on(document, 'mousemove', this._onTouchMove);
						_on(document, 'mouseup', this._onDrop);
					}

					this._loopId = setInterval(this._emulateDragOver, 150);
				}
				else {
					if (dataTransfer) {
						dataTransfer.effectAllowed = 'move';
						options.setData && options.setData.call(this, dataTransfer, dragEl);
					}

					_on(document, 'drop', this);
				}

				setTimeout(this._dragStarted, 0);
			},

			_onDragOver: function (/**Event*/evt) {
				var el = this.el,
					target,
					dragRect,
					revert,
					options = this.options,
					group = options.group,
					groupPut = group.put,
					isOwner = (activeGroup === group),
					canSort = options.sort;

				if (evt.preventDefault !== void 0) {
					evt.preventDefault();
					!options.dragoverBubble && evt.stopPropagation();
				}

				if (activeGroup && !options.disabled &&
					(isOwner
						? canSort || (revert = !rootEl.contains(dragEl))
						: activeGroup.pull && groupPut && (
							(activeGroup.name === group.name) || // by Name
							(groupPut.indexOf && ~groupPut.indexOf(activeGroup.name)) // by Array
						)
					) &&
					(evt.rootEl === void 0 || evt.rootEl === this.el)
				) {
					// Smart auto-scrolling
					_autoScroll(evt, options, this.el);

					if (_silent) {
						return;
					}

					target = _closest(evt.target, options.draggable, el);
					dragRect = dragEl.getBoundingClientRect();


					if (revert) {
						_cloneHide(true);

						if (cloneEl || nextEl) {
							rootEl.insertBefore(dragEl, cloneEl || nextEl);
						}
						else if (!canSort) {
							rootEl.appendChild(dragEl);
						}

						return;
					}


					if ((el.children.length === 0) || (el.children[0] === ghostEl) ||
						(el === evt.target) && (target = _ghostInBottom(el, evt))
					) {
						if (target) {
							if (target.animated) {
								return;
							}
							targetRect = target.getBoundingClientRect();
						}

						_cloneHide(isOwner);

						el.appendChild(dragEl);
						this._animate(dragRect, dragEl);
						target && this._animate(targetRect, target);
					}
					else if (target && !target.animated && target !== dragEl && (target.parentNode[expando] !== void 0)) {
						if (lastEl !== target) {
							lastEl = target;
							lastCSS = _css(target);
						}


						var targetRect = target.getBoundingClientRect(),
							width = targetRect.right - targetRect.left,
							height = targetRect.bottom - targetRect.top,
							floating = /left|right|inline/.test(lastCSS.cssFloat + lastCSS.display),
							isWide = (target.offsetWidth > dragEl.offsetWidth),
							isLong = (target.offsetHeight > dragEl.offsetHeight),
							halfway = (floating ? (evt.clientX - targetRect.left) / width : (evt.clientY - targetRect.top) / height) > 0.5,
							nextSibling = target.nextElementSibling,
							after
						;

						_silent = true;
						setTimeout(_unsilent, 30);

						_cloneHide(isOwner);

						if (floating) {
							after = (target.previousElementSibling === dragEl) && !isWide || halfway && isWide;
						} else {
							after = (nextSibling !== dragEl) && !isLong || halfway && isLong;
						}

						if (after && !nextSibling) {
							el.appendChild(dragEl);
						} else {
							target.parentNode.insertBefore(dragEl, after ? nextSibling : target);
						}

						this._animate(dragRect, dragEl);
						this._animate(targetRect, target);
					}
				}
			},

			_animate: function (prevRect, target) {
				var ms = this.options.animation;

				if (ms) {
					var currentRect = target.getBoundingClientRect();

					_css(target, 'transition', 'none');
					_css(target, 'transform', 'translate3d('
						+ (prevRect.left - currentRect.left) + 'px,'
						+ (prevRect.top - currentRect.top) + 'px,0)'
					);

					target.offsetWidth; // repaint

					_css(target, 'transition', 'all ' + ms + 'ms');
					_css(target, 'transform', 'translate3d(0,0,0)');

					clearTimeout(target.animated);
					target.animated = setTimeout(function () {
						_css(target, 'transition', '');
						_css(target, 'transform', '');
						target.animated = false;
					}, ms);
				}
			},

			_offUpEvents: function () {
				var ownerDocument = this.el.ownerDocument;

				_off(document, 'touchmove', this._onTouchMove);
				_off(ownerDocument, 'mouseup', this._onDrop);
				_off(ownerDocument, 'touchend', this._onDrop);
				_off(ownerDocument, 'touchcancel', this._onDrop);
			},

			_onDrop: function (/**Event*/evt) {
				var el = this.el,
					options = this.options;

				clearInterval(this._loopId);
				clearInterval(autoScroll.pid);

				clearTimeout(this.dragStartTimer);

				// Unbind events
				_off(document, 'drop', this);
				_off(document, 'mousemove', this._onTouchMove);
				_off(el, 'dragstart', this._onDragStart);

				this._offUpEvents();

				if (evt) {
					evt.preventDefault();
					!options.dropBubble && evt.stopPropagation();

					ghostEl && ghostEl.parentNode.removeChild(ghostEl);

					if (dragEl) {
						_off(dragEl, 'dragend', this);

						_disableDraggable(dragEl);
						_toggleClass(dragEl, this.options.ghostClass, false);

						if (rootEl !== dragEl.parentNode) {
							newIndex = _index(dragEl);

							// drag from one list and drop into another
							_dispatchEvent(null, dragEl.parentNode, 'sort', dragEl, rootEl, oldIndex, newIndex);
							_dispatchEvent(this, rootEl, 'sort', dragEl, rootEl, oldIndex, newIndex);

							// Add event
							_dispatchEvent(null, dragEl.parentNode, 'add', dragEl, rootEl, oldIndex, newIndex);

							// Remove event
							_dispatchEvent(this, rootEl, 'remove', dragEl, rootEl, oldIndex, newIndex);
						}
						else {
							// Remove clone
							cloneEl && cloneEl.parentNode.removeChild(cloneEl);

							if (dragEl.nextSibling !== nextEl) {
								// Get the index of the dragged element within its parent
								newIndex = _index(dragEl);

								// drag & drop within the same list
								_dispatchEvent(this, rootEl, 'update', dragEl, rootEl, oldIndex, newIndex);
								_dispatchEvent(this, rootEl, 'sort', dragEl, rootEl, oldIndex, newIndex);
							}
						}

						// Drag end event
						Sortable.active && _dispatchEvent(this, rootEl, 'end', dragEl, rootEl, oldIndex, newIndex);
					}

					// Nulling
					rootEl =
					dragEl =
					ghostEl =
					nextEl =
					cloneEl =

					scrollEl =
					scrollParentEl =

					tapEvt =
					touchEvt =

					lastEl =
					lastCSS =

					activeGroup =
					Sortable.active = null;

					// Save sorting
					this.save();
				}
			},


			handleEvent: function (/**Event*/evt) {
				var type = evt.type;

				if (type === 'dragover' || type === 'dragenter') {
					if (dragEl) {
						this._onDragOver(evt);
						_globalDragOver(evt);
					}
				}
				else if (type === 'drop' || type === 'dragend') {
					this._onDrop(evt);
				}
			},


			/**
			 * Serializes the item into an array of string.
			 * @returns {String[]}
			 */
			toArray: function () {
				var order = [],
					el,
					children = this.el.children,
					i = 0,
					n = children.length,
					options = this.options;

				for (; i < n; i++) {
					el = children[i];
					if (_closest(el, options.draggable, this.el)) {
						order.push(el.getAttribute(options.dataIdAttr) || _generateId(el));
					}
				}

				return order;
			},


			/**
			 * Sorts the elements according to the array.
			 * @param  {String[]}  order  order of the items
			 */
			sort: function (order) {
				var items = {}, rootEl = this.el;

				this.toArray().forEach(function (id, i) {
					var el = rootEl.children[i];

					if (_closest(el, this.options.draggable, rootEl)) {
						items[id] = el;
					}
				}, this);

				order.forEach(function (id) {
					if (items[id]) {
						rootEl.removeChild(items[id]);
						rootEl.appendChild(items[id]);
					}
				});
			},


			/**
			 * Save the current sorting
			 */
			save: function () {
				var store = this.options.store;
				store && store.set(this);
			},


			/**
			 * For each element in the set, get the first element that matches the selector by testing the element itself and traversing up through its ancestors in the DOM tree.
			 * @param   {HTMLElement}  el
			 * @param   {String}       [selector]  default: `options.draggable`
			 * @returns {HTMLElement|null}
			 */
			closest: function (el, selector) {
				return _closest(el, selector || this.options.draggable, this.el);
			},


			/**
			 * Set/get option
			 * @param   {string} name
			 * @param   {*}      [value]
			 * @returns {*}
			 */
			option: function (name, value) {
				var options = this.options;

				if (value === void 0) {
					return options[name];
				} else {
					options[name] = value;
				}
			},


			/**
			 * Destroy
			 */
			destroy: function () {
				var el = this.el;

				el[expando] = null;

				_off(el, 'mousedown', this._onTapStart);
				_off(el, 'touchstart', this._onTapStart);

				_off(el, 'dragover', this);
				_off(el, 'dragenter', this);

				// Remove draggable attributes
				Array.prototype.forEach.call(el.querySelectorAll('[draggable]'), function (el) {
					el.removeAttribute('draggable');
				});

				touchDragOverListeners.splice(touchDragOverListeners.indexOf(this._onDragOver), 1);

				this._onDrop();

				this.el = el = null;
			}
		};


		function _cloneHide(state) {
			if (cloneEl && (cloneEl.state !== state)) {
				_css(cloneEl, 'display', state ? 'none' : '');
				!state && cloneEl.state && rootEl.insertBefore(cloneEl, dragEl);
				cloneEl.state = state;
			}
		}


		function _bind(ctx, fn) {
			var args = slice.call(arguments, 2);
			return	fn.bind ? fn.bind.apply(fn, [ctx].concat(args)) : function () {
				return fn.apply(ctx, args.concat(slice.call(arguments)));
			};
		}


		function _closest(/**HTMLElement*/el, /**String*/selector, /**HTMLElement*/ctx) {
			if (el) {
				ctx = ctx || document;
				selector = selector.split('.');

				var tag = selector.shift().toUpperCase(),
					re = new RegExp('\\s(' + selector.join('|') + ')\\s', 'g');

				do {
					if (
						(tag === '>*' && el.parentNode === ctx) || (
							(tag === '' || el.nodeName.toUpperCase() == tag) &&
							(!selector.length || ((' ' + el.className + ' ').match(re) || []).length == selector.length)
						)
					) {
						return el;
					}
				}
				while (el !== ctx && (el = el.parentNode));
			}

			return null;
		}


		function _globalDragOver(/**Event*/evt) {
			evt.dataTransfer.dropEffect = 'move';
			evt.preventDefault();
		}


		function _on(el, event, fn) {
			el.addEventListener(event, fn, false);
		}


		function _off(el, event, fn) {
			el.removeEventListener(event, fn, false);
		}


		function _toggleClass(el, name, state) {
			if (el) {
				if (el.classList) {
					el.classList[state ? 'add' : 'remove'](name);
				}
				else {
					var className = (' ' + el.className + ' ').replace(RSPACE, ' ').replace(' ' + name + ' ', ' ');
					el.className = (className + (state ? ' ' + name : '')).replace(RSPACE, ' ');
				}
			}
		}


		function _css(el, prop, val) {
			var style = el && el.style;

			if (style) {
				if (val === void 0) {
					if (document.defaultView && document.defaultView.getComputedStyle) {
						val = document.defaultView.getComputedStyle(el, '');
					}
					else if (el.currentStyle) {
						val = el.currentStyle;
					}

					return prop === void 0 ? val : val[prop];
				}
				else {
					if (!(prop in style)) {
						prop = '-webkit-' + prop;
					}

					style[prop] = val + (typeof val === 'string' ? '' : 'px');
				}
			}
		}


		function _find(ctx, tagName, iterator) {
			if (ctx) {
				var list = ctx.getElementsByTagName(tagName), i = 0, n = list.length;

				if (iterator) {
					for (; i < n; i++) {
						iterator(list[i], i);
					}
				}

				return list;
			}

			return [];
		}


		function _disableDraggable(el) {
			el.draggable = false;
		}


		function _unsilent() {
			_silent = false;
		}


		/** @returns {HTMLElement|false} */
		function _ghostInBottom(el, evt) {
			var lastEl = el.lastElementChild, rect = lastEl.getBoundingClientRect();
			return (evt.clientY - (rect.top + rect.height) > 5) && lastEl; // min delta
		}


		/**
		 * Generate id
		 * @param   {HTMLElement} el
		 * @returns {String}
		 * @private
		 */
		function _generateId(el) {
			var str = el.tagName + el.className + el.src + el.href + el.textContent,
				i = str.length,
				sum = 0;

			while (i--) {
				sum += str.charCodeAt(i);
			}

			return sum.toString(36);
		}

		/**
		 * Returns the index of an element within its parent
		 * @param el
		 * @returns {number}
		 * @private
		 */
		function _index(/**HTMLElement*/el) {
			var index = 0;
			while (el && (el = el.previousElementSibling)) {
				if (el.nodeName.toUpperCase() !== 'TEMPLATE') {
					index++;
				}
			}
			return index;
		}

		function _throttle(callback, ms) {
			var args, _this;

			return function () {
				if (args === void 0) {
					args = arguments;
					_this = this;

					setTimeout(function () {
						if (args.length === 1) {
							callback.call(_this, args[0]);
						} else {
							callback.apply(_this, args);
						}

						args = void 0;
					}, ms);
				}
			};
		}

		function _extend(dst, src) {
			if (dst && src) {
				for (var key in src) {
					if (src.hasOwnProperty(key)) {
						dst[key] = src[key];
					}
				}
			}

			return dst;
		}


		// Export utils
		Sortable.utils = {
			on: _on,
			off: _off,
			css: _css,
			find: _find,
			bind: _bind,
			is: function (el, selector) {
				return !!_closest(el, selector, el);
			},
			extend: _extend,
			throttle: _throttle,
			closest: _closest,
			toggleClass: _toggleClass,
			index: _index
		};


		Sortable.version = '1.2.0';


		/**
		 * Create sortable instance
		 * @param {HTMLElement}  el
		 * @param {Object}      [options]
		 */
		Sortable.create = function (el, options) {
			return new Sortable(el, options);
		};

		// Export
		return Sortable;
	});


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

<<<<<<< HEAD
	riot.tag('presentationitem', '<div class="presentation-row {active: isActive()} { selected: opts.data.selected }" onclick="{ setActive }"> <div class="row"> <div class="{ col-lg-9: !opts.details } { col-lg-11: opts.details } { col-md-9: !opts.details } { col-md-11: opts.details }" onclick="{ setActive }"> <span class="pull-left presentation-row-icon"><i class="fa fa-check-circle-o" show="{ opts.data.selected }"></i>&nbsp;</span> <div class="presentation-title">{ opts.data.title }</div> </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show="{ !opts.details }" > { opts.data.vote_average } </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show="{ !opts.details }"> { opts.data.vote_count } </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show="{ !opts.details }"> { opts.data.total_points } </div> </div> </div>', '.presentation-row { border: 1px solid #D5D5D5; padding: 5px; margin-bottom: -1px; cursor: pointer; font-size: 1.3em; } .presentation-row.selected { background-color: rgba(221, 239, 255, 0.50); } .presentation-row a { text-decoration: none; } .presentation-row.active, .presentation-row.active a { background-color: #3A89D3; color: white; } .presentation-row .fa { padding-top: 0.2em; color: #0078AE; } .presentation-row.active .fa { color: white; } .presentation-row-icon { display: block; width: 30px; padding-left: 4px; } .presentation-title { margin-left: 30px; }', function(opts) {
=======
	riot.tag('presentationitem', '<div class="presentation-row {active: isActive()} { selected: opts.data.selected }" onclick="{ setActive }"> <div class="row"> <div class="{ col-lg-9: !opts.details } { col-lg-11: opts.details } { col-md-9: !opts.details } { col-md-11: opts.details }" onclick="{ setActive }"> <span class="pull-left presentation-row-icon"><i class="fa fa-check-circle-o" show="{ opts.data.selected }"></i>&nbsp;</span> <div class="presentation-title"> { opts.data.title } <span if="{ opts.data.moved_to_category }" class="new-presentation"><i class="fa fa-star"></i>New</span> </div> </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show="{ !opts.details }" > { opts.data.vote_average } </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show="{ !opts.details }"> { opts.data.vote_count } </div> <div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show="{ !opts.details }"> { opts.data.total_points } </div> </div> </div>', 'presentationitem .presentation-row, [riot-tag="presentationitem"] .presentation-row{ border: 1px solid #D5D5D5; padding: 5px; margin-bottom: -1px; cursor: pointer; font-size: 1.3em; } presentationitem .new-presentation, [riot-tag="presentationitem"] .new-presentation,presentationitem .new-presentation .fa, [riot-tag="presentationitem"] .new-presentation .fa{ color: orange!important; } presentationitem .presentation-row.active .new-presentation, [riot-tag="presentationitem"] .presentation-row.active .new-presentation,presentationitem .presentation-row.active .new-presentation .fa, [riot-tag="presentationitem"] .presentation-row.active .new-presentation .fa{ color: white!important; } presentationitem .presentation-row.selected, [riot-tag="presentationitem"] .presentation-row.selected{ background-color: rgba(221, 239, 255, 0.50); } presentationitem .presentation-row a, [riot-tag="presentationitem"] .presentation-row a{ text-decoration: none; } presentationitem .presentation-row.active, [riot-tag="presentationitem"] .presentation-row.active,presentationitem .presentation-row.active a, [riot-tag="presentationitem"] .presentation-row.active a{ background-color: #3A89D3; color: white; } presentationitem .presentation-row .fa, [riot-tag="presentationitem"] .presentation-row .fa{ padding-top: 0.2em; color: #0078AE; } presentationitem .presentation-row.active .fa, [riot-tag="presentationitem"] .presentation-row.active .fa{ color: white; } presentationitem .presentation-row-icon, [riot-tag="presentationitem"] .presentation-row-icon{ display: block; width: 30px; padding-left: 4px; } presentationitem .presentation-title, [riot-tag="presentationitem"] .presentation-title{ margin-left: 30px; }', function(opts) {
>>>>>>> 11ba413... Adding admin tools to approve changes and eavesdrop on other chair selections.

		this.setActive = function(e) {
			this.parent.setActiveKey(this.opts.key)
			riot.route('presentations/show/' + this.opts.data.id)
		}.bind(this);
		
		this.isActive = function() {
			return this.parent.activekey == this.opts.key
		}.bind(this);



	});

/***/ },
/* 7 */
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
/* 8 */
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
/* 9 */
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
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_RESULT__;/*global define:false */
	/**
	 * Copyright 2015 Craig Campbell
	 *
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 *
	 * http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	 *
	 * Mousetrap is a simple keyboard shortcut library for Javascript with
	 * no external dependencies
	 *
	 * @version 1.5.3
	 * @url craig.is/killing/mice
	 */
	(function(window, document, undefined) {

	    /**
	     * mapping of special keycodes to their corresponding keys
	     *
	     * everything in this dictionary cannot use keypress events
	     * so it has to be here to map to the correct keycodes for
	     * keyup/keydown events
	     *
	     * @type {Object}
	     */
	    var _MAP = {
	        8: 'backspace',
	        9: 'tab',
	        13: 'enter',
	        16: 'shift',
	        17: 'ctrl',
	        18: 'alt',
	        20: 'capslock',
	        27: 'esc',
	        32: 'space',
	        33: 'pageup',
	        34: 'pagedown',
	        35: 'end',
	        36: 'home',
	        37: 'left',
	        38: 'up',
	        39: 'right',
	        40: 'down',
	        45: 'ins',
	        46: 'del',
	        91: 'meta',
	        93: 'meta',
	        224: 'meta'
	    };

	    /**
	     * mapping for special characters so they can support
	     *
	     * this dictionary is only used incase you want to bind a
	     * keyup or keydown event to one of these keys
	     *
	     * @type {Object}
	     */
	    var _KEYCODE_MAP = {
	        106: '*',
	        107: '+',
	        109: '-',
	        110: '.',
	        111 : '/',
	        186: ';',
	        187: '=',
	        188: ',',
	        189: '-',
	        190: '.',
	        191: '/',
	        192: '`',
	        219: '[',
	        220: '\\',
	        221: ']',
	        222: '\''
	    };

	    /**
	     * this is a mapping of keys that require shift on a US keypad
	     * back to the non shift equivelents
	     *
	     * this is so you can use keyup events with these keys
	     *
	     * note that this will only work reliably on US keyboards
	     *
	     * @type {Object}
	     */
	    var _SHIFT_MAP = {
	        '~': '`',
	        '!': '1',
	        '@': '2',
	        '#': '3',
	        '$': '4',
	        '%': '5',
	        '^': '6',
	        '&': '7',
	        '*': '8',
	        '(': '9',
	        ')': '0',
	        '_': '-',
	        '+': '=',
	        ':': ';',
	        '\"': '\'',
	        '<': ',',
	        '>': '.',
	        '?': '/',
	        '|': '\\'
	    };

	    /**
	     * this is a list of special strings you can use to map
	     * to modifier keys when you specify your keyboard shortcuts
	     *
	     * @type {Object}
	     */
	    var _SPECIAL_ALIASES = {
	        'option': 'alt',
	        'command': 'meta',
	        'return': 'enter',
	        'escape': 'esc',
	        'plus': '+',
	        'mod': /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? 'meta' : 'ctrl'
	    };

	    /**
	     * variable to store the flipped version of _MAP from above
	     * needed to check if we should use keypress or not when no action
	     * is specified
	     *
	     * @type {Object|undefined}
	     */
	    var _REVERSE_MAP;

	    /**
	     * loop through the f keys, f1 to f19 and add them to the map
	     * programatically
	     */
	    for (var i = 1; i < 20; ++i) {
	        _MAP[111 + i] = 'f' + i;
	    }

	    /**
	     * loop through to map numbers on the numeric keypad
	     */
	    for (i = 0; i <= 9; ++i) {
	        _MAP[i + 96] = i;
	    }

	    /**
	     * cross browser add event method
	     *
	     * @param {Element|HTMLDocument} object
	     * @param {string} type
	     * @param {Function} callback
	     * @returns void
	     */
	    function _addEvent(object, type, callback) {
	        if (object.addEventListener) {
	            object.addEventListener(type, callback, false);
	            return;
	        }

	        object.attachEvent('on' + type, callback);
	    }

	    /**
	     * takes the event and returns the key character
	     *
	     * @param {Event} e
	     * @return {string}
	     */
	    function _characterFromEvent(e) {

	        // for keypress events we should return the character as is
	        if (e.type == 'keypress') {
	            var character = String.fromCharCode(e.which);

	            // if the shift key is not pressed then it is safe to assume
	            // that we want the character to be lowercase.  this means if
	            // you accidentally have caps lock on then your key bindings
	            // will continue to work
	            //
	            // the only side effect that might not be desired is if you
	            // bind something like 'A' cause you want to trigger an
	            // event when capital A is pressed caps lock will no longer
	            // trigger the event.  shift+a will though.
	            if (!e.shiftKey) {
	                character = character.toLowerCase();
	            }

	            return character;
	        }

	        // for non keypress events the special maps are needed
	        if (_MAP[e.which]) {
	            return _MAP[e.which];
	        }

	        if (_KEYCODE_MAP[e.which]) {
	            return _KEYCODE_MAP[e.which];
	        }

	        // if it is not in the special map

	        // with keydown and keyup events the character seems to always
	        // come in as an uppercase character whether you are pressing shift
	        // or not.  we should make sure it is always lowercase for comparisons
	        return String.fromCharCode(e.which).toLowerCase();
	    }

	    /**
	     * checks if two arrays are equal
	     *
	     * @param {Array} modifiers1
	     * @param {Array} modifiers2
	     * @returns {boolean}
	     */
	    function _modifiersMatch(modifiers1, modifiers2) {
	        return modifiers1.sort().join(',') === modifiers2.sort().join(',');
	    }

	    /**
	     * takes a key event and figures out what the modifiers are
	     *
	     * @param {Event} e
	     * @returns {Array}
	     */
	    function _eventModifiers(e) {
	        var modifiers = [];

	        if (e.shiftKey) {
	            modifiers.push('shift');
	        }

	        if (e.altKey) {
	            modifiers.push('alt');
	        }

	        if (e.ctrlKey) {
	            modifiers.push('ctrl');
	        }

	        if (e.metaKey) {
	            modifiers.push('meta');
	        }

	        return modifiers;
	    }

	    /**
	     * prevents default for this event
	     *
	     * @param {Event} e
	     * @returns void
	     */
	    function _preventDefault(e) {
	        if (e.preventDefault) {
	            e.preventDefault();
	            return;
	        }

	        e.returnValue = false;
	    }

	    /**
	     * stops propogation for this event
	     *
	     * @param {Event} e
	     * @returns void
	     */
	    function _stopPropagation(e) {
	        if (e.stopPropagation) {
	            e.stopPropagation();
	            return;
	        }

	        e.cancelBubble = true;
	    }

	    /**
	     * determines if the keycode specified is a modifier key or not
	     *
	     * @param {string} key
	     * @returns {boolean}
	     */
	    function _isModifier(key) {
	        return key == 'shift' || key == 'ctrl' || key == 'alt' || key == 'meta';
	    }

	    /**
	     * reverses the map lookup so that we can look for specific keys
	     * to see what can and can't use keypress
	     *
	     * @return {Object}
	     */
	    function _getReverseMap() {
	        if (!_REVERSE_MAP) {
	            _REVERSE_MAP = {};
	            for (var key in _MAP) {

	                // pull out the numeric keypad from here cause keypress should
	                // be able to detect the keys from the character
	                if (key > 95 && key < 112) {
	                    continue;
	                }

	                if (_MAP.hasOwnProperty(key)) {
	                    _REVERSE_MAP[_MAP[key]] = key;
	                }
	            }
	        }
	        return _REVERSE_MAP;
	    }

	    /**
	     * picks the best action based on the key combination
	     *
	     * @param {string} key - character for key
	     * @param {Array} modifiers
	     * @param {string=} action passed in
	     */
	    function _pickBestAction(key, modifiers, action) {

	        // if no action was picked in we should try to pick the one
	        // that we think would work best for this key
	        if (!action) {
	            action = _getReverseMap()[key] ? 'keydown' : 'keypress';
	        }

	        // modifier keys don't work as expected with keypress,
	        // switch to keydown
	        if (action == 'keypress' && modifiers.length) {
	            action = 'keydown';
	        }

	        return action;
	    }

	    /**
	     * Converts from a string key combination to an array
	     *
	     * @param  {string} combination like "command+shift+l"
	     * @return {Array}
	     */
	    function _keysFromString(combination) {
	        if (combination === '+') {
	            return ['+'];
	        }

	        combination = combination.replace(/\+{2}/g, '+plus');
	        return combination.split('+');
	    }

	    /**
	     * Gets info for a specific key combination
	     *
	     * @param  {string} combination key combination ("command+s" or "a" or "*")
	     * @param  {string=} action
	     * @returns {Object}
	     */
	    function _getKeyInfo(combination, action) {
	        var keys;
	        var key;
	        var i;
	        var modifiers = [];

	        // take the keys from this pattern and figure out what the actual
	        // pattern is all about
	        keys = _keysFromString(combination);

	        for (i = 0; i < keys.length; ++i) {
	            key = keys[i];

	            // normalize key names
	            if (_SPECIAL_ALIASES[key]) {
	                key = _SPECIAL_ALIASES[key];
	            }

	            // if this is not a keypress event then we should
	            // be smart about using shift keys
	            // this will only work for US keyboards however
	            if (action && action != 'keypress' && _SHIFT_MAP[key]) {
	                key = _SHIFT_MAP[key];
	                modifiers.push('shift');
	            }

	            // if this key is a modifier then add it to the list of modifiers
	            if (_isModifier(key)) {
	                modifiers.push(key);
	            }
	        }

	        // depending on what the key combination is
	        // we will try to pick the best event for it
	        action = _pickBestAction(key, modifiers, action);

	        return {
	            key: key,
	            modifiers: modifiers,
	            action: action
	        };
	    }

	    function _belongsTo(element, ancestor) {
	        if (element === null || element === document) {
	            return false;
	        }

	        if (element === ancestor) {
	            return true;
	        }

	        return _belongsTo(element.parentNode, ancestor);
	    }

	    function Mousetrap(targetElement) {
	        var self = this;

	        targetElement = targetElement || document;

	        if (!(self instanceof Mousetrap)) {
	            return new Mousetrap(targetElement);
	        }

	        /**
	         * element to attach key events to
	         *
	         * @type {Element}
	         */
	        self.target = targetElement;

	        /**
	         * a list of all the callbacks setup via Mousetrap.bind()
	         *
	         * @type {Object}
	         */
	        self._callbacks = {};

	        /**
	         * direct map of string combinations to callbacks used for trigger()
	         *
	         * @type {Object}
	         */
	        self._directMap = {};

	        /**
	         * keeps track of what level each sequence is at since multiple
	         * sequences can start out with the same sequence
	         *
	         * @type {Object}
	         */
	        var _sequenceLevels = {};

	        /**
	         * variable to store the setTimeout call
	         *
	         * @type {null|number}
	         */
	        var _resetTimer;

	        /**
	         * temporary state where we will ignore the next keyup
	         *
	         * @type {boolean|string}
	         */
	        var _ignoreNextKeyup = false;

	        /**
	         * temporary state where we will ignore the next keypress
	         *
	         * @type {boolean}
	         */
	        var _ignoreNextKeypress = false;

	        /**
	         * are we currently inside of a sequence?
	         * type of action ("keyup" or "keydown" or "keypress") or false
	         *
	         * @type {boolean|string}
	         */
	        var _nextExpectedAction = false;

	        /**
	         * resets all sequence counters except for the ones passed in
	         *
	         * @param {Object} doNotReset
	         * @returns void
	         */
	        function _resetSequences(doNotReset) {
	            doNotReset = doNotReset || {};

	            var activeSequences = false,
	                key;

	            for (key in _sequenceLevels) {
	                if (doNotReset[key]) {
	                    activeSequences = true;
	                    continue;
	                }
	                _sequenceLevels[key] = 0;
	            }

	            if (!activeSequences) {
	                _nextExpectedAction = false;
	            }
	        }

	        /**
	         * finds all callbacks that match based on the keycode, modifiers,
	         * and action
	         *
	         * @param {string} character
	         * @param {Array} modifiers
	         * @param {Event|Object} e
	         * @param {string=} sequenceName - name of the sequence we are looking for
	         * @param {string=} combination
	         * @param {number=} level
	         * @returns {Array}
	         */
	        function _getMatches(character, modifiers, e, sequenceName, combination, level) {
	            var i;
	            var callback;
	            var matches = [];
	            var action = e.type;

	            // if there are no events related to this keycode
	            if (!self._callbacks[character]) {
	                return [];
	            }

	            // if a modifier key is coming up on its own we should allow it
	            if (action == 'keyup' && _isModifier(character)) {
	                modifiers = [character];
	            }

	            // loop through all callbacks for the key that was pressed
	            // and see if any of them match
	            for (i = 0; i < self._callbacks[character].length; ++i) {
	                callback = self._callbacks[character][i];

	                // if a sequence name is not specified, but this is a sequence at
	                // the wrong level then move onto the next match
	                if (!sequenceName && callback.seq && _sequenceLevels[callback.seq] != callback.level) {
	                    continue;
	                }

	                // if the action we are looking for doesn't match the action we got
	                // then we should keep going
	                if (action != callback.action) {
	                    continue;
	                }

	                // if this is a keypress event and the meta key and control key
	                // are not pressed that means that we need to only look at the
	                // character, otherwise check the modifiers as well
	                //
	                // chrome will not fire a keypress if meta or control is down
	                // safari will fire a keypress if meta or meta+shift is down
	                // firefox will fire a keypress if meta or control is down
	                if ((action == 'keypress' && !e.metaKey && !e.ctrlKey) || _modifiersMatch(modifiers, callback.modifiers)) {

	                    // when you bind a combination or sequence a second time it
	                    // should overwrite the first one.  if a sequenceName or
	                    // combination is specified in this call it does just that
	                    //
	                    // @todo make deleting its own method?
	                    var deleteCombo = !sequenceName && callback.combo == combination;
	                    var deleteSequence = sequenceName && callback.seq == sequenceName && callback.level == level;
	                    if (deleteCombo || deleteSequence) {
	                        self._callbacks[character].splice(i, 1);
	                    }

	                    matches.push(callback);
	                }
	            }

	            return matches;
	        }

	        /**
	         * actually calls the callback function
	         *
	         * if your callback function returns false this will use the jquery
	         * convention - prevent default and stop propogation on the event
	         *
	         * @param {Function} callback
	         * @param {Event} e
	         * @returns void
	         */
	        function _fireCallback(callback, e, combo, sequence) {

	            // if this event should not happen stop here
	            if (self.stopCallback(e, e.target || e.srcElement, combo, sequence)) {
	                return;
	            }

	            if (callback(e, combo) === false) {
	                _preventDefault(e);
	                _stopPropagation(e);
	            }
	        }

	        /**
	         * handles a character key event
	         *
	         * @param {string} character
	         * @param {Array} modifiers
	         * @param {Event} e
	         * @returns void
	         */
	        self._handleKey = function(character, modifiers, e) {
	            var callbacks = _getMatches(character, modifiers, e);
	            var i;
	            var doNotReset = {};
	            var maxLevel = 0;
	            var processedSequenceCallback = false;

	            // Calculate the maxLevel for sequences so we can only execute the longest callback sequence
	            for (i = 0; i < callbacks.length; ++i) {
	                if (callbacks[i].seq) {
	                    maxLevel = Math.max(maxLevel, callbacks[i].level);
	                }
	            }

	            // loop through matching callbacks for this key event
	            for (i = 0; i < callbacks.length; ++i) {

	                // fire for all sequence callbacks
	                // this is because if for example you have multiple sequences
	                // bound such as "g i" and "g t" they both need to fire the
	                // callback for matching g cause otherwise you can only ever
	                // match the first one
	                if (callbacks[i].seq) {

	                    // only fire callbacks for the maxLevel to prevent
	                    // subsequences from also firing
	                    //
	                    // for example 'a option b' should not cause 'option b' to fire
	                    // even though 'option b' is part of the other sequence
	                    //
	                    // any sequences that do not match here will be discarded
	                    // below by the _resetSequences call
	                    if (callbacks[i].level != maxLevel) {
	                        continue;
	                    }

	                    processedSequenceCallback = true;

	                    // keep a list of which sequences were matches for later
	                    doNotReset[callbacks[i].seq] = 1;
	                    _fireCallback(callbacks[i].callback, e, callbacks[i].combo, callbacks[i].seq);
	                    continue;
	                }

	                // if there were no sequence matches but we are still here
	                // that means this is a regular match so we should fire that
	                if (!processedSequenceCallback) {
	                    _fireCallback(callbacks[i].callback, e, callbacks[i].combo);
	                }
	            }

	            // if the key you pressed matches the type of sequence without
	            // being a modifier (ie "keyup" or "keypress") then we should
	            // reset all sequences that were not matched by this event
	            //
	            // this is so, for example, if you have the sequence "h a t" and you
	            // type "h e a r t" it does not match.  in this case the "e" will
	            // cause the sequence to reset
	            //
	            // modifier keys are ignored because you can have a sequence
	            // that contains modifiers such as "enter ctrl+space" and in most
	            // cases the modifier key will be pressed before the next key
	            //
	            // also if you have a sequence such as "ctrl+b a" then pressing the
	            // "b" key will trigger a "keypress" and a "keydown"
	            //
	            // the "keydown" is expected when there is a modifier, but the
	            // "keypress" ends up matching the _nextExpectedAction since it occurs
	            // after and that causes the sequence to reset
	            //
	            // we ignore keypresses in a sequence that directly follow a keydown
	            // for the same character
	            var ignoreThisKeypress = e.type == 'keypress' && _ignoreNextKeypress;
	            if (e.type == _nextExpectedAction && !_isModifier(character) && !ignoreThisKeypress) {
	                _resetSequences(doNotReset);
	            }

	            _ignoreNextKeypress = processedSequenceCallback && e.type == 'keydown';
	        };

	        /**
	         * handles a keydown event
	         *
	         * @param {Event} e
	         * @returns void
	         */
	        function _handleKeyEvent(e) {

	            // normalize e.which for key events
	            // @see http://stackoverflow.com/questions/4285627/javascript-keycode-vs-charcode-utter-confusion
	            if (typeof e.which !== 'number') {
	                e.which = e.keyCode;
	            }

	            var character = _characterFromEvent(e);

	            // no character found then stop
	            if (!character) {
	                return;
	            }

	            // need to use === for the character check because the character can be 0
	            if (e.type == 'keyup' && _ignoreNextKeyup === character) {
	                _ignoreNextKeyup = false;
	                return;
	            }

	            self.handleKey(character, _eventModifiers(e), e);
	        }

	        /**
	         * called to set a 1 second timeout on the specified sequence
	         *
	         * this is so after each key press in the sequence you have 1 second
	         * to press the next key before you have to start over
	         *
	         * @returns void
	         */
	        function _resetSequenceTimer() {
	            clearTimeout(_resetTimer);
	            _resetTimer = setTimeout(_resetSequences, 1000);
	        }

	        /**
	         * binds a key sequence to an event
	         *
	         * @param {string} combo - combo specified in bind call
	         * @param {Array} keys
	         * @param {Function} callback
	         * @param {string=} action
	         * @returns void
	         */
	        function _bindSequence(combo, keys, callback, action) {

	            // start off by adding a sequence level record for this combination
	            // and setting the level to 0
	            _sequenceLevels[combo] = 0;

	            /**
	             * callback to increase the sequence level for this sequence and reset
	             * all other sequences that were active
	             *
	             * @param {string} nextAction
	             * @returns {Function}
	             */
	            function _increaseSequence(nextAction) {
	                return function() {
	                    _nextExpectedAction = nextAction;
	                    ++_sequenceLevels[combo];
	                    _resetSequenceTimer();
	                };
	            }

	            /**
	             * wraps the specified callback inside of another function in order
	             * to reset all sequence counters as soon as this sequence is done
	             *
	             * @param {Event} e
	             * @returns void
	             */
	            function _callbackAndReset(e) {
	                _fireCallback(callback, e, combo);

	                // we should ignore the next key up if the action is key down
	                // or keypress.  this is so if you finish a sequence and
	                // release the key the final key will not trigger a keyup
	                if (action !== 'keyup') {
	                    _ignoreNextKeyup = _characterFromEvent(e);
	                }

	                // weird race condition if a sequence ends with the key
	                // another sequence begins with
	                setTimeout(_resetSequences, 10);
	            }

	            // loop through keys one at a time and bind the appropriate callback
	            // function.  for any key leading up to the final one it should
	            // increase the sequence. after the final, it should reset all sequences
	            //
	            // if an action is specified in the original bind call then that will
	            // be used throughout.  otherwise we will pass the action that the
	            // next key in the sequence should match.  this allows a sequence
	            // to mix and match keypress and keydown events depending on which
	            // ones are better suited to the key provided
	            for (var i = 0; i < keys.length; ++i) {
	                var isFinal = i + 1 === keys.length;
	                var wrappedCallback = isFinal ? _callbackAndReset : _increaseSequence(action || _getKeyInfo(keys[i + 1]).action);
	                _bindSingle(keys[i], wrappedCallback, action, combo, i);
	            }
	        }

	        /**
	         * binds a single keyboard combination
	         *
	         * @param {string} combination
	         * @param {Function} callback
	         * @param {string=} action
	         * @param {string=} sequenceName - name of sequence if part of sequence
	         * @param {number=} level - what part of the sequence the command is
	         * @returns void
	         */
	        function _bindSingle(combination, callback, action, sequenceName, level) {

	            // store a direct mapped reference for use with Mousetrap.trigger
	            self._directMap[combination + ':' + action] = callback;

	            // make sure multiple spaces in a row become a single space
	            combination = combination.replace(/\s+/g, ' ');

	            var sequence = combination.split(' ');
	            var info;

	            // if this pattern is a sequence of keys then run through this method
	            // to reprocess each pattern one key at a time
	            if (sequence.length > 1) {
	                _bindSequence(combination, sequence, callback, action);
	                return;
	            }

	            info = _getKeyInfo(combination, action);

	            // make sure to initialize array if this is the first time
	            // a callback is added for this key
	            self._callbacks[info.key] = self._callbacks[info.key] || [];

	            // remove an existing match if there is one
	            _getMatches(info.key, info.modifiers, {type: info.action}, sequenceName, combination, level);

	            // add this call back to the array
	            // if it is a sequence put it at the beginning
	            // if not put it at the end
	            //
	            // this is important because the way these are processed expects
	            // the sequence ones to come first
	            self._callbacks[info.key][sequenceName ? 'unshift' : 'push']({
	                callback: callback,
	                modifiers: info.modifiers,
	                action: info.action,
	                seq: sequenceName,
	                level: level,
	                combo: combination
	            });
	        }

	        /**
	         * binds multiple combinations to the same callback
	         *
	         * @param {Array} combinations
	         * @param {Function} callback
	         * @param {string|undefined} action
	         * @returns void
	         */
	        self._bindMultiple = function(combinations, callback, action) {
	            for (var i = 0; i < combinations.length; ++i) {
	                _bindSingle(combinations[i], callback, action);
	            }
	        };

	        // start!
	        _addEvent(targetElement, 'keypress', _handleKeyEvent);
	        _addEvent(targetElement, 'keydown', _handleKeyEvent);
	        _addEvent(targetElement, 'keyup', _handleKeyEvent);
	    }

	    /**
	     * binds an event to mousetrap
	     *
	     * can be a single key, a combination of keys separated with +,
	     * an array of keys, or a sequence of keys separated by spaces
	     *
	     * be sure to list the modifier keys first to make sure that the
	     * correct key ends up getting bound (the last key in the pattern)
	     *
	     * @param {string|Array} keys
	     * @param {Function} callback
	     * @param {string=} action - 'keypress', 'keydown', or 'keyup'
	     * @returns void
	     */
	    Mousetrap.prototype.bind = function(keys, callback, action) {
	        var self = this;
	        keys = keys instanceof Array ? keys : [keys];
	        self._bindMultiple.call(self, keys, callback, action);
	        return self;
	    };

	    /**
	     * unbinds an event to mousetrap
	     *
	     * the unbinding sets the callback function of the specified key combo
	     * to an empty function and deletes the corresponding key in the
	     * _directMap dict.
	     *
	     * TODO: actually remove this from the _callbacks dictionary instead
	     * of binding an empty function
	     *
	     * the keycombo+action has to be exactly the same as
	     * it was defined in the bind method
	     *
	     * @param {string|Array} keys
	     * @param {string} action
	     * @returns void
	     */
	    Mousetrap.prototype.unbind = function(keys, action) {
	        var self = this;
	        return self.bind.call(self, keys, function() {}, action);
	    };

	    /**
	     * triggers an event that has already been bound
	     *
	     * @param {string} keys
	     * @param {string=} action
	     * @returns void
	     */
	    Mousetrap.prototype.trigger = function(keys, action) {
	        var self = this;
	        if (self._directMap[keys + ':' + action]) {
	            self._directMap[keys + ':' + action]({}, keys);
	        }
	        return self;
	    };

	    /**
	     * resets the library back to its initial state.  this is useful
	     * if you want to clear out the current keyboard shortcuts and bind
	     * new ones - for example if you switch to another page
	     *
	     * @returns void
	     */
	    Mousetrap.prototype.reset = function() {
	        var self = this;
	        self._callbacks = {};
	        self._directMap = {};
	        return self;
	    };

	    /**
	     * should we stop this event before firing off callbacks
	     *
	     * @param {Event} e
	     * @param {Element} element
	     * @return {boolean}
	     */
	    Mousetrap.prototype.stopCallback = function(e, element) {
	        var self = this;

	        // if the element has the class "mousetrap" then no need to stop
	        if ((' ' + element.className + ' ').indexOf(' mousetrap ') > -1) {
	            return false;
	        }

	        if (_belongsTo(element, self.target)) {
	            return false;
	        }

	        // stop for input, select, and textarea
	        return element.tagName == 'INPUT' || element.tagName == 'SELECT' || element.tagName == 'TEXTAREA' || element.isContentEditable;
	    };

	    /**
	     * exposes _handleKey publicly so it can be overwritten by extensions
	     */
	    Mousetrap.prototype.handleKey = function() {
	        var self = this;
	        return self._handleKey.apply(self, arguments);
	    };

	    /**
	     * Init the global mousetrap functions
	     *
	     * This method is needed to allow the global mousetrap functions to work
	     * now that mousetrap is a constructor function.
	     */
	    Mousetrap.init = function() {
	        var documentMousetrap = Mousetrap(document);
	        for (var method in documentMousetrap) {
	            if (method.charAt(0) !== '_') {
	                Mousetrap[method] = (function(method) {
	                    return function() {
	                        return documentMousetrap[method].apply(documentMousetrap, arguments);
	                    };
	                } (method));
	            }
	        }
	    };

	    Mousetrap.init();

	    // expose mousetrap to the global object
	    window.Mousetrap = Mousetrap;

	    // expose as a common js module
	    if (typeof module !== 'undefined' && module.exports) {
	        module.exports = Mousetrap;
	    }

	    // expose mousetrap as an AMD module
	    if (true) {
	        !(__WEBPACK_AMD_DEFINE_RESULT__ = function() {
	            return Mousetrap;
	        }.call(exports, __webpack_require__, exports, module), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	    }
	}) (window, document);


/***/ },
/* 11 */
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
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	__webpack_require__(13)
	__webpack_require__(14)

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
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	var Sortable = __webpack_require__(5)

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
/* 14 */
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
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(riot) {riot.tag("rg-toast",'<div class="toasts { opts.position }" if="{ opts.toasts.length > 0 }"> <div class="toast" each="{ opts.toasts }" onclick="{ parent.toastClicked }"> { text } </div> </div>','rg-toast .toasts, [riot-tag="rg-toast"] .toasts{ position: fixed; width: 250px; max-height: 100%; overflow-y: auto; background-color: transparent; z-index: 101; } rg-toast .toasts.topleft, [riot-tag="rg-toast"] .toasts.topleft{ top: 0; left: 0; } rg-toast .toasts.topright, [riot-tag="rg-toast"] .toasts.topright{ top: 0; right: 0; } rg-toast .toasts.bottomleft, [riot-tag="rg-toast"] .toasts.bottomleft{ bottom: 0; left: 0; } rg-toast .toasts.bottomright, [riot-tag="rg-toast"] .toasts.bottomright{ bottom: 0; right: 0; } rg-toast .toast, [riot-tag="rg-toast"] .toast{ padding: 20px; margin: 20px; background-color: rgba(0, 0, 0, 0.8); color: white; font-size: 13px; cursor: pointer; }',function(opts){var _this=this;_this.on("update",function(){opts.toasts.forEach(function(toast){toast.id=Math.random().toString(36).substr(2,8);if(!toast.timer&&!toast.sticky){toast.startTimer=function(){toast.timer=window.setTimeout(function(){opts.toasts.splice(opts.toasts.indexOf(toast),1);if(toast.onclose){toast.onclose()}_this.update()},toast.timeout||6e3)};toast.startTimer()}})});if(!opts.position){opts.position="topright"}_this.toastClicked=function(e){if(e.item.onclick){e.item.onclick(e)}if(e.item.onclose){e.item.onclose()}window.clearTimeout(e.item.timer);opts.toasts.splice(opts.toasts.indexOf(e.item),1)}});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(riot) {riot.tag("rg-modal",'<div class="overlay { expanded: opts.modal.visible, ghost: opts.modal.ghost }" onclick="{ close }"></div> <div class="modal { ghost: opts.modal.ghost }" if="{ opts.modal.visible }"> <header class="header"> <button if="{ opts.modal.close != false }" type="button" class="close" aria-label="Close" onclick="{ close }"> <span aria-hidden="true">&times;</span> </button> <h3 class="heading">{ opts.modal.heading }</h3> </header> <div class="body"> <yield></yield> </div> <footer class="footer"> <button class="button" each="{ opts.modal.buttons }" type="button" onclick="{ action }" riot-style="{ style }"> { text } </button> <div class="clear"></div> </footer> </div>','rg-modal .overlay, [riot-tag="rg-modal"] .overlay,rg-modal .overlay.ghost, [riot-tag="rg-modal"] .overlay.ghost{ position: fixed; top: 0; left: -100%; right: 0; bottom: 0; width: 100%; height: 100%; background-color: transparent; cursor: pointer; -webkit-transition: background-color 0.8s ease, left 0s 0.8s; -moz-transition: background-color 0.8s ease, left 0s 0.8s; -ms-transition: background-color 0.8s ease, left 0s 0.8s; -o-transition: background-color 0.8s ease, left 0s 0.8s; transition: background-color 0.8s ease, left 0s 0.8s; z-index: 50; } rg-modal .overlay.expanded, [riot-tag="rg-modal"] .overlay.expanded,rg-modal .overlay.ghost.expanded, [riot-tag="rg-modal"] .overlay.ghost.expanded{ left: 0; background-color: rgba(0, 0, 0, 0.8); -webkit-transition: background-color 0.8s ease, left 0s; -moz-transition: background-color 0.8s ease, left 0s; -ms-transition: background-color 0.8s ease, left 0s; -o-transition: background-color 0.8s ease, left 0s; transition: background-color 0.8s ease, left 0s; } rg-modal .modal, [riot-tag="rg-modal"] .modal,rg-modal .modal.ghost, [riot-tag="rg-modal"] .modal.ghost{ position: fixed; width: 95%; max-width: 500px; font-size: 1.1em; top: 50%; left: 50%; -webkit-transform: translate3d(-50%, -75%, 0); -moz-transform: translate3d(-50%, -75%, 0); -ms-transform: translate3d(-50%, -75%, 0); -o-transform: translate3d(-50%, -75%, 0); transform: translate3d(-50%, -75%, 0); background-color: white; color: #252519; z-index: 101; } rg-modal .modal.ghost, [riot-tag="rg-modal"] .modal.ghost{ background-color: transparent; color: white; } rg-modal .header, [riot-tag="rg-modal"] .header{ position: relative; text-align: center; } rg-modal .heading, [riot-tag="rg-modal"] .heading{ padding: 20px 20px 0 20px; margin: 0; font-size: 1.2em; } rg-modal .modal.ghost .heading, [riot-tag="rg-modal"] .modal.ghost .heading{ color: white; } rg-modal .close, [riot-tag="rg-modal"] .close{ position: absolute; top: 5px; right: 5px; padding: 0; height: 25px; width: 25px; line-height: 25px; font-size: 25px; border: 0; background-color: transparent; color: #ef424d; cursor: pointer; outline: none; } rg-modal .modal.ghost .close, [riot-tag="rg-modal"] .modal.ghost .close{ color: white; } rg-modal .body, [riot-tag="rg-modal"] .body{ padding: 20px; } rg-modal .footer, [riot-tag="rg-modal"] .footer{ padding: 0 20px 20px 20px; } rg-modal .button, [riot-tag="rg-modal"] .button{ float: right; padding: 10px; margin: 0 5px 0 0; border: none; font-size: 0.9em; text-transform: uppercase; cursor: pointer; outline: none; background-color: white; } rg-modal .modal.ghost .button, [riot-tag="rg-modal"] .modal.ghost .button{ color: white; background-color: transparent; } rg-modal .clear, [riot-tag="rg-modal"] .clear{ clear: both; }',function(opts){var _this=this;_this.close=function(e){opts.modal.visible=false;if(opts.modal.onclose){opts.modal.onclose(e)}}});
	/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(1)))

/***/ },
/* 17 */
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
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('chairdirectory', '<h2>Chair Directory</h2> <table class="table"> <tr> <th>Track</th> <th>Name</th> <th>Email</th> </tr> <tr each="{opts.chairs}"> <td>{ category }</td> <td><i class="fa fa-user"></i> { first_name } { last_name }</td> <td><a href="mailto:{ email }">{ email }</a></td> </tr> </table>', function(opts) {


	});

/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	riot.tag('tutorial', '<h2>A Quick Tutorial Video</h2> <iframe width="560" height="315" src="https://www.youtube.com/embed/_7A82b0Fp3A" frameborder="0" allowfullscreen></iframe>', function(opts) {


	});

/***/ },
/* 20 */,
/* 21 */,
/* 22 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);


	__webpack_require__(21)
	__webpack_require__(24)


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
/* 21 */
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
/* 22 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(riot) {
	/*
	* The track chairs API listeners and triggers to fetch server-side data
	*/

	// Requirements and globals
	reqwest = __webpack_require__(23)
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
/* 23 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	  * Reqwest! A general purpose XHR connection manager
	  * license MIT (c) Dustin Diaz 2014
	  * https://github.com/ded/reqwest
	  */

	!function (name, context, definition) {
	  if (typeof module != 'undefined' && module.exports) module.exports = definition()
	  else if (true) !(__WEBPACK_AMD_DEFINE_FACTORY__ = (definition), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__))
	  else context[name] = definition()
	}('reqwest', this, function () {

	  var win = window
	    , doc = document
	    , httpsRe = /^http/
	    , protocolRe = /(^\w+):\/\//
	    , twoHundo = /^(20\d|1223)$/ //http://stackoverflow.com/questions/10046972/msie-returns-status-code-of-1223-for-ajax-request
	    , byTag = 'getElementsByTagName'
	    , readyState = 'readyState'
	    , contentType = 'Content-Type'
	    , requestedWith = 'X-Requested-With'
	    , head = doc[byTag]('head')[0]
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
	          var xhr = win[xmlHttpRequest] ? new XMLHttpRequest() : null
	          if (xhr && 'withCredentials' in xhr) {
	            return xhr
	          } else if (win[xDomainRequest]) {
	            return new XDomainRequest()
	          } else {
	            throw new Error('Browser does not support cross-origin requests')
	          }
	        } else if (win[xmlHttpRequest]) {
	          return new XMLHttpRequest()
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
	    var protocol = protocolRe.exec(r.url);
	    protocol = (protocol && protocol[1]) || window.location.protocol;
	    return httpsRe.test(protocol) ? twoHundo.test(r.request.status) : !!r.request.response;
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

	    win[cbval] = generalCallback

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
	    if (win[xDomainRequest] && http instanceof win[xDomainRequest]) {
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
	            resp = win.JSON ? win.JSON.parse(r) : eval('(' + r + ')')
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
/* 24 */
/***/ function(module, exports, __webpack_require__) {

	var riot = __webpack_require__(1);

	<!-- Modal -->
	riot.tag('change-error-modal', '<div class="modal fade" id="changeErrorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> <div class="modal-dialog" role="document"> <div class="modal-content"> <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> <h4 class="modal-title" id="myModalLabel">Oops...</h4> </div> <div class="modal-body"> There was a request made to move this presentation to <strong>{ opts.request.new_category.title }</strong>, but it has already been selected by the track chairs of the current category, <strong>{ opts.request.old_category.title }</strong>. In order to move it, you\'ll need to ask if the chairs if they will unselect the presentation first. </div> <div class="modal-footer"> <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button> </div> </div> </div> </div>', function(opts) {


	});

/***/ }
/******/ ]);