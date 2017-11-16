/**
 * Survey analysis
 */
define(['jquery', 'TYPO3/CMS/PxaSurvey/Backend/Chart.min'], function ($, Chart) {
	'use strict';

	/**
	 * Return a static method named "getInstance".
	 */
	return (function () {
		/**
		 * @private
		 *
		 * Hold the instance (Singleton Pattern)
		 */
		var _instance = null;

		/**
		 * Some data
		 * @private
		 */
		var _staticData = {
			bar: 'chart-bar-',
			pie: 'chart-pie-'
		};

		/**
		 * Available types
		 *
		 * @type array
		 * @private
		 */
		var _chartTypes = ['bar', 'pie'];

		/**
		 * Default chart options
		 *
		 * @type object
		 * @private
		 */
		var _defaultChartOptions = {
			bar: {
				scales: {
					yAxes: [{
						ticks: {
							min: 0,
							max: 100,
							callback: function (value) {
								return value + '%';
							}
						}
					}]
				}
			}
		};

		/**
		 * Keep charts
		 *
		 * @type object
		 * @private
		 */
		var _chartsInstances = {};

		/**
		 * @public
		 *
		 * @param data
		 * @return object
		 */
		function SurveyAnalysis(data) {

			/**
			 * @public
			 *
			 * Start everything
			 */
			function init() {
				for (var questionID in data) {
					if (!data.hasOwnProperty(questionID) || data[questionID].allAnswersCount <= 0) {
						continue;
					}

					for (var i = 0; i < _chartTypes.length; i++) {
						var type = _chartTypes[i],
							chart = document.getElementById(_getFromStaticData(type) + questionID);

						if (chart !== null) {
							var ctx = chart.getContext('2d');

							_chartsInstances[_getFromStaticData(type) + questionID] = _createChart(ctx, data[questionID], type);
						}
					}
				}
			}

			/**
			 * Create chart
			 *
			 * @param ctx
			 * @param data
			 * @param type
			 * @private
			 */
			function _createChart(ctx, data, type) {
				var colorsRgb = palette('tol', _size(data.questionData)).map(function (hex) {
					var bigint = parseInt(hex, 16);
					var r = (bigint >> 16) & 255;
					var g = (bigint >> 8) & 255;
					var b = bigint & 255;

					return r + ',' + g + ',' + b;
				});

				return new Chart(ctx, {
					type: type,
					data: {
						labels: _getChartPropertyData(data.questionData, 'label'),
						datasets: [{
							label: data.labelChart,
							backgroundColor: colorsRgb.map(function (rgb) {
								return 'rgba(' + rgb + ', 0.2)';
							}),
							borderColor: colorsRgb.map(function (rgb) {
								return 'rgba(' + rgb + ',1)';
							}),
							borderWidth: 1,
							data: _getChartPropertyData(data.questionData, 'percents')
						}]
					},

					// Configuration options go here
					options: _getChartOptions(type)
				});
			}

			/**
			 * Get array data from question data
			 *
			 * @param questionData
			 * @param property
			 * @return {Array}
			 * @private
			 */
			function _getChartPropertyData(questionData, property) {
				var data = [];

				for (var prop in questionData) {
					if (!questionData.hasOwnProperty(prop)) {
						continue;
					}

					data.push(questionData[prop][property] || '');
				}

				return data;
			}

			/**
			 * Options of chart
			 *
			 * @return {Object}
			 * @private
			 */
			function _getChartOptions(type) {
				return _defaultChartOptions[type] || {};
			}

			/**
			 * Get size of object
			 *
			 * @param obj
			 * @return {number}
			 */
			function _size(obj) {
				var size = 0, key;

				for (key in obj) {
					if (obj.hasOwnProperty(key)) size++;
				}

				return size;
			}

			/**
			 * Get from static data by key
			 *
			 * @param key
			 * @return {*|null}
			 * @private
			 */
			function _getFromStaticData(key) {
				return _staticData[key] || null;
			}

			/**
			 * Publish the public methods.
			 */
			return {
				init: init
			};
		}

		/**
		 * Emulation of static methods
		 */
		return {
			/**
			 * @public
			 * @static
			 *
			 * Implement the "Singleton Pattern".
			 *
			 * @param data
			 * @return object
			 */
			getInstance: function (data) {
				if (_instance === null) {
					_instance = new SurveyAnalysis(data);
				}

				return _instance;
			}
		};
	})();
});