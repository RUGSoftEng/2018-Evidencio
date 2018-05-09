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
/******/ 	return __webpack_require__(__webpack_require__.s = 49);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */,
/* 1 */
/***/ (function(module, exports) {

/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file.
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = injectStyles
  }

  if (hook) {
    var functional = options.functional
    var existing = functional
      ? options.render
      : options.beforeCreate

    if (!functional) {
      // inject component registration as beforeCreate hook
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    } else {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return existing(h, context)
      }
    }
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function(useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if(item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function(modules, mediaQuery) {
		if(typeof modules === "string")
			modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for(var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if(typeof id === "number")
				alreadyImportedModules[id] = true;
		}
		for(i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if(typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if(mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if(mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};

function cssWithMappingToString(item, useSourceMap) {
	var content = item[1] || '';
	var cssMapping = item[3];
	if (!cssMapping) {
		return content;
	}

	if (useSourceMap && typeof btoa === 'function') {
		var sourceMapping = toComment(cssMapping);
		var sourceURLs = cssMapping.sources.map(function (source) {
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */'
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}


/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/

var hasDocument = typeof document !== 'undefined'

if (typeof DEBUG !== 'undefined' && DEBUG) {
  if (!hasDocument) {
    throw new Error(
    'vue-style-loader cannot be used in a non-browser environment. ' +
    "Use { target: 'node' } in your Webpack config to indicate a server-rendering environment."
  ) }
}

var listToStyles = __webpack_require__(55)

/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/

var stylesInDom = {/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/}

var head = hasDocument && (document.head || document.getElementsByTagName('head')[0])
var singletonElement = null
var singletonCounter = 0
var isProduction = false
var noop = function () {}
var options = null
var ssrIdKey = 'data-vue-ssr-id'

// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE = typeof navigator !== 'undefined' && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

module.exports = function (parentId, list, _isProduction, _options) {
  isProduction = _isProduction

  options = _options || {}

  var styles = listToStyles(parentId, list)
  addStylesToDom(styles)

  return function update (newList) {
    var mayRemove = []
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i]
      var domStyle = stylesInDom[item.id]
      domStyle.refs--
      mayRemove.push(domStyle)
    }
    if (newList) {
      styles = listToStyles(parentId, newList)
      addStylesToDom(styles)
    } else {
      styles = []
    }
    for (var i = 0; i < mayRemove.length; i++) {
      var domStyle = mayRemove[i]
      if (domStyle.refs === 0) {
        for (var j = 0; j < domStyle.parts.length; j++) {
          domStyle.parts[j]()
        }
        delete stylesInDom[domStyle.id]
      }
    }
  }
}

function addStylesToDom (styles /* Array<StyleObject> */) {
  for (var i = 0; i < styles.length; i++) {
    var item = styles[i]
    var domStyle = stylesInDom[item.id]
    if (domStyle) {
      domStyle.refs++
      for (var j = 0; j < domStyle.parts.length; j++) {
        domStyle.parts[j](item.parts[j])
      }
      for (; j < item.parts.length; j++) {
        domStyle.parts.push(addStyle(item.parts[j]))
      }
      if (domStyle.parts.length > item.parts.length) {
        domStyle.parts.length = item.parts.length
      }
    } else {
      var parts = []
      for (var j = 0; j < item.parts.length; j++) {
        parts.push(addStyle(item.parts[j]))
      }
      stylesInDom[item.id] = { id: item.id, refs: 1, parts: parts }
    }
  }
}

function createStyleElement () {
  var styleElement = document.createElement('style')
  styleElement.type = 'text/css'
  head.appendChild(styleElement)
  return styleElement
}

function addStyle (obj /* StyleObjectPart */) {
  var update, remove
  var styleElement = document.querySelector('style[' + ssrIdKey + '~="' + obj.id + '"]')

  if (styleElement) {
    if (isProduction) {
      // has SSR styles and in production mode.
      // simply do nothing.
      return noop
    } else {
      // has SSR styles but in dev mode.
      // for some reason Chrome can't handle source map in server-rendered
      // style tags - source maps in <style> only works if the style tag is
      // created and inserted dynamically. So we remove the server rendered
      // styles and inject new ones.
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  if (isOldIE) {
    // use singleton mode for IE9.
    var styleIndex = singletonCounter++
    styleElement = singletonElement || (singletonElement = createStyleElement())
    update = applyToSingletonTag.bind(null, styleElement, styleIndex, false)
    remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true)
  } else {
    // use multi-style-tag mode in all other cases
    styleElement = createStyleElement()
    update = applyToTag.bind(null, styleElement)
    remove = function () {
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  update(obj)

  return function updateStyle (newObj /* StyleObjectPart */) {
    if (newObj) {
      if (newObj.css === obj.css &&
          newObj.media === obj.media &&
          newObj.sourceMap === obj.sourceMap) {
        return
      }
      update(obj = newObj)
    } else {
      remove()
    }
  }
}

var replaceText = (function () {
  var textStore = []

  return function (index, replacement) {
    textStore[index] = replacement
    return textStore.filter(Boolean).join('\n')
  }
})()

function applyToSingletonTag (styleElement, index, remove, obj) {
  var css = remove ? '' : obj.css

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = replaceText(index, css)
  } else {
    var cssNode = document.createTextNode(css)
    var childNodes = styleElement.childNodes
    if (childNodes[index]) styleElement.removeChild(childNodes[index])
    if (childNodes.length) {
      styleElement.insertBefore(cssNode, childNodes[index])
    } else {
      styleElement.appendChild(cssNode)
    }
  }
}

function applyToTag (styleElement, obj) {
  var css = obj.css
  var media = obj.media
  var sourceMap = obj.sourceMap

  if (media) {
    styleElement.setAttribute('media', media)
  }
  if (options.ssrId) {
    styleElement.setAttribute(ssrIdKey, obj.id)
  }

  if (sourceMap) {
    // https://developer.chrome.com/devtools/docs/javascript-debugging
    // this makes source maps inside style tags work properly in Chrome
    css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */'
    // http://stackoverflow.com/a/26603875
    css += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + ' */'
  }

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild)
    }
    styleElement.appendChild(document.createTextNode(css))
  }
}


/***/ }),
/* 16 */,
/* 17 */,
/* 18 */,
/* 19 */,
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */,
/* 28 */,
/* 29 */,
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */,
/* 34 */,
/* 35 */,
/* 36 */,
/* 37 */,
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */,
/* 45 */,
/* 46 */,
/* 47 */,
/* 48 */,
/* 49 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(50);


/***/ }),
/* 50 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(51);
Vue.component("vueMultiselect", window.VueMultiselect.default);
Vue.component("workflowInformation", __webpack_require__(52));
Vue.component("variableViewList", __webpack_require__(58));
Vue.component("modalStep", __webpack_require__(75));

// ============================================================================================= //

/* Step-template:
    {-l
        id: -1,
        title: title,
        description: description,
        nodeId: -1,
        colour: '#0099ff',
        type: 'input' or 'result',
        create: true,
        destroy: false,
        [optional: 
          variables: [],
          rules: []
        ]
    }
    */

/* Level-template:
      {
        steps: []
      }
    */
window.vObj = new Vue({
  el: "#designerDiv",
  data: {
    modelLoaded: false,
    models: [],
    modelIds: [],
    numVariables: 0,
    usedVariables: {},
    timesUsedVariables: {},

    title: "Default title",
    description: "Default description",
    languageCode: "EN",
    steps: [],
    levels: [],
    maxStepsPerLevel: 0,
    stepsChanged: false,
    levelsChanged: false,
    nodeCounter: 0,

    deltaX: 150,
    deltaY: 250,
    addLevelButtons: [],
    addStepButtons: [],

    selectedStepId: 0,
    modalChanged: false,

    workflowId: null
  },

  created: function created() {
    var _this = this;

    // Event called when the Cytoscape graph is ready for interaction.
    Event.listen("graphReady", function () {
      _this.workflowId = _this.urlParam("workflow");
      if (_this.workflowId === null) {
        Event.fire("normalStart");
      } else _this.loadWorkflow(_this.workflowId);
    });
    // Event called when a normal (empty) start should occur.
    Event.listen("normalStart", function () {
      _this.addLevel(0);
      _this.addStep("Starter step", "First step in the model shown to the patient. Change this step to fit your needs.", 0);
      _this.fitView();
    });
    // Event called when the user tries to load an Evidencio model
    Event.listen("modelLoad", function (modelId) {
      _this.loadModelEvidencio(modelId);
    });
    // Event called when the user tries to save a workflow
    Event.listen("save", function () {
      _this.saveWorkflow();
    });
  },


  computed: {
    // Array of all variables, pass by reference rather than by value.
    allVariables: function allVariables() {
      if (this.modelLoaded) {
        var allvars = [];
        this.models.forEach(function (element) {
          allvars = allvars.concat(element.variables);
        });
        return allvars;
      } else return [];
    },

    // Deep-copy of the models and variables, used for MultiSelect
    possibleVariables: function possibleVariables() {
      if (this.modelLoaded) {
        deepCopy = JSON.parse(JSON.stringify(this.models));
        return deepCopy;
      }
      return [];
    },

    // Array containing children of currently selected step
    childrenNodes: function childrenNodes() {
      var _this2 = this;

      if (this.selectedStepId == -1) return [];
      var levelIndex = this.getStepLevel(this.selectedStepId);
      if (levelIndex == -1 || levelIndex == this.levels.length - 1) return [];
      var options = [];
      this.levels[levelIndex + 1].steps.forEach(function (element) {
        options.push({
          stepId: element,
          title: _this2.steps[element].title,
          id: _this2.steps[element].id,
          colour: _this2.steps[element].colour,
          ind: options.length
        });
      });
      return options;
    },

    // Array of model-representations for API-call
    modelChoiceRepresentation: function modelChoiceRepresentation() {
      var representation = [];
      this.models.forEach(function (element) {
        representation.push({
          title: element.title,
          id: element.id
        });
      });
      return representation;
    }
  },

  methods: {
    /**
     * Load model from Evidencio API, Model is prepared for later saving.
     * @param {Number} -> modelId is the id of the Evidencio model that should be loaded.
     */
    loadModelEvidencio: function loadModelEvidencio(modelId) {
      var self = this;
      if (!this.isModelLoaded(modelId)) {
        $.ajax({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          },
          url: "/designer/fetch",
          type: "POST",
          data: {
            modelId: modelId
          },
          success: function success(result) {
            self.debug = result;
            self.models.push(JSON.parse(result));
            var newVars = self.models[self.models.length - 1].variables.length;
            self.numVariables += newVars;
            self.models[self.models.length - 1].variables.map(function (x) {
              x["databaseId"] = -1;
              if (x["type"] == "categorical") {
                x.options.map(function (y) {
                  y["databaseId"] = -1;
                });
              }
            });
            self.modelLoaded = true;
            self.modelIds.push(modelId);
            self.recountVariableUses();
          }
        });
      }
    },


    /**
     * Save Workflow in database, IDs of saved data are set after saving.
     */
    saveWorkflow: function saveWorkflow() {
      var _this3 = this;

      var self = this;
      var url = "/designer/save";
      if (this.workflowId != null) url = url + "/" + this.workflowId;
      this.steps.map(function (x, index) {
        x["level"] = _this3.getStepLevel(index);
      }), $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        url: url,
        type: "POST",
        data: {
          title: self.title,
          description: self.description,
          languageCode: self.languageCode,
          steps: self.steps,
          variables: self.usedVariables,
          modelIds: self.modelIds
        },
        success: function success(result) {
          self.workflowId = Number(result.workflowId);
          var numberOfSteps = self.steps.length;
          for (var index = 0; index < numberOfSteps; index++) {
            self.steps[index].id = result.stepIds[index];
          }
          var varIds = result.variableIds;
          for (var key in varIds) {
            if (varIds.hasOwnProperty(key)) {
              self.usedVariables[key].databaseId = Number(varIds[key]);
            }
          }
          var optIds = result.optionIds;
          for (var key in optIds) {
            if (optIds.hasOwnProperty(key)) {
              optIds[key].forEach(function (element, index) {
                self.usedVariables[key].options[index].databaseId = Number(element);
              });
            }
          }
        }
      });
    },


    /**
     * Load a Workflow from the database, as of now does nothing with the workflow
     * @param {Number} workflowId 
     */
    loadWorkflow: function loadWorkflow(workflowId) {
      var self = this;
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        url: "/designer/load/" + workflowId,
        type: "POST",
        data: {},
        success: function success(result) {
          console.log("Workflow loaded: " + result.success);
          if (result.success) {
            result.evidencioModels.forEach(function (element) {
              self.loadModelEvidencio(element);
            });
            var currentSteps = self.stepsChanged;
            var currentLevels = self.levelsChanged;
            self.title = result.title;
            self.description = result.description;
            self.languageCode = result.languageCode;
            result.steps.forEach(function (element, index) {
              while (self.levels.length <= element.level) {
                self.addLevel(self.levels.length);
              }console.log("Index: " + index + ", #steps: " + self.steps.length);
              self.addStep(element.title, element.description, element.level);
              self.steps[index].id = element.id;
              self.steps[index].colour = element.colour;
              self.steps[index].variables = element.variables;
            });
            if (result.usedVariables.constructor !== Array) self.usedVariables = result.usedVariables;
            self.recountVariableUses();
            self.stepsChanged = !currentSteps;
            self.levelsChanged = !currentLevels;
          } else {
            self.workflowId = null;
            Event.fire("normalStart");
          }
        }
      });
    },


    /**
     * Get the value of a URL parameter.
     * @param {String} name of the url parameter to get the value from
     * @return {string} The value that was found, null if none.
     */
    urlParam: function urlParam(name) {
      var results = new RegExp("[?&]" + name + "=([^]*)").exec(window.location.href);
      if (results == null) {
        return null;
      } else {
        return results[1] || 0;
      }
    },


    /**
     * Checks if a model is already loaded, to ensure models aren't loaded twice.
     * @param {Number} modelID of the model to be checked.
     * @return {Boolean} true if found, false if not
     */
    isModelLoaded: function isModelLoaded(modelId) {
      for (var index = 0; index < this.modelIds.length; index++) {
        if (this.modelIds[index] == modelId) return true;
      }
      return false;
    },

    /**
     * Adds level to workflow. Levels contain one or more steps. The first level can contain at most one step.
     * @param {Number} index of position level should be added
     */
    addLevel: function addLevel(index) {
      this.levels.splice(index, 0, {
        steps: []
      });
      this.levelsChanged = !this.levelsChanged;
    },


    /**
     * Add step to workflow
     * @param {String} title of step
     * @param {String} description of step
     * @param {Number} level at which to add the step (it should exist!)
     */
    addStep: function addStep(title, description, level) {
      this.steps.push({
        id: -1,
        title: title,
        description: description,
        nodeId: -1,
        colour: "#0099ff",
        type: "input",
        variables: [],
        varCounter: 0,
        rules: [],
        apiCall: {
          model: null,
          variables: []
        },
        create: true,
        destroy: false
      });
      this.stepsChanged = !this.stepsChanged;
      this.levels[level].steps.push(this.steps.length - 1);
      if (this.levels[level].steps.length > this.maxStepsPerLevel) this.maxStepsPerLevel = this.levels[level].steps.length;
    },


    /**
     * Removes step (and node) given by step-id id.
     * @param {Number} id of step that should be removed. IMPORTANT: this should be the step-id, not the node-id
     */
    removeStep: function removeStep(id) {
      this.steps[id].destroy = true;
      this.stepsChanged = !this.stepsChanged;
    },


    /**
     * Fit the viewport around the nodes shown.
     */
    fitView: function fitView() {
      cy.fit();
    },


    /**
     * Positions the AddLevelButtons.
     */
    positionAddLevelButtons: function positionAddLevelButtons() {
      for (var index = 0; index < this.addLevelButtons.length; index++) {
        var element = this.addLevelButtons[index].nodeId;
        cy.getElementById(element).position({
          x: (this.maxStepsPerLevel / 2 + 1) * this.deltaX,
          y: (index + 0.5) * this.deltaY
        });
      }
    },


    /**
     * Positions the AddStepButtons.
     */
    positionAddStepButtons: function positionAddStepButtons() {
      if (this.levels.length > 0) {
        for (var index = 0; index < this.addStepButtons.length; index++) {
          var element = this.addStepButtons[index].nodeId;
          cy.getElementById(element).position({
            x: (this.levels[index + 1].steps.length / 2 + (this.levels[index + 1].steps.length > 0 ? 0.5 : 0)) * this.deltaX,
            y: (index + 1) * this.deltaY
          });
        }
      }
    },


    /**
     * Positions the Steps.
     */
    positionSteps: function positionSteps() {
      for (var indexLevel = 0; indexLevel < this.levels.length; indexLevel++) {
        var elementLevel = this.levels[indexLevel].steps;
        var left = -(elementLevel.length - 1) * this.deltaX / 2;
        for (var indexStep = 0; indexStep < elementLevel.length; indexStep++) {
          var elementStep = this.steps[elementLevel[indexStep]].nodeId;
          cy.getElementById(elementStep).position({
            x: left + indexStep * this.deltaX,
            y: indexLevel * this.deltaY
          });
        }
      }
    },


    /**
     * Returns the index of the AddLevelButton-node referred to by id.
     * @param {String} id of the node for which the index has to be found
     * @return {Number} index in button in array
     */
    getAddLevelButtonIndex: function getAddLevelButtonIndex(id) {
      for (var index = 0; index < this.addLevelButtons.length; index++) {
        var element = this.addLevelButtons[index].nodeId;
        if (element == id) {
          return index;
        }
      }
      return -1;
    },


    /**
     * Returns the index of the AddStepButton-node based on its id.
     * @param {String} id of the AddStepButton-node that the index is wanted of.
     * @return {Number} index of button in array
     */
    getAddStepButtonIndex: function getAddStepButtonIndex(id) {
      for (var index = 0; index < this.addStepButtons.length; index++) {
        var element = this.addStepButtons[index].nodeId;
        if (element == id) {
          return index;
        }
      }
      return -1;
    },


    /**
     * Opens an option-modal for the node that is clicked on.
     * @param {Object} nodeRef is the reference to the node that is clicked on.
     */
    prepareModal: function prepareModal(nodeRef) {
      this.selectedStepId = nodeRef.scratch("_nodeId");
      this.modalChanged = !this.modalChanged;
    },


    /**
     * Saves the changes made to a step (variables added, etc.)
     * @param {Object} changedStep has the new step and usedVariables (with changes made in the modal)
     */
    applyChanges: function applyChanges(changedStep) {
      this.steps[this.selectedStepId] = changedStep.step;
      this.usedVariables = changedStep.usedVars;

      // Set new backgroundcolor
      cy.getElementById(this.steps[this.selectedStepId].nodeId).style({
        "background-color": changedStep.step.colour
      });
      this.recountVariableUses();
    },


    /**
     * Returns the level (height in graph) of a step
     * @param {integer} stepIndex of step
     * @return {Number} the level at which a step is.
     */
    getStepLevel: function getStepLevel(stepIndex) {
      for (var levelIndex = 0; levelIndex < this.levels.length; levelIndex++) {
        var level = this.levels[levelIndex].steps;
        for (var index = 0; index < level.length; index++) {
          if (stepIndex == level[index]) return levelIndex;
        }
      }
      return -1;
    },


    /**
     * Recounts the number of times a variable is used, to be used whenever this changes.
     */
    recountVariableUses: function recountVariableUses() {
      var _this4 = this;

      this.timesUsedVariables = {};
      this.models.forEach(function (element) {
        element.variables.forEach(function (variable) {
          _this4.timesUsedVariables[variable.id.toString()] = 0;
        });
      });

      for (var indexStep = 0; indexStep < this.steps.length; indexStep++) {
        var elementStep = this.steps[indexStep];
        if (elementStep.type == "input") {
          for (var indexVariable = 0; indexVariable < elementStep.variables.length; indexVariable++) {
            var element = elementStep.variables[indexVariable];
            this.timesUsedVariables[this.usedVariables[element].id.toString()] += 1;
          }
        }
      }
    },


    /**
     * Changes the details of the currently loaded workflow.
     * @param {Object} newDetails contains an object with the new details (fiels title, description)
     */
    changeWorkflowDetails: function changeWorkflowDetails(newDetails) {
      this.title = newDetails.title;
      this.description = newDetails.description;
    }
  },

  watch: {
    /**
     * stepsChanged is used to indicate if a step has been set to be created or removed, this function does the actual work.
     */
    stepsChanged: function stepsChanged() {
      for (var index = 0; index < this.steps.length; index++) {
        if (this.steps[index].create) {
          this.steps[index].nodeId = cy.add({
            classes: "node",
            data: {
              id: "node_" + this.nodeCounter
            },
            scratch: {
              _nodeId: index
            },
            style: {
              "background-color": this.steps[index].colour
            }
          }).id();
          this.steps[index].create = false;
          cy.getElementById(this.steps[index].nodeId).style({
            label: this.steps[index].id
          });
          this.nodeCounter++;
        }
        if (this.steps[index].destroy) {
          cy.remove(this.steps[index].nodeId);
          this.steps.splice(index, 1);
        }
      }
      this.positionAddStepButtons();
      this.positionAddLevelButtons();
      this.positionSteps();
    },

    /**
     * levelsChanged is used to indicate if a level has been added or removed, this function does the actual work
     */
    levelsChanged: function levelsChanged() {
      while (this.levels.length > this.addLevelButtons.length) {
        if (this.addLevelButtons.length > 0) {
          this.addStepButtons.push({
            nodeId: cy.add({
              classes: "buttonAddStep"
            }).id()
          });
        }
        this.addLevelButtons.push({
          nodeId: cy.add({
            classes: "buttonAddLevel"
          }).id()
        });
      }
      while (this.levels.length < this.addLevelButtons.length) {
        cy.remove(this.addLevelButtons.pop().nodeId);
        cy.remove(this.addStepButtons.pop().nodeId);
      }
      this.positionAddLevelButtons();
      this.positionAddStepButtons();
      this.positionSteps();
    },

    selectedVariables: function selectedVariables() {
      this.recountVariableUses();
    }
  }
});

