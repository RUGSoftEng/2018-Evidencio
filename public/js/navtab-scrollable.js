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
/******/ 	return __webpack_require__(__webpack_require__.s = 59);
/******/ })
/************************************************************************/
/******/ ({

/***/ 59:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(60);


/***/ }),

/***/ 60:
/***/ (function(module, exports) {

var hidWidth;
var scrollBarWidths = 40;

var widthOfList = function widthOfList() {
  var itemsWidth = 0;
  $('.tab-list a').each(function () {
    var itemWidth = $(this).outerWidth();
    itemsWidth += itemWidth;
  });
  return itemsWidth;
};

var widthOfHidden = function widthOfHidden() {
  return $('.tab-wrapper').outerWidth() - widthOfList() - getLeftPosi() - scrollBarWidths;
};

var getLeftPosi = function getLeftPosi() {
  return $('.tab-list').position().left;
};

var reAdjust = function reAdjust() {
  if ($('.tab-wrapper').outerWidth() < widthOfList()) {
    $('.tab-scroller-right').show().css('display', 'flex');
  } else {
    $('.tab-scroller-right').hide();
  }

  if (getLeftPosi() < 0) {
    $('.tab-scroller-left').show().css('display', 'flex');
  } else {
    $('.item').animate({ left: "-=" + getLeftPosi() + "px" }, 'slow');
    $('.tab-scroller-left').hide();
  }
};

reAdjust();

$(window).on('resize', function (e) {
  reAdjust();
});

$('.tab-scroller-right').click(function () {

  $('.tab-scroller-left').fadeIn('slow');
  $('.tab-scroller-right').fadeOut('slow');

  $('.tab-list').animate({ left: "+=" + widthOfHidden() + "px" }, 'slow', function () {});
});

$('.tab-scroller-left').click(function () {

  $('.tab-scroller-right').fadeIn('slow');
  $('.tab-scroller-left').fadeOut('slow');

  $('.tab-list').animate({ left: "-=" + getLeftPosi() + "px" }, 'slow', function () {});
});

/***/ })

/******/ });
//# sourceMappingURL=navtab-scrollable.js.map