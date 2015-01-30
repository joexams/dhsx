(function(c) {
	c.fn.pager = function(d) {
		var e = c.extend({}, c.fn.pager.defaults, d);
		return this.each(function() {
			c(this).empty().append(b(parseInt(d.pagenumber), parseInt(d.pagecount), parseInt(d.showfirst), parseInt(d.showlast), d.buttonClickCallback, d.word));
			c(".pagination a").mouseover(function() {
				//document.body.style.cursor = "pointer";
			}).mouseout(function() {
				//document.body.style.cursor = "auto";
			});
		});
	};

	function b(i, h, g, f, m, p, q) {
		var e = '';
		if (!isNaN(h) && h > 1){
			e = c('<div class="pg"><span>共'+Ha.page.recordNum+'条记录</span></div>');
		}else {
			e = c('<div class="pg"></div>');
		}
		
		if (h > 1) {
			if (i != 1) {
				// if (isNaN(g)) {
				// 	e.append(a("first", i, h, m, p));
				// }
				e.append(a("prev", i, h, m, p));
			}
			var d = 1;
			var k = 5;
			if (i > 2) {
				d = i - 2;
				k = i + 2;
			}
			if (k > h) {
				d = h - 4;
				k = h;
			}
			if (d < 1) {
				d = 1;
			}

			if (d > 1) {
				c('<a href="javascript:;">1</a>').click(function(){m(this.firstChild.data);}).appendTo(e);
				c('<span>...</span>').appendTo(e);
			}

			for (var j = d; j <= k; j++) {
				var l = c('<a href="javascript:;">' + (j) + "</a>");
				if (j == i) {
					l = c('<strong class="current">' + (j) + "</strong>");
				}else {
					l.click(function() {
						m(this.firstChild.data);
					});
				}
				l.appendTo(e);
			}
			if (h > k) {
				c('<span>...</span>').appendTo(e);
				c('<a href="javascript:;">'+(h)+'</a>').click(function(){m(this.firstChild.data);}).appendTo(e);
			}
			if (i != h) {
				e.append(a("next", i, h, m, p));
				// if (isNaN(f)) {
				// 	e.append(a("last", i, h, m, p));
				// }
			}
		}
		return e;
	}
	function a(i, d, h, f, w) {
		var j = 1,
			g = "";
		switch (i) {
		case "first":
			j = 1;
			g = w.first;
			break;
		case "prev":
			j = d - 1;
			g = w.prev;
			break;
		case "next":
			j = d + 1;
			g = w.next;
			break;
		case "last":
			j = h;
			g = w.last;
			break;
		}
		var e = c('<a href="javascript:;">' + g + "</a>");
		if (i == "first" || i == "prev") {
			d <= 1 ? e.addClass("pgEmpty") : e.click(function() {
				f(j);
			});
		} else {
			d >= h ? e.addClass("pgEmpty") : e.click(function() {
				f(j);
			});
		}
		return e;
	}
	c.fn.pager.defaults = {
		pagenumber: 1,
		pagecount: 1,
		showfirst: 1,
		showlast: 1,
		word: {first: 'First', prev: 'Prev', next: 'Next', last: 'Last'}
	};
})(jQuery);
