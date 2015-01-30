;
(function($) {
	$.fn.multiChart = function(options) {
		var data = options;
		var chartContainer = $(this).attr('id');
		var containerId = '#' + chartContainer;
		var chartType = options.chartType || '';
		$(containerId).empty();
		if (data.hasOwnProperty('isMultiChart')) {
			var index = 1;
			for (var i in data.data) {
				var item = data.data[i];
				var width = item.width || '100%';
				var subId = chartContainer + index++;
				$(containerId).append("<div id='" + subId + "' style='float:left;width:" + width + "'></div>");
				$('#' + subId).createChart({
					chartType: chartType,
					dataFormat: 1,
					categories: item.data.categories,
					series: item.data.series,
					chartOptions: item.data.chartOptions
				});
			}
		} else {
			$(containerId).createChart(data);
		}
	}
	$.fn.createChart = function(options) {
		var defOptions = {
			title: {},
			colors: ['#1bd0dc', '#f9b700', '#eb6100', '#009944', '#eb6877', '#5674b9', '#a98fc2', '#9999ff', '#1c95bd', '#9dd30d'],
			lang: {
				months: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
				shortMonths: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
				weekdays: ['星期天', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
				resetZoom: '查看全图',
				resetZoomTitle: '查看全图',
				downloadPNG: '下载PNG',
				downloadJPEG: '下载JPEG',
				downloadPDF: '下载PDF',
				downloadSVG: '下载SVG',
				exportButtonTitle: '导出成图片',
				printButtonTitle: '打印图表',
				loading: '数据加载中，请稍候...'
			},
			chart: {
				borderWidth: 0,
				marginBottom: 50,
				marginTop: 20,
				marginRight: 20,
				zoomType: 'x',
				selectionMarkerFill: 'rgba(122, 201, 67, 0.25)',
				resetZoomButton: {
					theme: {
						fill: 'white',
						stroke: 'silver',
						r: 0,
						states: {
							hover: {
								fill: '#41739D',
								style: {
									color: 'white'
								}
							}
						}
					}
				}
			},
			xAxis: {
				startOnTick: false,
				lineColor: '#6a7791',
				lineWidth: 1,
				tickPixelInterval: 140,
				tickmarkPlacement: 'on'
			},
			yAxis: {
				title: {
					text: ''
				},
				min: 0,
				gridLineColor: '#eae9e9',
				showFirstLabel: false
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					innerSize: '45%',
					cursor: 'pointer',
					dataLabels: {
						enabled: false,
						color: '#000000',
						connectorColor: '#000000'
					}
				},
				series: {
					fillOpacity: 0.1,
					shadow: false,
					marker: {
						enabled: false,
						radius: 4,
						fillColor: null,
						lineWidth: 2,
						lineColor: '#FFFFFF',
						states: {
							hover: {
								enabled: true
							}
						}
					}
				}
			},
			legend: {
				borderWidth: 0,
				y: 8,
				floating: true,
				align: 'left'
			},
			tooltip: {
				borderColor: '#000000',
				useHTML: true,
				crosshairs: {
					color: '#7ac943',
					dashStyle: 'shortdot'
				},
				shared: true
			}
		}
		var defaults = {
			title: '',
			width: '100%',
			height: 300,
			showLabel: true,
			showMarker: true,
			chartType: 'area',
			dataFormat: 1,
			categories: [],
			series: [],
			yMin: null,
			yMax: null,
			xAxisLabelStep: 0,
			xAxisTickInterval: 0,
			useDefaultStyle: true,
			enableZoom: true,
			autoStep: true,
			chartOptions: {
				chart: {},
				title: {},
				xAxis: {
					categories: '',
					labels: {}
				},
				yAxis: {},
				plotOptions: {
					pie: {},
					series: {}
				},
				tooltip: {}
			}
		};
		options = $.extend(true, defaults, options);
		options.useDefaultStyle && Highcharts.setOptions(defOptions);
		var cOptions = options.chartOptions;
		var defaultChartType = {
			'area': 'area',
			'line': 'line',
			'pie': 'pie',
			'bar': 'bar',
			'spline': 'spline',
			'column': 'column'
		}[options.chartType] || 'area';
		cOptions.chart.type = cOptions.chart.type || defaultChartType;
		cOptions.yAxis.dataFormat = cOptions.yAxis.dataFormat || options.dataFormat;
		cOptions.title.text = options.title;
		if (options.categories) {
			var isDateTime = false;
			var maxLen = 0;
			var index = 0;
			for (var i in options.categories) {
				var cate = options.categories[i].toString();
				maxLen < cate.length && (maxLen = cate.length);
				if (index == 0) {
					var strDate = options.categories[i].toString();
					var ar = strDate.split('-');
					var startDate = Date.UTC(ar[0], ar[1] - 1, ar[2]);
					isDateTime = !isNaN(startDate);
					index++;
				}
			}
			isDateTime = false;
			if (!isDateTime) {
				var width = maxLen * 6 + 50;
				cOptions.xAxis.categories = toHighChartCategories(options.categories);
				if (options.autoStep) {
					var interval = cOptions.xAxis.tickInterval || 1;
					cOptions.xAxis.labels.step = Math.ceil(cOptions.xAxis.categories.length / ($(this).css('width').replace(/[^\d\.]/g, '') / width) / interval);
				}
			} else {
				var oneDay = 24 * 3600 * 1000;
				cOptions.plotOptions.series.pointStart = startDate;
				cOptions.plotOptions.series.pointInterval = oneDay;
				cOptions.xAxis.type = 'datetime';
				cOptions.xAxis.maxZoom = 7 * oneDay;
				cOptions.xAxis.labels = cOptions.xAxis.labels || {};
				cOptions.xAxis.labels.formatter = cOptions.xAxis.labels.formatter || function() {
					var d = new Date(this.value);
					var result = isNaN(d) ? this.value : d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
					return result;
				};
			}
			cOptions.series = toHighChartSeries(options.series);
		} else {
			cOptions.chart.type = 'pie';
			cOptions.series = [options.series];
			cOptions.tooltip = cOptions.tooltip || {};
			cOptions.tooltip.shared = false;
			cOptions.tooltip.useHTML = false;
			cOptions.tooltip.formatter = function() {
				return '<b>' + this.point.name + '</b>: ' + Math.round(this.percentage * 100) / 100 + ' %';
			};
		}
		cOptions.chart.renderTo = $(this).attr('id');
		if (options.yMin != null && options.yMax != null) {
			cOptions.yAxis.min = options.yMin;
			cOptions.yAxis.max = options.yMax;
		}
		cOptions.yAxis.labels = cOptions.yAxis.labels || {};
		if (cOptions.yAxis.dataFormat == 3) {
			cOptions.tooltip.valueSuffix = '%';
		}
		if (cOptions.tooltip.valueSuffix) {
			cOptions.yAxis.labels.formatter = function() {
				var value = (cOptions.yAxis.dataFormat == 5) ? Highcharts.numberFormat(this.value * 100, 0) : this.value;
				return value + cOptions.tooltip.valueSuffix;
			};
		}
		cOptions.tooltip.formatter = cOptions.tooltip.formatter || function() {
			var yName = cOptions.yAxis.name ? ' (' + cOptions.yAxis.name + ')' : '';
			var xName = isDateTime ? toDateDesc(this.x) : this.x;
			var s = '<small>' + xName + yName + '</small><table style="width: 200px">';
			$.each(this.points, function(i, point) {
				var dataFormat = cOptions.yAxis.dataFormat;
				var value = point.y;
				if (dataFormat == 1) {
					value = Highcharts.numberFormat(value, 0);
				} else if (dataFormat == 2 || dataFormat == 3) {
					value = Highcharts.numberFormat(value, 2);
				} else if (dataFormat == 5) {
					value = Highcharts.numberFormat(value * 100, 2);
				} else if (dataFormat == 4) {
					value = toTimeDesc(value);
				}
				var suffix = cOptions.tooltip.valueSuffix || '';
				s += '<tr><td style="color: #000">' + point.series.name + ': </td>' + '<td style="text-align: right"><b>' + value + suffix + ' </b></td></tr>';
			});
			s += '</table>';
			return s;
		};
		if (cOptions.yAxis.dataFormat == 4) {
			var toTimeDesc = function(t) {
				var h = parseInt(t / 3600);
				var m = '00' + parseInt((t % 3600) / 60);
				var s = '00' + parseInt(t % 3600 % 60);
				m = m.substr(m.length - 2, 2);
				s = s.substr(s.length - 2, 2);
				return h + ':' + m + ':' + s;
			};
			cOptions.yAxis.labels.formatter = function() {
				return toTimeDesc(this.value);
			};
		}
		$(this).css('height', options.height);
		var chart = new Highcharts.Chart(cOptions);
	};

	function isDate(obj) {
		var d = new Date(obj);
		return !isNaN(d);
	}

	function toDateDesc(obj) {
		var d = new Date(obj);
		var result = isNaN(d) ? obj : d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
		return result;
	}

	function toHighChartCategories(categories) {
		var hcCagetories = [];
		for (var c in categories) {
			hcCagetories.push(categories[c]);
		}
		return hcCagetories;
	}

	function toHighChartSeries(series) {
		var hcSeries = [];
		var hcSer;
		if ($.isArray(series)) {
			for (var i in series) {
				var ser = series[i];
				hcSer = toSeriesItem(ser);
				hcSeries.push(hcSer);
			}
		} else {
			hcSer = toSeriesItem(series);
			hcSeries.push(hcSer);
		}
		return hcSeries;
	}

	function toSeriesItem(ser) {
		if (!ser) {
			return {
				name: ' ',
				data: []
			};
		}
		var hcSer = {
			name: ser.name || '',
			data: []
		};
		var serData = ser.data || [];
		var hcData = [];
		for (var j in serData) {
			var point = serData[j];
			var hcPoint;
			hcPoint = point;
			hcData.push(hcPoint);
		}
		hcSer.data = hcData;
		return hcSer;
	}
})(jQuery); /*  |xGv00|f7012bee4b26b2e11e5356fb8f994ed8 */