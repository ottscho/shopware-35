/**
 * Observer - Observe formelements for changes
 *
 * @version		1.0rc3
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */
var Observer = new Class({

	Implements: [Options, Events],

	options: {
		periodical: false,
		delay: 1000
	},

	initialize: function(el, onFired, options){
		this.setOptions(options);
		this.addEvent('onFired', onFired);
		this.element = $(el) || $$(el);
		this.value = this.element.get('value');
		if (this.options.periodical) this.timer = this.changed.periodical(this.options.periodical, this);
		else this.element.addEvent('keyup', this.changed.bind(this));
	},

	changed: function() {
		var value = this.element.get('value');
		if ($equals(this.value, value)) return;
		this.clear();
		this.value = value;
		this.timeout = this.onFired.delay(this.options.delay, this);
	},

	setValue: function(value) {
		this.value = value;
		this.element.set('value', value);
		return this.clear();
	},

	onFired: function() {
		this.fireEvent('onFired', [this.value, this.element]);
	},

	clear: function() {
		$clear(this.timeout || null);
		return this;
	}

});

var $equals = function(obj1, obj2) {
	return (obj1 == obj2 || JSON.encode(obj1) == JSON.encode(obj2));
};


/**
 * Autocompleter
 *
 * @version		1.1rc1
 *
 * @todo: Caching, no-result handling!
 *
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */
var Autocompleter = {};

