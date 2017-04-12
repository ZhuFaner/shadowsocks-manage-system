'use strict';

(function(definition) {
	if (typeof define === 'function' && define.amd) {
		define(['QRCode'], definition);
	} else if (typeof module === 'object' && module.exports) {
		var QRCode = require('qrjs');
		module.exports = definition(QRCode);
	} else {
		definition(window.QRCode);
	}
})(function(QRCode) {
//
// Prototype
//
var proto = Object.create(HTMLElement.prototype, {
    //
    // Attributes
    //
    attrs: {
        value: {
            data: null,
            format: 'png',
            modulesize: 5,
            margin: 4
        }
    },
    defineAttributes: {
        value: function () {
            var attrs = Object.keys(this.attrs),
                attr;
            for (var i=0; i<attrs.length; i++) {
                attr = attrs[i];
                (function (attr) {
                    Object.defineProperty(this, attr, {
                        get: function () {
                            var value = this.getAttribute(attr);
                            return value === null ? this.attrs[attr] : value;
                        },
                        set: function (value) {
                            this.setAttribute(attr, value);
                        }
                    });
                }.bind(this))(attr);
            }
        }
    },
    //
    // LifeCycle Callbacks
    //
    createdCallback: {
        value: function () {
            this.createShadowRoot();
            this.defineAttributes();
            this.generate();
        }
    },
    attributeChangedCallback: {
        value: function (attrName, oldVal, newVal) {
            var fn = this[attrName+'Changed'];
            if (fn && typeof fn === 'function') {
                fn.call(this, oldVal, newVal);
            }
            this.generate();
        }
    },
    //
    // Methods
    //
    getOptions: {
    	value: function () {
            var modulesize = this.modulesize,
                margin = this.margin;
            return {
                modulesize: modulesize !== null ? parseInt(modulesize) : modulesize,
                margin: margin !== null ? parseInt(margin) : margin
            };
    	}
    },
    generate: {
        value: function () {
            if (this.data !== null) {
                if (this.format === 'png') {
                    this.generatePNG();
                }
                else if (this.format === 'html') {
                    this.generateHTML();
                }
                else if (this.format === 'svg') {
                    this.generateSVG();
                }
                else {
                    this.shadowRoot.innerHTML = '<div>qr-code: '+ this.format +' not supported!</div>'
                }
            }
            else {
                this.shadowRoot.innerHTML = '<div>qr-code: no data!</div>'
            }
        }
    },
    generatePNG: {
        value: function () {
            try {
                var img = document.createElement('img');
                img.src = QRCode.generatePNG(this.data, this.getOptions());
                this.clear();
                this.shadowRoot.appendChild(img);
            }
            catch (e) {
                this.shadowRoot.innerHTML = '<div>qr-code: no canvas support!</div>'
            }
        }
    },
    generateHTML: {
        value: function () {
            var div = QRCode.generateHTML(this.data, this.getOptions());
            this.clear();
            this.shadowRoot.appendChild(div);
        }
    },
    generateSVG: {
        value: function () {
            var div = QRCode.generateSVG(this.data, this.getOptions());
            this.clear();
            this.shadowRoot.appendChild(div);
        }
    },
    clear: {
        value: function () {
            while (this.shadowRoot.lastChild) {
                this.shadowRoot.removeChild(this.shadowRoot.lastChild);
            }
        }
    }
});
//
// Register
//
document.registerElement('qr-code', {
    prototype: proto
});
});


