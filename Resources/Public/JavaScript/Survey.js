PxaSurvey = (function () {

	/**
	 * Simulate singleton
	 */
	var _instance = null;

	/**
	 * Init settings
	 *
	 * @param settings
	 * @constructor
	 */
	function PxaSurvey(settings) {
		this._cacheQuestionIds = {};
		this._$additionalAnswerInput = $(settings.additionalAnswerInput);
		this._$progressBar = $(settings.progressBar);
		this._$form = $(settings.form);
	}

	PxaSurvey.prototype = {
		/**
		 * Init events
		 */
		init: function () {
			var self = this;

			// reset radios if needed
			this._$additionalAnswerInput.each(function () {
				var input = $(this);
				if (input.val() !== '') {
					self._cleanAdditionalInputRadios(input);
				}
			});

			this._$additionalAnswerInput.on('focus', function () {
				self._cleanAdditionalInputRadios($(this));
			});

			// on submit update progress bar
			if (this._$progressBar.length === 1) {
				this._$form.on('submit', function () {
					self._updateProgressBar();
				});
			}
		},

		/**
		 * Check values of additional inputs and unset radios if needed
		 *
		 * @private
		 */
		_cleanAdditionalInputRadios: function (input) {
			var questionId = this._determinateQuestionIdFromInput(input);

			if (questionId > 0) {
				$('input[name="tx_pxasurvey_survey[answers][' + questionId + '][answer]"]').prop('checked', false);
			}
		},

		/**
		 * Get question id from id of input
		 *
		 * @param input
		 * @return {Number}
		 * @private
		 */
		_determinateQuestionIdFromInput: function (input) {
			var id = this._cacheQuestionIds[input] || null;

			if (id === null) {
				var idPieces = input.attr('id').split('-');

				id = parseInt(idPieces[idPieces.length - 1]);

				this._cacheQuestionIds[input] = id;
			}

			return id;
		},

		/**
		 * Update progress bar
		 *
		 * @private
		 */
		_updateProgressBar: function () {
			var countAllQuestions = parseInt(this._$progressBar.data('count-all')),
				nextPosition = parseInt(this._$progressBar.data('current-position'));

			var progress = Math.round(nextPosition / countAllQuestions * 100);

			this._$progressBar.find('.progress-bar').css('width', progress + '%');
		}
	};

	/**
	 * public method
	 */
	return {
		init: function (settings) {
			if (_instance === null) {
				_instance = new PxaSurvey(settings);
				_instance.init();
			}
		}
	}
})();