/***/ }),
/* 51 */
/***/ (function(module, exports) {

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

window.Event = new (function () {
  function _class() {
    _classCallCheck(this, _class);

    this.vue = new Vue();
  }

  _createClass(_class, [{
    key: "fire",
    value: function fire(event) {
      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      this.vue.$emit(event, data);
    }
  }, {
    key: "listen",
    value: function listen(event, callback) {
      this.vue.$on(event, callback);
    }
  }]);

  return _class;
}())();

/***/ }),
/* 52 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(53)
}
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(56)
/* template */
var __vue_template__ = __webpack_require__(57)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-2f3d27f4"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/WorkflowInformation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2f3d27f4", Component.options)
  } else {
    hotAPI.reload("data-v-2f3d27f4", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 53 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(54);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(15)("54111701", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../node_modules/css-loader/index.js?sourceMap!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2f3d27f4\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./WorkflowInformation.vue", function() {
     var newContent = require("!!../../../../node_modules/css-loader/index.js?sourceMap!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-2f3d27f4\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./WorkflowInformation.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 54 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(14)(true);
// imports


// module
exports.push([module.i, "\n.right[data-v-2f3d27f4] {\n  float: right;\n}\ntextarea[data-v-2f3d27f4] {\n  resize: none;\n}\n.form-control-plaintext[disabled][data-v-2f3d27f4] {\n  background-color: white;\n}\n", "", {"version":3,"sources":["/home/jaap/Evidencio/2018-Evidencio/resources/assets/js/components/WorkflowInformation.vue"],"names":[],"mappings":";AAAA;EACE,aAAa;CAAE;AAEjB;EACE,aAAa;CAAE;AAEjB;EACE,wBAAwB;CAAE","file":"WorkflowInformation.vue","sourcesContent":[".right {\n  float: right; }\n\ntextarea {\n  resize: none; }\n\n.form-control-plaintext[disabled] {\n  background-color: white; }\n"],"sourceRoot":""}]);

// exports


/***/ }),
/* 55 */
/***/ (function(module, exports) {

/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */
module.exports = function listToStyles (parentId, list) {
  var styles = []
  var newStyles = {}
  for (var i = 0; i < list.length; i++) {
    var item = list[i]
    var id = item[0]
    var css = item[1]
    var media = item[2]
    var sourceMap = item[3]
    var part = {
      id: parentId + ':' + i,
      css: css,
      media: media,
      sourceMap: sourceMap
    }
    if (!newStyles[id]) {
      styles.push(newStyles[id] = { id: id, parts: [part] })
    } else {
      newStyles[id].parts.push(part)
    }
  }
  return styles
}


/***/ }),
/* 56 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      editing: false,
      localTitle: "",
      localDescription: ""
    };
  },


  props: {
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      required: true
    }
  },

  methods: {
    change: function change() {
      this.$emit("change", {
        title: this.localTitle,
        description: this.localDescription
      });
    },
    refresh: function refresh() {
      this.localTitle = this.title;
      this.localDescription = this.description;
    },
    saveWorkflow: function saveWorkflow() {
      Event.fire("save");
    }
  },

  computed: {
    getImage: function getImage() {
      if (this.editing) return "/images/check.svg";else return "/images/pencil.svg";
    }
  },

  mounted: function mounted() {
    this.refresh();
  },

  watch: {
    title: function title() {
      this.refresh();
    },
    localTitle: function localTitle() {
      this.refresh();
    }
  }
});

/***/ }),
/* 57 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "card height-100" }, [
    _c("div", { staticClass: "card-header d-flex align-items-center" }, [
      _vm._v("\n        Workflow\n        "),
      _c(
        "button",
        {
          staticClass: "btn btn-primary ml-2",
          attrs: { type: "button" },
          on: { click: _vm.saveWorkflow }
        },
        [_vm._v("Save Workflow")]
      )
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "card-body scrollbarAtProject full-height" }, [
      _c("form", { attrs: { onsubmit: "return false" } }, [
        _c("div", { staticClass: "form-group" }, [
          _c("label", { attrs: { for: "title" } }, [_vm._v("Title: ")]),
          _vm._v(" "),
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.localTitle,
                expression: "localTitle"
              }
            ],
            class: {
              "form-control": _vm.editing,
              "form-control-plaintext": !_vm.editing
            },
            attrs: {
              type: "text",
              name: "",
              id: "title' + indexItem",
              placeholder: "Title",
              disabled: !_vm.editing
            },
            domProps: { value: _vm.localTitle },
            on: {
              input: [
                function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.localTitle = $event.target.value
                },
                _vm.change
              ]
            }
          })
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "form-group" }, [
          _c("label", { attrs: { for: "description" } }, [
            _vm._v("Description: ")
          ]),
          _vm._v(" "),
          _c("textarea", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.localDescription,
                expression: "localDescription"
              }
            ],
            class: {
              "form-control": _vm.editing,
              "form-control-plaintext": !_vm.editing
            },
            attrs: {
              id: "description",
              cols: "30",
              rows: "3",
              placeholder: "Description",
              disabled: !_vm.editing
            },
            domProps: { value: _vm.localDescription },
            on: {
              input: [
                function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.localDescription = $event.target.value
                },
                _vm.change
              ]
            }
          }),
          _vm._v(" "),
          _c("input", {
            staticClass: "buttonIcon right",
            attrs: { type: "image", src: _vm.getImage, alt: "Edit" },
            on: {
              click: function($event) {
                _vm.editing = !_vm.editing
              }
            }
          })
        ])
      ])
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-2f3d27f4", module.exports)
  }
}

/***/ }),
/* 58 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(59)
/* template */
var __vue_template__ = __webpack_require__(74)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/VariableViewList.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-63841236", Component.options)
  } else {
    hotAPI.reload("data-v-63841236", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 59 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ModelLoad_vue__ = __webpack_require__(60);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__ModelLoad_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__ModelLoad_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__VariableViewItem_vue__ = __webpack_require__(65);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__VariableViewItem_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__VariableViewItem_vue__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    ModelLoad: __WEBPACK_IMPORTED_MODULE_0__ModelLoad_vue___default.a,
    VariableViewItem: __WEBPACK_IMPORTED_MODULE_1__VariableViewItem_vue___default.a
  },
  props: {
    allVariables: {
      type: Array,
      required: true
    },
    allVariablesUsed: {
      type: Object,
      required: true
    }
  },

  methods: {
    selectCard: function selectCard(index) {
      var numberOfUsedVariables = Object.keys(this.allVariablesUsed).length;
      for (var ind = 0; ind < numberOfUsedVariables; ind++) {
        if (ind == index) $("#varViewCollapse_" + ind).collapse("toggle");else $("#varViewCollapse_" + ind).collapse("hide");
      }
    }
  }
});

