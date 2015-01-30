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
			e = c('<div><span style="margin-right: 5px;padding: 5px 12px;">共'+recordNum+'条记录 当前第'+i+'页/共'+h+'页</span></div>');
		}else {
			e = c('<div></div>');
		}
		
		if (h > 1) {
			if (isNaN(g)) {
				e.append(a("first", i, h, m, p));
			}
			e.append(a("prev", i, h, m, p));
			var d = 1;
			var k = 9;
			if (i > 4) {
				d = i - 4;
				k = i + 4;
			}
			if (k > h) {
				d = h - 8;
				k = h;
			}
			if (d < 1) {
				d = 1;
			}
			for (var j = d;
			j <= k; j++) {
				var l = c('<a class="page-number">' + (j) + "</a>");
				j == i ? l.addClass("active") : l.click(function() {
					m(this.firstChild.data);
				});
				l.appendTo(e);
			}
			e.append(a("next", i, h, m, p));
			if (isNaN(f)) {
				e.append(a("last", i, h, m, p));
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
		var e = c('<a class="pgNext">' + g + "</a>");
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