Autocompleter.Base = new Class({

	options: {
		minLength: 1,
		markQuery: true,
		choicesWidth: 'inherit',
		maxChoices: 10,
		injectChoice: null,
		customChoices: null,
		className: 'autocompleter-choices',
		zIndex: 42,
		delay: 400,
		observerOptions: {},
		fxOptions: {},
		onOver: $empty,
		onSelect: $empty,
		onSelection: $empty,
		onShow: $empty,
		onHide: $empty,
		onBlur: $empty,
		onFocus: $empty,

		autoSubmit: false,
		overflow: false,
		overflowMargin: 25,
		selectFirst: true,
		filter: null,
		filterCase: false,
		filterSubset: false,
		forceSelect: false,
		selectMode: true,
		choicesMatch: null,

		multiple: false,
		separator: ', ',
		separatorSplit: /\s*[,;]\s*/,
		autoTrim: true,
		allowDupes: false,

		cache: true
	},

	initialize: function(el, options) {
		this.setOptions(options);
		this.element = $(el);
		this.build();
		this.observer = new Observer(this.element, this.prefetch.bind(this), $merge({
			'delay': this.options.delay
		}, this.options.observerOptions));
		this.queryValue = null;
		if (this.options.filter) this.filter = this.options.filter.bind(this);
		var mode = this.options.selectMode;
		this.typeAhead = (mode == 'type-ahead');
		this.selectMode = (mode === true) ? 'selection' : mode;
		this.cached = [];
	},

	/**
	 * build - Initialize DOM
	 *
	 * Builds the html structure for choices and appends the events to the element.
	 * Override this function to modify the html generation.
	 */
	build: function() {
		if ($(this.options.customChoices)) {
			this.choices = this.options.customChoices;
		} else {
			this.choices = new Element('ul', {
				'class': this.options.className,
				'styles': {
					'zIndex': this.options.zIndex
				}
			}).inject(this.element, 'after');
			this.fix = new OverlayFix(this.choices);
			this.relative = this.choices.getOffsetParent();
		}
		if (!this.options.separator.test(this.options.separatorSplit)) {
			this.options.separatorSplit = this.options.separator;
		}
		this.fx = (!this.options.fxOptions) ? null : new Fx.Tween(this.choices, 'opacity', $merge({
			'link': 'cancel',
			'duration': 200
		}, this.options.fxOptions)).addEvent('onStart', Chain.prototype.clearChain).set(0);
		this.element.setProperty('autocomplete', 'off')
			.addEvent((Browser.Engine.trident || Browser.Engine.webkit) ? 'keydown' : 'keypress', this.onCommand.bind(this))
			.addEvent('click', this.onCommand.bind(this, [false]))
			.addEvent('focus', this.toggleFocus.create({bind: this, arguments: true, delay: 100}))
			.addEvent('blur', this.toggleFocus.create({bind: this, arguments: false, delay: 100}))
			.addEvent('trash', this.destroy.bind(this));
	},

	destroy: function() {
		this.choices = this.selected = this.choices.destroy();
	},

	toggleFocus: function(state) {
		this.focussed = state;
		if (!state) this.hideChoices(true);
		this.fireEvent((state) ? 'onFocus' : 'onBlur', [this.element]);
	},

	onCommand: function(e) {
		if (!e && this.focussed) return this.prefetch();
		if (e && e.key && !e.shift) {
			switch (e.key) {
				case 'enter':
					if (this.element.value != this.opted) return true;
					if (this.selected && this.visible) {
						this.choiceSelect(this.selected);
						return !!(this.options.autoSubmit);
					}
					break;
				case 'up': case 'down':
					if (!this.prefetch() && this.queryValue !== null) {
						var up = (e.key == 'up');
						this.choiceOver((this.selected || this.choices)[
							(this.selected) ? ((up) ? 'getPrevious' : 'getNext') : ((up) ? 'getLast' : 'getFirst')
						](this.options.choicesMatch), true);
					}
					return false;
				case 'esc': case 'tab':
					this.hideChoices(true);
					break;
			}
		}
		return true;
	},

	setSelection: function(finish) {
		var input = this.selected.inputValue, value = input;
		var start = this.queryValue.length, end = input.length;
		if (input.substr(0, start).toLowerCase() != this.queryValue.toLowerCase()) start = 0;
		if (this.options.multiple) {
			value = this.element.value, split = this.options.separatorSplit;
			start += this.queryIndex;
			end += this.queryIndex;
			var old = value.substr(this.queryIndex).split(split, 1)[0];
			value = value.substr(0, this.queryIndex) + input + value.substr(this.queryIndex + old.length);
			if (finish) {
				var tokens = value.split(this.options.separatorSplit).clean();
				if (!this.options.allowDupes) tokens = [].merge(tokens);
				var sep = this.options.separator;
				value = tokens.join(sep) + sep;
				end = value.length;
			}
		}
		this.observer.setValue(value);
		this.opted = value;
		if (finish || this.selectMode == 'pick') start = end;
		this.element.selectRange(start, end);
		this.fireEvent('onSelection', [this.element, this.selected, value, input]);
	},

	showChoices: function() {
		var match = this.options.choicesMatch, first = this.choices.getFirst(match);
		this.selected = this.selectedValue = null;
		if (this.relative) {
			var pos = this.element.getCoordinates(this.relative), width = this.options.choicesWidth || 'auto';
			this.choices.setStyles({
				'left': pos.left,
				'top': pos.bottom,
				'width': (width === true || width == 'inherit') ? pos.width : width
			});
		}
		if (!first) return;
		if (!this.visible) {
			this.visible = true;
			if (this.fx) this.fx.start(1);
			this.choices.setStyle('display', '');
			this.fix.show();
			this.fireEvent('onShow', [this.element, this.choices]);
		}
		if (this.options.selectFirst || this.typeAhead || first.inputValue == this.queryValue) this.choiceOver(first, this.typeAhead);
		var items = this.choices.getChildren(match), max = this.options.maxChoices;
		var styles = {'overflowY': 'hidden', 'height': ''};
		if (items.length > max) {
			var item = items[max - 1];
			styles.overflowY = 'scroll';
			styles.height = item.getCoordinates(this.choices).bottom;
		};
		this.choices.setStyles(styles);
	},

	hideChoices: function(clear) {
		if (!this.visible) return;
		this.visible = false;
		if (clear) {
			var value = this.element.value;
			if (this.options.forceSelect) value = this.opted;
			if (this.options.autoTrim) {
				value = value.split(this.options.separatorSplit).clean().join(this.options.separator);
			}
			this.observer.setValue(value);
		}
		this.observer.clear();
		var hide = function(){
			this.choices.setStyle('display', 'none');
			this.fix.hide();
		}.bind(this);
		if (this.fx) this.fx.start(0).chain(hide);
		else hide();
		this.fireEvent('onHide', [this.element, this.choices]);
	},

	prefetch: function() {
		var value = this.element.value, query = value;
		if (this.options.multiple) {
			var split = this.options.separatorSplit;
			var values = value.split(split);
			var index = this.element.getCaretPosition();
			var toIndex = value.substr(0, index).split(split);
			var last = toIndex.length - 1;
			index -= toIndex[last].length;
			query = values[last];
		}
		if (query.length < this.options.minLength) {
			this.hideChoices();
		} else {
			if (query === this.queryValue || (this.visible && query == this.selectedValue)) {
				if (this.visible) return false;
				this.showChoices();
			} else {
				this.queryValue = query;
				this.queryIndex = index;
				if (!this.fetchCached()) this.query();
			}
		}
		return true;
	},

	fetchCached: function() {
		return false;
		if (!this.options.cache
			|| !this.cached
			|| !this.cached.length
			|| this.cached.length >= this.options.maxChoices
			|| this.queryValue) return false;
		this.update(this.filter(this.cached));
		return true;
	},

	update: function(tokens) {
		this.choices.empty();
		this.cached = tokens;
		if (!tokens || !tokens.length) {
			this.hideChoices();
		} else {
			if (this.options.maxChoices < tokens.length && !this.options.overflow) tokens.length = this.options.maxChoices;
			tokens.each(this.options.injectChoice || function(token){
				var choice = new Element('li', {'html': this.markQueryValue(token)});
				choice.inputValue = token;
				this.addChoiceEvents(choice).inject(this.choices);
			}, this);
			this.showChoices();
		}
	},

	choiceOver: function(choice, selection) {
		if (!choice || choice == this.selected) return;
		if (this.selected) this.selected.removeClass('autocompleter-selected');
		this.selected = choice.addClass('autocompleter-selected');
		this.fireEvent('onSelect', [this.element, this.selected, selection]);
		if (!selection) return;
		this.selectedValue = this.selected.inputValue;
		if (this.options.overflow) {
			var coords = this.selected.getCoordinates(this.choices), margin = this.options.overflowMargin,
				top = this.choices.scrollTop, height = this.choices.offsetHeight, bottom = top + height;
			if (coords.top - margin < top && top) this.choices.scrollTop = Math.max(coords.top - margin, 0);
			else if (coords.bottom + margin > bottom) this.choices.scrollTop = Math.min(coords.bottom - height + margin, bottom);
		}
		if (this.selectMode) this.setSelection();
	},

	choiceSelect: function(choice) {
		if (choice) this.choiceOver(choice);
		this.setSelection(true);
		this.queryValue = false;
		this.hideChoices();
	},

	filter: function(tokens) {
		var regex = new RegExp(((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp(), (this.options.filterCase) ? '' : 'i');
		return (tokens || this.tokens).filter(regex.test, regex);
	},

	/**
	 * markQueryValue
	 *
	 * Marks the queried word in the given string with <span class="autocompleter-queried">*</span>
	 * Call this i.e. from your custom parseChoices, same for addChoiceEvents
	 *
	 * @param		{String} Text
	 * @return		{String} Text
	 */
	markQueryValue: function(str) {
		return (!this.options.markQuery || !this.queryValue) ? str
			: str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<span class="autocompleter-queried">$1</span>');
	},

	/**
	 * addChoiceEvents
	 *
	 * Appends the needed event handlers for a choice-entry to the given element.
	 *
	 * @param		{Element} Choice entry
	 * @return		{Element} Choice entry
	 */
	addChoiceEvents: function(el) {
		return el.addEvents({
			'mouseover': this.choiceOver.bind(this, [el]),
			'click': this.choiceSelect.bind(this, [el])
		});
	}
});

Autocompleter.Base.implement(new Events);
Autocompleter.Base.implement(new Options);

Autocompleter.Local = new Class({

	Extends: Autocompleter.Base,

	options: {
		minLength: 0,
		delay: 200
	},

	initialize: function(el, tokens, options) {
		arguments.callee.parent(el, options);
		this.tokens = tokens;
	},

	query: function() {
		this.update(this.filter());
	}

});




var OverlayFix = new Class({

	initialize: function(el) {
		if (Browser.Engine.trident) {
			this.element = $(el).addEvent('trash', this.destroy.bind(this));
			this.relative = this.element.getOffsetParent();
			this.fix = new Element('iframe', {
				'frameborder': '0',
				'scrolling': 'no',
				'src': 'javascript:false;',
				'styles': {
					'position': 'absolute',
					'border': 'none',
					'display': 'none',
					'filter': 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)'
				}
			}).inject(this.element, 'after');
		}
	},

	show: function() {
		if (this.fix) {
			this.fix.setStyles($extend(this.element.getCoordinates(this.relative), {
				'display': '',
				'zIndex': (this.element.getStyle('zIndex') || 1) - 1
			}));
		}
		return this;
	},

	hide: function() {
		if (this.fix) this.fix.setStyle('display', 'none');
		return this;
	},

	destroy: function() {
		this.fix = this.fix.destroy();
	}

});

Element.implement({

	getCaretPosition: function() {
		if (!Browser.Engine.trident) return this.selectionStart;
		this.focus();
		var work = document.selection.createRange();
		var all = this.createTextRange();
		work.setEndPoint('StartToStart', all);
		return work.text.length;
	},

	selectRange: function(start, end) {
		if (Browser.Engine.trident) {
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', end);
			range.moveStart('character', start);
			range.select();
		} else {
			this.focus();
			this.setSelectionRange(start, end);
		}
		return this;
	}

});