/***/ }),
/* 60 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(61)
}
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(63)
/* template */
var __vue_template__ = __webpack_require__(64)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-94266ff0"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/ModelLoad.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-94266ff0", Component.options)
  } else {
    hotAPI.reload("data-v-94266ff0", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 61 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(62);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(15)("66c29602", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../node_modules/css-loader/index.js?sourceMap!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-94266ff0\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ModelLoad.vue", function() {
     var newContent = require("!!../../../../node_modules/css-loader/index.js?sourceMap!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-94266ff0\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../node_modules/sass-loader/lib/loader.js!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ModelLoad.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 62 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(14)(true);
// imports


// module
exports.push([module.i, "\n#inputModelID[data-v-94266ff0] {\n  width: 50px;\n}\n", "", {"version":3,"sources":["/home/jaap/Evidencio/2018-Evidencio/resources/assets/js/components/ModelLoad.vue"],"names":[],"mappings":";AAAA;EACE,YAAY;CAAE","file":"ModelLoad.vue","sourcesContent":["#inputModelID {\n  width: 50px; }\n"],"sourceRoot":""}]);

// exports


/***/ }),
/* 63 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      modelID: 0
    };
  },

  methods: {
    modelLoad: function modelLoad() {
      Event.fire("modelLoad", this.modelID);
    }
  }
});

/***/ }),
/* 64 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("input", {
      directives: [
        {
          name: "model",
          rawName: "v-model",
          value: _vm.modelID,
          expression: "modelID"
        }
      ],
      attrs: { type: "number", id: "inputModelID", name: "inputModelID" },
      domProps: { value: _vm.modelID },
      on: {
        keyup: function($event) {
          if (
            !("button" in $event) &&
            _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")
          ) {
            return null
          }
          return _vm.modelLoad($event)
        },
        input: function($event) {
          if ($event.target.composing) {
            return
          }
          _vm.modelID = $event.target.value
        }
      }
    }),
    _vm._v(" "),
    _c(
      "button",
      {
        staticClass: "btn btn-primary ml-2",
        attrs: { type: "button" },
        on: { click: _vm.modelLoad }
      },
      [_vm._v("Load Model")]
    )
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-94266ff0", module.exports)
  }
}

/***/ }),
/* 65 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(66)
/* template */
var __vue_template__ = __webpack_require__(73)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/VariableViewItem.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-597e9a2b", Component.options)
  } else {
    hotAPI.reload("data-v-597e9a2b", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 66 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__VariableViewCategorical_vue__ = __webpack_require__(67);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__VariableViewCategorical_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__VariableViewCategorical_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__VariableViewContinuous_vue__ = __webpack_require__(70);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__VariableViewContinuous_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__VariableViewContinuous_vue__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    VariableViewCategorical: __WEBPACK_IMPORTED_MODULE_0__VariableViewCategorical_vue___default.a,
    VariableViewContinuous: __WEBPACK_IMPORTED_MODULE_1__VariableViewContinuous_vue___default.a
  },

  props: {
    variable: {
      type: Object,
      required: true
    },
    timesUsed: {
      type: Number,
      required: true
    },
    indexItem: {
      type: Number,
      required: true
    }
  },

  methods: {
    toggleShow: function toggleShow() {
      this.$emit("toggle", this.indexItem);
    }
  }
});

