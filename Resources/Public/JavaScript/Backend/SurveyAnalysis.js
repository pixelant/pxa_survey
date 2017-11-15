/**
 * Survey analisys
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
			chartBarPrefix: 'chart-bar-',
			chartPiePrefix: 'chart-pie-'
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
				console.log(data);
				for (var questionID in data) {
					if (!data.hasOwnProperty(questionID) || data[questionID].allAnswersCount <= 0) {
						continue;
					}

					var chart = document.getElementById(_getFromStaticData('chartBarPrefix') + questionID);
					if (chart !== null) {
						var ctx = chart.getContext('2d');

						var chartInstance = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: _getChartPropertyData(data[questionID].questionData, 'label'),
								datasets: [{
									label: data[questionID].labelChart,
									backgroundColor: palette('tol', _size(data[questionID].questionData)).map(function (hex) {
										return '#' + hex;
									}),
									/*borderColor: data.map(function (item) {
										return _intToRgb(item, '1');
									}),*/
									data: _getChartPropertyData(data[questionID].questionData, 'percents')
								}]
							},

							// Configuration options go here
							options: {
								scales: {
									yAxes: [{
										ticks: {
											min: 0,
											max: 100,
											callback: function(value){return value+ "%"}
										},
										scaleLabel: {
											display: true,
											labelString: "Percentage"
										}
									}]
								}
							}
						});
					}
				}
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