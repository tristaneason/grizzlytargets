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
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

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
window.CompatibilityClasses = {};
window.CompatibilityClasses.AmazonPay = AmazonPay_1.AmazonPay;
window.CompatibilityClasses.BlueCheck = BlueCheck_1.BlueCheck;
window.CompatibilityClasses.Braintree = Braintree_1.Braintree;
window.CompatibilityClasses.BraintreeForWooCommerce = BraintreeForWooCommerce_1.BraintreeForWooCommerce;
window.CompatibilityClasses.CO2OK = CO2OK_1.CO2OK;
window.CompatibilityClasses.EUVatNumber = EUVatNumber_1.EUVatNumber;
window.CompatibilityClasses.InpsydePayPalPlus = InpsydePayPalPlus_1.InpsydePayPalPlus;
window.CompatibilityClasses.KlarnaCheckout = KlarnaCheckout_1.KlarnaCheckout;
window.CompatibilityClasses.KlarnaPayments = KlarnaPayments_1.KlarnaPayments;
window.CompatibilityClasses.MondialRelay = MondialRelay_1.MondialRelay;
window.CompatibilityClasses.NLPostcodeChecker = NLPostcodeChecker_1.NLPostcodeChecker;
window.CompatibilityClasses.OrderDeliveryDate = OrderDeliveryDate_1.OrderDeliveryDate;
window.CompatibilityClasses.PayPalCheckout = PayPalCheckout_1.PayPalCheckout;
window.CompatibilityClasses.PayPalForWooCommerce = PayPalForWooCommerce_1.PayPalForWooCommerce;
window.CompatibilityClasses.PostNL = PostNL_1.PostNL;
window.CompatibilityClasses.SendCloud = SendCloud_1.SendCloud;
window.CompatibilityClasses.ShipMondo = ShipMondo_1.ShipMondo;
window.CompatibilityClasses.Square = Square_1.Square;
window.CompatibilityClasses.Square1x = Square1x_1.Square1x;
window.CompatibilityClasses.SquareRecurring = SquareRecurring_1.SquareRecurring;
window.CompatibilityClasses.Stripe = Stripe_1.Stripe;
window.CompatibilityClasses.WooCommerceAddressValidation = WooCommerceAddressValidation_1.WooCommerceAddressValidation;
window.CompatibilityClasses.WooCommerceGermanized = WooCommerceGermanized_1.WooCommerceGermanized;
window.CompatibilityClasses.WooFunnelsOrderBumps = WooFunnelsOrderBumps_1.WooFunnelsOrderBumps;
window.CompatibilityClasses.WooSquarePro = WooSquarePro_1.WooSquarePro;
jQuery(document).ready(function () {
    window.load_main();
});


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
        if (window.cfwEventData.settings.enable_address_autocomplete !== true || window.cfwEventData.settings.is_order_received_page === true) {
            return;
        }
        var shipping_address_1 = jQuery('#shipping_address_1');
        var billing_address_1 = jQuery('#billing_address_1');
        shipping_address_1.prop('autocomplete', 'new-password');
        billing_address_1.prop('autocomplete', 'new-password');
        var shipping_autocomplete = new google.maps.places.Autocomplete(shipping_address_1.get(0), { types: ['geocode'] });
        var billing_autocomplete = new google.maps.places.Autocomplete(billing_address_1.get(0), { types: ['geocode'] });
        shipping_autocomplete.setFields(['address_component']);
        billing_autocomplete.setFields(['address_component']);
        if (false !== cfwEventData.settings.address_autocomplete_shipping_countries) {
            shipping_autocomplete.setComponentRestrictions({ 'country': cfwEventData.settings.address_autocomplete_shipping_countries });
        }
        if (false !== cfwEventData.settings.address_autocomplete_billing_countries) {
            billing_autocomplete.setComponentRestrictions({ 'country': cfwEventData.settings.address_autocomplete_billing_countries });
        }
        shipping_autocomplete.addListener('place_changed', function () { _this.fillAddress('shipping_', shipping_autocomplete); });
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

/***/ 3:
/*!***************************************************!*\
  !*** multi ./sources/ts/compatibility-classes.ts ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./sources/ts/compatibility-classes.ts */"./sources/ts/compatibility-classes.ts");


/***/ })

/******/ });
//# sourceMappingURL=checkoutwc-compatibility.js.map