/***/ }),
/* 67 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(68)
/* template */
var __vue_template__ = __webpack_require__(69)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/VariableViewCategorical.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-435dae90", Component.options)
  } else {
    hotAPI.reload("data-v-435dae90", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 68 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    options: {
      type: Array,
      required: true
    }
  }
});

/***/ }),
/* 69 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "ul",
    _vm._l(_vm.options, function(option, index) {
      return _c("li", { key: index }, [_vm._v(_vm._s(option.title))])
    })
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-435dae90", module.exports)
  }
}

/***/ }),
/* 70 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(71)
/* template */
var __vue_template__ = __webpack_require__(72)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/VariableViewContinuous.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-12e71427", Component.options)
  } else {
    hotAPI.reload("data-v-12e71427", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 71 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    options: {
      type: Object,
      required: true
    }
  }
});

/***/ }),
/* 72 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("p", [_vm._v("Minimal value: " + _vm._s(_vm.options.min))]),
    _vm._v(" "),
    _c("p", [_vm._v("Maximal value: " + _vm._s(_vm.options.max))]),
    _vm._v(" "),
    _c("p", [_vm._v("Stepsize: " + _vm._s(_vm.options.step))]),
    _vm._v(" "),
    _c("p", [_vm._v("Unit: " + _vm._s(_vm.options.unit))])
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-12e71427", module.exports)
  }
}

/***/ }),
/* 73 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "card" }, [
    _c(
      "a",
      {
        staticClass: "card-header collapsed",
        attrs: {
          href: "#",
          id: "varViewCollapseHeader_" + _vm.indexItem,
          "aria-expanded": "true",
          "aria-controls": "varViewCollapse_" + _vm.variable.ind,
          "data-parent": "#accVariablesView"
        },
        on: { click: _vm.toggleShow }
      },
      [
        _c("h6", { staticClass: "mb-0" }, [
          _vm._v(
            "\n            " + _vm._s(_vm.variable.title) + "\n            "
          ),
          _c(
            "span",
            {
              staticClass: "badge badge-pill",
              class: {
                "badge-danger": _vm.timesUsed == 0,
                "badge-success": _vm.timesUsed > 0
              }
            },
            [_vm._v(_vm._s(_vm.timesUsed))]
          )
        ])
      ]
    ),
    _vm._v(" "),
    _c(
      "div",
      {
        staticClass: "collapse",
        attrs: {
          id: "varViewCollapse_" + _vm.indexItem,
          "aria-labelledby": "#varViewCollapseHeader_" + _vm.indexItem
        }
      },
      [
        _c("p", [_vm._v(_vm._s(_vm.variable.title))]),
        _vm._v(" "),
        _c("p", [_vm._v(_vm._s(_vm.variable.description))]),
        _vm._v(" "),
        _vm.variable.type == "categorical"
          ? _c("variable-view-categorical", {
              attrs: { options: _vm.variable.options }
            })
          : _vm._e(),
        _vm._v(" "),
        _vm.variable.type == "continuous"
          ? _c("variable-view-continuous", {
              attrs: { options: _vm.variable.options }
            })
          : _vm._e()
      ],
      1
    )
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-597e9a2b", module.exports)
  }
}

/***/ }),
/* 74 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "card height-100" }, [
    _c(
      "div",
      { staticClass: "card-header" },
      [_vm._v("\n        Variables "), _c("model-load")],
      1
    ),
    _vm._v(" "),
    _c("div", { staticClass: "card-body height-100 scrollbarAtProject" }, [
      _c(
        "div",
        { attrs: { id: "accVariablesView" } },
        [
          _vm.allVariables.length == 0
            ? _c("div", { staticClass: "card" }, [_vm._m(0)])
            : _vm._e(),
          _vm._v(" "),
          _vm._l(_vm.allVariables, function(variable, index) {
            return _c("variable-view-item", {
              key: index,
              attrs: {
                "index-item": index,
                variable: variable,
                "times-used": _vm.allVariablesUsed[variable.id.toString()]
              },
              on: {
                toggle: function($event) {
                  _vm.selectCard($event)
                }
              }
            })
          })
        ],
        2
      )
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c(
      "div",
      { staticClass: "card-header", attrs: { id: "headingOne" } },
      [
        _c("h5", { staticClass: "mb-0" }, [
          _vm._v(
            "\n                        No variables added yet...\n                    "
          )
        ])
      ]
    )
  }
]
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-63841236", module.exports)
  }
}

/***/ }),
/* 75 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(76)
/* template */
var __vue_template__ = __webpack_require__(89)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/ModalStep.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-682a3b1c", Component.options)
  } else {
    hotAPI.reload("data-v-682a3b1c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 76 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__VariableEditList_vue__ = __webpack_require__(77);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__VariableEditList_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__VariableEditList_vue__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__RuleEditList_vue__ = __webpack_require__(83);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__RuleEditList_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__RuleEditList_vue__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    VariableEditList: __WEBPACK_IMPORTED_MODULE_0__VariableEditList_vue___default.a,
    RuleEditList: __WEBPACK_IMPORTED_MODULE_1__RuleEditList_vue___default.a
  },
  props: {
    stepId: {
      type: Number,
      required: true
    },
    step: {
      type: Object,
      default: function _default() {}
    },
    usedVariables: {
      type: Object,
      required: true
    },
    possibleVariables: {
      type: Array,
      required: true
    },
    childNodes: {
      type: Array,
      required: true
    },
    changed: {
      type: Boolean,
      required: true
    }
  },

  mounted: function mounted() {
    var self = this;
    $("#colorPalette").colorPalette().on("selectColor", function (evt) {
      self.localStep.colour = evt.color;
    });
  },

  watch: {
    changed: function changed() {
      this.reload();
    }
  },

  methods: {
    reload: function reload() {
      this.localStep = JSON.parse(JSON.stringify(this.step));
      this.localUsedVariables = JSON.parse(JSON.stringify(this.usedVariables));
      this.setSelectedVariables();
    },
    apply: function apply() {
      this.$emit("change", {
        step: this.localStep,
        usedVars: this.localUsedVariables
      });
    },


    /**
     * Adds the selected variables to the selectedVariable part of the multiselect.
     * Due to the work-around to remove groups, this is required. It is not nice/pretty/fast, but it works.
     */
    setSelectedVariables: function setSelectedVariables() {
      this.multiSelectedVariables = [];
      for (var index = 0; index < this.localStep.variables.length; index++) {
        var origID = this.localUsedVariables[this.localStep.variables[index]].id;
        findVariable: for (var indexOfMod = 0; indexOfMod < this.possibleVariables.length; indexOfMod++) {
          var element = this.possibleVariables[indexOfMod];
          for (var indexInMod = 0; indexInMod < element.variables.length; indexInMod++) {
            if (element.variables[indexInMod].id == origID) {
              this.multiSelectedVariables.push(element.variables[indexInMod]);
              break findVariable;
            }
          }
        }
      }
    },


    /**
     * Returns the text shown when more than the limit of options are selected.
     * @param {integer} [count] is the number of not-shown options.
     */
    multiselectVariablesText: function multiselectVariablesText(count) {
      return " and " + count + " other variable(s)";
    },


    /**
     * Removes the variables from the step.
     * @param {array||object} [removedVariables] are the variables to be removed
     */
    multiRemoveVariables: function multiRemoveVariables(removedVariables) {
      var _this = this;

      if (removedVariables.constructor == Array) {
        removedVariables.forEach(function (element) {
          _this.multiRemoveSingleVariable(element);
        });
      } else {
        this.multiRemoveSingleVariable(removedVariables);
      }
    },


    /**
     * Helper function for modalRemoveVariables(removedVariables), removes a single variable
     * @param {object} [removedVariable] the variable-object to be removed
     */
    multiRemoveSingleVariable: function multiRemoveSingleVariable(removedVariable) {
      for (var index = 0; index < this.localStep.variables.length; index++) {
        if (this.localUsedVariables[this.localStep.variables[index]].id == removedVariable.id) {
          delete this.localUsedVariables[this.localStep.variables[index]];
          this.localStep.variables.splice(index, 1);
          return;
        }
      }
    },


    /**
     * Selects the variables from the step.
     * @param {array||object} [selectedVariables] are the variables to be selected
     */
    multiSelectVariables: function multiSelectVariables(selectedVariables) {
      var _this2 = this;

      if (selectedVariables.constructor == Array) {
        selectedVariables.forEach(function (element) {
          _this2.multiSelectSingleVariable(element);
        });
      } else {
        this.multiSelectSingleVariable(selectedVariables);
      }
    },


    /**
     * Helper function for modalSelectVariables(selectedVariables), selects a single variable
     * @param {object} [selectedVariable] the variable-object to be selected
     */
    multiSelectSingleVariable: function multiSelectSingleVariable(selectedVariable) {
      var varName = "var" + this.stepId + "_" + this.localStep.varCounter++;
      this.localStep.variables.push(varName);
      this.localUsedVariables[varName] = JSON.parse(JSON.stringify(selectedVariable));
    }

    // /**
    //  * Adds a rule to the list of rules
    //  */
    // addRule() {
    //   this.modalRules.push({
    //     name: "Go to target",
    //     rule: [],
    //     target: -1
    //   });
    //   this.modalEditRuleFlags.push(false);
    // },

    // /**
    //  * Removes the rule with the given index from the list
    //  * @param {integer} [ruleIndex] of rule to be removed
    //  */
    // removeRule(ruleIndex) {
    //   this.modalRules.splice(ruleIndex, 1);
    //   this.modalEditRuleFlags.splice(ruleIndex, 1);
    // },

    // /**
    //  * Allows for a rule to be edited.
    //  * @param {integer} [index] of the rule to be edited
    //  */
    // editRule(index) {
    //   Vue.set(this.modalEditRuleFlags, index, !this.modalEditRuleFlags[index]);
    // },

    // /**
    //  * Returns the index in the models-array based on the Evidencio model ID, -1 if it does not exist.
    //  * @param {integer} [modelID] is the Evidencio model ID.
    //  */
    // getModelIndex(modelID) {
    //   for (let index = 0; index < this.models.length; index++) {
    //     if (this.models[index].id == modelID) return index;
    //   }
    //   return -1;
    // },

    // /**
    //  * Sets the variables-array in the apiCall-object to the variables of the newly selected model
    //  * @param {object} [selectedModel] is the newly selected model
    //  */
    // apiCallModelChangeAction(selectedModel) {
    //   let modID = this.getModelIndex(selectedModel.id);
    //   if (modID == -1) {
    //     this.modalApiCall.variables = [];
    //     return;
    //   }
    //   let modVars = [];
    //   this.models[modID].variables.forEach(element => {
    //     modVars.push({
    //       originalTitle: element.title,
    //       originalID: element.id,
    //       targetID: null
    //     });
    //   });
    //   this.modalApiCall.variables = modVars;
    // }

  },

  data: function data() {
    return {
      localStep: {},
      localUsedVariables: {},
      multiSelectedVariables: []
      /*  
      nodeID: -1, //ID in vue steps-array
      DatabaseStepId: -1, //ID in database
      modalStepType: "input",
      modalSelectedColor: "#000000",
      modalMultiselectSelectedVariables: [],
      modalSelectedVariables: [],
      modalVarCounter: -1,
      modalUsedVariables: {},
      modalRules: [],
      modalApiCall: {
        model: null,
        variables: []
      }*/
    };
  }
});

