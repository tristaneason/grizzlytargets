/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/raw-loader/index.js!./node_modules/EasyTabs/lib/jquery.easytabs.min.js":
/*!************************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/EasyTabs/lib/jquery.easytabs.min.js ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "/*\n * jQuery EasyTabs plugin 3.2.0\n *\n * Copyright (c) 2010-2011 Steve Schwartz (JangoSteve)\n *\n * Dual licensed under the MIT and GPL licenses:\n *   http://www.opensource.org/licenses/mit-license.php\n *   http://www.gnu.org/licenses/gpl.html\n *\n * Date: Thu May 09 17:30:00 2013 -0500\n */\n(function(a){a.easytabs=function(j,e){var f=this,q=a(j),i={animate:true,panelActiveClass:\"active\",tabActiveClass:\"active\",defaultTab:\"li:first-child\",animationSpeed:\"normal\",tabs:\"> ul > li\",updateHash:true,cycle:false,collapsible:false,collapsedClass:\"collapsed\",collapsedByDefault:true,uiTabs:false,transitionIn:\"fadeIn\",transitionOut:\"fadeOut\",transitionInEasing:\"swing\",transitionOutEasing:\"swing\",transitionCollapse:\"slideUp\",transitionUncollapse:\"slideDown\",transitionCollapseEasing:\"swing\",transitionUncollapseEasing:\"swing\",containerClass:\"\",tabsClass:\"\",tabClass:\"\",panelClass:\"\",cache:true,event:\"click\",panelContext:q},h,l,v,m,d,t={fast:200,normal:400,slow:600},r;f.init=function(){f.settings=r=a.extend({},i,e);r.bind_str=r.event+\".easytabs\";if(r.uiTabs){r.tabActiveClass=\"ui-tabs-selected\";r.containerClass=\"ui-tabs ui-widget ui-widget-content ui-corner-all\";r.tabsClass=\"ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all\";r.tabClass=\"ui-state-default ui-corner-top\";r.panelClass=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"}if(r.collapsible&&e.defaultTab!==undefined&&e.collpasedByDefault===undefined){r.collapsedByDefault=false}if(typeof(r.animationSpeed)===\"string\"){r.animationSpeed=t[r.animationSpeed]}a(\"a.anchor\").remove().prependTo(\"body\");q.data(\"easytabs\",{});f.setTransitions();f.getTabs();b();g();w();n();c();q.attr(\"data-easytabs\",true)};f.setTransitions=function(){v=(r.animate)?{show:r.transitionIn,hide:r.transitionOut,speed:r.animationSpeed,collapse:r.transitionCollapse,uncollapse:r.transitionUncollapse,halfSpeed:r.animationSpeed/2}:{show:\"show\",hide:\"hide\",speed:0,collapse:\"hide\",uncollapse:\"show\",halfSpeed:0}};f.getTabs=function(){var x;f.tabs=q.find(r.tabs),f.panels=a(),f.tabs.each(function(){var A=a(this),z=A.children(\"a\"),y=A.children(\"a\").data(\"target\");A.data(\"easytabs\",{});if(y!==undefined&&y!==null){A.data(\"easytabs\").ajax=z.attr(\"href\")}else{y=z.attr(\"href\")}y=y.match(/#([^\\?]+)/)[1];x=r.panelContext.find(\"#\"+y);if(x.length){x.data(\"easytabs\",{position:x.css(\"position\"),visibility:x.css(\"visibility\")});x.not(r.panelActiveClass).hide();f.panels=f.panels.add(x);A.data(\"easytabs\").panel=x}else{f.tabs=f.tabs.not(A);if(\"console\" in window){console.warn(\"Warning: tab without matching panel for selector '#\"+y+\"' removed from set\")}}})};f.selectTab=function(x,C){var y=window.location,B=y.hash.match(/^[^\\?]*/)[0],z=x.parent().data(\"easytabs\").panel,A=x.parent().data(\"easytabs\").ajax;if(r.collapsible&&!d&&(x.hasClass(r.tabActiveClass)||x.hasClass(r.collapsedClass))){f.toggleTabCollapse(x,z,A,C)}else{if(!x.hasClass(r.tabActiveClass)||!z.hasClass(r.panelActiveClass)){o(x,z,A,C)}else{if(!r.cache){o(x,z,A,C)}}}};f.toggleTabCollapse=function(x,y,z,A){f.panels.stop(true,true);if(u(q,\"easytabs:before\",[x,y,r])){f.tabs.filter(\".\"+r.tabActiveClass).removeClass(r.tabActiveClass).children().removeClass(r.tabActiveClass);if(x.hasClass(r.collapsedClass)){if(z&&(!r.cache||!x.parent().data(\"easytabs\").cached)){q.trigger(\"easytabs:ajax:beforeSend\",[x,y]);y.load(z,function(C,B,D){x.parent().data(\"easytabs\").cached=true;q.trigger(\"easytabs:ajax:complete\",[x,y,C,B,D])})}x.parent().removeClass(r.collapsedClass).addClass(r.tabActiveClass).children().removeClass(r.collapsedClass).addClass(r.tabActiveClass);y.addClass(r.panelActiveClass)[v.uncollapse](v.speed,r.transitionUncollapseEasing,function(){q.trigger(\"easytabs:midTransition\",[x,y,r]);if(typeof A==\"function\"){A()}})}else{x.addClass(r.collapsedClass).parent().addClass(r.collapsedClass);y.removeClass(r.panelActiveClass)[v.collapse](v.speed,r.transitionCollapseEasing,function(){q.trigger(\"easytabs:midTransition\",[x,y,r]);if(typeof A==\"function\"){A()}})}}};f.matchTab=function(x){return f.tabs.find(\"[href='\"+x+\"'],[data-target='\"+x+\"']\").first()};f.matchInPanel=function(x){return(x&&f.validId(x)?f.panels.filter(\":has(\"+x+\")\").first():[])};f.validId=function(x){return x.substr(1).match(/^[A-Za-z]+[A-Za-z0-9\\-_:\\.].$/)};f.selectTabFromHashChange=function(){var y=window.location.hash.match(/^[^\\?]*/)[0],x=f.matchTab(y),z;if(r.updateHash){if(x.length){d=true;f.selectTab(x)}else{z=f.matchInPanel(y);if(z.length){y=\"#\"+z.attr(\"id\");x=f.matchTab(y);d=true;f.selectTab(x)}else{if(!h.hasClass(r.tabActiveClass)&&!r.cycle){if(y===\"\"||f.matchTab(m).length||q.closest(y).length){d=true;f.selectTab(l)}}}}}};f.cycleTabs=function(x){if(r.cycle){x=x%f.tabs.length;$tab=a(f.tabs[x]).children(\"a\").first();d=true;f.selectTab($tab,function(){setTimeout(function(){f.cycleTabs(x+1)},r.cycle)})}};f.publicMethods={select:function(x){var y;if((y=f.tabs.filter(x)).length===0){if((y=f.tabs.find(\"a[href='\"+x+\"']\")).length===0){if((y=f.tabs.find(\"a\"+x)).length===0){if((y=f.tabs.find(\"[data-target='\"+x+\"']\")).length===0){if((y=f.tabs.find(\"a[href$='\"+x+\"']\")).length===0){a.error(\"Tab '\"+x+\"' does not exist in tab set\")}}}}}else{y=y.children(\"a\").first()}f.selectTab(y)}};var u=function(A,x,z){var y=a.Event(x);A.trigger(y,z);return y.result!==false};var b=function(){q.addClass(r.containerClass);f.tabs.parent().addClass(r.tabsClass);f.tabs.addClass(r.tabClass);f.panels.addClass(r.panelClass)};var g=function(){var y=window.location.hash.match(/^[^\\?]*/)[0],x=f.matchTab(y).parent(),z;if(x.length===1){h=x;r.cycle=false}else{z=f.matchInPanel(y);if(z.length){y=\"#\"+z.attr(\"id\");h=f.matchTab(y).parent()}else{h=f.tabs.parent().find(r.defaultTab);if(h.length===0){a.error(\"The specified default tab ('\"+r.defaultTab+\"') could not be found in the tab set ('\"+r.tabs+\"') out of \"+f.tabs.length+\" tabs.\")}}}l=h.children(\"a\").first();p(x)};var p=function(z){var y,x;if(r.collapsible&&z.length===0&&r.collapsedByDefault){h.addClass(r.collapsedClass).children().addClass(r.collapsedClass)}else{y=a(h.data(\"easytabs\").panel);x=h.data(\"easytabs\").ajax;if(x&&(!r.cache||!h.data(\"easytabs\").cached)){q.trigger(\"easytabs:ajax:beforeSend\",[l,y]);y.load(x,function(B,A,C){h.data(\"easytabs\").cached=true;q.trigger(\"easytabs:ajax:complete\",[l,y,B,A,C])})}h.data(\"easytabs\").panel.show().addClass(r.panelActiveClass);h.addClass(r.tabActiveClass).children().addClass(r.tabActiveClass)}q.trigger(\"easytabs:initialised\",[l,y])};var w=function(){f.tabs.children(\"a\").bind(r.bind_str,function(x){r.cycle=false;d=false;f.selectTab(a(this));x.preventDefault?x.preventDefault():x.returnValue=false})};var o=function(z,D,E,H){f.panels.stop(true,true);if(u(q,\"easytabs:before\",[z,D,r])){var A=f.panels.filter(\":visible\"),y=D.parent(),F,x,C,G,B=window.location.hash.match(/^[^\\?]*/)[0];if(r.animate){F=s(D);x=A.length?k(A):0;C=F-x}m=B;G=function(){q.trigger(\"easytabs:midTransition\",[z,D,r]);if(r.animate&&r.transitionIn==\"fadeIn\"){if(C<0){y.animate({height:y.height()+C},v.halfSpeed).css({\"min-height\":\"\"})}}if(r.updateHash&&!d){window.location.hash=\"#\"+D.attr(\"id\")}else{d=false}D[v.show](v.speed,r.transitionInEasing,function(){y.css({height:\"\",\"min-height\":\"\"});q.trigger(\"easytabs:after\",[z,D,r]);if(typeof H==\"function\"){H()}})};if(E&&(!r.cache||!z.parent().data(\"easytabs\").cached)){q.trigger(\"easytabs:ajax:beforeSend\",[z,D]);D.load(E,function(J,I,K){z.parent().data(\"easytabs\").cached=true;q.trigger(\"easytabs:ajax:complete\",[z,D,J,I,K])})}if(r.animate&&r.transitionOut==\"fadeOut\"){if(C>0){y.animate({height:(y.height()+C)},v.halfSpeed)}else{y.css({\"min-height\":y.height()})}}f.tabs.filter(\".\"+r.tabActiveClass).removeClass(r.tabActiveClass).children().removeClass(r.tabActiveClass);f.tabs.filter(\".\"+r.collapsedClass).removeClass(r.collapsedClass).children().removeClass(r.collapsedClass);z.parent().addClass(r.tabActiveClass).children().addClass(r.tabActiveClass);f.panels.filter(\".\"+r.panelActiveClass).removeClass(r.panelActiveClass);D.addClass(r.panelActiveClass);if(A.length){A[v.hide](v.speed,r.transitionOutEasing,G)}else{D[v.uncollapse](v.speed,r.transitionUncollapseEasing,G)}}};var s=function(z){if(z.data(\"easytabs\")&&z.data(\"easytabs\").lastHeight){return z.data(\"easytabs\").lastHeight}var B=z.css(\"display\"),y,x;try{y=a(\"<div></div>\",{position:\"absolute\",visibility:\"hidden\",overflow:\"hidden\"})}catch(A){y=a(\"<div></div>\",{visibility:\"hidden\",overflow:\"hidden\"})}x=z.wrap(y).css({position:\"relative\",visibility:\"hidden\",display:\"block\"}).outerHeight();z.unwrap();z.css({position:z.data(\"easytabs\").position,visibility:z.data(\"easytabs\").visibility,display:B});z.data(\"easytabs\").lastHeight=x;return x};var k=function(y){var x=y.outerHeight();if(y.data(\"easytabs\")){y.data(\"easytabs\").lastHeight=x}else{y.data(\"easytabs\",{lastHeight:x})}return x};var n=function(){if(typeof a(window).hashchange===\"function\"){a(window).hashchange(function(){f.selectTabFromHashChange()})}else{if(a.address&&typeof a.address.change===\"function\"){a.address.change(function(){f.selectTabFromHashChange()})}}};var c=function(){var x;if(r.cycle){x=f.tabs.index(h);setTimeout(function(){f.cycleTabs(x+1)},r.cycle)}};f.init()};a.fn.easytabs=function(c){var b=arguments;return this.each(function(){var e=a(this),d=e.data(\"easytabs\");if(undefined===d){d=new a.easytabs(this,c);e.data(\"easytabs\",d)}if(d.publicMethods[c]){return d.publicMethods[c](Array.prototype.slice.call(b,1))}})}})(jQuery);\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/EasyTabs/vendor/jquery.hashchange.min.js":
/*!*****************************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/EasyTabs/vendor/jquery.hashchange.min.js ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "/*\n * jQuery hashchange event - v1.3 - 7/21/2010\n * http://benalman.com/projects/jquery-hashchange-plugin/\n * \n * Copyright (c) 2010 \"Cowboy\" Ben Alman\n * Dual licensed under the MIT and GPL licenses.\n * http://benalman.com/about/license/\n */\n(function($,e,b){var c=\"hashchange\",h=document,f,g=$.event.special,i=h.documentMode,d=\"on\"+c in e&&(i===b||i>7);function a(j){j=j||location.href;return\"#\"+j.replace(/^[^#]*#?(.*)$/,\"$1\")}$.fn[c]=function(j){return j?this.bind(c,j):this.trigger(c)};$.fn[c].delay=50;g[c]=$.extend(g[c],{setup:function(){if(d){return false}$(f.start)},teardown:function(){if(d){return false}$(f.stop)}});f=(function(){var j={},p,m=a(),k=function(q){return q},l=k,o=k;j.start=function(){p||n()};j.stop=function(){p&&clearTimeout(p);p=b};function n(){var r=a(),q=o(m);if(r!==m){l(m=r,q);$(e).trigger(c)}else{if(q!==m){location.href=location.href.replace(/#.*/,\"\")+q}}p=setTimeout(n,$.fn[c].delay)}$.browser.msie&&!d&&(function(){var q,r;j.start=function(){if(!q){r=$.fn[c].src;r=r&&r+a();q=$('<iframe tabindex=\"-1\" title=\"empty\"/>').hide().one(\"load\",function(){r||l(a());n()}).attr(\"src\",r||\"javascript:0\").insertAfter(\"body\")[0].contentWindow;h.onpropertychange=function(){try{if(event.propertyName===\"title\"){q.document.title=h.title}}catch(s){}}}};j.stop=k;o=function(){return a(q.location.href)};l=function(v,s){var u=q.document,t=$.fn[c].domain;if(v!==s){u.title=h.title;u.open();t&&u.write('<script>document.domain=\"'+t+'\"<\\/script>');u.close();q.location.hash=v}}})();return j})()})(jQuery,this);"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/dom4/build/dom4.max.js":
/*!***********************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/dom4/build/dom4.max.js ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "/*!\nCopyright (C) 2013-2015 by Andrea Giammarchi - @WebReflection\n\nPermission is hereby granted, free of charge, to any person obtaining a copy\nof this software and associated documentation files (the \"Software\"), to deal\nin the Software without restriction, including without limitation the rights\nto use, copy, modify, merge, publish, distribute, sublicense, and/or sell\ncopies of the Software, and to permit persons to whom the Software is\nfurnished to do so, subject to the following conditions:\n\nThe above copyright notice and this permission notice shall be included in\nall copies or substantial portions of the Software.\n\nTHE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR\nIMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,\nFITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE\nAUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER\nLIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,\nOUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN\nTHE SOFTWARE.\n\n*/\n(function(window){'use strict';\n  /* jshint loopfunc: true, noempty: false*/\n  // http://www.w3.org/TR/dom/#element\n\n  function createDocumentFragment() {\n    return document.createDocumentFragment();\n  }\n\n  function createElement(nodeName) {\n    return document.createElement(nodeName);\n  }\n\n  function enoughArguments(length, name) {\n    if (!length) throw new Error(\n      'Failed to construct ' +\n        name +\n      ': 1 argument required, but only 0 present.'\n    );\n  }\n\n  function mutationMacro(nodes) {\n    if (nodes.length === 1) {\n      return textNodeIfPrimitive(nodes[0]);\n    }\n    for (var\n      fragment = createDocumentFragment(),\n      list = slice.call(nodes),\n      i = 0; i < nodes.length; i++\n    ) {\n      fragment.appendChild(textNodeIfPrimitive(list[i]));\n    }\n    return fragment;\n  }\n\n  function textNodeIfPrimitive(node) {\n    return typeof node === 'object' ? node : document.createTextNode(node);\n  }\n\n  for(var\n    head,\n    property,\n    TemporaryPrototype,\n    TemporaryTokenList,\n    wrapVerifyToken,\n    document = window.document,\n    hOP = Object.prototype.hasOwnProperty,\n    defineProperty = Object.defineProperty || function (object, property, descriptor) {\n      if (hOP.call(descriptor, 'value')) {\n        object[property] = descriptor.value;\n      } else {\n        if (hOP.call(descriptor, 'get'))\n          object.__defineGetter__(property, descriptor.get);\n        if (hOP.call(descriptor, 'set'))\n          object.__defineSetter__(property, descriptor.set);\n      }\n      return object;\n    },\n    indexOf = [].indexOf || function indexOf(value){\n      var length = this.length;\n      while(length--) {\n        if (this[length] === value) {\n          break;\n        }\n      }\n      return length;\n    },\n    // http://www.w3.org/TR/domcore/#domtokenlist\n    verifyToken = function (token) {\n      if (!token) {\n        throw 'SyntaxError';\n      } else if (spaces.test(token)) {\n        throw 'InvalidCharacterError';\n      }\n      return token;\n    },\n    DOMTokenList = function (node) {\n      var\n        noClassName = typeof node.className === 'undefined',\n        className = noClassName ?\n          (node.getAttribute('class') || '') : node.className,\n        isSVG = noClassName || typeof className === 'object',\n        value = (isSVG ?\n          (noClassName ? className : className.baseVal) :\n          className\n        ).replace(trim, '')\n      ;\n      if (value.length) {\n        properties.push.apply(\n          this,\n          value.split(spaces)\n        );\n      }\n      this._isSVG = isSVG;\n      this._ = node;\n    },\n    classListDescriptor = {\n      get: function get() {\n        return new DOMTokenList(this);\n      },\n      set: function(){}\n    },\n    trim = /^\\s+|\\s+$/g,\n    spaces = /\\s+/,\n    SPACE = '\\x20',\n    CLASS_LIST = 'classList',\n    toggle = function toggle(token, force) {\n      if (this.contains(token)) {\n        if (!force) {\n          // force is not true (either false or omitted)\n          this.remove(token);\n        }\n      } else if(force === undefined || force) {\n        force = true;\n        this.add(token);\n      }\n      return !!force;\n    },\n    DocumentFragmentPrototype = window.DocumentFragment && DocumentFragment.prototype,\n    Node = window.Node,\n    NodePrototype = (Node || Element).prototype,\n    CharacterData = window.CharacterData || Node,\n    CharacterDataPrototype = CharacterData && CharacterData.prototype,\n    DocumentType = window.DocumentType,\n    DocumentTypePrototype = DocumentType && DocumentType.prototype,\n    ElementPrototype = (window.Element || Node || window.HTMLElement).prototype,\n    HTMLSelectElement = window.HTMLSelectElement || createElement('select').constructor,\n    selectRemove = HTMLSelectElement.prototype.remove,\n    SVGElement = window.SVGElement,\n    properties = [\n      'matches', (\n        ElementPrototype.matchesSelector ||\n        ElementPrototype.webkitMatchesSelector ||\n        ElementPrototype.khtmlMatchesSelector ||\n        ElementPrototype.mozMatchesSelector ||\n        ElementPrototype.msMatchesSelector ||\n        ElementPrototype.oMatchesSelector ||\n        function matches(selector) {\n          var parentNode = this.parentNode;\n          return !!parentNode && -1 < indexOf.call(\n            parentNode.querySelectorAll(selector),\n            this\n          );\n        }\n      ),\n      'closest', function closest(selector) {\n        var parentNode = this, matches;\n        while (\n          // document has no .matches\n          (matches = parentNode && parentNode.matches) &&\n          !parentNode.matches(selector)\n        ) {\n          parentNode = parentNode.parentNode;\n        }\n        return matches ? parentNode : null;\n      },\n      'prepend', function prepend() {\n        var firstChild = this.firstChild,\n            node = mutationMacro(arguments);\n        if (firstChild) {\n          this.insertBefore(node, firstChild);\n        } else {\n          this.appendChild(node);\n        }\n      },\n      'append', function append() {\n        this.appendChild(mutationMacro(arguments));\n      },\n      'before', function before() {\n        var parentNode = this.parentNode;\n        if (parentNode) {\n          parentNode.insertBefore(\n            mutationMacro(arguments), this\n          );\n        }\n      },\n      'after', function after() {\n        var parentNode = this.parentNode,\n            nextSibling = this.nextSibling,\n            node = mutationMacro(arguments);\n        if (parentNode) {\n          if (nextSibling) {\n            parentNode.insertBefore(node, nextSibling);\n          } else {\n            parentNode.appendChild(node);\n          }\n        }\n      },\n      // https://dom.spec.whatwg.org/#dom-element-toggleattribute\n      'toggleAttribute', function toggleAttribute(name, force) {\n        var had = this.hasAttribute(name);\n        if (1 < arguments.length) {\n          if (had && !force)\n            this.removeAttribute(name);\n          else if (force && !had)\n            this.setAttribute(name, \"\");\n        }\n        else if (had)\n          this.removeAttribute(name);\n        else\n          this.setAttribute(name, \"\");\n        return this.hasAttribute(name);\n      },\n      // WARNING - DEPRECATED - use .replaceWith() instead\n      'replace', function replace() {\n        this.replaceWith.apply(this, arguments);\n      },\n      'replaceWith', function replaceWith() {\n        var parentNode = this.parentNode;\n        if (parentNode) {\n          parentNode.replaceChild(\n            mutationMacro(arguments),\n            this\n          );\n        }\n      },\n      'remove', function remove() {\n        var parentNode = this.parentNode;\n        if (parentNode) {\n          parentNode.removeChild(this);\n        }\n      }\n    ],\n    slice = properties.slice,\n    i = properties.length; i; i -= 2\n  ) {\n    property = properties[i - 2];\n    if (!(property in ElementPrototype)) {\n      ElementPrototype[property] = properties[i - 1];\n    }\n    // avoid unnecessary re-patch when the script is included\n    // gazillion times without any reason whatsoever\n    // https://github.com/WebReflection/dom4/pull/48\n    if (property === 'remove' && !selectRemove._dom4) {\n      // see https://github.com/WebReflection/dom4/issues/19\n      (HTMLSelectElement.prototype[property] = function () {\n        return 0 < arguments.length ?\n          selectRemove.apply(this, arguments) :\n          ElementPrototype.remove.call(this);\n      })._dom4 = true;\n    }\n    // see https://github.com/WebReflection/dom4/issues/18\n    if (/^(?:before|after|replace|replaceWith|remove)$/.test(property)) {\n      if (CharacterData && !(property in CharacterDataPrototype)) {\n        CharacterDataPrototype[property] = properties[i - 1];\n      }\n      if (DocumentType && !(property in DocumentTypePrototype)) {\n        DocumentTypePrototype[property] = properties[i - 1];\n      }\n    }\n    // see https://github.com/WebReflection/dom4/pull/26\n    if (/^(?:append|prepend)$/.test(property)) {\n      if (DocumentFragmentPrototype) {\n        if (!(property in DocumentFragmentPrototype)) {\n          DocumentFragmentPrototype[property] = properties[i - 1];\n        }\n      } else {\n        try {\n          createDocumentFragment().constructor.prototype[property] = properties[i - 1];\n        } catch(o_O) {}\n      }\n    }\n  }\n\n  // most likely an IE9 only issue\n  // see https://github.com/WebReflection/dom4/issues/6\n  if (!createElement('a').matches('a')) {\n    ElementPrototype[property] = function(matches){\n      return function (selector) {\n        return matches.call(\n          this.parentNode ?\n            this :\n            createDocumentFragment().appendChild(this),\n          selector\n        );\n      };\n    }(ElementPrototype[property]);\n  }\n\n  // used to fix both old webkit and SVG\n  DOMTokenList.prototype = {\n    length: 0,\n    add: function add() {\n      for(var j = 0, token; j < arguments.length; j++) {\n        token = arguments[j];\n        if(!this.contains(token)) {\n          properties.push.call(this, property);\n        }\n      }\n      if (this._isSVG) {\n        this._.setAttribute('class', '' + this);\n      } else {\n        this._.className = '' + this;\n      }\n    },\n    contains: (function(indexOf){\n      return function contains(token) {\n        i = indexOf.call(this, property = verifyToken(token));\n        return -1 < i;\n      };\n    }([].indexOf || function (token) {\n      i = this.length;\n      while(i-- && this[i] !== token){}\n      return i;\n    })),\n    item: function item(i) {\n      return this[i] || null;\n    },\n    remove: function remove() {\n      for(var j = 0, token; j < arguments.length; j++) {\n        token = arguments[j];\n        if(this.contains(token)) {\n          properties.splice.call(this, i, 1);\n        }\n      }\n      if (this._isSVG) {\n        this._.setAttribute('class', '' + this);\n      } else {\n        this._.className = '' + this;\n      }\n    },\n    toggle: toggle,\n    toString: function toString() {\n      return properties.join.call(this, SPACE);\n    }\n  };\n\n  if (SVGElement && !(CLASS_LIST in SVGElement.prototype)) {\n    defineProperty(SVGElement.prototype, CLASS_LIST, classListDescriptor);\n  }\n\n  // http://www.w3.org/TR/dom/#domtokenlist\n  // iOS 5.1 has completely screwed this property\n  // classList in ElementPrototype is false\n  // but it's actually there as getter\n  if (!(CLASS_LIST in document.documentElement)) {\n    defineProperty(ElementPrototype, CLASS_LIST, classListDescriptor);\n  } else {\n    // iOS 5.1 and Nokia ASHA do not support multiple add or remove\n    // trying to detect and fix that in here\n    TemporaryTokenList = createElement('div')[CLASS_LIST];\n    TemporaryTokenList.add('a', 'b', 'a');\n    if ('a\\x20b' != TemporaryTokenList) {\n      // no other way to reach original methods in iOS 5.1\n      TemporaryPrototype = TemporaryTokenList.constructor.prototype;\n      if (!('add' in TemporaryPrototype)) {\n        // ASHA double fails in here\n        TemporaryPrototype = window.TemporaryTokenList.prototype;\n      }\n      wrapVerifyToken = function (original) {\n        return function () {\n          var i = 0;\n          while (i < arguments.length) {\n            original.call(this, arguments[i++]);\n          }\n        };\n      };\n      TemporaryPrototype.add = wrapVerifyToken(TemporaryPrototype.add);\n      TemporaryPrototype.remove = wrapVerifyToken(TemporaryPrototype.remove);\n      // toggle is broken too ^_^ ... let's fix it\n      TemporaryPrototype.toggle = toggle;\n    }\n  }\n\n  if (!('contains' in NodePrototype)) {\n    defineProperty(NodePrototype, 'contains', {\n      value: function (el) {\n        while (el && el !== this) el = el.parentNode;\n        return this === el;\n      }\n    });\n  }\n\n  if (!('head' in document)) {\n    defineProperty(document, 'head', {\n      get: function () {\n        return head || (\n          head = document.getElementsByTagName('head')[0]\n        );\n      }\n    });\n  }\n\n  // requestAnimationFrame partial polyfill\n  (function () {\n    for (var\n      raf,\n      rAF = window.requestAnimationFrame,\n      cAF = window.cancelAnimationFrame,\n      prefixes = ['o', 'ms', 'moz', 'webkit'],\n      i = prefixes.length;\n      !cAF && i--;\n    ) {\n      rAF = rAF || window[prefixes[i] + 'RequestAnimationFrame'];\n      cAF = window[prefixes[i] + 'CancelAnimationFrame'] ||\n            window[prefixes[i] + 'CancelRequestAnimationFrame'];\n    }\n    if (!cAF) {\n      // some FF apparently implemented rAF but no cAF \n      if (rAF) {\n        raf = rAF;\n        rAF = function (callback) {\n          var goOn = true;\n          raf(function () {\n            if (goOn) callback.apply(this, arguments);\n          });\n          return function () {\n            goOn = false;\n          };\n        };\n        cAF = function (id) {\n          id();\n        };\n      } else {\n        rAF = function (callback) {\n          return setTimeout(callback, 15, 15);\n        };\n        cAF = function (id) {\n          clearTimeout(id);\n        };\n      }\n    }\n    window.requestAnimationFrame = rAF;\n    window.cancelAnimationFrame = cAF;\n  }());\n\n  // http://www.w3.org/TR/dom/#customevent\n  try{new window.CustomEvent('?');}catch(o_O){\n    window.CustomEvent = function(\n      eventName,\n      defaultInitDict\n    ){\n\n      // the infamous substitute\n      function CustomEvent(type, eventInitDict) {\n        /*jshint eqnull:true */\n        var event = document.createEvent(eventName);\n        if (typeof type != 'string') {\n          throw new Error('An event name must be provided');\n        }\n        if (eventName == 'Event') {\n          event.initCustomEvent = initCustomEvent;\n        }\n        if (eventInitDict == null) {\n          eventInitDict = defaultInitDict;\n        }\n        event.initCustomEvent(\n          type,\n          eventInitDict.bubbles,\n          eventInitDict.cancelable,\n          eventInitDict.detail\n        );\n        return event;\n      }\n\n      // attached at runtime\n      function initCustomEvent(\n        type, bubbles, cancelable, detail\n      ) {\n        /*jshint validthis:true*/\n        this.initEvent(type, bubbles, cancelable);\n        this.detail = detail;\n      }\n\n      // that's it\n      return CustomEvent;\n    }(\n      // is this IE9 or IE10 ?\n      // where CustomEvent is there\n      // but not usable as construtor ?\n      window.CustomEvent ?\n        // use the CustomEvent interface in such case\n        'CustomEvent' : 'Event',\n        // otherwise the common compatible one\n      {\n        bubbles: false,\n        cancelable: false,\n        detail: null\n      }\n    );\n  }\n\n  // window.Event as constructor\n  try { new Event('_'); } catch (o_O) {\n    /* jshint -W022 */\n    o_O = (function ($Event) {\n      function Event(type, init) {\n        enoughArguments(arguments.length, 'Event');\n        var out = document.createEvent('Event');\n        if (!init) init = {};\n        out.initEvent(\n          type,\n          !!init.bubbles,\n          !!init.cancelable\n        );\n        return out;\n      }\n      Event.prototype = $Event.prototype;\n      return Event;\n    }(window.Event || function Event() {}));\n    defineProperty(window, 'Event', {value: o_O});\n    // Android 4 gotcha\n    if (Event !== o_O) Event = o_O;\n  }\n\n  // window.KeyboardEvent as constructor\n  try { new KeyboardEvent('_', {}); } catch (o_O) {\n    /* jshint -W022 */\n    o_O = (function ($KeyboardEvent) {\n      // code inspired by https://gist.github.com/termi/4654819\n      var\n        initType = 0,\n        defaults = {\n          char: '',\n          key: '',\n          location: 0,\n          ctrlKey: false,\n          shiftKey: false,\n          altKey: false,\n          metaKey: false,\n          altGraphKey: false,\n          repeat: false,\n          locale: navigator.language,\n          detail: 0,\n          bubbles: false,\n          cancelable: false,\n          keyCode: 0,\n          charCode: 0,\n          which: 0\n        },\n        eventType\n      ;\n      try {\n        var e = document.createEvent('KeyboardEvent');\n        e.initKeyboardEvent(\n          'keyup', false, false, window, '+', 3,\n          true, false, true, false, false\n        );\n        initType = (\n          (e.keyIdentifier || e.key) == '+' &&\n          (e.keyLocation || e.location) == 3\n        ) && (\n          e.ctrlKey ? e.altKey ? 1 : 3 : e.shiftKey ? 2 : 4\n        ) || 9;\n      } catch(o_O) {}\n      eventType = 0 < initType ? 'KeyboardEvent' : 'Event';\n\n      function getModifier(init) {\n        for (var\n          out = [],\n          keys = [\n            'ctrlKey',\n            'Control',\n            'shiftKey',\n            'Shift',\n            'altKey',\n            'Alt',\n            'metaKey',\n            'Meta',\n            'altGraphKey',\n            'AltGraph'\n          ],\n          i = 0; i < keys.length; i += 2\n        ) {\n          if (init[keys[i]])\n            out.push(keys[i + 1]);\n        }\n        return out.join(' ');\n      }\n\n      function withDefaults(target, source) {\n        for (var key in source) {\n          if (\n            source.hasOwnProperty(key) &&\n            !source.hasOwnProperty.call(target, key)\n          ) target[key] = source[key];\n        }\n        return target;\n      }\n\n      function withInitValues(key, out, init) {\n        try {\n          out[key] = init[key];\n        } catch(o_O) {}\n      }\n\n      function KeyboardEvent(type, init) {\n        enoughArguments(arguments.length, 'KeyboardEvent');\n        init = withDefaults(init || {}, defaults);\n        var\n          out = document.createEvent(eventType),\n          ctrlKey = init.ctrlKey,\n          shiftKey = init.shiftKey,\n          altKey = init.altKey,\n          metaKey = init.metaKey,\n          altGraphKey = init.altGraphKey,\n          modifiers = initType > 3 ? getModifier(init) : null,\n          key = String(init.key),\n          chr = String(init.char),\n          location = init.location,\n          keyCode = init.keyCode || (\n            (init.keyCode = key) &&\n            key.charCodeAt(0)\n          ) || 0,\n          charCode = init.charCode || (\n            (init.charCode = chr) &&\n            chr.charCodeAt(0)\n          ) || 0,\n          bubbles = init.bubbles,\n          cancelable = init.cancelable,\n          repeat = init.repeat,\n          locale = init.locale,\n          view = init.view || window,\n          args\n        ;\n        if (!init.which) init.which = init.keyCode;\n        if ('initKeyEvent' in out) {\n          out.initKeyEvent(\n            type, bubbles, cancelable, view,\n            ctrlKey, altKey, shiftKey, metaKey, keyCode, charCode\n          );\n        } else if (0 < initType && 'initKeyboardEvent' in out) {\n          args = [type, bubbles, cancelable, view];\n          switch (initType) {\n            case 1:\n              args.push(key, location, ctrlKey, shiftKey, altKey, metaKey, altGraphKey);\n              break;\n            case 2:\n              args.push(ctrlKey, altKey, shiftKey, metaKey, keyCode, charCode);\n              break;\n            case 3:\n              args.push(key, location, ctrlKey, altKey, shiftKey, metaKey, altGraphKey);\n              break;\n            case 4:\n              args.push(key, location, modifiers, repeat, locale);\n              break;\n            default:\n              args.push(char, key, location, modifiers, repeat, locale);\n          }\n          out.initKeyboardEvent.apply(out, args);\n        } else {\n          out.initEvent(type, bubbles, cancelable);\n        }\n        for (key in out) {\n          if (defaults.hasOwnProperty(key) && out[key] !== init[key]) {\n            withInitValues(key, out, init);\n          }\n        }\n        return out;\n      }\n      KeyboardEvent.prototype = $KeyboardEvent.prototype;\n      return KeyboardEvent;\n    }(window.KeyboardEvent || function KeyboardEvent() {}));\n    defineProperty(window, 'KeyboardEvent', {value: o_O});\n    // Android 4 gotcha\n    if (KeyboardEvent !== o_O) KeyboardEvent = o_O;\n  }\n\n  // window.MouseEvent as constructor\n  try { new MouseEvent('_', {}); } catch (o_O) {\n    /* jshint -W022 */\n    o_O = (function ($MouseEvent) {\n      function MouseEvent(type, init) {\n        enoughArguments(arguments.length, 'MouseEvent');\n        var out = document.createEvent('MouseEvent');\n        if (!init) init = {};\n        out.initMouseEvent(\n          type,\n          !!init.bubbles,\n          !!init.cancelable,\n          init.view || window,\n          init.detail || 1,\n          init.screenX || 0,\n          init.screenY || 0,\n          init.clientX || 0,\n          init.clientY || 0,\n          !!init.ctrlKey,\n          !!init.altKey,\n          !!init.shiftKey,\n          !!init.metaKey,\n          init.button || 0,\n          init.relatedTarget || null\n        );\n        return out;\n      }\n      MouseEvent.prototype = $MouseEvent.prototype;\n      return MouseEvent;\n    }(window.MouseEvent || function MouseEvent() {}));\n    defineProperty(window, 'MouseEvent', {value: o_O});\n    // Android 4 gotcha\n    if (MouseEvent !== o_O) MouseEvent = o_O;\n  }\n\n  if (!document.querySelectorAll('*').forEach) {\n    (function () {\n      function patch(what) {\n        var querySelectorAll = what.querySelectorAll;\n        what.querySelectorAll = function qSA(css) {\n          var result = querySelectorAll.call(this, css);\n          result.forEach = Array.prototype.forEach;\n          return result;\n        };\n      }\n      patch(document);\n      patch(Element.prototype);\n    }());\n  }\n\n  try {\n    // https://drafts.csswg.org/selectors-4/#the-scope-pseudo\n    document.querySelector(':scope *');\n  } catch(o_O) {\n    (function () {\n      var dataScope = 'data-scope-' + (Math.random() * 1e9 >>> 0);\n      var proto = Element.prototype;\n      var querySelector = proto.querySelector;\n      var querySelectorAll = proto.querySelectorAll;\n      proto.querySelector = function qS(css) {\n        return find(this, querySelector, css);\n      };\n      proto.querySelectorAll = function qSA(css) {\n        return find(this, querySelectorAll, css);\n      };\n      function find(node, method, css) {\n        node.setAttribute(dataScope, null);\n        var result = method.call(\n          node,\n          String(css).replace(\n            /(^|,\\s*)(:scope([ >]|$))/g,\n            function ($0, $1, $2, $3) {\n              return $1 + '[' + dataScope + ']' + ($3 || ' ');\n            }\n          )\n        );\n        node.removeAttribute(dataScope);\n        return result;\n      }\n    }());\n  }\n}(window));\n(function (global){'use strict';\n\n  // a WeakMap fallback for DOM nodes only used as key\n  var DOMMap = global.WeakMap || (function () {\n\n    var\n      counter = 0,\n      dispatched = false,\n      drop = false,\n      value\n    ;\n\n    function dispatch(key, ce, shouldDrop) {\n      drop = shouldDrop;\n      dispatched = false;\n      value = undefined;\n      key.dispatchEvent(ce);\n    }\n\n    function Handler(value) {\n      this.value = value;\n    }\n\n    Handler.prototype.handleEvent = function handleEvent(e) {\n      dispatched = true;\n      if (drop) {\n        e.currentTarget.removeEventListener(e.type, this, false);\n      } else {\n        value = this.value;\n      }\n    };\n\n    function DOMMap() {\n      counter++;  // make id clashing highly improbable\n      this.__ce__ = new Event(('@DOMMap:' + counter) + Math.random());\n    }\n\n    DOMMap.prototype = {\n      'constructor': DOMMap,\n      'delete': function del(key) {\n        return dispatch(key, this.__ce__, true), dispatched;\n      },\n      'get': function get(key) {\n        dispatch(key, this.__ce__, false);\n        var v = value;\n        value = undefined;\n        return v;\n      },\n      'has': function has(key) {\n        return dispatch(key, this.__ce__, false), dispatched;\n      },\n      'set': function set(key, value) {\n        dispatch(key, this.__ce__, true);\n        key.addEventListener(this.__ce__.type, new Handler(value), false);\n        return this;\n      },\n    };\n\n    return DOMMap;\n\n  }());\n\n  function Dict() {}\n  Dict.prototype = (Object.create || Object)(null);\n\n  // https://dom.spec.whatwg.org/#interface-eventtarget\n\n  function createEventListener(type, callback, options) {\n    function eventListener(e) {\n      if (eventListener.once) {\n        e.currentTarget.removeEventListener(\n          e.type,\n          callback,\n          eventListener\n        );\n        eventListener.removed = true;\n      }\n      if (eventListener.passive) {\n        e.preventDefault = createEventListener.preventDefault;\n      }\n      if (typeof eventListener.callback === 'function') {\n        /* jshint validthis: true */\n        eventListener.callback.call(this, e);\n      } else if (eventListener.callback) {\n        eventListener.callback.handleEvent(e);\n      }\n      if (eventListener.passive) {\n        delete e.preventDefault;\n      }\n    }\n    eventListener.type = type;\n    eventListener.callback = callback;\n    eventListener.capture = !!options.capture;\n    eventListener.passive = !!options.passive;\n    eventListener.once = !!options.once;\n    // currently pointless but specs say to use it, so ...\n    eventListener.removed = false;\n    return eventListener;\n  }\n\n  createEventListener.preventDefault = function preventDefault() {};\n\n  var\n    Event = global.CustomEvent,\n    dE = global.dispatchEvent,\n    aEL = global.addEventListener,\n    rEL = global.removeEventListener,\n    counter = 0,\n    increment = function () { counter++; },\n    indexOf = [].indexOf || function indexOf(value){\n      var length = this.length;\n      while(length--) {\n        if (this[length] === value) {\n          break;\n        }\n      }\n      return length;\n    },\n    getListenerKey = function (options) {\n      return ''.concat(\n        options.capture ? '1' : '0',\n        options.passive ? '1' : '0',\n        options.once ? '1' : '0'\n      );\n    },\n    augment\n  ;\n\n  try {\n    aEL('_', increment, {once: true});\n    dE(new Event('_'));\n    dE(new Event('_'));\n    rEL('_', increment, {once: true});\n  } catch(o_O) {}\n\n  if (counter !== 1) {\n    (function () {\n      var dm = new DOMMap();\n      function createAEL(aEL) {\n        return function addEventListener(type, handler, options) {\n          if (options && typeof options !== 'boolean') {\n            var\n              info = dm.get(this),\n              key = getListenerKey(options),\n              i, tmp, wrap\n            ;\n            if (!info) dm.set(this, (info = new Dict()));\n            if (!(type in info)) info[type] = {\n              handler: [],\n              wrap: []\n            };\n            tmp = info[type];\n            i = indexOf.call(tmp.handler, handler);\n            if (i < 0) {\n              i = tmp.handler.push(handler) - 1;\n              tmp.wrap[i] = (wrap = new Dict());\n            } else {\n              wrap = tmp.wrap[i];\n            }\n            if (!(key in wrap)) {\n              wrap[key] = createEventListener(type, handler, options);\n              aEL.call(this, type, wrap[key], wrap[key].capture);\n            }\n          } else {\n            aEL.call(this, type, handler, options);\n          }\n        };\n      }\n      function createREL(rEL) {\n        return function removeEventListener(type, handler, options) {\n          if (options && typeof options !== 'boolean') {\n            var\n              info = dm.get(this),\n              key, i, tmp, wrap\n            ;\n            if (info && (type in info)) {\n              tmp = info[type];\n              i = indexOf.call(tmp.handler, handler);\n              if (-1 < i) {\n                key = getListenerKey(options);\n                wrap = tmp.wrap[i];\n                if (key in wrap) {\n                  rEL.call(this, type, wrap[key], wrap[key].capture);\n                  delete wrap[key];\n                  // return if there are other wraps\n                  for (key in wrap) return;\n                  // otherwise remove all the things\n                  tmp.handler.splice(i, 1);\n                  tmp.wrap.splice(i, 1);\n                  // if there are no other handlers\n                  if (tmp.handler.length === 0)\n                    // drop the info[type] entirely\n                    delete info[type];\n                }\n              }\n            }\n          } else {\n            rEL.call(this, type, handler, options);\n          }\n        };\n      }\n\n      augment = function (Constructor) {\n        if (!Constructor) return;\n        var proto = Constructor.prototype;\n        proto.addEventListener = createAEL(proto.addEventListener);\n        proto.removeEventListener = createREL(proto.removeEventListener);\n      };\n\n      if (global.EventTarget) {\n        augment(EventTarget);\n      } else {\n        augment(global.Text);\n        augment(global.Element || global.HTMLElement);\n        augment(global.HTMLDocument);\n        augment(global.Window || {prototype:global});\n        augment(global.XMLHttpRequest);\n      }\n\n    }());\n  }\n\n}(self));\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/garlicjs/dist/garlic.min.js":
/*!****************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/garlicjs/dist/garlic.min.js ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "!function(t){function e(n){if(i[n])return i[n].exports;var s=i[n]={i:n,l:!1,exports:{}};return t[n].call(s.exports,s,s.exports,e),s.l=!0,s.exports}var i={};e.m=t,e.c=i,e.d=function(t,i,n){e.o(t,i)||Object.defineProperty(t,i,{enumerable:!0,get:n})},e.r=function(t){\"undefined\"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:\"Module\"}),Object.defineProperty(t,\"__esModule\",{value:!0})},e.t=function(t,i){if(1&i&&(t=e(t)),8&i)return t;if(4&i&&\"object\"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(e.r(n),Object.defineProperty(n,\"default\",{enumerable:!0,value:t}),2&i&&\"string\"!=typeof t)for(var s in t)e.d(n,s,function(e){return t[e]}.bind(null,s));return n},e.n=function(t){var i=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(i,\"a\",i),i},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p=\"\",e(e.s=0)}([function(t,e){!function(t){\"use strict\";var e=function(t){this.defined=\"undefined\"!=typeof localStorage;var e=\"garlic:\"+document.domain+\">test\";try{localStorage.setItem(e,e),localStorage.removeItem(e)}catch(t){this.defined=!1}};e.prototype={constructor:e,get:function(t,e){var i=localStorage.getItem(t);if(i){try{i=JSON.parse(i)}catch(t){}return i}return void 0!==e?e:null},has:function(t){return!!localStorage.getItem(t)},set:function(t,e,i){return\"\"===e||e instanceof Array&&0===e.length?this.destroy(t):(e=JSON.stringify(e),localStorage.setItem(t,e)),\"function\"!=typeof i||i()},destroy:function(t,e){return localStorage.removeItem(t),\"function\"!=typeof e||e()},clean:function(t){for(var e=localStorage.length-1;e>=0;e--)void 0===Array.indexOf&&-1!==localStorage.key(e).indexOf(\"garlic:\")&&localStorage.removeItem(localStorage.key(e));return\"function\"!=typeof t||t()},clear:function(t){return localStorage.clear(),\"function\"!=typeof t||t()}};var i=function(t,e,i){this.init(\"garlic\",t,e,i)};i.prototype={constructor:i,init:function(e,i,n,s){this.type=e,this.$element=t(i),this.options=this.getOptions(s),this.storage=n,this.path=this.options.getPath(this.$element)||this.getPath(),this.parentForm=this.$element.closest(\"form\"),this.$element.addClass(\"garlic-auto-save\"),this.expiresFlag=!!this.options.expires&&(this.$element.data(\"expires\")?this.path:this.getPath(this.parentForm))+\"_flag\",this.$element.on(this.options.events.join(\".\"+this.type+\" \"),!1,t.proxy(this.persist,this)),this.options.destroy&&t(this.parentForm).on(\"submit reset\",!1,t.proxy(this.destroy,this)),this.retrieve()},getOptions:function(e){return t.extend({},t.fn[this.type].defaults,e,this.$element.data())},persist:function(){if(this.$element.is(\"input[type=radio], input[type=checkbox]\")||this.val!==this.getVal()){this.val=this.getVal(),this.options.expires&&this.storage.set(this.expiresFlag,((new Date).getTime()+1e3*this.options.expires).toString());var t=this.options.prePersist(this.$element,this.val);\"string\"==typeof t&&(this.val=t),this.storage.set(this.path,this.val),this.options.onPersist(this.$element,this.val)}},getVal:function(){return this.$element.is(\"input[type=checkbox]\")?this.$element.prop(\"checked\")?\"checked\":\"unchecked\":this.$element.val()},retrieve:function(){if(this.storage.has(this.path)){if(this.options.expires){var t=(new Date).getTime();if(this.storage.get(this.expiresFlag)<t.toString())return void this.storage.destroy(this.path);this.$element.attr(\"expires-in\",Math.floor((parseInt(this.storage.get(this.expiresFlag))-t)/1e3))}var e=this.$element.val(),i=this.storage.get(this.path);if(\"boolean\"==typeof(i=this.options.preRetrieve(this.$element,e,i))&&0==i)return;return this.options.conflictManager.enabled&&this.detectConflict()?this.conflictManager():this.$element.is(\"input[type=radio], input[type=checkbox]\")?\"checked\"===i||this.$element.val()===i?this.$element.prop(\"checked\",!0):void(\"unchecked\"===i&&this.$element.prop(\"checked\",!1)):(this.$element.val(i),this.$element.trigger(\"input\"),void this.options.onRetrieve(this.$element,i))}},detectConflict:function(){var e=this;if(this.$element.is(\"input[type=checkbox], input[type=radio]\"))return!1;if(this.$element.val()&&this.storage.get(this.path)!==this.$element.val()){if(this.$element.is(\"select\")){var i=!1;return this.$element.find(\"option\").each(function(){0!==t(this).index()&&t(this).attr(\"selected\")&&t(this).val()!==e.storage.get(this.path)&&(i=!0)}),i}return!0}return!1},conflictManager:function(){if(\"function\"==typeof this.options.conflictManager.onConflictDetected&&!this.options.conflictManager.onConflictDetected(this.$element,this.storage.get(this.path)))return!1;this.options.conflictManager.garlicPriority?(this.$element.data(\"swap-data\",this.$element.val()),this.$element.data(\"swap-state\",\"garlic\"),this.$element.val(this.storage.get(this.path))):(this.$element.data(\"swap-data\",this.storage.get(this.path)),this.$element.data(\"swap-state\",\"default\")),this.swapHandler(),this.$element.addClass(\"garlic-conflict-detected\"),this.$element.closest(\"input[type=submit]\").attr(\"disabled\",!0)},swapHandler:function(){var e=t(this.options.conflictManager.template);this.$element.after(e.text(this.options.conflictManager.message)),e.on(\"click\",!1,t.proxy(this.swap,this))},swap:function(){var e=this.$element.data(\"swap-data\");this.$element.data(\"swap-state\",\"garlic\"===this.$element.data(\"swap-state\")?\"default\":\"garlic\"),this.$element.data(\"swap-data\",this.$element.val()),t(this.$element).val(e),this.options.onSwap(this.$element,this.$element.data(\"swap-data\"),e)},destroy:function(){this.storage.destroy(this.path)},remove:function(){this.destroy(),this.$element.is(\"input[type=radio], input[type=checkbox]\")?t(this.$element).attr(\"checked\",!1):this.$element.val(\"\")},getPath:function(e){if(void 0===e&&(e=this.$element),this.options.getPath(e))return this.options.getPath(e);if(1!=e.length)return!1;for(var i=\"\",n=e.is(\"input[type=checkbox]\"),s=e;s.length;){var a=s[0],o=a.nodeName;if(!o)break;o=o.toLowerCase();var r=s.parent(),l=r.children(o);if(t(a).is(\"form, input, select, textarea\")||n){if(o+=t(a).attr(\"name\")?\".\"+t(a).attr(\"name\"):\"\",l.length>1&&!t(a).is(\"input[type=radio]\")&&(o+=\":eq(\"+l.index(a)+\")\"),i=o+(i?\">\"+i:\"\"),\"form\"==a.nodeName.toLowerCase())break;s=r}else s=r}return\"garlic:\"+document.domain+(this.options.domain?\"*\":window.location.pathname)+\">\"+i},getStorage:function(){return this.storage}},t.fn.garlic=function(n,s){function a(e){var s=t(e),a=s.data(\"garlic\"),l=t.extend({},o,s.data());if((void 0===l.storage||l.storage)&&\"password\"!==t(e).attr(\"type\"))return a||s.data(\"garlic\",a=new i(e,r,l)),\"string\"==typeof n&&\"function\"==typeof a[n]?a[n]():void 0}var o=t.extend(!0,{},t.fn.garlic.defaults,n,this.data()),r=new e,l=!1;return!!r.defined&&(this.each(function(){if(t(this).is(\"form\"))t(this).find(o.inputs).each(function(){t(this).is(o.excluded)||(l=a(t(this)))});else if(t(this).is(o.inputs)){if(t(this).is(o.excluded))return;l=a(t(this))}}),\"function\"==typeof s?s():l)},t.fn.garlic.Constructor=i,t.fn.garlic.defaults={destroy:!0,inputs:\"input, textarea, select\",excluded:'input[type=\"file\"], input[type=\"hidden\"], input[type=\"submit\"], input[type=\"reset\"], [data-persist=\"false\"]',events:[\"DOMAttrModified\",\"textInput\",\"input\",\"change\",\"click\",\"keypress\",\"paste\",\"focus\"],domain:!1,expires:!1,conflictManager:{enabled:!1,garlicPriority:!0,template:'<span class=\"garlic-swap\"></span>',message:\"This is your saved data. Click here to see default one\",onConflictDetected:function(t,e){return!0}},getPath:function(t){},preRetrieve:function(t,e,i){return i},onRetrieve:function(t,e){},prePersist:function(t,e){return!1},onPersist:function(t,e){},onSwap:function(t,e,i){}},t(window).on(\"load\",function(){t('[data-persist=\"garlic\"]').each(function(){t(this).garlic()})})}(window.jQuery||window.Zepto)}]);"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/da.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/da.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('da', {\n  defaultMessage: \"Indtast venligst en korrekt værdi.\",\n  type: {\n    email:        \"Indtast venligst en korrekt emailadresse.\",\n    url:          \"Indtast venligst en korrekt internetadresse.\",\n    number:       \"Indtast venligst et tal.\",\n    integer:      \"Indtast venligst et heltal.\",\n    digits:       \"Dette felt må kun bestå af tal.\",\n    alphanum:     \"Dette felt skal indeholde både tal og bogstaver.\"\n  },\n  notblank:       \"Dette felt må ikke være tomt.\",\n  required:       \"Dette felt er påkrævet.\",\n  pattern:        \"Ugyldig indtastning.\",\n  min:            \"Dette felt skal indeholde et tal som er større end eller lig med %s.\",\n  max:            \"Dette felt skal indeholde et tal som er mindre end eller lig med %s.\",\n  range:          \"Dette felt skal indeholde et tal mellem %s og %s.\",\n  minlength:      \"Indtast venligst mindst %s tegn.\",\n  maxlength:      \"Dette felt kan højst indeholde %s tegn.\",\n  length:         \"Længden af denne værdi er ikke korrekt. Værdien skal være mellem %s og %s tegn lang.\",\n  mincheck:       \"Vælg mindst %s muligheder.\",\n  maxcheck:       \"Vælg op til %s muligheder.\",\n  check:          \"Vælg mellem %s og %s muligheder.\",\n  equalto:        \"De to felter er ikke ens.\"\n});\n\nParsley.setLocale('da');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/de.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/de.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('de', {\n  defaultMessage: \"Die Eingabe scheint nicht korrekt zu sein.\",\n  type: {\n    email:        \"Die Eingabe muss eine gültige E-Mail-Adresse sein.\",\n    url:          \"Die Eingabe muss eine gültige URL sein.\",\n    number:       \"Die Eingabe muss eine Zahl sein.\",\n    integer:      \"Die Eingabe muss eine Zahl sein.\",\n    digits:       \"Die Eingabe darf nur Ziffern enthalten.\",\n    alphanum:     \"Die Eingabe muss alphanumerisch sein.\"\n  },\n  notblank:       \"Die Eingabe darf nicht leer sein.\",\n  required:       \"Dies ist ein Pflichtfeld.\",\n  pattern:        \"Die Eingabe scheint ungültig zu sein.\",\n  min:            \"Die Eingabe muss größer oder gleich %s sein.\",\n  max:            \"Die Eingabe muss kleiner oder gleich %s sein.\",\n  range:          \"Die Eingabe muss zwischen %s und %s liegen.\",\n  minlength:      \"Die Eingabe ist zu kurz. Es müssen mindestens %s Zeichen eingegeben werden.\",\n  maxlength:      \"Die Eingabe ist zu lang. Es dürfen höchstens %s Zeichen eingegeben werden.\",\n  length:         \"Die Länge der Eingabe ist ungültig. Es müssen zwischen %s und %s Zeichen eingegeben werden.\",\n  mincheck:       \"Wählen Sie mindestens %s Angaben aus.\",\n  maxcheck:       \"Wählen Sie maximal %s Angaben aus.\",\n  check:          \"Wählen Sie zwischen %s und %s Angaben.\",\n  equalto:        \"Dieses Feld muss dem anderen entsprechen.\"\n});\n\nParsley.setLocale('de');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/en.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/en.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// This is included with the Parsley library itself,\n// thus there is no use in adding it to your project.\n\n\nParsley.addMessages('en', {\n  defaultMessage: \"This value seems to be invalid.\",\n  type: {\n    email:        \"This value should be a valid email.\",\n    url:          \"This value should be a valid url.\",\n    number:       \"This value should be a valid number.\",\n    integer:      \"This value should be a valid integer.\",\n    digits:       \"This value should be digits.\",\n    alphanum:     \"This value should be alphanumeric.\"\n  },\n  notblank:       \"This value should not be blank.\",\n  required:       \"This value is required.\",\n  pattern:        \"This value seems to be invalid.\",\n  min:            \"This value should be greater than or equal to %s.\",\n  max:            \"This value should be lower than or equal to %s.\",\n  range:          \"This value should be between %s and %s.\",\n  minlength:      \"This value is too short. It should have %s characters or more.\",\n  maxlength:      \"This value is too long. It should have %s characters or fewer.\",\n  length:         \"This value length is invalid. It should be between %s and %s characters long.\",\n  mincheck:       \"You must select at least %s choices.\",\n  maxcheck:       \"You must select %s choices or fewer.\",\n  check:          \"You must select between %s and %s choices.\",\n  equalto:        \"This value should be the same.\",\n  euvatin:        \"It's not a valid VAT Identification Number.\",\n});\n\nParsley.setLocale('en');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/es.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/es.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// ParsleyConfig definition if not already set\n// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('es', {\n  defaultMessage: \"Este valor parece ser inválido.\",\n  type: {\n    email:        \"Este valor debe ser un correo válido.\",\n    url:          \"Este valor debe ser una URL válida.\",\n    number:       \"Este valor debe ser un número válido.\",\n    integer:      \"Este valor debe ser un número válido.\",\n    digits:       \"Este valor debe ser un dígito válido.\",\n    alphanum:     \"Este valor debe ser alfanumérico.\"\n  },\n  notblank:       \"Este valor no debe estar en blanco.\",\n  required:       \"Este valor es requerido.\",\n  pattern:        \"Este valor es incorrecto.\",\n  min:            \"Este valor no debe ser menor que %s.\",\n  max:            \"Este valor no debe ser mayor que %s.\",\n  range:          \"Este valor debe estar entre %s y %s.\",\n  minlength:      \"Este valor es muy corto. La longitud mínima es de %s caracteres.\",\n  maxlength:      \"Este valor es muy largo. La longitud máxima es de %s caracteres.\",\n  length:         \"La longitud de este valor debe estar entre %s y %s caracteres.\",\n  mincheck:       \"Debe seleccionar al menos %s opciones.\",\n  maxcheck:       \"Debe seleccionar %s opciones o menos.\",\n  check:          \"Debe seleccionar entre %s y %s opciones.\",\n  equalto:        \"Este valor debe ser idéntico.\"\n});\n\nParsley.setLocale('es');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/fi.extra.js":
/*!********************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/fi.extra.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('fi', {\n  dateiso: \"Sy&ouml;t&auml; oikea p&auml;iv&auml;m&auml;&auml;r&auml; (YYYY-MM-DD).\"\n});\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/fi.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/fi.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('fi', {\n  defaultMessage: \"Sy&ouml;tetty arvo on virheellinen.\",\n  type: {\n    email:        \"S&auml;hk&ouml;postiosoite on virheellinen.\",\n    url:          \"Url-osoite on virheellinen.\",\n    number:       \"Sy&ouml;t&auml; numero.\",\n    integer:      \"Sy&ouml;t&auml; kokonaisluku.\",\n    digits:       \"Sy&ouml;t&auml; ainoastaan numeroita.\",\n    alphanum:     \"Sy&ouml;t&auml; ainoastaan kirjaimia tai numeroita.\"\n  },\n  notblank:       \"T&auml;m&auml; kentt&auml;&auml; ei voi j&auml;tt&auml;&auml; tyhj&auml;ksi.\",\n  required:       \"T&auml;m&auml; kentt&auml; on pakollinen.\",\n  pattern:        \"Sy&ouml;tetty arvo on virheellinen.\",\n  min:            \"Sy&ouml;t&auml; arvo joka on yht&auml; suuri tai suurempi kuin %s.\",\n  max:            \"Sy&ouml;t&auml; arvo joka on pienempi tai yht&auml; suuri kuin %s.\",\n  range:          \"Sy&ouml;t&auml; arvo v&auml;lilt&auml;: %s-%s.\",\n  minlength:      \"Sy&ouml;tetyn arvon t&auml;ytyy olla v&auml;hint&auml;&auml;n %s merkki&auml; pitk&auml;.\",\n  maxlength:      \"Sy&ouml;tetty arvo saa olla enint&auml;&auml;n %s merkki&auml; pitk&auml;.\",\n  length:         \"Sy&ouml;tetyn arvon t&auml;ytyy olla v&auml;hint&auml;&auml;n %s ja enint&auml;&auml;n %s merkki&auml; pitk&auml;.\",\n  mincheck:       \"Valitse v&auml;hint&auml;&auml;n %s vaihtoehtoa.\",\n  maxcheck:       \"Valitse enint&auml;&auml;n %s vaihtoehtoa.\",\n  check:          \"Valitse %s-%s vaihtoehtoa.\",\n  equalto:        \"Salasanat eiv&auml;t t&auml;sm&auml;&auml;.\"\n});\n\nParsley.setLocale('fi');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/fr.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/fr.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('fr', {\n  defaultMessage: \"Cette valeur semble non valide.\",\n  type: {\n    email:        \"Cette valeur n'est pas une adresse email valide.\",\n    url:          \"Cette valeur n'est pas une URL valide.\",\n    number:       \"Cette valeur doit être un nombre.\",\n    integer:      \"Cette valeur doit être un entier.\",\n    digits:       \"Cette valeur doit être numérique.\",\n    alphanum:     \"Cette valeur doit être alphanumérique.\"\n  },\n  notblank:       \"Cette valeur ne peut pas être vide.\",\n  required:       \"Ce champ est requis.\",\n  pattern:        \"Cette valeur semble non valide.\",\n  min:            \"Cette valeur ne doit pas être inférieure à %s.\",\n  max:            \"Cette valeur ne doit pas excéder %s.\",\n  range:          \"Cette valeur doit être comprise entre %s et %s.\",\n  minlength:      \"Cette chaîne est trop courte. Elle doit avoir au minimum %s caractères.\",\n  maxlength:      \"Cette chaîne est trop longue. Elle doit avoir au maximum %s caractères.\",\n  length:         \"Cette valeur doit contenir entre %s et %s caractères.\",\n  mincheck:       \"Vous devez sélectionner au moins %s choix.\",\n  maxcheck:       \"Vous devez sélectionner %s choix maximum.\",\n  check:          \"Vous devez sélectionner entre %s et %s choix.\",\n  equalto:        \"Cette valeur devrait être identique.\"\n});\n\nParsley.setLocale('fr');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/he.extra.js":
/*!********************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/he.extra.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('he', {\n  dateiso: \"ערך זה צריך להיות תאריך בפורמט (YYYY-MM-DD).\"\n});\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/he.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/he.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('he', {\n  defaultMessage: \"נראה כי ערך זה אינו תקף.\",\n  type: {\n    email:        \"ערך זה צריך להיות כתובת אימייל.\",\n    url:          \"ערך זה צריך להיות URL תקף.\",\n    number:       \"ערך זה צריך להיות מספר.\",\n    integer:      \"ערך זה צריך להיות מספר שלם.\",\n    digits:       \"ערך זה צריך להיות ספרתי.\",\n    alphanum:     \"ערך זה צריך להיות אלפאנומרי.\"\n  },\n  notblank:       \"ערך זה אינו יכול להשאר ריק.\",\n  required:       \"ערך זה דרוש.\",\n  pattern:        \"נראה כי ערך זה אינו תקף.\",\n  min:            \"ערך זה צריך להיות לכל הפחות %s.\",\n  max:            \"ערך זה צריך להיות לכל היותר %s.\",\n  range:          \"ערך זה צריך להיות בין %s ל-%s.\",\n  minlength:      \"ערך זה קצר מידי. הוא צריך להיות לכל הפחות %s תווים.\",\n  maxlength:      \"ערך זה ארוך מידי. הוא צריך להיות לכל היותר %s תווים.\",\n  length:         \"ערך זה אינו באורך תקף. האורך צריך להיות בין %s ל-%s תווים.\",\n  mincheck:       \"אנא בחר לפחות %s אפשרויות.\",\n  maxcheck:       \"אנא בחר לכל היותר %s אפשרויות.\",\n  check:          \"אנא בחר בין %s ל-%s אפשרויות.\",\n  equalto:        \"ערך זה צריך להיות זהה.\"\n});\n\nParsley.setLocale('he');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/hu.extra.js":
/*!********************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/hu.extra.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('hu', {\n  dateiso:  \"A mező értéke csak érvényes dátum lehet (YYYY-MM-DD).\",\n  minwords: \"Minimum %s szó megadása szükséges.\",\n  maxwords: \"Maximum %s szó megadása engedélyezett.\",\n  words:    \"Minimum %s, maximum %s szó megadása szükséges.\",\n  gt:       \"A mező értéke nagyobb kell legyen.\",\n  gte:      \"A mező értéke nagyobb vagy egyenlő kell legyen.\",\n  lt:       \"A mező értéke kevesebb kell legyen.\",\n  lte:      \"A mező értéke kevesebb vagy egyenlő kell legyen.\",\n  notequalto: \"Az érték különböző kell legyen.\"\n});\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/hu.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/hu.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// This is included with the Parsley library itself,\n// thus there is no use in adding it to your project.\n\n\nParsley.addMessages('hu', {\n  defaultMessage: \"Érvénytelen mező.\",\n  type: {\n    email:        \"Érvénytelen email cím.\",\n    url:          \"Érvénytelen URL cím.\",\n    number:       \"Érvénytelen szám.\",\n    integer:      \"Érvénytelen egész szám.\",\n    digits:       \"Érvénytelen szám.\",\n    alphanum:     \"Érvénytelen alfanumerikus érték.\"\n  },\n  notblank:       \"Ez a mező nem maradhat üresen.\",\n  required:       \"A mező kitöltése kötelező.\",\n  pattern:        \"Érvénytelen érték.\",\n  min:            \"A mező értéke nagyobb vagy egyenlő kell legyen mint %s.\",\n  max:            \"A mező értéke kisebb vagy egyenlő kell legyen mint %s.\",\n  range:          \"A mező értéke %s és %s közé kell essen.\",\n  minlength:      \"Legalább %s karakter megadása szükséges.\",\n  maxlength:      \"Legfeljebb %s karakter megadása engedélyezett.\",\n  length:         \"Nem megfelelő karakterszám. Minimum %s, maximum %s karakter adható meg.\",\n  mincheck:       \"Legalább %s értéket kell kiválasztani.\",\n  maxcheck:       \"Maximum %s értéket lehet kiválasztani.\",\n  check:          \"Legalább %s, legfeljebb %s értéket kell kiválasztani.\",\n  equalto:        \"A mező értéke nem egyező.\"\n});\n\nParsley.setLocale('hu');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/it.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/it.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('it', {\n  defaultMessage: \"Questo valore sembra essere non valido.\",\n  type: {\n    email:        \"Questo valore deve essere un indirizzo email valido.\",\n    url:          \"Questo valore deve essere un URL valido.\",\n    number:       \"Questo valore deve essere un numero valido.\",\n    integer:      \"Questo valore deve essere un numero valido.\",\n    digits:       \"Questo valore deve essere di tipo numerico.\",\n    alphanum:     \"Questo valore deve essere di tipo alfanumerico.\"\n  },\n  notblank:       \"Questo valore non deve essere vuoto.\",\n  required:       \"Questo valore è richiesto.\",\n  pattern:        \"Questo valore non è corretto.\",\n  min:            \"Questo valore deve essere maggiore di %s.\",\n  max:            \"Questo valore deve essere minore di %s.\",\n  range:          \"Questo valore deve essere compreso tra %s e %s.\",\n  minlength:      \"Questo valore è troppo corto. La lunghezza minima è di %s caratteri.\",\n  maxlength:      \"Questo valore è troppo lungo. La lunghezza massima è di %s caratteri.\",\n  length:         \"La lunghezza di questo valore deve essere compresa fra %s e %s caratteri.\",\n  mincheck:       \"Devi scegliere almeno %s opzioni.\",\n  maxcheck:       \"Devi scegliere al più %s opzioni.\",\n  check:          \"Devi scegliere tra %s e %s opzioni.\",\n  equalto:        \"Questo valore deve essere identico.\",\n  euvatin:        \"Non è un codice IVA valido\",\n});\n\nParsley.setLocale('it');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/ja.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/ja.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('ja', {\n  defaultMessage: \"無効な値です。\",\n  type: {\n    email:        \"有効なメールアドレスを入力してください。\",\n    url:          \"有効なURLを入力してください。\",\n    number:       \"数値を入力してください。\",\n    integer:      \"整数を入力してください。\",\n    digits:       \"数字を入力してください。\",\n    alphanum:     \"英数字を入力してください。\"\n  },\n  notblank:       \"この値を入力してください\",\n  required:       \"この値は必須です。\",\n  pattern:        \"この値は無効です。\",\n  min:            \"%s 以上の値にしてください。\",\n  max:            \"%s 以下の値にしてください。\",\n  range:          \"%s から %s の値にしてください。\",\n  minlength:      \"%s 文字以上で入力してください。\",\n  maxlength:      \"%s 文字以下で入力してください。\",\n  length:         \"%s から %s 文字の間で入力してください。\",\n  mincheck:       \"%s 個以上選択してください。\",\n  maxcheck:       \"%s 個以下選択してください。\",\n  check:          \"%s から %s 個選択してください。\",\n  equalto:        \"値が違います。\"\n});\n\nParsley.setLocale('ja');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/nl.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/nl.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('nl', {\n  defaultMessage: \"Deze waarde lijkt onjuist.\",\n  type: {\n    email:        \"Dit lijkt geen geldig e-mail adres te zijn.\",\n    url:          \"Dit lijkt geen geldige URL te zijn.\",\n    number:       \"Deze waarde moet een nummer zijn.\",\n    integer:      \"Deze waarde moet een nummer zijn.\",\n    digits:       \"Deze waarde moet numeriek zijn.\",\n    alphanum:     \"Deze waarde moet alfanumeriek zijn.\"\n  },\n  notblank:       \"Deze waarde mag niet leeg zijn.\",\n  required:       \"Dit veld is verplicht.\",\n  pattern:        \"Deze waarde lijkt onjuist te zijn.\",\n  min:            \"Deze waarde mag niet lager zijn dan %s.\",\n  max:            \"Deze waarde mag niet groter zijn dan %s.\",\n  range:          \"Deze waarde moet tussen %s en %s liggen.\",\n  minlength:      \"Deze tekst is te kort. Deze moet uit minimaal %s karakters bestaan.\",\n  maxlength:      \"Deze waarde is te lang. Deze mag maximaal %s karakters lang zijn.\",\n  length:         \"Deze waarde moet tussen %s en %s karakters lang zijn.\",\n  equalto:        \"Deze waardes moeten identiek zijn.\"\n});\n\nParsley.setLocale('nl');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/no.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/no.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('no', {\n  defaultMessage: \"Verdien er ugyldig.\",\n  type: {\n    email:        \"Verdien må være en gyldig e-postadresse.\",\n    url:          \"Verdien må være en gyldig url.\",\n    number:       \"Verdien må være et gyldig tall.\",\n    integer:      \"Verdien må være et gyldig heltall.\",\n    digits:       \"Verdien må være et siffer.\",\n    alphanum:     \"Verdien må være alfanumerisk\"\n  },\n  notblank:       \"Verdien kan ikke være blank.\",\n  required:       \"Verdien er obligatorisk.\",\n  pattern:        \"Verdien er ugyldig.\",\n  min:            \"Verdien må være større eller lik %s.\",\n  max:            \"Verdien må være mindre eller lik %s.\",\n  range:          \"Verdien må være mellom %s and %s.\",\n  minlength:      \"Verdien er for kort. Den må bestå av minst %s tegn.\",\n  maxlength:      \"Verdien er for lang. Den kan bestå av maksimalt %s tegn.\",\n  length:         \"Verdien har ugyldig lengde. Den må være mellom %s og %s tegn lang.\",\n  mincheck:       \"Du må velge minst %s alternativer.\",\n  maxcheck:       \"Du må velge %s eller færre alternativer.\",\n  check:          \"Du må velge mellom %s og %s alternativer.\",\n  equalto:        \"Verdien må være lik.\"\n});\n\nParsley.setLocale('no');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/pl.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/pl.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('pl', {\n  defaultMessage: \"Wartość wygląda na nieprawidłową\",\n  type: {\n    email:        \"Wpisz poprawny adres e-mail.\",\n    url:          \"Wpisz poprawny adres URL.\",\n    number:       \"Wpisz poprawną liczbę.\",\n    integer:      \"Dozwolone są jedynie liczby całkowite.\",\n    digits:       \"Dozwolone są jedynie cyfry.\",\n    alphanum:     \"Dozwolone są jedynie znaki alfanumeryczne.\"\n  },\n  notblank:       \"Pole nie może być puste.\",\n  required:       \"Pole jest wymagane.\",\n  pattern:        \"Pole zawiera nieprawidłową wartość.\",\n  min:            \"Wartość nie może być mniejsza od %s.\",\n  max:            \"Wartość nie może być większa od %s.\",\n  range:          \"Wartość powinna zawierać się pomiędzy %s a %s.\",\n  minlength:      \"Minimalna ilość znaków wynosi %s.\",\n  maxlength:      \"Maksymalna ilość znaków wynosi %s.\",\n  length:         \"Ilość znaków wynosi od %s do %s.\",\n  mincheck:       \"Wybierz minimalnie %s opcji.\",\n  maxcheck:       \"Wybierz maksymalnie %s opcji.\",\n  check:          \"Wybierz od %s do %s opcji.\",\n  equalto:        \"Wartości nie są identyczne.\"\n});\n\nParsley.setLocale('pl');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/pt-br.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/pt-br.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('pt-br', {\n  defaultMessage: \"Este valor parece ser inválido.\",\n  type: {\n    email:        \"Este campo deve ser um email válido.\",\n    url:          \"Este campo deve ser um URL válida.\",\n    number:       \"Este campo deve ser um número válido.\",\n    integer:      \"Este campo deve ser um inteiro válido.\",\n    digits:       \"Este campo deve conter apenas dígitos.\",\n    alphanum:     \"Este campo deve ser alfa numérico.\"\n  },\n  notblank:       \"Este campo não pode ficar vazio.\",\n  required:       \"Este campo é obrigatório.\",\n  pattern:        \"Este campo parece estar inválido.\",\n  min:            \"Este campo deve ser maior ou igual a %s.\",\n  max:            \"Este campo deve ser menor ou igual a %s.\",\n  range:          \"Este campo deve estar entre %s e %s.\",\n  minlength:      \"Este campo é pequeno demais. Ele deveria ter %s caracteres ou mais.\",\n  maxlength:      \"Este campo é grande demais. Ele deveria ter %s caracteres ou menos.\",\n  length:         \"O tamanho deste campo é inválido. Ele deveria ter entre %s e %s caracteres.\",\n  mincheck:       \"Você deve escolher pelo menos %s opções.\",\n  maxcheck:       \"Você deve escolher %s opções ou mais\",\n  check:          \"Você deve escolher entre %s e %s opções.\",\n  equalto:        \"Este valor deveria ser igual.\"\n});\n\nParsley.setLocale('pt-br');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/pt-pt.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/pt-pt.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('pt-pt', {\n  defaultMessage: \"Este valor parece ser inválido.\",\n  type: {\n    email:        \"Este campo deve ser um email válido.\",\n    url:          \"Este campo deve ser um URL válido.\",\n    number:       \"Este campo deve ser um número válido.\",\n    integer:      \"Este campo deve ser um número inteiro válido.\",\n    digits:       \"Este campo deve conter apenas dígitos.\",\n    alphanum:     \"Este campo deve ser alfanumérico.\"\n  },\n  notblank:       \"Este campo não pode ficar vazio.\",\n  required:       \"Este campo é obrigatório.\",\n  pattern:        \"Este campo parece estar inválido.\",\n  min:            \"Este valor deve ser maior ou igual a %s.\",\n  max:            \"Este valor deve ser menor ou igual a %s.\",\n  range:          \"Este valor deve estar entre %s e %s.\",\n  minlength:      \"Este campo é pequeno demais. Deve ter %s caracteres ou mais.\",\n  maxlength:      \"Este campo é grande demais. Deve ter %s caracteres ou menos.\",\n  length:         \"O tamanho deste campo é inválido. Ele deveria ter entre %s e %s caracteres.\",\n  mincheck:       \"Escolha pelo menos %s opções.\",\n  maxcheck:       \"Escolha %s opções ou mais\",\n  check:          \"Escolha entre %s e %s opções.\",\n  equalto:        \"Este valor deveria ser igual.\"\n});\n\nParsley.setLocale('pt-pt');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/sl.extra.js":
/*!********************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/sl.extra.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('sl', {\n  dateiso:  \"Vnesite datum v ISO obliki (YYYY-MM-DD).\",\n  minwords: \"Vpis je prekratek. Vpisati morate najmnaj %s besed.\",\n  maxwords: \"Vpis je predolg. Vpišete lahko največ %s besed.\",\n  words:    \"Dolžina vpisa je napačna. Dolžina je lahko samo med %s in %s besed.\",\n  gt:       \"Vpisani podatek mora biti večji.\",\n  gte:      \"Vpisani podatek mora biti enak ali večji.\",\n  lt:       \"Vpisani podatek mora biti manjši.\",\n  lte:      \"Vpisani podatek mora biti enak ali manjši.\",\n  notequalto: \"Vpisana vrednost mora biti drugačna.\"\n});\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/sl.js":
/*!**************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/sl.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// This is included with the Parsley library itself,\n// thus there is no use in adding it to your project.\n\n\nParsley.addMessages('sl', {\n  defaultMessage: \"Podatek ne ustreza vpisnim kriterijem.\",\n  type: {\n    email:        \"Vpišite pravilen email.\",\n    url:          \"Vpišite pravilen url naslov.\",\n    number:       \"Vpišite številko.\",\n    integer:      \"Vpišite celo število brez decimalnih mest.\",\n    digits:       \"Vpišite samo cifre.\",\n    alphanum:     \"Vpišite samo alfanumerične znake (cifre in črke).\"\n  },\n  notblank:       \"To polje ne sme biti prazno.\",\n  required:       \"To polje je obvezno.\",\n  pattern:        \"Podatek ne ustreza vpisnim kriterijem.\",\n  min:            \"Vrednost mora biti višja ali enaka kot %s.\",\n  max:            \"Vrednost mora biti nižja ali enaka kot  %s.\",\n  range:          \"Vrednost mora biti med %s in %s.\",\n  minlength:      \"Vpis je prekratek. Mora imeti najmanj %s znakov.\",\n  maxlength:      \"Vpis je predolg. Lahko ima največ %s znakov.\",\n  length:         \"Število vpisanih znakov je napačno. Število znakov je lahko samo med %s in %s.\",\n  mincheck:       \"Izbrati morate vsaj %s možnosti.\",\n  maxcheck:       \"Izberete lahko največ %s možnosti.\",\n  check:          \"Število izbranih možnosti je lahko samo med %s in %s.\",\n  equalto:        \"Vnos mora biti enak.\"\n});\n\nParsley.setLocale('sl');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/zh_cn.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/zh_cn.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "// Validation errors messages for Parsley\n// Load this after Parsley\n\nParsley.addMessages('zh-cn', {\n  defaultMessage: \"不正确的值\",\n  type: {\n    email:        \"请输入一个有效的电子邮箱地址\",\n    url:          \"请输入一个有效的链接\",\n    number:       \"请输入正确的数字\",\n    integer:      \"请输入正确的整数\",\n    digits:       \"请输入正确的号码\",\n    alphanum:     \"请输入字母或数字\"\n  },\n  notblank:       \"请输入值\",\n  required:       \"必填项\",\n  pattern:        \"格式不正确\",\n  min:            \"输入值请大于或等于 %s\",\n  max:            \"输入值请小于或等于 %s\",\n  range:          \"输入值应该在 %s 到 %s 之间\",\n  minlength:      \"请输入至少 %s 个字符\",\n  maxlength:      \"请输入至多 %s 个字符\",\n  length:         \"字符长度应该在 %s 到 %s 之间\",\n  mincheck:       \"请至少选择 %s 个选项\",\n  maxcheck:       \"请选择不超过 %s 个选项\",\n  check:          \"请选择 %s 到 %s 个选项\",\n  equalto:        \"输入值不同\"\n});\n\nParsley.setLocale('zh-cn');\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/parsley.min.js":
/*!******************************************************************************!*\
  !*** ./node_modules/raw-loader!./node_modules/parsleyjs/dist/parsley.min.js ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "!function(t,e){\"object\"==typeof exports&&\"undefined\"!=typeof module?module.exports=e(require(\"jquery\")):\"function\"==typeof define&&define.amd?define([\"jquery\"],e):t.parsley=e(t.jQuery)}(this,function(h){\"use strict\";function r(t){return(r=\"function\"==typeof Symbol&&\"symbol\"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&\"function\"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?\"symbol\":typeof t})(t)}function o(){return(o=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t}).apply(this,arguments)}function l(t,e){return function(t){if(Array.isArray(t))return t}(t)||function(t,e){var i=[],n=!0,r=!1,s=void 0;try{for(var a,o=t[Symbol.iterator]();!(n=(a=o.next()).done)&&(i.push(a.value),!e||i.length!==e);n=!0);}catch(t){r=!0,s=t}finally{try{n||null==o.return||o.return()}finally{if(r)throw s}}return i}(t,e)||function(){throw new TypeError(\"Invalid attempt to destructure non-iterable instance\")}()}function u(t){return function(t){if(Array.isArray(t)){for(var e=0,i=new Array(t.length);e<t.length;e++)i[e]=t[e];return i}}(t)||function(t){if(Symbol.iterator in Object(t)||\"[object Arguments]\"===Object.prototype.toString.call(t))return Array.from(t)}(t)||function(){throw new TypeError(\"Invalid attempt to spread non-iterable instance\")}()}var i,t=1,e={},d={attr:function(t,e,i){var n,r,s,a=new RegExp(\"^\"+e,\"i\");if(void 0===i)i={};else for(n in i)i.hasOwnProperty(n)&&delete i[n];if(!t)return i;for(n=(s=t.attributes).length;n--;)(r=s[n])&&r.specified&&a.test(r.name)&&(i[this.camelize(r.name.slice(e.length))]=this.deserializeValue(r.value));return i},checkAttr:function(t,e,i){return t.hasAttribute(e+i)},setAttr:function(t,e,i,n){t.setAttribute(this.dasherize(e+i),String(n))},getType:function(t){return t.getAttribute(\"type\")||\"text\"},generateID:function(){return\"\"+t++},deserializeValue:function(e){var t;try{return e?\"true\"==e||\"false\"!=e&&(\"null\"==e?null:isNaN(t=Number(e))?/^[\\[\\{]/.test(e)?JSON.parse(e):e:t):e}catch(t){return e}},camelize:function(t){return t.replace(/-+(.)?/g,function(t,e){return e?e.toUpperCase():\"\"})},dasherize:function(t){return t.replace(/::/g,\"/\").replace(/([A-Z]+)([A-Z][a-z])/g,\"$1_$2\").replace(/([a-z\\d])([A-Z])/g,\"$1_$2\").replace(/_/g,\"-\").toLowerCase()},warn:function(){var t;window.console&&\"function\"==typeof window.console.warn&&(t=window.console).warn.apply(t,arguments)},warnOnce:function(t){e[t]||(e[t]=!0,this.warn.apply(this,arguments))},_resetWarnings:function(){e={}},trimString:function(t){return t.replace(/^\\s+|\\s+$/g,\"\")},parse:{date:function(t){var e=t.match(/^(\\d{4,})-(\\d\\d)-(\\d\\d)$/);if(!e)return null;var i=l(e.map(function(t){return parseInt(t,10)}),4),n=(i[0],i[1]),r=i[2],s=i[3],a=new Date(n,r-1,s);return a.getFullYear()!==n||a.getMonth()+1!==r||a.getDate()!==s?null:a},string:function(t){return t},integer:function(t){return isNaN(t)?null:parseInt(t,10)},number:function(t){if(isNaN(t))throw null;return parseFloat(t)},boolean:function(t){return!/^\\s*false\\s*$/i.test(t)},object:function(t){return d.deserializeValue(t)},regexp:function(t){var e=\"\";return t=/^\\/.*\\/(?:[gimy]*)$/.test(t)?(e=t.replace(/.*\\/([gimy]*)$/,\"$1\"),t.replace(new RegExp(\"^/(.*?)/\"+e+\"$\"),\"$1\")):\"^\"+t+\"$\",new RegExp(t,e)}},parseRequirement:function(t,e){var i=this.parse[t||\"string\"];if(!i)throw'Unknown requirement specification: \"'+t+'\"';var n=i(e);if(null===n)throw\"Requirement is not a \".concat(t,': \"').concat(e,'\"');return n},namespaceEvents:function(t,e){return(t=this.trimString(t||\"\").split(/\\s+/))[0]?h.map(t,function(t){return\"\".concat(t,\".\").concat(e)}).join(\" \"):\"\"},difference:function(t,i){var n=[];return h.each(t,function(t,e){-1==i.indexOf(e)&&n.push(e)}),n},all:function(t){return h.when.apply(h,u(t).concat([42,42]))},objectCreate:Object.create||(i=function(){},function(t){if(1<arguments.length)throw Error(\"Second argument not supported\");if(\"object\"!=r(t))throw TypeError(\"Argument must be an object\");i.prototype=t;var e=new i;return i.prototype=null,e}),_SubmitSelector:'input[type=\"submit\"], button:submit'},n={namespace:\"data-parsley-\",inputs:\"input, textarea, select\",excluded:\"input[type=button], input[type=submit], input[type=reset], input[type=hidden]\",priorityEnabled:!0,multiple:null,group:null,uiEnabled:!0,validationThreshold:3,focus:\"first\",trigger:!1,triggerAfterFailure:\"input\",errorClass:\"parsley-error\",successClass:\"parsley-success\",classHandler:function(t){},errorsContainer:function(t){},errorsWrapper:'<ul class=\"parsley-errors-list\"></ul>',errorTemplate:\"<li></li>\"},s=function(){this.__id__=d.generateID()};s.prototype={asyncSupport:!0,_pipeAccordingToValidationResult:function(){var e=this,t=function(){var t=h.Deferred();return!0!==e.validationResult&&t.reject(),t.resolve().promise()};return[t,t]},actualizeOptions:function(){return d.attr(this.element,this.options.namespace,this.domOptions),this.parent&&this.parent.actualizeOptions&&this.parent.actualizeOptions(),this},_resetOptions:function(t){for(var e in this.domOptions=d.objectCreate(this.parent.options),this.options=d.objectCreate(this.domOptions),t)t.hasOwnProperty(e)&&(this.options[e]=t[e]);this.actualizeOptions()},_listeners:null,on:function(t,e){return this._listeners=this._listeners||{},(this._listeners[t]=this._listeners[t]||[]).push(e),this},subscribe:function(t,e){h.listenTo(this,t.toLowerCase(),e)},off:function(t,e){var i=this._listeners&&this._listeners[t];if(i)if(e)for(var n=i.length;n--;)i[n]===e&&i.splice(n,1);else delete this._listeners[t];return this},unsubscribe:function(t,e){h.unsubscribeTo(this,t.toLowerCase())},trigger:function(t,e,i){e=e||this;var n,r=this._listeners&&this._listeners[t];if(r)for(var s=r.length;s--;)if(!1===(n=r[s].call(e,e,i)))return n;return!this.parent||this.parent.trigger(t,e,i)},asyncIsValid:function(t,e){return d.warnOnce(\"asyncIsValid is deprecated; please use whenValid instead\"),this.whenValid({group:t,force:e})},_findRelated:function(){return this.options.multiple?h(this.parent.element.querySelectorAll(\"[\".concat(this.options.namespace,'multiple=\"').concat(this.options.multiple,'\"]'))):this.$element}};var c=function(t){h.extend(!0,this,t)};c.prototype={validate:function(t,e){if(this.fn)return 3<arguments.length&&(e=[].slice.call(arguments,1,-1)),this.fn(t,e);if(Array.isArray(t)){if(!this.validateMultiple)throw\"Validator `\"+this.name+\"` does not handle multiple values\";return this.validateMultiple.apply(this,arguments)}var i=arguments[arguments.length-1];if(this.validateDate&&i._isDateInput())return null!==(t=d.parse.date(t))&&this.validateDate.apply(this,arguments);if(this.validateNumber)return!t||!isNaN(t)&&(t=parseFloat(t),this.validateNumber.apply(this,arguments));if(this.validateString)return this.validateString.apply(this,arguments);throw\"Validator `\"+this.name+\"` only handles multiple values\"},parseRequirements:function(t,e){if(\"string\"!=typeof t)return Array.isArray(t)?t:[t];var i=this.requirementType;if(Array.isArray(i)){for(var n=function(t,e){var i=t.match(/^\\s*\\[(.*)\\]\\s*$/);if(!i)throw'Requirement is not an array: \"'+t+'\"';var n=i[1].split(\",\").map(d.trimString);if(n.length!==e)throw\"Requirement has \"+n.length+\" values when \"+e+\" are needed\";return n}(t,i.length),r=0;r<n.length;r++)n[r]=d.parseRequirement(i[r],n[r]);return n}return h.isPlainObject(i)?function(t,e,i){var n=null,r={};for(var s in t)if(s){var a=i(s);\"string\"==typeof a&&(a=d.parseRequirement(t[s],a)),r[s]=a}else n=d.parseRequirement(t[s],e);return[n,r]}(i,t,e):[d.parseRequirement(i,t)]},requirementType:\"string\",priority:2};var a=function(t,e){this.__class__=\"ValidatorRegistry\",this.locale=\"en\",this.init(t||{},e||{})},p={email:/^((([a-zA-Z]|\\d|[!#\\$%&'\\*\\+\\-\\/=\\?\\^_`{\\|}~]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])+(\\.([a-zA-Z]|\\d|[!#\\$%&'\\*\\+\\-\\/=\\?\\^_`{\\|}~]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])+)*)|((\\x22)((((\\x20|\\x09)*(\\x0d\\x0a))?(\\x20|\\x09)+)?(([\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x7f]|\\x21|[\\x23-\\x5b]|[\\x5d-\\x7e]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|(\\\\([\\x01-\\x09\\x0b\\x0c\\x0d-\\x7f]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF]))))*(((\\x20|\\x09)*(\\x0d\\x0a))?(\\x20|\\x09)+)?(\\x22)))@((([a-zA-Z]|\\d|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|(([a-zA-Z]|\\d|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])([a-zA-Z]|\\d|-|_|~|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])*([a-zA-Z]|\\d|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])))\\.)+(([a-zA-Z]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|(([a-zA-Z]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])([a-zA-Z]|\\d|-|_|~|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])*([a-zA-Z]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])))$/,number:/^-?(\\d*\\.)?\\d+(e[-+]?\\d+)?$/i,integer:/^-?\\d+$/,digits:/^\\d+$/,alphanum:/^\\w+$/i,date:{test:function(t){return null!==d.parse.date(t)}},url:new RegExp(\"^(?:(?:https?|ftp)://)?(?:\\\\S+(?::\\\\S*)?@)?(?:(?:[1-9]\\\\d?|1\\\\d\\\\d|2[01]\\\\d|22[0-3])(?:\\\\.(?:1?\\\\d{1,2}|2[0-4]\\\\d|25[0-5])){2}(?:\\\\.(?:[1-9]\\\\d?|1\\\\d\\\\d|2[0-4]\\\\d|25[0-4]))|(?:(?:[a-zA-Z\\\\u00a1-\\\\uffff0-9]-*)*[a-zA-Z\\\\u00a1-\\\\uffff0-9]+)(?:\\\\.(?:[a-zA-Z\\\\u00a1-\\\\uffff0-9]-*)*[a-zA-Z\\\\u00a1-\\\\uffff0-9]+)*(?:\\\\.(?:[a-zA-Z\\\\u00a1-\\\\uffff]{2,})))(?::\\\\d{2,5})?(?:/\\\\S*)?$\")};p.range=p.number;var f=function(t){var e=(\"\"+t).match(/(?:\\.(\\d+))?(?:[eE]([+-]?\\d+))?$/);return e?Math.max(0,(e[1]?e[1].length:0)-(e[2]?+e[2]:0)):0},m=function(s,a){return function(t){for(var e=arguments.length,i=new Array(1<e?e-1:0),n=1;n<e;n++)i[n-1]=arguments[n];return i.pop(),a.apply(void 0,[t].concat(u((r=s,i.map(d.parse[r])))));var r}},g=function(t){return{validateDate:m(\"date\",t),validateNumber:m(\"number\",t),requirementType:t.length<=2?\"string\":[\"string\",\"string\"],priority:30}};a.prototype={init:function(t,e){for(var i in this.catalog=e,this.validators=o({},this.validators),t)this.addValidator(i,t[i].fn,t[i].priority);window.Parsley.trigger(\"parsley:validator:init\")},setLocale:function(t){if(void 0===this.catalog[t])throw new Error(t+\" is not available in the catalog\");return this.locale=t,this},addCatalog:function(t,e,i){return\"object\"===r(e)&&(this.catalog[t]=e),!0===i?this.setLocale(t):this},addMessage:function(t,e,i){return void 0===this.catalog[t]&&(this.catalog[t]={}),this.catalog[t][e]=i,this},addMessages:function(t,e){for(var i in e)this.addMessage(t,i,e[i]);return this},addValidator:function(t,e,i){if(this.validators[t])d.warn('Validator \"'+t+'\" is already defined.');else if(n.hasOwnProperty(t))return void d.warn('\"'+t+'\" is a restricted keyword and is not a valid validator name.');return this._setValidator.apply(this,arguments)},hasValidator:function(t){return!!this.validators[t]},updateValidator:function(t,e,i){return this.validators[t]?this._setValidator.apply(this,arguments):(d.warn('Validator \"'+t+'\" is not already defined.'),this.addValidator.apply(this,arguments))},removeValidator:function(t){return this.validators[t]||d.warn('Validator \"'+t+'\" is not defined.'),delete this.validators[t],this},_setValidator:function(t,e,i){for(var n in\"object\"!==r(e)&&(e={fn:e,priority:i}),e.validate||(e=new c(e)),(this.validators[t]=e).messages||{})this.addMessage(n,t,e.messages[n]);return this},getErrorMessage:function(t){var e;\"type\"===t.name?e=(this.catalog[this.locale][t.name]||{})[t.requirements]:e=this.formatMessage(this.catalog[this.locale][t.name],t.requirements);return e||this.catalog[this.locale].defaultMessage||this.catalog.en.defaultMessage},formatMessage:function(t,e){if(\"object\"!==r(e))return\"string\"==typeof t?t.replace(/%s/i,e):\"\";for(var i in e)t=this.formatMessage(t,e[i]);return t},validators:{notblank:{validateString:function(t){return/\\S/.test(t)},priority:2},required:{validateMultiple:function(t){return 0<t.length},validateString:function(t){return/\\S/.test(t)},priority:512},type:{validateString:function(t,e){var i=2<arguments.length&&void 0!==arguments[2]?arguments[2]:{},n=i.step,r=void 0===n?\"any\":n,s=i.base,a=void 0===s?0:s,o=p[e];if(!o)throw new Error(\"validator type `\"+e+\"` is not supported\");if(!t)return!0;if(!o.test(t))return!1;if(\"number\"===e&&!/^any$/i.test(r||\"\")){var l=Number(t),u=Math.max(f(r),f(a));if(f(l)>u)return!1;var d=function(t){return Math.round(t*Math.pow(10,u))};if((d(l)-d(a))%d(r)!=0)return!1}return!0},requirementType:{\"\":\"string\",step:\"string\",base:\"number\"},priority:256},pattern:{validateString:function(t,e){return!t||e.test(t)},requirementType:\"regexp\",priority:64},minlength:{validateString:function(t,e){return!t||t.length>=e},requirementType:\"integer\",priority:30},maxlength:{validateString:function(t,e){return t.length<=e},requirementType:\"integer\",priority:30},length:{validateString:function(t,e,i){return!t||t.length>=e&&t.length<=i},requirementType:[\"integer\",\"integer\"],priority:30},mincheck:{validateMultiple:function(t,e){return t.length>=e},requirementType:\"integer\",priority:30},maxcheck:{validateMultiple:function(t,e){return t.length<=e},requirementType:\"integer\",priority:30},check:{validateMultiple:function(t,e,i){return t.length>=e&&t.length<=i},requirementType:[\"integer\",\"integer\"],priority:30},min:g(function(t,e){return e<=t}),max:g(function(t,e){return t<=e}),range:g(function(t,e,i){return e<=t&&t<=i}),equalto:{validateString:function(t,e){if(!t)return!0;var i=h(e);return i.length?t===i.val():t===e},priority:256},euvatin:{validateString:function(t,e){if(!t)return!0;return/^[A-Z][A-Z][A-Za-z0-9 -]{2,}$/.test(t)},priority:30}}};var v={};v.Form={_actualizeTriggers:function(){var e=this;this.$element.on(\"submit.Parsley\",function(t){e.onSubmitValidate(t)}),this.$element.on(\"click.Parsley\",d._SubmitSelector,function(t){e.onSubmitButton(t)}),!1!==this.options.uiEnabled&&this.element.setAttribute(\"novalidate\",\"\")},focus:function(){if(!(this._focusedField=null)===this.validationResult||\"none\"===this.options.focus)return null;for(var t=0;t<this.fields.length;t++){var e=this.fields[t];if(!0!==e.validationResult&&0<e.validationResult.length&&void 0===e.options.noFocus&&(this._focusedField=e.$element,\"first\"===this.options.focus))break}return null===this._focusedField?null:this._focusedField.focus()},_destroyUI:function(){this.$element.off(\".Parsley\")}},v.Field={_reflowUI:function(){if(this._buildUI(),this._ui){var t=function t(e,i,n){for(var r=[],s=[],a=0;a<e.length;a++){for(var o=!1,l=0;l<i.length;l++)if(e[a].assert.name===i[l].assert.name){o=!0;break}o?s.push(e[a]):r.push(e[a])}return{kept:s,added:r,removed:n?[]:t(i,e,!0).added}}(this.validationResult,this._ui.lastValidationResult);this._ui.lastValidationResult=this.validationResult,this._manageStatusClass(),this._manageErrorsMessages(t),this._actualizeTriggers(),!t.kept.length&&!t.added.length||this._failedOnce||(this._failedOnce=!0,this._actualizeTriggers())}},getErrorsMessages:function(){if(!0===this.validationResult)return[];for(var t=[],e=0;e<this.validationResult.length;e++)t.push(this.validationResult[e].errorMessage||this._getErrorMessage(this.validationResult[e].assert));return t},addError:function(t){var e=1<arguments.length&&void 0!==arguments[1]?arguments[1]:{},i=e.message,n=e.assert,r=e.updateClass,s=void 0===r||r;this._buildUI(),this._addError(t,{message:i,assert:n}),s&&this._errorClass()},updateError:function(t){var e=1<arguments.length&&void 0!==arguments[1]?arguments[1]:{},i=e.message,n=e.assert,r=e.updateClass,s=void 0===r||r;this._buildUI(),this._updateError(t,{message:i,assert:n}),s&&this._errorClass()},removeError:function(t){var e=(1<arguments.length&&void 0!==arguments[1]?arguments[1]:{}).updateClass,i=void 0===e||e;this._buildUI(),this._removeError(t),i&&this._manageStatusClass()},_manageStatusClass:function(){this.hasConstraints()&&this.needsValidation()&&!0===this.validationResult?this._successClass():0<this.validationResult.length?this._errorClass():this._resetClass()},_manageErrorsMessages:function(t){if(void 0===this.options.errorsMessagesDisabled){if(void 0!==this.options.errorMessage)return t.added.length||t.kept.length?(this._insertErrorWrapper(),0===this._ui.$errorsWrapper.find(\".parsley-custom-error-message\").length&&this._ui.$errorsWrapper.append(h(this.options.errorTemplate).addClass(\"parsley-custom-error-message\")),this._ui.$errorsWrapper.addClass(\"filled\").find(\".parsley-custom-error-message\").html(this.options.errorMessage)):this._ui.$errorsWrapper.removeClass(\"filled\").find(\".parsley-custom-error-message\").remove();for(var e=0;e<t.removed.length;e++)this._removeError(t.removed[e].assert.name);for(e=0;e<t.added.length;e++)this._addError(t.added[e].assert.name,{message:t.added[e].errorMessage,assert:t.added[e].assert});for(e=0;e<t.kept.length;e++)this._updateError(t.kept[e].assert.name,{message:t.kept[e].errorMessage,assert:t.kept[e].assert})}},_addError:function(t,e){var i=e.message,n=e.assert;this._insertErrorWrapper(),this._ui.$errorClassHandler.attr(\"aria-describedby\",this._ui.errorsWrapperId),this._ui.$errorsWrapper.addClass(\"filled\").append(h(this.options.errorTemplate).addClass(\"parsley-\"+t).html(i||this._getErrorMessage(n)))},_updateError:function(t,e){var i=e.message,n=e.assert;this._ui.$errorsWrapper.addClass(\"filled\").find(\".parsley-\"+t).html(i||this._getErrorMessage(n))},_removeError:function(t){this._ui.$errorClassHandler.removeAttr(\"aria-describedby\"),this._ui.$errorsWrapper.removeClass(\"filled\").find(\".parsley-\"+t).remove()},_getErrorMessage:function(t){var e=t.name+\"Message\";return void 0!==this.options[e]?window.Parsley.formatMessage(this.options[e],t.requirements):window.Parsley.getErrorMessage(t)},_buildUI:function(){if(!this._ui&&!1!==this.options.uiEnabled){var t={};this.element.setAttribute(this.options.namespace+\"id\",this.__id__),t.$errorClassHandler=this._manageClassHandler(),t.errorsWrapperId=\"parsley-id-\"+(this.options.multiple?\"multiple-\"+this.options.multiple:this.__id__),t.$errorsWrapper=h(this.options.errorsWrapper).attr(\"id\",t.errorsWrapperId),t.lastValidationResult=[],t.validationInformationVisible=!1,this._ui=t}},_manageClassHandler:function(){if(\"string\"==typeof this.options.classHandler&&h(this.options.classHandler).length)return h(this.options.classHandler);var t=this.options.classHandler;if(\"string\"==typeof this.options.classHandler&&\"function\"==typeof window[this.options.classHandler]&&(t=window[this.options.classHandler]),\"function\"==typeof t){var e=t.call(this,this);if(void 0!==e&&e.length)return e}else{if(\"object\"===r(t)&&t instanceof jQuery&&t.length)return t;t&&d.warn(\"The class handler `\"+t+\"` does not exist in DOM nor as a global JS function\")}return this._inputHolder()},_inputHolder:function(){return this.options.multiple&&\"SELECT\"!==this.element.nodeName?this.$element.parent():this.$element},_insertErrorWrapper:function(){var t=this.options.errorsContainer;if(0!==this._ui.$errorsWrapper.parent().length)return this._ui.$errorsWrapper.parent();if(\"string\"==typeof t){if(h(t).length)return h(t).append(this._ui.$errorsWrapper);\"function\"==typeof window[t]?t=window[t]:d.warn(\"The errors container `\"+t+\"` does not exist in DOM nor as a global JS function\")}return\"function\"==typeof t&&(t=t.call(this,this)),\"object\"===r(t)&&t.length?t.append(this._ui.$errorsWrapper):this._inputHolder().after(this._ui.$errorsWrapper)},_actualizeTriggers:function(){var t,e=this,i=this._findRelated();i.off(\".Parsley\"),this._failedOnce?i.on(d.namespaceEvents(this.options.triggerAfterFailure,\"Parsley\"),function(){e._validateIfNeeded()}):(t=d.namespaceEvents(this.options.trigger,\"Parsley\"))&&i.on(t,function(t){e._validateIfNeeded(t)})},_validateIfNeeded:function(t){var e=this;t&&/key|input/.test(t.type)&&(!this._ui||!this._ui.validationInformationVisible)&&this.getValue().length<=this.options.validationThreshold||(this.options.debounce?(window.clearTimeout(this._debounced),this._debounced=window.setTimeout(function(){return e.validate()},this.options.debounce)):this.validate())},_resetUI:function(){this._failedOnce=!1,this._actualizeTriggers(),void 0!==this._ui&&(this._ui.$errorsWrapper.removeClass(\"filled\").children().remove(),this._resetClass(),this._ui.lastValidationResult=[],this._ui.validationInformationVisible=!1)},_destroyUI:function(){this._resetUI(),void 0!==this._ui&&this._ui.$errorsWrapper.remove(),delete this._ui},_successClass:function(){this._ui.validationInformationVisible=!0,this._ui.$errorClassHandler.removeClass(this.options.errorClass).addClass(this.options.successClass)},_errorClass:function(){this._ui.validationInformationVisible=!0,this._ui.$errorClassHandler.removeClass(this.options.successClass).addClass(this.options.errorClass)},_resetClass:function(){this._ui.$errorClassHandler.removeClass(this.options.successClass).removeClass(this.options.errorClass)}};var y=function(t,e,i){this.__class__=\"Form\",this.element=t,this.$element=h(t),this.domOptions=e,this.options=i,this.parent=window.Parsley,this.fields=[],this.validationResult=null},_={pending:null,resolved:!0,rejected:!1};y.prototype={onSubmitValidate:function(t){var e=this;if(!0!==t.parsley){var i=this._submitSource||this.$element.find(d._SubmitSelector)[0];if(this._submitSource=null,this.$element.find(\".parsley-synthetic-submit-button\").prop(\"disabled\",!0),!i||null===i.getAttribute(\"formnovalidate\")){window.Parsley._remoteCache={};var n=this.whenValidate({event:t});\"resolved\"===n.state()&&!1!==this._trigger(\"submit\")||(t.stopImmediatePropagation(),t.preventDefault(),\"pending\"===n.state()&&n.done(function(){e._submit(i)}))}}},onSubmitButton:function(t){this._submitSource=t.currentTarget},_submit:function(t){if(!1!==this._trigger(\"submit\")){if(t){var e=this.$element.find(\".parsley-synthetic-submit-button\").prop(\"disabled\",!1);0===e.length&&(e=h('<input class=\"parsley-synthetic-submit-button\" type=\"hidden\">').appendTo(this.$element)),e.attr({name:t.getAttribute(\"name\"),value:t.getAttribute(\"value\")})}this.$element.trigger(o(h.Event(\"submit\"),{parsley:!0}))}},validate:function(t){if(1<=arguments.length&&!h.isPlainObject(t)){d.warnOnce(\"Calling validate on a parsley form without passing arguments as an object is deprecated.\");var e=Array.prototype.slice.call(arguments);t={group:e[0],force:e[1],event:e[2]}}return _[this.whenValidate(t).state()]},whenValidate:function(){var t,e=this,i=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{},n=i.group,r=i.force,s=i.event;(this.submitEvent=s)&&(this.submitEvent=o({},s,{preventDefault:function(){d.warnOnce(\"Using `this.submitEvent.preventDefault()` is deprecated; instead, call `this.validationResult = false`\"),e.validationResult=!1}})),this.validationResult=!0,this._trigger(\"validate\"),this._refreshFields();var a=this._withoutReactualizingFormOptions(function(){return h.map(e.fields,function(t){return t.whenValidate({force:r,group:n})})});return(t=d.all(a).done(function(){e._trigger(\"success\")}).fail(function(){e.validationResult=!1,e.focus(),e._trigger(\"error\")}).always(function(){e._trigger(\"validated\")})).pipe.apply(t,u(this._pipeAccordingToValidationResult()))},isValid:function(t){if(1<=arguments.length&&!h.isPlainObject(t)){d.warnOnce(\"Calling isValid on a parsley form without passing arguments as an object is deprecated.\");var e=Array.prototype.slice.call(arguments);t={group:e[0],force:e[1]}}return _[this.whenValid(t).state()]},whenValid:function(){var t=this,e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{},i=e.group,n=e.force;this._refreshFields();var r=this._withoutReactualizingFormOptions(function(){return h.map(t.fields,function(t){return t.whenValid({group:i,force:n})})});return d.all(r)},refresh:function(){return this._refreshFields(),this},reset:function(){for(var t=0;t<this.fields.length;t++)this.fields[t].reset();this._trigger(\"reset\")},destroy:function(){this._destroyUI();for(var t=0;t<this.fields.length;t++)this.fields[t].destroy();this.$element.removeData(\"Parsley\"),this._trigger(\"destroy\")},_refreshFields:function(){return this.actualizeOptions()._bindFields()},_bindFields:function(){var r=this,t=this.fields;return this.fields=[],this.fieldsMappedById={},this._withoutReactualizingFormOptions(function(){r.$element.find(r.options.inputs).not(r.options.excluded).not(\"[\".concat(r.options.namespace,\"excluded=true]\")).each(function(t,e){var i=new window.Parsley.Factory(e,{},r);if(\"Field\"===i.__class__||\"FieldMultiple\"===i.__class__){var n=i.__class__+\"-\"+i.__id__;void 0===r.fieldsMappedById[n]&&(r.fieldsMappedById[n]=i,r.fields.push(i))}}),h.each(d.difference(t,r.fields),function(t,e){e.reset()})}),this},_withoutReactualizingFormOptions:function(t){var e=this.actualizeOptions;this.actualizeOptions=function(){return this};var i=t();return this.actualizeOptions=e,i},_trigger:function(t){return this.trigger(\"form:\"+t)}};var w=function(t,e,i,n,r){var s=window.Parsley._validatorRegistry.validators[e],a=new c(s);o(this,{validator:a,name:e,requirements:i,priority:n=n||t.options[e+\"Priority\"]||a.priority,isDomConstraint:r=!0===r}),this._parseRequirements(t.options)},b=function(t,e,i,n){this.__class__=\"Field\",this.element=t,this.$element=h(t),void 0!==n&&(this.parent=n),this.options=i,this.domOptions=e,this.constraints=[],this.constraintsByName={},this.validationResult=!0,this._bindConstraints()},F={pending:null,resolved:!0,rejected:!(w.prototype={validate:function(t,e){var i;return(i=this.validator).validate.apply(i,[t].concat(u(this.requirementList),[e]))},_parseRequirements:function(i){var n=this;this.requirementList=this.validator.parseRequirements(this.requirements,function(t){return i[n.name+(e=t,e[0].toUpperCase()+e.slice(1))];var e})}})};b.prototype={validate:function(t){1<=arguments.length&&!h.isPlainObject(t)&&(d.warnOnce(\"Calling validate on a parsley field without passing arguments as an object is deprecated.\"),t={options:t});var e=this.whenValidate(t);if(!e)return!0;switch(e.state()){case\"pending\":return null;case\"resolved\":return!0;case\"rejected\":return this.validationResult}},whenValidate:function(){var t,e=this,i=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{},n=i.force,r=i.group;if(this.refresh(),!r||this._isInGroup(r))return this.value=this.getValue(),this._trigger(\"validate\"),(t=this.whenValid({force:n,value:this.value,_refreshed:!0}).always(function(){e._reflowUI()}).done(function(){e._trigger(\"success\")}).fail(function(){e._trigger(\"error\")}).always(function(){e._trigger(\"validated\")})).pipe.apply(t,u(this._pipeAccordingToValidationResult()))},hasConstraints:function(){return 0!==this.constraints.length},needsValidation:function(t){return void 0===t&&(t=this.getValue()),!(!t.length&&!this._isRequired()&&void 0===this.options.validateIfEmpty)},_isInGroup:function(t){return Array.isArray(this.options.group)?-1!==h.inArray(t,this.options.group):this.options.group===t},isValid:function(t){if(1<=arguments.length&&!h.isPlainObject(t)){d.warnOnce(\"Calling isValid on a parsley field without passing arguments as an object is deprecated.\");var e=Array.prototype.slice.call(arguments);t={force:e[0],value:e[1]}}var i=this.whenValid(t);return!i||F[i.state()]},whenValid:function(){var n=this,t=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{},e=t.force,i=void 0!==e&&e,r=t.value,s=t.group;if(t._refreshed||this.refresh(),!s||this._isInGroup(s)){if(this.validationResult=!0,!this.hasConstraints())return h.when();if(null==r&&(r=this.getValue()),!this.needsValidation(r)&&!0!==i)return h.when();var a=this._getGroupedConstraints(),o=[];return h.each(a,function(t,e){var i=d.all(h.map(e,function(t){return n._validateConstraint(r,t)}));if(o.push(i),\"rejected\"===i.state())return!1}),d.all(o)}},_validateConstraint:function(t,e){var i=this,n=e.validate(t,this);return!1===n&&(n=h.Deferred().reject()),d.all([n]).fail(function(t){i.validationResult instanceof Array||(i.validationResult=[]),i.validationResult.push({assert:e,errorMessage:\"string\"==typeof t&&t})})},getValue:function(){var t;return null==(t=\"function\"==typeof this.options.value?this.options.value(this):void 0!==this.options.value?this.options.value:this.$element.val())?\"\":this._handleWhitespace(t)},reset:function(){return this._resetUI(),this._trigger(\"reset\")},destroy:function(){this._destroyUI(),this.$element.removeData(\"Parsley\"),this.$element.removeData(\"FieldMultiple\"),this._trigger(\"destroy\")},refresh:function(){return this._refreshConstraints(),this},_refreshConstraints:function(){return this.actualizeOptions()._bindConstraints()},refreshConstraints:function(){return d.warnOnce(\"Parsley's refreshConstraints is deprecated. Please use refresh\"),this.refresh()},addConstraint:function(t,e,i,n){if(window.Parsley._validatorRegistry.validators[t]){var r=new w(this,t,e,i,n);\"undefined\"!==this.constraintsByName[r.name]&&this.removeConstraint(r.name),this.constraints.push(r),this.constraintsByName[r.name]=r}return this},removeConstraint:function(t){for(var e=0;e<this.constraints.length;e++)if(t===this.constraints[e].name){this.constraints.splice(e,1);break}return delete this.constraintsByName[t],this},updateConstraint:function(t,e,i){return this.removeConstraint(t).addConstraint(t,e,i)},_bindConstraints:function(){for(var t=[],e={},i=0;i<this.constraints.length;i++)!1===this.constraints[i].isDomConstraint&&(t.push(this.constraints[i]),e[this.constraints[i].name]=this.constraints[i]);for(var n in this.constraints=t,this.constraintsByName=e,this.options)this.addConstraint(n,this.options[n],void 0,!0);return this._bindHtml5Constraints()},_bindHtml5Constraints:function(){null!==this.element.getAttribute(\"required\")&&this.addConstraint(\"required\",!0,void 0,!0),null!==this.element.getAttribute(\"pattern\")&&this.addConstraint(\"pattern\",this.element.getAttribute(\"pattern\"),void 0,!0);var t=this.element.getAttribute(\"min\"),e=this.element.getAttribute(\"max\");null!==t&&null!==e?this.addConstraint(\"range\",[t,e],void 0,!0):null!==t?this.addConstraint(\"min\",t,void 0,!0):null!==e&&this.addConstraint(\"max\",e,void 0,!0),null!==this.element.getAttribute(\"minlength\")&&null!==this.element.getAttribute(\"maxlength\")?this.addConstraint(\"length\",[this.element.getAttribute(\"minlength\"),this.element.getAttribute(\"maxlength\")],void 0,!0):null!==this.element.getAttribute(\"minlength\")?this.addConstraint(\"minlength\",this.element.getAttribute(\"minlength\"),void 0,!0):null!==this.element.getAttribute(\"maxlength\")&&this.addConstraint(\"maxlength\",this.element.getAttribute(\"maxlength\"),void 0,!0);var i=d.getType(this.element);return\"number\"===i?this.addConstraint(\"type\",[\"number\",{step:this.element.getAttribute(\"step\")||\"1\",base:t||this.element.getAttribute(\"value\")}],void 0,!0):/^(email|url|range|date)$/i.test(i)?this.addConstraint(\"type\",i,void 0,!0):this},_isRequired:function(){return void 0!==this.constraintsByName.required&&!1!==this.constraintsByName.required.requirements},_trigger:function(t){return this.trigger(\"field:\"+t)},_handleWhitespace:function(t){return!0===this.options.trimValue&&d.warnOnce('data-parsley-trim-value=\"true\" is deprecated, please use data-parsley-whitespace=\"trim\"'),\"squish\"===this.options.whitespace&&(t=t.replace(/\\s{2,}/g,\" \")),\"trim\"!==this.options.whitespace&&\"squish\"!==this.options.whitespace&&!0!==this.options.trimValue||(t=d.trimString(t)),t},_isDateInput:function(){var t=this.constraintsByName.type;return t&&\"date\"===t.requirements},_getGroupedConstraints:function(){if(!1===this.options.priorityEnabled)return[this.constraints];for(var t=[],e={},i=0;i<this.constraints.length;i++){var n=this.constraints[i].priority;e[n]||t.push(e[n]=[]),e[n].push(this.constraints[i])}return t.sort(function(t,e){return e[0].priority-t[0].priority}),t}};var C=function(){this.__class__=\"FieldMultiple\"};C.prototype={addElement:function(t){return this.$elements.push(t),this},_refreshConstraints:function(){var t;if(this.constraints=[],\"SELECT\"===this.element.nodeName)return this.actualizeOptions()._bindConstraints(),this;for(var e=0;e<this.$elements.length;e++)if(h(\"html\").has(this.$elements[e]).length){t=this.$elements[e].data(\"FieldMultiple\")._refreshConstraints().constraints;for(var i=0;i<t.length;i++)this.addConstraint(t[i].name,t[i].requirements,t[i].priority,t[i].isDomConstraint)}else this.$elements.splice(e,1);return this},getValue:function(){if(\"function\"==typeof this.options.value)return this.options.value(this);if(void 0!==this.options.value)return this.options.value;if(\"INPUT\"===this.element.nodeName){var t=d.getType(this.element);if(\"radio\"===t)return this._findRelated().filter(\":checked\").val()||\"\";if(\"checkbox\"===t){var e=[];return this._findRelated().filter(\":checked\").each(function(){e.push(h(this).val())}),e}}return\"SELECT\"===this.element.nodeName&&null===this.$element.val()?[]:this.$element.val()},_init:function(){return this.$elements=[this.$element],this}};var A=function(t,e,i){this.element=t,this.$element=h(t);var n=this.$element.data(\"Parsley\");if(n)return void 0!==i&&n.parent===window.Parsley&&(n.parent=i,n._resetOptions(n.options)),\"object\"===r(e)&&o(n.options,e),n;if(!this.$element.length)throw new Error(\"You must bind Parsley on an existing element.\");if(void 0!==i&&\"Form\"!==i.__class__)throw new Error(\"Parent instance must be a Form instance\");return this.parent=i||window.Parsley,this.init(e)};A.prototype={init:function(t){return this.__class__=\"Parsley\",this.__version__=\"2.9.1\",this.__id__=d.generateID(),this._resetOptions(t),\"FORM\"===this.element.nodeName||d.checkAttr(this.element,this.options.namespace,\"validate\")&&!this.$element.is(this.options.inputs)?this.bind(\"parsleyForm\"):this.isMultiple()?this.handleMultiple():this.bind(\"parsleyField\")},isMultiple:function(){var t=d.getType(this.element);return\"radio\"===t||\"checkbox\"===t||\"SELECT\"===this.element.nodeName&&null!==this.element.getAttribute(\"multiple\")},handleMultiple:function(){var t,e,n=this;if(this.options.multiple=this.options.multiple||(t=this.element.getAttribute(\"name\"))||this.element.getAttribute(\"id\"),\"SELECT\"===this.element.nodeName&&null!==this.element.getAttribute(\"multiple\"))return this.options.multiple=this.options.multiple||this.__id__,this.bind(\"parsleyFieldMultiple\");if(!this.options.multiple)return d.warn(\"To be bound by Parsley, a radio, a checkbox and a multiple select input must have either a name or a multiple option.\",this.$element),this;this.options.multiple=this.options.multiple.replace(/(:|\\.|\\[|\\]|\\{|\\}|\\$)/g,\"\"),t&&h('input[name=\"'+t+'\"]').each(function(t,e){var i=d.getType(e);\"radio\"!==i&&\"checkbox\"!==i||e.setAttribute(n.options.namespace+\"multiple\",n.options.multiple)});for(var i=this._findRelated(),r=0;r<i.length;r++)if(void 0!==(e=h(i.get(r)).data(\"Parsley\"))){this.$element.data(\"FieldMultiple\")||e.addElement(this.$element);break}return this.bind(\"parsleyField\",!0),e||this.bind(\"parsleyFieldMultiple\")},bind:function(t,e){var i;switch(t){case\"parsleyForm\":i=h.extend(new y(this.element,this.domOptions,this.options),new s,window.ParsleyExtend)._bindFields();break;case\"parsleyField\":i=h.extend(new b(this.element,this.domOptions,this.options,this.parent),new s,window.ParsleyExtend);break;case\"parsleyFieldMultiple\":i=h.extend(new b(this.element,this.domOptions,this.options,this.parent),new C,new s,window.ParsleyExtend)._init();break;default:throw new Error(t+\"is not a supported Parsley type\")}return this.options.multiple&&d.setAttr(this.element,this.options.namespace,\"multiple\",this.options.multiple),void 0!==e?this.$element.data(\"FieldMultiple\",i):(this.$element.data(\"Parsley\",i),i._actualizeTriggers(),i._trigger(\"init\")),i}};var E=h.fn.jquery.split(\".\");if(parseInt(E[0])<=1&&parseInt(E[1])<8)throw\"The loaded version of jQuery is too old. Please upgrade to 1.8.x or better.\";E.forEach||d.warn(\"Parsley requires ES5 to run properly. Please include https://github.com/es-shims/es5-shim\");var x=o(new s,{element:document,$element:h(document),actualizeOptions:null,_resetOptions:null,Factory:A,version:\"2.9.1\"});o(b.prototype,v.Field,s.prototype),o(y.prototype,v.Form,s.prototype),o(A.prototype,s.prototype),h.fn.parsley=h.fn.psly=function(t){if(1<this.length){var e=[];return this.each(function(){e.push(h(this).parsley(t))}),e}if(0!=this.length)return new A(this[0],t)},void 0===window.ParsleyExtend&&(window.ParsleyExtend={}),x.options=o(d.objectCreate(n),window.ParsleyConfig),window.ParsleyConfig=x.options,window.Parsley=window.psly=x,x.Utils=d,window.ParsleyUtils={},h.each(d,function(t,e){\"function\"==typeof e&&(window.ParsleyUtils[t]=function(){return d.warnOnce(\"Accessing `window.ParsleyUtils` is deprecated. Use `window.Parsley.Utils` instead.\"),d[t].apply(d,arguments)})});var $=window.Parsley._validatorRegistry=new a(window.ParsleyConfig.validators,window.ParsleyConfig.i18n);window.ParsleyValidator={},h.each(\"setLocale addCatalog addMessage addMessages getErrorMessage formatMessage addValidator updateValidator removeValidator hasValidator\".split(\" \"),function(t,e){window.Parsley[e]=function(){return $[e].apply($,arguments)},window.ParsleyValidator[e]=function(){var t;return d.warnOnce(\"Accessing the method '\".concat(e,\"' through Validator is deprecated. Simply call 'window.Parsley.\").concat(e,\"(...)'\")),(t=window.Parsley)[e].apply(t,arguments)}}),window.Parsley.UI=v,window.ParsleyUI={removeError:function(t,e,i){var n=!0!==i;return d.warnOnce(\"Accessing UI is deprecated. Call 'removeError' on the instance directly. Please comment in issue 1073 as to your need to call this method.\"),t.removeError(e,{updateClass:n})},getErrorsMessages:function(t){return d.warnOnce(\"Accessing UI is deprecated. Call 'getErrorsMessages' on the instance directly.\"),t.getErrorsMessages()}},h.each(\"addError updateError\".split(\" \"),function(t,a){window.ParsleyUI[a]=function(t,e,i,n,r){var s=!0!==r;return d.warnOnce(\"Accessing UI is deprecated. Call '\".concat(a,\"' on the instance directly. Please comment in issue 1073 as to your need to call this method.\")),t[a](e,{message:i,assert:n,updateClass:s})}}),!1!==window.ParsleyConfig.autoBind&&h(function(){h(\"[data-parsley-validate]\").length&&h(\"[data-parsley-validate]\").parsley()});var V=h({}),P=function(){d.warnOnce(\"Parsley's pubsub module is deprecated; use the 'on' and 'off' methods on parsley instances or window.Parsley\")};function O(e,i){return e.parsleyAdaptedCallback||(e.parsleyAdaptedCallback=function(){var t=Array.prototype.slice.call(arguments,0);t.unshift(this),e.apply(i||V,t)}),e.parsleyAdaptedCallback}var T=\"parsley:\";function M(t){return 0===t.lastIndexOf(T,0)?t.substr(T.length):t}return h.listen=function(t,e){var i;if(P(),\"object\"===r(arguments[1])&&\"function\"==typeof arguments[2]&&(i=arguments[1],e=arguments[2]),\"function\"!=typeof e)throw new Error(\"Wrong parameters\");window.Parsley.on(M(t),O(e,i))},h.listenTo=function(t,e,i){if(P(),!(t instanceof b||t instanceof y))throw new Error(\"Must give Parsley instance\");if(\"string\"!=typeof e||\"function\"!=typeof i)throw new Error(\"Wrong parameters\");t.on(M(e),O(i))},h.unsubscribe=function(t,e){if(P(),\"string\"!=typeof t||\"function\"!=typeof e)throw new Error(\"Wrong arguments\");window.Parsley.off(M(t),e.parsleyAdaptedCallback)},h.unsubscribeTo=function(t,e){if(P(),!(t instanceof b||t instanceof y))throw new Error(\"Must give Parsley instance\");t.off(M(e))},h.unsubscribeAll=function(e){P(),window.Parsley.off(M(e)),h(\"form,input,textarea,select\").each(function(){var t=h(this).data(\"Parsley\");t&&t.off(M(e))})},h.emit=function(t,e){var i;P();var n=e instanceof b||e instanceof y,r=Array.prototype.slice.call(arguments,n?2:1);r.unshift(M(t)),n||(e=window.Parsley),(i=e).trigger.apply(i,u(r))},h.extend(!0,x,{asyncValidators:{default:{fn:function(t){return 200<=t.status&&t.status<300},url:!1},reverse:{fn:function(t){return t.status<200||300<=t.status},url:!1}},addAsyncValidator:function(t,e,i,n){return x.asyncValidators[t]={fn:e,url:i||!1,options:n||{}},this}}),x.addValidator(\"remote\",{requirementType:{\"\":\"string\",validator:\"string\",reverse:\"boolean\",options:\"object\"},validateString:function(t,e,i,n){var r,s,a={},o=i.validator||(!0===i.reverse?\"reverse\":\"default\");if(void 0===x.asyncValidators[o])throw new Error(\"Calling an undefined async validator: `\"+o+\"`\");-1<(e=x.asyncValidators[o].url||e).indexOf(\"{value}\")?e=e.replace(\"{value}\",encodeURIComponent(t)):a[n.element.getAttribute(\"name\")||n.element.getAttribute(\"id\")]=t;var l=h.extend(!0,i.options||{},x.asyncValidators[o].options);r=h.extend(!0,{},{url:e,data:a,type:\"GET\"},l),n.trigger(\"field:ajaxoptions\",n,r),s=h.param(r),void 0===x._remoteCache&&(x._remoteCache={});var u=x._remoteCache[s]=x._remoteCache[s]||h.ajax(r),d=function(){var t=x.asyncValidators[o].fn.call(n,u,e,i);return t||(t=h.Deferred().reject()),h.when(t)};return u.then(d,d)},priority:-1}),x.on(\"form:submit\",function(){x._remoteCache={}}),s.prototype.addAsyncValidator=function(){return d.warnOnce(\"Accessing the method `addAsyncValidator` through an instance is deprecated. Simply call `Parsley.addAsyncValidator(...)`\"),x.addAsyncValidator.apply(x,arguments)},x.addMessages(\"en\",{defaultMessage:\"This value seems to be invalid.\",type:{email:\"This value should be a valid email.\",url:\"This value should be a valid url.\",number:\"This value should be a valid number.\",integer:\"This value should be a valid integer.\",digits:\"This value should be digits.\",alphanum:\"This value should be alphanumeric.\"},notblank:\"This value should not be blank.\",required:\"This value is required.\",pattern:\"This value seems to be invalid.\",min:\"This value should be greater than or equal to %s.\",max:\"This value should be lower than or equal to %s.\",range:\"This value should be between %s and %s.\",minlength:\"This value is too short. It should have %s characters or more.\",maxlength:\"This value is too long. It should have %s characters or fewer.\",length:\"This value length is invalid. It should be between %s and %s characters long.\",mincheck:\"You must select at least %s choices.\",maxcheck:\"You must select %s choices or fewer.\",check:\"You must select between %s and %s choices.\",equalto:\"This value should be the same.\",euvatin:\"It's not a valid VAT Identification Number.\"}),x.setLocale(\"en\"),(new function(){var n=this,r=window||global;o(this,{isNativeEvent:function(t){return t.originalEvent&&!1!==t.originalEvent.isTrusted},fakeInputEvent:function(t){n.isNativeEvent(t)&&h(t.target).trigger(\"input\")},misbehaves:function(t){n.isNativeEvent(t)&&(n.behavesOk(t),h(document).on(\"change.inputevent\",t.data.selector,n.fakeInputEvent),n.fakeInputEvent(t))},behavesOk:function(t){n.isNativeEvent(t)&&h(document).off(\"input.inputevent\",t.data.selector,n.behavesOk).off(\"change.inputevent\",t.data.selector,n.misbehaves)},install:function(){if(!r.inputEventPatched){r.inputEventPatched=\"0.0.3\";for(var t=[\"select\",'input[type=\"checkbox\"]','input[type=\"radio\"]','input[type=\"file\"]'],e=0;e<t.length;e++){var i=t[e];h(document).on(\"input.inputevent\",i,{selector:i},n.behavesOk).on(\"change.inputevent\",i,{selector:i},n.misbehaves)}}},uninstall:function(){delete r.inputEventPatched,h(document).off(\".inputevent\")}})}).install(),x});\n//# sourceMappingURL=parsley.min.js.map\n"

/***/ }),

/***/ "./node_modules/raw-loader/index.js!./sources/js/ObjectAssignPoly.js":
/*!******************************************************************!*\
  !*** ./node_modules/raw-loader!./sources/js/ObjectAssignPoly.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "if ( typeof Object.assign !== 'function' ) {\n    Object.assign = function(target) {\n        'use strict';\n        if (target == null) {\n            throw new TypeError('Cannot convert undefined or null to object');\n        }\n\n        target = Object(target);\n        for (var index = 1; index < arguments.length; index++) {\n            var source = arguments[index];\n            if (source != null) {\n                for (var key in source) {\n                    if (Object.prototype.hasOwnProperty.call(source, key)) {\n                        target[key] = source[key];\n                    }\n                }\n            }\n        }\n        return target;\n    };\n}"

/***/ }),

/***/ "./node_modules/script-loader/addScript.js":
/*!*************************************************!*\
  !*** ./node_modules/script-loader/addScript.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
module.exports = function(src) {
	function log(error) {
		(typeof console !== "undefined")
		&& (console.error || console.log)("[Script Loader]", error);
	}

	// Check for IE =< 8
	function isIE() {
		return typeof attachEvent !== "undefined" && typeof addEventListener === "undefined";
	}

	try {
		if (typeof execScript !== "undefined" && isIE()) {
			execScript(src);
		} else if (typeof eval !== "undefined") {
			eval.call(null, src);
		} else {
			log("EvalError: No eval function available");
		}
	} catch (error) {
		log(error);
	}
}


/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/EasyTabs/lib/jquery.easytabs.min.js":
/*!***************************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/EasyTabs/lib/jquery.easytabs.min.js ***!
  \***************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/EasyTabs/lib/jquery.easytabs.min.js */ "./node_modules/raw-loader/index.js!./node_modules/EasyTabs/lib/jquery.easytabs.min.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/EasyTabs/vendor/jquery.hashchange.min.js":
/*!********************************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/EasyTabs/vendor/jquery.hashchange.min.js ***!
  \********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/EasyTabs/vendor/jquery.hashchange.min.js */ "./node_modules/raw-loader/index.js!./node_modules/EasyTabs/vendor/jquery.hashchange.min.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/dom4/build/dom4.max.js":
/*!**************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/dom4/build/dom4.max.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/dom4/build/dom4.max.js */ "./node_modules/raw-loader/index.js!./node_modules/dom4/build/dom4.max.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/garlicjs/dist/garlic.min.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/garlicjs/dist/garlic.min.js ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/garlicjs/dist/garlic.min.js */ "./node_modules/raw-loader/index.js!./node_modules/garlicjs/dist/garlic.min.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/da.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/da.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/da.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/da.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/de.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/de.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/de.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/de.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/en.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/en.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/en.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/en.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/es.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/es.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/es.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/es.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/fi.extra.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/fi.extra.js ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/fi.extra.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/fi.extra.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/fi.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/fi.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/fi.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/fi.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/fr.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/fr.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/fr.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/fr.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/he.extra.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/he.extra.js ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/he.extra.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/he.extra.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/he.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/he.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/he.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/he.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/hu.extra.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/hu.extra.js ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/hu.extra.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/hu.extra.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/hu.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/hu.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/hu.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/hu.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/it.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/it.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/it.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/it.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/ja.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/ja.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/ja.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/ja.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/nl.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/nl.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/nl.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/nl.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/no.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/no.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/no.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/no.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/pl.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/pl.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/pl.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/pl.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/pt-br.js":
/*!********************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/pt-br.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/pt-br.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/pt-br.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/pt-pt.js":
/*!********************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/pt-pt.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/pt-pt.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/pt-pt.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/sl.extra.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/sl.extra.js ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/sl.extra.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/sl.extra.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/sl.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/sl.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/sl.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/sl.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/zh_cn.js":
/*!********************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/i18n/zh_cn.js ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/i18n/zh_cn.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/i18n/zh_cn.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/parsley.min.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/script-loader!./node_modules/parsleyjs/dist/parsley.min.js ***!
  \*********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./node_modules/parsleyjs/dist/parsley.min.js */ "./node_modules/raw-loader/index.js!./node_modules/parsleyjs/dist/parsley.min.js"))

/***/ }),

/***/ "./node_modules/script-loader/index.js!./sources/js/ObjectAssignPoly.js":
/*!*********************************************************************!*\
  !*** ./node_modules/script-loader!./sources/js/ObjectAssignPoly.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! !./node_modules/script-loader/addScript.js */ "./node_modules/script-loader/addScript.js")(__webpack_require__(/*! !./node_modules/raw-loader!./sources/js/ObjectAssignPoly.js */ "./node_modules/raw-loader/index.js!./sources/js/ObjectAssignPoly.js"))

/***/ }),

/***/ "./node_modules/ts-md5/dist/md5.js":
/*!*****************************************!*\
  !*** ./node_modules/ts-md5/dist/md5.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

/*

TypeScript Md5
==============

Based on work by
* Joseph Myers: http://www.myersdaily.org/joseph/javascript/md5-text.html
* André Cruz: https://github.com/satazor/SparkMD5
* Raymond Hill: https://github.com/gorhill/yamd5.js

Effectively a TypeScrypt re-write of Raymond Hill JS Library

The MIT License (MIT)

Copyright (C) 2014 Raymond Hill

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.



            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                    Version 2, December 2004

 Copyright (C) 2015 André Cruz <amdfcruz@gmail.com>

 Everyone is permitted to copy and distribute verbatim or modified
 copies of this license document, and changing it is allowed as long
 as the name is changed.

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

  0. You just DO WHAT THE FUCK YOU WANT TO.


*/
Object.defineProperty(exports, "__esModule", { value: true });
var Md5 = /** @class */ (function () {
    function Md5() {
        this._state = new Int32Array(4);
        this._buffer = new ArrayBuffer(68);
        this._buffer8 = new Uint8Array(this._buffer, 0, 68);
        this._buffer32 = new Uint32Array(this._buffer, 0, 17);
        this.start();
    }
    // One time hashing functions
    Md5.hashStr = function (str, raw) {
        if (raw === void 0) { raw = false; }
        return this.onePassHasher
            .start()
            .appendStr(str)
            .end(raw);
    };
    Md5.hashAsciiStr = function (str, raw) {
        if (raw === void 0) { raw = false; }
        return this.onePassHasher
            .start()
            .appendAsciiStr(str)
            .end(raw);
    };
    Md5._hex = function (x) {
        var hc = Md5.hexChars;
        var ho = Md5.hexOut;
        var n;
        var offset;
        var j;
        var i;
        for (i = 0; i < 4; i += 1) {
            offset = i * 8;
            n = x[i];
            for (j = 0; j < 8; j += 2) {
                ho[offset + 1 + j] = hc.charAt(n & 0x0F);
                n >>>= 4;
                ho[offset + 0 + j] = hc.charAt(n & 0x0F);
                n >>>= 4;
            }
        }
        return ho.join('');
    };
    Md5._md5cycle = function (x, k) {
        var a = x[0];
        var b = x[1];
        var c = x[2];
        var d = x[3];
        // ff()
        a += (b & c | ~b & d) + k[0] - 680876936 | 0;
        a = (a << 7 | a >>> 25) + b | 0;
        d += (a & b | ~a & c) + k[1] - 389564586 | 0;
        d = (d << 12 | d >>> 20) + a | 0;
        c += (d & a | ~d & b) + k[2] + 606105819 | 0;
        c = (c << 17 | c >>> 15) + d | 0;
        b += (c & d | ~c & a) + k[3] - 1044525330 | 0;
        b = (b << 22 | b >>> 10) + c | 0;
        a += (b & c | ~b & d) + k[4] - 176418897 | 0;
        a = (a << 7 | a >>> 25) + b | 0;
        d += (a & b | ~a & c) + k[5] + 1200080426 | 0;
        d = (d << 12 | d >>> 20) + a | 0;
        c += (d & a | ~d & b) + k[6] - 1473231341 | 0;
        c = (c << 17 | c >>> 15) + d | 0;
        b += (c & d | ~c & a) + k[7] - 45705983 | 0;
        b = (b << 22 | b >>> 10) + c | 0;
        a += (b & c | ~b & d) + k[8] + 1770035416 | 0;
        a = (a << 7 | a >>> 25) + b | 0;
        d += (a & b | ~a & c) + k[9] - 1958414417 | 0;
        d = (d << 12 | d >>> 20) + a | 0;
        c += (d & a | ~d & b) + k[10] - 42063 | 0;
        c = (c << 17 | c >>> 15) + d | 0;
        b += (c & d | ~c & a) + k[11] - 1990404162 | 0;
        b = (b << 22 | b >>> 10) + c | 0;
        a += (b & c | ~b & d) + k[12] + 1804603682 | 0;
        a = (a << 7 | a >>> 25) + b | 0;
        d += (a & b | ~a & c) + k[13] - 40341101 | 0;
        d = (d << 12 | d >>> 20) + a | 0;
        c += (d & a | ~d & b) + k[14] - 1502002290 | 0;
        c = (c << 17 | c >>> 15) + d | 0;
        b += (c & d | ~c & a) + k[15] + 1236535329 | 0;
        b = (b << 22 | b >>> 10) + c | 0;
        // gg()
        a += (b & d | c & ~d) + k[1] - 165796510 | 0;
        a = (a << 5 | a >>> 27) + b | 0;
        d += (a & c | b & ~c) + k[6] - 1069501632 | 0;
        d = (d << 9 | d >>> 23) + a | 0;
        c += (d & b | a & ~b) + k[11] + 643717713 | 0;
        c = (c << 14 | c >>> 18) + d | 0;
        b += (c & a | d & ~a) + k[0] - 373897302 | 0;
        b = (b << 20 | b >>> 12) + c | 0;
        a += (b & d | c & ~d) + k[5] - 701558691 | 0;
        a = (a << 5 | a >>> 27) + b | 0;
        d += (a & c | b & ~c) + k[10] + 38016083 | 0;
        d = (d << 9 | d >>> 23) + a | 0;
        c += (d & b | a & ~b) + k[15] - 660478335 | 0;
        c = (c << 14 | c >>> 18) + d | 0;
        b += (c & a | d & ~a) + k[4] - 405537848 | 0;
        b = (b << 20 | b >>> 12) + c | 0;
        a += (b & d | c & ~d) + k[9] + 568446438 | 0;
        a = (a << 5 | a >>> 27) + b | 0;
        d += (a & c | b & ~c) + k[14] - 1019803690 | 0;
        d = (d << 9 | d >>> 23) + a | 0;
        c += (d & b | a & ~b) + k[3] - 187363961 | 0;
        c = (c << 14 | c >>> 18) + d | 0;
        b += (c & a | d & ~a) + k[8] + 1163531501 | 0;
        b = (b << 20 | b >>> 12) + c | 0;
        a += (b & d | c & ~d) + k[13] - 1444681467 | 0;
        a = (a << 5 | a >>> 27) + b | 0;
        d += (a & c | b & ~c) + k[2] - 51403784 | 0;
        d = (d << 9 | d >>> 23) + a | 0;
        c += (d & b | a & ~b) + k[7] + 1735328473 | 0;
        c = (c << 14 | c >>> 18) + d | 0;
        b += (c & a | d & ~a) + k[12] - 1926607734 | 0;
        b = (b << 20 | b >>> 12) + c | 0;
        // hh()
        a += (b ^ c ^ d) + k[5] - 378558 | 0;
        a = (a << 4 | a >>> 28) + b | 0;
        d += (a ^ b ^ c) + k[8] - 2022574463 | 0;
        d = (d << 11 | d >>> 21) + a | 0;
        c += (d ^ a ^ b) + k[11] + 1839030562 | 0;
        c = (c << 16 | c >>> 16) + d | 0;
        b += (c ^ d ^ a) + k[14] - 35309556 | 0;
        b = (b << 23 | b >>> 9) + c | 0;
        a += (b ^ c ^ d) + k[1] - 1530992060 | 0;
        a = (a << 4 | a >>> 28) + b | 0;
        d += (a ^ b ^ c) + k[4] + 1272893353 | 0;
        d = (d << 11 | d >>> 21) + a | 0;
        c += (d ^ a ^ b) + k[7] - 155497632 | 0;
        c = (c << 16 | c >>> 16) + d | 0;
        b += (c ^ d ^ a) + k[10] - 1094730640 | 0;
        b = (b << 23 | b >>> 9) + c | 0;
        a += (b ^ c ^ d) + k[13] + 681279174 | 0;
        a = (a << 4 | a >>> 28) + b | 0;
        d += (a ^ b ^ c) + k[0] - 358537222 | 0;
        d = (d << 11 | d >>> 21) + a | 0;
        c += (d ^ a ^ b) + k[3] - 722521979 | 0;
        c = (c << 16 | c >>> 16) + d | 0;
        b += (c ^ d ^ a) + k[6] + 76029189 | 0;
        b = (b << 23 | b >>> 9) + c | 0;
        a += (b ^ c ^ d) + k[9] - 640364487 | 0;
        a = (a << 4 | a >>> 28) + b | 0;
        d += (a ^ b ^ c) + k[12] - 421815835 | 0;
        d = (d << 11 | d >>> 21) + a | 0;
        c += (d ^ a ^ b) + k[15] + 530742520 | 0;
        c = (c << 16 | c >>> 16) + d | 0;
        b += (c ^ d ^ a) + k[2] - 995338651 | 0;
        b = (b << 23 | b >>> 9) + c | 0;
        // ii()
        a += (c ^ (b | ~d)) + k[0] - 198630844 | 0;
        a = (a << 6 | a >>> 26) + b | 0;
        d += (b ^ (a | ~c)) + k[7] + 1126891415 | 0;
        d = (d << 10 | d >>> 22) + a | 0;
        c += (a ^ (d | ~b)) + k[14] - 1416354905 | 0;
        c = (c << 15 | c >>> 17) + d | 0;
        b += (d ^ (c | ~a)) + k[5] - 57434055 | 0;
        b = (b << 21 | b >>> 11) + c | 0;
        a += (c ^ (b | ~d)) + k[12] + 1700485571 | 0;
        a = (a << 6 | a >>> 26) + b | 0;
        d += (b ^ (a | ~c)) + k[3] - 1894986606 | 0;
        d = (d << 10 | d >>> 22) + a | 0;
        c += (a ^ (d | ~b)) + k[10] - 1051523 | 0;
        c = (c << 15 | c >>> 17) + d | 0;
        b += (d ^ (c | ~a)) + k[1] - 2054922799 | 0;
        b = (b << 21 | b >>> 11) + c | 0;
        a += (c ^ (b | ~d)) + k[8] + 1873313359 | 0;
        a = (a << 6 | a >>> 26) + b | 0;
        d += (b ^ (a | ~c)) + k[15] - 30611744 | 0;
        d = (d << 10 | d >>> 22) + a | 0;
        c += (a ^ (d | ~b)) + k[6] - 1560198380 | 0;
        c = (c << 15 | c >>> 17) + d | 0;
        b += (d ^ (c | ~a)) + k[13] + 1309151649 | 0;
        b = (b << 21 | b >>> 11) + c | 0;
        a += (c ^ (b | ~d)) + k[4] - 145523070 | 0;
        a = (a << 6 | a >>> 26) + b | 0;
        d += (b ^ (a | ~c)) + k[11] - 1120210379 | 0;
        d = (d << 10 | d >>> 22) + a | 0;
        c += (a ^ (d | ~b)) + k[2] + 718787259 | 0;
        c = (c << 15 | c >>> 17) + d | 0;
        b += (d ^ (c | ~a)) + k[9] - 343485551 | 0;
        b = (b << 21 | b >>> 11) + c | 0;
        x[0] = a + x[0] | 0;
        x[1] = b + x[1] | 0;
        x[2] = c + x[2] | 0;
        x[3] = d + x[3] | 0;
    };
    Md5.prototype.start = function () {
        this._dataLength = 0;
        this._bufferLength = 0;
        this._state.set(Md5.stateIdentity);
        return this;
    };
    // Char to code point to to array conversion:
    // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/charCodeAt
    // #Example.3A_Fixing_charCodeAt_to_handle_non-Basic-Multilingual-Plane_characters_if_their_presence_earlier_in_the_string_is_unknown
    Md5.prototype.appendStr = function (str) {
        var buf8 = this._buffer8;
        var buf32 = this._buffer32;
        var bufLen = this._bufferLength;
        var code;
        var i;
        for (i = 0; i < str.length; i += 1) {
            code = str.charCodeAt(i);
            if (code < 128) {
                buf8[bufLen++] = code;
            }
            else if (code < 0x800) {
                buf8[bufLen++] = (code >>> 6) + 0xC0;
                buf8[bufLen++] = code & 0x3F | 0x80;
            }
            else if (code < 0xD800 || code > 0xDBFF) {
                buf8[bufLen++] = (code >>> 12) + 0xE0;
                buf8[bufLen++] = (code >>> 6 & 0x3F) | 0x80;
                buf8[bufLen++] = (code & 0x3F) | 0x80;
            }
            else {
                code = ((code - 0xD800) * 0x400) + (str.charCodeAt(++i) - 0xDC00) + 0x10000;
                if (code > 0x10FFFF) {
                    throw new Error('Unicode standard supports code points up to U+10FFFF');
                }
                buf8[bufLen++] = (code >>> 18) + 0xF0;
                buf8[bufLen++] = (code >>> 12 & 0x3F) | 0x80;
                buf8[bufLen++] = (code >>> 6 & 0x3F) | 0x80;
                buf8[bufLen++] = (code & 0x3F) | 0x80;
            }
            if (bufLen >= 64) {
                this._dataLength += 64;
                Md5._md5cycle(this._state, buf32);
                bufLen -= 64;
                buf32[0] = buf32[16];
            }
        }
        this._bufferLength = bufLen;
        return this;
    };
    Md5.prototype.appendAsciiStr = function (str) {
        var buf8 = this._buffer8;
        var buf32 = this._buffer32;
        var bufLen = this._bufferLength;
        var i;
        var j = 0;
        for (;;) {
            i = Math.min(str.length - j, 64 - bufLen);
            while (i--) {
                buf8[bufLen++] = str.charCodeAt(j++);
            }
            if (bufLen < 64) {
                break;
            }
            this._dataLength += 64;
            Md5._md5cycle(this._state, buf32);
            bufLen = 0;
        }
        this._bufferLength = bufLen;
        return this;
    };
    Md5.prototype.appendByteArray = function (input) {
        var buf8 = this._buffer8;
        var buf32 = this._buffer32;
        var bufLen = this._bufferLength;
        var i;
        var j = 0;
        for (;;) {
            i = Math.min(input.length - j, 64 - bufLen);
            while (i--) {
                buf8[bufLen++] = input[j++];
            }
            if (bufLen < 64) {
                break;
            }
            this._dataLength += 64;
            Md5._md5cycle(this._state, buf32);
            bufLen = 0;
        }
        this._bufferLength = bufLen;
        return this;
    };
    Md5.prototype.getState = function () {
        var self = this;
        var s = self._state;
        return {
            buffer: String.fromCharCode.apply(null, self._buffer8),
            buflen: self._bufferLength,
            length: self._dataLength,
            state: [s[0], s[1], s[2], s[3]]
        };
    };
    Md5.prototype.setState = function (state) {
        var buf = state.buffer;
        var x = state.state;
        var s = this._state;
        var i;
        this._dataLength = state.length;
        this._bufferLength = state.buflen;
        s[0] = x[0];
        s[1] = x[1];
        s[2] = x[2];
        s[3] = x[3];
        for (i = 0; i < buf.length; i += 1) {
            this._buffer8[i] = buf.charCodeAt(i);
        }
    };
    Md5.prototype.end = function (raw) {
        if (raw === void 0) { raw = false; }
        var bufLen = this._bufferLength;
        var buf8 = this._buffer8;
        var buf32 = this._buffer32;
        var i = (bufLen >> 2) + 1;
        var dataBitsLen;
        this._dataLength += bufLen;
        buf8[bufLen] = 0x80;
        buf8[bufLen + 1] = buf8[bufLen + 2] = buf8[bufLen + 3] = 0;
        buf32.set(Md5.buffer32Identity.subarray(i), i);
        if (bufLen > 55) {
            Md5._md5cycle(this._state, buf32);
            buf32.set(Md5.buffer32Identity);
        }
        // Do the final computation based on the tail and length
        // Beware that the final length may not fit in 32 bits so we take care of that
        dataBitsLen = this._dataLength * 8;
        if (dataBitsLen <= 0xFFFFFFFF) {
            buf32[14] = dataBitsLen;
        }
        else {
            var matches = dataBitsLen.toString(16).match(/(.*?)(.{0,8})$/);
            if (matches === null) {
                return;
            }
            var lo = parseInt(matches[2], 16);
            var hi = parseInt(matches[1], 16) || 0;
            buf32[14] = lo;
            buf32[15] = hi;
        }
        Md5._md5cycle(this._state, buf32);
        return raw ? this._state : Md5._hex(this._state);
    };
    // Private Static Variables
    Md5.stateIdentity = new Int32Array([1732584193, -271733879, -1732584194, 271733878]);
    Md5.buffer32Identity = new Int32Array([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]);
    Md5.hexChars = '0123456789abcdef';
    Md5.hexOut = [];
    // Permanent instance is to use for one-call hashing
    Md5.onePassHasher = new Md5();
    return Md5;
}());
exports.Md5 = Md5;
if (Md5.hashStr('hello') !== '5d41402abc4b2a76b9719d911017c592') {
    console.error('Md5 self test failed.');
}
//# sourceMappingURL=md5.js.map

/***/ }),

/***/ "./node_modules/ts-polyfill/dist/ts-polyfill.js":
/*!******************************************************!*\
  !*** ./node_modules/ts-polyfill/dist/ts-polyfill.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var tsPolyfill = (function () {
	'use strict';

	var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	function unwrapExports (x) {
		return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
	}

	function createCommonjsModule(fn, module) {
		return module = { exports: {} }, fn(module, module.exports), module.exports;
	}

	var O = 'object';
	var check = function (it) {
	  return it && it.Math == Math && it;
	};

	// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
	var global_1 =
	  // eslint-disable-next-line no-undef
	  check(typeof globalThis == O && globalThis) ||
	  check(typeof window == O && window) ||
	  check(typeof self == O && self) ||
	  check(typeof commonjsGlobal == O && commonjsGlobal) ||
	  // eslint-disable-next-line no-new-func
	  Function('return this')();

	var fails = function (exec) {
	  try {
	    return !!exec();
	  } catch (error) {
	    return true;
	  }
	};

	// Thank's IE8 for his funny defineProperty
	var descriptors = !fails(function () {
	  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
	});

	var nativePropertyIsEnumerable = {}.propertyIsEnumerable;
	var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

	// Nashorn ~ JDK8 bug
	var NASHORN_BUG = getOwnPropertyDescriptor && !nativePropertyIsEnumerable.call({ 1: 2 }, 1);

	// `Object.prototype.propertyIsEnumerable` method implementation
	// https://tc39.github.io/ecma262/#sec-object.prototype.propertyisenumerable
	var f = NASHORN_BUG ? function propertyIsEnumerable(V) {
	  var descriptor = getOwnPropertyDescriptor(this, V);
	  return !!descriptor && descriptor.enumerable;
	} : nativePropertyIsEnumerable;

	var objectPropertyIsEnumerable = {
		f: f
	};

	var createPropertyDescriptor = function (bitmap, value) {
	  return {
	    enumerable: !(bitmap & 1),
	    configurable: !(bitmap & 2),
	    writable: !(bitmap & 4),
	    value: value
	  };
	};

	var toString = {}.toString;

	var classofRaw = function (it) {
	  return toString.call(it).slice(8, -1);
	};

	var split = ''.split;

	// fallback for non-array-like ES3 and non-enumerable old V8 strings
	var indexedObject = fails(function () {
	  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
	  // eslint-disable-next-line no-prototype-builtins
	  return !Object('z').propertyIsEnumerable(0);
	}) ? function (it) {
	  return classofRaw(it) == 'String' ? split.call(it, '') : Object(it);
	} : Object;

	// `RequireObjectCoercible` abstract operation
	// https://tc39.github.io/ecma262/#sec-requireobjectcoercible
	var requireObjectCoercible = function (it) {
	  if (it == undefined) throw TypeError("Can't call method on " + it);
	  return it;
	};

	// toObject with fallback for non-array-like ES3 strings



	var toIndexedObject = function (it) {
	  return indexedObject(requireObjectCoercible(it));
	};

	var isObject = function (it) {
	  return typeof it === 'object' ? it !== null : typeof it === 'function';
	};

	// `ToPrimitive` abstract operation
	// https://tc39.github.io/ecma262/#sec-toprimitive
	// instead of the ES6 spec version, we didn't implement @@toPrimitive case
	// and the second argument - flag - preferred type is a string
	var toPrimitive = function (input, PREFERRED_STRING) {
	  if (!isObject(input)) return input;
	  var fn, val;
	  if (PREFERRED_STRING && typeof (fn = input.toString) == 'function' && !isObject(val = fn.call(input))) return val;
	  if (typeof (fn = input.valueOf) == 'function' && !isObject(val = fn.call(input))) return val;
	  if (!PREFERRED_STRING && typeof (fn = input.toString) == 'function' && !isObject(val = fn.call(input))) return val;
	  throw TypeError("Can't convert object to primitive value");
	};

	var hasOwnProperty = {}.hasOwnProperty;

	var has = function (it, key) {
	  return hasOwnProperty.call(it, key);
	};

	var document$1 = global_1.document;
	// typeof document.createElement is 'object' in old IE
	var EXISTS = isObject(document$1) && isObject(document$1.createElement);

	var documentCreateElement = function (it) {
	  return EXISTS ? document$1.createElement(it) : {};
	};

	// Thank's IE8 for his funny defineProperty
	var ie8DomDefine = !descriptors && !fails(function () {
	  return Object.defineProperty(documentCreateElement('div'), 'a', {
	    get: function () { return 7; }
	  }).a != 7;
	});

	var nativeGetOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

	// `Object.getOwnPropertyDescriptor` method
	// https://tc39.github.io/ecma262/#sec-object.getownpropertydescriptor
	var f$1 = descriptors ? nativeGetOwnPropertyDescriptor : function getOwnPropertyDescriptor(O, P) {
	  O = toIndexedObject(O);
	  P = toPrimitive(P, true);
	  if (ie8DomDefine) try {
	    return nativeGetOwnPropertyDescriptor(O, P);
	  } catch (error) { /* empty */ }
	  if (has(O, P)) return createPropertyDescriptor(!objectPropertyIsEnumerable.f.call(O, P), O[P]);
	};

	var objectGetOwnPropertyDescriptor = {
		f: f$1
	};

	var anObject = function (it) {
	  if (!isObject(it)) {
	    throw TypeError(String(it) + ' is not an object');
	  } return it;
	};

	var nativeDefineProperty = Object.defineProperty;

	// `Object.defineProperty` method
	// https://tc39.github.io/ecma262/#sec-object.defineproperty
	var f$2 = descriptors ? nativeDefineProperty : function defineProperty(O, P, Attributes) {
	  anObject(O);
	  P = toPrimitive(P, true);
	  anObject(Attributes);
	  if (ie8DomDefine) try {
	    return nativeDefineProperty(O, P, Attributes);
	  } catch (error) { /* empty */ }
	  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported');
	  if ('value' in Attributes) O[P] = Attributes.value;
	  return O;
	};

	var objectDefineProperty = {
		f: f$2
	};

	var hide = descriptors ? function (object, key, value) {
	  return objectDefineProperty.f(object, key, createPropertyDescriptor(1, value));
	} : function (object, key, value) {
	  object[key] = value;
	  return object;
	};

	var setGlobal = function (key, value) {
	  try {
	    hide(global_1, key, value);
	  } catch (error) {
	    global_1[key] = value;
	  } return value;
	};

	var isPure = false;

	var shared = createCommonjsModule(function (module) {
	var SHARED = '__core-js_shared__';
	var store = global_1[SHARED] || setGlobal(SHARED, {});

	(module.exports = function (key, value) {
	  return store[key] || (store[key] = value !== undefined ? value : {});
	})('versions', []).push({
	  version: '3.2.1',
	  mode:  'global',
	  copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
	});
	});

	var functionToString = shared('native-function-to-string', Function.toString);

	var WeakMap = global_1.WeakMap;

	var nativeWeakMap = typeof WeakMap === 'function' && /native code/.test(functionToString.call(WeakMap));

	var id = 0;
	var postfix = Math.random();

	var uid = function (key) {
	  return 'Symbol(' + String(key === undefined ? '' : key) + ')_' + (++id + postfix).toString(36);
	};

	var keys = shared('keys');

	var sharedKey = function (key) {
	  return keys[key] || (keys[key] = uid(key));
	};

	var hiddenKeys = {};

	var WeakMap$1 = global_1.WeakMap;
	var set, get, has$1;

	var enforce = function (it) {
	  return has$1(it) ? get(it) : set(it, {});
	};

	var getterFor = function (TYPE) {
	  return function (it) {
	    var state;
	    if (!isObject(it) || (state = get(it)).type !== TYPE) {
	      throw TypeError('Incompatible receiver, ' + TYPE + ' required');
	    } return state;
	  };
	};

	if (nativeWeakMap) {
	  var store = new WeakMap$1();
	  var wmget = store.get;
	  var wmhas = store.has;
	  var wmset = store.set;
	  set = function (it, metadata) {
	    wmset.call(store, it, metadata);
	    return metadata;
	  };
	  get = function (it) {
	    return wmget.call(store, it) || {};
	  };
	  has$1 = function (it) {
	    return wmhas.call(store, it);
	  };
	} else {
	  var STATE = sharedKey('state');
	  hiddenKeys[STATE] = true;
	  set = function (it, metadata) {
	    hide(it, STATE, metadata);
	    return metadata;
	  };
	  get = function (it) {
	    return has(it, STATE) ? it[STATE] : {};
	  };
	  has$1 = function (it) {
	    return has(it, STATE);
	  };
	}

	var internalState = {
	  set: set,
	  get: get,
	  has: has$1,
	  enforce: enforce,
	  getterFor: getterFor
	};

	var redefine = createCommonjsModule(function (module) {
	var getInternalState = internalState.get;
	var enforceInternalState = internalState.enforce;
	var TEMPLATE = String(functionToString).split('toString');

	shared('inspectSource', function (it) {
	  return functionToString.call(it);
	});

	(module.exports = function (O, key, value, options) {
	  var unsafe = options ? !!options.unsafe : false;
	  var simple = options ? !!options.enumerable : false;
	  var noTargetGet = options ? !!options.noTargetGet : false;
	  if (typeof value == 'function') {
	    if (typeof key == 'string' && !has(value, 'name')) hide(value, 'name', key);
	    enforceInternalState(value).source = TEMPLATE.join(typeof key == 'string' ? key : '');
	  }
	  if (O === global_1) {
	    if (simple) O[key] = value;
	    else setGlobal(key, value);
	    return;
	  } else if (!unsafe) {
	    delete O[key];
	  } else if (!noTargetGet && O[key]) {
	    simple = true;
	  }
	  if (simple) O[key] = value;
	  else hide(O, key, value);
	// add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
	})(Function.prototype, 'toString', function toString() {
	  return typeof this == 'function' && getInternalState(this).source || functionToString.call(this);
	});
	});

	var path = global_1;

	var aFunction = function (variable) {
	  return typeof variable == 'function' ? variable : undefined;
	};

	var getBuiltIn = function (namespace, method) {
	  return arguments.length < 2 ? aFunction(path[namespace]) || aFunction(global_1[namespace])
	    : path[namespace] && path[namespace][method] || global_1[namespace] && global_1[namespace][method];
	};

	var ceil = Math.ceil;
	var floor = Math.floor;

	// `ToInteger` abstract operation
	// https://tc39.github.io/ecma262/#sec-tointeger
	var toInteger = function (argument) {
	  return isNaN(argument = +argument) ? 0 : (argument > 0 ? floor : ceil)(argument);
	};

	var min = Math.min;

	// `ToLength` abstract operation
	// https://tc39.github.io/ecma262/#sec-tolength
	var toLength = function (argument) {
	  return argument > 0 ? min(toInteger(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
	};

	var max = Math.max;
	var min$1 = Math.min;

	// Helper for a popular repeating case of the spec:
	// Let integer be ? ToInteger(index).
	// If integer < 0, let result be max((length + integer), 0); else let result be min(length, length).
	var toAbsoluteIndex = function (index, length) {
	  var integer = toInteger(index);
	  return integer < 0 ? max(integer + length, 0) : min$1(integer, length);
	};

	// `Array.prototype.{ indexOf, includes }` methods implementation
	var createMethod = function (IS_INCLUDES) {
	  return function ($this, el, fromIndex) {
	    var O = toIndexedObject($this);
	    var length = toLength(O.length);
	    var index = toAbsoluteIndex(fromIndex, length);
	    var value;
	    // Array#includes uses SameValueZero equality algorithm
	    // eslint-disable-next-line no-self-compare
	    if (IS_INCLUDES && el != el) while (length > index) {
	      value = O[index++];
	      // eslint-disable-next-line no-self-compare
	      if (value != value) return true;
	    // Array#indexOf ignores holes, Array#includes - not
	    } else for (;length > index; index++) {
	      if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
	    } return !IS_INCLUDES && -1;
	  };
	};

	var arrayIncludes = {
	  // `Array.prototype.includes` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.includes
	  includes: createMethod(true),
	  // `Array.prototype.indexOf` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.indexof
	  indexOf: createMethod(false)
	};

	var indexOf = arrayIncludes.indexOf;


	var objectKeysInternal = function (object, names) {
	  var O = toIndexedObject(object);
	  var i = 0;
	  var result = [];
	  var key;
	  for (key in O) !has(hiddenKeys, key) && has(O, key) && result.push(key);
	  // Don't enum bug & hidden keys
	  while (names.length > i) if (has(O, key = names[i++])) {
	    ~indexOf(result, key) || result.push(key);
	  }
	  return result;
	};

	// IE8- don't enum bug keys
	var enumBugKeys = [
	  'constructor',
	  'hasOwnProperty',
	  'isPrototypeOf',
	  'propertyIsEnumerable',
	  'toLocaleString',
	  'toString',
	  'valueOf'
	];

	var hiddenKeys$1 = enumBugKeys.concat('length', 'prototype');

	// `Object.getOwnPropertyNames` method
	// https://tc39.github.io/ecma262/#sec-object.getownpropertynames
	var f$3 = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
	  return objectKeysInternal(O, hiddenKeys$1);
	};

	var objectGetOwnPropertyNames = {
		f: f$3
	};

	var f$4 = Object.getOwnPropertySymbols;

	var objectGetOwnPropertySymbols = {
		f: f$4
	};

	// all object keys, includes non-enumerable and symbols
	var ownKeys = getBuiltIn('Reflect', 'ownKeys') || function ownKeys(it) {
	  var keys = objectGetOwnPropertyNames.f(anObject(it));
	  var getOwnPropertySymbols = objectGetOwnPropertySymbols.f;
	  return getOwnPropertySymbols ? keys.concat(getOwnPropertySymbols(it)) : keys;
	};

	var copyConstructorProperties = function (target, source) {
	  var keys = ownKeys(source);
	  var defineProperty = objectDefineProperty.f;
	  var getOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
	  for (var i = 0; i < keys.length; i++) {
	    var key = keys[i];
	    if (!has(target, key)) defineProperty(target, key, getOwnPropertyDescriptor(source, key));
	  }
	};

	var replacement = /#|\.prototype\./;

	var isForced = function (feature, detection) {
	  var value = data[normalize(feature)];
	  return value == POLYFILL ? true
	    : value == NATIVE ? false
	    : typeof detection == 'function' ? fails(detection)
	    : !!detection;
	};

	var normalize = isForced.normalize = function (string) {
	  return String(string).replace(replacement, '.').toLowerCase();
	};

	var data = isForced.data = {};
	var NATIVE = isForced.NATIVE = 'N';
	var POLYFILL = isForced.POLYFILL = 'P';

	var isForced_1 = isForced;

	var getOwnPropertyDescriptor$1 = objectGetOwnPropertyDescriptor.f;






	/*
	  options.target      - name of the target object
	  options.global      - target is the global object
	  options.stat        - export as static methods of target
	  options.proto       - export as prototype methods of target
	  options.real        - real prototype method for the `pure` version
	  options.forced      - export even if the native feature is available
	  options.bind        - bind methods to the target, required for the `pure` version
	  options.wrap        - wrap constructors to preventing global pollution, required for the `pure` version
	  options.unsafe      - use the simple assignment of property instead of delete + defineProperty
	  options.sham        - add a flag to not completely full polyfills
	  options.enumerable  - export as enumerable property
	  options.noTargetGet - prevent calling a getter on target
	*/
	var _export = function (options, source) {
	  var TARGET = options.target;
	  var GLOBAL = options.global;
	  var STATIC = options.stat;
	  var FORCED, target, key, targetProperty, sourceProperty, descriptor;
	  if (GLOBAL) {
	    target = global_1;
	  } else if (STATIC) {
	    target = global_1[TARGET] || setGlobal(TARGET, {});
	  } else {
	    target = (global_1[TARGET] || {}).prototype;
	  }
	  if (target) for (key in source) {
	    sourceProperty = source[key];
	    if (options.noTargetGet) {
	      descriptor = getOwnPropertyDescriptor$1(target, key);
	      targetProperty = descriptor && descriptor.value;
	    } else targetProperty = target[key];
	    FORCED = isForced_1(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
	    // contained in target
	    if (!FORCED && targetProperty !== undefined) {
	      if (typeof sourceProperty === typeof targetProperty) continue;
	      copyConstructorProperties(sourceProperty, targetProperty);
	    }
	    // add a flag to not completely full polyfills
	    if (options.sham || (targetProperty && targetProperty.sham)) {
	      hide(sourceProperty, 'sham', true);
	    }
	    // extend global
	    redefine(target, key, sourceProperty, options);
	  }
	};

	var freezing = !fails(function () {
	  return Object.isExtensible(Object.preventExtensions({}));
	});

	var internalMetadata = createCommonjsModule(function (module) {
	var defineProperty = objectDefineProperty.f;



	var METADATA = uid('meta');
	var id = 0;

	var isExtensible = Object.isExtensible || function () {
	  return true;
	};

	var setMetadata = function (it) {
	  defineProperty(it, METADATA, { value: {
	    objectID: 'O' + ++id, // object ID
	    weakData: {}          // weak collections IDs
	  } });
	};

	var fastKey = function (it, create) {
	  // return a primitive with prefix
	  if (!isObject(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
	  if (!has(it, METADATA)) {
	    // can't set metadata to uncaught frozen object
	    if (!isExtensible(it)) return 'F';
	    // not necessary to add metadata
	    if (!create) return 'E';
	    // add missing metadata
	    setMetadata(it);
	  // return object ID
	  } return it[METADATA].objectID;
	};

	var getWeakData = function (it, create) {
	  if (!has(it, METADATA)) {
	    // can't set metadata to uncaught frozen object
	    if (!isExtensible(it)) return true;
	    // not necessary to add metadata
	    if (!create) return false;
	    // add missing metadata
	    setMetadata(it);
	  // return the store of weak collections IDs
	  } return it[METADATA].weakData;
	};

	// add metadata on freeze-family methods calling
	var onFreeze = function (it) {
	  if (freezing && meta.REQUIRED && isExtensible(it) && !has(it, METADATA)) setMetadata(it);
	  return it;
	};

	var meta = module.exports = {
	  REQUIRED: false,
	  fastKey: fastKey,
	  getWeakData: getWeakData,
	  onFreeze: onFreeze
	};

	hiddenKeys[METADATA] = true;
	});
	var internalMetadata_1 = internalMetadata.REQUIRED;
	var internalMetadata_2 = internalMetadata.fastKey;
	var internalMetadata_3 = internalMetadata.getWeakData;
	var internalMetadata_4 = internalMetadata.onFreeze;

	var nativeSymbol = !!Object.getOwnPropertySymbols && !fails(function () {
	  // Chrome 38 Symbol has incorrect toString conversion
	  // eslint-disable-next-line no-undef
	  return !String(Symbol());
	});

	var Symbol$1 = global_1.Symbol;
	var store$1 = shared('wks');

	var wellKnownSymbol = function (name) {
	  return store$1[name] || (store$1[name] = nativeSymbol && Symbol$1[name]
	    || (nativeSymbol ? Symbol$1 : uid)('Symbol.' + name));
	};

	var iterators = {};

	var ITERATOR = wellKnownSymbol('iterator');
	var ArrayPrototype = Array.prototype;

	// check on default Array iterator
	var isArrayIteratorMethod = function (it) {
	  return it !== undefined && (iterators.Array === it || ArrayPrototype[ITERATOR] === it);
	};

	var aFunction$1 = function (it) {
	  if (typeof it != 'function') {
	    throw TypeError(String(it) + ' is not a function');
	  } return it;
	};

	// optional / simple context binding
	var bindContext = function (fn, that, length) {
	  aFunction$1(fn);
	  if (that === undefined) return fn;
	  switch (length) {
	    case 0: return function () {
	      return fn.call(that);
	    };
	    case 1: return function (a) {
	      return fn.call(that, a);
	    };
	    case 2: return function (a, b) {
	      return fn.call(that, a, b);
	    };
	    case 3: return function (a, b, c) {
	      return fn.call(that, a, b, c);
	    };
	  }
	  return function (/* ...args */) {
	    return fn.apply(that, arguments);
	  };
	};

	var TO_STRING_TAG = wellKnownSymbol('toStringTag');
	// ES3 wrong here
	var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) == 'Arguments';

	// fallback for IE11 Script Access Denied error
	var tryGet = function (it, key) {
	  try {
	    return it[key];
	  } catch (error) { /* empty */ }
	};

	// getting tag from ES6+ `Object.prototype.toString`
	var classof = function (it) {
	  var O, tag, result;
	  return it === undefined ? 'Undefined' : it === null ? 'Null'
	    // @@toStringTag case
	    : typeof (tag = tryGet(O = Object(it), TO_STRING_TAG)) == 'string' ? tag
	    // builtinTag case
	    : CORRECT_ARGUMENTS ? classofRaw(O)
	    // ES3 arguments fallback
	    : (result = classofRaw(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : result;
	};

	var ITERATOR$1 = wellKnownSymbol('iterator');

	var getIteratorMethod = function (it) {
	  if (it != undefined) return it[ITERATOR$1]
	    || it['@@iterator']
	    || iterators[classof(it)];
	};

	// call something on iterator step with safe closing on error
	var callWithSafeIterationClosing = function (iterator, fn, value, ENTRIES) {
	  try {
	    return ENTRIES ? fn(anObject(value)[0], value[1]) : fn(value);
	  // 7.4.6 IteratorClose(iterator, completion)
	  } catch (error) {
	    var returnMethod = iterator['return'];
	    if (returnMethod !== undefined) anObject(returnMethod.call(iterator));
	    throw error;
	  }
	};

	var iterate_1 = createCommonjsModule(function (module) {
	var Result = function (stopped, result) {
	  this.stopped = stopped;
	  this.result = result;
	};

	var iterate = module.exports = function (iterable, fn, that, AS_ENTRIES, IS_ITERATOR) {
	  var boundFunction = bindContext(fn, that, AS_ENTRIES ? 2 : 1);
	  var iterator, iterFn, index, length, result, step;

	  if (IS_ITERATOR) {
	    iterator = iterable;
	  } else {
	    iterFn = getIteratorMethod(iterable);
	    if (typeof iterFn != 'function') throw TypeError('Target is not iterable');
	    // optimisation for array iterators
	    if (isArrayIteratorMethod(iterFn)) {
	      for (index = 0, length = toLength(iterable.length); length > index; index++) {
	        result = AS_ENTRIES
	          ? boundFunction(anObject(step = iterable[index])[0], step[1])
	          : boundFunction(iterable[index]);
	        if (result && result instanceof Result) return result;
	      } return new Result(false);
	    }
	    iterator = iterFn.call(iterable);
	  }

	  while (!(step = iterator.next()).done) {
	    result = callWithSafeIterationClosing(iterator, boundFunction, step.value, AS_ENTRIES);
	    if (result && result instanceof Result) return result;
	  } return new Result(false);
	};

	iterate.stop = function (result) {
	  return new Result(true, result);
	};
	});

	var anInstance = function (it, Constructor, name) {
	  if (!(it instanceof Constructor)) {
	    throw TypeError('Incorrect ' + (name ? name + ' ' : '') + 'invocation');
	  } return it;
	};

	var ITERATOR$2 = wellKnownSymbol('iterator');
	var SAFE_CLOSING = false;

	try {
	  var called = 0;
	  var iteratorWithReturn = {
	    next: function () {
	      return { done: !!called++ };
	    },
	    'return': function () {
	      SAFE_CLOSING = true;
	    }
	  };
	  iteratorWithReturn[ITERATOR$2] = function () {
	    return this;
	  };
	  // eslint-disable-next-line no-throw-literal
	  Array.from(iteratorWithReturn, function () { throw 2; });
	} catch (error) { /* empty */ }

	var checkCorrectnessOfIteration = function (exec, SKIP_CLOSING) {
	  if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
	  var ITERATION_SUPPORT = false;
	  try {
	    var object = {};
	    object[ITERATOR$2] = function () {
	      return {
	        next: function () {
	          return { done: ITERATION_SUPPORT = true };
	        }
	      };
	    };
	    exec(object);
	  } catch (error) { /* empty */ }
	  return ITERATION_SUPPORT;
	};

	var defineProperty = objectDefineProperty.f;



	var TO_STRING_TAG$1 = wellKnownSymbol('toStringTag');

	var setToStringTag = function (it, TAG, STATIC) {
	  if (it && !has(it = STATIC ? it : it.prototype, TO_STRING_TAG$1)) {
	    defineProperty(it, TO_STRING_TAG$1, { configurable: true, value: TAG });
	  }
	};

	var aPossiblePrototype = function (it) {
	  if (!isObject(it) && it !== null) {
	    throw TypeError("Can't set " + String(it) + ' as a prototype');
	  } return it;
	};

	// `Object.setPrototypeOf` method
	// https://tc39.github.io/ecma262/#sec-object.setprototypeof
	// Works with __proto__ only. Old v8 can't work with null proto objects.
	/* eslint-disable no-proto */
	var objectSetPrototypeOf = Object.setPrototypeOf || ('__proto__' in {} ? function () {
	  var CORRECT_SETTER = false;
	  var test = {};
	  var setter;
	  try {
	    setter = Object.getOwnPropertyDescriptor(Object.prototype, '__proto__').set;
	    setter.call(test, []);
	    CORRECT_SETTER = test instanceof Array;
	  } catch (error) { /* empty */ }
	  return function setPrototypeOf(O, proto) {
	    anObject(O);
	    aPossiblePrototype(proto);
	    if (CORRECT_SETTER) setter.call(O, proto);
	    else O.__proto__ = proto;
	    return O;
	  };
	}() : undefined);

	// makes subclassing work correct for wrapped built-ins
	var inheritIfRequired = function ($this, dummy, Wrapper) {
	  var NewTarget, NewTargetPrototype;
	  if (
	    // it can work only with native `setPrototypeOf`
	    objectSetPrototypeOf &&
	    // we haven't completely correct pre-ES6 way for getting `new.target`, so use this
	    typeof (NewTarget = dummy.constructor) == 'function' &&
	    NewTarget !== Wrapper &&
	    isObject(NewTargetPrototype = NewTarget.prototype) &&
	    NewTargetPrototype !== Wrapper.prototype
	  ) objectSetPrototypeOf($this, NewTargetPrototype);
	  return $this;
	};

	var collection = function (CONSTRUCTOR_NAME, wrapper, common, IS_MAP, IS_WEAK) {
	  var NativeConstructor = global_1[CONSTRUCTOR_NAME];
	  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
	  var Constructor = NativeConstructor;
	  var ADDER = IS_MAP ? 'set' : 'add';
	  var exported = {};

	  var fixMethod = function (KEY) {
	    var nativeMethod = NativePrototype[KEY];
	    redefine(NativePrototype, KEY,
	      KEY == 'add' ? function add(value) {
	        nativeMethod.call(this, value === 0 ? 0 : value);
	        return this;
	      } : KEY == 'delete' ? function (key) {
	        return IS_WEAK && !isObject(key) ? false : nativeMethod.call(this, key === 0 ? 0 : key);
	      } : KEY == 'get' ? function get(key) {
	        return IS_WEAK && !isObject(key) ? undefined : nativeMethod.call(this, key === 0 ? 0 : key);
	      } : KEY == 'has' ? function has(key) {
	        return IS_WEAK && !isObject(key) ? false : nativeMethod.call(this, key === 0 ? 0 : key);
	      } : function set(key, value) {
	        nativeMethod.call(this, key === 0 ? 0 : key, value);
	        return this;
	      }
	    );
	  };

	  // eslint-disable-next-line max-len
	  if (isForced_1(CONSTRUCTOR_NAME, typeof NativeConstructor != 'function' || !(IS_WEAK || NativePrototype.forEach && !fails(function () {
	    new NativeConstructor().entries().next();
	  })))) {
	    // create collection constructor
	    Constructor = common.getConstructor(wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER);
	    internalMetadata.REQUIRED = true;
	  } else if (isForced_1(CONSTRUCTOR_NAME, true)) {
	    var instance = new Constructor();
	    // early implementations not supports chaining
	    var HASNT_CHAINING = instance[ADDER](IS_WEAK ? {} : -0, 1) != instance;
	    // V8 ~ Chromium 40- weak-collections throws on primitives, but should return false
	    var THROWS_ON_PRIMITIVES = fails(function () { instance.has(1); });
	    // most early implementations doesn't supports iterables, most modern - not close it correctly
	    // eslint-disable-next-line no-new
	    var ACCEPT_ITERABLES = checkCorrectnessOfIteration(function (iterable) { new NativeConstructor(iterable); });
	    // for early implementations -0 and +0 not the same
	    var BUGGY_ZERO = !IS_WEAK && fails(function () {
	      // V8 ~ Chromium 42- fails only with 5+ elements
	      var $instance = new NativeConstructor();
	      var index = 5;
	      while (index--) $instance[ADDER](index, index);
	      return !$instance.has(-0);
	    });

	    if (!ACCEPT_ITERABLES) {
	      Constructor = wrapper(function (dummy, iterable) {
	        anInstance(dummy, Constructor, CONSTRUCTOR_NAME);
	        var that = inheritIfRequired(new NativeConstructor(), dummy, Constructor);
	        if (iterable != undefined) iterate_1(iterable, that[ADDER], that, IS_MAP);
	        return that;
	      });
	      Constructor.prototype = NativePrototype;
	      NativePrototype.constructor = Constructor;
	    }

	    if (THROWS_ON_PRIMITIVES || BUGGY_ZERO) {
	      fixMethod('delete');
	      fixMethod('has');
	      IS_MAP && fixMethod('get');
	    }

	    if (BUGGY_ZERO || HASNT_CHAINING) fixMethod(ADDER);

	    // weak collections should not contains .clear method
	    if (IS_WEAK && NativePrototype.clear) delete NativePrototype.clear;
	  }

	  exported[CONSTRUCTOR_NAME] = Constructor;
	  _export({ global: true, forced: Constructor != NativeConstructor }, exported);

	  setToStringTag(Constructor, CONSTRUCTOR_NAME);

	  if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

	  return Constructor;
	};

	// `Object.keys` method
	// https://tc39.github.io/ecma262/#sec-object.keys
	var objectKeys = Object.keys || function keys(O) {
	  return objectKeysInternal(O, enumBugKeys);
	};

	// `Object.defineProperties` method
	// https://tc39.github.io/ecma262/#sec-object.defineproperties
	var objectDefineProperties = descriptors ? Object.defineProperties : function defineProperties(O, Properties) {
	  anObject(O);
	  var keys = objectKeys(Properties);
	  var length = keys.length;
	  var index = 0;
	  var key;
	  while (length > index) objectDefineProperty.f(O, key = keys[index++], Properties[key]);
	  return O;
	};

	var html = getBuiltIn('document', 'documentElement');

	var IE_PROTO = sharedKey('IE_PROTO');

	var PROTOTYPE = 'prototype';
	var Empty = function () { /* empty */ };

	// Create object with fake `null` prototype: use iframe Object with cleared prototype
	var createDict = function () {
	  // Thrash, waste and sodomy: IE GC bug
	  var iframe = documentCreateElement('iframe');
	  var length = enumBugKeys.length;
	  var lt = '<';
	  var script = 'script';
	  var gt = '>';
	  var js = 'java' + script + ':';
	  var iframeDocument;
	  iframe.style.display = 'none';
	  html.appendChild(iframe);
	  iframe.src = String(js);
	  iframeDocument = iframe.contentWindow.document;
	  iframeDocument.open();
	  iframeDocument.write(lt + script + gt + 'document.F=Object' + lt + '/' + script + gt);
	  iframeDocument.close();
	  createDict = iframeDocument.F;
	  while (length--) delete createDict[PROTOTYPE][enumBugKeys[length]];
	  return createDict();
	};

	// `Object.create` method
	// https://tc39.github.io/ecma262/#sec-object.create
	var objectCreate = Object.create || function create(O, Properties) {
	  var result;
	  if (O !== null) {
	    Empty[PROTOTYPE] = anObject(O);
	    result = new Empty();
	    Empty[PROTOTYPE] = null;
	    // add "__proto__" for Object.getPrototypeOf polyfill
	    result[IE_PROTO] = O;
	  } else result = createDict();
	  return Properties === undefined ? result : objectDefineProperties(result, Properties);
	};

	hiddenKeys[IE_PROTO] = true;

	var redefineAll = function (target, src, options) {
	  for (var key in src) redefine(target, key, src[key], options);
	  return target;
	};

	// `ToObject` abstract operation
	// https://tc39.github.io/ecma262/#sec-toobject
	var toObject = function (argument) {
	  return Object(requireObjectCoercible(argument));
	};

	var correctPrototypeGetter = !fails(function () {
	  function F() { /* empty */ }
	  F.prototype.constructor = null;
	  return Object.getPrototypeOf(new F()) !== F.prototype;
	});

	var IE_PROTO$1 = sharedKey('IE_PROTO');
	var ObjectPrototype = Object.prototype;

	// `Object.getPrototypeOf` method
	// https://tc39.github.io/ecma262/#sec-object.getprototypeof
	var objectGetPrototypeOf = correctPrototypeGetter ? Object.getPrototypeOf : function (O) {
	  O = toObject(O);
	  if (has(O, IE_PROTO$1)) return O[IE_PROTO$1];
	  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
	    return O.constructor.prototype;
	  } return O instanceof Object ? ObjectPrototype : null;
	};

	var ITERATOR$3 = wellKnownSymbol('iterator');
	var BUGGY_SAFARI_ITERATORS = false;

	var returnThis = function () { return this; };

	// `%IteratorPrototype%` object
	// https://tc39.github.io/ecma262/#sec-%iteratorprototype%-object
	var IteratorPrototype, PrototypeOfArrayIteratorPrototype, arrayIterator;

	if ([].keys) {
	  arrayIterator = [].keys();
	  // Safari 8 has buggy iterators w/o `next`
	  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS = true;
	  else {
	    PrototypeOfArrayIteratorPrototype = objectGetPrototypeOf(objectGetPrototypeOf(arrayIterator));
	    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype = PrototypeOfArrayIteratorPrototype;
	  }
	}

	if (IteratorPrototype == undefined) IteratorPrototype = {};

	// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
	if ( !has(IteratorPrototype, ITERATOR$3)) hide(IteratorPrototype, ITERATOR$3, returnThis);

	var iteratorsCore = {
	  IteratorPrototype: IteratorPrototype,
	  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS
	};

	var IteratorPrototype$1 = iteratorsCore.IteratorPrototype;





	var returnThis$1 = function () { return this; };

	var createIteratorConstructor = function (IteratorConstructor, NAME, next) {
	  var TO_STRING_TAG = NAME + ' Iterator';
	  IteratorConstructor.prototype = objectCreate(IteratorPrototype$1, { next: createPropertyDescriptor(1, next) });
	  setToStringTag(IteratorConstructor, TO_STRING_TAG, false);
	  iterators[TO_STRING_TAG] = returnThis$1;
	  return IteratorConstructor;
	};

	var IteratorPrototype$2 = iteratorsCore.IteratorPrototype;
	var BUGGY_SAFARI_ITERATORS$1 = iteratorsCore.BUGGY_SAFARI_ITERATORS;
	var ITERATOR$4 = wellKnownSymbol('iterator');
	var KEYS = 'keys';
	var VALUES = 'values';
	var ENTRIES = 'entries';

	var returnThis$2 = function () { return this; };

	var defineIterator = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
	  createIteratorConstructor(IteratorConstructor, NAME, next);

	  var getIterationMethod = function (KIND) {
	    if (KIND === DEFAULT && defaultIterator) return defaultIterator;
	    if (!BUGGY_SAFARI_ITERATORS$1 && KIND in IterablePrototype) return IterablePrototype[KIND];
	    switch (KIND) {
	      case KEYS: return function keys() { return new IteratorConstructor(this, KIND); };
	      case VALUES: return function values() { return new IteratorConstructor(this, KIND); };
	      case ENTRIES: return function entries() { return new IteratorConstructor(this, KIND); };
	    } return function () { return new IteratorConstructor(this); };
	  };

	  var TO_STRING_TAG = NAME + ' Iterator';
	  var INCORRECT_VALUES_NAME = false;
	  var IterablePrototype = Iterable.prototype;
	  var nativeIterator = IterablePrototype[ITERATOR$4]
	    || IterablePrototype['@@iterator']
	    || DEFAULT && IterablePrototype[DEFAULT];
	  var defaultIterator = !BUGGY_SAFARI_ITERATORS$1 && nativeIterator || getIterationMethod(DEFAULT);
	  var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
	  var CurrentIteratorPrototype, methods, KEY;

	  // fix native
	  if (anyNativeIterator) {
	    CurrentIteratorPrototype = objectGetPrototypeOf(anyNativeIterator.call(new Iterable()));
	    if (IteratorPrototype$2 !== Object.prototype && CurrentIteratorPrototype.next) {
	      if ( objectGetPrototypeOf(CurrentIteratorPrototype) !== IteratorPrototype$2) {
	        if (objectSetPrototypeOf) {
	          objectSetPrototypeOf(CurrentIteratorPrototype, IteratorPrototype$2);
	        } else if (typeof CurrentIteratorPrototype[ITERATOR$4] != 'function') {
	          hide(CurrentIteratorPrototype, ITERATOR$4, returnThis$2);
	        }
	      }
	      // Set @@toStringTag to native iterators
	      setToStringTag(CurrentIteratorPrototype, TO_STRING_TAG, true);
	    }
	  }

	  // fix Array#{values, @@iterator}.name in V8 / FF
	  if (DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
	    INCORRECT_VALUES_NAME = true;
	    defaultIterator = function values() { return nativeIterator.call(this); };
	  }

	  // define iterator
	  if ( IterablePrototype[ITERATOR$4] !== defaultIterator) {
	    hide(IterablePrototype, ITERATOR$4, defaultIterator);
	  }
	  iterators[NAME] = defaultIterator;

	  // export additional methods
	  if (DEFAULT) {
	    methods = {
	      values: getIterationMethod(VALUES),
	      keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
	      entries: getIterationMethod(ENTRIES)
	    };
	    if (FORCED) for (KEY in methods) {
	      if (BUGGY_SAFARI_ITERATORS$1 || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
	        redefine(IterablePrototype, KEY, methods[KEY]);
	      }
	    } else _export({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS$1 || INCORRECT_VALUES_NAME }, methods);
	  }

	  return methods;
	};

	var SPECIES = wellKnownSymbol('species');

	var setSpecies = function (CONSTRUCTOR_NAME) {
	  var Constructor = getBuiltIn(CONSTRUCTOR_NAME);
	  var defineProperty = objectDefineProperty.f;

	  if (descriptors && Constructor && !Constructor[SPECIES]) {
	    defineProperty(Constructor, SPECIES, {
	      configurable: true,
	      get: function () { return this; }
	    });
	  }
	};

	var defineProperty$1 = objectDefineProperty.f;








	var fastKey = internalMetadata.fastKey;


	var setInternalState = internalState.set;
	var internalStateGetterFor = internalState.getterFor;

	var collectionStrong = {
	  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
	    var C = wrapper(function (that, iterable) {
	      anInstance(that, C, CONSTRUCTOR_NAME);
	      setInternalState(that, {
	        type: CONSTRUCTOR_NAME,
	        index: objectCreate(null),
	        first: undefined,
	        last: undefined,
	        size: 0
	      });
	      if (!descriptors) that.size = 0;
	      if (iterable != undefined) iterate_1(iterable, that[ADDER], that, IS_MAP);
	    });

	    var getInternalState = internalStateGetterFor(CONSTRUCTOR_NAME);

	    var define = function (that, key, value) {
	      var state = getInternalState(that);
	      var entry = getEntry(that, key);
	      var previous, index;
	      // change existing entry
	      if (entry) {
	        entry.value = value;
	      // create new entry
	      } else {
	        state.last = entry = {
	          index: index = fastKey(key, true),
	          key: key,
	          value: value,
	          previous: previous = state.last,
	          next: undefined,
	          removed: false
	        };
	        if (!state.first) state.first = entry;
	        if (previous) previous.next = entry;
	        if (descriptors) state.size++;
	        else that.size++;
	        // add to index
	        if (index !== 'F') state.index[index] = entry;
	      } return that;
	    };

	    var getEntry = function (that, key) {
	      var state = getInternalState(that);
	      // fast case
	      var index = fastKey(key);
	      var entry;
	      if (index !== 'F') return state.index[index];
	      // frozen object case
	      for (entry = state.first; entry; entry = entry.next) {
	        if (entry.key == key) return entry;
	      }
	    };

	    redefineAll(C.prototype, {
	      // 23.1.3.1 Map.prototype.clear()
	      // 23.2.3.2 Set.prototype.clear()
	      clear: function clear() {
	        var that = this;
	        var state = getInternalState(that);
	        var data = state.index;
	        var entry = state.first;
	        while (entry) {
	          entry.removed = true;
	          if (entry.previous) entry.previous = entry.previous.next = undefined;
	          delete data[entry.index];
	          entry = entry.next;
	        }
	        state.first = state.last = undefined;
	        if (descriptors) state.size = 0;
	        else that.size = 0;
	      },
	      // 23.1.3.3 Map.prototype.delete(key)
	      // 23.2.3.4 Set.prototype.delete(value)
	      'delete': function (key) {
	        var that = this;
	        var state = getInternalState(that);
	        var entry = getEntry(that, key);
	        if (entry) {
	          var next = entry.next;
	          var prev = entry.previous;
	          delete state.index[entry.index];
	          entry.removed = true;
	          if (prev) prev.next = next;
	          if (next) next.previous = prev;
	          if (state.first == entry) state.first = next;
	          if (state.last == entry) state.last = prev;
	          if (descriptors) state.size--;
	          else that.size--;
	        } return !!entry;
	      },
	      // 23.2.3.6 Set.prototype.forEach(callbackfn, thisArg = undefined)
	      // 23.1.3.5 Map.prototype.forEach(callbackfn, thisArg = undefined)
	      forEach: function forEach(callbackfn /* , that = undefined */) {
	        var state = getInternalState(this);
	        var boundFunction = bindContext(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
	        var entry;
	        while (entry = entry ? entry.next : state.first) {
	          boundFunction(entry.value, entry.key, this);
	          // revert to the last existing entry
	          while (entry && entry.removed) entry = entry.previous;
	        }
	      },
	      // 23.1.3.7 Map.prototype.has(key)
	      // 23.2.3.7 Set.prototype.has(value)
	      has: function has(key) {
	        return !!getEntry(this, key);
	      }
	    });

	    redefineAll(C.prototype, IS_MAP ? {
	      // 23.1.3.6 Map.prototype.get(key)
	      get: function get(key) {
	        var entry = getEntry(this, key);
	        return entry && entry.value;
	      },
	      // 23.1.3.9 Map.prototype.set(key, value)
	      set: function set(key, value) {
	        return define(this, key === 0 ? 0 : key, value);
	      }
	    } : {
	      // 23.2.3.1 Set.prototype.add(value)
	      add: function add(value) {
	        return define(this, value = value === 0 ? 0 : value, value);
	      }
	    });
	    if (descriptors) defineProperty$1(C.prototype, 'size', {
	      get: function () {
	        return getInternalState(this).size;
	      }
	    });
	    return C;
	  },
	  setStrong: function (C, CONSTRUCTOR_NAME, IS_MAP) {
	    var ITERATOR_NAME = CONSTRUCTOR_NAME + ' Iterator';
	    var getInternalCollectionState = internalStateGetterFor(CONSTRUCTOR_NAME);
	    var getInternalIteratorState = internalStateGetterFor(ITERATOR_NAME);
	    // add .keys, .values, .entries, [@@iterator]
	    // 23.1.3.4, 23.1.3.8, 23.1.3.11, 23.1.3.12, 23.2.3.5, 23.2.3.8, 23.2.3.10, 23.2.3.11
	    defineIterator(C, CONSTRUCTOR_NAME, function (iterated, kind) {
	      setInternalState(this, {
	        type: ITERATOR_NAME,
	        target: iterated,
	        state: getInternalCollectionState(iterated),
	        kind: kind,
	        last: undefined
	      });
	    }, function () {
	      var state = getInternalIteratorState(this);
	      var kind = state.kind;
	      var entry = state.last;
	      // revert to the last existing entry
	      while (entry && entry.removed) entry = entry.previous;
	      // get next entry
	      if (!state.target || !(state.last = entry = entry ? entry.next : state.state.first)) {
	        // or finish the iteration
	        state.target = undefined;
	        return { value: undefined, done: true };
	      }
	      // return step by kind
	      if (kind == 'keys') return { value: entry.key, done: false };
	      if (kind == 'values') return { value: entry.value, done: false };
	      return { value: [entry.key, entry.value], done: false };
	    }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

	    // add [@@species], 23.1.2.2, 23.2.2.2
	    setSpecies(CONSTRUCTOR_NAME);
	  }
	};

	// `Map` constructor
	// https://tc39.github.io/ecma262/#sec-map-objects
	var es_map = collection('Map', function (get) {
	  return function Map() { return get(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong, true);

	var TO_STRING_TAG$2 = wellKnownSymbol('toStringTag');
	var test = {};

	test[TO_STRING_TAG$2] = 'z';

	// `Object.prototype.toString` method implementation
	// https://tc39.github.io/ecma262/#sec-object.prototype.tostring
	var objectToString = String(test) !== '[object z]' ? function toString() {
	  return '[object ' + classof(this) + ']';
	} : test.toString;

	var ObjectPrototype$1 = Object.prototype;

	// `Object.prototype.toString` method
	// https://tc39.github.io/ecma262/#sec-object.prototype.tostring
	if (objectToString !== ObjectPrototype$1.toString) {
	  redefine(ObjectPrototype$1, 'toString', objectToString, { unsafe: true });
	}

	// `String.prototype.{ codePointAt, at }` methods implementation
	var createMethod$1 = function (CONVERT_TO_STRING) {
	  return function ($this, pos) {
	    var S = String(requireObjectCoercible($this));
	    var position = toInteger(pos);
	    var size = S.length;
	    var first, second;
	    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
	    first = S.charCodeAt(position);
	    return first < 0xD800 || first > 0xDBFF || position + 1 === size
	      || (second = S.charCodeAt(position + 1)) < 0xDC00 || second > 0xDFFF
	        ? CONVERT_TO_STRING ? S.charAt(position) : first
	        : CONVERT_TO_STRING ? S.slice(position, position + 2) : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
	  };
	};

	var stringMultibyte = {
	  // `String.prototype.codePointAt` method
	  // https://tc39.github.io/ecma262/#sec-string.prototype.codepointat
	  codeAt: createMethod$1(false),
	  // `String.prototype.at` method
	  // https://github.com/mathiasbynens/String.prototype.at
	  charAt: createMethod$1(true)
	};

	var charAt = stringMultibyte.charAt;



	var STRING_ITERATOR = 'String Iterator';
	var setInternalState$1 = internalState.set;
	var getInternalState = internalState.getterFor(STRING_ITERATOR);

	// `String.prototype[@@iterator]` method
	// https://tc39.github.io/ecma262/#sec-string.prototype-@@iterator
	defineIterator(String, 'String', function (iterated) {
	  setInternalState$1(this, {
	    type: STRING_ITERATOR,
	    string: String(iterated),
	    index: 0
	  });
	// `%StringIteratorPrototype%.next` method
	// https://tc39.github.io/ecma262/#sec-%stringiteratorprototype%.next
	}, function next() {
	  var state = getInternalState(this);
	  var string = state.string;
	  var index = state.index;
	  var point;
	  if (index >= string.length) return { value: undefined, done: true };
	  point = charAt(string, index);
	  state.index += point.length;
	  return { value: point, done: false };
	});

	// iterable DOM collections
	// flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
	var domIterables = {
	  CSSRuleList: 0,
	  CSSStyleDeclaration: 0,
	  CSSValueList: 0,
	  ClientRectList: 0,
	  DOMRectList: 0,
	  DOMStringList: 0,
	  DOMTokenList: 1,
	  DataTransferItemList: 0,
	  FileList: 0,
	  HTMLAllCollection: 0,
	  HTMLCollection: 0,
	  HTMLFormElement: 0,
	  HTMLSelectElement: 0,
	  MediaList: 0,
	  MimeTypeArray: 0,
	  NamedNodeMap: 0,
	  NodeList: 1,
	  PaintRequestList: 0,
	  Plugin: 0,
	  PluginArray: 0,
	  SVGLengthList: 0,
	  SVGNumberList: 0,
	  SVGPathSegList: 0,
	  SVGPointList: 0,
	  SVGStringList: 0,
	  SVGTransformList: 0,
	  SourceBufferList: 0,
	  StyleSheetList: 0,
	  TextTrackCueList: 0,
	  TextTrackList: 0,
	  TouchList: 0
	};

	var UNSCOPABLES = wellKnownSymbol('unscopables');
	var ArrayPrototype$1 = Array.prototype;

	// Array.prototype[@@unscopables]
	// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
	if (ArrayPrototype$1[UNSCOPABLES] == undefined) {
	  hide(ArrayPrototype$1, UNSCOPABLES, objectCreate(null));
	}

	// add a key to Array.prototype[@@unscopables]
	var addToUnscopables = function (key) {
	  ArrayPrototype$1[UNSCOPABLES][key] = true;
	};

	var ARRAY_ITERATOR = 'Array Iterator';
	var setInternalState$2 = internalState.set;
	var getInternalState$1 = internalState.getterFor(ARRAY_ITERATOR);

	// `Array.prototype.entries` method
	// https://tc39.github.io/ecma262/#sec-array.prototype.entries
	// `Array.prototype.keys` method
	// https://tc39.github.io/ecma262/#sec-array.prototype.keys
	// `Array.prototype.values` method
	// https://tc39.github.io/ecma262/#sec-array.prototype.values
	// `Array.prototype[@@iterator]` method
	// https://tc39.github.io/ecma262/#sec-array.prototype-@@iterator
	// `CreateArrayIterator` internal method
	// https://tc39.github.io/ecma262/#sec-createarrayiterator
	var es_array_iterator = defineIterator(Array, 'Array', function (iterated, kind) {
	  setInternalState$2(this, {
	    type: ARRAY_ITERATOR,
	    target: toIndexedObject(iterated), // target
	    index: 0,                          // next index
	    kind: kind                         // kind
	  });
	// `%ArrayIteratorPrototype%.next` method
	// https://tc39.github.io/ecma262/#sec-%arrayiteratorprototype%.next
	}, function () {
	  var state = getInternalState$1(this);
	  var target = state.target;
	  var kind = state.kind;
	  var index = state.index++;
	  if (!target || index >= target.length) {
	    state.target = undefined;
	    return { value: undefined, done: true };
	  }
	  if (kind == 'keys') return { value: index, done: false };
	  if (kind == 'values') return { value: target[index], done: false };
	  return { value: [index, target[index]], done: false };
	}, 'values');

	// argumentsList[@@iterator] is %ArrayProto_values%
	// https://tc39.github.io/ecma262/#sec-createunmappedargumentsobject
	// https://tc39.github.io/ecma262/#sec-createmappedargumentsobject
	iterators.Arguments = iterators.Array;

	// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables('keys');
	addToUnscopables('values');
	addToUnscopables('entries');

	var ITERATOR$5 = wellKnownSymbol('iterator');
	var TO_STRING_TAG$3 = wellKnownSymbol('toStringTag');
	var ArrayValues = es_array_iterator.values;

	for (var COLLECTION_NAME in domIterables) {
	  var Collection = global_1[COLLECTION_NAME];
	  var CollectionPrototype = Collection && Collection.prototype;
	  if (CollectionPrototype) {
	    // some Chrome versions have non-configurable methods on DOMTokenList
	    if (CollectionPrototype[ITERATOR$5] !== ArrayValues) try {
	      hide(CollectionPrototype, ITERATOR$5, ArrayValues);
	    } catch (error) {
	      CollectionPrototype[ITERATOR$5] = ArrayValues;
	    }
	    if (!CollectionPrototype[TO_STRING_TAG$3]) hide(CollectionPrototype, TO_STRING_TAG$3, COLLECTION_NAME);
	    if (domIterables[COLLECTION_NAME]) for (var METHOD_NAME in es_array_iterator) {
	      // some Chrome versions have non-configurable methods on DOMTokenList
	      if (CollectionPrototype[METHOD_NAME] !== es_array_iterator[METHOD_NAME]) try {
	        hide(CollectionPrototype, METHOD_NAME, es_array_iterator[METHOD_NAME]);
	      } catch (error) {
	        CollectionPrototype[METHOD_NAME] = es_array_iterator[METHOD_NAME];
	      }
	    }
	  }
	}

	var map = path.Map;

	// `IsArray` abstract operation
	// https://tc39.github.io/ecma262/#sec-isarray
	var isArray = Array.isArray || function isArray(arg) {
	  return classofRaw(arg) == 'Array';
	};

	var SPECIES$1 = wellKnownSymbol('species');

	// `ArraySpeciesCreate` abstract operation
	// https://tc39.github.io/ecma262/#sec-arrayspeciescreate
	var arraySpeciesCreate = function (originalArray, length) {
	  var C;
	  if (isArray(originalArray)) {
	    C = originalArray.constructor;
	    // cross-realm fallback
	    if (typeof C == 'function' && (C === Array || isArray(C.prototype))) C = undefined;
	    else if (isObject(C)) {
	      C = C[SPECIES$1];
	      if (C === null) C = undefined;
	    }
	  } return new (C === undefined ? Array : C)(length === 0 ? 0 : length);
	};

	var push = [].push;

	// `Array.prototype.{ forEach, map, filter, some, every, find, findIndex }` methods implementation
	var createMethod$2 = function (TYPE) {
	  var IS_MAP = TYPE == 1;
	  var IS_FILTER = TYPE == 2;
	  var IS_SOME = TYPE == 3;
	  var IS_EVERY = TYPE == 4;
	  var IS_FIND_INDEX = TYPE == 6;
	  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
	  return function ($this, callbackfn, that, specificCreate) {
	    var O = toObject($this);
	    var self = indexedObject(O);
	    var boundFunction = bindContext(callbackfn, that, 3);
	    var length = toLength(self.length);
	    var index = 0;
	    var create = specificCreate || arraySpeciesCreate;
	    var target = IS_MAP ? create($this, length) : IS_FILTER ? create($this, 0) : undefined;
	    var value, result;
	    for (;length > index; index++) if (NO_HOLES || index in self) {
	      value = self[index];
	      result = boundFunction(value, index, O);
	      if (TYPE) {
	        if (IS_MAP) target[index] = result; // map
	        else if (result) switch (TYPE) {
	          case 3: return true;              // some
	          case 5: return value;             // find
	          case 6: return index;             // findIndex
	          case 2: push.call(target, value); // filter
	        } else if (IS_EVERY) return false;  // every
	      }
	    }
	    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
	  };
	};

	var arrayIteration = {
	  // `Array.prototype.forEach` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.foreach
	  forEach: createMethod$2(0),
	  // `Array.prototype.map` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.map
	  map: createMethod$2(1),
	  // `Array.prototype.filter` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.filter
	  filter: createMethod$2(2),
	  // `Array.prototype.some` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.some
	  some: createMethod$2(3),
	  // `Array.prototype.every` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.every
	  every: createMethod$2(4),
	  // `Array.prototype.find` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.find
	  find: createMethod$2(5),
	  // `Array.prototype.findIndex` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.findIndex
	  findIndex: createMethod$2(6)
	};

	var getWeakData = internalMetadata.getWeakData;








	var setInternalState$3 = internalState.set;
	var internalStateGetterFor$1 = internalState.getterFor;
	var find = arrayIteration.find;
	var findIndex = arrayIteration.findIndex;
	var id$1 = 0;

	// fallback for uncaught frozen keys
	var uncaughtFrozenStore = function (store) {
	  return store.frozen || (store.frozen = new UncaughtFrozenStore());
	};

	var UncaughtFrozenStore = function () {
	  this.entries = [];
	};

	var findUncaughtFrozen = function (store, key) {
	  return find(store.entries, function (it) {
	    return it[0] === key;
	  });
	};

	UncaughtFrozenStore.prototype = {
	  get: function (key) {
	    var entry = findUncaughtFrozen(this, key);
	    if (entry) return entry[1];
	  },
	  has: function (key) {
	    return !!findUncaughtFrozen(this, key);
	  },
	  set: function (key, value) {
	    var entry = findUncaughtFrozen(this, key);
	    if (entry) entry[1] = value;
	    else this.entries.push([key, value]);
	  },
	  'delete': function (key) {
	    var index = findIndex(this.entries, function (it) {
	      return it[0] === key;
	    });
	    if (~index) this.entries.splice(index, 1);
	    return !!~index;
	  }
	};

	var collectionWeak = {
	  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
	    var C = wrapper(function (that, iterable) {
	      anInstance(that, C, CONSTRUCTOR_NAME);
	      setInternalState$3(that, {
	        type: CONSTRUCTOR_NAME,
	        id: id$1++,
	        frozen: undefined
	      });
	      if (iterable != undefined) iterate_1(iterable, that[ADDER], that, IS_MAP);
	    });

	    var getInternalState = internalStateGetterFor$1(CONSTRUCTOR_NAME);

	    var define = function (that, key, value) {
	      var state = getInternalState(that);
	      var data = getWeakData(anObject(key), true);
	      if (data === true) uncaughtFrozenStore(state).set(key, value);
	      else data[state.id] = value;
	      return that;
	    };

	    redefineAll(C.prototype, {
	      // 23.3.3.2 WeakMap.prototype.delete(key)
	      // 23.4.3.3 WeakSet.prototype.delete(value)
	      'delete': function (key) {
	        var state = getInternalState(this);
	        if (!isObject(key)) return false;
	        var data = getWeakData(key);
	        if (data === true) return uncaughtFrozenStore(state)['delete'](key);
	        return data && has(data, state.id) && delete data[state.id];
	      },
	      // 23.3.3.4 WeakMap.prototype.has(key)
	      // 23.4.3.4 WeakSet.prototype.has(value)
	      has: function has$1(key) {
	        var state = getInternalState(this);
	        if (!isObject(key)) return false;
	        var data = getWeakData(key);
	        if (data === true) return uncaughtFrozenStore(state).has(key);
	        return data && has(data, state.id);
	      }
	    });

	    redefineAll(C.prototype, IS_MAP ? {
	      // 23.3.3.3 WeakMap.prototype.get(key)
	      get: function get(key) {
	        var state = getInternalState(this);
	        if (isObject(key)) {
	          var data = getWeakData(key);
	          if (data === true) return uncaughtFrozenStore(state).get(key);
	          return data ? data[state.id] : undefined;
	        }
	      },
	      // 23.3.3.5 WeakMap.prototype.set(key, value)
	      set: function set(key, value) {
	        return define(this, key, value);
	      }
	    } : {
	      // 23.4.3.1 WeakSet.prototype.add(value)
	      add: function add(value) {
	        return define(this, value, true);
	      }
	    });

	    return C;
	  }
	};

	var es_weakMap = createCommonjsModule(function (module) {






	var enforceIternalState = internalState.enforce;


	var IS_IE11 = !global_1.ActiveXObject && 'ActiveXObject' in global_1;
	var isExtensible = Object.isExtensible;
	var InternalWeakMap;

	var wrapper = function (get) {
	  return function WeakMap() {
	    return get(this, arguments.length ? arguments[0] : undefined);
	  };
	};

	// `WeakMap` constructor
	// https://tc39.github.io/ecma262/#sec-weakmap-constructor
	var $WeakMap = module.exports = collection('WeakMap', wrapper, collectionWeak, true, true);

	// IE11 WeakMap frozen keys fix
	// We can't use feature detection because it crash some old IE builds
	// https://github.com/zloirock/core-js/issues/485
	if (nativeWeakMap && IS_IE11) {
	  InternalWeakMap = collectionWeak.getConstructor(wrapper, 'WeakMap', true);
	  internalMetadata.REQUIRED = true;
	  var WeakMapPrototype = $WeakMap.prototype;
	  var nativeDelete = WeakMapPrototype['delete'];
	  var nativeHas = WeakMapPrototype.has;
	  var nativeGet = WeakMapPrototype.get;
	  var nativeSet = WeakMapPrototype.set;
	  redefineAll(WeakMapPrototype, {
	    'delete': function (key) {
	      if (isObject(key) && !isExtensible(key)) {
	        var state = enforceIternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        return nativeDelete.call(this, key) || state.frozen['delete'](key);
	      } return nativeDelete.call(this, key);
	    },
	    has: function has(key) {
	      if (isObject(key) && !isExtensible(key)) {
	        var state = enforceIternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        return nativeHas.call(this, key) || state.frozen.has(key);
	      } return nativeHas.call(this, key);
	    },
	    get: function get(key) {
	      if (isObject(key) && !isExtensible(key)) {
	        var state = enforceIternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        return nativeHas.call(this, key) ? nativeGet.call(this, key) : state.frozen.get(key);
	      } return nativeGet.call(this, key);
	    },
	    set: function set(key, value) {
	      if (isObject(key) && !isExtensible(key)) {
	        var state = enforceIternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        nativeHas.call(this, key) ? nativeSet.call(this, key, value) : state.frozen.set(key, value);
	      } else nativeSet.call(this, key, value);
	      return this;
	    }
	  });
	}
	});

	var weakMap = path.WeakMap;

	// `Set` constructor
	// https://tc39.github.io/ecma262/#sec-set-objects
	var es_set = collection('Set', function (get) {
	  return function Set() { return get(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong);

	var set$1 = path.Set;

	// `WeakSet` constructor
	// https://tc39.github.io/ecma262/#sec-weakset-constructor
	collection('WeakSet', function (get) {
	  return function WeakSet() { return get(this, arguments.length ? arguments[0] : undefined); };
	}, collectionWeak, false, true);

	var weakSet = path.WeakSet;

	var es2015Collection = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2015Collection);

	var $find = arrayIteration.find;


	var FIND = 'find';
	var SKIPS_HOLES = true;

	// Shouldn't skip holes
	if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES = false; });

	// `Array.prototype.find` method
	// https://tc39.github.io/ecma262/#sec-array.prototype.find
	_export({ target: 'Array', proto: true, forced: SKIPS_HOLES }, {
	  find: function find(callbackfn /* , that = undefined */) {
	    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables(FIND);

	var call = Function.call;

	var entryUnbind = function (CONSTRUCTOR, METHOD, length) {
	  return bindContext(call, global_1[CONSTRUCTOR].prototype[METHOD], length);
	};

	var find$1 = entryUnbind('Array', 'find');

	var defineProperty$2 = objectDefineProperty.f;





	var DataView = global_1.DataView;
	var DataViewPrototype = DataView && DataView.prototype;
	var Int8Array$1 = global_1.Int8Array;
	var Int8ArrayPrototype = Int8Array$1 && Int8Array$1.prototype;
	var Uint8ClampedArray = global_1.Uint8ClampedArray;
	var Uint8ClampedArrayPrototype = Uint8ClampedArray && Uint8ClampedArray.prototype;
	var TypedArray = Int8Array$1 && objectGetPrototypeOf(Int8Array$1);
	var TypedArrayPrototype = Int8ArrayPrototype && objectGetPrototypeOf(Int8ArrayPrototype);
	var ObjectPrototype$2 = Object.prototype;
	var isPrototypeOf = ObjectPrototype$2.isPrototypeOf;

	var TO_STRING_TAG$4 = wellKnownSymbol('toStringTag');
	var TYPED_ARRAY_TAG = uid('TYPED_ARRAY_TAG');
	var NATIVE_ARRAY_BUFFER = !!(global_1.ArrayBuffer && DataView);
	// Fixing native typed arrays in Opera Presto crashes the browser, see #595
	var NATIVE_ARRAY_BUFFER_VIEWS = NATIVE_ARRAY_BUFFER && !!objectSetPrototypeOf && classof(global_1.opera) !== 'Opera';
	var TYPED_ARRAY_TAG_REQIRED = false;
	var NAME;

	var TypedArrayConstructorsList = {
	  Int8Array: 1,
	  Uint8Array: 1,
	  Uint8ClampedArray: 1,
	  Int16Array: 2,
	  Uint16Array: 2,
	  Int32Array: 4,
	  Uint32Array: 4,
	  Float32Array: 4,
	  Float64Array: 8
	};

	var isView = function isView(it) {
	  var klass = classof(it);
	  return klass === 'DataView' || has(TypedArrayConstructorsList, klass);
	};

	var isTypedArray = function (it) {
	  return isObject(it) && has(TypedArrayConstructorsList, classof(it));
	};

	var aTypedArray = function (it) {
	  if (isTypedArray(it)) return it;
	  throw TypeError('Target is not a typed array');
	};

	var aTypedArrayConstructor = function (C) {
	  if (objectSetPrototypeOf) {
	    if (isPrototypeOf.call(TypedArray, C)) return C;
	  } else for (var ARRAY in TypedArrayConstructorsList) if (has(TypedArrayConstructorsList, NAME)) {
	    var TypedArrayConstructor = global_1[ARRAY];
	    if (TypedArrayConstructor && (C === TypedArrayConstructor || isPrototypeOf.call(TypedArrayConstructor, C))) {
	      return C;
	    }
	  } throw TypeError('Target is not a typed array constructor');
	};

	var exportProto = function (KEY, property, forced) {
	  if (!descriptors) return;
	  if (forced) for (var ARRAY in TypedArrayConstructorsList) {
	    var TypedArrayConstructor = global_1[ARRAY];
	    if (TypedArrayConstructor && has(TypedArrayConstructor.prototype, KEY)) {
	      delete TypedArrayConstructor.prototype[KEY];
	    }
	  }
	  if (!TypedArrayPrototype[KEY] || forced) {
	    redefine(TypedArrayPrototype, KEY, forced ? property
	      : NATIVE_ARRAY_BUFFER_VIEWS && Int8ArrayPrototype[KEY] || property);
	  }
	};

	var exportStatic = function (KEY, property, forced) {
	  var ARRAY, TypedArrayConstructor;
	  if (!descriptors) return;
	  if (objectSetPrototypeOf) {
	    if (forced) for (ARRAY in TypedArrayConstructorsList) {
	      TypedArrayConstructor = global_1[ARRAY];
	      if (TypedArrayConstructor && has(TypedArrayConstructor, KEY)) {
	        delete TypedArrayConstructor[KEY];
	      }
	    }
	    if (!TypedArray[KEY] || forced) {
	      // V8 ~ Chrome 49-50 `%TypedArray%` methods are non-writable non-configurable
	      try {
	        return redefine(TypedArray, KEY, forced ? property : NATIVE_ARRAY_BUFFER_VIEWS && Int8Array$1[KEY] || property);
	      } catch (error) { /* empty */ }
	    } else return;
	  }
	  for (ARRAY in TypedArrayConstructorsList) {
	    TypedArrayConstructor = global_1[ARRAY];
	    if (TypedArrayConstructor && (!TypedArrayConstructor[KEY] || forced)) {
	      redefine(TypedArrayConstructor, KEY, property);
	    }
	  }
	};

	for (NAME in TypedArrayConstructorsList) {
	  if (!global_1[NAME]) NATIVE_ARRAY_BUFFER_VIEWS = false;
	}

	// WebKit bug - typed arrays constructors prototype is Object.prototype
	if (!NATIVE_ARRAY_BUFFER_VIEWS || typeof TypedArray != 'function' || TypedArray === Function.prototype) {
	  // eslint-disable-next-line no-shadow
	  TypedArray = function TypedArray() {
	    throw TypeError('Incorrect invocation');
	  };
	  if (NATIVE_ARRAY_BUFFER_VIEWS) for (NAME in TypedArrayConstructorsList) {
	    if (global_1[NAME]) objectSetPrototypeOf(global_1[NAME], TypedArray);
	  }
	}

	if (!NATIVE_ARRAY_BUFFER_VIEWS || !TypedArrayPrototype || TypedArrayPrototype === ObjectPrototype$2) {
	  TypedArrayPrototype = TypedArray.prototype;
	  if (NATIVE_ARRAY_BUFFER_VIEWS) for (NAME in TypedArrayConstructorsList) {
	    if (global_1[NAME]) objectSetPrototypeOf(global_1[NAME].prototype, TypedArrayPrototype);
	  }
	}

	// WebKit bug - one more object in Uint8ClampedArray prototype chain
	if (NATIVE_ARRAY_BUFFER_VIEWS && objectGetPrototypeOf(Uint8ClampedArrayPrototype) !== TypedArrayPrototype) {
	  objectSetPrototypeOf(Uint8ClampedArrayPrototype, TypedArrayPrototype);
	}

	if (descriptors && !has(TypedArrayPrototype, TO_STRING_TAG$4)) {
	  TYPED_ARRAY_TAG_REQIRED = true;
	  defineProperty$2(TypedArrayPrototype, TO_STRING_TAG$4, { get: function () {
	    return isObject(this) ? this[TYPED_ARRAY_TAG] : undefined;
	  } });
	  for (NAME in TypedArrayConstructorsList) if (global_1[NAME]) {
	    hide(global_1[NAME], TYPED_ARRAY_TAG, NAME);
	  }
	}

	// WebKit bug - the same parent prototype for typed arrays and data view
	if (NATIVE_ARRAY_BUFFER && objectSetPrototypeOf && objectGetPrototypeOf(DataViewPrototype) !== ObjectPrototype$2) {
	  objectSetPrototypeOf(DataViewPrototype, ObjectPrototype$2);
	}

	var arrayBufferViewCore = {
	  NATIVE_ARRAY_BUFFER: NATIVE_ARRAY_BUFFER,
	  NATIVE_ARRAY_BUFFER_VIEWS: NATIVE_ARRAY_BUFFER_VIEWS,
	  TYPED_ARRAY_TAG: TYPED_ARRAY_TAG_REQIRED && TYPED_ARRAY_TAG,
	  aTypedArray: aTypedArray,
	  aTypedArrayConstructor: aTypedArrayConstructor,
	  exportProto: exportProto,
	  exportStatic: exportStatic,
	  isView: isView,
	  isTypedArray: isTypedArray,
	  TypedArray: TypedArray,
	  TypedArrayPrototype: TypedArrayPrototype
	};

	var $find$1 = arrayIteration.find;

	var aTypedArray$1 = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.find` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.find
	arrayBufferViewCore.exportProto('find', function find(predicate /* , thisArg */) {
	  return $find$1(aTypedArray$1(this), predicate, arguments.length > 1 ? arguments[1] : undefined);
	});

	var $findIndex = arrayIteration.findIndex;


	var FIND_INDEX = 'findIndex';
	var SKIPS_HOLES$1 = true;

	// Shouldn't skip holes
	if (FIND_INDEX in []) Array(1)[FIND_INDEX](function () { SKIPS_HOLES$1 = false; });

	// `Array.prototype.findIndex` method
	// https://tc39.github.io/ecma262/#sec-array.prototype.findindex
	_export({ target: 'Array', proto: true, forced: SKIPS_HOLES$1 }, {
	  findIndex: function findIndex(callbackfn /* , that = undefined */) {
	    return $findIndex(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables(FIND_INDEX);

	var findIndex$1 = entryUnbind('Array', 'findIndex');

	var $findIndex$1 = arrayIteration.findIndex;

	var aTypedArray$2 = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.findIndex` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.findindex
	arrayBufferViewCore.exportProto('findIndex', function findIndex(predicate /* , thisArg */) {
	  return $findIndex$1(aTypedArray$2(this), predicate, arguments.length > 1 ? arguments[1] : undefined);
	});

	// `Array.prototype.fill` method implementation
	// https://tc39.github.io/ecma262/#sec-array.prototype.fill
	var arrayFill = function fill(value /* , start = 0, end = @length */) {
	  var O = toObject(this);
	  var length = toLength(O.length);
	  var argumentsLength = arguments.length;
	  var index = toAbsoluteIndex(argumentsLength > 1 ? arguments[1] : undefined, length);
	  var end = argumentsLength > 2 ? arguments[2] : undefined;
	  var endPos = end === undefined ? length : toAbsoluteIndex(end, length);
	  while (endPos > index) O[index++] = value;
	  return O;
	};

	// `Array.prototype.fill` method
	// https://tc39.github.io/ecma262/#sec-array.prototype.fill
	_export({ target: 'Array', proto: true }, {
	  fill: arrayFill
	});

	// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables('fill');

	var fill = entryUnbind('Array', 'fill');

	var aTypedArray$3 = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.fill` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.fill
	// eslint-disable-next-line no-unused-vars
	arrayBufferViewCore.exportProto('fill', function fill(value /* , start, end */) {
	  return arrayFill.apply(aTypedArray$3(this), arguments);
	});

	var min$2 = Math.min;

	// `Array.prototype.copyWithin` method implementation
	// https://tc39.github.io/ecma262/#sec-array.prototype.copywithin
	var arrayCopyWithin = [].copyWithin || function copyWithin(target /* = 0 */, start /* = 0, end = @length */) {
	  var O = toObject(this);
	  var len = toLength(O.length);
	  var to = toAbsoluteIndex(target, len);
	  var from = toAbsoluteIndex(start, len);
	  var end = arguments.length > 2 ? arguments[2] : undefined;
	  var count = min$2((end === undefined ? len : toAbsoluteIndex(end, len)) - from, len - to);
	  var inc = 1;
	  if (from < to && to < from + count) {
	    inc = -1;
	    from += count - 1;
	    to += count - 1;
	  }
	  while (count-- > 0) {
	    if (from in O) O[to] = O[from];
	    else delete O[to];
	    to += inc;
	    from += inc;
	  } return O;
	};

	// `Array.prototype.copyWithin` method
	// https://tc39.github.io/ecma262/#sec-array.prototype.copywithin
	_export({ target: 'Array', proto: true }, {
	  copyWithin: arrayCopyWithin
	});

	// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables('copyWithin');

	var copyWithin = entryUnbind('Array', 'copyWithin');

	var aTypedArray$4 = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.copyWithin` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.copywithin
	arrayBufferViewCore.exportProto('copyWithin', function copyWithin(target, start /* , end */) {
	  return arrayCopyWithin.call(aTypedArray$4(this), target, start, arguments.length > 2 ? arguments[2] : undefined);
	});

	var createProperty = function (object, key, value) {
	  var propertyKey = toPrimitive(key);
	  if (propertyKey in object) objectDefineProperty.f(object, propertyKey, createPropertyDescriptor(0, value));
	  else object[propertyKey] = value;
	};

	// `Array.from` method implementation
	// https://tc39.github.io/ecma262/#sec-array.from
	var arrayFrom = function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
	  var O = toObject(arrayLike);
	  var C = typeof this == 'function' ? this : Array;
	  var argumentsLength = arguments.length;
	  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
	  var mapping = mapfn !== undefined;
	  var index = 0;
	  var iteratorMethod = getIteratorMethod(O);
	  var length, result, step, iterator;
	  if (mapping) mapfn = bindContext(mapfn, argumentsLength > 2 ? arguments[2] : undefined, 2);
	  // if the target is not iterable or it's an array with the default iterator - use a simple case
	  if (iteratorMethod != undefined && !(C == Array && isArrayIteratorMethod(iteratorMethod))) {
	    iterator = iteratorMethod.call(O);
	    result = new C();
	    for (;!(step = iterator.next()).done; index++) {
	      createProperty(result, index, mapping
	        ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true)
	        : step.value
	      );
	    }
	  } else {
	    length = toLength(O.length);
	    result = new C(length);
	    for (;length > index; index++) {
	      createProperty(result, index, mapping ? mapfn(O[index], index) : O[index]);
	    }
	  }
	  result.length = index;
	  return result;
	};

	var INCORRECT_ITERATION = !checkCorrectnessOfIteration(function (iterable) {
	  Array.from(iterable);
	});

	// `Array.from` method
	// https://tc39.github.io/ecma262/#sec-array.from
	_export({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
	  from: arrayFrom
	});

	var from_1 = path.Array.from;

	/* eslint-disable no-new */



	var NATIVE_ARRAY_BUFFER_VIEWS$1 = arrayBufferViewCore.NATIVE_ARRAY_BUFFER_VIEWS;

	var ArrayBuffer = global_1.ArrayBuffer;
	var Int8Array$2 = global_1.Int8Array;

	var typedArraysConstructorsRequiresWrappers = !NATIVE_ARRAY_BUFFER_VIEWS$1 || !fails(function () {
	  Int8Array$2(1);
	}) || !fails(function () {
	  new Int8Array$2(-1);
	}) || !checkCorrectnessOfIteration(function (iterable) {
	  new Int8Array$2();
	  new Int8Array$2(null);
	  new Int8Array$2(1.5);
	  new Int8Array$2(iterable);
	}, true) || fails(function () {
	  // Safari 11 bug
	  return new Int8Array$2(new ArrayBuffer(2), 1, undefined).length !== 1;
	});

	var aTypedArrayConstructor$1 = arrayBufferViewCore.aTypedArrayConstructor;

	var typedArrayFrom = function from(source /* , mapfn, thisArg */) {
	  var O = toObject(source);
	  var argumentsLength = arguments.length;
	  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
	  var mapping = mapfn !== undefined;
	  var iteratorMethod = getIteratorMethod(O);
	  var i, length, result, step, iterator;
	  if (iteratorMethod != undefined && !isArrayIteratorMethod(iteratorMethod)) {
	    iterator = iteratorMethod.call(O);
	    O = [];
	    while (!(step = iterator.next()).done) {
	      O.push(step.value);
	    }
	  }
	  if (mapping && argumentsLength > 2) {
	    mapfn = bindContext(mapfn, arguments[2], 2);
	  }
	  length = toLength(O.length);
	  result = new (aTypedArrayConstructor$1(this))(length);
	  for (i = 0; length > i; i++) {
	    result[i] = mapping ? mapfn(O[i], i) : O[i];
	  }
	  return result;
	};

	// `%TypedArray%.from` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.from
	arrayBufferViewCore.exportStatic('from', typedArrayFrom, typedArraysConstructorsRequiresWrappers);

	var ISNT_GENERIC = fails(function () {
	  function F() { /* empty */ }
	  return !(Array.of.call(F) instanceof F);
	});

	// `Array.of` method
	// https://tc39.github.io/ecma262/#sec-array.of
	// WebKit Array.of isn't generic
	_export({ target: 'Array', stat: true, forced: ISNT_GENERIC }, {
	  of: function of(/* ...args */) {
	    var index = 0;
	    var argumentsLength = arguments.length;
	    var result = new (typeof this == 'function' ? this : Array)(argumentsLength);
	    while (argumentsLength > index) createProperty(result, index, arguments[index++]);
	    result.length = argumentsLength;
	    return result;
	  }
	});

	var of = path.Array.of;

	var aTypedArrayConstructor$2 = arrayBufferViewCore.aTypedArrayConstructor;

	// `%TypedArray%.of` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.of
	arrayBufferViewCore.exportStatic('of', function of(/* ...items */) {
	  var index = 0;
	  var length = arguments.length;
	  var result = new (aTypedArrayConstructor$2(this))(length);
	  while (length > index) result[index] = arguments[index++];
	  return result;
	}, typedArraysConstructorsRequiresWrappers);

	var defineProperty$3 = objectDefineProperty.f;

	var FunctionPrototype = Function.prototype;
	var FunctionPrototypeToString = FunctionPrototype.toString;
	var nameRE = /^\s*function ([^ (]*)/;
	var NAME$1 = 'name';

	// Function instances `.name` property
	// https://tc39.github.io/ecma262/#sec-function-instances-name
	if (descriptors && !(NAME$1 in FunctionPrototype)) {
	  defineProperty$3(FunctionPrototype, NAME$1, {
	    configurable: true,
	    get: function () {
	      try {
	        return FunctionPrototypeToString.call(this).match(nameRE)[1];
	      } catch (error) {
	        return '';
	      }
	    }
	  });
	}

	var floor$1 = Math.floor;
	var log = Math.log;
	var LOG2E = Math.LOG2E;

	// `Math.clz32` method
	// https://tc39.github.io/ecma262/#sec-math.clz32
	_export({ target: 'Math', stat: true }, {
	  clz32: function clz32(x) {
	    return (x >>>= 0) ? 31 - floor$1(log(x + 0.5) * LOG2E) : 32;
	  }
	});

	var clz32 = path.Math.clz32;

	var nativeImul = Math.imul;

	var FORCED = fails(function () {
	  return nativeImul(0xFFFFFFFF, 5) != -5 || nativeImul.length != 2;
	});

	// `Math.imul` method
	// https://tc39.github.io/ecma262/#sec-math.imul
	// some WebKit versions fails with big numbers, some has wrong arity
	_export({ target: 'Math', stat: true, forced: FORCED }, {
	  imul: function imul(x, y) {
	    var UINT16 = 0xFFFF;
	    var xn = +x;
	    var yn = +y;
	    var xl = UINT16 & xn;
	    var yl = UINT16 & yn;
	    return 0 | xl * yl + ((UINT16 & xn >>> 16) * yl + xl * (UINT16 & yn >>> 16) << 16 >>> 0);
	  }
	});

	var imul = path.Math.imul;

	// `Math.sign` method implementation
	// https://tc39.github.io/ecma262/#sec-math.sign
	var mathSign = Math.sign || function sign(x) {
	  // eslint-disable-next-line no-self-compare
	  return (x = +x) == 0 || x != x ? x : x < 0 ? -1 : 1;
	};

	// `Math.sign` method
	// https://tc39.github.io/ecma262/#sec-math.sign
	_export({ target: 'Math', stat: true }, {
	  sign: mathSign
	});

	var sign = path.Math.sign;

	var log$1 = Math.log;
	var LOG10E = Math.LOG10E;

	// `Math.log10` method
	// https://tc39.github.io/ecma262/#sec-math.log10
	_export({ target: 'Math', stat: true }, {
	  log10: function log10(x) {
	    return log$1(x) * LOG10E;
	  }
	});

	var log10 = path.Math.log10;

	var log$2 = Math.log;
	var LN2 = Math.LN2;

	// `Math.log2` method
	// https://tc39.github.io/ecma262/#sec-math.log2
	_export({ target: 'Math', stat: true }, {
	  log2: function log2(x) {
	    return log$2(x) / LN2;
	  }
	});

	var log2 = path.Math.log2;

	var log$3 = Math.log;

	// `Math.log1p` method implementation
	// https://tc39.github.io/ecma262/#sec-math.log1p
	var mathLog1p = Math.log1p || function log1p(x) {
	  return (x = +x) > -1e-8 && x < 1e-8 ? x - x * x / 2 : log$3(1 + x);
	};

	// `Math.log1p` method
	// https://tc39.github.io/ecma262/#sec-math.log1p
	_export({ target: 'Math', stat: true }, { log1p: mathLog1p });

	var log1p = path.Math.log1p;

	var nativeExpm1 = Math.expm1;
	var exp = Math.exp;

	// `Math.expm1` method implementation
	// https://tc39.github.io/ecma262/#sec-math.expm1
	var mathExpm1 = (!nativeExpm1
	  // Old FF bug
	  || nativeExpm1(10) > 22025.465794806719 || nativeExpm1(10) < 22025.4657948067165168
	  // Tor Browser bug
	  || nativeExpm1(-2e-17) != -2e-17
	) ? function expm1(x) {
	  return (x = +x) == 0 ? x : x > -1e-6 && x < 1e-6 ? x + x * x / 2 : exp(x) - 1;
	} : nativeExpm1;

	// `Math.expm1` method
	// https://tc39.github.io/ecma262/#sec-math.expm1
	_export({ target: 'Math', stat: true, forced: mathExpm1 != Math.expm1 }, { expm1: mathExpm1 });

	var expm1 = path.Math.expm1;

	var nativeCosh = Math.cosh;
	var abs = Math.abs;
	var E = Math.E;

	// `Math.cosh` method
	// https://tc39.github.io/ecma262/#sec-math.cosh
	_export({ target: 'Math', stat: true, forced: !nativeCosh || nativeCosh(710) === Infinity }, {
	  cosh: function cosh(x) {
	    var t = mathExpm1(abs(x) - 1) + 1;
	    return (t + 1 / (t * E * E)) * (E / 2);
	  }
	});

	var cosh = path.Math.cosh;

	var abs$1 = Math.abs;
	var exp$1 = Math.exp;
	var E$1 = Math.E;

	var FORCED$1 = fails(function () {
	  return Math.sinh(-2e-17) != -2e-17;
	});

	// `Math.sinh` method
	// https://tc39.github.io/ecma262/#sec-math.sinh
	// V8 near Chromium 38 has a problem with very small numbers
	_export({ target: 'Math', stat: true, forced: FORCED$1 }, {
	  sinh: function sinh(x) {
	    return abs$1(x = +x) < 1 ? (mathExpm1(x) - mathExpm1(-x)) / 2 : (exp$1(x - 1) - exp$1(-x - 1)) * (E$1 / 2);
	  }
	});

	var sinh = path.Math.sinh;

	var exp$2 = Math.exp;

	// `Math.tanh` method
	// https://tc39.github.io/ecma262/#sec-math.tanh
	_export({ target: 'Math', stat: true }, {
	  tanh: function tanh(x) {
	    var a = mathExpm1(x = +x);
	    var b = mathExpm1(-x);
	    return a == Infinity ? 1 : b == Infinity ? -1 : (a - b) / (exp$2(x) + exp$2(-x));
	  }
	});

	var tanh = path.Math.tanh;

	var nativeAcosh = Math.acosh;
	var log$4 = Math.log;
	var sqrt = Math.sqrt;
	var LN2$1 = Math.LN2;

	var FORCED$2 = !nativeAcosh
	  // V8 bug: https://code.google.com/p/v8/issues/detail?id=3509
	  || Math.floor(nativeAcosh(Number.MAX_VALUE)) != 710
	  // Tor Browser bug: Math.acosh(Infinity) -> NaN
	  || nativeAcosh(Infinity) != Infinity;

	// `Math.acosh` method
	// https://tc39.github.io/ecma262/#sec-math.acosh
	_export({ target: 'Math', stat: true, forced: FORCED$2 }, {
	  acosh: function acosh(x) {
	    return (x = +x) < 1 ? NaN : x > 94906265.62425156
	      ? log$4(x) + LN2$1
	      : mathLog1p(x - 1 + sqrt(x - 1) * sqrt(x + 1));
	  }
	});

	var acosh = path.Math.acosh;

	var nativeAsinh = Math.asinh;
	var log$5 = Math.log;
	var sqrt$1 = Math.sqrt;

	function asinh(x) {
	  return !isFinite(x = +x) || x == 0 ? x : x < 0 ? -asinh(-x) : log$5(x + sqrt$1(x * x + 1));
	}

	// `Math.asinh` method
	// https://tc39.github.io/ecma262/#sec-math.asinh
	// Tor Browser bug: Math.asinh(0) -> -0
	_export({ target: 'Math', stat: true, forced: !(nativeAsinh && 1 / nativeAsinh(0) > 0) }, {
	  asinh: asinh
	});

	var asinh$1 = path.Math.asinh;

	var nativeAtanh = Math.atanh;
	var log$6 = Math.log;

	// `Math.atanh` method
	// https://tc39.github.io/ecma262/#sec-math.atanh
	// Tor Browser bug: Math.atanh(-0) -> 0
	_export({ target: 'Math', stat: true, forced: !(nativeAtanh && 1 / nativeAtanh(-0) < 0) }, {
	  atanh: function atanh(x) {
	    return (x = +x) == 0 ? x : log$6((1 + x) / (1 - x)) / 2;
	  }
	});

	var atanh = path.Math.atanh;

	var $hypot = Math.hypot;
	var abs$2 = Math.abs;
	var sqrt$2 = Math.sqrt;

	// Chrome 77 bug
	// https://bugs.chromium.org/p/v8/issues/detail?id=9546
	var BUGGY = !!$hypot && $hypot(Infinity, NaN) !== Infinity;

	// `Math.hypot` method
	// https://tc39.github.io/ecma262/#sec-math.hypot
	_export({ target: 'Math', stat: true, forced: BUGGY }, {
	  hypot: function hypot(value1, value2) { // eslint-disable-line no-unused-vars
	    var sum = 0;
	    var i = 0;
	    var aLen = arguments.length;
	    var larg = 0;
	    var arg, div;
	    while (i < aLen) {
	      arg = abs$2(arguments[i++]);
	      if (larg < arg) {
	        div = larg / arg;
	        sum = sum * div * div + 1;
	        larg = arg;
	      } else if (arg > 0) {
	        div = arg / larg;
	        sum += div * div;
	      } else sum += arg;
	    }
	    return larg === Infinity ? Infinity : larg * sqrt$2(sum);
	  }
	});

	var hypot = path.Math.hypot;

	var ceil$1 = Math.ceil;
	var floor$2 = Math.floor;

	// `Math.trunc` method
	// https://tc39.github.io/ecma262/#sec-math.trunc
	_export({ target: 'Math', stat: true }, {
	  trunc: function trunc(it) {
	    return (it > 0 ? floor$2 : ceil$1)(it);
	  }
	});

	var trunc = path.Math.trunc;

	var abs$3 = Math.abs;
	var pow = Math.pow;
	var EPSILON = pow(2, -52);
	var EPSILON32 = pow(2, -23);
	var MAX32 = pow(2, 127) * (2 - EPSILON32);
	var MIN32 = pow(2, -126);

	var roundTiesToEven = function (n) {
	  return n + 1 / EPSILON - 1 / EPSILON;
	};

	// `Math.fround` method implementation
	// https://tc39.github.io/ecma262/#sec-math.fround
	var mathFround = Math.fround || function fround(x) {
	  var $abs = abs$3(x);
	  var $sign = mathSign(x);
	  var a, result;
	  if ($abs < MIN32) return $sign * roundTiesToEven($abs / MIN32 / EPSILON32) * MIN32 * EPSILON32;
	  a = (1 + EPSILON32 / EPSILON) * $abs;
	  result = a - (a - $abs);
	  // eslint-disable-next-line no-self-compare
	  if (result > MAX32 || result != result) return $sign * Infinity;
	  return $sign * result;
	};

	// `Math.fround` method
	// https://tc39.github.io/ecma262/#sec-math.fround
	_export({ target: 'Math', stat: true }, { fround: mathFround });

	var fround = path.Math.fround;

	var abs$4 = Math.abs;
	var pow$1 = Math.pow;

	// `Math.cbrt` method
	// https://tc39.github.io/ecma262/#sec-math.cbrt
	_export({ target: 'Math', stat: true }, {
	  cbrt: function cbrt(x) {
	    return mathSign(x = +x) * pow$1(abs$4(x), 1 / 3);
	  }
	});

	var cbrt = path.Math.cbrt;

	// `Number.EPSILON` constant
	// https://tc39.github.io/ecma262/#sec-number.epsilon
	_export({ target: 'Number', stat: true }, {
	  EPSILON: Math.pow(2, -52)
	});

	var globalIsFinite = global_1.isFinite;

	// `Number.isFinite` method
	// https://tc39.github.io/ecma262/#sec-number.isfinite
	var numberIsFinite = Number.isFinite || function isFinite(it) {
	  return typeof it == 'number' && globalIsFinite(it);
	};

	// `Number.isFinite` method
	// https://tc39.github.io/ecma262/#sec-number.isfinite
	_export({ target: 'Number', stat: true }, { isFinite: numberIsFinite });

	var _isFinite = path.Number.isFinite;

	var floor$3 = Math.floor;

	// `Number.isInteger` method implementation
	// https://tc39.github.io/ecma262/#sec-number.isinteger
	var isInteger = function isInteger(it) {
	  return !isObject(it) && isFinite(it) && floor$3(it) === it;
	};

	// `Number.isInteger` method
	// https://tc39.github.io/ecma262/#sec-number.isinteger
	_export({ target: 'Number', stat: true }, {
	  isInteger: isInteger
	});

	var isInteger$1 = path.Number.isInteger;

	// `Number.isNaN` method
	// https://tc39.github.io/ecma262/#sec-number.isnan
	_export({ target: 'Number', stat: true }, {
	  isNaN: function isNaN(number) {
	    // eslint-disable-next-line no-self-compare
	    return number != number;
	  }
	});

	var isNan = path.Number.isNaN;

	var abs$5 = Math.abs;

	// `Number.isSafeInteger` method
	// https://tc39.github.io/ecma262/#sec-number.issafeinteger
	_export({ target: 'Number', stat: true }, {
	  isSafeInteger: function isSafeInteger(number) {
	    return isInteger(number) && abs$5(number) <= 0x1FFFFFFFFFFFFF;
	  }
	});

	var isSafeInteger = path.Number.isSafeInteger;

	// `Number.MAX_SAFE_INTEGER` constant
	// https://tc39.github.io/ecma262/#sec-number.max_safe_integer
	_export({ target: 'Number', stat: true }, {
	  MAX_SAFE_INTEGER: 0x1FFFFFFFFFFFFF
	});

	// `Number.MIN_SAFE_INTEGER` constant
	// https://tc39.github.io/ecma262/#sec-number.min_safe_integer
	_export({ target: 'Number', stat: true }, {
	  MIN_SAFE_INTEGER: -0x1FFFFFFFFFFFFF
	});

	// a string of all valid unicode whitespaces
	// eslint-disable-next-line max-len
	var whitespaces = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

	var whitespace = '[' + whitespaces + ']';
	var ltrim = RegExp('^' + whitespace + whitespace + '*');
	var rtrim = RegExp(whitespace + whitespace + '*$');

	// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
	var createMethod$3 = function (TYPE) {
	  return function ($this) {
	    var string = String(requireObjectCoercible($this));
	    if (TYPE & 1) string = string.replace(ltrim, '');
	    if (TYPE & 2) string = string.replace(rtrim, '');
	    return string;
	  };
	};

	var stringTrim = {
	  // `String.prototype.{ trimLeft, trimStart }` methods
	  // https://tc39.github.io/ecma262/#sec-string.prototype.trimstart
	  start: createMethod$3(1),
	  // `String.prototype.{ trimRight, trimEnd }` methods
	  // https://tc39.github.io/ecma262/#sec-string.prototype.trimend
	  end: createMethod$3(2),
	  // `String.prototype.trim` method
	  // https://tc39.github.io/ecma262/#sec-string.prototype.trim
	  trim: createMethod$3(3)
	};

	var trim = stringTrim.trim;


	var nativeParseFloat = global_1.parseFloat;
	var FORCED$3 = 1 / nativeParseFloat(whitespaces + '-0') !== -Infinity;

	// `parseFloat` method
	// https://tc39.github.io/ecma262/#sec-parsefloat-string
	var _parseFloat = FORCED$3 ? function parseFloat(string) {
	  var trimmedString = trim(String(string));
	  var result = nativeParseFloat(trimmedString);
	  return result === 0 && trimmedString.charAt(0) == '-' ? -0 : result;
	} : nativeParseFloat;

	// `Number.parseFloat` method
	// https://tc39.github.io/ecma262/#sec-number.parseFloat
	_export({ target: 'Number', stat: true, forced: Number.parseFloat != _parseFloat }, {
	  parseFloat: _parseFloat
	});

	var _parseFloat$1 = path.Number.parseFloat;

	var trim$1 = stringTrim.trim;


	var nativeParseInt = global_1.parseInt;
	var hex = /^[+-]?0[Xx]/;
	var FORCED$4 = nativeParseInt(whitespaces + '08') !== 8 || nativeParseInt(whitespaces + '0x16') !== 22;

	// `parseInt` method
	// https://tc39.github.io/ecma262/#sec-parseint-string-radix
	var _parseInt = FORCED$4 ? function parseInt(string, radix) {
	  var S = trim$1(String(string));
	  return nativeParseInt(S, (radix >>> 0) || (hex.test(S) ? 16 : 10));
	} : nativeParseInt;

	// `Number.parseInt` method
	// https://tc39.github.io/ecma262/#sec-number.parseint
	_export({ target: 'Number', stat: true, forced: Number.parseInt != _parseInt }, {
	  parseInt: _parseInt
	});

	var _parseInt$1 = path.Number.parseInt;

	var nativeAssign = Object.assign;

	// `Object.assign` method
	// https://tc39.github.io/ecma262/#sec-object.assign
	// should work with symbols and should have deterministic property order (V8 bug)
	var objectAssign = !nativeAssign || fails(function () {
	  var A = {};
	  var B = {};
	  // eslint-disable-next-line no-undef
	  var symbol = Symbol();
	  var alphabet = 'abcdefghijklmnopqrst';
	  A[symbol] = 7;
	  alphabet.split('').forEach(function (chr) { B[chr] = chr; });
	  return nativeAssign({}, A)[symbol] != 7 || objectKeys(nativeAssign({}, B)).join('') != alphabet;
	}) ? function assign(target, source) { // eslint-disable-line no-unused-vars
	  var T = toObject(target);
	  var argumentsLength = arguments.length;
	  var index = 1;
	  var getOwnPropertySymbols = objectGetOwnPropertySymbols.f;
	  var propertyIsEnumerable = objectPropertyIsEnumerable.f;
	  while (argumentsLength > index) {
	    var S = indexedObject(arguments[index++]);
	    var keys = getOwnPropertySymbols ? objectKeys(S).concat(getOwnPropertySymbols(S)) : objectKeys(S);
	    var length = keys.length;
	    var j = 0;
	    var key;
	    while (length > j) {
	      key = keys[j++];
	      if (!descriptors || propertyIsEnumerable.call(S, key)) T[key] = S[key];
	    }
	  } return T;
	} : nativeAssign;

	// `Object.assign` method
	// https://tc39.github.io/ecma262/#sec-object.assign
	_export({ target: 'Object', stat: true, forced: Object.assign !== objectAssign }, {
	  assign: objectAssign
	});

	var assign = path.Object.assign;

	var nativeGetOwnPropertyNames = objectGetOwnPropertyNames.f;

	var toString$1 = {}.toString;

	var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
	  ? Object.getOwnPropertyNames(window) : [];

	var getWindowNames = function (it) {
	  try {
	    return nativeGetOwnPropertyNames(it);
	  } catch (error) {
	    return windowNames.slice();
	  }
	};

	// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
	var f$5 = function getOwnPropertyNames(it) {
	  return windowNames && toString$1.call(it) == '[object Window]'
	    ? getWindowNames(it)
	    : nativeGetOwnPropertyNames(toIndexedObject(it));
	};

	var objectGetOwnPropertyNamesExternal = {
		f: f$5
	};

	var f$6 = wellKnownSymbol;

	var wrappedWellKnownSymbol = {
		f: f$6
	};

	var defineProperty$4 = objectDefineProperty.f;

	var defineWellKnownSymbol = function (NAME) {
	  var Symbol = path.Symbol || (path.Symbol = {});
	  if (!has(Symbol, NAME)) defineProperty$4(Symbol, NAME, {
	    value: wrappedWellKnownSymbol.f(NAME)
	  });
	};

	var $forEach = arrayIteration.forEach;

	var HIDDEN = sharedKey('hidden');
	var SYMBOL = 'Symbol';
	var PROTOTYPE$1 = 'prototype';
	var TO_PRIMITIVE = wellKnownSymbol('toPrimitive');
	var setInternalState$4 = internalState.set;
	var getInternalState$2 = internalState.getterFor(SYMBOL);
	var ObjectPrototype$3 = Object[PROTOTYPE$1];
	var $Symbol = global_1.Symbol;
	var JSON = global_1.JSON;
	var nativeJSONStringify = JSON && JSON.stringify;
	var nativeGetOwnPropertyDescriptor$1 = objectGetOwnPropertyDescriptor.f;
	var nativeDefineProperty$1 = objectDefineProperty.f;
	var nativeGetOwnPropertyNames$1 = objectGetOwnPropertyNamesExternal.f;
	var nativePropertyIsEnumerable$1 = objectPropertyIsEnumerable.f;
	var AllSymbols = shared('symbols');
	var ObjectPrototypeSymbols = shared('op-symbols');
	var StringToSymbolRegistry = shared('string-to-symbol-registry');
	var SymbolToStringRegistry = shared('symbol-to-string-registry');
	var WellKnownSymbolsStore = shared('wks');
	var QObject = global_1.QObject;
	// Don't use setters in Qt Script, https://github.com/zloirock/core-js/issues/173
	var USE_SETTER = !QObject || !QObject[PROTOTYPE$1] || !QObject[PROTOTYPE$1].findChild;

	// fallback for old Android, https://code.google.com/p/v8/issues/detail?id=687
	var setSymbolDescriptor = descriptors && fails(function () {
	  return objectCreate(nativeDefineProperty$1({}, 'a', {
	    get: function () { return nativeDefineProperty$1(this, 'a', { value: 7 }).a; }
	  })).a != 7;
	}) ? function (O, P, Attributes) {
	  var ObjectPrototypeDescriptor = nativeGetOwnPropertyDescriptor$1(ObjectPrototype$3, P);
	  if (ObjectPrototypeDescriptor) delete ObjectPrototype$3[P];
	  nativeDefineProperty$1(O, P, Attributes);
	  if (ObjectPrototypeDescriptor && O !== ObjectPrototype$3) {
	    nativeDefineProperty$1(ObjectPrototype$3, P, ObjectPrototypeDescriptor);
	  }
	} : nativeDefineProperty$1;

	var wrap = function (tag, description) {
	  var symbol = AllSymbols[tag] = objectCreate($Symbol[PROTOTYPE$1]);
	  setInternalState$4(symbol, {
	    type: SYMBOL,
	    tag: tag,
	    description: description
	  });
	  if (!descriptors) symbol.description = description;
	  return symbol;
	};

	var isSymbol = nativeSymbol && typeof $Symbol.iterator == 'symbol' ? function (it) {
	  return typeof it == 'symbol';
	} : function (it) {
	  return Object(it) instanceof $Symbol;
	};

	var $defineProperty = function defineProperty(O, P, Attributes) {
	  if (O === ObjectPrototype$3) $defineProperty(ObjectPrototypeSymbols, P, Attributes);
	  anObject(O);
	  var key = toPrimitive(P, true);
	  anObject(Attributes);
	  if (has(AllSymbols, key)) {
	    if (!Attributes.enumerable) {
	      if (!has(O, HIDDEN)) nativeDefineProperty$1(O, HIDDEN, createPropertyDescriptor(1, {}));
	      O[HIDDEN][key] = true;
	    } else {
	      if (has(O, HIDDEN) && O[HIDDEN][key]) O[HIDDEN][key] = false;
	      Attributes = objectCreate(Attributes, { enumerable: createPropertyDescriptor(0, false) });
	    } return setSymbolDescriptor(O, key, Attributes);
	  } return nativeDefineProperty$1(O, key, Attributes);
	};

	var $defineProperties = function defineProperties(O, Properties) {
	  anObject(O);
	  var properties = toIndexedObject(Properties);
	  var keys = objectKeys(properties).concat($getOwnPropertySymbols(properties));
	  $forEach(keys, function (key) {
	    if (!descriptors || $propertyIsEnumerable.call(properties, key)) $defineProperty(O, key, properties[key]);
	  });
	  return O;
	};

	var $create = function create(O, Properties) {
	  return Properties === undefined ? objectCreate(O) : $defineProperties(objectCreate(O), Properties);
	};

	var $propertyIsEnumerable = function propertyIsEnumerable(V) {
	  var P = toPrimitive(V, true);
	  var enumerable = nativePropertyIsEnumerable$1.call(this, P);
	  if (this === ObjectPrototype$3 && has(AllSymbols, P) && !has(ObjectPrototypeSymbols, P)) return false;
	  return enumerable || !has(this, P) || !has(AllSymbols, P) || has(this, HIDDEN) && this[HIDDEN][P] ? enumerable : true;
	};

	var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(O, P) {
	  var it = toIndexedObject(O);
	  var key = toPrimitive(P, true);
	  if (it === ObjectPrototype$3 && has(AllSymbols, key) && !has(ObjectPrototypeSymbols, key)) return;
	  var descriptor = nativeGetOwnPropertyDescriptor$1(it, key);
	  if (descriptor && has(AllSymbols, key) && !(has(it, HIDDEN) && it[HIDDEN][key])) {
	    descriptor.enumerable = true;
	  }
	  return descriptor;
	};

	var $getOwnPropertyNames = function getOwnPropertyNames(O) {
	  var names = nativeGetOwnPropertyNames$1(toIndexedObject(O));
	  var result = [];
	  $forEach(names, function (key) {
	    if (!has(AllSymbols, key) && !has(hiddenKeys, key)) result.push(key);
	  });
	  return result;
	};

	var $getOwnPropertySymbols = function getOwnPropertySymbols(O) {
	  var IS_OBJECT_PROTOTYPE = O === ObjectPrototype$3;
	  var names = nativeGetOwnPropertyNames$1(IS_OBJECT_PROTOTYPE ? ObjectPrototypeSymbols : toIndexedObject(O));
	  var result = [];
	  $forEach(names, function (key) {
	    if (has(AllSymbols, key) && (!IS_OBJECT_PROTOTYPE || has(ObjectPrototype$3, key))) {
	      result.push(AllSymbols[key]);
	    }
	  });
	  return result;
	};

	// `Symbol` constructor
	// https://tc39.github.io/ecma262/#sec-symbol-constructor
	if (!nativeSymbol) {
	  $Symbol = function Symbol() {
	    if (this instanceof $Symbol) throw TypeError('Symbol is not a constructor');
	    var description = !arguments.length || arguments[0] === undefined ? undefined : String(arguments[0]);
	    var tag = uid(description);
	    var setter = function (value) {
	      if (this === ObjectPrototype$3) setter.call(ObjectPrototypeSymbols, value);
	      if (has(this, HIDDEN) && has(this[HIDDEN], tag)) this[HIDDEN][tag] = false;
	      setSymbolDescriptor(this, tag, createPropertyDescriptor(1, value));
	    };
	    if (descriptors && USE_SETTER) setSymbolDescriptor(ObjectPrototype$3, tag, { configurable: true, set: setter });
	    return wrap(tag, description);
	  };

	  redefine($Symbol[PROTOTYPE$1], 'toString', function toString() {
	    return getInternalState$2(this).tag;
	  });

	  objectPropertyIsEnumerable.f = $propertyIsEnumerable;
	  objectDefineProperty.f = $defineProperty;
	  objectGetOwnPropertyDescriptor.f = $getOwnPropertyDescriptor;
	  objectGetOwnPropertyNames.f = objectGetOwnPropertyNamesExternal.f = $getOwnPropertyNames;
	  objectGetOwnPropertySymbols.f = $getOwnPropertySymbols;

	  if (descriptors) {
	    // https://github.com/tc39/proposal-Symbol-description
	    nativeDefineProperty$1($Symbol[PROTOTYPE$1], 'description', {
	      configurable: true,
	      get: function description() {
	        return getInternalState$2(this).description;
	      }
	    });
	    {
	      redefine(ObjectPrototype$3, 'propertyIsEnumerable', $propertyIsEnumerable, { unsafe: true });
	    }
	  }

	  wrappedWellKnownSymbol.f = function (name) {
	    return wrap(wellKnownSymbol(name), name);
	  };
	}

	_export({ global: true, wrap: true, forced: !nativeSymbol, sham: !nativeSymbol }, {
	  Symbol: $Symbol
	});

	$forEach(objectKeys(WellKnownSymbolsStore), function (name) {
	  defineWellKnownSymbol(name);
	});

	_export({ target: SYMBOL, stat: true, forced: !nativeSymbol }, {
	  // `Symbol.for` method
	  // https://tc39.github.io/ecma262/#sec-symbol.for
	  'for': function (key) {
	    var string = String(key);
	    if (has(StringToSymbolRegistry, string)) return StringToSymbolRegistry[string];
	    var symbol = $Symbol(string);
	    StringToSymbolRegistry[string] = symbol;
	    SymbolToStringRegistry[symbol] = string;
	    return symbol;
	  },
	  // `Symbol.keyFor` method
	  // https://tc39.github.io/ecma262/#sec-symbol.keyfor
	  keyFor: function keyFor(sym) {
	    if (!isSymbol(sym)) throw TypeError(sym + ' is not a symbol');
	    if (has(SymbolToStringRegistry, sym)) return SymbolToStringRegistry[sym];
	  },
	  useSetter: function () { USE_SETTER = true; },
	  useSimple: function () { USE_SETTER = false; }
	});

	_export({ target: 'Object', stat: true, forced: !nativeSymbol, sham: !descriptors }, {
	  // `Object.create` method
	  // https://tc39.github.io/ecma262/#sec-object.create
	  create: $create,
	  // `Object.defineProperty` method
	  // https://tc39.github.io/ecma262/#sec-object.defineproperty
	  defineProperty: $defineProperty,
	  // `Object.defineProperties` method
	  // https://tc39.github.io/ecma262/#sec-object.defineproperties
	  defineProperties: $defineProperties,
	  // `Object.getOwnPropertyDescriptor` method
	  // https://tc39.github.io/ecma262/#sec-object.getownpropertydescriptors
	  getOwnPropertyDescriptor: $getOwnPropertyDescriptor
	});

	_export({ target: 'Object', stat: true, forced: !nativeSymbol }, {
	  // `Object.getOwnPropertyNames` method
	  // https://tc39.github.io/ecma262/#sec-object.getownpropertynames
	  getOwnPropertyNames: $getOwnPropertyNames,
	  // `Object.getOwnPropertySymbols` method
	  // https://tc39.github.io/ecma262/#sec-object.getownpropertysymbols
	  getOwnPropertySymbols: $getOwnPropertySymbols
	});

	// Chrome 38 and 39 `Object.getOwnPropertySymbols` fails on primitives
	// https://bugs.chromium.org/p/v8/issues/detail?id=3443
	_export({ target: 'Object', stat: true, forced: fails(function () { objectGetOwnPropertySymbols.f(1); }) }, {
	  getOwnPropertySymbols: function getOwnPropertySymbols(it) {
	    return objectGetOwnPropertySymbols.f(toObject(it));
	  }
	});

	// `JSON.stringify` method behavior with symbols
	// https://tc39.github.io/ecma262/#sec-json.stringify
	JSON && _export({ target: 'JSON', stat: true, forced: !nativeSymbol || fails(function () {
	  var symbol = $Symbol();
	  // MS Edge converts symbol values to JSON as {}
	  return nativeJSONStringify([symbol]) != '[null]'
	    // WebKit converts symbol values to JSON as null
	    || nativeJSONStringify({ a: symbol }) != '{}'
	    // V8 throws on boxed symbols
	    || nativeJSONStringify(Object(symbol)) != '{}';
	}) }, {
	  stringify: function stringify(it) {
	    var args = [it];
	    var index = 1;
	    var replacer, $replacer;
	    while (arguments.length > index) args.push(arguments[index++]);
	    $replacer = replacer = args[1];
	    if (!isObject(replacer) && it === undefined || isSymbol(it)) return; // IE8 returns string on undefined
	    if (!isArray(replacer)) replacer = function (key, value) {
	      if (typeof $replacer == 'function') value = $replacer.call(this, key, value);
	      if (!isSymbol(value)) return value;
	    };
	    args[1] = replacer;
	    return nativeJSONStringify.apply(JSON, args);
	  }
	});

	// `Symbol.prototype[@@toPrimitive]` method
	// https://tc39.github.io/ecma262/#sec-symbol.prototype-@@toprimitive
	if (!$Symbol[PROTOTYPE$1][TO_PRIMITIVE]) hide($Symbol[PROTOTYPE$1], TO_PRIMITIVE, $Symbol[PROTOTYPE$1].valueOf);
	// `Symbol.prototype[@@toStringTag]` property
	// https://tc39.github.io/ecma262/#sec-symbol.prototype-@@tostringtag
	setToStringTag($Symbol, SYMBOL);

	hiddenKeys[HIDDEN] = true;

	var getOwnPropertySymbols = path.Object.getOwnPropertySymbols;

	// `SameValue` abstract operation
	// https://tc39.github.io/ecma262/#sec-samevalue
	var sameValue = Object.is || function is(x, y) {
	  // eslint-disable-next-line no-self-compare
	  return x === y ? x !== 0 || 1 / x === 1 / y : x != x && y != y;
	};

	// `Object.is` method
	// https://tc39.github.io/ecma262/#sec-object.is
	_export({ target: 'Object', stat: true }, {
	  is: sameValue
	});

	var is = path.Object.is;

	// `Object.setPrototypeOf` method
	// https://tc39.github.io/ecma262/#sec-object.setprototypeof
	_export({ target: 'Object', stat: true }, {
	  setPrototypeOf: objectSetPrototypeOf
	});

	var setPrototypeOf = path.Object.setPrototypeOf;

	// `RegExp.prototype.flags` getter implementation
	// https://tc39.github.io/ecma262/#sec-get-regexp.prototype.flags
	var regexpFlags = function () {
	  var that = anObject(this);
	  var result = '';
	  if (that.global) result += 'g';
	  if (that.ignoreCase) result += 'i';
	  if (that.multiline) result += 'm';
	  if (that.dotAll) result += 's';
	  if (that.unicode) result += 'u';
	  if (that.sticky) result += 'y';
	  return result;
	};

	// `RegExp.prototype.flags` getter
	// https://tc39.github.io/ecma262/#sec-get-regexp.prototype.flags
	if (descriptors && /./g.flags != 'g') {
	  objectDefineProperty.f(RegExp.prototype, 'flags', {
	    configurable: true,
	    get: regexpFlags
	  });
	}

	var MATCH = wellKnownSymbol('match');

	// `IsRegExp` abstract operation
	// https://tc39.github.io/ecma262/#sec-isregexp
	var isRegexp = function (it) {
	  var isRegExp;
	  return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : classofRaw(it) == 'RegExp');
	};

	var defineProperty$5 = objectDefineProperty.f;
	var getOwnPropertyNames = objectGetOwnPropertyNames.f;







	var MATCH$1 = wellKnownSymbol('match');
	var NativeRegExp = global_1.RegExp;
	var RegExpPrototype = NativeRegExp.prototype;
	var re1 = /a/g;
	var re2 = /a/g;

	// "new" should create a new object, old webkit bug
	var CORRECT_NEW = new NativeRegExp(re1) !== re1;

	var FORCED$5 = descriptors && isForced_1('RegExp', (!CORRECT_NEW || fails(function () {
	  re2[MATCH$1] = false;
	  // RegExp constructor can alter flags and IsRegExp works correct with @@match
	  return NativeRegExp(re1) != re1 || NativeRegExp(re2) == re2 || NativeRegExp(re1, 'i') != '/a/i';
	})));

	// `RegExp` constructor
	// https://tc39.github.io/ecma262/#sec-regexp-constructor
	if (FORCED$5) {
	  var RegExpWrapper = function RegExp(pattern, flags) {
	    var thisIsRegExp = this instanceof RegExpWrapper;
	    var patternIsRegExp = isRegexp(pattern);
	    var flagsAreUndefined = flags === undefined;
	    return !thisIsRegExp && patternIsRegExp && pattern.constructor === RegExpWrapper && flagsAreUndefined ? pattern
	      : inheritIfRequired(CORRECT_NEW
	        ? new NativeRegExp(patternIsRegExp && !flagsAreUndefined ? pattern.source : pattern, flags)
	        : NativeRegExp((patternIsRegExp = pattern instanceof RegExpWrapper)
	          ? pattern.source
	          : pattern, patternIsRegExp && flagsAreUndefined ? regexpFlags.call(pattern) : flags)
	      , thisIsRegExp ? this : RegExpPrototype, RegExpWrapper);
	  };
	  var proxy = function (key) {
	    key in RegExpWrapper || defineProperty$5(RegExpWrapper, key, {
	      configurable: true,
	      get: function () { return NativeRegExp[key]; },
	      set: function (it) { NativeRegExp[key] = it; }
	    });
	  };
	  var keys$1 = getOwnPropertyNames(NativeRegExp);
	  var index = 0;
	  while (keys$1.length > index) proxy(keys$1[index++]);
	  RegExpPrototype.constructor = RegExpWrapper;
	  RegExpWrapper.prototype = RegExpPrototype;
	  redefine(global_1, 'RegExp', RegExpWrapper);
	}

	// https://tc39.github.io/ecma262/#sec-get-regexp-@@species
	setSpecies('RegExp');

	var codeAt = stringMultibyte.codeAt;

	// `String.prototype.codePointAt` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.codepointat
	_export({ target: 'String', proto: true }, {
	  codePointAt: function codePointAt(pos) {
	    return codeAt(this, pos);
	  }
	});

	var codePointAt = entryUnbind('String', 'codePointAt');

	var notARegexp = function (it) {
	  if (isRegexp(it)) {
	    throw TypeError("The method doesn't accept regular expressions");
	  } return it;
	};

	var MATCH$2 = wellKnownSymbol('match');

	var correctIsRegexpLogic = function (METHOD_NAME) {
	  var regexp = /./;
	  try {
	    '/./'[METHOD_NAME](regexp);
	  } catch (e) {
	    try {
	      regexp[MATCH$2] = false;
	      return '/./'[METHOD_NAME](regexp);
	    } catch (f) { /* empty */ }
	  } return false;
	};

	// `String.prototype.includes` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.includes
	_export({ target: 'String', proto: true, forced: !correctIsRegexpLogic('includes') }, {
	  includes: function includes(searchString /* , position = 0 */) {
	    return !!~String(requireObjectCoercible(this))
	      .indexOf(notARegexp(searchString), arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var includes = entryUnbind('String', 'includes');

	var nativeEndsWith = ''.endsWith;
	var min$3 = Math.min;

	// `String.prototype.endsWith` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.endswith
	_export({ target: 'String', proto: true, forced: !correctIsRegexpLogic('endsWith') }, {
	  endsWith: function endsWith(searchString /* , endPosition = @length */) {
	    var that = String(requireObjectCoercible(this));
	    notARegexp(searchString);
	    var endPosition = arguments.length > 1 ? arguments[1] : undefined;
	    var len = toLength(that.length);
	    var end = endPosition === undefined ? len : min$3(toLength(endPosition), len);
	    var search = String(searchString);
	    return nativeEndsWith
	      ? nativeEndsWith.call(that, search, end)
	      : that.slice(end - search.length, end) === search;
	  }
	});

	var endsWith = entryUnbind('String', 'endsWith');

	// `String.prototype.repeat` method implementation
	// https://tc39.github.io/ecma262/#sec-string.prototype.repeat
	var stringRepeat = ''.repeat || function repeat(count) {
	  var str = String(requireObjectCoercible(this));
	  var result = '';
	  var n = toInteger(count);
	  if (n < 0 || n == Infinity) throw RangeError('Wrong number of repetitions');
	  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) result += str;
	  return result;
	};

	// `String.prototype.repeat` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.repeat
	_export({ target: 'String', proto: true }, {
	  repeat: stringRepeat
	});

	var repeat = entryUnbind('String', 'repeat');

	var nativeStartsWith = ''.startsWith;
	var min$4 = Math.min;

	// `String.prototype.startsWith` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.startswith
	_export({ target: 'String', proto: true, forced: !correctIsRegexpLogic('startsWith') }, {
	  startsWith: function startsWith(searchString /* , position = 0 */) {
	    var that = String(requireObjectCoercible(this));
	    notARegexp(searchString);
	    var index = toLength(min$4(arguments.length > 1 ? arguments[1] : undefined, that.length));
	    var search = String(searchString);
	    return nativeStartsWith
	      ? nativeStartsWith.call(that, search, index)
	      : that.slice(index, index + search.length) === search;
	  }
	});

	var startsWith = entryUnbind('String', 'startsWith');

	var quot = /"/g;

	// B.2.3.2.1 CreateHTML(string, tag, attribute, value)
	// https://tc39.github.io/ecma262/#sec-createhtml
	var createHtml = function (string, tag, attribute, value) {
	  var S = String(requireObjectCoercible(string));
	  var p1 = '<' + tag;
	  if (attribute !== '') p1 += ' ' + attribute + '="' + String(value).replace(quot, '&quot;') + '"';
	  return p1 + '>' + S + '</' + tag + '>';
	};

	// check the existence of a method, lowercase
	// of a tag and escaping quotes in arguments
	var forcedStringHtmlMethod = function (METHOD_NAME) {
	  return fails(function () {
	    var test = ''[METHOD_NAME]('"');
	    return test !== test.toLowerCase() || test.split('"').length > 3;
	  });
	};

	// `String.prototype.anchor` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.anchor
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('anchor') }, {
	  anchor: function anchor(name) {
	    return createHtml(this, 'a', 'name', name);
	  }
	});

	var anchor = entryUnbind('String', 'anchor');

	// `String.prototype.blink` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.blink
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('blink') }, {
	  blink: function blink() {
	    return createHtml(this, 'blink', '', '');
	  }
	});

	var blink = entryUnbind('String', 'blink');

	// `String.prototype.bold` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.bold
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('bold') }, {
	  bold: function bold() {
	    return createHtml(this, 'b', '', '');
	  }
	});

	var bold = entryUnbind('String', 'bold');

	// `String.prototype.fixed` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.fixed
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('fixed') }, {
	  fixed: function fixed() {
	    return createHtml(this, 'tt', '', '');
	  }
	});

	var fixed = entryUnbind('String', 'fixed');

	// `String.prototype.fontcolor` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.fontcolor
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('fontcolor') }, {
	  fontcolor: function fontcolor(color) {
	    return createHtml(this, 'font', 'color', color);
	  }
	});

	var fontcolor = entryUnbind('String', 'fontcolor');

	// `String.prototype.fontsize` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.fontsize
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('fontsize') }, {
	  fontsize: function fontsize(size) {
	    return createHtml(this, 'font', 'size', size);
	  }
	});

	var fontsize = entryUnbind('String', 'fontsize');

	// `String.prototype.italics` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.italics
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('italics') }, {
	  italics: function italics() {
	    return createHtml(this, 'i', '', '');
	  }
	});

	var italics = entryUnbind('String', 'italics');

	// `String.prototype.link` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.link
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('link') }, {
	  link: function link(url) {
	    return createHtml(this, 'a', 'href', url);
	  }
	});

	var link = entryUnbind('String', 'link');

	// `String.prototype.small` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.small
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('small') }, {
	  small: function small() {
	    return createHtml(this, 'small', '', '');
	  }
	});

	var small = entryUnbind('String', 'small');

	// `String.prototype.strike` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.strike
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('strike') }, {
	  strike: function strike() {
	    return createHtml(this, 'strike', '', '');
	  }
	});

	var strike = entryUnbind('String', 'strike');

	// `String.prototype.sub` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.sub
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('sub') }, {
	  sub: function sub() {
	    return createHtml(this, 'sub', '', '');
	  }
	});

	var sub = entryUnbind('String', 'sub');

	// `String.prototype.sup` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.sup
	_export({ target: 'String', proto: true, forced: forcedStringHtmlMethod('sup') }, {
	  sup: function sup() {
	    return createHtml(this, 'sup', '', '');
	  }
	});

	var sup = entryUnbind('String', 'sup');

	var fromCharCode = String.fromCharCode;
	var nativeFromCodePoint = String.fromCodePoint;

	// length should be 1, old FF problem
	var INCORRECT_LENGTH = !!nativeFromCodePoint && nativeFromCodePoint.length != 1;

	// `String.fromCodePoint` method
	// https://tc39.github.io/ecma262/#sec-string.fromcodepoint
	_export({ target: 'String', stat: true, forced: INCORRECT_LENGTH }, {
	  fromCodePoint: function fromCodePoint(x) { // eslint-disable-line no-unused-vars
	    var elements = [];
	    var length = arguments.length;
	    var i = 0;
	    var code;
	    while (length > i) {
	      code = +arguments[i++];
	      if (toAbsoluteIndex(code, 0x10FFFF) !== code) throw RangeError(code + ' is not a valid code point');
	      elements.push(code < 0x10000
	        ? fromCharCode(code)
	        : fromCharCode(((code -= 0x10000) >> 10) + 0xD800, code % 0x400 + 0xDC00)
	      );
	    } return elements.join('');
	  }
	});

	var fromCodePoint = path.String.fromCodePoint;

	// `String.raw` method
	// https://tc39.github.io/ecma262/#sec-string.raw
	_export({ target: 'String', stat: true }, {
	  raw: function raw(template) {
	    var rawTemplate = toIndexedObject(template.raw);
	    var literalSegments = toLength(rawTemplate.length);
	    var argumentsLength = arguments.length;
	    var elements = [];
	    var i = 0;
	    while (literalSegments > i) {
	      elements.push(String(rawTemplate[i++]));
	      if (i < argumentsLength) elements.push(String(arguments[i]));
	    } return elements.join('');
	  }
	});

	var raw = path.String.raw;

	var es2015Core = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2015Core);

	// `Symbol.iterator` well-known symbol
	// https://tc39.github.io/ecma262/#sec-symbol.iterator
	defineWellKnownSymbol('iterator');

	var iterator = wrappedWellKnownSymbol.f('iterator');

	var iterator$1 = entryUnbind('Array', 'values');

	var entries = entryUnbind('Array', 'entries');

	var keys$2 = entryUnbind('Array', 'keys');

	var values = entryUnbind('Array', 'values');

	var ITERATOR$6 = wellKnownSymbol('iterator');
	var Uint8Array = global_1.Uint8Array;
	var arrayValues = es_array_iterator.values;
	var arrayKeys = es_array_iterator.keys;
	var arrayEntries = es_array_iterator.entries;
	var aTypedArray$5 = arrayBufferViewCore.aTypedArray;
	var exportProto$1 = arrayBufferViewCore.exportProto;
	var nativeTypedArrayIterator = Uint8Array && Uint8Array.prototype[ITERATOR$6];

	var CORRECT_ITER_NAME = !!nativeTypedArrayIterator
	  && (nativeTypedArrayIterator.name == 'values' || nativeTypedArrayIterator.name == undefined);

	var typedArrayValues = function values() {
	  return arrayValues.call(aTypedArray$5(this));
	};

	// `%TypedArray%.prototype.entries` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.entries
	exportProto$1('entries', function entries() {
	  return arrayEntries.call(aTypedArray$5(this));
	});
	// `%TypedArray%.prototype.keys` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.keys
	exportProto$1('keys', function keys() {
	  return arrayKeys.call(aTypedArray$5(this));
	});
	// `%TypedArray%.prototype.values` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.values
	exportProto$1('values', typedArrayValues, !CORRECT_ITER_NAME);
	// `%TypedArray%.prototype[@@iterator]` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype-@@iterator
	exportProto$1(ITERATOR$6, typedArrayValues, !CORRECT_ITER_NAME);

	var es2015Iterable = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2015Iterable);

	var nativePromiseConstructor = global_1.Promise;

	var SPECIES$2 = wellKnownSymbol('species');

	// `SpeciesConstructor` abstract operation
	// https://tc39.github.io/ecma262/#sec-speciesconstructor
	var speciesConstructor = function (O, defaultConstructor) {
	  var C = anObject(O).constructor;
	  var S;
	  return C === undefined || (S = anObject(C)[SPECIES$2]) == undefined ? defaultConstructor : aFunction$1(S);
	};

	var location = global_1.location;
	var set$2 = global_1.setImmediate;
	var clear = global_1.clearImmediate;
	var process = global_1.process;
	var MessageChannel = global_1.MessageChannel;
	var Dispatch = global_1.Dispatch;
	var counter = 0;
	var queue = {};
	var ONREADYSTATECHANGE = 'onreadystatechange';
	var defer, channel, port;

	var run = function (id) {
	  // eslint-disable-next-line no-prototype-builtins
	  if (queue.hasOwnProperty(id)) {
	    var fn = queue[id];
	    delete queue[id];
	    fn();
	  }
	};

	var runner = function (id) {
	  return function () {
	    run(id);
	  };
	};

	var listener = function (event) {
	  run(event.data);
	};

	var post = function (id) {
	  // old engines have not location.origin
	  global_1.postMessage(id + '', location.protocol + '//' + location.host);
	};

	// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
	if (!set$2 || !clear) {
	  set$2 = function setImmediate(fn) {
	    var args = [];
	    var i = 1;
	    while (arguments.length > i) args.push(arguments[i++]);
	    queue[++counter] = function () {
	      // eslint-disable-next-line no-new-func
	      (typeof fn == 'function' ? fn : Function(fn)).apply(undefined, args);
	    };
	    defer(counter);
	    return counter;
	  };
	  clear = function clearImmediate(id) {
	    delete queue[id];
	  };
	  // Node.js 0.8-
	  if (classofRaw(process) == 'process') {
	    defer = function (id) {
	      process.nextTick(runner(id));
	    };
	  // Sphere (JS game engine) Dispatch API
	  } else if (Dispatch && Dispatch.now) {
	    defer = function (id) {
	      Dispatch.now(runner(id));
	    };
	  // Browsers with MessageChannel, includes WebWorkers
	  } else if (MessageChannel) {
	    channel = new MessageChannel();
	    port = channel.port2;
	    channel.port1.onmessage = listener;
	    defer = bindContext(port.postMessage, port, 1);
	  // Browsers with postMessage, skip WebWorkers
	  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
	  } else if (global_1.addEventListener && typeof postMessage == 'function' && !global_1.importScripts && !fails(post)) {
	    defer = post;
	    global_1.addEventListener('message', listener, false);
	  // IE8-
	  } else if (ONREADYSTATECHANGE in documentCreateElement('script')) {
	    defer = function (id) {
	      html.appendChild(documentCreateElement('script'))[ONREADYSTATECHANGE] = function () {
	        html.removeChild(this);
	        run(id);
	      };
	    };
	  // Rest old browsers
	  } else {
	    defer = function (id) {
	      setTimeout(runner(id), 0);
	    };
	  }
	}

	var task = {
	  set: set$2,
	  clear: clear
	};

	var userAgent = getBuiltIn('navigator', 'userAgent') || '';

	var getOwnPropertyDescriptor$2 = objectGetOwnPropertyDescriptor.f;

	var macrotask = task.set;


	var MutationObserver = global_1.MutationObserver || global_1.WebKitMutationObserver;
	var process$1 = global_1.process;
	var Promise = global_1.Promise;
	var IS_NODE = classofRaw(process$1) == 'process';
	// Node.js 11 shows ExperimentalWarning on getting `queueMicrotask`
	var queueMicrotaskDescriptor = getOwnPropertyDescriptor$2(global_1, 'queueMicrotask');
	var queueMicrotask = queueMicrotaskDescriptor && queueMicrotaskDescriptor.value;

	var flush, head, last, notify, toggle, node, promise, then;

	// modern engines have queueMicrotask method
	if (!queueMicrotask) {
	  flush = function () {
	    var parent, fn;
	    if (IS_NODE && (parent = process$1.domain)) parent.exit();
	    while (head) {
	      fn = head.fn;
	      head = head.next;
	      try {
	        fn();
	      } catch (error) {
	        if (head) notify();
	        else last = undefined;
	        throw error;
	      }
	    } last = undefined;
	    if (parent) parent.enter();
	  };

	  // Node.js
	  if (IS_NODE) {
	    notify = function () {
	      process$1.nextTick(flush);
	    };
	  // browsers with MutationObserver, except iOS - https://github.com/zloirock/core-js/issues/339
	  } else if (MutationObserver && !/(iphone|ipod|ipad).*applewebkit/i.test(userAgent)) {
	    toggle = true;
	    node = document.createTextNode('');
	    new MutationObserver(flush).observe(node, { characterData: true }); // eslint-disable-line no-new
	    notify = function () {
	      node.data = toggle = !toggle;
	    };
	  // environments with maybe non-completely correct, but existent Promise
	  } else if (Promise && Promise.resolve) {
	    // Promise.resolve without an argument throws an error in LG WebOS 2
	    promise = Promise.resolve(undefined);
	    then = promise.then;
	    notify = function () {
	      then.call(promise, flush);
	    };
	  // for other environments - macrotask based on:
	  // - setImmediate
	  // - MessageChannel
	  // - window.postMessag
	  // - onreadystatechange
	  // - setTimeout
	  } else {
	    notify = function () {
	      // strange IE + webpack dev server bug - use .call(global)
	      macrotask.call(global_1, flush);
	    };
	  }
	}

	var microtask = queueMicrotask || function (fn) {
	  var task = { fn: fn, next: undefined };
	  if (last) last.next = task;
	  if (!head) {
	    head = task;
	    notify();
	  } last = task;
	};

	var PromiseCapability = function (C) {
	  var resolve, reject;
	  this.promise = new C(function ($$resolve, $$reject) {
	    if (resolve !== undefined || reject !== undefined) throw TypeError('Bad Promise constructor');
	    resolve = $$resolve;
	    reject = $$reject;
	  });
	  this.resolve = aFunction$1(resolve);
	  this.reject = aFunction$1(reject);
	};

	// 25.4.1.5 NewPromiseCapability(C)
	var f$7 = function (C) {
	  return new PromiseCapability(C);
	};

	var newPromiseCapability = {
		f: f$7
	};

	var promiseResolve = function (C, x) {
	  anObject(C);
	  if (isObject(x) && x.constructor === C) return x;
	  var promiseCapability = newPromiseCapability.f(C);
	  var resolve = promiseCapability.resolve;
	  resolve(x);
	  return promiseCapability.promise;
	};

	var hostReportErrors = function (a, b) {
	  var console = global_1.console;
	  if (console && console.error) {
	    arguments.length === 1 ? console.error(a) : console.error(a, b);
	  }
	};

	var perform = function (exec) {
	  try {
	    return { error: false, value: exec() };
	  } catch (error) {
	    return { error: true, value: error };
	  }
	};

	var task$1 = task.set;










	var SPECIES$3 = wellKnownSymbol('species');
	var PROMISE = 'Promise';
	var getInternalState$3 = internalState.get;
	var setInternalState$5 = internalState.set;
	var getInternalPromiseState = internalState.getterFor(PROMISE);
	var PromiseConstructor = nativePromiseConstructor;
	var TypeError$1 = global_1.TypeError;
	var document$2 = global_1.document;
	var process$2 = global_1.process;
	var $fetch = global_1.fetch;
	var versions = process$2 && process$2.versions;
	var v8 = versions && versions.v8 || '';
	var newPromiseCapability$1 = newPromiseCapability.f;
	var newGenericPromiseCapability = newPromiseCapability$1;
	var IS_NODE$1 = classofRaw(process$2) == 'process';
	var DISPATCH_EVENT = !!(document$2 && document$2.createEvent && global_1.dispatchEvent);
	var UNHANDLED_REJECTION = 'unhandledrejection';
	var REJECTION_HANDLED = 'rejectionhandled';
	var PENDING = 0;
	var FULFILLED = 1;
	var REJECTED = 2;
	var HANDLED = 1;
	var UNHANDLED = 2;
	var Internal, OwnPromiseCapability, PromiseWrapper, nativeThen;

	var FORCED$6 = isForced_1(PROMISE, function () {
	  // correct subclassing with @@species support
	  var promise = PromiseConstructor.resolve(1);
	  var empty = function () { /* empty */ };
	  var FakePromise = (promise.constructor = {})[SPECIES$3] = function (exec) {
	    exec(empty, empty);
	  };
	  // unhandled rejections tracking support, NodeJS Promise without it fails @@species test
	  return !((IS_NODE$1 || typeof PromiseRejectionEvent == 'function')
	    && (!isPure || promise['finally'])
	    && promise.then(empty) instanceof FakePromise
	    // v8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
	    // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
	    // we can't detect it synchronously, so just check versions
	    && v8.indexOf('6.6') !== 0
	    && userAgent.indexOf('Chrome/66') === -1);
	});

	var INCORRECT_ITERATION$1 = FORCED$6 || !checkCorrectnessOfIteration(function (iterable) {
	  PromiseConstructor.all(iterable)['catch'](function () { /* empty */ });
	});

	// helpers
	var isThenable = function (it) {
	  var then;
	  return isObject(it) && typeof (then = it.then) == 'function' ? then : false;
	};

	var notify$1 = function (promise, state, isReject) {
	  if (state.notified) return;
	  state.notified = true;
	  var chain = state.reactions;
	  microtask(function () {
	    var value = state.value;
	    var ok = state.state == FULFILLED;
	    var index = 0;
	    // variable length - can't use forEach
	    while (chain.length > index) {
	      var reaction = chain[index++];
	      var handler = ok ? reaction.ok : reaction.fail;
	      var resolve = reaction.resolve;
	      var reject = reaction.reject;
	      var domain = reaction.domain;
	      var result, then, exited;
	      try {
	        if (handler) {
	          if (!ok) {
	            if (state.rejection === UNHANDLED) onHandleUnhandled(promise, state);
	            state.rejection = HANDLED;
	          }
	          if (handler === true) result = value;
	          else {
	            if (domain) domain.enter();
	            result = handler(value); // can throw
	            if (domain) {
	              domain.exit();
	              exited = true;
	            }
	          }
	          if (result === reaction.promise) {
	            reject(TypeError$1('Promise-chain cycle'));
	          } else if (then = isThenable(result)) {
	            then.call(result, resolve, reject);
	          } else resolve(result);
	        } else reject(value);
	      } catch (error) {
	        if (domain && !exited) domain.exit();
	        reject(error);
	      }
	    }
	    state.reactions = [];
	    state.notified = false;
	    if (isReject && !state.rejection) onUnhandled(promise, state);
	  });
	};

	var dispatchEvent = function (name, promise, reason) {
	  var event, handler;
	  if (DISPATCH_EVENT) {
	    event = document$2.createEvent('Event');
	    event.promise = promise;
	    event.reason = reason;
	    event.initEvent(name, false, true);
	    global_1.dispatchEvent(event);
	  } else event = { promise: promise, reason: reason };
	  if (handler = global_1['on' + name]) handler(event);
	  else if (name === UNHANDLED_REJECTION) hostReportErrors('Unhandled promise rejection', reason);
	};

	var onUnhandled = function (promise, state) {
	  task$1.call(global_1, function () {
	    var value = state.value;
	    var IS_UNHANDLED = isUnhandled(state);
	    var result;
	    if (IS_UNHANDLED) {
	      result = perform(function () {
	        if (IS_NODE$1) {
	          process$2.emit('unhandledRejection', value, promise);
	        } else dispatchEvent(UNHANDLED_REJECTION, promise, value);
	      });
	      // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
	      state.rejection = IS_NODE$1 || isUnhandled(state) ? UNHANDLED : HANDLED;
	      if (result.error) throw result.value;
	    }
	  });
	};

	var isUnhandled = function (state) {
	  return state.rejection !== HANDLED && !state.parent;
	};

	var onHandleUnhandled = function (promise, state) {
	  task$1.call(global_1, function () {
	    if (IS_NODE$1) {
	      process$2.emit('rejectionHandled', promise);
	    } else dispatchEvent(REJECTION_HANDLED, promise, state.value);
	  });
	};

	var bind = function (fn, promise, state, unwrap) {
	  return function (value) {
	    fn(promise, state, value, unwrap);
	  };
	};

	var internalReject = function (promise, state, value, unwrap) {
	  if (state.done) return;
	  state.done = true;
	  if (unwrap) state = unwrap;
	  state.value = value;
	  state.state = REJECTED;
	  notify$1(promise, state, true);
	};

	var internalResolve = function (promise, state, value, unwrap) {
	  if (state.done) return;
	  state.done = true;
	  if (unwrap) state = unwrap;
	  try {
	    if (promise === value) throw TypeError$1("Promise can't be resolved itself");
	    var then = isThenable(value);
	    if (then) {
	      microtask(function () {
	        var wrapper = { done: false };
	        try {
	          then.call(value,
	            bind(internalResolve, promise, wrapper, state),
	            bind(internalReject, promise, wrapper, state)
	          );
	        } catch (error) {
	          internalReject(promise, wrapper, error, state);
	        }
	      });
	    } else {
	      state.value = value;
	      state.state = FULFILLED;
	      notify$1(promise, state, false);
	    }
	  } catch (error) {
	    internalReject(promise, { done: false }, error, state);
	  }
	};

	// constructor polyfill
	if (FORCED$6) {
	  // 25.4.3.1 Promise(executor)
	  PromiseConstructor = function Promise(executor) {
	    anInstance(this, PromiseConstructor, PROMISE);
	    aFunction$1(executor);
	    Internal.call(this);
	    var state = getInternalState$3(this);
	    try {
	      executor(bind(internalResolve, this, state), bind(internalReject, this, state));
	    } catch (error) {
	      internalReject(this, state, error);
	    }
	  };
	  // eslint-disable-next-line no-unused-vars
	  Internal = function Promise(executor) {
	    setInternalState$5(this, {
	      type: PROMISE,
	      done: false,
	      notified: false,
	      parent: false,
	      reactions: [],
	      rejection: false,
	      state: PENDING,
	      value: undefined
	    });
	  };
	  Internal.prototype = redefineAll(PromiseConstructor.prototype, {
	    // `Promise.prototype.then` method
	    // https://tc39.github.io/ecma262/#sec-promise.prototype.then
	    then: function then(onFulfilled, onRejected) {
	      var state = getInternalPromiseState(this);
	      var reaction = newPromiseCapability$1(speciesConstructor(this, PromiseConstructor));
	      reaction.ok = typeof onFulfilled == 'function' ? onFulfilled : true;
	      reaction.fail = typeof onRejected == 'function' && onRejected;
	      reaction.domain = IS_NODE$1 ? process$2.domain : undefined;
	      state.parent = true;
	      state.reactions.push(reaction);
	      if (state.state != PENDING) notify$1(this, state, false);
	      return reaction.promise;
	    },
	    // `Promise.prototype.catch` method
	    // https://tc39.github.io/ecma262/#sec-promise.prototype.catch
	    'catch': function (onRejected) {
	      return this.then(undefined, onRejected);
	    }
	  });
	  OwnPromiseCapability = function () {
	    var promise = new Internal();
	    var state = getInternalState$3(promise);
	    this.promise = promise;
	    this.resolve = bind(internalResolve, promise, state);
	    this.reject = bind(internalReject, promise, state);
	  };
	  newPromiseCapability.f = newPromiseCapability$1 = function (C) {
	    return C === PromiseConstructor || C === PromiseWrapper
	      ? new OwnPromiseCapability(C)
	      : newGenericPromiseCapability(C);
	  };

	  if ( typeof nativePromiseConstructor == 'function') {
	    nativeThen = nativePromiseConstructor.prototype.then;

	    // wrap native Promise#then for native async functions
	    redefine(nativePromiseConstructor.prototype, 'then', function then(onFulfilled, onRejected) {
	      var that = this;
	      return new PromiseConstructor(function (resolve, reject) {
	        nativeThen.call(that, resolve, reject);
	      }).then(onFulfilled, onRejected);
	    });

	    // wrap fetch result
	    if (typeof $fetch == 'function') _export({ global: true, enumerable: true, forced: true }, {
	      // eslint-disable-next-line no-unused-vars
	      fetch: function fetch(input) {
	        return promiseResolve(PromiseConstructor, $fetch.apply(global_1, arguments));
	      }
	    });
	  }
	}

	_export({ global: true, wrap: true, forced: FORCED$6 }, {
	  Promise: PromiseConstructor
	});

	setToStringTag(PromiseConstructor, PROMISE, false);
	setSpecies(PROMISE);

	PromiseWrapper = path[PROMISE];

	// statics
	_export({ target: PROMISE, stat: true, forced: FORCED$6 }, {
	  // `Promise.reject` method
	  // https://tc39.github.io/ecma262/#sec-promise.reject
	  reject: function reject(r) {
	    var capability = newPromiseCapability$1(this);
	    capability.reject.call(undefined, r);
	    return capability.promise;
	  }
	});

	_export({ target: PROMISE, stat: true, forced:  FORCED$6 }, {
	  // `Promise.resolve` method
	  // https://tc39.github.io/ecma262/#sec-promise.resolve
	  resolve: function resolve(x) {
	    return promiseResolve( this, x);
	  }
	});

	_export({ target: PROMISE, stat: true, forced: INCORRECT_ITERATION$1 }, {
	  // `Promise.all` method
	  // https://tc39.github.io/ecma262/#sec-promise.all
	  all: function all(iterable) {
	    var C = this;
	    var capability = newPromiseCapability$1(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform(function () {
	      var $promiseResolve = aFunction$1(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate_1(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        values.push(undefined);
	        remaining++;
	        $promiseResolve.call(C, promise).then(function (value) {
	          if (alreadyCalled) return;
	          alreadyCalled = true;
	          values[index] = value;
	          --remaining || resolve(values);
	        }, reject);
	      });
	      --remaining || resolve(values);
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  },
	  // `Promise.race` method
	  // https://tc39.github.io/ecma262/#sec-promise.race
	  race: function race(iterable) {
	    var C = this;
	    var capability = newPromiseCapability$1(C);
	    var reject = capability.reject;
	    var result = perform(function () {
	      var $promiseResolve = aFunction$1(C.resolve);
	      iterate_1(iterable, function (promise) {
	        $promiseResolve.call(C, promise).then(capability.resolve, reject);
	      });
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  }
	});

	// `Promise.allSettled` method
	// https://github.com/tc39/proposal-promise-allSettled
	_export({ target: 'Promise', stat: true }, {
	  allSettled: function allSettled(iterable) {
	    var C = this;
	    var capability = newPromiseCapability.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform(function () {
	      var promiseResolve = aFunction$1(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate_1(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        values.push(undefined);
	        remaining++;
	        promiseResolve.call(C, promise).then(function (value) {
	          if (alreadyCalled) return;
	          alreadyCalled = true;
	          values[index] = { status: 'fulfilled', value: value };
	          --remaining || resolve(values);
	        }, function (e) {
	          if (alreadyCalled) return;
	          alreadyCalled = true;
	          values[index] = { status: 'rejected', reason: e };
	          --remaining || resolve(values);
	        });
	      });
	      --remaining || resolve(values);
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  }
	});

	// `Promise.prototype.finally` method
	// https://tc39.github.io/ecma262/#sec-promise.prototype.finally
	_export({ target: 'Promise', proto: true, real: true }, {
	  'finally': function (onFinally) {
	    var C = speciesConstructor(this, getBuiltIn('Promise'));
	    var isFunction = typeof onFinally == 'function';
	    return this.then(
	      isFunction ? function (x) {
	        return promiseResolve(C, onFinally()).then(function () { return x; });
	      } : onFinally,
	      isFunction ? function (e) {
	        return promiseResolve(C, onFinally()).then(function () { throw e; });
	      } : onFinally
	    );
	  }
	});

	// patch native Promise.prototype for native async functions
	if ( typeof nativePromiseConstructor == 'function' && !nativePromiseConstructor.prototype['finally']) {
	  redefine(nativePromiseConstructor.prototype, 'finally', getBuiltIn('Promise').prototype['finally']);
	}

	var promise$1 = path.Promise;

	var es2015Promise = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2015Promise);

	var nativeApply = getBuiltIn('Reflect', 'apply');
	var functionApply = Function.apply;

	// MS Edge argumentsList argument is optional
	var OPTIONAL_ARGUMENTS_LIST = !fails(function () {
	  nativeApply(function () { /* empty */ });
	});

	// `Reflect.apply` method
	// https://tc39.github.io/ecma262/#sec-reflect.apply
	_export({ target: 'Reflect', stat: true, forced: OPTIONAL_ARGUMENTS_LIST }, {
	  apply: function apply(target, thisArgument, argumentsList) {
	    aFunction$1(target);
	    anObject(argumentsList);
	    return nativeApply
	      ? nativeApply(target, thisArgument, argumentsList)
	      : functionApply.call(target, thisArgument, argumentsList);
	  }
	});

	var apply = path.Reflect.apply;

	var slice = [].slice;
	var factories = {};

	var construct = function (C, argsLength, args) {
	  if (!(argsLength in factories)) {
	    for (var list = [], i = 0; i < argsLength; i++) list[i] = 'a[' + i + ']';
	    // eslint-disable-next-line no-new-func
	    factories[argsLength] = Function('C,a', 'return new C(' + list.join(',') + ')');
	  } return factories[argsLength](C, args);
	};

	// `Function.prototype.bind` method implementation
	// https://tc39.github.io/ecma262/#sec-function.prototype.bind
	var functionBind = Function.bind || function bind(that /* , ...args */) {
	  var fn = aFunction$1(this);
	  var partArgs = slice.call(arguments, 1);
	  var boundFunction = function bound(/* args... */) {
	    var args = partArgs.concat(slice.call(arguments));
	    return this instanceof boundFunction ? construct(fn, args.length, args) : fn.apply(that, args);
	  };
	  if (isObject(fn.prototype)) boundFunction.prototype = fn.prototype;
	  return boundFunction;
	};

	var nativeConstruct = getBuiltIn('Reflect', 'construct');

	// `Reflect.construct` method
	// https://tc39.github.io/ecma262/#sec-reflect.construct
	// MS Edge supports only 2 arguments and argumentsList argument is optional
	// FF Nightly sets third argument as `new.target`, but does not create `this` from it
	var NEW_TARGET_BUG = fails(function () {
	  function F() { /* empty */ }
	  return !(nativeConstruct(function () { /* empty */ }, [], F) instanceof F);
	});
	var ARGS_BUG = !fails(function () {
	  nativeConstruct(function () { /* empty */ });
	});
	var FORCED$7 = NEW_TARGET_BUG || ARGS_BUG;

	_export({ target: 'Reflect', stat: true, forced: FORCED$7, sham: FORCED$7 }, {
	  construct: function construct(Target, args /* , newTarget */) {
	    aFunction$1(Target);
	    anObject(args);
	    var newTarget = arguments.length < 3 ? Target : aFunction$1(arguments[2]);
	    if (ARGS_BUG && !NEW_TARGET_BUG) return nativeConstruct(Target, args, newTarget);
	    if (Target == newTarget) {
	      // w/o altered newTarget, optimization for 0-4 arguments
	      switch (args.length) {
	        case 0: return new Target();
	        case 1: return new Target(args[0]);
	        case 2: return new Target(args[0], args[1]);
	        case 3: return new Target(args[0], args[1], args[2]);
	        case 4: return new Target(args[0], args[1], args[2], args[3]);
	      }
	      // w/o altered newTarget, lot of arguments case
	      var $args = [null];
	      $args.push.apply($args, args);
	      return new (functionBind.apply(Target, $args))();
	    }
	    // with altered newTarget, not support built-in constructors
	    var proto = newTarget.prototype;
	    var instance = objectCreate(isObject(proto) ? proto : Object.prototype);
	    var result = Function.apply.call(Target, instance, args);
	    return isObject(result) ? result : instance;
	  }
	});

	var construct$1 = path.Reflect.construct;

	// MS Edge has broken Reflect.defineProperty - throwing instead of returning false
	var ERROR_INSTEAD_OF_FALSE = fails(function () {
	  // eslint-disable-next-line no-undef
	  Reflect.defineProperty(objectDefineProperty.f({}, 1, { value: 1 }), 1, { value: 2 });
	});

	// `Reflect.defineProperty` method
	// https://tc39.github.io/ecma262/#sec-reflect.defineproperty
	_export({ target: 'Reflect', stat: true, forced: ERROR_INSTEAD_OF_FALSE, sham: !descriptors }, {
	  defineProperty: function defineProperty(target, propertyKey, attributes) {
	    anObject(target);
	    var key = toPrimitive(propertyKey, true);
	    anObject(attributes);
	    try {
	      objectDefineProperty.f(target, key, attributes);
	      return true;
	    } catch (error) {
	      return false;
	    }
	  }
	});

	var defineProperty$6 = path.Reflect.defineProperty;

	var getOwnPropertyDescriptor$3 = objectGetOwnPropertyDescriptor.f;

	// `Reflect.deleteProperty` method
	// https://tc39.github.io/ecma262/#sec-reflect.deleteproperty
	_export({ target: 'Reflect', stat: true }, {
	  deleteProperty: function deleteProperty(target, propertyKey) {
	    var descriptor = getOwnPropertyDescriptor$3(anObject(target), propertyKey);
	    return descriptor && !descriptor.configurable ? false : delete target[propertyKey];
	  }
	});

	var deleteProperty = path.Reflect.deleteProperty;

	// `Reflect.get` method
	// https://tc39.github.io/ecma262/#sec-reflect.get
	function get$1(target, propertyKey /* , receiver */) {
	  var receiver = arguments.length < 3 ? target : arguments[2];
	  var descriptor, prototype;
	  if (anObject(target) === receiver) return target[propertyKey];
	  if (descriptor = objectGetOwnPropertyDescriptor.f(target, propertyKey)) return has(descriptor, 'value')
	    ? descriptor.value
	    : descriptor.get === undefined
	      ? undefined
	      : descriptor.get.call(receiver);
	  if (isObject(prototype = objectGetPrototypeOf(target))) return get$1(prototype, propertyKey, receiver);
	}

	_export({ target: 'Reflect', stat: true }, {
	  get: get$1
	});

	var get$2 = path.Reflect.get;

	// `Reflect.getOwnPropertyDescriptor` method
	// https://tc39.github.io/ecma262/#sec-reflect.getownpropertydescriptor
	_export({ target: 'Reflect', stat: true, sham: !descriptors }, {
	  getOwnPropertyDescriptor: function getOwnPropertyDescriptor(target, propertyKey) {
	    return objectGetOwnPropertyDescriptor.f(anObject(target), propertyKey);
	  }
	});

	var getOwnPropertyDescriptor$4 = path.Reflect.getOwnPropertyDescriptor;

	// `Reflect.getPrototypeOf` method
	// https://tc39.github.io/ecma262/#sec-reflect.getprototypeof
	_export({ target: 'Reflect', stat: true, sham: !correctPrototypeGetter }, {
	  getPrototypeOf: function getPrototypeOf(target) {
	    return objectGetPrototypeOf(anObject(target));
	  }
	});

	var getPrototypeOf = path.Reflect.getPrototypeOf;

	// `Reflect.has` method
	// https://tc39.github.io/ecma262/#sec-reflect.has
	_export({ target: 'Reflect', stat: true }, {
	  has: function has(target, propertyKey) {
	    return propertyKey in target;
	  }
	});

	var has$2 = path.Reflect.has;

	var objectIsExtensible = Object.isExtensible;

	// `Reflect.isExtensible` method
	// https://tc39.github.io/ecma262/#sec-reflect.isextensible
	_export({ target: 'Reflect', stat: true }, {
	  isExtensible: function isExtensible(target) {
	    anObject(target);
	    return objectIsExtensible ? objectIsExtensible(target) : true;
	  }
	});

	var isExtensible = path.Reflect.isExtensible;

	// `Reflect.ownKeys` method
	// https://tc39.github.io/ecma262/#sec-reflect.ownkeys
	_export({ target: 'Reflect', stat: true }, {
	  ownKeys: ownKeys
	});

	var ownKeys$1 = path.Reflect.ownKeys;

	// `Reflect.preventExtensions` method
	// https://tc39.github.io/ecma262/#sec-reflect.preventextensions
	_export({ target: 'Reflect', stat: true, sham: !freezing }, {
	  preventExtensions: function preventExtensions(target) {
	    anObject(target);
	    try {
	      var objectPreventExtensions = getBuiltIn('Object', 'preventExtensions');
	      if (objectPreventExtensions) objectPreventExtensions(target);
	      return true;
	    } catch (error) {
	      return false;
	    }
	  }
	});

	var preventExtensions = path.Reflect.preventExtensions;

	// `Reflect.set` method
	// https://tc39.github.io/ecma262/#sec-reflect.set
	function set$3(target, propertyKey, V /* , receiver */) {
	  var receiver = arguments.length < 4 ? target : arguments[3];
	  var ownDescriptor = objectGetOwnPropertyDescriptor.f(anObject(target), propertyKey);
	  var existingDescriptor, prototype;
	  if (!ownDescriptor) {
	    if (isObject(prototype = objectGetPrototypeOf(target))) {
	      return set$3(prototype, propertyKey, V, receiver);
	    }
	    ownDescriptor = createPropertyDescriptor(0);
	  }
	  if (has(ownDescriptor, 'value')) {
	    if (ownDescriptor.writable === false || !isObject(receiver)) return false;
	    if (existingDescriptor = objectGetOwnPropertyDescriptor.f(receiver, propertyKey)) {
	      if (existingDescriptor.get || existingDescriptor.set || existingDescriptor.writable === false) return false;
	      existingDescriptor.value = V;
	      objectDefineProperty.f(receiver, propertyKey, existingDescriptor);
	    } else objectDefineProperty.f(receiver, propertyKey, createPropertyDescriptor(0, V));
	    return true;
	  }
	  return ownDescriptor.set === undefined ? false : (ownDescriptor.set.call(receiver, V), true);
	}

	_export({ target: 'Reflect', stat: true }, {
	  set: set$3
	});

	var set$4 = path.Reflect.set;

	// `Reflect.setPrototypeOf` method
	// https://tc39.github.io/ecma262/#sec-reflect.setprototypeof
	if (objectSetPrototypeOf) _export({ target: 'Reflect', stat: true }, {
	  setPrototypeOf: function setPrototypeOf(target, proto) {
	    anObject(target);
	    aPossiblePrototype(proto);
	    try {
	      objectSetPrototypeOf(target, proto);
	      return true;
	    } catch (error) {
	      return false;
	    }
	  }
	});

	var setPrototypeOf$1 = path.Reflect.setPrototypeOf;

	var es2015Reflect = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2015Reflect);

	var $includes = arrayIncludes.includes;


	// `Array.prototype.includes` method
	// https://tc39.github.io/ecma262/#sec-array.prototype.includes
	_export({ target: 'Array', proto: true }, {
	  includes: function includes(el /* , fromIndex = 0 */) {
	    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	// https://tc39.github.io/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables('includes');

	var includes$1 = entryUnbind('Array', 'includes');

	var $includes$1 = arrayIncludes.includes;

	var aTypedArray$6 = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.includes` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.includes
	arrayBufferViewCore.exportProto('includes', function includes(searchElement /* , fromIndex */) {
	  return $includes$1(aTypedArray$6(this), searchElement, arguments.length > 1 ? arguments[1] : undefined);
	});

	var es2016ArrayInclude = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2016ArrayInclude);

	var propertyIsEnumerable = objectPropertyIsEnumerable.f;

	// `Object.{ entries, values }` methods implementation
	var createMethod$4 = function (TO_ENTRIES) {
	  return function (it) {
	    var O = toIndexedObject(it);
	    var keys = objectKeys(O);
	    var length = keys.length;
	    var i = 0;
	    var result = [];
	    var key;
	    while (length > i) {
	      key = keys[i++];
	      if (!descriptors || propertyIsEnumerable.call(O, key)) {
	        result.push(TO_ENTRIES ? [key, O[key]] : O[key]);
	      }
	    }
	    return result;
	  };
	};

	var objectToArray = {
	  // `Object.entries` method
	  // https://tc39.github.io/ecma262/#sec-object.entries
	  entries: createMethod$4(true),
	  // `Object.values` method
	  // https://tc39.github.io/ecma262/#sec-object.values
	  values: createMethod$4(false)
	};

	var $values = objectToArray.values;

	// `Object.values` method
	// https://tc39.github.io/ecma262/#sec-object.values
	_export({ target: 'Object', stat: true }, {
	  values: function values(O) {
	    return $values(O);
	  }
	});

	var values$1 = path.Object.values;

	var $entries = objectToArray.entries;

	// `Object.entries` method
	// https://tc39.github.io/ecma262/#sec-object.entries
	_export({ target: 'Object', stat: true }, {
	  entries: function entries(O) {
	    return $entries(O);
	  }
	});

	var entries$1 = path.Object.entries;

	// `Object.getOwnPropertyDescriptors` method
	// https://tc39.github.io/ecma262/#sec-object.getownpropertydescriptors
	_export({ target: 'Object', stat: true, sham: !descriptors }, {
	  getOwnPropertyDescriptors: function getOwnPropertyDescriptors(object) {
	    var O = toIndexedObject(object);
	    var getOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
	    var keys = ownKeys(O);
	    var result = {};
	    var index = 0;
	    var key, descriptor;
	    while (keys.length > index) {
	      descriptor = getOwnPropertyDescriptor(O, key = keys[index++]);
	      if (descriptor !== undefined) createProperty(result, key, descriptor);
	    }
	    return result;
	  }
	});

	var getOwnPropertyDescriptors = path.Object.getOwnPropertyDescriptors;

	var es2017Object = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2017Object);

	// https://github.com/tc39/proposal-string-pad-start-end




	var ceil$2 = Math.ceil;

	// `String.prototype.{ padStart, padEnd }` methods implementation
	var createMethod$5 = function (IS_END) {
	  return function ($this, maxLength, fillString) {
	    var S = String(requireObjectCoercible($this));
	    var stringLength = S.length;
	    var fillStr = fillString === undefined ? ' ' : String(fillString);
	    var intMaxLength = toLength(maxLength);
	    var fillLen, stringFiller;
	    if (intMaxLength <= stringLength || fillStr == '') return S;
	    fillLen = intMaxLength - stringLength;
	    stringFiller = stringRepeat.call(fillStr, ceil$2(fillLen / fillStr.length));
	    if (stringFiller.length > fillLen) stringFiller = stringFiller.slice(0, fillLen);
	    return IS_END ? S + stringFiller : stringFiller + S;
	  };
	};

	var stringPad = {
	  // `String.prototype.padStart` method
	  // https://tc39.github.io/ecma262/#sec-string.prototype.padstart
	  start: createMethod$5(false),
	  // `String.prototype.padEnd` method
	  // https://tc39.github.io/ecma262/#sec-string.prototype.padend
	  end: createMethod$5(true)
	};

	// https://github.com/zloirock/core-js/issues/280


	// eslint-disable-next-line unicorn/no-unsafe-regex
	var webkitStringPadBug = /Version\/10\.\d+(\.\d+)?( Mobile\/\w+)? Safari\//.test(userAgent);

	var $padStart = stringPad.start;


	// `String.prototype.padStart` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.padstart
	_export({ target: 'String', proto: true, forced: webkitStringPadBug }, {
	  padStart: function padStart(maxLength /* , fillString = ' ' */) {
	    return $padStart(this, maxLength, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var padStart = entryUnbind('String', 'padStart');

	var $padEnd = stringPad.end;


	// `String.prototype.padEnd` method
	// https://tc39.github.io/ecma262/#sec-string.prototype.padend
	_export({ target: 'String', proto: true, forced: webkitStringPadBug }, {
	  padEnd: function padEnd(maxLength /* , fillString = ' ' */) {
	    return $padEnd(this, maxLength, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var padEnd = entryUnbind('String', 'padEnd');

	var es2017String = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2017String);

	// `ToIndex` abstract operation
	// https://tc39.github.io/ecma262/#sec-toindex
	var toIndex = function (it) {
	  if (it === undefined) return 0;
	  var number = toInteger(it);
	  var length = toLength(number);
	  if (number !== length) throw RangeError('Wrong length or index');
	  return length;
	};

	var arrayBuffer = createCommonjsModule(function (module, exports) {


	var NATIVE_ARRAY_BUFFER = arrayBufferViewCore.NATIVE_ARRAY_BUFFER;







	var getOwnPropertyNames = objectGetOwnPropertyNames.f;
	var defineProperty = objectDefineProperty.f;




	var getInternalState = internalState.get;
	var setInternalState = internalState.set;
	var ARRAY_BUFFER = 'ArrayBuffer';
	var DATA_VIEW = 'DataView';
	var PROTOTYPE = 'prototype';
	var WRONG_LENGTH = 'Wrong length';
	var WRONG_INDEX = 'Wrong index';
	var NativeArrayBuffer = global_1[ARRAY_BUFFER];
	var $ArrayBuffer = NativeArrayBuffer;
	var $DataView = global_1[DATA_VIEW];
	var Math = global_1.Math;
	var RangeError = global_1.RangeError;
	// eslint-disable-next-line no-shadow-restricted-names
	var Infinity = 1 / 0;
	var abs = Math.abs;
	var pow = Math.pow;
	var floor = Math.floor;
	var log = Math.log;
	var LN2 = Math.LN2;

	// IEEE754 conversions based on https://github.com/feross/ieee754
	var packIEEE754 = function (number, mantissaLength, bytes) {
	  var buffer = new Array(bytes);
	  var exponentLength = bytes * 8 - mantissaLength - 1;
	  var eMax = (1 << exponentLength) - 1;
	  var eBias = eMax >> 1;
	  var rt = mantissaLength === 23 ? pow(2, -24) - pow(2, -77) : 0;
	  var sign = number < 0 || number === 0 && 1 / number < 0 ? 1 : 0;
	  var index = 0;
	  var exponent, mantissa, c;
	  number = abs(number);
	  // eslint-disable-next-line no-self-compare
	  if (number != number || number === Infinity) {
	    // eslint-disable-next-line no-self-compare
	    mantissa = number != number ? 1 : 0;
	    exponent = eMax;
	  } else {
	    exponent = floor(log(number) / LN2);
	    if (number * (c = pow(2, -exponent)) < 1) {
	      exponent--;
	      c *= 2;
	    }
	    if (exponent + eBias >= 1) {
	      number += rt / c;
	    } else {
	      number += rt * pow(2, 1 - eBias);
	    }
	    if (number * c >= 2) {
	      exponent++;
	      c /= 2;
	    }
	    if (exponent + eBias >= eMax) {
	      mantissa = 0;
	      exponent = eMax;
	    } else if (exponent + eBias >= 1) {
	      mantissa = (number * c - 1) * pow(2, mantissaLength);
	      exponent = exponent + eBias;
	    } else {
	      mantissa = number * pow(2, eBias - 1) * pow(2, mantissaLength);
	      exponent = 0;
	    }
	  }
	  for (; mantissaLength >= 8; buffer[index++] = mantissa & 255, mantissa /= 256, mantissaLength -= 8);
	  exponent = exponent << mantissaLength | mantissa;
	  exponentLength += mantissaLength;
	  for (; exponentLength > 0; buffer[index++] = exponent & 255, exponent /= 256, exponentLength -= 8);
	  buffer[--index] |= sign * 128;
	  return buffer;
	};

	var unpackIEEE754 = function (buffer, mantissaLength) {
	  var bytes = buffer.length;
	  var exponentLength = bytes * 8 - mantissaLength - 1;
	  var eMax = (1 << exponentLength) - 1;
	  var eBias = eMax >> 1;
	  var nBits = exponentLength - 7;
	  var index = bytes - 1;
	  var sign = buffer[index--];
	  var exponent = sign & 127;
	  var mantissa;
	  sign >>= 7;
	  for (; nBits > 0; exponent = exponent * 256 + buffer[index], index--, nBits -= 8);
	  mantissa = exponent & (1 << -nBits) - 1;
	  exponent >>= -nBits;
	  nBits += mantissaLength;
	  for (; nBits > 0; mantissa = mantissa * 256 + buffer[index], index--, nBits -= 8);
	  if (exponent === 0) {
	    exponent = 1 - eBias;
	  } else if (exponent === eMax) {
	    return mantissa ? NaN : sign ? -Infinity : Infinity;
	  } else {
	    mantissa = mantissa + pow(2, mantissaLength);
	    exponent = exponent - eBias;
	  } return (sign ? -1 : 1) * mantissa * pow(2, exponent - mantissaLength);
	};

	var unpackInt32 = function (buffer) {
	  return buffer[3] << 24 | buffer[2] << 16 | buffer[1] << 8 | buffer[0];
	};

	var packInt8 = function (number) {
	  return [number & 0xFF];
	};

	var packInt16 = function (number) {
	  return [number & 0xFF, number >> 8 & 0xFF];
	};

	var packInt32 = function (number) {
	  return [number & 0xFF, number >> 8 & 0xFF, number >> 16 & 0xFF, number >> 24 & 0xFF];
	};

	var packFloat32 = function (number) {
	  return packIEEE754(number, 23, 4);
	};

	var packFloat64 = function (number) {
	  return packIEEE754(number, 52, 8);
	};

	var addGetter = function (Constructor, key) {
	  defineProperty(Constructor[PROTOTYPE], key, { get: function () { return getInternalState(this)[key]; } });
	};

	var get = function (view, count, index, isLittleEndian) {
	  var numIndex = +index;
	  var intIndex = toIndex(numIndex);
	  var store = getInternalState(view);
	  if (intIndex + count > store.byteLength) throw RangeError(WRONG_INDEX);
	  var bytes = getInternalState(store.buffer).bytes;
	  var start = intIndex + store.byteOffset;
	  var pack = bytes.slice(start, start + count);
	  return isLittleEndian ? pack : pack.reverse();
	};

	var set = function (view, count, index, conversion, value, isLittleEndian) {
	  var numIndex = +index;
	  var intIndex = toIndex(numIndex);
	  var store = getInternalState(view);
	  if (intIndex + count > store.byteLength) throw RangeError(WRONG_INDEX);
	  var bytes = getInternalState(store.buffer).bytes;
	  var start = intIndex + store.byteOffset;
	  var pack = conversion(+value);
	  for (var i = 0; i < count; i++) bytes[start + i] = pack[isLittleEndian ? i : count - i - 1];
	};

	if (!NATIVE_ARRAY_BUFFER) {
	  $ArrayBuffer = function ArrayBuffer(length) {
	    anInstance(this, $ArrayBuffer, ARRAY_BUFFER);
	    var byteLength = toIndex(length);
	    setInternalState(this, {
	      bytes: arrayFill.call(new Array(byteLength), 0),
	      byteLength: byteLength
	    });
	    if (!descriptors) this.byteLength = byteLength;
	  };

	  $DataView = function DataView(buffer, byteOffset, byteLength) {
	    anInstance(this, $DataView, DATA_VIEW);
	    anInstance(buffer, $ArrayBuffer, DATA_VIEW);
	    var bufferLength = getInternalState(buffer).byteLength;
	    var offset = toInteger(byteOffset);
	    if (offset < 0 || offset > bufferLength) throw RangeError('Wrong offset');
	    byteLength = byteLength === undefined ? bufferLength - offset : toLength(byteLength);
	    if (offset + byteLength > bufferLength) throw RangeError(WRONG_LENGTH);
	    setInternalState(this, {
	      buffer: buffer,
	      byteLength: byteLength,
	      byteOffset: offset
	    });
	    if (!descriptors) {
	      this.buffer = buffer;
	      this.byteLength = byteLength;
	      this.byteOffset = offset;
	    }
	  };

	  if (descriptors) {
	    addGetter($ArrayBuffer, 'byteLength');
	    addGetter($DataView, 'buffer');
	    addGetter($DataView, 'byteLength');
	    addGetter($DataView, 'byteOffset');
	  }

	  redefineAll($DataView[PROTOTYPE], {
	    getInt8: function getInt8(byteOffset) {
	      return get(this, 1, byteOffset)[0] << 24 >> 24;
	    },
	    getUint8: function getUint8(byteOffset) {
	      return get(this, 1, byteOffset)[0];
	    },
	    getInt16: function getInt16(byteOffset /* , littleEndian */) {
	      var bytes = get(this, 2, byteOffset, arguments.length > 1 ? arguments[1] : undefined);
	      return (bytes[1] << 8 | bytes[0]) << 16 >> 16;
	    },
	    getUint16: function getUint16(byteOffset /* , littleEndian */) {
	      var bytes = get(this, 2, byteOffset, arguments.length > 1 ? arguments[1] : undefined);
	      return bytes[1] << 8 | bytes[0];
	    },
	    getInt32: function getInt32(byteOffset /* , littleEndian */) {
	      return unpackInt32(get(this, 4, byteOffset, arguments.length > 1 ? arguments[1] : undefined));
	    },
	    getUint32: function getUint32(byteOffset /* , littleEndian */) {
	      return unpackInt32(get(this, 4, byteOffset, arguments.length > 1 ? arguments[1] : undefined)) >>> 0;
	    },
	    getFloat32: function getFloat32(byteOffset /* , littleEndian */) {
	      return unpackIEEE754(get(this, 4, byteOffset, arguments.length > 1 ? arguments[1] : undefined), 23);
	    },
	    getFloat64: function getFloat64(byteOffset /* , littleEndian */) {
	      return unpackIEEE754(get(this, 8, byteOffset, arguments.length > 1 ? arguments[1] : undefined), 52);
	    },
	    setInt8: function setInt8(byteOffset, value) {
	      set(this, 1, byteOffset, packInt8, value);
	    },
	    setUint8: function setUint8(byteOffset, value) {
	      set(this, 1, byteOffset, packInt8, value);
	    },
	    setInt16: function setInt16(byteOffset, value /* , littleEndian */) {
	      set(this, 2, byteOffset, packInt16, value, arguments.length > 2 ? arguments[2] : undefined);
	    },
	    setUint16: function setUint16(byteOffset, value /* , littleEndian */) {
	      set(this, 2, byteOffset, packInt16, value, arguments.length > 2 ? arguments[2] : undefined);
	    },
	    setInt32: function setInt32(byteOffset, value /* , littleEndian */) {
	      set(this, 4, byteOffset, packInt32, value, arguments.length > 2 ? arguments[2] : undefined);
	    },
	    setUint32: function setUint32(byteOffset, value /* , littleEndian */) {
	      set(this, 4, byteOffset, packInt32, value, arguments.length > 2 ? arguments[2] : undefined);
	    },
	    setFloat32: function setFloat32(byteOffset, value /* , littleEndian */) {
	      set(this, 4, byteOffset, packFloat32, value, arguments.length > 2 ? arguments[2] : undefined);
	    },
	    setFloat64: function setFloat64(byteOffset, value /* , littleEndian */) {
	      set(this, 8, byteOffset, packFloat64, value, arguments.length > 2 ? arguments[2] : undefined);
	    }
	  });
	} else {
	  if (!fails(function () {
	    NativeArrayBuffer(1);
	  }) || !fails(function () {
	    new NativeArrayBuffer(-1); // eslint-disable-line no-new
	  }) || fails(function () {
	    new NativeArrayBuffer(); // eslint-disable-line no-new
	    new NativeArrayBuffer(1.5); // eslint-disable-line no-new
	    new NativeArrayBuffer(NaN); // eslint-disable-line no-new
	    return NativeArrayBuffer.name != ARRAY_BUFFER;
	  })) {
	    $ArrayBuffer = function ArrayBuffer(length) {
	      anInstance(this, $ArrayBuffer);
	      return new NativeArrayBuffer(toIndex(length));
	    };
	    var ArrayBufferPrototype = $ArrayBuffer[PROTOTYPE] = NativeArrayBuffer[PROTOTYPE];
	    for (var keys = getOwnPropertyNames(NativeArrayBuffer), j = 0, key; keys.length > j;) {
	      if (!((key = keys[j++]) in $ArrayBuffer)) hide($ArrayBuffer, key, NativeArrayBuffer[key]);
	    }
	    ArrayBufferPrototype.constructor = $ArrayBuffer;
	  }
	  // iOS Safari 7.x bug
	  var testView = new $DataView(new $ArrayBuffer(2));
	  var nativeSetInt8 = $DataView[PROTOTYPE].setInt8;
	  testView.setInt8(0, 2147483648);
	  testView.setInt8(1, 2147483649);
	  if (testView.getInt8(0) || !testView.getInt8(1)) redefineAll($DataView[PROTOTYPE], {
	    setInt8: function setInt8(byteOffset, value) {
	      nativeSetInt8.call(this, byteOffset, value << 24 >> 24);
	    },
	    setUint8: function setUint8(byteOffset, value) {
	      nativeSetInt8.call(this, byteOffset, value << 24 >> 24);
	    }
	  }, { unsafe: true });
	}

	setToStringTag($ArrayBuffer, ARRAY_BUFFER);
	setToStringTag($DataView, DATA_VIEW);
	exports[ARRAY_BUFFER] = $ArrayBuffer;
	exports[DATA_VIEW] = $DataView;
	});

	var toOffset = function (it, BYTES) {
	  var offset = toInteger(it);
	  if (offset < 0 || offset % BYTES) throw RangeError('Wrong offset');
	  return offset;
	};

	var typedArrayConstructor = createCommonjsModule(function (module) {


















	var getOwnPropertyNames = objectGetOwnPropertyNames.f;

	var forEach = arrayIteration.forEach;





	var getInternalState = internalState.get;
	var setInternalState = internalState.set;
	var nativeDefineProperty = objectDefineProperty.f;
	var nativeGetOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
	var round = Math.round;
	var RangeError = global_1.RangeError;
	var ArrayBuffer = arrayBuffer.ArrayBuffer;
	var DataView = arrayBuffer.DataView;
	var NATIVE_ARRAY_BUFFER_VIEWS = arrayBufferViewCore.NATIVE_ARRAY_BUFFER_VIEWS;
	var TYPED_ARRAY_TAG = arrayBufferViewCore.TYPED_ARRAY_TAG;
	var TypedArray = arrayBufferViewCore.TypedArray;
	var TypedArrayPrototype = arrayBufferViewCore.TypedArrayPrototype;
	var aTypedArrayConstructor = arrayBufferViewCore.aTypedArrayConstructor;
	var isTypedArray = arrayBufferViewCore.isTypedArray;
	var BYTES_PER_ELEMENT = 'BYTES_PER_ELEMENT';
	var WRONG_LENGTH = 'Wrong length';

	var fromList = function (C, list) {
	  var index = 0;
	  var length = list.length;
	  var result = new (aTypedArrayConstructor(C))(length);
	  while (length > index) result[index] = list[index++];
	  return result;
	};

	var addGetter = function (it, key) {
	  nativeDefineProperty(it, key, { get: function () {
	    return getInternalState(this)[key];
	  } });
	};

	var isArrayBuffer = function (it) {
	  var klass;
	  return it instanceof ArrayBuffer || (klass = classof(it)) == 'ArrayBuffer' || klass == 'SharedArrayBuffer';
	};

	var isTypedArrayIndex = function (target, key) {
	  return isTypedArray(target)
	    && typeof key != 'symbol'
	    && key in target
	    && String(+key) == String(key);
	};

	var wrappedGetOwnPropertyDescriptor = function getOwnPropertyDescriptor(target, key) {
	  return isTypedArrayIndex(target, key = toPrimitive(key, true))
	    ? createPropertyDescriptor(2, target[key])
	    : nativeGetOwnPropertyDescriptor(target, key);
	};

	var wrappedDefineProperty = function defineProperty(target, key, descriptor) {
	  if (isTypedArrayIndex(target, key = toPrimitive(key, true))
	    && isObject(descriptor)
	    && has(descriptor, 'value')
	    && !has(descriptor, 'get')
	    && !has(descriptor, 'set')
	    // TODO: add validation descriptor w/o calling accessors
	    && !descriptor.configurable
	    && (!has(descriptor, 'writable') || descriptor.writable)
	    && (!has(descriptor, 'enumerable') || descriptor.enumerable)
	  ) {
	    target[key] = descriptor.value;
	    return target;
	  } return nativeDefineProperty(target, key, descriptor);
	};

	if (descriptors) {
	  if (!NATIVE_ARRAY_BUFFER_VIEWS) {
	    objectGetOwnPropertyDescriptor.f = wrappedGetOwnPropertyDescriptor;
	    objectDefineProperty.f = wrappedDefineProperty;
	    addGetter(TypedArrayPrototype, 'buffer');
	    addGetter(TypedArrayPrototype, 'byteOffset');
	    addGetter(TypedArrayPrototype, 'byteLength');
	    addGetter(TypedArrayPrototype, 'length');
	  }

	  _export({ target: 'Object', stat: true, forced: !NATIVE_ARRAY_BUFFER_VIEWS }, {
	    getOwnPropertyDescriptor: wrappedGetOwnPropertyDescriptor,
	    defineProperty: wrappedDefineProperty
	  });

	  // eslint-disable-next-line max-statements
	  module.exports = function (TYPE, BYTES, wrapper, CLAMPED) {
	    var CONSTRUCTOR_NAME = TYPE + (CLAMPED ? 'Clamped' : '') + 'Array';
	    var GETTER = 'get' + TYPE;
	    var SETTER = 'set' + TYPE;
	    var NativeTypedArrayConstructor = global_1[CONSTRUCTOR_NAME];
	    var TypedArrayConstructor = NativeTypedArrayConstructor;
	    var TypedArrayConstructorPrototype = TypedArrayConstructor && TypedArrayConstructor.prototype;
	    var exported = {};

	    var getter = function (that, index) {
	      var data = getInternalState(that);
	      return data.view[GETTER](index * BYTES + data.byteOffset, true);
	    };

	    var setter = function (that, index, value) {
	      var data = getInternalState(that);
	      if (CLAMPED) value = (value = round(value)) < 0 ? 0 : value > 0xFF ? 0xFF : value & 0xFF;
	      data.view[SETTER](index * BYTES + data.byteOffset, value, true);
	    };

	    var addElement = function (that, index) {
	      nativeDefineProperty(that, index, {
	        get: function () {
	          return getter(this, index);
	        },
	        set: function (value) {
	          return setter(this, index, value);
	        },
	        enumerable: true
	      });
	    };

	    if (!NATIVE_ARRAY_BUFFER_VIEWS) {
	      TypedArrayConstructor = wrapper(function (that, data, offset, $length) {
	        anInstance(that, TypedArrayConstructor, CONSTRUCTOR_NAME);
	        var index = 0;
	        var byteOffset = 0;
	        var buffer, byteLength, length;
	        if (!isObject(data)) {
	          length = toIndex(data);
	          byteLength = length * BYTES;
	          buffer = new ArrayBuffer(byteLength);
	        } else if (isArrayBuffer(data)) {
	          buffer = data;
	          byteOffset = toOffset(offset, BYTES);
	          var $len = data.byteLength;
	          if ($length === undefined) {
	            if ($len % BYTES) throw RangeError(WRONG_LENGTH);
	            byteLength = $len - byteOffset;
	            if (byteLength < 0) throw RangeError(WRONG_LENGTH);
	          } else {
	            byteLength = toLength($length) * BYTES;
	            if (byteLength + byteOffset > $len) throw RangeError(WRONG_LENGTH);
	          }
	          length = byteLength / BYTES;
	        } else if (isTypedArray(data)) {
	          return fromList(TypedArrayConstructor, data);
	        } else {
	          return typedArrayFrom.call(TypedArrayConstructor, data);
	        }
	        setInternalState(that, {
	          buffer: buffer,
	          byteOffset: byteOffset,
	          byteLength: byteLength,
	          length: length,
	          view: new DataView(buffer)
	        });
	        while (index < length) addElement(that, index++);
	      });

	      if (objectSetPrototypeOf) objectSetPrototypeOf(TypedArrayConstructor, TypedArray);
	      TypedArrayConstructorPrototype = TypedArrayConstructor.prototype = objectCreate(TypedArrayPrototype);
	    } else if (typedArraysConstructorsRequiresWrappers) {
	      TypedArrayConstructor = wrapper(function (dummy, data, typedArrayOffset, $length) {
	        anInstance(dummy, TypedArrayConstructor, CONSTRUCTOR_NAME);
	        if (!isObject(data)) return new NativeTypedArrayConstructor(toIndex(data));
	        if (isArrayBuffer(data)) return $length !== undefined
	          ? new NativeTypedArrayConstructor(data, toOffset(typedArrayOffset, BYTES), $length)
	          : typedArrayOffset !== undefined
	            ? new NativeTypedArrayConstructor(data, toOffset(typedArrayOffset, BYTES))
	            : new NativeTypedArrayConstructor(data);
	        if (isTypedArray(data)) return fromList(TypedArrayConstructor, data);
	        return typedArrayFrom.call(TypedArrayConstructor, data);
	      });

	      if (objectSetPrototypeOf) objectSetPrototypeOf(TypedArrayConstructor, TypedArray);
	      forEach(getOwnPropertyNames(NativeTypedArrayConstructor), function (key) {
	        if (!(key in TypedArrayConstructor)) hide(TypedArrayConstructor, key, NativeTypedArrayConstructor[key]);
	      });
	      TypedArrayConstructor.prototype = TypedArrayConstructorPrototype;
	    }

	    if (TypedArrayConstructorPrototype.constructor !== TypedArrayConstructor) {
	      hide(TypedArrayConstructorPrototype, 'constructor', TypedArrayConstructor);
	    }

	    if (TYPED_ARRAY_TAG) hide(TypedArrayConstructorPrototype, TYPED_ARRAY_TAG, CONSTRUCTOR_NAME);

	    exported[CONSTRUCTOR_NAME] = TypedArrayConstructor;

	    _export({
	      global: true, forced: TypedArrayConstructor != NativeTypedArrayConstructor, sham: !NATIVE_ARRAY_BUFFER_VIEWS
	    }, exported);

	    if (!(BYTES_PER_ELEMENT in TypedArrayConstructor)) {
	      hide(TypedArrayConstructor, BYTES_PER_ELEMENT, BYTES);
	    }

	    if (!(BYTES_PER_ELEMENT in TypedArrayConstructorPrototype)) {
	      hide(TypedArrayConstructorPrototype, BYTES_PER_ELEMENT, BYTES);
	    }

	    setSpecies(CONSTRUCTOR_NAME);
	  };
	} else module.exports = function () { /* empty */ };
	});

	// `Int8Array` constructor
	// https://tc39.github.io/ecma262/#sec-typedarray-objects
	typedArrayConstructor('Int8', 1, function (init) {
	  return function Int8Array(data, byteOffset, length) {
	    return init(this, data, byteOffset, length);
	  };
	});

	var $every = arrayIteration.every;

	var aTypedArray$7 = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.every` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.every
	arrayBufferViewCore.exportProto('every', function every(callbackfn /* , thisArg */) {
	  return $every(aTypedArray$7(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	});

	var $filter = arrayIteration.filter;


	var aTypedArray$8 = arrayBufferViewCore.aTypedArray;
	var aTypedArrayConstructor$3 = arrayBufferViewCore.aTypedArrayConstructor;

	// `%TypedArray%.prototype.filter` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.filter
	arrayBufferViewCore.exportProto('filter', function filter(callbackfn /* , thisArg */) {
	  var list = $filter(aTypedArray$8(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  var C = speciesConstructor(this, this.constructor);
	  var index = 0;
	  var length = list.length;
	  var result = new (aTypedArrayConstructor$3(C))(length);
	  while (length > index) result[index] = list[index++];
	  return result;
	});

	var $forEach$1 = arrayIteration.forEach;

	var aTypedArray$9 = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.forEach` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.foreach
	arrayBufferViewCore.exportProto('forEach', function forEach(callbackfn /* , thisArg */) {
	  $forEach$1(aTypedArray$9(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	});

	var $indexOf = arrayIncludes.indexOf;

	var aTypedArray$a = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.indexOf` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.indexof
	arrayBufferViewCore.exportProto('indexOf', function indexOf(searchElement /* , fromIndex */) {
	  return $indexOf(aTypedArray$a(this), searchElement, arguments.length > 1 ? arguments[1] : undefined);
	});

	var aTypedArray$b = arrayBufferViewCore.aTypedArray;
	var $join = [].join;

	// `%TypedArray%.prototype.join` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.join
	// eslint-disable-next-line no-unused-vars
	arrayBufferViewCore.exportProto('join', function join(separator) {
	  return $join.apply(aTypedArray$b(this), arguments);
	});

	var sloppyArrayMethod = function (METHOD_NAME, argument) {
	  var method = [][METHOD_NAME];
	  return !method || !fails(function () {
	    // eslint-disable-next-line no-useless-call,no-throw-literal
	    method.call(null, argument || function () { throw 1; }, 1);
	  });
	};

	var min$5 = Math.min;
	var nativeLastIndexOf = [].lastIndexOf;
	var NEGATIVE_ZERO = !!nativeLastIndexOf && 1 / [1].lastIndexOf(1, -0) < 0;
	var SLOPPY_METHOD = sloppyArrayMethod('lastIndexOf');

	// `Array.prototype.lastIndexOf` method implementation
	// https://tc39.github.io/ecma262/#sec-array.prototype.lastindexof
	var arrayLastIndexOf = (NEGATIVE_ZERO || SLOPPY_METHOD) ? function lastIndexOf(searchElement /* , fromIndex = @[*-1] */) {
	  // convert -0 to +0
	  if (NEGATIVE_ZERO) return nativeLastIndexOf.apply(this, arguments) || 0;
	  var O = toIndexedObject(this);
	  var length = toLength(O.length);
	  var index = length - 1;
	  if (arguments.length > 1) index = min$5(index, toInteger(arguments[1]));
	  if (index < 0) index = length + index;
	  for (;index >= 0; index--) if (index in O && O[index] === searchElement) return index || 0;
	  return -1;
	} : nativeLastIndexOf;

	var aTypedArray$c = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.lastIndexOf` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.lastindexof
	// eslint-disable-next-line no-unused-vars
	arrayBufferViewCore.exportProto('lastIndexOf', function lastIndexOf(searchElement /* , fromIndex */) {
	  return arrayLastIndexOf.apply(aTypedArray$c(this), arguments);
	});

	var $map = arrayIteration.map;


	var aTypedArray$d = arrayBufferViewCore.aTypedArray;
	var aTypedArrayConstructor$4 = arrayBufferViewCore.aTypedArrayConstructor;

	// `%TypedArray%.prototype.map` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.map
	arrayBufferViewCore.exportProto('map', function map(mapfn /* , thisArg */) {
	  return $map(aTypedArray$d(this), mapfn, arguments.length > 1 ? arguments[1] : undefined, function (O, length) {
	    return new (aTypedArrayConstructor$4(speciesConstructor(O, O.constructor)))(length);
	  });
	});

	// `Array.prototype.{ reduce, reduceRight }` methods implementation
	var createMethod$6 = function (IS_RIGHT) {
	  return function (that, callbackfn, argumentsLength, memo) {
	    aFunction$1(callbackfn);
	    var O = toObject(that);
	    var self = indexedObject(O);
	    var length = toLength(O.length);
	    var index = IS_RIGHT ? length - 1 : 0;
	    var i = IS_RIGHT ? -1 : 1;
	    if (argumentsLength < 2) while (true) {
	      if (index in self) {
	        memo = self[index];
	        index += i;
	        break;
	      }
	      index += i;
	      if (IS_RIGHT ? index < 0 : length <= index) {
	        throw TypeError('Reduce of empty array with no initial value');
	      }
	    }
	    for (;IS_RIGHT ? index >= 0 : length > index; index += i) if (index in self) {
	      memo = callbackfn(memo, self[index], index, O);
	    }
	    return memo;
	  };
	};

	var arrayReduce = {
	  // `Array.prototype.reduce` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.reduce
	  left: createMethod$6(false),
	  // `Array.prototype.reduceRight` method
	  // https://tc39.github.io/ecma262/#sec-array.prototype.reduceright
	  right: createMethod$6(true)
	};

	var $reduce = arrayReduce.left;

	var aTypedArray$e = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.reduce` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.reduce
	arrayBufferViewCore.exportProto('reduce', function reduce(callbackfn /* , initialValue */) {
	  return $reduce(aTypedArray$e(this), callbackfn, arguments.length, arguments.length > 1 ? arguments[1] : undefined);
	});

	var $reduceRight = arrayReduce.right;

	var aTypedArray$f = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.reduceRicht` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.reduceright
	arrayBufferViewCore.exportProto('reduceRight', function reduceRight(callbackfn /* , initialValue */) {
	  return $reduceRight(aTypedArray$f(this), callbackfn, arguments.length, arguments.length > 1 ? arguments[1] : undefined);
	});

	var aTypedArray$g = arrayBufferViewCore.aTypedArray;
	var floor$4 = Math.floor;

	// `%TypedArray%.prototype.reverse` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.reverse
	arrayBufferViewCore.exportProto('reverse', function reverse() {
	  var that = this;
	  var length = aTypedArray$g(that).length;
	  var middle = floor$4(length / 2);
	  var index = 0;
	  var value;
	  while (index < middle) {
	    value = that[index];
	    that[index++] = that[--length];
	    that[length] = value;
	  } return that;
	});

	var aTypedArray$h = arrayBufferViewCore.aTypedArray;

	var FORCED$8 = fails(function () {
	  // eslint-disable-next-line no-undef
	  new Int8Array(1).set({});
	});

	// `%TypedArray%.prototype.set` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.set
	arrayBufferViewCore.exportProto('set', function set(arrayLike /* , offset */) {
	  aTypedArray$h(this);
	  var offset = toOffset(arguments.length > 1 ? arguments[1] : undefined, 1);
	  var length = this.length;
	  var src = toObject(arrayLike);
	  var len = toLength(src.length);
	  var index = 0;
	  if (len + offset > length) throw RangeError('Wrong length');
	  while (index < len) this[offset + index] = src[index++];
	}, FORCED$8);

	var aTypedArray$i = arrayBufferViewCore.aTypedArray;
	var aTypedArrayConstructor$5 = arrayBufferViewCore.aTypedArrayConstructor;
	var $slice = [].slice;

	var FORCED$9 = fails(function () {
	  // eslint-disable-next-line no-undef
	  new Int8Array(1).slice();
	});

	// `%TypedArray%.prototype.slice` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.slice
	arrayBufferViewCore.exportProto('slice', function slice(start, end) {
	  var list = $slice.call(aTypedArray$i(this), start, end);
	  var C = speciesConstructor(this, this.constructor);
	  var index = 0;
	  var length = list.length;
	  var result = new (aTypedArrayConstructor$5(C))(length);
	  while (length > index) result[index] = list[index++];
	  return result;
	}, FORCED$9);

	var $some = arrayIteration.some;

	var aTypedArray$j = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.some` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.some
	arrayBufferViewCore.exportProto('some', function some(callbackfn /* , thisArg */) {
	  return $some(aTypedArray$j(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	});

	var aTypedArray$k = arrayBufferViewCore.aTypedArray;
	var $sort = [].sort;

	// `%TypedArray%.prototype.sort` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.sort
	arrayBufferViewCore.exportProto('sort', function sort(comparefn) {
	  return $sort.call(aTypedArray$k(this), comparefn);
	});

	var aTypedArray$l = arrayBufferViewCore.aTypedArray;

	// `%TypedArray%.prototype.subarray` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.subarray
	arrayBufferViewCore.exportProto('subarray', function subarray(begin, end) {
	  var O = aTypedArray$l(this);
	  var length = O.length;
	  var beginIndex = toAbsoluteIndex(begin, length);
	  return new (speciesConstructor(O, O.constructor))(
	    O.buffer,
	    O.byteOffset + beginIndex * O.BYTES_PER_ELEMENT,
	    toLength((end === undefined ? length : toAbsoluteIndex(end, length)) - beginIndex)
	  );
	});

	var Int8Array$3 = global_1.Int8Array;
	var aTypedArray$m = arrayBufferViewCore.aTypedArray;
	var $toLocaleString = [].toLocaleString;
	var $slice$1 = [].slice;

	// iOS Safari 6.x fails here
	var TO_LOCALE_STRING_BUG = !!Int8Array$3 && fails(function () {
	  $toLocaleString.call(new Int8Array$3(1));
	});

	var FORCED$a = fails(function () {
	  return [1, 2].toLocaleString() != new Int8Array$3([1, 2]).toLocaleString();
	}) || !fails(function () {
	  Int8Array$3.prototype.toLocaleString.call([1, 2]);
	});

	// `%TypedArray%.prototype.toLocaleString` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.tolocalestring
	arrayBufferViewCore.exportProto('toLocaleString', function toLocaleString() {
	  return $toLocaleString.apply(TO_LOCALE_STRING_BUG ? $slice$1.call(aTypedArray$m(this)) : aTypedArray$m(this), arguments);
	}, FORCED$a);

	var Uint8Array$1 = global_1.Uint8Array;
	var Uint8ArrayPrototype = Uint8Array$1 && Uint8Array$1.prototype;
	var arrayToString = [].toString;
	var arrayJoin = [].join;

	if (fails(function () { arrayToString.call({}); })) {
	  arrayToString = function toString() {
	    return arrayJoin.call(this);
	  };
	}

	// `%TypedArray%.prototype.toString` method
	// https://tc39.github.io/ecma262/#sec-%typedarray%.prototype.tostring
	arrayBufferViewCore.exportProto('toString', arrayToString, (Uint8ArrayPrototype || {}).toString != arrayToString);

	var int8Array = global_1.Int8Array;

	// `Uint8Array` constructor
	// https://tc39.github.io/ecma262/#sec-typedarray-objects
	typedArrayConstructor('Uint8', 1, function (init) {
	  return function Uint8Array(data, byteOffset, length) {
	    return init(this, data, byteOffset, length);
	  };
	});

	var uint8Array = global_1.Uint8Array;

	// `Uint8ClampedArray` constructor
	// https://tc39.github.io/ecma262/#sec-typedarray-objects
	typedArrayConstructor('Uint8', 1, function (init) {
	  return function Uint8ClampedArray(data, byteOffset, length) {
	    return init(this, data, byteOffset, length);
	  };
	}, true);

	var uint8ClampedArray = global_1.Uint8ClampedArray;

	// `Int16Array` constructor
	// https://tc39.github.io/ecma262/#sec-typedarray-objects
	typedArrayConstructor('Int16', 2, function (init) {
	  return function Int16Array(data, byteOffset, length) {
	    return init(this, data, byteOffset, length);
	  };
	});

	var int16Array = global_1.Int16Array;

	// `Uint16Array` constructor
	// https://tc39.github.io/ecma262/#sec-typedarray-objects
	typedArrayConstructor('Uint16', 2, function (init) {
	  return function Uint16Array(data, byteOffset, length) {
	    return init(this, data, byteOffset, length);
	  };
	});

	var uint16Array = global_1.Uint16Array;

	// `Int32Array` constructor
	// https://tc39.github.io/ecma262/#sec-typedarray-objects
	typedArrayConstructor('Int32', 4, function (init) {
	  return function Int32Array(data, byteOffset, length) {
	    return init(this, data, byteOffset, length);
	  };
	});

	var int32Array = global_1.Int32Array;

	// `Uint32Array` constructor
	// https://tc39.github.io/ecma262/#sec-typedarray-objects
	typedArrayConstructor('Uint32', 4, function (init) {
	  return function Uint32Array(data, byteOffset, length) {
	    return init(this, data, byteOffset, length);
	  };
	});

	var uint32Array = global_1.Uint32Array;

	// `Float32Array` constructor
	// https://tc39.github.io/ecma262/#sec-typedarray-objects
	typedArrayConstructor('Float32', 4, function (init) {
	  return function Float32Array(data, byteOffset, length) {
	    return init(this, data, byteOffset, length);
	  };
	});

	var float32Array = global_1.Float32Array;

	// `Float64Array` constructor
	// https://tc39.github.io/ecma262/#sec-typedarray-objects
	typedArrayConstructor('Float64', 8, function (init) {
	  return function Float64Array(data, byteOffset, length) {
	    return init(this, data, byteOffset, length);
	  };
	});

	var float64Array = global_1.Float64Array;

	var es2017TypedArrays = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2017TypedArrays);

	var _finally = entryUnbind('Promise', 'finally');

	var es2018Promise = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2018Promise);

	// `FlattenIntoArray` abstract operation
	// https://tc39.github.io/proposal-flatMap/#sec-FlattenIntoArray
	var flattenIntoArray = function (target, original, source, sourceLen, start, depth, mapper, thisArg) {
	  var targetIndex = start;
	  var sourceIndex = 0;
	  var mapFn = mapper ? bindContext(mapper, thisArg, 3) : false;
	  var element;

	  while (sourceIndex < sourceLen) {
	    if (sourceIndex in source) {
	      element = mapFn ? mapFn(source[sourceIndex], sourceIndex, original) : source[sourceIndex];

	      if (depth > 0 && isArray(element)) {
	        targetIndex = flattenIntoArray(target, original, element, toLength(element.length), targetIndex, depth - 1) - 1;
	      } else {
	        if (targetIndex >= 0x1FFFFFFFFFFFFF) throw TypeError('Exceed the acceptable array length');
	        target[targetIndex] = element;
	      }

	      targetIndex++;
	    }
	    sourceIndex++;
	  }
	  return targetIndex;
	};

	var flattenIntoArray_1 = flattenIntoArray;

	// `Array.prototype.flatMap` method
	// https://github.com/tc39/proposal-flatMap
	_export({ target: 'Array', proto: true }, {
	  flatMap: function flatMap(callbackfn /* , thisArg */) {
	    var O = toObject(this);
	    var sourceLen = toLength(O.length);
	    var A;
	    aFunction$1(callbackfn);
	    A = arraySpeciesCreate(O, 0);
	    A.length = flattenIntoArray_1(A, O, O, sourceLen, 0, 1, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	    return A;
	  }
	});

	// this method was added to unscopables after implementation
	// in popular engines, so it's moved to a separate module


	addToUnscopables('flatMap');

	var flatMap = entryUnbind('Array', 'flatMap');

	// `Array.prototype.flat` method
	// https://github.com/tc39/proposal-flatMap
	_export({ target: 'Array', proto: true }, {
	  flat: function flat(/* depthArg = 1 */) {
	    var depthArg = arguments.length ? arguments[0] : undefined;
	    var O = toObject(this);
	    var sourceLen = toLength(O.length);
	    var A = arraySpeciesCreate(O, 0);
	    A.length = flattenIntoArray_1(A, O, O, sourceLen, 0, depthArg === undefined ? 1 : toInteger(depthArg));
	    return A;
	  }
	});

	// this method was added to unscopables after implementation
	// in popular engines, so it's moved to a separate module


	addToUnscopables('flat');

	var flat = entryUnbind('Array', 'flat');

	var es2019Array = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2019Array);

	var non = '\u200B\u0085\u180E';

	// check that a method works with the correct list
	// of whitespaces and has a correct name
	var forcedStringTrimMethod = function (METHOD_NAME) {
	  return fails(function () {
	    return !!whitespaces[METHOD_NAME]() || non[METHOD_NAME]() != non || whitespaces[METHOD_NAME].name !== METHOD_NAME;
	  });
	};

	var $trimEnd = stringTrim.end;


	var FORCED$b = forcedStringTrimMethod('trimEnd');

	var trimEnd = FORCED$b ? function trimEnd() {
	  return $trimEnd(this);
	} : ''.trimEnd;

	// `String.prototype.{ trimEnd, trimRight }` methods
	// https://github.com/tc39/ecmascript-string-left-right-trim
	_export({ target: 'String', proto: true, forced: FORCED$b }, {
	  trimEnd: trimEnd,
	  trimRight: trimEnd
	});

	var trimEnd$1 = entryUnbind('String', 'trimRight');

	var $trimStart = stringTrim.start;


	var FORCED$c = forcedStringTrimMethod('trimStart');

	var trimStart = FORCED$c ? function trimStart() {
	  return $trimStart(this);
	} : ''.trimStart;

	// `String.prototype.{ trimStart, trimLeft }` methods
	// https://github.com/tc39/ecmascript-string-left-right-trim
	_export({ target: 'String', proto: true, forced: FORCED$c }, {
	  trimStart: trimStart,
	  trimLeft: trimStart
	});

	var trimStart$1 = entryUnbind('String', 'trimLeft');

	var trimLeft = entryUnbind('String', 'trimLeft');

	var trimRight = entryUnbind('String', 'trimRight');

	var es2019String = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2019String);

	// `Object.fromEntries` method
	// https://github.com/tc39/proposal-object-from-entries
	_export({ target: 'Object', stat: true }, {
	  fromEntries: function fromEntries(iterable) {
	    var obj = {};
	    iterate_1(iterable, function (k, v) {
	      createProperty(obj, k, v);
	    }, undefined, true);
	    return obj;
	  }
	});

	var fromEntries = path.Object.fromEntries;

	var es2019Object = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2019Object);

	var charAt$1 = stringMultibyte.charAt;

	// `AdvanceStringIndex` abstract operation
	// https://tc39.github.io/ecma262/#sec-advancestringindex
	var advanceStringIndex = function (S, index, unicode) {
	  return index + (unicode ? charAt$1(S, index).length : 1);
	};

	var MATCH_ALL = wellKnownSymbol('matchAll');
	var REGEXP_STRING = 'RegExp String';
	var REGEXP_STRING_ITERATOR = REGEXP_STRING + ' Iterator';
	var setInternalState$6 = internalState.set;
	var getInternalState$4 = internalState.getterFor(REGEXP_STRING_ITERATOR);
	var RegExpPrototype$1 = RegExp.prototype;
	var regExpBuiltinExec = RegExpPrototype$1.exec;

	var regExpExec = function (R, S) {
	  var exec = R.exec;
	  var result;
	  if (typeof exec == 'function') {
	    result = exec.call(R, S);
	    if (typeof result != 'object') throw TypeError('Incorrect exec result');
	    return result;
	  } return regExpBuiltinExec.call(R, S);
	};

	// eslint-disable-next-line max-len
	var $RegExpStringIterator = createIteratorConstructor(function RegExpStringIterator(regexp, string, global, fullUnicode) {
	  setInternalState$6(this, {
	    type: REGEXP_STRING_ITERATOR,
	    regexp: regexp,
	    string: string,
	    global: global,
	    unicode: fullUnicode,
	    done: false
	  });
	}, REGEXP_STRING, function next() {
	  var state = getInternalState$4(this);
	  if (state.done) return { value: undefined, done: true };
	  var R = state.regexp;
	  var S = state.string;
	  var match = regExpExec(R, S);
	  if (match === null) return { value: undefined, done: state.done = true };
	  if (state.global) {
	    if (String(match[0]) == '') R.lastIndex = advanceStringIndex(S, toLength(R.lastIndex), state.unicode);
	    return { value: match, done: false };
	  }
	  state.done = true;
	  return { value: match, done: false };
	});

	var $matchAll = function (string) {
	  var R = anObject(this);
	  var S = String(string);
	  var C, flagsValue, flags, matcher, global, fullUnicode;
	  C = speciesConstructor(R, RegExp);
	  flagsValue = R.flags;
	  if (flagsValue === undefined && R instanceof RegExp && !('flags' in RegExpPrototype$1)) {
	    flagsValue = regexpFlags.call(R);
	  }
	  flags = flagsValue === undefined ? '' : String(flagsValue);
	  matcher = new C(C === RegExp ? R.source : R, flags);
	  global = !!~flags.indexOf('g');
	  fullUnicode = !!~flags.indexOf('u');
	  matcher.lastIndex = toLength(R.lastIndex);
	  return new $RegExpStringIterator(matcher, S, global, fullUnicode);
	};

	// `String.prototype.matchAll` method
	// https://github.com/tc39/proposal-string-matchall
	_export({ target: 'String', proto: true }, {
	  matchAll: function matchAll(regexp) {
	    var O = requireObjectCoercible(this);
	    var S, matcher, rx;
	    if (regexp != null) {
	      matcher = regexp[MATCH_ALL];
	      if (matcher === undefined && isPure && classof(regexp) == 'RegExp') matcher = $matchAll;
	      if (matcher != null) return aFunction$1(matcher).call(regexp, O);
	    }
	    S = String(O);
	    rx = new RegExp(regexp, 'g');
	    return  rx[MATCH_ALL](S);
	  }
	});

	 MATCH_ALL in RegExpPrototype$1 || hide(RegExpPrototype$1, MATCH_ALL, $matchAll);

	var matchAll = entryUnbind('String', 'matchAll');

	var es2020String = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	unwrapExports(es2020String);

	var lib = createCommonjsModule(function (module, exports) {
	Object.defineProperty(exports, "__esModule", { value: true });
	});

	var index$1 = unwrapExports(lib);

	return index$1;

}());

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ "./sources/js/vendor.js":
/*!******************************!*\
  !*** ./sources/js/vendor.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! script-loader!dom4/build/dom4.max.js */ "./node_modules/script-loader/index.js!./node_modules/dom4/build/dom4.max.js");
__webpack_require__(/*! script-loader!./ObjectAssignPoly.js */ "./node_modules/script-loader/index.js!./sources/js/ObjectAssignPoly.js");
__webpack_require__(/*! script-loader!EasyTabs/vendor/jquery.hashchange.min.js */ "./node_modules/script-loader/index.js!./node_modules/EasyTabs/vendor/jquery.hashchange.min.js");
__webpack_require__(/*! script-loader!EasyTabs/lib/jquery.easytabs.min.js */ "./node_modules/script-loader/index.js!./node_modules/EasyTabs/lib/jquery.easytabs.min.js");
__webpack_require__(/*! script-loader!garlicjs/dist/garlic.min.js */ "./node_modules/script-loader/index.js!./node_modules/garlicjs/dist/garlic.min.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/parsley.min.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/parsley.min.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/de.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/de.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/da.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/da.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/es.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/es.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/fi.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/fi.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/fi.extra.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/fi.extra.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/fr.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/fr.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/it.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/it.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/ja.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/ja.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/nl.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/nl.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/no.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/no.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/hu.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/hu.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/pl.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/pl.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/he.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/he.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/he.extra.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/he.extra.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/sl.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/sl.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/sl.extra.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/sl.extra.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/hu.extra.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/hu.extra.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/pt-br.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/pt-br.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/pt-pt.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/pt-pt.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/zh_cn.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/zh_cn.js");
__webpack_require__(/*! script-loader!parsleyjs/dist/i18n/en.js */ "./node_modules/script-loader/index.js!./node_modules/parsleyjs/dist/i18n/en.js");

/***/ }),

/***/ "./sources/scss/front/order-pay.scss":
/*!*******************************************!*\
  !*** ./sources/scss/front/order-pay.scss ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./sources/ts/checkout.ts":
/*!********************************!*\
  !*** ./sources/ts/checkout.ts ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
__webpack_require__(/*! ts-polyfill */ "./node_modules/ts-polyfill/dist/ts-polyfill.js");
var Main_1 = __webpack_require__(/*! ./front/CFW/Main */ "./sources/ts/front/CFW/Main.ts");
var TabContainer_1 = __webpack_require__(/*! ./front/CFW/Elements/TabContainer */ "./sources/ts/front/CFW/Elements/TabContainer.ts");
var TabContainerBreadcrumb_1 = __webpack_require__(/*! ./front/CFW/Elements/TabContainerBreadcrumb */ "./sources/ts/front/CFW/Elements/TabContainerBreadcrumb.ts");
var TabContainerSection_1 = __webpack_require__(/*! ./front/CFW/Elements/TabContainerSection */ "./sources/ts/front/CFW/Elements/TabContainerSection.ts");
var compatibility_classes_1 = __webpack_require__(/*! ./compatibility-classes */ "./sources/ts/compatibility-classes.ts");
window.CompatibilityClasses = compatibility_classes_1.CompatibilityClasses;
window.errorObserverIgnoreList = [];
// Fired from compatibility-classes.ts
jQuery(document).ready(function () {
    var data = cfwEventData;
    var checkoutFormEl = jQuery(data.elements.checkoutFormSelector);
    var easyTabsWrapEl = jQuery(data.elements.easyTabsWrapElClass);
    var breadCrumbEl = jQuery(data.elements.breadCrumbElId);
    var customerInfoEl = jQuery(data.elements.customerInfoElId);
    var shippingMethodEl = jQuery(data.elements.shippingMethodElId);
    var paymentMethodEl = jQuery(data.elements.paymentMethodElId);
    var alertContainerEl = jQuery(data.elements.alertContainerId);
    var tabContainerEl = jQuery(data.elements.tabContainerElId);
    // Allow users to add their own Typescript Compatibility classes
    window.dispatchEvent(new CustomEvent('cfw-add-user-compatibility-definitions'));
    var tabContainerBreadcrumb = new TabContainerBreadcrumb_1.TabContainerBreadcrumb(breadCrumbEl);
    var tabContainerSections = [
        new TabContainerSection_1.TabContainerSection(customerInfoEl, 'customer_info'),
        new TabContainerSection_1.TabContainerSection(shippingMethodEl, 'shipping_method'),
        new TabContainerSection_1.TabContainerSection(paymentMethodEl, 'payment_method')
    ];
    var tabContainer = new TabContainer_1.TabContainer(tabContainerEl, tabContainerBreadcrumb, tabContainerSections);
    var main = new Main_1.Main(checkoutFormEl, easyTabsWrapEl, alertContainerEl, tabContainer, data.ajaxInfo, data.settings, data.compatibility);
    main.setup();
});


/***/ }),

/***/ "./sources/ts/compatibility-classes.ts":
/*!*********************************************!*\
  !*** ./sources/ts/compatibility-classes.ts ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var AmazonPay_1 = __webpack_require__(/*! ./front/CFW/Compatibility/AmazonPay */ "./sources/ts/front/CFW/Compatibility/AmazonPay.ts");
var BlueCheck_1 = __webpack_require__(/*! ./front/CFW/Compatibility/BlueCheck */ "./sources/ts/front/CFW/Compatibility/BlueCheck.ts");
var Braintree_1 = __webpack_require__(/*! ./front/CFW/Compatibility/Braintree */ "./sources/ts/front/CFW/Compatibility/Braintree.ts");
var BraintreeForWooCommerce_1 = __webpack_require__(/*! ./front/CFW/Compatibility/BraintreeForWooCommerce */ "./sources/ts/front/CFW/Compatibility/BraintreeForWooCommerce.ts");
var CO2OK_1 = __webpack_require__(/*! ./front/CFW/Compatibility/CO2OK */ "./sources/ts/front/CFW/Compatibility/CO2OK.ts");
var EUVatNumber_1 = __webpack_require__(/*! ./front/CFW/Compatibility/EUVatNumber */ "./sources/ts/front/CFW/Compatibility/EUVatNumber.ts");
var InpsydePayPalPlus_1 = __webpack_require__(/*! ./front/CFW/Compatibility/InpsydePayPalPlus */ "./sources/ts/front/CFW/Compatibility/InpsydePayPalPlus.ts");
var KlarnaCheckout_1 = __webpack_require__(/*! ./front/CFW/Compatibility/KlarnaCheckout */ "./sources/ts/front/CFW/Compatibility/KlarnaCheckout.ts");
var KlarnaPayments_1 = __webpack_require__(/*! ./front/CFW/Compatibility/KlarnaPayments */ "./sources/ts/front/CFW/Compatibility/KlarnaPayments.ts");
var MondialRelay_1 = __webpack_require__(/*! ./front/CFW/Compatibility/MondialRelay */ "./sources/ts/front/CFW/Compatibility/MondialRelay.ts");
var NLPostcodeChecker_1 = __webpack_require__(/*! ./front/CFW/Compatibility/NLPostcodeChecker */ "./sources/ts/front/CFW/Compatibility/NLPostcodeChecker.ts");
var OrderDeliveryDate_1 = __webpack_require__(/*! ./front/CFW/Compatibility/OrderDeliveryDate */ "./sources/ts/front/CFW/Compatibility/OrderDeliveryDate.ts");
var PayPalCheckout_1 = __webpack_require__(/*! ./front/CFW/Compatibility/PayPalCheckout */ "./sources/ts/front/CFW/Compatibility/PayPalCheckout.ts");
var PayPalForWooCommerce_1 = __webpack_require__(/*! ./front/CFW/Compatibility/PayPalForWooCommerce */ "./sources/ts/front/CFW/Compatibility/PayPalForWooCommerce.ts");
var PostNL_1 = __webpack_require__(/*! ./front/CFW/Compatibility/PostNL */ "./sources/ts/front/CFW/Compatibility/PostNL.ts");
var SendCloud_1 = __webpack_require__(/*! ./front/CFW/Compatibility/SendCloud */ "./sources/ts/front/CFW/Compatibility/SendCloud.ts");
var ShipMondo_1 = __webpack_require__(/*! ./front/CFW/Compatibility/ShipMondo */ "./sources/ts/front/CFW/Compatibility/ShipMondo.ts");
var Square_1 = __webpack_require__(/*! ./front/CFW/Compatibility/Square */ "./sources/ts/front/CFW/Compatibility/Square.ts");
var Square1x_1 = __webpack_require__(/*! ./front/CFW/Compatibility/Square1x */ "./sources/ts/front/CFW/Compatibility/Square1x.ts");
var SquareRecurring_1 = __webpack_require__(/*! ./front/CFW/Compatibility/SquareRecurring */ "./sources/ts/front/CFW/Compatibility/SquareRecurring.ts");
var Stripe_1 = __webpack_require__(/*! ./front/CFW/Compatibility/Stripe */ "./sources/ts/front/CFW/Compatibility/Stripe.ts");
var WooCommerceAddressValidation_1 = __webpack_require__(/*! ./front/CFW/Compatibility/WooCommerceAddressValidation */ "./sources/ts/front/CFW/Compatibility/WooCommerceAddressValidation.ts");
var WooCommerceGermanized_1 = __webpack_require__(/*! ./front/CFW/Compatibility/WooCommerceGermanized */ "./sources/ts/front/CFW/Compatibility/WooCommerceGermanized.ts");
var WooFunnelsOrderBumps_1 = __webpack_require__(/*! ./front/CFW/Compatibility/WooFunnelsOrderBumps */ "./sources/ts/front/CFW/Compatibility/WooFunnelsOrderBumps.ts");
var WooSquarePro_1 = __webpack_require__(/*! ./front/CFW/Compatibility/WooSquarePro */ "./sources/ts/front/CFW/Compatibility/WooSquarePro.ts");
exports.CompatibilityClasses = {};
exports.CompatibilityClasses.AmazonPay = AmazonPay_1.AmazonPay;
exports.CompatibilityClasses.BlueCheck = BlueCheck_1.BlueCheck;
exports.CompatibilityClasses.Braintree = Braintree_1.Braintree;
exports.CompatibilityClasses.BraintreeForWooCommerce = BraintreeForWooCommerce_1.BraintreeForWooCommerce;
exports.CompatibilityClasses.CO2OK = CO2OK_1.CO2OK;
exports.CompatibilityClasses.EUVatNumber = EUVatNumber_1.EUVatNumber;
exports.CompatibilityClasses.InpsydePayPalPlus = InpsydePayPalPlus_1.InpsydePayPalPlus;
exports.CompatibilityClasses.KlarnaCheckout = KlarnaCheckout_1.KlarnaCheckout;
exports.CompatibilityClasses.KlarnaPayments = KlarnaPayments_1.KlarnaPayments;
exports.CompatibilityClasses.MondialRelay = MondialRelay_1.MondialRelay;
exports.CompatibilityClasses.NLPostcodeChecker = NLPostcodeChecker_1.NLPostcodeChecker;
exports.CompatibilityClasses.OrderDeliveryDate = OrderDeliveryDate_1.OrderDeliveryDate;
exports.CompatibilityClasses.PayPalCheckout = PayPalCheckout_1.PayPalCheckout;
exports.CompatibilityClasses.PayPalForWooCommerce = PayPalForWooCommerce_1.PayPalForWooCommerce;
exports.CompatibilityClasses.PostNL = PostNL_1.PostNL;
exports.CompatibilityClasses.SendCloud = SendCloud_1.SendCloud;
exports.CompatibilityClasses.ShipMondo = ShipMondo_1.ShipMondo;
exports.CompatibilityClasses.Square = Square_1.Square;
exports.CompatibilityClasses.Square1x = Square1x_1.Square1x;
exports.CompatibilityClasses.SquareRecurring = SquareRecurring_1.SquareRecurring;
exports.CompatibilityClasses.Stripe = Stripe_1.Stripe;
exports.CompatibilityClasses.WooCommerceAddressValidation = WooCommerceAddressValidation_1.WooCommerceAddressValidation;
exports.CompatibilityClasses.WooCommerceGermanized = WooCommerceGermanized_1.WooCommerceGermanized;
exports.CompatibilityClasses.WooFunnelsOrderBumps = WooFunnelsOrderBumps_1.WooFunnelsOrderBumps;
exports.CompatibilityClasses.WooSquarePro = WooSquarePro_1.WooSquarePro;


/***/ }),

/***/ "./sources/ts/front/CFW/Actions/AccountExistsAction.ts":
/*!*************************************************************!*\
  !*** ./sources/ts/front/CFW/Actions/AccountExistsAction.ts ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Action_1 = __webpack_require__(/*! ./Action */ "./sources/ts/front/CFW/Actions/Action.ts");
/**
 * Ajax does the account exist action. Takes the information from email box and fires of a request to see if the account
 * exists
 */
var AccountExistsAction = /** @class */ (function (_super) {
    __extends(AccountExistsAction, _super);
    /**
     * @param id
     * @param ajaxInfo
     * @param email
     * @param ezTabContainer
     */
    function AccountExistsAction(id, ajaxInfo, email) {
        var _this = this;
        // Object prep
        var data = {
            "wc-ajax": id,
            email: email
        };
        // Call parent
        _this = _super.call(this, id, data) || this;
        return _this;
    }
    /**
     *
     * @param resp
     */
    AccountExistsAction.prototype.response = function (resp) {
        if (typeof resp !== "object") {
            resp = JSON.parse(resp);
        }
        var login_slide = jQuery('#cfw-login-slide');
        var $create_account = jQuery('#createaccount');
        var register_user_checkbox = ($create_account.length > 0) ? $create_account : null;
        var register_container = jQuery('#cfw-login-details .cfw-check-input');
        // Cleanup any login required alerts
        jQuery(".cfw-login-required-error").remove();
        // If account exists slide down the password field, uncheck the register box, and hide the container for the checkbox
        if (resp.account_exists) {
            if (!login_slide.hasClass('stay-open')) {
                login_slide.slideDown(300);
            }
            if (register_user_checkbox && register_user_checkbox.is(':checkbox')) {
                register_user_checkbox.prop('checked', false);
                register_user_checkbox.trigger('change');
                register_user_checkbox.prop('disabled', true);
            }
            register_container.css('display', 'none');
            AccountExistsAction.checkBox = true;
            // If account does not exist, reverse
        }
        else {
            if (!login_slide.hasClass('stay-open')) {
                login_slide.slideUp(300);
            }
            register_container.css('display', 'block');
            if (AccountExistsAction.checkBox) {
                if (register_user_checkbox && register_user_checkbox.is(':checkbox')) {
                    if (window.cfwEventData.settings.check_create_account_by_default == true) {
                        register_user_checkbox.prop('checked', true);
                    }
                    register_user_checkbox.trigger('change');
                    register_user_checkbox.prop('disabled', false);
                }
                AccountExistsAction.checkBox = false;
            }
        }
    };
    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    AccountExistsAction.prototype.error = function (xhr, textStatus, errorThrown) {
        console.log("Account Exists Error: " + errorThrown + " (" + textStatus + ")");
    };
    Object.defineProperty(AccountExistsAction, "checkBox", {
        /**
         * @returns {boolean}
         */
        get: function () {
            return AccountExistsAction._checkBox;
        },
        /**
         * @param {boolean} value
         */
        set: function (value) {
            AccountExistsAction._checkBox = value;
        },
        enumerable: true,
        configurable: true
    });
    /**
     * @type {boolean}
     * @private
     */
    AccountExistsAction._checkBox = true;
    return AccountExistsAction;
}(Action_1.Action));
exports.AccountExistsAction = AccountExistsAction;


/***/ }),

/***/ "./sources/ts/front/CFW/Actions/Action.ts":
/*!************************************************!*\
  !*** ./sources/ts/front/CFW/Actions/Action.ts ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
/**
 * Base class for our ajax handling. Child classes will extend this and override the response function and implement their
 * own custom solutions for the php side of actions
 */
var Action = /** @class */ (function () {
    /**
     * @param id
     * @param url
     * @param data
     */
    function Action(id, data) {
        this.id = id;
        this.url = cfwEventData.checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', this.id);
        this.data = data;
    }
    /**
     * Automatically assign the items to the data
     *
     * @param {string} id
     * @param {AjaxInfo} ajaxInfo
     * @param items
     * @returns {any}
     */
    Action.prep = function (id, ajaxInfo, items) {
        var data = {
            "wc-ajax": id,
        };
        Object.assign(data, items);
        return data;
    };
    /**
     * Fire ze ajax
     */
    Action.prototype.load = function () {
        jQuery.ajax({
            type: "POST",
            url: this.url,
            data: this.data,
            success: this.response.bind(this),
            error: this.error.bind(this),
            dataType: 'json'
        });
    };
    Object.defineProperty(Action.prototype, "id", {
        /**
         * @returns {string}
         */
        get: function () {
            return this._id;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._id = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Action.prototype, "url", {
        /**
         * @returns {string}
         */
        get: function () {
            return this._url;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._url = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Action.prototype, "data", {
        /**
         * @returns {Object}
         */
        get: function () {
            return this._data;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._data = value;
        },
        enumerable: true,
        configurable: true
    });
    return Action;
}());
exports.Action = Action;


/***/ }),

/***/ "./sources/ts/front/CFW/Actions/ApplyCouponAction.ts":
/*!***********************************************************!*\
  !*** ./sources/ts/front/CFW/Actions/ApplyCouponAction.ts ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Action_1 = __webpack_require__(/*! ./Action */ "./sources/ts/front/CFW/Actions/Action.ts");
var Alert_1 = __webpack_require__(/*! ../Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
/**
 *
 */
var ApplyCouponAction = /** @class */ (function (_super) {
    __extends(ApplyCouponAction, _super);
    /**
     * @param {string} id
     * @param {AjaxInfo} ajaxInfo
     * @param {string} code
     * @param {Cart} cart
     * @param {any} fields
     */
    function ApplyCouponAction(id, ajaxInfo, code, fields) {
        var _this = this;
        var data = {
            "wc-ajax": id,
            coupon_code: code
        };
        _this = _super.call(this, id, data) || this;
        _this.fields = fields;
        return _this;
    }
    /**
     *
     * @param resp
     */
    ApplyCouponAction.prototype.response = function (resp) {
        if (typeof resp !== "object") {
            resp = JSON.parse(resp);
        }
        var tabContainer = Main_1.Main.instance.tabContainer;
        if (resp.coupons) {
            jQuery(document.body).trigger('cfw-apply-coupon-success');
        }
        else {
            jQuery(document.body).trigger('cfw-apply-coupon-failure');
        }
        jQuery(document.body).trigger('cfw-apply-coupon-complete');
        tabContainer.queueUpdateCheckout({}, { update_shipping_method: false });
    };
    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    ApplyCouponAction.prototype.error = function (xhr, textStatus, errorThrown) {
        jQuery(document.body).trigger('cfw-apply-coupon-error');
        var alertInfo = {
            type: "error",
            message: "Failed to apply coupon. Error: " + errorThrown + " (" + textStatus + ")",
            cssClass: "cfw-alert-error"
        };
        var alert = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
        alert.addAlert();
    };
    Object.defineProperty(ApplyCouponAction.prototype, "fields", {
        /**
         * @returns {any}
         */
        get: function () {
            return this._fields;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._fields = value;
        },
        enumerable: true,
        configurable: true
    });
    return ApplyCouponAction;
}(Action_1.Action));
exports.ApplyCouponAction = ApplyCouponAction;


/***/ }),

/***/ "./sources/ts/front/CFW/Actions/CompleteOrderAction.ts":
/*!*************************************************************!*\
  !*** ./sources/ts/front/CFW/Actions/CompleteOrderAction.ts ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Action_1 = __webpack_require__(/*! ./Action */ "./sources/ts/front/CFW/Actions/Action.ts");
var Alert_1 = __webpack_require__(/*! ../Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var CompleteOrderAction = /** @class */ (function (_super) {
    __extends(CompleteOrderAction, _super);
    /**
     *
     * @param id
     * @param ajaxInfo
     * @param checkoutData
     */
    function CompleteOrderAction(id, ajaxInfo, checkoutData) {
        var _this = _super.call(this, id, Action_1.Action.prep(id, ajaxInfo, checkoutData)) || this;
        Main_1.Main.addOverlay();
        _this.setup();
        return _this;
    }
    /**
     * The setup function which mainly determines if we need a stripe token to continue
     */
    CompleteOrderAction.prototype.setup = function () {
        Main_1.Main.instance.checkoutForm.off('form:validate');
        this.load();
    };
    /**
     *
     * @param resp
     */
    CompleteOrderAction.prototype.response = function (resp) {
        if (typeof resp !== "object") {
            resp = JSON.parse(resp);
        }
        if (resp.result === "success") {
            // Destroy all the cache!
            jQuery('.garlic-auto-save').each(function (index, elem) { return jQuery(elem).garlic('destroy'); });
            // Destroy all the parsley!
            Main_1.Main.instance.checkoutForm.parsley().destroy();
            // Redirect all the browsers! ( well just the 1)
            window.location.href = resp.redirect;
        }
        else if (resp.result === "failure") {
            window.dispatchEvent(new CustomEvent('cfw-checkout-failed-before-error-message', { detail: { response: resp } }));
            Alert_1.Alert.removeAlerts(Main_1.Main.instance.alertContainer);
            if (resp.messages !== "") {
                // Wrapping the response in a <div /> is required for correct parsing
                var messages = jQuery(jQuery.parseHTML("<div>" + resp.messages + "</div>"));
                jQuery.each(messages.find('li'), function (i, el) {
                    var alert = new Alert_1.Alert(Main_1.Main.instance.alertContainer, {
                        type: "error",
                        message: jQuery.trim(jQuery(el).text()),
                        cssClass: "cfw-alert-error"
                    });
                    alert.addAlert();
                });
                jQuery.each(messages.find('.woocommerce-info'), function (i, el) {
                    var alert = new Alert_1.Alert(Main_1.Main.instance.alertContainer, {
                        type: "notice",
                        message: jQuery.trim(jQuery(el).text()),
                        cssClass: "cfw-alert-info"
                    });
                    alert.addAlert();
                });
                jQuery.each(messages.find('.woocommerce-message'), function (i, el) {
                    var alert = new Alert_1.Alert(Main_1.Main.instance.alertContainer, {
                        type: "success",
                        message: jQuery.trim(jQuery(el).text()),
                        cssClass: "cfw-alert-success"
                    });
                    alert.addAlert();
                });
            }
            else {
                /**
                 * If the payment gateway comes back with no message, show a generic error.
                 */
                var alertInfo = {
                    type: "error",
                    message: 'An unknown error occurred. Response from payment gateway was empty.',
                    cssClass: "cfw-alert-error"
                };
                var alert_1 = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
                alert_1.addAlert();
            }
            CompleteOrderAction.initCompleteOrder = false;
            // Fire updated_checkout event.
            jQuery(document.body).trigger('updated_checkout');
        }
    };
    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    CompleteOrderAction.prototype.error = function (xhr, textStatus, errorThrown) {
        var message;
        if (xhr.status === 0) {
            message = 'Could not connect to server. Please refresh and try again or contact site administrator.';
        }
        else if (xhr.status === 404) {
            message = 'Requested resource could not be found. Please contact site administrator. (404)';
        }
        else if (xhr.status === 500) {
            message = 'An internal server error occurred. Please contact site administrator. (500)';
        }
        else if (textStatus === 'parsererror') {
            message = 'Server response could not be parsed. Please contact site administrator.';
        }
        else if (textStatus === 'timeout' || xhr.status === 504) {
            message = 'The server timed out while processing your request. Please refresh and try again or contact site administrator.';
        }
        else if (textStatus === 'abort') {
            message = 'Request was aborted. Please contact site administrator.';
        }
        else {
            message = "Uncaught Error: " + xhr.responseText;
        }
        console.log("CheckoutWC XHR response: " + xhr.response);
        console.log("CheckoutWC XHR responseText: " + xhr.responseText);
        console.log("CheckoutWC XHR status: " + xhr.status);
        console.log("CheckoutWC XHR errorThrown: " + errorThrown);
        var alertInfo = {
            type: "error",
            message: message,
            cssClass: "cfw-alert-error"
        };
        var alert = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
        alert.addAlert();
    };
    Object.defineProperty(CompleteOrderAction, "preppingOrder", {
        /**
         * @returns {boolean}
         */
        get: function () {
            return this._preppingOrder;
        },
        /**
         * @param {boolean} value
         */
        set: function (value) {
            this._preppingOrder = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(CompleteOrderAction, "initCompleteOrder", {
        /**
         * @return {boolean}
         */
        get: function () {
            return this._initCompleteOrder;
        },
        /**
         * @param {boolean} value
         */
        set: function (value) {
            this._initCompleteOrder = value;
        },
        enumerable: true,
        configurable: true
    });
    /**
     * @type {boolean}
     * @static
     * @private
     */
    CompleteOrderAction._preppingOrder = false;
    /**
     * @type {boolean}
     * @static
     * @private
     */
    CompleteOrderAction._initCompleteOrder = false;
    return CompleteOrderAction;
}(Action_1.Action));
exports.CompleteOrderAction = CompleteOrderAction;


/***/ }),

/***/ "./sources/ts/front/CFW/Actions/LoginAction.ts":
/*!*****************************************************!*\
  !*** ./sources/ts/front/CFW/Actions/LoginAction.ts ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Action_1 = __webpack_require__(/*! ./Action */ "./sources/ts/front/CFW/Actions/Action.ts");
var Alert_1 = __webpack_require__(/*! ../Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
/**
 *
 */
var LoginAction = /** @class */ (function (_super) {
    __extends(LoginAction, _super);
    /**
     *
     * @param id
     * @param ajaxInfo
     * @param email
     * @param password
     */
    function LoginAction(id, ajaxInfo, email, password) {
        var _this = this;
        var data = {
            "wc-ajax": id,
            email: email,
            password: password
        };
        _this = _super.call(this, id, data) || this;
        return _this;
    }
    /**
     *
     * @param resp
     */
    LoginAction.prototype.response = function (resp) {
        if (typeof resp !== "object") {
            resp = JSON.parse(resp);
        }
        if (resp.logged_in) {
            location.reload();
        }
        else {
            var alertInfo = {
                type: "error",
                message: resp.message,
                cssClass: "cfw-alert-error"
            };
            var alert_1 = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
            alert_1.addAlert();
        }
    };
    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    LoginAction.prototype.error = function (xhr, textStatus, errorThrown) {
        var alertInfo = {
            type: "error",
            message: "An error occurred during login. Error: " + errorThrown + " (" + textStatus + ")",
            cssClass: "cfw-alert-error"
        };
        var alert = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
        alert.addAlert();
    };
    return LoginAction;
}(Action_1.Action));
exports.LoginAction = LoginAction;


/***/ }),

/***/ "./sources/ts/front/CFW/Actions/UpdateCartAction.ts":
/*!**********************************************************!*\
  !*** ./sources/ts/front/CFW/Actions/UpdateCartAction.ts ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Action_1 = __webpack_require__(/*! ./Action */ "./sources/ts/front/CFW/Actions/Action.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var UpdateCheckoutAction_1 = __webpack_require__(/*! ./UpdateCheckoutAction */ "./sources/ts/front/CFW/Actions/UpdateCheckoutAction.ts");
/**
 *
 */
var UpdateCartAction = /** @class */ (function (_super) {
    __extends(UpdateCartAction, _super);
    /**
     *
     * @param id
     * @param ajaxInfo
     * @param formData
     */
    function UpdateCartAction(id, ajaxInfo, formData) {
        var _this = this;
        var cleanedFormData = Object.keys(formData).filter(function (key) { return key.startsWith('cart'); }).reduce(function (object, key) {
            object[key] = formData[key];
            return object;
        }, {});
        _this = _super.call(this, id, Action_1.Action.prep(id, ajaxInfo, cleanedFormData)) || this;
        _this.blockUI();
        return _this;
    }
    /**
     *
     * @param resp
     */
    UpdateCartAction.prototype.response = function (resp) {
        if (typeof resp !== "object") {
            resp = JSON.parse(resp);
        }
        if (false !== resp.redirect) {
            window.location = resp.redirect;
        }
        else {
            var tabContainer = Main_1.Main.instance.tabContainer;
            // Fire updated_checkout event.
            tabContainer.queueUpdateCheckout({}, { update_shipping_method: false });
        }
    };
    UpdateCartAction.prototype.blockUI = function () {
        jQuery(UpdateCheckoutAction_1.UpdateCheckoutAction.blockUISelector).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        }).addClass('blocked');
    };
    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    UpdateCartAction.prototype.error = function (xhr, textStatus, errorThrown) {
        console.log("Update Cart Error: " + errorThrown + " (" + textStatus + ")");
    };
    return UpdateCartAction;
}(Action_1.Action));
exports.UpdateCartAction = UpdateCartAction;


/***/ }),

/***/ "./sources/ts/front/CFW/Actions/UpdateCheckoutAction.ts":
/*!**************************************************************!*\
  !*** ./sources/ts/front/CFW/Actions/UpdateCheckoutAction.ts ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Action_1 = __webpack_require__(/*! ./Action */ "./sources/ts/front/CFW/Actions/Action.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var Alert_1 = __webpack_require__(/*! ../Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var UpdateCheckoutAction = /** @class */ (function (_super) {
    __extends(UpdateCheckoutAction, _super);
    /**
     * @param {string} id
     * @param {AjaxInfo} ajaxInfo
     * @param fields
     * @param args
     */
    function UpdateCheckoutAction(id, ajaxInfo, fields, args) {
        var _this = this;
        var main = Main_1.Main.instance;
        // If update shipping method is false, strip out any shipping_method keys from fields object
        if (false === args.update_shipping_method) {
            Object.keys(fields).filter(function (key) {
                return key.match(/^shipping_method/);
            }).forEach(function (key) {
                delete fields[key];
            });
        }
        // This gives us another way to force updated_checkout
        if (!main.force_updated_checkout && typeof args.force_updated_checkout !== "undefined" && true === args.force_updated_checkout) {
            main.force_updated_checkout = true;
        }
        if (main.force_updated_checkout) {
            fields['force_updated_checkout'] = main.force_updated_checkout;
        }
        _this = _super.call(this, id, Action_1.Action.prep(id, ajaxInfo, fields)) || this;
        return _this;
    }
    UpdateCheckoutAction.prototype.load = function () {
        this.blockUI();
        if (UpdateCheckoutAction.underlyingRequest !== null) {
            UpdateCheckoutAction.underlyingRequest.abort();
        }
        UpdateCheckoutAction.underlyingRequest = jQuery.post(this.url, this.data, this.response.bind(this));
    };
    UpdateCheckoutAction.prototype.blockUI = function () {
        jQuery(UpdateCheckoutAction.blockUISelector).not('.blocked').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    };
    UpdateCheckoutAction.prototype.unblockUI = function () {
        jQuery(UpdateCheckoutAction.blockUISelector).unblock().removeClass('blocked');
    };
    /**
     *
     * @param resp
     */
    UpdateCheckoutAction.prototype.response = function (resp) {
        if (typeof resp !== "object") {
            resp = JSON.parse(resp);
        }
        if (resp.redirect !== false) {
            window.location = resp.redirect;
        }
        var main = Main_1.Main.instance;
        // Payment methods
        var updated_payment_methods_container = jQuery('#cfw-billing-methods');
        /**
         * Updated payment methods will be false if md5 fingerprint hasn't changed
         */
        if (false !== resp.updated_payment_methods) {
            /**
             * Save payment details to a temporary object
             */
            var paymentDetails_1 = {};
            jQuery('.payment_box :input').each(function () {
                var ID = jQuery(this).attr('id');
                if (ID) {
                    if (jQuery.inArray(jQuery(this).attr('type'), ['checkbox', 'radio']) !== -1) {
                        paymentDetails_1[ID] = jQuery(this).prop('checked');
                    }
                    else {
                        paymentDetails_1[ID] = jQuery(this).val();
                    }
                }
            });
            updated_payment_methods_container.html("" + resp.updated_payment_methods);
            /**
             * Fill in the payment details if possible without overwriting data if set.
             */
            if (!jQuery.isEmptyObject(paymentDetails_1)) {
                jQuery('.payment_box :input').each(function () {
                    var ID = jQuery(this).attr('id');
                    if (ID) {
                        if (jQuery.inArray(jQuery(this).attr('type'), ['checkbox', 'radio']) !== -1) {
                            jQuery(this).prop('checked', paymentDetails_1[ID]).change();
                        }
                        else if (null !== jQuery(this).val() && 0 === jQuery(this).val().length) {
                            jQuery(this).val(paymentDetails_1[ID]).change();
                        }
                    }
                });
            }
            // Setup payment gateway radio buttons again
            // since we replaced the HTML
            Main_1.Main.instance.tabContainer.setUpPaymentGatewayRadioButtons();
        }
        /**
         * Update Fragments
         *
         * For our elements as well as those from other plugins
         */
        // Always update the fragments
        if (resp.fragments) {
            jQuery.each(resp.fragments, function (key, value) {
                jQuery(key).replaceWith(value);
            });
        }
        var alerts = [];
        if (resp.notices.success) {
            Object.keys(resp.notices.success).forEach(function (key) {
                alerts.push({
                    type: "success",
                    message: resp.notices.success[key],
                    cssClass: "cfw-alert-success"
                });
            });
        }
        if (resp.notices.notice) {
            Object.keys(resp.notices.notice).forEach(function (key) {
                alerts.push({
                    type: "notice",
                    message: resp.notices.notice[key],
                    cssClass: "cfw-alert-info"
                });
            });
        }
        if (resp.notices.error) {
            Object.keys(resp.notices.error).forEach(function (key) {
                alerts.push({
                    type: "error",
                    message: resp.notices.error[key],
                    cssClass: "cfw-alert-error"
                });
            });
        }
        if (!Main_1.Main.instance.preserve_alerts) {
            Alert_1.Alert.removeAlerts(Main_1.Main.instance.alertContainer);
        }
        Main_1.Main.instance.preserve_alerts = false;
        if (alerts.length > 0) {
            alerts.forEach(function (alertInfo) {
                var alert = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
                alert.addAlert();
            });
        }
        /**
         * Unblock UI
         */
        this.unblockUI();
        /**
         * A custom event that runs every time, since we are suppressing
         * updated_checkout if the payment gateways haven't updated
         */
        jQuery(document.body).trigger('cfw_updated_checkout');
        if (main.force_updated_checkout === true || false !== resp.updated_payment_methods) {
            main.force_updated_checkout = false;
            Main_1.Main.instance.tabContainer.triggerUpdatedCheckout(resp);
        }
        updated_payment_methods_container.unblock();
    };
    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    UpdateCheckoutAction.prototype.error = function (xhr, textStatus, errorThrown) {
        /**
         * Unblock UI
         */
        this.unblockUI();
        console.log("Update Checkout Error: " + errorThrown + " (" + textStatus + ")");
    };
    Object.defineProperty(UpdateCheckoutAction, "underlyingRequest", {
        /**
         * @returns {any}
         */
        get: function () {
            return this._underlyingRequest;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._underlyingRequest = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(UpdateCheckoutAction, "blockUISelector", {
        get: function () {
            return this._blockUISelector;
        },
        set: function (value) {
            this._blockUISelector = value;
        },
        enumerable: true,
        configurable: true
    });
    /**
     *
     */
    UpdateCheckoutAction._underlyingRequest = null;
    /**
     *
     */
    UpdateCheckoutAction._blockUISelector = '#cfw-billing-methods, #cfw-shipping-details-fields, #cfw-shipping-method-list, #cfw-cart-details, #cfw-place-order';
    return UpdateCheckoutAction;
}(Action_1.Action));
exports.UpdateCheckoutAction = UpdateCheckoutAction;


/***/ }),

/***/ "./sources/ts/front/CFW/Actions/UpdatePaymentMethod.ts":
/*!*************************************************************!*\
  !*** ./sources/ts/front/CFW/Actions/UpdatePaymentMethod.ts ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Action_1 = __webpack_require__(/*! ./Action */ "./sources/ts/front/CFW/Actions/Action.ts");
/**
 *
 */
var UpdatePaymentMethod = /** @class */ (function (_super) {
    __extends(UpdatePaymentMethod, _super);
    /**
     *
     * @param id
     * @param ajaxInfo
     * @param payment_method
     */
    function UpdatePaymentMethod(id, ajaxInfo, payment_method) {
        var _this = this;
        var data = {
            "wc-ajax": id,
            payment_method: payment_method
        };
        _this = _super.call(this, id, data) || this;
        return _this;
    }
    /**
     *
     * @param resp
     */
    UpdatePaymentMethod.prototype.response = function (resp) {
        if (typeof resp !== "object") {
            resp = JSON.parse(resp);
        }
    };
    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    UpdatePaymentMethod.prototype.error = function (xhr, textStatus, errorThrown) {
        console.log("Update Payment Method Error: " + errorThrown + " (" + textStatus + ")");
    };
    return UpdatePaymentMethod;
}(Action_1.Action));
exports.UpdatePaymentMethod = UpdatePaymentMethod;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/AmazonPay.ts":
/*!*********************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/AmazonPay.ts ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var Alert_1 = __webpack_require__(/*! ../Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var AmazonPay = /** @class */ (function (_super) {
    __extends(AmazonPay, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function AmazonPay(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'AmazonPay') || this;
    }
    /**
     * Run the compatibility
     *
     * @param main
     */
    AmazonPay.prototype.load = function (main) {
        var _this = this;
        var errorKey = 'cfw_amazon_redirect_error';
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        var getParams = this.getUrlParamsMap();
        jQuery(window.document).on('wc_amazon_pa_widget_ready', function () {
            jQuery('#cfw-first-for-plugins, #cfw-last-for-plugins, #cfw-email-wrap').addClass('cfw-floating-label');
        });
        if (getParams[errorKey] !== undefined) {
            var alertInfo = {
                type: "error",
                message: jQuery('.woocommerce-error').html(),
                cssClass: "cfw-alert-error"
            };
            jQuery('.woocommerce-error').remove();
            var alert_1 = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
            alert_1.addAlert();
            jQuery('#checkout').addClass('has-overlay');
            jQuery('#cfw-deductors-list').addClass('has-overlay');
            jQuery('#checkout').append('<div class="amazon-pay-overlay"></div>');
            jQuery('#cfw-deductors-list').append('<div class="amazon-pay-overlay"></div>');
            if (amazon_payments_advanced_params !== undefined &&
                amazon_payments_advanced_params.declined_code !== undefined &&
                amazon_payments_advanced_params.declined_code === 'InvalidPaymentMethod') {
                setTimeout(function () {
                    location.href = location.href = location.href.replace(errorKey + "=1", '');
                }, 3000);
            }
            return;
        }
        /**
         * If the OffAmazonPayments and amazon_payments_advanced_params exist we can then check to see if there is a reference
         * id set. If not we are not logged in. If there is we are logged in.
         */
        try {
            if (OffAmazonPayments !== undefined &&
                amazon_payments_advanced_params !== undefined &&
                (amazon_payments_advanced_params.reference_id !== "" || amazon_payments_advanced_params.access_token !== "")) {
                jQuery(window).on('load cfw_updated_checkout', function () {
                    _this.cleanUpExtraStuff();
                });
                easyTabsWrap.bind('easytabs:after', function (event, clicked, target) { return _this.amazonRefresh(); });
                window.addEventListener('cfw-checkout-failed-before-error-message', function (_a) {
                    var detail = _a.detail;
                    var response = detail.response;
                    if (response.reload) {
                        var errorParam = "&" + errorKey + "=1";
                        location.href = amazon_payments_advanced_params.redirect + errorParam;
                    }
                });
            }
        }
        catch (error) {
            console.log(error);
        }
    };
    AmazonPay.prototype.getUrlParamsMap = function () {
        var map = {};
        var urlGetParams = location.href.split('&').splice(1).map(function (paramSet) {
            var keyValue = paramSet.split('=');
            var key = keyValue[0];
            var value = keyValue[1];
            map[key] = value;
        });
        return map;
    };
    AmazonPay.prototype.cleanUpExtraStuff = function () {
        jQuery('#cfw-billing-methods .create-account').remove();
        jQuery('#payment-info-separator-wrap').hide();
        jQuery('#cfw-shipping-same-billing').hide();
        jQuery('#cfw-billing-methods > .cfw-module-title').hide();
        jQuery('#cfw-shipping-info > .cfw-module-title').hide();
        jQuery('#cfw-payment-method > .cfw-module-title').hide();
    };
    /**
     * Refreshes and loads the split amazon setup
     */
    AmazonPay.prototype.amazonRefresh = function () {
        OffAmazonPayments.Widgets.Utilities.setup();
        this.cleanUpExtraStuff();
    };
    return AmazonPay;
}(Compatibility_1.Compatibility));
exports.AmazonPay = AmazonPay;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/BlueCheck.ts":
/*!*********************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/BlueCheck.ts ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var BlueCheck = /** @class */ (function (_super) {
    __extends(BlueCheck, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function BlueCheck(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'BlueCheck') || this;
    }
    BlueCheck.prototype.load = function (main) {
        jQuery(document).on('cfw_updated_checkout', function () {
            var checkout_form = main.checkoutForm;
            var lookFor = main.settings.default_address_fields;
            if (checkout_form.find('input[name="bill_to_different_address"]:checked').val() === "same_as_shipping") {
                lookFor.forEach(function (field) {
                    var billing = jQuery("#billing_" + field);
                    var shipping = jQuery("#shipping_" + field);
                    if (billing.length > 0) {
                        billing.val(shipping.val());
                        billing.trigger('keyup');
                    }
                });
            }
        });
    };
    return BlueCheck;
}(Compatibility_1.Compatibility));
exports.BlueCheck = BlueCheck;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/Braintree.ts":
/*!*********************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/Braintree.ts ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var EasyTabService_1 = __webpack_require__(/*! ../Services/EasyTabService */ "./sources/ts/front/CFW/Services/EasyTabService.ts");
/**
 * Helper compatibility class for the Braintree plugin
 */
var Braintree = /** @class */ (function (_super) {
    __extends(Braintree, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function Braintree(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'Braintree') || this;
    }
    /**
     * Loads the Braintree compatibility class
     *
     * @param {Main} main
     * @param {any} params
     */
    Braintree.prototype.load = function (main, params) {
        var _this = this;
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        if (params.cc_gateway_available) {
            // Bind to the easytabs after
            this.easyTabsCreditCardAfterEvent(easyTabsWrap, main);
            jQuery(document.body).on('updated_checkout payment_method_selected', function () {
                _this.creditCardRefresh();
                _this.savedPaymentMethods();
            });
            jQuery(document.body).one('cfw_run_braintree_refresh', function () {
                _this.creditCardRefresh();
                _this.savedPaymentMethods();
            });
            window.addEventListener('cfw-payment-error-observer-ignore-list', function () {
                window.errorObserverIgnoreList.push('Currently unavailable. Please try a different payment method.');
            });
        }
        if (params.paypal_gateway_available) {
            jQuery(document.body).on('cfw_updated_checkout payment_method_selected', function () {
                _this.paypalRefresh();
            });
            jQuery(document.body).one('cfw_run_braintree_refresh', function () {
                _this.paypalRefresh();
            });
        }
    };
    /**
     * @param easyTabsWrap
     * @param main
     */
    Braintree.prototype.easyTabsCreditCardAfterEvent = function (easyTabsWrap, main) {
        var _this = this;
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) { return _this.creditCardPaymentRefreshOnTabSwitch(main, event, clicked, target); });
    };
    /**
     * The braintree credit card handler needs to be refreshed when switching to the payment tab from another tab otherwise the fields won't re-generate.
     *
     * @param {Main} main
     * @param {any} event
     * @param {any} clicked
     * @param {any} target
     */
    Braintree.prototype.creditCardPaymentRefreshOnTabSwitch = function (main, event, clicked, target) {
        var easyTabDirection = EasyTabService_1.EasyTabService.getTabDirection(target);
        var easyTabID = EasyTabService_1.EasyTabService.getTabId(easyTabDirection.target);
        var paymentContainerId = main.tabContainer.tabContainerSectionBy('name', 'payment_method').jel.attr('id');
        if (paymentContainerId === easyTabID) {
            jQuery(document.body).trigger('cfw_run_braintree_refresh');
        }
    };
    /**
     * Calls the refresh_braintree method on the credit card handler. Resets the state back to default
     */
    Braintree.prototype.creditCardRefresh = function () {
        if (typeof wc_braintree_credit_card_handler !== 'undefined') {
            wc_braintree_credit_card_handler.refresh_braintree();
        }
    };
    Braintree.prototype.paypalRefresh = function () {
        if (typeof wc_braintree_paypal_handler !== 'undefined') {
            wc_braintree_paypal_handler.setup_braintree();
            wc_braintree_paypal_handler.handle_saved_payment_methods();
        }
    };
    Braintree.prototype.savedPaymentMethods = function () {
        jQuery('.wc-braintree-credit-card-new-payment-method-form .form-row').css('display', 'block');
    };
    return Braintree;
}(Compatibility_1.Compatibility));
exports.Braintree = Braintree;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/BraintreeForWooCommerce.ts":
/*!***********************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/BraintreeForWooCommerce.ts ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var EasyTabService_1 = __webpack_require__(/*! ../Services/EasyTabService */ "./sources/ts/front/CFW/Services/EasyTabService.ts");
var BraintreeForWooCommerce = /** @class */ (function (_super) {
    __extends(BraintreeForWooCommerce, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function BraintreeForWooCommerce(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'BraintreeForWooCommerce') || this;
    }
    BraintreeForWooCommerce.prototype.load = function (main) {
        var _this = this;
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) { return _this.refreshBraintree(main, event, clicked, target); });
        jQuery(window).on('payment_method_selected', function () {
            main.force_updated_checkout = true;
            main.tabContainer.triggerUpdateCheckout();
        });
    };
    BraintreeForWooCommerce.prototype.refreshBraintree = function (main, event, clicked, target) {
        var easyTabDirection = EasyTabService_1.EasyTabService.getTabDirection(target);
        var easyTabID = EasyTabService_1.EasyTabService.getTabId(easyTabDirection.target);
        var paymentContainerId = main.tabContainer.tabContainerSectionBy('name', 'payment_method').jel.attr('id');
        if (paymentContainerId === easyTabID) {
            main.force_updated_checkout = true;
            main.tabContainer.triggerUpdateCheckout();
        }
    };
    return BraintreeForWooCommerce;
}(Compatibility_1.Compatibility));
exports.BraintreeForWooCommerce = BraintreeForWooCommerce;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/CO2OK.ts":
/*!*****************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/CO2OK.ts ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var CO2OK = /** @class */ (function (_super) {
    __extends(CO2OK, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function CO2OK(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'CO2OK') || this;
    }
    CO2OK.prototype.load = function (main) {
        jQuery(document.body).on('cfw_updated_checkout', function () {
            jQuery('a.co2ok_nolink').prop('href', '#cfw-payment-method');
        });
    };
    return CO2OK;
}(Compatibility_1.Compatibility));
exports.CO2OK = CO2OK;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/Compatibility.ts":
/*!*************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/Compatibility.ts ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility = /** @class */ (function () {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     * @param {string} name The name of the compatibility module
     */
    function Compatibility(main, params, load, name) {
        if (load === void 0) { load = true; }
        this.name = name;
        if (load) {
            this.load(main, params);
            console.log('CheckoutWC Compatibility Module Loaded: ' + this.name);
        }
    }
    Object.defineProperty(Compatibility.prototype, "name", {
        get: function () {
            return this._name;
        },
        set: function (value) {
            this._name = value;
        },
        enumerable: true,
        configurable: true
    });
    return Compatibility;
}());
exports.Compatibility = Compatibility;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/EUVatNumber.ts":
/*!***********************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/EUVatNumber.ts ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var EUVatNumber = /** @class */ (function (_super) {
    __extends(EUVatNumber, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function EUVatNumber(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'EUVatNumber') || this;
    }
    EUVatNumber.prototype.load = function (main) {
        // If shipping country or ship to different address value changes, we need to catch it
        jQuery('form.checkout').on('change', 'select#shipping_country, input[name="bill_to_different_address"]', function () {
            var country = jQuery('select#shipping_country').val();
            var check_countries = wc_eu_vat_params.eu_countries;
            var same_as_shipping = jQuery('input[name="bill_to_different_address"]:checked').val();
            if (country && jQuery.inArray(country, check_countries) >= 0 && same_as_shipping === "same_as_shipping") {
                // If shipping country is in EU and same as shipping address is checked, show vat number
                jQuery('#woocommerce_eu_vat_number').fadeIn();
            }
            else if (country && jQuery.inArray(country, check_countries) === -1 && same_as_shipping === "same_as_shipping") {
                // If shipping country is not in EU and same as shipping address is checked, hide vat number
                jQuery('#woocommerce_eu_vat_number').fadeOut();
            }
            else {
                // Otherwise, trigger a change on the billing country so that EU Vat Number's native JS will run
                jQuery('select#billing_country').change();
            }
        });
        // Make sure that on refresh, we trigger a change on shipping country so that the field renders in the right state
        jQuery(window).load(function () {
            jQuery('select#shipping_country').change();
        });
    };
    return EUVatNumber;
}(Compatibility_1.Compatibility));
exports.EUVatNumber = EUVatNumber;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/InpsydePayPalPlus.ts":
/*!*****************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/InpsydePayPalPlus.ts ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var InpsydePayPalPlus = /** @class */ (function (_super) {
    __extends(InpsydePayPalPlus, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function InpsydePayPalPlus(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'InpsydePayPalPlus') || this;
    }
    InpsydePayPalPlus.prototype.load = function (main) {
        var _this = this;
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) {
            if (jQuery(target).attr('id') == 'cfw-payment-method') {
                _this.refreshPayPalPlus();
            }
        });
    };
    InpsydePayPalPlus.prototype.refreshPayPalPlus = function () {
        var main = Main_1.Main.instance;
        main.force_updated_checkout = true;
        main.tabContainer.triggerUpdateCheckout();
    };
    return InpsydePayPalPlus;
}(Compatibility_1.Compatibility));
exports.InpsydePayPalPlus = InpsydePayPalPlus;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/KlarnaCheckout.ts":
/*!**************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/KlarnaCheckout.ts ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var KlarnaCheckout = /** @class */ (function (_super) {
    __extends(KlarnaCheckout, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function KlarnaCheckout(main, params, load) {
        if (load === void 0) { load = true; }
        var _this = _super.call(this, main, params, load, 'KlarnaCheckout') || this;
        _this.klarna_button_id = "#klarna-pay-button";
        _this.show_easy_tabs = false;
        return _this;
    }
    KlarnaCheckout.prototype.load = function (main, params) {
        var _this = this;
        this.show_easy_tabs = params.showEasyTabs;
        // Do not initialize easy tabs service
        main.easyTabService.isDisplayed = this.show_easy_tabs;
        if (!this.show_easy_tabs) {
            this.hideWooCouponNotification();
        }
        jQuery(document).on('ready', function () {
            var pay_btn = jQuery(_this.klarna_button_id);
            pay_btn.on('click', function (evt) {
                evt.preventDefault();
                window.location.href = '?payment_method=kco';
            });
            jQuery(document).on('click', '#payment_method_kco', function (evt) {
                window.location.href = '?payment_method=kco';
            });
        });
    };
    KlarnaCheckout.prototype.hideWooCouponNotification = function () {
        jQuery('.woocommerce-form-coupon-toggle').remove();
        jQuery('.checkout_coupon.woocommerce-form-coupon').remove();
    };
    return KlarnaCheckout;
}(Compatibility_1.Compatibility));
exports.KlarnaCheckout = KlarnaCheckout;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/KlarnaPayments.ts":
/*!**************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/KlarnaPayments.ts ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var EasyTabService_1 = __webpack_require__(/*! ../Services/EasyTabService */ "./sources/ts/front/CFW/Services/EasyTabService.ts");
var KlarnaPayments = /** @class */ (function (_super) {
    __extends(KlarnaPayments, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function KlarnaPayments(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'KlarnaPayments') || this;
    }
    KlarnaPayments.prototype.load = function (main) {
        var _this = this;
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) { return _this.refreshKlarnaPayments(main, event, clicked, target); });
        jQuery(document.body).on('cfw_updated_checkout', function () {
            var same_as_shipping = jQuery("input[name=\"bill_to_different_address\"]:checked").val();
            if (same_as_shipping === 'same_as_shipping') {
                jQuery('#billing_first_name').val(jQuery('#shipping_first_name').val());
                jQuery('#billing_last_name').val(jQuery('#shipping_last_name').val());
                jQuery('#billing_address_1').val(jQuery('#shipping_address_1').val());
                jQuery('#billing_address_2').val(jQuery('#shipping_address_2').val());
                jQuery('#billing_company').val(jQuery('#shipping_company').val());
                jQuery('#billing_country').val(jQuery('#shipping_country').val());
                jQuery('#billing_state').val(jQuery('#shipping_state').val());
                jQuery('#billing_postcode').val(jQuery('#shipping_postcode').val());
            }
        });
    };
    KlarnaPayments.prototype.refreshKlarnaPayments = function (main, event, clicked, target) {
        var easyTabDirection = EasyTabService_1.EasyTabService.getTabDirection(target);
        var easyTabID = EasyTabService_1.EasyTabService.getTabId(easyTabDirection.target);
        var paymentContainerId = main.tabContainer.tabContainerSectionBy('name', 'payment_method').jel.attr('id');
        if (paymentContainerId === easyTabID) {
            main.force_updated_checkout = true;
            main.tabContainer.triggerUpdateCheckout();
        }
    };
    return KlarnaPayments;
}(Compatibility_1.Compatibility));
exports.KlarnaPayments = KlarnaPayments;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/MondialRelay.ts":
/*!************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/MondialRelay.ts ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var Alert_1 = __webpack_require__(/*! ../Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var MondialRelay = /** @class */ (function (_super) {
    __extends(MondialRelay, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function MondialRelay(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'MondialRelay') || this;
    }
    MondialRelay.prototype.load = function (main) {
        jQuery(document.body).on('cfw_updated_checkout', function () {
            var main = Main_1.Main.instance;
            var same_as_shipping = jQuery('input[name="bill_to_different_address"]:checked').val();
            if (same_as_shipping === 'same_as_shipping') {
                jQuery('#billing_country').val(jQuery('#shipping_country').val());
                jQuery('#billing_postcode').val(jQuery('#shipping_postcode').val());
            }
            if (!main.force_updated_checkout && mrwp_prepare_shipping()) {
                jQuery("#mrwp_weight").attr("value", mrwpPluginSettings.mondialrelay_weight);
                var mrwpShippingRaw = mrwpPluginSettings.mondialrelay_ids_livraison, mrwpShipping = JSON.parse(mrwpShippingRaw), availableShippingOptions = jQuery('input[name^="shipping_method"]'), selectedShippingOption = jQuery('input[name^="shipping_method"]:checked'), selectedShipping = void 0;
                if (selectedShippingOption.length > 0)
                    selectedShipping = selectedShippingOption.val();
                else {
                    if (!(availableShippingOptions.length > 0))
                        return;
                    selectedShipping = jQuery('input[name^="shipping_method"]').val();
                }
                var currentShippingCode = mrwpShippingCode(mrwpShipping, selectedShipping);
                if (currentShippingCode == jQuery("#mrwp_shipping_code").val()) {
                    var previousAddress = jQuery("#mrwp_parcel_shop_address").val();
                    jQuery("#parcel_shop_info").html(previousAddress);
                }
                else
                    jQuery("#mrwp_shipping_code").attr("value", currentShippingCode), jQuery("#mrwp_parcel_shop_id").attr("value", ""), jQuery("#mrwp_parcel_shop_address").attr("value", "");
                "DRI" != currentShippingCode && -1 == currentShippingCode.indexOf("24") ? mrwpNeedsParcelPicker(!1) : (mrwpNeedsParcelPicker(!0), mrwpParcelPickerInit());
            }
        });
        jQuery('#cfw-shipping-action').hide();
        jQuery(document.body).on('cfw_updated_checkout', function () {
            jQuery('#cfw-shipping-action').show();
        });
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        easyTabsWrap.bind('easytabs:before', function (event, clicked, target) {
            if (jQuery(target).attr('id') == 'cfw-payment-method') {
                if (jQuery('#mrwp_parcel_shop_mandatory').val() == "Yes") {
                    if (jQuery('#mrwp_parcel_shop_id').val() == '') {
                        // Prevent removing alert on next update checkout
                        Main_1.Main.instance.preserve_alerts = true;
                        var alertInfo = {
                            type: "error",
                            message: 'Vous n\'avez pas encore choisi de Point Relais.',
                            cssClass: "cfw-alert-error"
                        };
                        var alert_1 = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
                        alert_1.addAlert(true);
                        event.stopImmediatePropagation();
                        return false;
                    }
                }
            }
        });
    };
    return MondialRelay;
}(Compatibility_1.Compatibility));
exports.MondialRelay = MondialRelay;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/NLPostcodeChecker.ts":
/*!*****************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/NLPostcodeChecker.ts ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var FormElement_1 = __webpack_require__(/*! ../Elements/FormElement */ "./sources/ts/front/CFW/Elements/FormElement.ts");
var NLPostcodeChecker = /** @class */ (function (_super) {
    __extends(NLPostcodeChecker, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function NLPostcodeChecker(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'MondialRelay') || this;
    }
    NLPostcodeChecker.prototype.load = function (main) {
        jQuery('body').on('wpo_wcnlpc_fields_updated', function () {
            // Shipping address
            var shipping_street_name = jQuery('#shipping_street_name');
            var shipping_house_number = jQuery('#shipping_house_number');
            var shipping_house_number_suffix = jQuery('#shipping_house_number_suffix');
            var shipping_city = jQuery('#shipping_city');
            var shipping_address_1 = '';
            // Fix float labels
            if (shipping_street_name.val()) {
                shipping_street_name.parent().addClass(FormElement_1.FormElement.labelClass);
            }
            if (shipping_city.val()) {
                shipping_city.parent().addClass(FormElement_1.FormElement.labelClass);
            }
            // Set address 1
            if (shipping_street_name.val() && shipping_house_number.val()) {
                shipping_address_1 = shipping_street_name.val() + ' ' + shipping_house_number.val();
            }
            if (shipping_house_number_suffix.val() && shipping_address_1) {
                shipping_address_1 = shipping_address_1 + '-' + shipping_house_number_suffix.val();
            }
            if (shipping_address_1) {
                jQuery('#shipping_address_1').val(shipping_address_1);
            }
            // Billing address
            var billing_street_name = jQuery('#billing_street_name');
            var billing_house_number = jQuery('#billing_house_number');
            var billing_house_number_suffix = jQuery('#billing_house_number_suffix');
            var billing_city = jQuery('#billing_city');
            var billing_address_1 = '';
            // Fix float labels
            if (billing_street_name.val()) {
                billing_street_name.parent().addClass(FormElement_1.FormElement.labelClass);
            }
            if (billing_city.val()) {
                billing_city.parent().addClass(FormElement_1.FormElement.labelClass);
            }
            // Set address 1
            if (billing_street_name.val() && billing_house_number.val()) {
                billing_address_1 = billing_street_name.val() + ' ' + billing_house_number.val();
            }
            if (billing_house_number_suffix.val() && billing_address_1) {
                billing_address_1 = billing_address_1 + '-' + billing_house_number_suffix.val();
            }
            if (billing_address_1) {
                jQuery('#billing_address_1').val(billing_address_1);
            }
        });
        jQuery(window).load(function () {
            // Hide empty containers from WC Postcode Checker NL moving fields around
            jQuery('.cfw-sg-container:not(:has(*))').hide();
            // Add spacing due to moving fields around
            jQuery('.cfw-column-12').filter(function () {
                return jQuery(this).next('.cfw-column-12').length;
            }).css('margin-bottom', '12.5px');
        });
    };
    return NLPostcodeChecker;
}(Compatibility_1.Compatibility));
exports.NLPostcodeChecker = NLPostcodeChecker;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/OrderDeliveryDate.ts":
/*!*****************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/OrderDeliveryDate.ts ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var OrderDeliveryDate = /** @class */ (function (_super) {
    __extends(OrderDeliveryDate, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function OrderDeliveryDate(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'OrderDeliveryDate') || this;
    }
    OrderDeliveryDate.prototype.load = function (main) {
        jQuery(document.body).one('updated_checkout', function () {
            jQuery("input[name=\"shipping_method[0]\"]:checked").trigger('change');
        });
    };
    return OrderDeliveryDate;
}(Compatibility_1.Compatibility));
exports.OrderDeliveryDate = OrderDeliveryDate;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/PayPalCheckout.ts":
/*!**************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/PayPalCheckout.ts ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var EasyTabService_1 = __webpack_require__(/*! ../Services/EasyTabService */ "./sources/ts/front/CFW/Services/EasyTabService.ts");
var PayPalCheckout = /** @class */ (function (_super) {
    __extends(PayPalCheckout, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function PayPalCheckout(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'PayPalCheckout') || this;
    }
    PayPalCheckout.prototype.load = function (main) {
        var interval = 0;
        // Bind to the easytabs after
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        this.easyTabsCreditCardAfterEvent(easyTabsWrap, main);
        jQuery(window).on('payment_method_selected cfw_updated_checkout', function () {
            var max_iterations = 200;
            var iterations = 0;
            interval = setInterval(function () {
                var main = Main_1.Main.instance;
                if (jQuery('input[name="payment_method"]:checked').is('#payment_method_ppec_paypal') && jQuery('#woo_pp_ec_button_checkout').is(':empty')) {
                    main.tabContainer.triggerUpdatedCheckout();
                }
                else if (!jQuery('input[name="payment_method"]:checked').is('#payment_method_ppec_paypal') || !jQuery('#woo_pp_ec_button_checkout').is(':empty')) {
                    // Wrong gateway selected OR the button rendered
                    clearInterval(interval);
                }
                else if (iterations >= max_iterations) {
                    // Give up
                    clearInterval(interval);
                }
                else {
                    iterations++;
                }
            }, 50);
        });
        jQuery(window).on('cfw_updated_checkout', function () {
            var isPPEC = jQuery('input[name="payment_method"]:checked').is('#payment_method_ppec_paypal');
            jQuery('#place_order').toggle(!isPPEC);
            jQuery('#woo_pp_ec_button_checkout').toggle(isPPEC);
        });
    };
    /**
     * @param easyTabsWrap
     * @param main
     */
    PayPalCheckout.prototype.easyTabsCreditCardAfterEvent = function (easyTabsWrap, main) {
        var _this = this;
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) { return _this.refreshPayPalButtons(main, event, clicked, target); });
    };
    /**
     *
     * @param {Main} main
     * @param {any} event
     * @param {any} clicked
     * @param {any} target
     */
    PayPalCheckout.prototype.refreshPayPalButtons = function (main, event, clicked, target) {
        var easyTabDirection = EasyTabService_1.EasyTabService.getTabDirection(target);
        var easyTabID = EasyTabService_1.EasyTabService.getTabId(easyTabDirection.target);
        var paymentContainerId = main.tabContainer.tabContainerSectionBy('name', 'payment_method').jel.attr('id');
        if (paymentContainerId === easyTabID) {
            var isPPEC = jQuery('input[name="payment_method"]:checked').is('#payment_method_ppec_paypal');
            jQuery('#place_order').toggle(!isPPEC);
            jQuery('#woo_pp_ec_button_checkout').toggle(isPPEC);
        }
    };
    return PayPalCheckout;
}(Compatibility_1.Compatibility));
exports.PayPalCheckout = PayPalCheckout;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/PayPalForWooCommerce.ts":
/*!********************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/PayPalForWooCommerce.ts ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var PayPalForWooCommerce = /** @class */ (function (_super) {
    __extends(PayPalForWooCommerce, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function PayPalForWooCommerce(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'PayPalForWooCommerce') || this;
    }
    PayPalForWooCommerce.prototype.load = function (main) {
        var interval = 0;
        jQuery(document).ready(function () {
            // Don't run the below if on express checkout review page
            var is_set_class = jQuery("div").is(".express-provided-address");
            if (is_set_class) {
                return;
            }
            jQuery(window).on('payment_method_selected cfw_updated_checkout', function () {
                var max_iterations = 200;
                var iterations = 0;
                interval = setInterval(function () {
                    var main = Main_1.Main.instance;
                    if (jQuery('input[name="payment_method"]:checked').is('#payment_method_paypal_express') && jQuery('.angelleye_smart_button_checkout_bottom').first().is(':empty')) {
                        main.tabContainer.triggerUpdatedCheckout();
                    }
                    else if (!jQuery('input[name="payment_method"]:checked').is('#payment_method_paypal_express') || !jQuery('.angelleye_smart_button_checkout_bottom').first().is(':empty')) {
                        clearInterval(interval);
                    }
                    else if (iterations >= max_iterations) {
                        // Give up
                        clearInterval(interval);
                    }
                    else {
                        iterations++;
                    }
                }, 50);
            });
            jQuery(window).on('cfw_updated_checkout', function () {
                var isPPEC = jQuery('input[name="payment_method"]:checked').is('#payment_method_paypal_express');
                jQuery('#place_order').toggle(!isPPEC);
                jQuery('.angelleye_smart_button_checkout_bottom').toggle(isPPEC);
            });
        });
    };
    return PayPalForWooCommerce;
}(Compatibility_1.Compatibility));
exports.PayPalForWooCommerce = PayPalForWooCommerce;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/PostNL.ts":
/*!******************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/PostNL.ts ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var FormElement_1 = __webpack_require__(/*! ../Elements/FormElement */ "./sources/ts/front/CFW/Elements/FormElement.ts");
var PostNL = /** @class */ (function (_super) {
    __extends(PostNL, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function PostNL(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'PostNL') || this;
    }
    PostNL.prototype.load = function (main) {
        jQuery(document.body).on('cfw_updated_checkout', function () {
            // Shipping address
            var shipping_street_name = jQuery('#shipping_street_name');
            var shipping_house_number = jQuery('#shipping_house_number');
            var shipping_house_number_suffix = jQuery('#shipping_house_number_suffix');
            var shipping_city = jQuery('#shipping_city');
            var shipping_address_1 = '';
            // Fix float labels
            if (shipping_street_name.val()) {
                shipping_street_name.parent().addClass(FormElement_1.FormElement.labelClass);
            }
            if (shipping_city.val()) {
                shipping_city.parent().addClass(FormElement_1.FormElement.labelClass);
            }
            // Set address 1
            if (shipping_street_name.val() && shipping_house_number.val()) {
                shipping_address_1 = shipping_street_name.val() + ' ' + shipping_house_number.val();
            }
            if (shipping_house_number_suffix.val() && shipping_address_1) {
                shipping_address_1 = shipping_address_1 + '-' + shipping_house_number_suffix.val();
            }
            if (shipping_address_1) {
                jQuery('#shipping_address_1').val(shipping_address_1);
            }
            // Billing address
            var billing_street_name = jQuery('#billing_street_name');
            var billing_house_number = jQuery('#billing_house_number');
            var billing_house_number_suffix = jQuery('#billing_house_number_suffix');
            var billing_city = jQuery('#billing_city');
            var billing_address_1 = '';
            // Fix float labels
            if (billing_street_name.val()) {
                billing_street_name.parent().addClass(FormElement_1.FormElement.labelClass);
            }
            if (billing_city.val()) {
                billing_city.parent().addClass(FormElement_1.FormElement.labelClass);
            }
            // Set address 1
            if (billing_street_name.val() && billing_house_number.val()) {
                billing_address_1 = billing_street_name.val() + ' ' + billing_house_number.val();
            }
            if (billing_house_number_suffix.val() && billing_address_1) {
                billing_address_1 = billing_address_1 + '-' + billing_house_number_suffix.val();
            }
            if (billing_address_1) {
                jQuery('#billing_address_1').val(billing_address_1);
            }
        });
        jQuery(window).load(function () {
            // Hide empty containers from WC Postcode Checker NL moving fields around
            jQuery('.cfw-sg-container:not(:has(*))').hide();
            // Add spacing due to moving fields around
            jQuery('.cfw-column-12').filter(function () {
                return jQuery(this).next('.cfw-column-12').length;
            }).css('margin-bottom', '12.5px');
        });
    };
    return PostNL;
}(Compatibility_1.Compatibility));
exports.PostNL = PostNL;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/SendCloud.ts":
/*!*********************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/SendCloud.ts ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var Alert_1 = __webpack_require__(/*! ../Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var SendCloud = /** @class */ (function (_super) {
    __extends(SendCloud, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function SendCloud(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'SendCloud') || this;
    }
    SendCloud.prototype.load = function (main, params) {
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        easyTabsWrap.bind('easytabs:before', function (event, clicked, target) {
            if (jQuery(target).attr('id') == 'cfw-payment-method') {
                var selected_shipping_method = jQuery("input[name='shipping_method[0]']:checked").val();
                var selected_service_point = jQuery('#sendcloudshipping_service_point_selected');
                if (typeof selected_shipping_method != 'undefined' && selected_shipping_method.indexOf('service_point_shipping_method') !== -1) {
                    if (selected_service_point.length == 0 || selected_service_point.val() == '') {
                        // Prevent removing alert on next update checkout
                        Main_1.Main.instance.preserve_alerts = true;
                        var alertInfo = {
                            type: "error",
                            message: params.notice,
                            cssClass: "cfw-alert-error"
                        };
                        var alert_1 = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
                        alert_1.addAlert(true);
                        event.stopImmediatePropagation();
                        return false;
                    }
                }
            }
        });
    };
    return SendCloud;
}(Compatibility_1.Compatibility));
exports.SendCloud = SendCloud;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/ShipMondo.ts":
/*!*********************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/ShipMondo.ts ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var Alert_1 = __webpack_require__(/*! ../Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var ShipMondo = /** @class */ (function (_super) {
    __extends(ShipMondo, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function ShipMondo(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'ShipMondo') || this;
    }
    ShipMondo.prototype.load = function (main, params) {
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        easyTabsWrap.bind('easytabs:before', function (event, clicked, target) {
            if (jQuery(target).attr('id') == 'cfw-payment-method') {
                var selected_shipping_method = jQuery("input[name='shipping_method[0]']:checked").val();
                if (typeof selected_shipping_method != 'undefined' && selected_shipping_method.indexOf('shipmondo_shipping_gls') !== -1) {
                    if (jQuery('[name="shop_ID"]').first().val() == '') {
                        var alertInfo = {
                            type: "error",
                            message: params.notice,
                            cssClass: "cfw-alert-error"
                        };
                        var alert_1 = new Alert_1.Alert(Main_1.Main.instance.alertContainer, alertInfo);
                        alert_1.addAlert(true);
                        event.stopImmediatePropagation();
                        return false;
                    }
                }
            }
        });
    };
    return ShipMondo;
}(Compatibility_1.Compatibility));
exports.ShipMondo = ShipMondo;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/Square.ts":
/*!******************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/Square.ts ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Square = /** @class */ (function (_super) {
    __extends(Square, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function Square(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'Square') || this;
    }
    Square.prototype.load = function (main) {
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        jQuery(window).on('payment_method_selected cfw_updated_checkout', function () {
            var same_as_shipping = jQuery('input[name="bill_to_different_address"]:checked').val();
            if (same_as_shipping === 'same_as_shipping') {
                jQuery('#billing_postcode').val(jQuery('#shipping_postcode').val());
            }
            if (typeof window.wc_square_credit_card_payment_form_handler !== 'undefined') {
                window.wc_square_credit_card_payment_form_handler.set_payment_fields();
            }
        });
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) {
            if (jQuery(target).attr('id') == 'cfw-payment-method') {
                if (typeof window.wc_square_credit_card_payment_form_handler !== 'undefined') {
                    window.wc_square_credit_card_payment_form_handler.set_payment_fields();
                }
            }
        });
    };
    return Square;
}(Compatibility_1.Compatibility));
exports.Square = Square;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/Square1x.ts":
/*!********************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/Square1x.ts ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Square1x = /** @class */ (function (_super) {
    __extends(Square1x, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function Square1x(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'Square1x') || this;
    }
    Square1x.prototype.load = function (main) {
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        jQuery(window).on('payment_method_selected cfw_updated_checkout', function () {
            jQuery.wc_square_payments.loadForm();
        });
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) {
            if (jQuery(target).attr('id') == 'cfw-payment-method') {
                jQuery.wc_square_payments.loadForm();
            }
        });
    };
    return Square1x;
}(Compatibility_1.Compatibility));
exports.Square1x = Square1x;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/SquareRecurring.ts":
/*!***************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/SquareRecurring.ts ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var SquareRecurring = /** @class */ (function (_super) {
    __extends(SquareRecurring, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function SquareRecurring(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'SquareRecurring') || this;
    }
    SquareRecurring.prototype.load = function (main) {
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        jQuery(window).on('payment_method_selected cfw_updated_checkout', function () {
            if (typeof jQuery.WooSquare_payments !== 'undefined') {
                jQuery.WooSquare_payments.loadForm();
            }
        });
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) {
            if (jQuery(target).attr('id') == 'cfw-payment-method') {
                if (typeof jQuery.WooSquare_payments !== 'undefined') {
                    jQuery.WooSquare_payments.loadForm();
                }
            }
        });
    };
    return SquareRecurring;
}(Compatibility_1.Compatibility));
exports.SquareRecurring = SquareRecurring;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/Stripe.ts":
/*!******************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/Stripe.ts ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var Stripe = /** @class */ (function (_super) {
    __extends(Stripe, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function Stripe(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'Stripe') || this;
    }
    Stripe.prototype.load = function (main) {
        jQuery(document).on('stripeError', this.onError);
    };
    Stripe.prototype.onError = function () {
        window.location.hash = 'cfw-payment-method';
    };
    return Stripe;
}(Compatibility_1.Compatibility));
exports.Stripe = Stripe;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/WooCommerceAddressValidation.ts":
/*!****************************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/WooCommerceAddressValidation.ts ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var WooCommerceAddressValidation = /** @class */ (function (_super) {
    __extends(WooCommerceAddressValidation, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function WooCommerceAddressValidation(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'WooCommerceAddressValidation') || this;
    }
    WooCommerceAddressValidation.prototype.load = function (main) {
        var _this = this;
        jQuery(document.body).on('load', function () {
            // Trigger window resize event for plugins that need it
            _this.reactivateBillingAddress();
        });
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) {
            _this.reactivateBillingAddress();
        });
        jQuery('[type="radio"][name="bill_to_different_address"]').on('change click', function () {
            _this.reactivateBillingAddress();
        });
    };
    WooCommerceAddressValidation.prototype.resizeWindow = function () {
        setTimeout(function () {
            jQuery(window).resize();
        }, 400);
    };
    WooCommerceAddressValidation.prototype.reactivateBillingAddress = function () {
        if (jQuery('[type="radio"][name="bill_to_different_address"]:checked').val() === 'different_from_shipping') {
            this.deactivate_billing();
            this.activate_billing();
            this.resizeWindow();
        }
    };
    WooCommerceAddressValidation.prototype.activate_billing = function () {
        var smartyui = jQuery('.deactivated.smarty-addr-billing_address_1');
        if (smartyui.length) {
            smartyui.push(smartyui[0].parentElement);
            smartyui.removeClass('deactivated');
            smartyui.addClass('activated');
            smartyui.show();
        }
    };
    WooCommerceAddressValidation.prototype.deactivate_billing = function () {
        var smartyui = jQuery('.smarty-addr-billing_address_1:visible');
        var autocompleteui = jQuery('.smarty-autocomplete.smarty-addr-billing_address_1');
        if (smartyui.length) {
            smartyui.addClass('deactivated');
            smartyui.parent().addClass('deactivated');
            autocompleteui.addClass('deactivated');
            smartyui.hide();
            smartyui.parent().hide();
            autocompleteui.hide();
        }
    };
    return WooCommerceAddressValidation;
}(Compatibility_1.Compatibility));
exports.WooCommerceAddressValidation = WooCommerceAddressValidation;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/WooCommerceGermanized.ts":
/*!*********************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/WooCommerceGermanized.ts ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var WooCommerceGermanized = /** @class */ (function (_super) {
    __extends(WooCommerceGermanized, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function WooCommerceGermanized(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'WooCommerceGermanized') || this;
    }
    WooCommerceGermanized.prototype.load = function (main) {
        jQuery(window).load(function () {
            jQuery(document).off('change', '.payment_methods input[name="payment_method"]');
        });
    };
    return WooCommerceGermanized;
}(Compatibility_1.Compatibility));
exports.WooCommerceGermanized = WooCommerceGermanized;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/WooFunnelsOrderBumps.ts":
/*!********************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/WooFunnelsOrderBumps.ts ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var WooFunnelsOrderBumps = /** @class */ (function (_super) {
    __extends(WooFunnelsOrderBumps, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function WooFunnelsOrderBumps(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'WooFunnelsOrderBumps') || this;
    }
    WooFunnelsOrderBumps.prototype.load = function (main) {
        jQuery(document.body).on('wfob_bump_trigger', function () {
            main.tabContainer.queueUpdateCheckout();
        });
    };
    return WooFunnelsOrderBumps;
}(Compatibility_1.Compatibility));
exports.WooFunnelsOrderBumps = WooFunnelsOrderBumps;


/***/ }),

/***/ "./sources/ts/front/CFW/Compatibility/WooSquarePro.ts":
/*!************************************************************!*\
  !*** ./sources/ts/front/CFW/Compatibility/WooSquarePro.ts ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Compatibility_1 = __webpack_require__(/*! ./Compatibility */ "./sources/ts/front/CFW/Compatibility/Compatibility.ts");
var WooSquarePro = /** @class */ (function (_super) {
    __extends(WooSquarePro, _super);
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    function WooSquarePro(main, params, load) {
        if (load === void 0) { load = true; }
        return _super.call(this, main, params, load, 'WooSquarePro') || this;
    }
    WooSquarePro.prototype.load = function (main) {
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        jQuery(window).on('payment_method_selected cfw_updated_checkout', function () {
            var same_as_shipping = jQuery('input[name="bill_to_different_address"]:checked').val();
            if (same_as_shipping === 'same_as_shipping') {
                jQuery('#billing_postcode').val(jQuery('#shipping_postcode').val());
            }
            if (typeof jQuery.WooSquare_payments !== 'undefined') {
                jQuery.WooSquare_payments.loadForm();
            }
        });
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) {
            if (jQuery(target).attr('id') == 'cfw-payment-method') {
                if (typeof jQuery.WooSquare_payments !== 'undefined') {
                    jQuery.WooSquare_payments.loadForm();
                }
            }
        });
    };
    return WooSquarePro;
}(Compatibility_1.Compatibility));
exports.WooSquarePro = WooSquarePro;


/***/ }),

/***/ "./sources/ts/front/CFW/Elements/Alert.ts":
/*!************************************************!*\
  !*** ./sources/ts/front/CFW/Elements/Alert.ts ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Element_1 = __webpack_require__(/*! ./Element */ "./sources/ts/front/CFW/Elements/Element.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var md5_1 = __webpack_require__(/*! ts-md5/dist/md5 */ "./node_modules/ts-md5/dist/md5.js");
/**
 *
 */
var Alert = /** @class */ (function (_super) {
    __extends(Alert, _super);
    /**
     *
     * @param alertContainer
     * @param alertInfo
     */
    function Alert(alertContainer, alertInfo) {
        var _this = _super.call(this, alertContainer) || this;
        _this.alertInfo = alertInfo;
        return _this;
    }
    /**
     * @param {boolean} temporary
     */
    Alert.prototype.addAlert = function (temporary) {
        if (temporary === void 0) { temporary = false; }
        // If error, trigger checkout_error event
        if (this.alertInfo.type === "error") {
            jQuery(document.body).trigger('checkout_error');
        }
        // TODO: This seems like evil coupling
        Main_1.Main.removeOverlay();
        var hash = md5_1.Md5.hashStr(this.alertInfo.message + this.alertInfo.cssClass + this.alertInfo.type);
        var alert_element = jQuery(".cfw-alert-" + hash);
        if (alert_element.length == 0) {
            alert_element = jQuery('#cfw-alert-placeholder').contents().clone();
            alert_element.find('.message').html(this.alertInfo.message);
            alert_element.addClass(this.alertInfo.cssClass);
            alert_element.addClass("cfw-alert-" + hash);
            alert_element.appendTo(this.jel);
            this.jel.slideDown(300);
            alert_element = jQuery(".cfw-alert-" + hash);
            window.dispatchEvent(new CustomEvent('cfw-add-alert-event', { detail: { alertInfo: this.alertInfo } }));
        }
        // Temporary alerts are removed on tab switch
        if (temporary) {
            alert_element.addClass('cfw-alert-temporary');
        }
        // Scroll to the top of current tab on tab switch
        jQuery('html, body').stop().animate({
            scrollTop: alert_element.offset().top
        }, 300);
    };
    /**
     * @param {any} alertContainer
     */
    Alert.removeAlerts = function (alertContainer) {
        alertContainer.find('.cfw-alert').remove();
    };
    Object.defineProperty(Alert.prototype, "alertInfo", {
        /**
         * @returns {AlertInfo}
         */
        get: function () {
            return this._alertInfo;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._alertInfo = value;
        },
        enumerable: true,
        configurable: true
    });
    return Alert;
}(Element_1.Element));
exports.Alert = Alert;


/***/ }),

/***/ "./sources/ts/front/CFW/Elements/Element.ts":
/*!**************************************************!*\
  !*** ./sources/ts/front/CFW/Elements/Element.ts ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
/**
 *
 */
var Element = /** @class */ (function () {
    /**
     * @param jel
     */
    function Element(jel) {
        this.jel = jel;
    }
    Object.defineProperty(Element.prototype, "jel", {
        /**
         * @returns {any}
         */
        get: function () {
            return this._jel;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._jel = value;
        },
        enumerable: true,
        configurable: true
    });
    return Element;
}());
exports.Element = Element;


/***/ }),

/***/ "./sources/ts/front/CFW/Elements/FormElement.ts":
/*!******************************************************!*\
  !*** ./sources/ts/front/CFW/Elements/FormElement.ts ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Element_1 = __webpack_require__(/*! ./Element */ "./sources/ts/front/CFW/Elements/Element.ts");
var LabelType_1 = __webpack_require__(/*! ../Enums/LabelType */ "./sources/ts/front/CFW/Enums/LabelType.ts");
/**
 *
 */
var FormElement = /** @class */ (function (_super) {
    __extends(FormElement, _super);
    /**
     * @param jel
     */
    function FormElement(jel) {
        var _this = _super.call(this, jel) || this;
        /**
         * @type {Array}
         * @private
         */
        _this._eventCallbacks = [];
        _this.moduleContainer = _this.jel.parents('.cfw-module');
        return _this;
    }
    /**
     * @returns {any}
     */
    FormElement.getLabelTypes = function () {
        return jQuery.map(LabelType_1.LabelType, function (value, index) {
            return [value];
        });
    };
    /**
     *
     */
    FormElement.prototype.regAndWrap = function () {
        this.registerEventCallbacks();
        this.wrapClassSwap(this.holder.jel.val());
    };
    /**
     * @param tjel
     * @param useType
     */
    FormElement.prototype.setHolderAndLabel = function (tjel, useType) {
        if (useType === void 0) { useType = false; }
        var lt = FormElement.getLabelTypes();
        // Note: Length is divided by 2 because of ENUM implementation. Read TS docs
        for (var i = 0; i < lt.length / 2; i++) {
            var jqTjel = tjel;
            if (useType && typeof tjel === 'string') {
                var type = lt[i].toLowerCase();
                jqTjel = this.jel.find(tjel.replace('%s', type));
            }
            if (jqTjel.length > 0) {
                this.holder = new Element_1.Element(jqTjel);
            }
        }
    };
    /**
     * @param value
     */
    FormElement.prototype.wrapClassSwap = function (value) {
        if (value !== '' && !this.jel.hasClass(FormElement.labelClass)) {
            this.jel.addClass(FormElement.labelClass);
        }
        if (value === '' && this.jel.hasClass(FormElement.labelClass)) {
            this.jel.removeClass(FormElement.labelClass);
        }
    };
    /**
     *
     */
    FormElement.prototype.registerEventCallbacks = function () {
        var _this = this;
        if (this.holder) {
            this.eventCallbacks.forEach(function (eventCb) {
                var eventName = eventCb.eventName;
                var cb = eventCb.func;
                var target = eventCb.target;
                if (!target) {
                    target = _this.holder.jel;
                }
                target.on(eventName, cb);
            });
        }
    };
    Object.defineProperty(FormElement, "labelClass", {
        /**
         * @returns {string}
         */
        get: function () {
            return FormElement._labelClass;
        },
        /**
         * @param value
         */
        set: function (value) {
            FormElement._labelClass = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormElement.prototype, "eventCallbacks", {
        /**
         * @returns {Array<EventCallback>}
         */
        get: function () {
            return this._eventCallbacks;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._eventCallbacks = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormElement.prototype, "moduleContainer", {
        /**
         * @returns {any}
         */
        get: function () {
            return this._moduleContainer;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._moduleContainer = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(FormElement.prototype, "holder", {
        /**
         * @returns {Element}
         */
        get: function () {
            return this._holder;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._holder = value;
        },
        enumerable: true,
        configurable: true
    });
    /**
     * @type {string}
     * @private
     */
    FormElement._labelClass = "cfw-floating-label";
    return FormElement;
}(Element_1.Element));
exports.FormElement = FormElement;


/***/ }),

/***/ "./sources/ts/front/CFW/Elements/InputLabelWrap.ts":
/*!*********************************************************!*\
  !*** ./sources/ts/front/CFW/Elements/InputLabelWrap.ts ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var FormElement_1 = __webpack_require__(/*! ./FormElement */ "./sources/ts/front/CFW/Elements/FormElement.ts");
/**
 *
 */
var InputLabelWrap = /** @class */ (function (_super) {
    __extends(InputLabelWrap, _super);
    /**
     * @param jel
     */
    function InputLabelWrap(jel) {
        var _this = _super.call(this, jel) || this;
        _this.setHolderAndLabel('input[type="%s"]', true);
        if (_this.holder) {
            _this.eventCallbacks = [
                {
                    eventName: "keyup change", func: function () {
                        this.wrapClassSwap(this.holder.jel.val());
                    }.bind(_this), target: null
                }
            ];
            _this.regAndWrap();
        }
        return _this;
    }
    return InputLabelWrap;
}(FormElement_1.FormElement));
exports.InputLabelWrap = InputLabelWrap;


/***/ }),

/***/ "./sources/ts/front/CFW/Elements/SelectLabelWrap.ts":
/*!**********************************************************!*\
  !*** ./sources/ts/front/CFW/Elements/SelectLabelWrap.ts ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var FormElement_1 = __webpack_require__(/*! ./FormElement */ "./sources/ts/front/CFW/Elements/FormElement.ts");
/**
 *
 */
var SelectLabelWrap = /** @class */ (function (_super) {
    __extends(SelectLabelWrap, _super);
    /**
     * @param jel
     */
    function SelectLabelWrap(jel) {
        var _this = _super.call(this, jel) || this;
        _this.setHolderAndLabel(_this.jel.find('select'));
        if (_this.holder) {
            _this.eventCallbacks = [
                {
                    eventName: "change", func: function () {
                        this.wrapClassSwap(this.holder.jel.val());
                    }.bind(_this), target: null
                },
                {
                    eventName: "keyup", func: function () {
                        this.wrapClassSwap(this.holder.jel.val());
                    }.bind(_this), target: null
                }
            ];
            _this.regAndWrap();
        }
        return _this;
    }
    /**
     * @param value
     */
    SelectLabelWrap.prototype.wrapClassSwap = function (value) {
        if (!this.jel.hasClass(FormElement_1.FormElement.labelClass)) {
            this.jel.addClass(FormElement_1.FormElement.labelClass);
        }
    };
    return SelectLabelWrap;
}(FormElement_1.FormElement));
exports.SelectLabelWrap = SelectLabelWrap;


/***/ }),

/***/ "./sources/ts/front/CFW/Elements/TabContainer.ts":
/*!*******************************************************!*\
  !*** ./sources/ts/front/CFW/Elements/TabContainer.ts ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Element_1 = __webpack_require__(/*! ./Element */ "./sources/ts/front/CFW/Elements/Element.ts");
var AccountExistsAction_1 = __webpack_require__(/*! ../Actions/AccountExistsAction */ "./sources/ts/front/CFW/Actions/AccountExistsAction.ts");
var LoginAction_1 = __webpack_require__(/*! ../Actions/LoginAction */ "./sources/ts/front/CFW/Actions/LoginAction.ts");
var FormElement_1 = __webpack_require__(/*! ./FormElement */ "./sources/ts/front/CFW/Elements/FormElement.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var UpdateCheckoutAction_1 = __webpack_require__(/*! ../Actions/UpdateCheckoutAction */ "./sources/ts/front/CFW/Actions/UpdateCheckoutAction.ts");
var ApplyCouponAction_1 = __webpack_require__(/*! ../Actions/ApplyCouponAction */ "./sources/ts/front/CFW/Actions/ApplyCouponAction.ts");
var CompleteOrderAction_1 = __webpack_require__(/*! ../Actions/CompleteOrderAction */ "./sources/ts/front/CFW/Actions/CompleteOrderAction.ts");
var UpdatePaymentMethod_1 = __webpack_require__(/*! ../Actions/UpdatePaymentMethod */ "./sources/ts/front/CFW/Actions/UpdatePaymentMethod.ts");
var UpdateCartAction_1 = __webpack_require__(/*! ../Actions/UpdateCartAction */ "./sources/ts/front/CFW/Actions/UpdateCartAction.ts");
/**
 *
 */
var TabContainer = /** @class */ (function (_super) {
    __extends(TabContainer, _super);
    /**
     * @param jel
     * @param tabContainerBreadcrumb
     * @param tabContainerSections
     */
    function TabContainer(jel, tabContainerBreadcrumb, tabContainerSections) {
        var _this = _super.call(this, jel) || this;
        _this.tabContainerBreadcrumb = tabContainerBreadcrumb;
        _this.tabContainerSections = tabContainerSections;
        return _this;
    }
    /**
     * Sometimes in some browsers ( looking at you safari and chrome ) the label doesn't float when the data is retrieved
     * via garlic. This will fix this issue and float the label like it should.
     */
    TabContainer.prototype.setFloatLabelOnGarlicRetrieve = function () {
        jQuery('.garlic-auto-save').each(function (index, elem) {
            jQuery(elem).garlic({ onRetrieve: function (element) { return jQuery(element).parent().addClass(FormElement_1.FormElement.labelClass); } });
        });
    };
    /**
     * All update_checkout triggers should happen here
     *
     * Exceptions would be edge cases involving TS compat classes
     */
    TabContainer.prototype.setUpdateCheckoutTriggers = function () {
        var _this = this;
        var main = Main_1.Main.instance;
        var checkout_form = main.checkoutForm;
        checkout_form.on('change', 'select.shipping_method, input[name^="shipping_method"], [name="bill_to_different_address"], .update_totals_on_change select, .update_totals_on_change input[type="radio"], .update_totals_on_change input[type="checkbox"]', this.queueUpdateCheckout.bind(this));
        checkout_form.on('change', '.address-field select', this.queueUpdateCheckout.bind(this));
        checkout_form.on('change', '.address-field input.input-text, .update_totals_on_change input.input-text', this.queueUpdateCheckout.bind(this));
        checkout_form.on('change', '#wc_checkout_add_ons :input', this.queueUpdateCheckout.bind(this));
        checkout_form.on('keydown', '.address-field input.input-text, .update_totals_on_change input.input-text', this.queueUpdateCheckout.bind(this));
        var easyTabsWrap = main.easyTabService.easyTabsWrap;
        easyTabsWrap.bind('easytabs:after', function (event, clicked, target) {
            if (jQuery(target).attr('id') == 'cfw-shipping-method') {
                _this.queueUpdateCheckout(event);
            }
        });
    };
    TabContainer.prototype.resetUpdateCheckoutTimer = function () {
        var main = Main_1.Main.instance;
        clearTimeout(main.updateCheckoutTimer);
    };
    TabContainer.prototype.queueUpdateCheckout = function (e, args) {
        var main = Main_1.Main.instance;
        var code = 0;
        if (typeof e !== 'undefined') {
            code = e.keyCode || e.which || 0;
        }
        if (code === 9) {
            return true;
        }
        this.resetUpdateCheckoutTimer();
        jQuery(document.body).trigger('cfw_queue_update_checkout');
        main.updateCheckoutTimer = setTimeout(this.maybeUpdateCheckout.bind(this), 1000, args);
    };
    /**
     * Queue up an update_checkout
     */
    TabContainer.prototype.maybeUpdateCheckout = function (args) {
        var main = Main_1.Main.instance;
        // Small timeout to prevent multiple requests when several fields update at the same time
        this.resetUpdateCheckoutTimer();
        main.updateCheckoutTimer = setTimeout(this.triggerUpdateCheckout.bind(this), 5, args);
    };
    /**
     * Call update_checkout
     *
     * This should be the ONLY place we call this ourselves
     */
    TabContainer.prototype.triggerUpdateCheckout = function (args) {
        var main = Main_1.Main.instance;
        if (main.settings.is_checkout_pay_page) {
            return;
        }
        if (!CompleteOrderAction_1.CompleteOrderAction.initCompleteOrder) {
            if (typeof args === 'undefined') {
                args = {
                    update_shipping_method: true
                };
            }
            new UpdateCheckoutAction_1.UpdateCheckoutAction('update_checkout', main.ajaxInfo, this.getFormObject(), args).load();
        }
    };
    /**
     * Call updated_checkout
     *
     * This should be the ONLY place we call this ourselves
     */
    TabContainer.prototype.triggerUpdatedCheckout = function (data) {
        if (typeof data === 'undefined') {
            // If this is running in the dark, we need
            // to shim in fragments because some plugins
            // ( like WooCommerce Smart Coupons ) expect it
            data = { fragments: {} };
        }
        jQuery(document.body).trigger('updated_checkout', [data]);
    };
    /**
     * Find the selected payment gateway and trigger a click
     *
     * Some gateways look for a click action to init themselves properly
     */
    TabContainer.prototype.initSelectedPaymentGateway = function () {
        // If there are none selected, select the first.
        if (0 === jQuery('input[name^="payment_method"][type="radio"]:checked').length) {
            jQuery('input[name^="payment_method"][type="radio"]').eq(0).prop('checked', true);
        }
        jQuery('input[name^="payment_method"][type="radio"]:checked').trigger('click');
    };
    /**
     *
     */
    TabContainer.prototype.setAccountCheckListener = function () {
        if (cfwEventData.settings.enable_checkout_login_reminder) {
            var email_input = jQuery('#billing_email');
            if (email_input) {
                // Add check to keyup event
                email_input.on('keyup change', this.debounce(this.triggerAccountExistsCheck, 250));
                // Handles page onload use case
                this.triggerAccountExistsCheck();
            }
        }
    };
    TabContainer.prototype.setDefaultLoginFormListener = function () {
        jQuery(document.body).on('click', 'a.showlogin', function () {
            jQuery('form.login, form.woocommerce-form--login').slideToggle();
            return false;
        });
    };
    TabContainer.prototype.triggerAccountExistsCheck = function () {
        var ajax_info = Main_1.Main.instance.ajaxInfo;
        var email_input = jQuery('#billing_email');
        if (email_input) {
            new AccountExistsAction_1.AccountExistsAction('account_exists', ajax_info, email_input.val()).load();
        }
    };
    TabContainer.prototype.debounce = function (func, delay) {
        var inDebounce;
        return function () {
            var context = this;
            var args = arguments;
            clearTimeout(inDebounce);
            inDebounce = setTimeout(function () { return func(context, args); }, delay);
        };
    };
    /**
     *
     */
    TabContainer.prototype.setLogInListener = function () {
        var email_input = jQuery('#billing_email');
        if (email_input) {
            var password_input_1 = jQuery('#cfw-password');
            var login_btn = jQuery('#cfw-login-btn');
            // Fire the login action on click
            login_btn.on('click', function () { return new LoginAction_1.LoginAction('login', Main_1.Main.instance.ajaxInfo, email_input.val(), password_input_1.val()).load(); });
        }
    };
    /**
     * Setup payment gateway radio buttons
     */
    TabContainer.prototype.setUpPaymentGatewayRadioButtons = function () {
        // The payment radio buttons to register the click events too
        var payment_radio_buttons = this
            .tabContainerSectionBy('name', 'payment_method')
            .getInputsFromSection("[type=\"radio\"][name=\"payment_method\"]");
        if (payment_radio_buttons.length > 0 && jQuery("[type=\"radio\"][name=\"payment_method\"]").length && jQuery("[type=\"radio\"][name=\"payment_method\"]:checked").length == 0) {
            payment_radio_buttons[0].jel.prop('checked', true);
        }
        this.setRevealOnRadioButtonGroup(payment_radio_buttons);
    };
    /**
     * Setup payment tab address radio buttons ( Billing address )
     */
    TabContainer.prototype.setUpPaymentTabAddressRadioButtons = function () {
        // TODO: Refactor this in the future. There's no reason to use custom Element and TabSection wrappers
        var bill_to_different_address_radio_buttons = jQuery("[type=\"radio\"][name=\"bill_to_different_address\"]");
        var bill_to_different_address_radio_buttons_array = [];
        bill_to_different_address_radio_buttons.each(function (index, elem) {
            bill_to_different_address_radio_buttons_array.push(new Element_1.Element(jQuery(elem)));
        });
        this.setRevealOnRadioButtonGroup(bill_to_different_address_radio_buttons_array, true, [this.toggleRequiredInputAttribute]);
    };
    /**
     * Handles the payment method revealing and registering the click events.
     */
    TabContainer.prototype.setRevealOnRadioButtonGroup = function (radio_buttons, click_event, callbacks) {
        var _this = this;
        if (click_event === void 0) { click_event = true; }
        if (callbacks === void 0) { callbacks = []; }
        // Register the slide up and down container on click
        radio_buttons
            .forEach(function (radio_button) {
            var $radio_button = radio_button.jel;
            if (click_event) {
                $radio_button.on('click', function () {
                    _this.toggleRadioButtonContainers(radio_button, radio_buttons, callbacks);
                });
            }
            if ($radio_button.is(':checked')) {
                _this.toggleRadioButtonContainers(radio_button, radio_buttons, callbacks);
            }
        });
    };
    TabContainer.prototype.toggleRadioButtonContainers = function (radio_button, radio_buttons, callbacks) {
        // Filter out the current radio button
        // Slide up the other containers
        radio_buttons
            .filter(function (filterItem) { return filterItem != radio_button; })
            .forEach(function (other) {
            other.jel.parents('.cfw-radio-reveal-title-wrap').siblings('.cfw-radio-reveal-content').find(':input').prop('disabled', true);
            other.jel.parents('.cfw-radio-reveal-title-wrap').siblings('.cfw-radio-reveal-content').slideUp(300);
        });
        // Slide down our container
        radio_button.jel.parents('.cfw-radio-reveal-title-wrap').siblings('.cfw-radio-reveal-content').find(':input').prop('disabled', false);
        radio_button.jel.parents('.cfw-radio-reveal-title-wrap').siblings('.cfw-radio-reveal-content').slideDown(300);
        // Fire any callbacks
        callbacks.forEach(function (callback) { return callback(radio_button); });
    };
    TabContainer.prototype.toggleRequiredInputAttribute = function (radio_button) {
        var selected_radio_value = radio_button.jel.val();
        var shipping_dif_than_billing = 'different_from_shipping';
        var billing_selected = selected_radio_value === shipping_dif_than_billing;
        var placeholder_attribute = 'cfw-required-placeholder';
        var required_attribute = 'required';
        var attribute_value = '';
        var input_wraps = jQuery('#cfw-billing-fields-container').find('.cfw-input-wrap');
        var toggleRequired = function (item, _a) {
            var search = _a.search, replace = _a.replace, value = _a.value;
            if (item.hasAttribute(search)) {
                item.setAttribute(replace, value);
                item.removeAttribute(search);
            }
        };
        input_wraps.each(function (index, elem) {
            var items = jQuery(elem).find('input, select');
            items.each(function (index, item) {
                var attributes_data = {
                    search: billing_selected ? placeholder_attribute : required_attribute,
                    replace: billing_selected ? required_attribute : placeholder_attribute,
                    value: attribute_value
                };
                toggleRequired(item, attributes_data);
            });
        });
    };
    /**
     *
     */
    TabContainer.prototype.setPaymentMethodUpdate = function () {
        jQuery(document.body).on('click', 'input[name^="payment_method"][type="radio"]', function () {
            if (jQuery(this).data('order_button_text')) {
                jQuery('#place_order').text(jQuery(this).data('order_button_text'));
            }
            else {
                jQuery('#place_order').text(jQuery('#place_order').data('value'));
            }
            new UpdatePaymentMethod_1.UpdatePaymentMethod('update_payment_method', Main_1.Main.instance.ajaxInfo, jQuery(this).val()).load();
            jQuery(document.body).trigger('payment_method_selected');
        });
    };
    /**
     *
     */
    TabContainer.prototype.setUpdateCartTriggers = function () {
        var _this = this;
        jQuery(document.body).on('change', 'select.cfw-cart-quantity-input', function (event) {
            if (jQuery(event.target).val() !== 'max') {
                new UpdateCartAction_1.UpdateCartAction('update_cart', Main_1.Main.instance.ajaxInfo, _this.getFormObject()).load();
            }
        });
    };
    /**
     *
     */
    TabContainer.prototype.setQuantityPromptTriggers = function () {
        var _this = this;
        jQuery(document.body).on('change', 'select.cfw-cart-quantity-input', function (event) {
            if (jQuery(event.target).val() === 'max') {
                var response = window.prompt(cfwEventData.settings.quantity_prompt_message, '10');
                // If we have input
                if (null !== response) {
                    var new_quantity = Number(response);
                    jQuery(event.target).children("option:selected").val(new_quantity);
                    new UpdateCartAction_1.UpdateCartAction('update_cart', Main_1.Main.instance.ajaxInfo, _this.getFormObject()).load();
                }
                else {
                    // If no input, set back to the original value
                    jQuery(event.target).val(jQuery(event.target).data('default'));
                }
            }
        });
        jQuery(document.body).on('click', '.quantity-edit-label a', function (event) {
            var cart_item_key = jQuery(event.target).data('item-key');
            var cart_item_quantity_input = jQuery(".cfw-cart-quantity-input-" + cart_item_key);
            var response = window.prompt(cfwEventData.settings.quantity_prompt_message, cart_item_quantity_input.val());
            // If we have input
            if (null !== response) {
                var new_quantity = Number(response);
                cart_item_quantity_input.val(new_quantity);
                new UpdateCartAction_1.UpdateCartAction('update_cart', Main_1.Main.instance.ajaxInfo, _this.getFormObject()).load();
            }
        });
    };
    /**
     *
     */
    TabContainer.prototype.setUpdateCheckoutHandler = function () {
        var _this = this;
        jQuery(document.body).on('update_checkout', function (e, args) {
            _this.queueUpdateCheckout(e, args);
        });
    };
    /**
     *
     */
    TabContainer.prototype.setUpMobileCartDetailsReveal = function () {
        var showCartDetails = new Element_1.Element(jQuery('#cfw-show-cart-details'));
        showCartDetails.jel.on('click', function (e) {
            e.preventDefault();
            jQuery('#cfw-cart-details-collapse-wrap').slideToggle(300).parent().toggleClass('active');
        });
        jQuery(window).on('resize', function () {
            if (window.innerWidth >= 770) {
                jQuery('#cfw-cart-details-collapse-wrap').css('display', 'block');
                jQuery('#cfw-cart-details').removeClass('active');
            }
        });
        if (window.innerWidth >= 770) {
            jQuery('#cfw-cart-details-collapse-wrap').css('display', 'block');
        }
        else {
            jQuery('#cfw-cart-details-collapse-wrap').css('display', 'none');
        }
    };
    /**
     * @returns {{}}
     */
    TabContainer.prototype.getFormObject = function () {
        var main = Main_1.Main.instance;
        var checkout_form = main.checkoutForm;
        var bill_to_different_address = jQuery('[name="bill_to_different_address"]:checked').val();
        var $required_inputs = checkout_form.find('.address-field.validate-required:visible');
        var has_full_address = true;
        var lookFor = main.settings.default_address_fields;
        var formData = {
            post_data: checkout_form.serialize()
        };
        if ($required_inputs.length) {
            $required_inputs.each(function () {
                if (jQuery(this).find(':input').val() === '') {
                    has_full_address = false;
                }
            });
        }
        var formArr = checkout_form.serializeArray();
        formArr.forEach(function (item) { return formData[item.name] = item.value; });
        // Handle shipped subscriptions since they are render outside of the form
        jQuery('#cfw-other-totals input[name^="shipping_method"][type="radio"]:checked, #cfw-other-totals input[name^="shipping_method"][type="hidden"]').each(function (index, el) {
            formData[jQuery(el).attr('name')] = jQuery(el).val();
        });
        formData['has_full_address'] = has_full_address;
        formData['bill_to_different_address'] = bill_to_different_address;
        if (bill_to_different_address === 'same_as_shipping') {
            lookFor.forEach(function (field) {
                if (jQuery("#billing_" + field).length > 0) {
                    formData["billing_" + field] = formData["shipping_" + field];
                    // Make sure the post_data has the same info
                    formData['post_data'] = formData['post_data'] + ("&billing_" + field + "=") + formData["shipping_" + field];
                }
            });
        }
        /**
         * Some gateways remove the checkout and shipping fields. If guest checkout is enabled we need to check for
         * these fields
         */
        if (jQuery('#cfw-first-for-plugins #billing_first_name').length > 0 && jQuery('#cfw-last-for-plugins #billing_last_name').length > 0) {
            formData['billing_first_name'] = jQuery('#cfw-first-for-plugins #billing_first_name').val();
            formData['billing_last_name'] = jQuery('#cfw-last-for-plugins #billing_last_name').val();
        }
        return formData;
    };
    /**
     *
     */
    TabContainer.prototype.setTermsAndConditions = function () {
        var termsAndConditionsLinkClass = 'woocommerce-terms-and-conditions-link';
        var termsAndConditionsContentClass = 'woocommerce-terms-and-conditions';
        var termsAndConditionsLink = new Element_1.Element(jQuery("." + termsAndConditionsLinkClass));
        var termsAndConditionsContent = new Element_1.Element(jQuery("." + termsAndConditionsContentClass));
        termsAndConditionsLink.jel.on('click', function (eventObject) {
            eventObject.preventDefault();
            termsAndConditionsContent.jel.slideToggle(300);
        });
    };
    TabContainer.prototype.setCreateAccountCheckboxListener = function () {
        if (!cfwEventData.settings.registration_generate_password) {
            var create_account_checkbox = jQuery("#createaccount");
            var account_password_slide_1 = jQuery("#cfw-account-password-slide");
            var account_password_1 = jQuery("#account_password");
            create_account_checkbox.change(function () {
                if (jQuery(this).is(':checked')) {
                    account_password_slide_1.slideDown(300);
                    account_password_1.attr('data-parsley-group', account_password_1.attr('data-parsley-group-old'))
                        .removeAttr('data-parsley-group-old');
                }
                else {
                    account_password_slide_1.slideUp(300);
                    account_password_1.attr('data-parsley-group-old', account_password_1.attr('data-parsley-group'))
                        .removeAttr('data-parsley-group');
                }
            }).trigger('change');
        }
    };
    /**
     *
     */
    TabContainer.prototype.setCompleteOrderHandlers = function () {
        var checkout_form = Main_1.Main.instance.checkoutForm;
        checkout_form.on('submit', this.completeOrderSubmitHandler.bind(this));
    };
    /**
     *
     */
    TabContainer.prototype.completeOrderSubmitHandler = function (e) {
        var main = Main_1.Main.instance;
        var checkout_form = main.checkoutForm;
        var lookFor = main.settings.default_address_fields;
        var preSwapData = this.checkoutDataAtSubmitClick = {};
        if (checkout_form.is('.processing')) {
            return false;
        }
        CompleteOrderAction_1.CompleteOrderAction.initCompleteOrder = true;
        Main_1.Main.addOverlay();
        checkout_form.find('.woocommerce-error').remove();
        jQuery(document.body).on('checkout_error', function () {
            checkout_form.removeClass('processing').unblock(); // compatibility with gateways / plugins that expect this
            Main_1.Main.removeOverlay();
            CompleteOrderAction_1.CompleteOrderAction.initCompleteOrder = false;
        });
        if (checkout_form.find('input[name="bill_to_different_address"]:checked').val() === "same_as_shipping") {
            lookFor.forEach(function (field) {
                var billing = jQuery("#billing_" + field);
                var shipping = jQuery("#shipping_" + field);
                if (billing.length > 0) {
                    preSwapData[field] = billing.val();
                    billing.val(shipping.val());
                    billing.trigger('keyup');
                }
            });
        }
        // If all the payment stuff has finished any ajax calls, run the complete order.
        if (checkout_form.triggerHandler('checkout_place_order') !== false && checkout_form.triggerHandler('checkout_place_order_' + checkout_form.find('input[name="payment_method"]:checked').val()) !== false) {
            checkout_form.addClass('processing');
            // Reset data
            for (var field in preSwapData) {
                var billing = jQuery("#billing_" + field);
                billing.val(preSwapData[field]);
            }
            this.orderKickOff(main.ajaxInfo, this.getFormObject());
        }
        else {
            checkout_form.removeClass('processing').unblock();
        }
        /**
         * Throwing an error here seems to cause situations where the error briefly appears during a successful order
         */
        return false;
    };
    /**
     *
     * @param {AjaxInfo} ajaxInfo
     * @param data
     */
    TabContainer.prototype.orderKickOff = function (ajaxInfo, data) {
        new CompleteOrderAction_1.CompleteOrderAction('complete_order', ajaxInfo, data);
    };
    /**
     *
     */
    TabContainer.prototype.setApplyCouponListener = function () {
        var _this = this;
        var promo_apply_button = jQuery('#cfw-promo-code-btn');
        jQuery('#cfw-promo-code').on('keypress', function (e) {
            if (e.which == 13) {
                e.preventDefault();
                promo_apply_button.trigger('click');
            }
        });
        promo_apply_button.on('click', function () {
            var coupon_field = jQuery('#cfw-promo-code');
            if (coupon_field.val() !== '') {
                new ApplyCouponAction_1.ApplyCouponAction('cfw_apply_coupon', Main_1.Main.instance.ajaxInfo, coupon_field.val(), _this.getFormObject()).load();
            }
        });
    };
    /**
     * @param by
     * @param value
     * @returns {TabContainerSection}
     */
    TabContainer.prototype.tabContainerSectionBy = function (by, value) {
        return this.tabContainerSections.find(function (tabContainerSection) { return tabContainerSection[by] == value; });
    };
    Object.defineProperty(TabContainer.prototype, "tabContainerBreadcrumb", {
        /**
         * @returns {TabContainerBreadcrumb}
         */
        get: function () {
            return this._tabContainerBreadcrumb;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._tabContainerBreadcrumb = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(TabContainer.prototype, "tabContainerSections", {
        /**
         * @returns {Array<TabContainerSection>}
         */
        get: function () {
            return this._tabContainerSections;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._tabContainerSections = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(TabContainer.prototype, "checkoutDataAtSubmitClick", {
        /**
         * @returns {any}
         */
        get: function () {
            return this._checkoutDataAtSubmitClick;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._checkoutDataAtSubmitClick = value;
        },
        enumerable: true,
        configurable: true
    });
    return TabContainer;
}(Element_1.Element));
exports.TabContainer = TabContainer;


/***/ }),

/***/ "./sources/ts/front/CFW/Elements/TabContainerBreadcrumb.ts":
/*!*****************************************************************!*\
  !*** ./sources/ts/front/CFW/Elements/TabContainerBreadcrumb.ts ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Element_1 = __webpack_require__(/*! ./Element */ "./sources/ts/front/CFW/Elements/Element.ts");
/**
 *
 */
var TabContainerBreadcrumb = /** @class */ (function (_super) {
    __extends(TabContainerBreadcrumb, _super);
    /**
     *
     * @param jel
     */
    function TabContainerBreadcrumb(jel) {
        return _super.call(this, jel) || this;
    }
    /**
     * Hides the breadcrumb
     */
    TabContainerBreadcrumb.prototype.hide = function () {
        this.jel.hide();
    };
    /**
     * Shows the breadcrumb
     */
    TabContainerBreadcrumb.prototype.show = function () {
        this.jel.show();
    };
    return TabContainerBreadcrumb;
}(Element_1.Element));
exports.TabContainerBreadcrumb = TabContainerBreadcrumb;


/***/ }),

/***/ "./sources/ts/front/CFW/Elements/TabContainerSection.ts":
/*!**************************************************************!*\
  !*** ./sources/ts/front/CFW/Elements/TabContainerSection.ts ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var Element_1 = __webpack_require__(/*! ./Element */ "./sources/ts/front/CFW/Elements/Element.ts");
var InputLabelWrap_1 = __webpack_require__(/*! ./InputLabelWrap */ "./sources/ts/front/CFW/Elements/InputLabelWrap.ts");
var LabelType_1 = __webpack_require__(/*! ../Enums/LabelType */ "./sources/ts/front/CFW/Enums/LabelType.ts");
var SelectLabelWrap_1 = __webpack_require__(/*! ./SelectLabelWrap */ "./sources/ts/front/CFW/Elements/SelectLabelWrap.ts");
var TextareaLabelWrap_1 = __webpack_require__(/*! ./TextareaLabelWrap */ "./sources/ts/front/CFW/Elements/TextareaLabelWrap.ts");
/**
 *
 */
var TabContainerSection = /** @class */ (function (_super) {
    __extends(TabContainerSection, _super);
    /**
     *
     * @param jel
     * @param name
     */
    function TabContainerSection(jel, name) {
        var _this = _super.call(this, jel) || this;
        /**
         *
         * @type {string}
         * @private
         */
        _this._name = "";
        /**
         *
         * @type {Array}
         * @private
         */
        _this._inputLabelWraps = [];
        /**
         *
         * @type {Array}
         * @private
         */
        _this._selectLabelWraps = [];
        _this.name = name;
        return _this;
    }
    /**
     *
     * @returns {string}
     */
    TabContainerSection.prototype.getWrapSelector = function () {
        var selector = "";
        TabContainerSection.inputLabelTypes.forEach(function (labelType, index) {
            selector += "." + TabContainerSection.inputLabelWrapClass + "." + labelType.cssClass;
            if (index + 1 != TabContainerSection.inputLabelTypes.length) {
                selector += ", ";
            }
        });
        return selector;
    };
    /**
     * Gets all the inputs for a tab section
     *
     * @param query
     * @returns {Array<Element>}
     */
    TabContainerSection.prototype.getInputsFromSection = function (query) {
        if (query === void 0) { query = ""; }
        var out = [];
        this.jel.find("input" + query).each(function (index, elem) {
            out.push(new Element_1.Element(jQuery(elem)));
        });
        return out;
    };
    /**
     *
     */
    TabContainerSection.prototype.setWraps = function () {
        var jLabelWrap = this.jel.find(this.getWrapSelector());
        jLabelWrap.each(function (index, wrap) {
            if (jQuery(wrap).hasClass('cfw-select-input') && jQuery(wrap).find('select').length > 0) {
                new SelectLabelWrap_1.SelectLabelWrap(jQuery(wrap));
            }
            else if (jQuery(wrap).hasClass('cfw-textarea-input') && jQuery(wrap).find('textarea').length > 0) {
                new TextareaLabelWrap_1.TextareaLabelWrap(jQuery(wrap));
            }
            else {
                new InputLabelWrap_1.InputLabelWrap(jQuery(wrap));
            }
        });
    };
    Object.defineProperty(TabContainerSection.prototype, "name", {
        /**
         *
         * @returns {string}
         */
        get: function () {
            return this._name;
        },
        /**
         *
         * @param value
         */
        set: function (value) {
            this._name = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(TabContainerSection.prototype, "selectLabelWraps", {
        /**
         *
         * @returns {Array<SelectLabelWrap>}
         */
        get: function () {
            return this._selectLabelWraps;
        },
        /**
         *
         * @param value
         */
        set: function (value) {
            this._selectLabelWraps = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(TabContainerSection, "inputLabelTypes", {
        /**
         *
         * @returns {Array<InputLabelType>}
         */
        get: function () {
            return TabContainerSection._inputLabelTypes;
        },
        /**
         *
         * @param value
         */
        set: function (value) {
            TabContainerSection._inputLabelTypes = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(TabContainerSection, "inputLabelWrapClass", {
        /**
         *
         * @returns {string}
         */
        get: function () {
            return TabContainerSection._inputLabelWrapClass;
        },
        /**
         *
         * @param value
         */
        set: function (value) {
            TabContainerSection._inputLabelWrapClass = value;
        },
        enumerable: true,
        configurable: true
    });
    /**
     *
     * @type {string}
     * @private
     */
    TabContainerSection._inputLabelWrapClass = "cfw-input-wrap";
    /**
     *
     * @type {[{type: LabelType; cssClass: string},{type: LabelType; cssClass: string},{type: LabelType; cssClass: string}]}
     * @private
     */
    TabContainerSection._inputLabelTypes = [
        { type: LabelType_1.LabelType.TEXT, cssClass: 'cfw-text-input' },
        { type: LabelType_1.LabelType.TEL, cssClass: 'cfw-tel-input' },
        { type: LabelType_1.LabelType.PASSWORD, cssClass: 'cfw-password-input' },
        { type: LabelType_1.LabelType.SELECT, cssClass: 'cfw-select-input' },
        { type: LabelType_1.LabelType.TEXTAREA, cssClass: 'cfw-textarea-input' },
        { type: LabelType_1.LabelType.NUMBER, cssClass: 'cfw-number-input' },
    ];
    return TabContainerSection;
}(Element_1.Element));
exports.TabContainerSection = TabContainerSection;


/***/ }),

/***/ "./sources/ts/front/CFW/Elements/TextareaLabelWrap.ts":
/*!************************************************************!*\
  !*** ./sources/ts/front/CFW/Elements/TextareaLabelWrap.ts ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
Object.defineProperty(exports, "__esModule", { value: true });
var FormElement_1 = __webpack_require__(/*! ./FormElement */ "./sources/ts/front/CFW/Elements/FormElement.ts");
/**
 *
 */
var TextareaLabelWrap = /** @class */ (function (_super) {
    __extends(TextareaLabelWrap, _super);
    /**
     * @param jel
     */
    function TextareaLabelWrap(jel) {
        var _this = _super.call(this, jel) || this;
        _this.setHolderAndLabel(_this.jel.find('textarea'));
        if (_this.holder) {
            _this.eventCallbacks = [
                {
                    eventName: "keyup", func: function () {
                        this.wrapClassSwap(this.holder.jel.val());
                    }.bind(_this), target: null
                }
            ];
            _this.regAndWrap();
        }
        return _this;
    }
    return TextareaLabelWrap;
}(FormElement_1.FormElement));
exports.TextareaLabelWrap = TextareaLabelWrap;


/***/ }),

/***/ "./sources/ts/front/CFW/Enums/LabelType.ts":
/*!*************************************************!*\
  !*** ./sources/ts/front/CFW/Enums/LabelType.ts ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
/**
 *
 */
var LabelType;
(function (LabelType) {
    LabelType[LabelType["TEXT"] = 0] = "TEXT";
    LabelType[LabelType["TEL"] = 1] = "TEL";
    LabelType[LabelType["EMAIL"] = 2] = "EMAIL";
    LabelType[LabelType["PASSWORD"] = 3] = "PASSWORD";
    LabelType[LabelType["SELECT"] = 4] = "SELECT";
    LabelType[LabelType["TEXTAREA"] = 5] = "TEXTAREA";
    LabelType[LabelType["NUMBER"] = 6] = "NUMBER";
})(LabelType = exports.LabelType || (exports.LabelType = {}));


/***/ }),

/***/ "./sources/ts/front/CFW/Main.ts":
/*!**************************************!*\
  !*** ./sources/ts/front/CFW/Main.ts ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var ValidationService_1 = __webpack_require__(/*! ./Services/ValidationService */ "./sources/ts/front/CFW/Services/ValidationService.ts");
var EasyTabService_1 = __webpack_require__(/*! ./Services/EasyTabService */ "./sources/ts/front/CFW/Services/EasyTabService.ts");
var ParsleyService_1 = __webpack_require__(/*! ./Services/ParsleyService */ "./sources/ts/front/CFW/Services/ParsleyService.ts");
var Alert_1 = __webpack_require__(/*! ./Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var CompleteOrderAction_1 = __webpack_require__(/*! ./Actions/CompleteOrderAction */ "./sources/ts/front/CFW/Actions/CompleteOrderAction.ts");
var ZipAutocompleteService_1 = __webpack_require__(/*! ./Services/ZipAutocompleteService */ "./sources/ts/front/CFW/Services/ZipAutocompleteService.ts");
var AddressAutocompleteService_1 = __webpack_require__(/*! ./Services/AddressAutocompleteService */ "./sources/ts/front/CFW/Services/AddressAutocompleteService.ts");
/**
 * The main class of the front end checkout system
 */
var Main = /** @class */ (function () {
    /**
     * @param {any} checkoutFormEl
     * @param {any} easyTabsWrap
     * @param {any} alertContainer
     * @param {TabContainer} tabContainer
     * @param {AjaxInfo} ajaxInfo
     * @param {Cart} cart
     * @param {any} settings
     * @param {any} compatibility
     */
    function Main(checkoutFormEl, easyTabsWrap, alertContainer, tabContainer, ajaxInfo, settings, compatibility) {
        Main.instance = this;
        checkoutFormEl.garlic({
            events: ['textInput', 'input', 'change', 'click', 'keypress', 'paste', 'focus'],
            destroy: false,
            excluded: 'input[type="file"], input[type="hidden"], input[type="submit"], input[type="reset"], input[name="payment_method"], input[name="paypal_pro-card-number"], input[name="paypal_pro-card-cvc"], input[name="wc-authorize-net-aim-account-number"], input[name="wc-authorize-net-aim-csc"], input[name="paypal_pro_payflow-card-number"], input[name="paypal_pro_payflow-card-cvc"], input[name="paytrace-card-number"], input[name="paytrace-card-cvc"], input[id="stripe-card-number"], input[id="stripe-card-cvc"], input[name="creditCard"], input[name="cvv"], input.wc-credit-card-form-card-number, input[name="wc-authorize-net-cim-credit-card-account-number"], input[name="wc-authorize-net-cim-credit-card-csc"], input.wc-credit-card-form-card-cvc, input.js-sv-wc-payment-gateway-credit-card-form-account-number, input.js-sv-wc-payment-gateway-credit-card-form-csc, input.shipping_method, input[name^="tocheckoutcw"], #_sumo_pp_enable_order_payment_plan, .cfw-cart-quantity-input, .gift-certificate-show-form input'
        });
        if (easyTabsWrap.length === 0) {
            easyTabsWrap = tabContainer.jel;
        }
        this.checkoutForm = checkoutFormEl;
        this.tabContainer = tabContainer;
        this.alertContainer = alertContainer;
        this.ajaxInfo = ajaxInfo;
        this.settings = settings;
        this.parsleyService = new ParsleyService_1.ParsleyService();
        this.easyTabService = new EasyTabService_1.EasyTabService(easyTabsWrap);
        this.zipAutocompleteService = new ZipAutocompleteService_1.ZipAutocompleteService();
        this.addressAutocompleteService = new AddressAutocompleteService_1.AddressAutocompleteService();
        this.easyTabService.isDisplayed = this.settings.load_tabs;
        // Setup events and event listeners
        this.eventSetup();
    }
    /**
     * Handles event setup and registration of listeners
     */
    Main.prototype.eventSetup = function () {
        this.compatibilityEvents();
        this.observerEvents();
    };
    ;
    /**
     * Event setup relating to the registration and creation of compatibility classes
     */
    Main.prototype.compatibilityEvents = function () {
        // Compatibility Class Creation
        Object.keys(cfwEventData.compatibility).forEach(function (key) {
            new window.CompatibilityClasses[cfwEventData.compatibility[key].class](Main.instance, cfwEventData.compatibility[key].params);
        });
    };
    /**
     * Event setup relating to observers
     */
    Main.prototype.observerEvents = function () {
        window.addEventListener('cfw-main-after-setup', function (_a) {
            var detail = _a.detail;
            // Error observer messages to ignore
            window.dispatchEvent(new CustomEvent('cfw-payment-error-observer-ignore-list'));
            // Setup the errorObserver
            detail.main.errorObserverWatch();
        });
    };
    /**
     * Sets up the tab container by running easy tabs, setting up animation listeners, and setting up events and on load
     * functionality
     */
    Main.prototype.setup = function () {
        var _this = this;
        // Before setup event
        window.dispatchEvent(new CustomEvent('cfw-main-before-setup', { detail: { main: this } }));
        // Initialize the easy tabs
        this.easyTabService.initialize(this.tabContainer.tabContainerBreadcrumb);
        // Setup the validation service - has to happen after tabs are setup
        this.validationService = new ValidationService_1.ValidationService(this.easyTabService.easyTabsWrap);
        // Setup animation listeners
        this.setupAnimationListeners();
        // Fix floating labels
        this.tabContainer.setFloatLabelOnGarlicRetrieve();
        // Before set wraps event in case anyone needs to do some JIT class adding
        window.dispatchEvent(new CustomEvent('cfw-main-before-tab-container-set-wraps', { detail: { main: this } }));
        /**
         * NOTE: If you are doing any DOM manipulation ( adding and removing classes specifically ). Do it before the setWraps
         * call on the tab container sections. Once this is called all the setup of the different areas will have completed and
         * wont be run again until next page load
         */
        // Loop through and set up the wraps on the tab container sections
        this.tabContainer.tabContainerSections.forEach(function (tcs) { return tcs.setWraps(); });
        // After the set wraps has done but before we set up any tabContainer listeners
        window.dispatchEvent(new CustomEvent('cfw-main-after-tab-container-set-wraps', { detail: { main: this } }));
        // Set up event handlers
        this.tabContainer.setUpdateCheckoutHandler();
        this.tabContainer.setAccountCheckListener();
        this.tabContainer.setDefaultLoginFormListener();
        this.tabContainer.setLogInListener();
        this.tabContainer.setPaymentMethodUpdate();
        this.tabContainer.setUpdateCartTriggers();
        this.tabContainer.setQuantityPromptTriggers();
        this.tabContainer.setUpMobileCartDetailsReveal();
        this.tabContainer.setCompleteOrderHandlers();
        this.tabContainer.setApplyCouponListener();
        this.tabContainer.setTermsAndConditions();
        this.tabContainer.setCreateAccountCheckboxListener();
        this.zipAutocompleteService.setZipAutocompleteHandlers();
        // Page load actions
        jQuery(window).on('load', function () {
            _this.tabContainer.setUpPaymentGatewayRadioButtons();
            _this.tabContainer.setUpPaymentTabAddressRadioButtons();
            _this.tabContainer.initSelectedPaymentGateway();
            jQuery('#ship-to-different-address-checkbox').trigger('change');
            jQuery(document.body).one('updated_checkout', function () {
                /**
                 * Re-init Payment Gateways
                 */
                _this.tabContainer.initSelectedPaymentGateway();
            });
            /**
             * On first load, we force updated_checkout to run for gateways
             * that need it / want it / gotta have it
             */
            _this.force_updated_checkout = true;
            _this.preserve_alerts = true;
            _this.tabContainer.triggerUpdateCheckout();
            /**
             * Now setup our triggers so that we don't double fire on page load
             */
            _this.tabContainer.setUpdateCheckoutTriggers();
            // Init checkout ( WooCommerce native event )
            jQuery(document.body).trigger('init_checkout');
        });
        // After setup event
        window.dispatchEvent(new CustomEvent('cfw-main-after-setup', { detail: { main: this } }));
    };
    Main.prototype.errorObserverWatch = function () {
        var _this = this;
        // Select the node that will be observed for mutations
        var targetNode = jQuery('form').get(0);
        // Options for the observer ( which mutations to observe )
        var config = { childList: true, characterData: true, subtree: true };
        if (!this.errorObserver) {
            // Create an observer instance linked to the callback function
            var observer = new MutationObserver(function (mutationsList) { return _this.errorMutationListener(mutationsList); });
            // Start observing the target node for configured mutations
            observer.observe(targetNode, config);
            this.errorObserver = observer;
        }
    };
    /**
     * @param mutationsList
     */
    Main.prototype.errorMutationListener = function (mutationsList) {
        var ignoreList = window.errorObserverIgnoreList;
        if (jQuery('#cfw-payment-method:visible').length > 0) {
            var _loop_1 = function (mutation) {
                if (mutation.type === 'childList') {
                    var addedNodes = mutation.addedNodes;
                    var $errorNode_1 = null;
                    Array.from(addedNodes).forEach(function (node) {
                        var $node = jQuery(node);
                        var hasClass = $node.hasClass('woocommerce-error');
                        var hasGroupCheckoutClass = $node.hasClass('woocommerce-NoticeGroup-checkout');
                        if (hasClass || hasGroupCheckoutClass) {
                            if (ignoreList.indexOf($node.text()) == -1) {
                                Main.removeOverlay();
                                $errorNode_1 = $node;
                                $errorNode_1.attr('class', '');
                            }
                        }
                    });
                    if ($errorNode_1) {
                        var alertInfo = {
                            type: "error",
                            message: $errorNode_1,
                            cssClass: "cfw-alert-error"
                        };
                        var alert_1 = new Alert_1.Alert(Main.instance.alertContainer, alertInfo);
                        alert_1.addAlert();
                        CompleteOrderAction_1.CompleteOrderAction.initCompleteOrder = false;
                    }
                }
            };
            for (var _i = 0, mutationsList_1 = mutationsList; _i < mutationsList_1.length; _i++) {
                var mutation = mutationsList_1[_i];
                _loop_1(mutation);
            }
        }
    };
    /**
     * Adds a visual indicator that the checkout is doing something
     */
    Main.addOverlay = function () {
        if (jQuery('#cfw-payment-method:visible').length > 0) {
            jQuery('body').addClass('show-overlay');
        }
    };
    /**
     * Remove the visual indicator
     */
    Main.removeOverlay = function () {
        jQuery('body').removeClass('show-overlay');
    };
    /**
     * Sets up animation listeners
     */
    Main.prototype.setupAnimationListeners = function () {
        jQuery('#cfw-ci-login').on('click', function () {
            jQuery('#cfw-login-slide').addClass('stay-open').slideDown(300);
            jQuery('#createaccount').prop('checked', false);
        });
    };
    Object.defineProperty(Main.prototype, "checkoutForm", {
        /**
         * @returns {any}
         */
        get: function () {
            return this._checkoutForm;
        },
        /**
         * @param {any} value
         */
        set: function (value) {
            this._checkoutForm = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "tabContainer", {
        /**
         * @returns {TabContainer}
         */
        get: function () {
            return this._tabContainer;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._tabContainer = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "alertContainer", {
        /**
         * @return {any}
         */
        get: function () {
            return this._alertContainer;
        },
        /**
         * @param {any} value
         */
        set: function (value) {
            this._alertContainer = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "ajaxInfo", {
        /**
         * @returns {AjaxInfo}
         */
        get: function () {
            return this._ajaxInfo;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._ajaxInfo = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "settings", {
        /**
         * @returns {any}
         */
        get: function () {
            return this._settings;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._settings = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "parsleyService", {
        /**
         * @returns {ParsleyService}
         */
        get: function () {
            return this._parsleyService;
        },
        /**
         * @param {ParsleyService} value
         */
        set: function (value) {
            this._parsleyService = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "easyTabService", {
        /**
         * @returns {EasyTabService}
         */
        get: function () {
            return this._easyTabService;
        },
        /**
         * @param {EasyTabService} value
         */
        set: function (value) {
            this._easyTabService = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "validationService", {
        /**
         * @returns {ValidationService}
         */
        get: function () {
            return this._validationService;
        },
        /**
         * @param {ValidationService} value
         */
        set: function (value) {
            this._validationService = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "zipAutocompleteService", {
        /**
         * @returns {ZipAutocompleteService}
         */
        get: function () {
            return this._zipAutocompleteService;
        },
        /**
         * @param {ZipAutocompleteService} value
         */
        set: function (value) {
            this._zipAutocompleteService = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "addressAutocompleteService", {
        get: function () {
            return this._addressAutocompleteService;
        },
        set: function (value) {
            this._addressAutocompleteService = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "errorObserver", {
        /**
         * @returns {MutationObserver}
         */
        get: function () {
            return this._errorObserver;
        },
        /**
         * @param {MutationObserver} value
         */
        set: function (value) {
            this._errorObserver = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "force_updated_checkout", {
        /**
         * @returns {boolean}
         */
        get: function () {
            return this._force_updated_checkout;
        },
        /**
         * @param {boolean} value
         */
        set: function (value) {
            this._force_updated_checkout = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "preserve_alerts", {
        /**
         * @returns {boolean}
         */
        get: function () {
            return this._preserve_alerts;
        },
        /**
         * @param {boolean} value
         */
        set: function (value) {
            this._preserve_alerts = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main.prototype, "updateCheckoutTimer", {
        get: function () {
            return this._updateCheckoutTimer;
        },
        set: function (value) {
            this._updateCheckoutTimer = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(Main, "instance", {
        /**
         * @returns {Main}
         */
        get: function () {
            return Main._instance;
        },
        /**
         * @param {Main} value
         */
        set: function (value) {
            if (!Main._instance) {
                Main._instance = value;
            }
        },
        enumerable: true,
        configurable: true
    });
    return Main;
}());
exports.Main = Main;


/***/ }),

/***/ "./sources/ts/front/CFW/Services/AddressAutocompleteService.ts":
/*!*********************************************************************!*\
  !*** ./sources/ts/front/CFW/Services/AddressAutocompleteService.ts ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var AddressAutocompleteService = /** @class */ (function () {
    /**
     * Attach change events to postcode fields
     */
    function AddressAutocompleteService() {
        var _this = this;
        this._address_formats = {
            "DE": "street_name house_number",
            "AL": "street_name house_number",
            "AO": "street_name house_number",
            "AR": "street_name house_number",
            "AT": "street_name house_number",
            "BY": "street_name house_number",
            "BE": "street_name house_number",
            "BO": "street_name house_number",
            "BA": "street_name house_number",
            "BW": "street_name house_number",
            "BR": "street_name, house_number",
            "BN": "house_number, street_name",
            "BG": "street_name house_number",
            "BI": "street_name house_number",
            "CM": "street_name house_number",
            "BQ": "street_name house_number",
            "CF": "street_name house_number",
            "TD": "street_name house_number",
            "CL": "street_name house_number",
            "CO": "street_name house_number",
            "KM": "street_name house_number",
            "HR": "street_name house_number",
            "CW": "street_name house_number",
            "CZ": "street_name house_number",
            "DK": "street_name house_number",
            "DO": "street_name house_number",
            "EC": "street_name house_number",
            "SV": "street_name house_number",
            "GQ": "street_name house_number",
            "ER": "street_name house_number",
            "EE": "street_name house_number",
            "ET": "street_name house_number",
            "FO": "street_name house_number",
            "FI": "street_name house_number",
            "GR": "street_name house_number",
            "GL": "street_name house_number",
            "GD": "street_name house_number",
            "GT": "street_name house_number",
            "GN": "street_name house_number",
            "GW": "street_name house_number",
            "HT": "street_name house_number",
            "VA": "street_name house_number",
            "HN": "street_name house_number",
            "HU": "street_name house_number",
            "IS": "street_name house_number",
            "IR": "street_name house_number",
            "IT": "street_name house_number",
            "JO": "street_name house_number",
            "KZ": "street_name house_number",
            "KI": "street_name house_number",
            "KW": "street_name house_number",
            "KG": "street_name house_number",
            "LV": "street_name house_number",
            "LR": "street_name house_number",
            "LY": "street_name house_number",
            "LI": "street_name house_number",
            "LT": "street_name house_number",
            "MO": "street_name house_number",
            "MK": "street_name house_number",
            "MY": "street_name house_number",
            "ML": "street_name house_number",
            "MX": "street_name house_number",
            "MD": "street_name house_number",
            "ME": "street_name house_number",
            "MZ": "street_name, house_number",
            "NL": "street_name house_number",
            "NO": "street_name house_number",
            "PK": "house_number - street_name",
            "PA": "street_name house_number",
            "PY": "street_name house_number",
            "PE": "street_name house_number",
            "PL": "street_name house_number",
            "PT": "street_name house_number",
            "QA": "street_name house_number",
            "RO": "street_name house_number",
            "RU": "street_name house_number",
            "LC": "street_name house_number",
            "WS": "street_name house_number",
            "SM": "street_name house_number",
            "ST": "street_name house_number",
            "RS": "street_name house_number",
            "SX": "street_name house_number",
            "SK": "street_name house_number",
            "SI": "street_name house_number",
            "SB": "street_name house_number",
            "SO": "street_name house_number",
            "SS": "street_name house_number",
            "ES": "street_name, house_number",
            "SD": "street_name house_number",
            "SR": "street_name house_number",
            "SJ": "street_name house_number",
            "SE": "street_name house_number",
            "CH": "street_name house_number",
            "SY": "street_name house_number",
            "TJ": "street_name house_number",
            "TZ": "street_name house_number",
            "TR": "street_name house_number",
            "UA": "street_name house_number",
            "UY": "street_name house_number",
            "VU": "street_name house_number",
            "EH": "street_name house_number"
        };
        if (window.cfwEventData.settings.enable_address_autocomplete !== true || typeof google === 'undefined') {
            return;
        }
        if (window.cfwEventData.settings.needs_shipping_address == true) {
            var shipping_address_1 = jQuery('#shipping_address_1');
            shipping_address_1.prop('autocomplete', 'new-password');
            var shipping_autocomplete_1 = new google.maps.places.Autocomplete(shipping_address_1.get(0), { types: ['geocode'] });
            shipping_autocomplete_1.setFields(['address_component']);
            if (false !== cfwEventData.settings.address_autocomplete_shipping_countries) {
                shipping_autocomplete_1.setComponentRestrictions({ 'country': cfwEventData.settings.address_autocomplete_shipping_countries });
            }
            shipping_autocomplete_1.addListener('place_changed', function () { _this.fillAddress('shipping_', shipping_autocomplete_1); });
        }
        var billing_address_1 = jQuery('#billing_address_1');
        billing_address_1.prop('autocomplete', 'new-password');
        var billing_autocomplete = new google.maps.places.Autocomplete(billing_address_1.get(0), { types: ['geocode'] });
        billing_autocomplete.setFields(['address_component']);
        if (false !== cfwEventData.settings.address_autocomplete_billing_countries) {
            billing_autocomplete.setComponentRestrictions({ 'country': cfwEventData.settings.address_autocomplete_billing_countries });
        }
        billing_autocomplete.addListener('place_changed', function () { _this.fillAddress('billing_', billing_autocomplete); });
    }
    AddressAutocompleteService.prototype.fillAddress = function (prefix, autocomplete_object) {
        if (!autocomplete_object.getPlace().hasOwnProperty('address_components')) {
            return;
        }
        var parts = autocomplete_object.getPlace().address_components.reduce(function (parts, component) {
            parts[component.types[0]] = component.short_name || '';
            return parts;
        }, {});
        // Standard format
        var address_1 = [parts.street_number, parts.route].filter(Boolean).join(' ');
        // If we have a special format, use it here
        if (this._address_formats.hasOwnProperty(parts.country)) {
            address_1 = this._address_formats[parts.country].replace('street_name', parts.route).replace('house_number', parts.street_number);
        }
        var city = parts.locality || parts.postal_town || parts.sublocality_level_1 || parts.administrative_area_level_2 || parts.administrative_area_level_3;
        // Cleanup anything undefined
        address_1 = address_1.replace('undefined', '');
        city = city.replace('undefined', '');
        jQuery('#' + prefix + 'address_1').val(address_1).trigger('change').trigger('keyup');
        jQuery('#' + prefix + 'country').val(parts.country).trigger('change').trigger('keyup');
        jQuery('#' + prefix + 'postcode').val(parts.postal_code).trigger('change').trigger('keyup');
        jQuery('#' + prefix + 'state').val(parts.administrative_area_level_1).trigger('change').trigger('keyup');
        jQuery('#' + prefix + 'city').val(city).trigger('change').trigger('keyup');
    };
    return AddressAutocompleteService;
}());
exports.AddressAutocompleteService = AddressAutocompleteService;


/***/ }),

/***/ "./sources/ts/front/CFW/Services/EasyTabService.ts":
/*!*********************************************************!*\
  !*** ./sources/ts/front/CFW/Services/EasyTabService.ts ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
/**
 * EzTab Enum
 */
var EasyTab;
(function (EasyTab) {
    EasyTab[EasyTab["CUSTOMER"] = 0] = "CUSTOMER";
    EasyTab[EasyTab["SHIPPING"] = 1] = "SHIPPING";
    EasyTab[EasyTab["PAYMENT"] = 2] = "PAYMENT";
})(EasyTab = exports.EasyTab || (exports.EasyTab = {}));
/**
 *
 */
var EasyTabService = /** @class */ (function () {
    /**
     * @param easyTabsWrap
     */
    function EasyTabService(easyTabsWrap) {
        /**
         * @type {boolean}
         * @private
         */
        this._isDisplayed = true;
        this.easyTabsWrap = easyTabsWrap;
    }
    /**
     * Returns the current and target tab indexes
     *
     * @param target
     * @returns {EasyTabDirection}
     */
    EasyTabService.getTabDirection = function (target) {
        var currentTabIndex = 0;
        var targetTabIndex = 0;
        Main_1.Main.instance.tabContainer.tabContainerSections.forEach(function (tab, index) {
            var $tab = tab.jel;
            if ($tab.filter(':visible').length !== 0) {
                currentTabIndex = index;
            }
            if ($tab.is(jQuery(target))) {
                targetTabIndex = index;
            }
        });
        return { current: currentTabIndex, target: targetTabIndex };
    };
    /**
     *
     */
    EasyTabService.prototype.initialize = function (breadcrumb) {
        var _this = this;
        if (this.isDisplayed) {
            this.easyTabsWrap.easytabs({
                defaultTab: 'li.tab#default-tab',
                tabs: 'ul > li.tab'
            });
            this.easyTabsWrap.removeClass('cfw-tabs-not-initialized');
            breadcrumb.show();
            this.easyTabsWrap.bind('easytabs:after', function (event, clicked, target) {
                // Scroll to the top of current tab on tab switch
                jQuery('html, body').animate({
                    scrollTop: jQuery('#cfw-tab-container').offset().top
                }, 300);
                // Add a class to checkout form to indicate payment tab is active tab
                // Doesn't work when tab is initialized by hash in URL
                var easyTabDirection = EasyTabService.getTabDirection(target);
                var current_tab_id = EasyTabService.getTabId(easyTabDirection.target);
                _this.setActiveTabClass(current_tab_id + '-active');
                // Remove temporary alerts on successful tab switch
                Main_1.Main.instance.alertContainer.find('.cfw-alert-temporary').remove();
            });
            // Add payment tab active class on window load
            jQuery(window).on('load cfw_updated_checkout', function () {
                var hash = window.location.hash;
                if (hash) {
                    _this.setActiveTabClass(hash.replace('#', '') + '-active');
                }
                else {
                    _this.setActiveTabClass('cfw-customer-info-active');
                }
            });
            jQuery(document.body).on('click', '.cfw-tab-link, .cfw-next-tab, .cfw-prev-tab', function (event) {
                if (jQuery(event.target).data('tab')) {
                    _this.easyTabsWrap.easytabs('select', jQuery(event.target).data('tab'));
                }
            });
        }
        else {
            breadcrumb.hide();
        }
    };
    /**
     *
     * @param active_class any
     */
    EasyTabService.prototype.setActiveTabClass = function (active_class) {
        var main = Main_1.Main.instance;
        var checkout_form = main.checkoutForm;
        checkout_form.removeClass("cfw-customer-info-active").removeClass("cfw-shipping-method-active").removeClass("cfw-payment-method-active").addClass(active_class);
    };
    /**
     * @param {EasyTab} tab
     */
    EasyTabService.go = function (tab) {
        Main_1.Main.instance.easyTabService.easyTabsWrap.easytabs('select', EasyTabService.getTabId(tab));
    };
    /**
     * Returns the id of the tab passed in
     *
     * @param {EasyTab} tab
     * @returns {string}
     */
    EasyTabService.getTabId = function (tab) {
        var tabContainer = Main_1.Main.instance.tabContainer;
        var easyTabs = tabContainer.tabContainerSections;
        return easyTabs[tab].jel.attr('id');
    };
    Object.defineProperty(EasyTabService.prototype, "easyTabsWrap", {
        /**
         * @return {any}
         */
        get: function () {
            return this._easyTabsWrap;
        },
        /**
         * @param {any} value
         */
        set: function (value) {
            this._easyTabsWrap = value;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(EasyTabService.prototype, "isDisplayed", {
        /**
         * @return {boolean}
         */
        get: function () {
            return this._isDisplayed;
        },
        /**
         * @param {any} value
         */
        set: function (value) {
            this._isDisplayed = value;
        },
        enumerable: true,
        configurable: true
    });
    return EasyTabService;
}());
exports.EasyTabService = EasyTabService;


/***/ }),

/***/ "./sources/ts/front/CFW/Services/ParsleyService.ts":
/*!*********************************************************!*\
  !*** ./sources/ts/front/CFW/Services/ParsleyService.ts ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var CompleteOrderAction_1 = __webpack_require__(/*! ../Actions/CompleteOrderAction */ "./sources/ts/front/CFW/Actions/CompleteOrderAction.ts");
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var ParsleyService = /** @class */ (function () {
    /**
     *
     */
    function ParsleyService() {
        this.setParsleyValidators();
    }
    /**
     *
     */
    ParsleyService.prototype.setParsleyValidators = function () {
        var _this = this;
        var self = this;
        jQuery(window).on('load', function () {
            _this.parsley = window.Parsley;
            _this.parsley.on('form:error', function () {
                // TODO: Evil coupling!
                Main_1.Main.removeOverlay();
                CompleteOrderAction_1.CompleteOrderAction.initCompleteOrder = false;
            });
            try {
                // Parsley locale
                window.Parsley.setLocale(cfwEventData.settings.locale);
            }
            catch (_a) {
                console.log('CheckoutWC: Could not load Parsley translation domain (' + cfwEventData.settings.locale + ')');
            }
            Main_1.Main.instance.checkoutForm.parsley();
        });
        var locale_json = wc_address_i18n_params.locale.replace(/&quot;/g, '"');
        var locale = jQuery.parseJSON(locale_json);
        // TODO: This isn't related to validation, so we should really move this somewhere else
        // We are lucky enough that this seems to consistently run before the select2 handler in country-select.js
        // Theory: Delegated events may run before bound events?
        jQuery(document.body).on('country_to_state_changed', function () {
            jQuery('.state_select').removeClass('state_select');
        });
        // Setup proper validation whenever the state field changes ( or potentially does )
        jQuery(document.body).bind('country_to_state_changing', function (event, country, wrapper) {
            // Find the actual field wrapper
            var city_wrapper = wrapper.find('#billing_city, #shipping_city').parent('.cfw-input-wrap');
            var postcode_wrapper = wrapper.find('#billing_postcode, #shipping_postcode').parent('.cfw-input-wrap');
            var thislocale;
            if (typeof locale[country] !== 'undefined') {
                thislocale = locale[country];
            }
            else {
                thislocale = locale['default'];
            }
            wrapper = wrapper.find('#billing_state, #shipping_state').parent('.cfw-input-wrap');
            wrapper.find('#billing_state, #shipping_state').each(function () {
                var fieldLocale = jQuery.extend(true, {}, locale['default']['state'], thislocale['state']);
                var group = jQuery(this).attr('id').split('_')[0];
                if (jQuery(this).is('select')) {
                    // Setup data again
                    jQuery(this).attr('field_key', 'state')
                        .addClass('garlic-auto-save')
                        .addClass('state-select')
                        .garlic();
                    // Disable first option
                    jQuery(this).find('option:first').attr('disabled', 'disabled');
                    wrapper.addClass('cfw-select-input')
                        .removeClass('cfw-hidden-input')
                        .removeClass('cfw-text-input')
                        .addClass('cfw-floating-label');
                }
                else if (jQuery(this).attr('type') === 'text') {
                    jQuery(this).attr('field_key', 'state')
                        .addClass('garlic-auto-save')
                        .addClass('input-text')
                        .garlic();
                    wrapper.addClass('cfw-text-input')
                        .removeClass('cfw-hidden-input')
                        .removeClass('cfw-select-input')
                        .addClass('cfw-floating-label');
                }
                else {
                    jQuery(this).addClass('hidden');
                    wrapper.addClass('cfw-hidden-input')
                        .removeClass('cfw-text-input')
                        .removeClass('cfw-select-input')
                        .removeClass('cfw-floating-label');
                }
                // Handle required toggle
                if (fieldLocale.required) {
                    jQuery(this).attr('data-parsley-validate-if-empty', '')
                        .attr('data-parsley-trigger', 'keyup change focusout')
                        .attr('data-parsley-group', group)
                        .attr('data-parsley-required', 'true');
                }
                else {
                    jQuery(this).removeAttr('data-parsley-validate-if-empty')
                        .removeAttr('data-parsley-trigger')
                        .removeAttr('data-parsley-group')
                        .removeAttr('data-parsley-required')
                        .parsley().validate(); // removes irrelevant errors if they are there
                }
            });
            setTimeout(function () {
                city_wrapper.find('#billing_city, #shipping_city').each(function () {
                    if (!jQuery(this).is(':visible')) {
                        jQuery(this).attr('data-parsley-group-old', jQuery(this).attr('data-parsley-group'))
                            .attr('data-parsley-required-old', jQuery(this).attr('data-parsley-required'))
                            .removeAttr('data-parsley-group')
                            .removeAttr('data-parsley-required');
                    }
                    else if (jQuery(this).is(':visible') && this.hasAttribute('data-parsley-group-old') && this.hasAttribute('data-parsley-required-old')) {
                        jQuery(this).attr('data-parsley-group', jQuery(this).attr('data-parsley-group-old'))
                            .attr('data-parsley-required', jQuery(this).attr('data-parsley-required-old'))
                            .removeAttr('data-parsley-group-old')
                            .removeAttr('data-parsley-required-old');
                    }
                });
                postcode_wrapper.find('#billing_postcode, #shipping_postcode').each(function () {
                    if (!jQuery(this).is(':visible')) {
                        jQuery(this).attr('data-parsley-group-old', jQuery(this).attr('data-parsley-group'))
                            .removeAttr('data-parsley-group');
                    }
                    else if (jQuery(this).is(':visible') && this.hasAttribute('data-parsley-group-old')) {
                        jQuery(this).attr('data-parsley-group', jQuery(this).attr('data-parsley-group-old'))
                            .removeAttr('data-parsley-group-old');
                    }
                });
                self.reinitParsley(wrapper);
            }, 100);
            self.reinitParsley(wrapper);
        });
    };
    ParsleyService.prototype.reinitParsley = function (wrapper) {
        // Remove existing parsley errors.
        wrapper.find('.parsley-errors-list').remove();
        // Re-register all the elements
        Main_1.Main.instance.checkoutForm.parsley();
        Main_1.Main.instance.checkoutForm.parsley().isValid();
    };
    Object.defineProperty(ParsleyService.prototype, "parsley", {
        /**
         * @returns {any}
         */
        get: function () {
            return this._parsley;
        },
        /**
         * @param value
         */
        set: function (value) {
            this._parsley = value;
        },
        enumerable: true,
        configurable: true
    });
    return ParsleyService;
}());
exports.ParsleyService = ParsleyService;


/***/ }),

/***/ "./sources/ts/front/CFW/Services/ValidationService.ts":
/*!************************************************************!*\
  !*** ./sources/ts/front/CFW/Services/ValidationService.ts ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var Main_1 = __webpack_require__(/*! ../Main */ "./sources/ts/front/CFW/Main.ts");
var EasyTabService_1 = __webpack_require__(/*! ./EasyTabService */ "./sources/ts/front/CFW/Services/EasyTabService.ts");
var EasyTabService_2 = __webpack_require__(/*! ./EasyTabService */ "./sources/ts/front/CFW/Services/EasyTabService.ts");
var Alert_1 = __webpack_require__(/*! ../Elements/Alert */ "./sources/ts/front/CFW/Elements/Alert.ts");
var AccountExistsAction_1 = __webpack_require__(/*! ../Actions/AccountExistsAction */ "./sources/ts/front/CFW/Actions/AccountExistsAction.ts");
/**
 * Validation Sections Enum
 */
var EValidationSections;
(function (EValidationSections) {
    EValidationSections[EValidationSections["SHIPPING"] = 0] = "SHIPPING";
    EValidationSections[EValidationSections["BILLING"] = 1] = "BILLING";
    EValidationSections[EValidationSections["ACCOUNT"] = 2] = "ACCOUNT";
})(EValidationSections = exports.EValidationSections || (exports.EValidationSections = {}));
/**
 *
 */
var ValidationService = /** @class */ (function () {
    /**
     * @param easyTabsWrap
     */
    function ValidationService(easyTabsWrap) {
        this.validateSectionsBeforeSwitch(easyTabsWrap);
        this.validateBillingFieldsBeforeSubmit();
        ValidationService.validateShippingOnLoadIfNotCustomerTab();
    }
    /**
     * Execute validation checks before each easy tab easy tab switch.
     *
     * @param {any} easyTabsWrap
     */
    ValidationService.prototype.validateSectionsBeforeSwitch = function (easyTabsWrap) {
        easyTabsWrap.bind('easytabs:before', function (event, clicked, target) {
            // Where are we going?
            var easyTabDirection = EasyTabService_1.EasyTabService.getTabDirection(target);
            // If we are moving forward in the checkout process and we are currently on the customer tab
            if (easyTabDirection.current === EasyTabService_2.EasyTab.CUSTOMER && easyTabDirection.target > easyTabDirection.current) {
                var validated = ValidationService.validateSectionsForCustomerTab();
                var login_required_error = false;
                var tabId = EasyTabService_1.EasyTabService.getTabId(easyTabDirection.current);
                if (!cfwEventData.settings.user_logged_in && cfwEventData.settings.is_registration_required && AccountExistsAction_1.AccountExistsAction.checkBox) {
                    login_required_error = true;
                    validated = false;
                }
                // If a login required error happened, add it here so it happens after the hash jump above
                if (login_required_error) {
                    var alert_1 = new Alert_1.Alert(Main_1.Main.instance.alertContainer, {
                        type: "error",
                        message: cfwEventData.settings.account_already_registered_notice,
                        cssClass: "cfw-alert-error cfw-login-required-error"
                    });
                    alert_1.addAlert();
                }
                if (!validated) {
                    event.stopImmediatePropagation();
                }
                // Return the validation
                return validated;
            }
            // If we are moving forward / backwards, have a shipping easy tab, and are not on the customer tab then allow
            // the tab switch
            return true;
        }.bind(this));
    };
    ValidationService.prototype.validateBillingFieldsBeforeSubmit = function () {
        var checkoutForm = Main_1.Main.instance.checkoutForm;
        checkoutForm.on('submit', function (e) {
            var validated = false;
            if (cfwEventData.settings.needs_shipping_address == 1 && checkoutForm.find('input[name="bill_to_different_address"]:checked').val() !== "same_as_shipping") {
                validated = ValidationService.validate(EValidationSections.BILLING);
            }
            else {
                validated = true; // If digital only order, billing address was handled on customer info tab so set to true
            }
            if (!validated) {
                e.preventDefault();
                e.stopImmediatePropagation(); // prevent bubbling up the DOM *and* prevent other submit handlers from firing, such as completeOrder
            }
            return validated;
        });
    };
    /**
     *
     * @returns {boolean}
     */
    ValidationService.validateSectionsForCustomerTab = function () {
        var validated = false;
        var account_validated = ValidationService.validate(EValidationSections.ACCOUNT);
        if (cfwEventData.settings.needs_shipping_address == 0) {
            var billing_address_validated = ValidationService.validate(EValidationSections.BILLING);
            validated = account_validated && billing_address_validated;
        }
        else {
            var shipping_address_validated = ValidationService.validate(EValidationSections.SHIPPING);
            validated = account_validated && shipping_address_validated;
        }
        return validated;
    };
    /**
     * @param {EValidationSections} section
     * @returns {any}
     */
    ValidationService.validate = function (section) {
        var validated;
        var checkoutForm = Main_1.Main.instance.checkoutForm;
        ValidationService.currentlyValidating = section;
        switch (section) {
            case EValidationSections.SHIPPING:
                validated = checkoutForm.parsley().validate({ group: 'shipping' });
                break;
            case EValidationSections.BILLING:
                validated = checkoutForm.parsley().validate({ group: 'billing' });
                break;
            case EValidationSections.ACCOUNT:
                validated = checkoutForm.parsley().validate({ group: 'account' });
                break;
        }
        if (validated == null) {
            validated = true;
        }
        return validated;
    };
    /**
     * Handles non ajax cases
     */
    ValidationService.validateShippingOnLoadIfNotCustomerTab = function () {
        var hash = window.location.hash;
        var customerInfoId = "#cfw-customer-info";
        var sectionToValidate = (cfwEventData.settings.needs_shipping_address == 1) ? EValidationSections.SHIPPING : EValidationSections.BILLING;
        if (hash != customerInfoId && hash != "") {
            if (!ValidationService.validate(sectionToValidate)) {
                EasyTabService_1.EasyTabService.go(EasyTabService_2.EasyTab.CUSTOMER);
            }
        }
    };
    Object.defineProperty(ValidationService, "currentlyValidating", {
        /**
         * @return {EValidationSections}
         */
        get: function () {
            return this._currentlyValidating;
        },
        /**
         * @param {EValidationSections} value
         */
        set: function (value) {
            this._currentlyValidating = value;
        },
        enumerable: true,
        configurable: true
    });
    return ValidationService;
}());
exports.ValidationService = ValidationService;


/***/ }),

/***/ "./sources/ts/front/CFW/Services/ZipAutocompleteService.ts":
/*!*****************************************************************!*\
  !*** ./sources/ts/front/CFW/Services/ZipAutocompleteService.ts ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var ZipAutocompleteService = /** @class */ (function () {
    function ZipAutocompleteService() {
    }
    /**
     * Attach change events to postcode fields
     */
    ZipAutocompleteService.prototype.setZipAutocompleteHandlers = function () {
        if (window.cfwEventData.settings.enable_zip_autocomplete === true) {
            jQuery(document.body).on('textInput input change keypress paste', '#shipping_postcode, #billing_postcode', this.autoCompleteCityState);
        }
    };
    ZipAutocompleteService.prototype.autoCompleteCityState = function (e) {
        var type = e.currentTarget.id.split('_')[0]; //either shipping or billing
        var zip = e.currentTarget.value.trim();
        var country = jQuery("#" + type + "_country").val();
        /**
         * Unfortunately, some countries copyright their zip codes
         * Meaning that you can only look up by the first 3 characters which
         * does not provide enough specificity so we skip them
         *
         * This is an incomplete list. Just hitting some big ones here.
         */
        var incompatibleCountries = ['GB', 'CA'];
        if (incompatibleCountries.indexOf(country) === -1) {
            ZipAutocompleteService.getZipData(country, zip, type);
        }
    };
    ZipAutocompleteService.getZipData = function (country, zip, type) {
        jQuery.ajax({
            url: "https://api.zippopotam.us/" + country + "/" + zip,
            dataType: 'json',
            success: function (result) {
                var _a = result.places[0], city = _a["place name"], state = _a["state abbreviation"];
                var state_field = jQuery("[name=\"" + type + "_state\"]:visible");
                // Cleanup Parsley messages
                state_field.val(state).trigger('change');
                state_field.removeClass('parsley-error').parent().find('.parsley-errors-list').remove();
                // If there's more than one result, don't autocomplete city
                // This prevents crappy autocompletes
                if (result.places.length === 1) {
                    var city_field = jQuery("#" + type + "_city");
                    // Cleanup Parsley messages
                    city_field.val(city).trigger('change');
                    city_field.removeClass('parsley-error').parent().find('.parsley-errors-list').remove();
                }
            }
        });
    };
    return ZipAutocompleteService;
}());
exports.ZipAutocompleteService = ZipAutocompleteService;


/***/ }),

/***/ "./sources/ts/order-pay.ts":
/*!*********************************!*\
  !*** ./sources/ts/order-pay.ts ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {



/***/ }),

/***/ 1:
/*!***************************************************************************************************************************!*\
  !*** multi ./sources/js/vendor.js ./sources/ts/checkout.ts ./sources/ts/order-pay.ts ./sources/scss/front/order-pay.scss ***!
  \***************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! ./sources/js/vendor.js */"./sources/js/vendor.js");
__webpack_require__(/*! ./sources/ts/checkout.ts */"./sources/ts/checkout.ts");
__webpack_require__(/*! ./sources/ts/order-pay.ts */"./sources/ts/order-pay.ts");
module.exports = __webpack_require__(/*! ./sources/scss/front/order-pay.scss */"./sources/scss/front/order-pay.scss");


/***/ })

/******/ });
//# sourceMappingURL=checkoutwc-order-pay.js.map