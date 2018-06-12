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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	__webpack_require__.p = "/";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 343);
/******/ })
/************************************************************************/
/******/ ({

/***/ 343:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(344);


/***/ }),

/***/ 344:
/***/ (function(module, exports) {

/**
 * bootstrap-colorpalette.js
 * (c) 2013~ Jiung Kang
 * Licensed under the Apache License, Version 2.0 (the "License");
 */

(function ($) {
  "use strict";

  var aaColor = [['#000000', '#424242', '#636363', '#9C9C94', '#CEC6CE', '#EFEFEF', '#F7F7F7', '#FFFFFF'], ['#FF0000', '#FF9C00', '#FFFF00', '#00FF00', '#00FFFF', '#0000FF', '#9C00FF', '#FF00FF'], ['#F7C6CE', '#FFE7CE', '#FFEFC6', '#D6EFD6', '#CEDEE7', '#CEE7F7', '#D6D6E7', '#E7D6DE'], ['#E79C9C', '#FFC69C', '#FFE79C', '#B5D6A5', '#A5C6CE', '#9CC6EF', '#B5A5D6', '#D6A5BD'], ['#E76363', '#F7AD6B', '#FFD663', '#94BD7B', '#73A5AD', '#6BADDE', '#8C7BC6', '#C67BA5'], ['#CE0000', '#E79439', '#EFC631', '#6BA54A', '#4A7B8C', '#3984C6', '#634AA5', '#A54A7B'], ['#9C0000', '#B56308', '#BD9400', '#397B21', '#104A5A', '#085294', '#311873', '#731842'], ['#630000', '#7B3900', '#846300', '#295218', '#083139', '#003163', '#21104A', '#4A1031']];

  var createPaletteElement = function createPaletteElement(element, _aaColor) {
    element.addClass('bootstrap-colorpalette');
    var aHTML = [];
    $.each(_aaColor, function (i, aColor) {
      aHTML.push('<div>');
      $.each(aColor, function (i, sColor) {
        var sButton = ['<button type="button" class="btn-color" style="background-color:', sColor, '" data-value="', sColor, '" title="', sColor, '"></button>'].join('');
        aHTML.push(sButton);
      });
      aHTML.push('</div>');
    });
    element.html(aHTML.join(''));
  };

  var attachEvent = function attachEvent(palette) {
    palette.element.on('click', function (e) {
      var welTarget = $(e.target),
          welBtn = welTarget.closest('.btn-color');

      if (!welBtn[0]) {
        return;
      }

      var value = welBtn.attr('data-value');
      palette.value = value;
      palette.element.trigger({
        type: 'selectColor',
        color: value,
        element: palette.element
      });
    });
  };

  var Palette = function Palette(element, options) {
    this.element = element;
    createPaletteElement(element, options && options.colors || aaColor);
    attachEvent(this);
  };

  $.fn.extend({
    colorPalette: function colorPalette(options) {
      this.each(function () {
        var $this = $(this),
            data = $this.data('colorpalette');
        if (!data) {
          $this.data('colorpalette', new Palette($this, options));
        }
      });
      return this;
    }
  });
})($);

/***/ })

/******/ });
//# sourceMappingURL=bootstrap-colorpalette.js.map