/***/ }),
/* 77 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(78)
/* template */
var __vue_template__ = __webpack_require__(82)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/VariableEditList.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0c99109b", Component.options)
  } else {
    hotAPI.reload("data-v-0c99109b", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 78 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__VariableEditItem_vue__ = __webpack_require__(79);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__VariableEditItem_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__VariableEditItem_vue__);
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    VariableEditItem: __WEBPACK_IMPORTED_MODULE_0__VariableEditItem_vue___default.a
  },

  props: {
    selectedVariables: {
      type: Array,
      required: true
    },
    usedVariables: {
      type: Object,
      required: true
    }
  },

  methods: {
    selectCard: function selectCard(index) {
      for (var ind = 0; ind < this.selectedVariables.length; ind++) {
        if (ind == index) $("#varEditCollapse_" + ind).collapse("toggle");else $("#varEditCollapse_" + ind).collapse("hide");
      }
    }
  }
});

/***/ }),
/* 79 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(80)
/* template */
var __vue_template__ = __webpack_require__(81)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/VariableEditItem.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-02939890", Component.options)
  } else {
    hotAPI.reload("data-v-02939890", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 80 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    variable: {
      type: Object,
      required: true
    },
    indexItem: {
      type: Number,
      required: true
    }
  },

  data: function data() {
    return {
      editing: false
    };
  },


  methods: {
    toggleShow: function toggleShow() {
      this.$emit("toggle", this.indexItem);
    }
  },

  computed: {
    getImage: function getImage() {
      if (this.editing) return "/images/check.svg";else return "/images/pencil.svg";
    }
  }
});

/***/ }),
/* 81 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "card" }, [
    _c(
      "a",
      {
        staticClass: "card-header collapsed",
        attrs: {
          href: "#",
          id: "varEditCollapseHeader_" + _vm.indexItem,
          "data-parent": "#accVariablesEdit",
          "aria-expanded": "false",
          "aria-controls": "varEditCollapse_" + _vm.indexItem
        },
        on: { click: _vm.toggleShow }
      },
      [
        _c("h6", { staticClass: "mb-0" }, [
          _vm._v("\n            " + _vm._s(_vm.variable.title) + "\n        ")
        ])
      ]
    ),
    _vm._v(" "),
    _c(
      "div",
      {
        staticClass: "collapse",
        attrs: {
          id: "varEditCollapse_" + _vm.indexItem,
          "aria-labelledby": "#varEditCollapseHeader_" + _vm.indexItem
        }
      },
      [
        _c("div", { staticClass: "card-body" }, [
          _c("form", { attrs: { onsubmit: "return false" } }, [
            _vm.editing
              ? _c("div", [
                  _c("div", { staticClass: "form-group" }, [
                    _c(
                      "label",
                      { attrs: { for: "titleVar_" + _vm.indexItem } },
                      [_vm._v("Title: ")]
                    ),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.variable.title,
                          expression: "variable.title"
                        }
                      ],
                      staticClass: "form-control",
                      attrs: {
                        type: "text",
                        name: "",
                        id: "titleVar_" + _vm.indexItem,
                        placeholder: "Title"
                      },
                      domProps: { value: _vm.variable.title },
                      on: {
                        input: function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(_vm.variable, "title", $event.target.value)
                        }
                      }
                    }),
                    _vm._v(" "),
                    _c(
                      "small",
                      {
                        staticClass: "form-text text-muted",
                        attrs: { id: "titleVarHelp_" + _vm.indexItem }
                      },
                      [_vm._v("Title of the variable")]
                    )
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "form-group" }, [
                    _c(
                      "label",
                      { attrs: { for: "descriptionVar_" + _vm.indexItem } },
                      [_vm._v("Description: ")]
                    ),
                    _vm._v(" "),
                    _c("textarea", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.variable.description,
                          expression: "variable.description"
                        }
                      ],
                      staticClass: "form-control",
                      attrs: {
                        id: "descriptionVar_" + _vm.indexItem,
                        cols: "30",
                        rows: "3"
                      },
                      domProps: { value: _vm.variable.description },
                      on: {
                        input: function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(
                            _vm.variable,
                            "description",
                            $event.target.value
                          )
                        }
                      }
                    }),
                    _vm._v(" "),
                    _c(
                      "small",
                      {
                        staticClass: "form-text text-muted",
                        attrs: { id: "descriptionVarHelp_" + _vm.indexItem }
                      },
                      [_vm._v("Description of the variable")]
                    ),
                    _vm._v(" "),
                    _c("input", {
                      staticClass: "buttonIcon",
                      attrs: { type: "image", src: _vm.getImage, alt: "Edit" },
                      on: {
                        click: function($event) {
                          _vm.editing = !_vm.editing
                        }
                      }
                    })
                  ])
                ])
              : _c("div", [
                  _c("div", { staticClass: "form-group" }, [
                    _c(
                      "label",
                      { attrs: { for: "titleVar_" + _vm.indexItem } },
                      [_vm._v("Title: ")]
                    ),
                    _vm._v(" "),
                    _c("span", [_vm._v(_vm._s(_vm.variable.title))]),
                    _vm._v(" "),
                    _c(
                      "small",
                      {
                        staticClass: "form-text text-muted",
                        attrs: { id: "titleVarHelp_" + _vm.indexItem }
                      },
                      [_vm._v("Title of the variable")]
                    )
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "form-group" }, [
                    _c(
                      "label",
                      { attrs: { for: "descriptionVar_" + _vm.indexItem } },
                      [_vm._v("Description: ")]
                    ),
                    _vm._v(" "),
                    _c("span", [
                      _vm._v(_vm._s(_vm.variable.description) + " ")
                    ]),
                    _vm._v(" "),
                    _c(
                      "small",
                      {
                        staticClass: "form-text text-muted",
                        attrs: { id: "descriptionVarHelp_" + _vm.indexItem }
                      },
                      [_vm._v("Description of the variable")]
                    ),
                    _vm._v(" "),
                    _c("input", {
                      staticClass: "buttonIcon",
                      attrs: { type: "image", src: _vm.getImage, alt: "Edit" },
                      on: {
                        click: function($event) {
                          _vm.editing = !_vm.editing
                        }
                      }
                    })
                  ])
                ])
          ])
        ])
      ]
    )
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-02939890", module.exports)
  }
}

/***/ }),
/* 82 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "accVariablesEdit" } },
    _vm._l(_vm.selectedVariables, function(variable, index) {
      return _c("variable-edit-item", {
        key: index,
        attrs: { "index-item": index, variable: _vm.usedVariables[variable] },
        on: {
          toggle: function($event) {
            _vm.selectCard($event)
          }
        }
      })
    })
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-0c99109b", module.exports)
  }
}

/***/ }),
/* 83 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(84)
/* template */
var __vue_template__ = __webpack_require__(88)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/RuleEditList.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-424c86ca", Component.options)
  } else {
    hotAPI.reload("data-v-424c86ca", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 84 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__RuleEditItem_vue__ = __webpack_require__(85);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__RuleEditItem_vue___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__RuleEditItem_vue__);
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    RuleEditItem: __WEBPACK_IMPORTED_MODULE_0__RuleEditItem_vue___default.a
  },

  props: {
    rules: {
      type: Array,
      required: true
    },
    children: {
      type: Array,
      required: true
    }
  },

  methods: {
    selectCard: function selectCard(index) {
      for (var ind = 0; ind < this.rules.length; ind++) {
        if (ind == index) $("#ruleEditCollapse_" + ind).collapse("toggle");else $("#ruleEditCollapse_" + ind).collapse("hide");
      }
    }
  }
});

/***/ }),
/* 85 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(1)
/* script */
var __vue_script__ = __webpack_require__(86)
/* template */
var __vue_template__ = __webpack_require__(87)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/components/RuleEditItem.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-565776e0", Component.options)
  } else {
    hotAPI.reload("data-v-565776e0", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 86 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    rule: {
      type: Object,
      required: true
    },
    indexItem: {
      type: Number,
      required: true
    },
    options: {
      type: Array,
      required: true
    }
  },

  data: function data() {
    return {
      editing: false
    };
  },


  methods: {
    toggleShow: function toggleShow() {
      this.$emit("toggle", this.indexItem);
    }
  },

  computed: {
    getImage: function getImage() {
      if (this.editing) return "/images/check.svg";else return "/images/pencil.svg";
    }
  }
});

/***/ }),
/* 87 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "card" }, [
    _c(
      "a",
      {
        staticClass: "card-header collapsed",
        attrs: {
          href: "#",
          id: "ruleEditCollapseHeader_" + _vm.indexItem,
          "data-parent": "#accRulesEdit",
          "aria-expanded": "false",
          "aria-controls": "ruleEditCollapse_" + _vm.indexItem
        },
        on: { click: _vm.toggleShow }
      },
      [
        _c("h6", { staticClass: "mb-0" }, [
          _vm._v("\n            " + _vm._s(_vm.rule.title) + "\n        ")
        ])
      ]
    ),
    _vm._v(" "),
    _c(
      "div",
      {
        staticClass: "collapse",
        attrs: {
          id: "ruleEditCollapse_" + _vm.indexItem,
          "aria-labelledby": "#ruleEditCollapseHeader_" + _vm.indexItem
        }
      },
      [
        _c("div", { staticClass: "card-body" }, [
          _c("form", { attrs: { onsubmit: "return false" } }, [
            _c("div", { staticClass: "form-group" }, [
              _c("label", { attrs: { for: "titleRule_" + _vm.indexItem } }, [
                _vm._v("Title: ")
              ]),
              _vm._v(" "),
              _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.rule.title,
                    expression: "rule.title"
                  }
                ],
                staticClass: "form-control",
                attrs: {
                  type: "text",
                  name: "",
                  id: "titleRule_" + _vm.indexItem,
                  placeholder: "Title",
                  disabled: !_vm.editing
                },
                domProps: { value: _vm.rule.title },
                on: {
                  input: function($event) {
                    if ($event.target.composing) {
                      return
                    }
                    _vm.$set(_vm.rule, "title", $event.target.value)
                  }
                }
              }),
              _vm._v(" "),
              _c(
                "small",
                {
                  staticClass: "form-text text-muted",
                  attrs: { id: "titleRuleHelp_" + _vm.indexItem }
                },
                [_vm._v("Title of the variable")]
              )
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "form-group" }, [
              _c(
                "label",
                { attrs: { for: "conditionRule_" + _vm.indexItem } },
                [_vm._v("Condition: ")]
              ),
              _vm._v(" "),
              _c("textarea", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.rule.condition,
                    expression: "rule.condition"
                  }
                ],
                staticClass: "form-control",
                attrs: {
                  id: "conditionRule_" + _vm.indexItem,
                  cols: "30",
                  rows: "3",
                  disabled: !_vm.editing
                },
                domProps: { value: _vm.rule.condition },
                on: {
                  input: function($event) {
                    if ($event.target.composing) {
                      return
                    }
                    _vm.$set(_vm.rule, "condition", $event.target.value)
                  }
                }
              }),
              _vm._v(" "),
              _c(
                "small",
                {
                  staticClass: "form-text text-muted",
                  attrs: { id: "descriptionVarHelp_" + _vm.indexItem }
                },
                [_vm._v("Condition of the rule")]
              ),
              _vm._v(" "),
              _c("input", {
                staticClass: "buttonIcon",
                attrs: { type: "image", src: _vm.getImage, alt: "Edit" },
                on: {
                  click: function($event) {
                    _vm.editing = !_vm.editing
                  }
                }
              })
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "form-group" },
              [
                _c("label", { staticClass: "typo__label" }, [
                  _vm._v("Custom option template")
                ]),
                _vm._v(" "),
                _c("vue-multiselect", {
                  attrs: {
                    label: "title",
                    "track-by": "ind",
                    options: _vm.options,
                    "option-height": 44,
                    "show-labels": false,
                    "preselect-first": "",
                    "allow-empty": false
                  },
                  scopedSlots: _vm._u([
                    {
                      key: "singleLabel",
                      fn: function(props) {
                        return [
                          _c("div", { staticClass: "container-fluid" }, [
                            _c("div", { staticClass: "row" }, [
                              _c("div", { staticClass: "col" }, [
                                _c(
                                  "svg",
                                  {
                                    staticClass: "option__image",
                                    attrs: {
                                      viewBox: "0 0 44 44",
                                      width: "44",
                                      height: "44"
                                    }
                                  },
                                  [
                                    _c("rect", {
                                      style:
                                        "fill:" +
                                        props.option.color +
                                        ";stroke-width:1;stroke:rgb(0,0,0)",
                                      attrs: {
                                        x: "2",
                                        y: "2",
                                        width: "40",
                                        height: "40",
                                        rx: "4",
                                        ry: "4"
                                      }
                                    })
                                  ]
                                )
                              ]),
                              _vm._v(" "),
                              _c("div", { staticClass: "col option__desc" }, [
                                _c("span", { staticClass: "option__title" }, [
                                  _vm._v(_vm._s(props.option.title))
                                ]),
                                _vm._v(" "),
                                _c("span", [_vm._v(_vm._s(props.option.id))])
                              ])
                            ])
                          ])
                        ]
                      }
                    },
                    {
                      key: "option",
                      fn: function(props) {
                        return [
                          _c("div", { staticClass: "container-fluid" }, [
                            _c("div", { staticClass: "row" }, [
                              _c("div", { staticClass: "col" }, [
                                _c(
                                  "svg",
                                  {
                                    staticClass: "option__image",
                                    attrs: {
                                      viewBox: "0 0 44 44",
                                      width: "44",
                                      height: "44"
                                    }
                                  },
                                  [
                                    _c("rect", {
                                      style:
                                        "fill:" +
                                        props.option.color +
                                        ";stroke-width:1;stroke:rgb(0,0,0)",
                                      attrs: {
                                        x: "2",
                                        y: "2",
                                        width: "40",
                                        height: "40",
                                        rx: "4",
                                        ry: "4"
                                      }
                                    })
                                  ]
                                )
                              ]),
                              _vm._v(" "),
                              _c("div", { staticClass: "col option__desc" }, [
                                _c("span", { staticClass: "option__title" }, [
                                  _vm._v(_vm._s(props.option.title))
                                ]),
                                _vm._v(" "),
                                _c("span", [_vm._v(_vm._s(props.option.id))])
                              ])
                            ])
                          ])
                        ]
                      }
                    }
                  ]),
                  model: {
                    value: _vm.rule.target,
                    callback: function($$v) {
                      _vm.$set(_vm.rule, "target", $$v)
                    },
                    expression: "rule.target"
                  }
                })
              ],
              1
            )
          ])
        ])
      ]
    )
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-565776e0", module.exports)
  }
}

/***/ }),
/* 88 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { attrs: { id: "accRulesEdit" } },
    _vm._l(_vm.rules, function(rule, index) {
      return _c("rule-edit-item", {
        key: index,
        attrs: { "index-item": index, rule: rule, options: _vm.children },
        on: {
          toggle: function($event) {
            _vm.selectCard($event)
          }
        }
      })
    })
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-424c86ca", module.exports)
  }
}

/***/ }),
/* 89 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "modal fade",
      attrs: {
        id: "modalStep",
        tabindex: "-1",
        role: "dialog",
        "aria-labelledby": "modalStepOptions",
        "aria-hidden": "true"
      }
    },
    [
      _c(
        "div",
        { staticClass: "modal-dialog modal-lg", attrs: { role: "document" } },
        [
          _c("div", { staticClass: "modal-content" }, [
            _vm._m(0),
            _vm._v(" "),
            _c("div", { staticClass: "modal-body" }, [
              _c("div", { staticClass: "container-fluid" }, [
                _c("div", { staticClass: "row" }, [
                  _c("div", { staticClass: "col-md-4" }, [
                    _c("label", { attrs: { for: "colorPick" } }, [
                      _vm._v("Pick a color:")
                    ]),
                    _vm._v(" "),
                    _c(
                      "button",
                      {
                        staticClass:
                          "btn btn-colorpick dropdown-toggle outline",
                        style: { "background-color": _vm.localStep.colour },
                        attrs: {
                          id: "colorPick",
                          type: "button",
                          "data-toggle": "dropdown"
                        }
                      },
                      [_vm._v(_vm._s(_vm.localStep.id))]
                    ),
                    _vm._v(" "),
                    _vm._m(1),
                    _vm._v(" "),
                    _c("div", { staticClass: "form-group" }, [
                      _c("label", { attrs: { for: "stepType" } }, [
                        _vm._v("Select step-type:")
                      ]),
                      _vm._v(" "),
                      _c(
                        "select",
                        {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.localStep.type,
                              expression: "localStep.type"
                            }
                          ],
                          staticClass: "custom-select",
                          attrs: {
                            name: "stepType",
                            id: "stepType",
                            disabled: _vm.stepId == 0
                          },
                          on: {
                            change: function($event) {
                              var $$selectedVal = Array.prototype.filter
                                .call($event.target.options, function(o) {
                                  return o.selected
                                })
                                .map(function(o) {
                                  var val = "_value" in o ? o._value : o.value
                                  return val
                                })
                              _vm.$set(
                                _vm.localStep,
                                "type",
                                $event.target.multiple
                                  ? $$selectedVal
                                  : $$selectedVal[0]
                              )
                            }
                          }
                        },
                        [
                          _c("option", { attrs: { value: "input" } }, [
                            _vm._v("Input")
                          ]),
                          _vm._v(" "),
                          _c("option", { attrs: { value: "result" } }, [
                            _vm._v("Result")
                          ])
                        ]
                      )
                    ])
                  ]),
                  _vm._v(" "),
                  _vm.localStep.type == "input"
                    ? _c(
                        "div",
                        { staticClass: "col-md-8" },
                        [
                          _c("vue-multiselect", {
                            attrs: {
                              options: _vm.possibleVariables,
                              multiple: true,
                              "group-values": "variables",
                              "group-label": "title",
                              "group-select": true,
                              "close-on-select": false,
                              "clear-on-select": false,
                              label: "title",
                              "track-by": "id",
                              limit: 3,
                              "limit-text": _vm.multiselectVariablesText,
                              "preserve-search": true,
                              placeholder: "Choose variables"
                            },
                            on: {
                              remove: _vm.multiRemoveVariables,
                              select: _vm.multiSelectVariables
                            },
                            scopedSlots: _vm._u([
                              {
                                key: "tag",
                                fn: function(props) {
                                  return [
                                    _c(
                                      "span",
                                      {
                                        staticClass:
                                          "badge badge-info badge-larger"
                                      },
                                      [
                                        _c(
                                          "span",
                                          { staticClass: "badge-maxwidth" },
                                          [_vm._v(_vm._s(props.option.title))]
                                        ),
                                        _vm._v(
                                          " \n                                        "
                                        ),
                                        _c(
                                          "span",
                                          {
                                            staticClass: "custom__remove",
                                            on: {
                                              click: function($event) {
                                                props.remove(props.option)
                                              }
                                            }
                                          },
                                          [_vm._v("❌")]
                                        )
                                      ]
                                    )
                                  ]
                                }
                              }
                            ]),
                            model: {
                              value: _vm.multiSelectedVariables,
                              callback: function($$v) {
                                _vm.multiSelectedVariables = $$v
                              },
                              expression: "multiSelectedVariables"
                            }
                          })
                        ],
                        1
                      )
                    : _vm._e(),
                  _vm._v(" "),
                  _vm.localStep.type == "result"
                    ? _c("div", { staticClass: "col-md-8" })
                    : _vm._e()
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "row" }, [
                  _c("div", { staticClass: "col" }, [
                    _vm.localStep.type == "input"
                      ? _c("div", { staticClass: "card" }, [
                          _vm._m(2),
                          _vm._v(" "),
                          _c(
                            "div",
                            {
                              staticClass: "card-body",
                              attrs: { id: "modalCard" }
                            },
                            [
                              _c(
                                "div",
                                {
                                  staticClass: "tab-content",
                                  attrs: { id: "nav-tabContent-modal" }
                                },
                                [
                                  _c(
                                    "div",
                                    {
                                      staticClass: "tab-pane fade show active",
                                      attrs: {
                                        id: "nav-variables",
                                        role: "tabpanel",
                                        "aria-labelledby": "nav-variables-tab"
                                      }
                                    },
                                    [
                                      _c("variable-edit-list", {
                                        attrs: {
                                          "selected-variables":
                                            _vm.localStep.variables,
                                          "used-variables":
                                            _vm.localUsedVariables
                                        }
                                      })
                                    ],
                                    1
                                  ),
                                  _vm._v(" "),
                                  _c(
                                    "div",
                                    {
                                      staticClass: "tab-pane fade",
                                      attrs: {
                                        id: "nav-logic",
                                        role: "tabpanel",
                                        "aria-labelledby": "nav-logic-tab"
                                      }
                                    },
                                    [
                                      _c("rule-edit-list", {
                                        attrs: {
                                          rules: _vm.localStep.rules,
                                          children: _vm.childNodes
                                        }
                                      })
                                    ],
                                    1
                                  ),
                                  _vm._v(" "),
                                  _c("div", {
                                    staticClass: "tab-pane fade",
                                    attrs: {
                                      id: "nav-api",
                                      role: "tabpanel",
                                      "aria-labelledby": "nav-api-tab"
                                    }
                                  })
                                ]
                              )
                            ]
                          )
                        ])
                      : _vm._e(),
                    _vm._v(" "),
                    _vm.localStep.type == "result"
                      ? _c(
                          "div",
                          {
                            staticClass: "card",
                            attrs: { id: "outputOptionsMenu" }
                          },
                          [_vm._m(3)]
                        )
                      : _vm._e()
                  ])
                ])
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "modal-footer" }, [
              _c(
                "button",
                {
                  staticClass: "btn btn-secondary",
                  attrs: { type: "button", "data-dismiss": "modal" }
                },
                [_vm._v("Cancel")]
              ),
              _vm._v(" "),
              _c(
                "button",
                {
                  staticClass: "btn btn-primary",
                  attrs: { type: "button", "data-dismiss": "modal" },
                  on: { click: _vm.apply }
                },
                [_vm._v("Apply Changes")]
              )
            ])
          ])
        ]
      )
    ]
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "modal-header" }, [
      _c("h4", { staticClass: "modal-title", attrs: { id: "modelTitleId" } }, [
        _vm._v("Step Options")
      ]),
      _vm._v(" "),
      _c(
        "button",
        {
          staticClass: "close",
          attrs: {
            type: "button",
            "data-dismiss": "modal",
            "aria-label": "Close"
          }
        },
        [_c("span", { attrs: { "aria-hidden": "true" } }, [_vm._v("×")])]
      )
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("ul", { staticClass: "dropdown-menu" }, [
      _c("li", [_c("div", { attrs: { id: "colorPalette" } })])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "card-header" }, [
      _c("nav", [
        _c(
          "div",
          {
            staticClass: "nav nav-tabs card-header-tabs nav-scroll",
            attrs: { id: "nav-tab-modal", role: "tablist" }
          },
          [
            _c(
              "a",
              {
                staticClass: "nav-item nav-link active",
                attrs: {
                  id: "nav-variables-tab",
                  "data-toggle": "tab",
                  href: "#nav-variables",
                  role: "tab",
                  "aria-controls": "nav-variables",
                  "aria-selected": "true"
                }
              },
              [_vm._v("Variables")]
            ),
            _vm._v(" "),
            _c(
              "a",
              {
                staticClass: "nav-item nav-link",
                attrs: {
                  id: "nav-logic-tab",
                  "data-toggle": "tab",
                  href: "#nav-logic",
                  role: "tab",
                  "aria-controls": "nav-logic",
                  "aria-selected": "false"
                }
              },
              [_vm._v("Logic")]
            ),
            _vm._v(" "),
            _c(
              "a",
              {
                staticClass: "nav-item nav-link",
                attrs: {
                  id: "nav-api-tab",
                  "data-toggle": "tab",
                  href: "#nav-api",
                  role: "tab",
                  "aria-controls": "nav-api",
                  "aria-selected": "false"
                }
              },
              [_vm._v("Model calculation")]
            )
          ]
        )
      ])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c(
      "div",
      { staticClass: "row vdivide", attrs: { id: "outputCategories" } },
      [
        _c(
          "div",
          { staticClass: "col-sm-6", attrs: { id: "outputTypeLeft" } },
          [
            _c("div", { attrs: { id: "outputCategoriesAccordion" } }, [
              _c("div", { staticClass: "card" }, [
                _c("div", { staticClass: "card-header" }, [
                  _c(
                    "a",
                    {
                      staticClass: "card-link",
                      attrs: { "data-toggle": "collapse", href: "#collapseOne" }
                    },
                    [
                      _vm._v(
                        "\n                                                        Pie Chart\n                                                    "
                      )
                    ]
                  )
                ]),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass: "collapse show",
                    attrs: {
                      id: "collapseOne",
                      "data-parent": "#outputCategoriesAccordion"
                    }
                  },
                  [
                    _c("div", { staticClass: "card-body" }, [
                      _vm._v(
                        "\n                                                        Lorem ipsum..\n                                                    "
                      )
                    ])
                  ]
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "card" }, [
                _c("div", { staticClass: "card-header" }, [
                  _c(
                    "a",
                    {
                      staticClass: "collapsed card-link",
                      attrs: { "data-toggle": "collapse", href: "#collapseTwo" }
                    },
                    [
                      _vm._v(
                        "\n                                                        Bar Plot\n                                                    "
                      )
                    ]
                  )
                ]),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass: "collapse",
                    attrs: {
                      id: "collapseTwo",
                      "data-parent": "#outputCategoriesAccordion"
                    }
                  },
                  [
                    _c("div", { staticClass: "card-body" }, [
                      _vm._v(
                        "\n                                                        Lorem ipsum dolor sit amet, adhuc temporibus concludaturque nec et, cu nostrud euismod dissentias mel. Te nec vidisse persius\n                                                        referrentur. Ad ius semper iuvaret, albucius placerat mea ad.\n                                                        Agam appetere quo te, ad nusquam suavitate reformidans pri. Pri\n                                                        viderer nominavi an, eu solet labores deserunt vim, te diceret\n                                                        adipiscing liberavisse qui. Eos in viris tacimates periculis,\n                                                        in pri consequat theophrastus, amet accusamus duo in. Aperiri\n                                                        verterem per et, augue congue cu vis. Ne inani erroribus cum.\n                                                        Essent tritani insolens eu pri. Ei dolore mucius detraxit sea,\n                                                        vide liber ne est. Cu tation aliquip quaestio cum, per ad aeterno\n                                                        patrioque intellegam. Te sit minimum albucius. Ad scripta consulatu\n                                                        vim, cu case laudem partem vix. Ei eos consul inimicus, ius id\n                                                        blandit deseruisse. Est purto idque ea, per cu eripuit saperet\n                                                        consetetur. Id vim error nihil noster, in illud oblique sententiae\n                                                        nec. Eu velit laudem nec, at tacimates imperdiet nec. Ei prima\n                                                        aperiri legendos duo, ut rebum ullamcorper deterruisset his.\n                                                        Vel eu feugiat salutatus, at ipsum aeterno reprehendunt sit.\n                                                        Te dicam suscipit percipitur vel, in quo nulla graecis necessitatibus,\n                                                        alia tollit placerat ut mel. Nominavi invidunt ut vel, copiosae\n                                                        scribentur his cu. At eos vero noster. Ius vitae everti an, pro\n                                                        eu dicunt convenire splendide. Vim natum illum signiferumque\n                                                        et, numquam petentium per id. No duo adolescens vituperatoribus,\n                                                        luptatum reprehendunt te quo. Erat impedit quo ut, sed dicant\n                                                        omnesque an. Mel inani vitae omnesque ex, expetendis delicatissimi\n                                                        conclusionemque in vel.\n                                                    "
                      )
                    ])
                  ]
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "card" }, [
                _c("div", { staticClass: "card-header" }, [
                  _c(
                    "a",
                    {
                      staticClass: "collapsed card-link",
                      attrs: {
                        "data-toggle": "collapse",
                        href: "#collapseThree"
                      }
                    },
                    [
                      _vm._v(
                        "\n                                                        Doughnut chart\n                                                    "
                      )
                    ]
                  )
                ]),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass: "collapse",
                    attrs: {
                      id: "collapseThree",
                      "data-parent": "#outputCategoriesAccordion"
                    }
                  },
                  [
                    _c("div", { staticClass: "card-body" }, [
                      _vm._v(
                        "\n                                                        Lorem ipsum..\n                                                    "
                      )
                    ])
                  ]
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "card" }, [
                _c("div", { staticClass: "card-header" }, [
                  _c(
                    "a",
                    {
                      staticClass: "collapsed card-link",
                      attrs: {
                        "data-toggle": "collapse",
                        href: "#collapseFour"
                      }
                    },
                    [
                      _vm._v(
                        "\n                                                        Polar area Chart\n                                                    "
                      )
                    ]
                  )
                ]),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass: "collapse",
                    attrs: {
                      id: "collapseFour",
                      "data-parent": "#outputCategoriesAccordion"
                    }
                  },
                  [
                    _c("div", { staticClass: "card-body" }, [
                      _vm._v(
                        "\n                                                        Lorem ipsum..\n                                                    "
                      )
                    ])
                  ]
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "card" }, [
                _c("div", { staticClass: "card-header" }, [
                  _c(
                    "a",
                    {
                      staticClass: "collapsed card-link",
                      attrs: {
                        "data-toggle": "collapse",
                        href: "#collapseFive"
                      }
                    },
                    [
                      _vm._v(
                        "\n                                                        Smiley Faces\n                                                    "
                      )
                    ]
                  )
                ]),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass: "collapse",
                    attrs: {
                      id: "collapseFive",
                      "data-parent": "#outputCategoriesAccordion"
                    }
                  },
                  [
                    _c("div", { staticClass: "card-body" }, [
                      _vm._v(
                        "\n                                                        Lorem ipsum..\n                                                    "
                      )
                    ])
                  ]
                )
              ])
            ])
          ]
        ),
        _vm._v(" "),
        _c(
          "div",
          { staticClass: "col-sm-6", attrs: { id: "outputTypeRight" } },
          [
            _vm._v(
              "\n                                        TODO: Preview..\n                                    "
            )
          ]
        )
      ]
    )
  }
]
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-682a3b1c", module.exports)
  }
}

/***/ })
/******/ ]);
//# sourceMappingURL=designer.js.map