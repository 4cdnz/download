$(document).ready(function () {

	String.prototype.trim = function() {
		return this.replace(/^\s+/, '').replace(/\s+$/, '');
	};

	if (!('ontouchstart' in document)) {
		$('body').addClass('no-touch');
	}

	$.fn.exists = function () {
		return this.length > 0;
	};

	(function() {
		var storage = {};
		var recaptchaLoaded = false;
		var recaptchaInit = new Date().getTime();

		var pageContext = window['pageContext'];

		var animationSpeed = 400;
		if (pageContext && pageContext['animation']) {
			animationSpeed = parseInt(pageContext['animation']) || 0;
		}

		var lazyLoadDefOptions = {
			effect: 'fadeIn',
			attribute: 'data-original',
			placeholder: 'data:image/gif;base64,R0lGODlhAQABAIAAAAUEBAAAACwAAAAAAQABAAACAkQBADs=',
			threshold: 200
		};

		var defaultErrorMessage = 'Unexpected server response received. Please contact support.';
		if (pageContext && pageContext['server_error']) {
			defaultErrorMessage = pageContext['server_error'];
		}

		var listNonCachableBlocks = {
			'list_videos_my_favourite_videos': 1,
			'list_videos_my_uploaded_videos': 1,
			'list_albums_my_favourite_albums': 1,
			'list_albums_my_created_albums': 1,
			'list_playlists_my_created_playlists': 1,
			'list_dvds_my_created_channels': 1,
			'list_videos_my_channel_videos': 1,
			'list_members_subscriptions_my_subscriptions': 1,
			'list_videos_videos_from_my_subscriptions': 1,
			'list_albums_albums_from_my_subscriptions': 1,
			'list_videos_my_purchased_videos': 1,
			'list_albums_my_purchased_albums': 1,
			'list_members_my_conversations': 1,
			'list_members_my_friends': 1,
			'list_messages_my_conversation_messages': 1
		};

		var utilitiesMergeOptions = function(def, contextKey) {
			if (pageContext && pageContext[contextKey]) {
				for (var prop in pageContext[contextKey]) {
					if (pageContext[contextKey].hasOwnProperty(prop)) {
						def[prop] = pageContext[contextKey][prop];
					}
				}
			}
			return def;
		};

		var utilitiesLoadSmileys = function($container) {
			$container.find('img[data-src]').each(function () {
				var originalSrc = $(this).attr('data-src');
				if (originalSrc) {
					this.src = originalSrc;
					$(this).removeAttr('data-src');
				}
			});
		};

		var utilitiesScrollTo = function($obj, speed) {
			if (typeof speed == 'undefined') {
				speed = animationSpeed;
			}
			if ($obj.exists()) {
				var windowTop = $(document).scrollTop();
				var windowBottom = windowTop + $(window).height();
				var objectTop = $obj.offset().top;
				if (objectTop > windowTop && objectTop < windowBottom) {
					return;
				}
			}
			$.scrollTo($obj, speed, {offset: -100});
		};

		var utilitiesCountFormat = function(str, token, number) {
			if (!str || !token) {
				return '';
			}
			number = parseInt(number) || 0;
			str = str.replace(/\[count](.*?)\[\/count]/gi, function(match, p1) {
				if (match.indexOf(token) < 0) {
					return match;
				}
				var defaultValue = '';
				var values = p1.split('||');
				for (var i = 0; i < values.length; i++) {
					var temp = values[i].split(':', 2);
					if (temp.length == 1) {
						defaultValue = temp[0].trim();
					} else {
						var compareExamples = temp[0].split(',');
						for (var j = 0; j < compareExamples.length; j++) {
							var compareExample = compareExamples[j].trim();
							if (compareExample.indexOf('//') == 0) {
								if (number % 100 == parseInt(compareExample.substring(2))) {
									return temp[1].trim().replace(token, '' + number);
								}
							} else if (compareExample.indexOf('/') == 0) {
								if (number % 10 == parseInt(compareExample.substring(1))) {
									return temp[1].trim().replace(token, '' + number);
								}
							} else if (number == parseInt(temp[0].trim())) {
								return temp[1].trim().replace(token, '' + number);
							}
						}
					}
				}
				return defaultValue;
			}).replace(token, '' + number);
			return str;
		};

		var utilitiesParseParameters = function(str) {
			var result = {};
			if (str) {
				var params = str.split(';');
				for (var i = 0; i < params.length; i++) {
					var pair = params[i].split(':');
					if (pair.length == 2) {
						var paramNames = pair[0].split('+');
						for (var j = 0; j < paramNames.length; j++) {
							result[paramNames[j]] = decodeURIComponent(pair[1]).replace(/[+]/g,' ');
						}
					}
				}
			}
			return result;
		};

		var utilitiesGetBlock = function (blockId, sender, args, params) {
			var url = (args.url ? args.url : window.location.href);
			if (url.indexOf('#') > 0) {
				url = url.substring(0, url.indexOf('#'));
			}
			$.ajax({
				url: url + (url.indexOf('?') >= 0 ? '&' : '?') + 'mode=async&function=get_block&block_id=' + blockId + (params ? '&' + $.param(params) : ''),
				type: 'GET',
				cache: false,
				beforeSend: function () {
					$(sender).block({message: null});
					if (args.beforeSend) {
						args.beforeSend(sender);
					}
				},
				complete: function () {
					$(sender).unblock();
					if (args.complete) {
						args.complete(sender);
					}
				},
				success: function (html) {
					storage[blockId] = params;
					if (args.success) {
						args.success(sender, html);
					}
				},
				error: function () {
					if (args.error) {
						args.error(sender);
					}
				}
			});
		};

		var utilitiesAjaxRequest = function(sender, params, successCallback) {
			var url = window.location.href;
			if (url.indexOf('#') > 0) {
				url = url.substring(0, url.indexOf('#'));
			}
			$.ajax({
				url: url + (url.indexOf('?') >= 0 ? '&' : '?') + 'mode=async&format=json&' + $.param(params),
				type: 'GET',
				beforeSend: function () {
					$(sender).block({message: null});
				},
				complete: function () {
					$(sender).unblock();
				},
				success: function (json) {
					if (typeof json != 'object') {
						json = JSON.parse(json);
					}
					if (json && successCallback) {
						successCallback(json);
					}
				}
			});
		};

		var utilitiesRecaptcha = function($container) {
			if (!$container) {
				$container = $(document);
			}
			if (recaptchaLoaded) {
				$container.find('[data-recaptcha-key]').each(function() {
					var $outer = $(this);
					if (!$outer.attr('data-recaptcha-id')) {
						$outer.html('');
						var recaptchaId = grecaptcha.render($outer.get(0), {
							sitekey: $outer.attr('data-recaptcha-key'),
							theme: $outer.attr('data-recaptcha-theme') || 'light',
							size: $outer.attr('data-recaptcha-size') || 'normal',
							language: $('html').attr('lang') || 'en',
							hl: $('html').attr('lang') || 'en',
							callback: function() {
								var $errorContainer = $outer.parent().find('.field-error');
								$errorContainer.fadeOut();
								$outer.parent().find('.error').removeClass('error');
							}
						});
						$outer.attr('data-recaptcha-id', recaptchaId);
					}
				});
			}
		};

		var utilitiesAjaxForm = function($form, callbacks, withCredentials) {
			var considerFormBlocking = function($form, isBlock) {
				var $popupParent = $form.parents('.popup-holder');
				if ($popupParent.exists()) {
					$form = $popupParent;
				}
				isBlock ? $form.block({ message: null }) : $form.unblock();
			};

			$form.kvsProcessFormErrors = function(response) {
				for (var i = 0; i < response['errors'].length; i++) {
					var error = response['errors'][i];

					var fieldName = error['field'];
					var errorCode = error['code'];
					var errorMessage = error['message'];

					var $errorContainer = null;
					if (fieldName) {
						var $field = $form.find('[name="' + fieldName + '"]');
						if ($field.length > 1) {
							$field = $form.find('[data-name="' + fieldName + '"]');
						}
						if (!$field.exists()) {
							$field = $form.find('[data-name="' + fieldName + '"] [type="text"]');
						}
						if (!$field.exists()) {
							$field = $form.find('[data-name="' + fieldName + '"] select');
						}
						if (!$field.exists()) {
							$field = $form.find('[data-name="' + fieldName + '"]');
						}
						if ($field.exists()) {
							$field.addClass('error');
							$field.parents('.file-control').find('[type="text"]').addClass('error');
							$errorContainer = $field.parent().find('.field-error');
							if (!$errorContainer.exists()) {
								var fieldTitle = $field.parent().find('label').text();
								if (fieldTitle) {
									errorMessage += ' (' + fieldTitle + ')';
								}
							}
							if (i==0) {
								$field.trigger('focus');
							}
						} else {
							errorMessage += ' (' + fieldName + ')';
						}
					}
					if (!$errorContainer || !$errorContainer.exists()) {
						$errorContainer = $form.find('.generic-error');
					}

					$errorContainer.empty().html(errorMessage).fadeIn();

					if (fieldName == 'code' && errorCode != 'required') {
						var $captcha = $form.find('.captcha-control img');
						if ($captcha.exists()) {
							$captcha.prop('src', $captcha.attr('src').replace(new RegExp('rand=\\d+'),'rand=' + new Date().getTime()));
							$form.find('.captcha-control .textfield').val('');
						}
					}
				}

				if (recaptchaLoaded) {
					$form.find('[data-recaptcha-key]').each(function() {
						var recaptchaId = $(this).attr('data-recaptcha-id');
						if (recaptchaId) {
							if (grecaptcha.getResponse(recaptchaId)) {
								grecaptcha.reset(recaptchaId);
							}
						}
					});
				}

				if (callbacks && callbacks['error']) {
					callbacks['error']($form);
				}
				utilitiesScrollTo($form, 0);
				return $form;
			};

			$form.ajaxForm({
				data: {
					format: 'json',
					mode: 'async'
				},

				xhrFields: {
					withCredentials: !!withCredentials
				},

				beforeSerialize: function () {
					var $autoPopulates = $form.find('[data-form-populate-from]');
					$autoPopulates.each(function() {
						var populateFromName = $(this).attr('data-form-populate-from');
						if (populateFromName) {
							var $populateFrom = $form.find('[name="' + populateFromName + '"]');
							if ($populateFrom.exists()) {
								$(this).val($populateFrom.val());
							}
						}
					});
					if (callbacks && callbacks['beforeSerialize']) {
						callbacks['beforeSerialize']($form);
					}
				},

				beforeSubmit: function (data) {
					var confirmText = $form.attr('data-confirm') || '';
					if (confirmText && !confirm(confirmText)) {
						return false;
					}

					var result = true;
					if (callbacks && callbacks['beforeSubmit']) {
						result = callbacks['beforeSubmit']($form, data);
					}
					considerFormBlocking($form, result);
					return result;
				},

				uploadProgress: function(event, position, total, percent) {
					if (callbacks && callbacks['uploadProgress']) {
						callbacks['uploadProgress']($form, percent);
					}
				},

				success: function (response, statusText, xhr) {
					$form.find('.generic-error').empty().hide();
					considerFormBlocking($form, false);

					if (xhr.getResponseHeader('Content-Type').indexOf('application/json') >= 0) {
						if (typeof response != 'object') {
							response = JSON.parse(response);
						}

						if (response['status'] == 'failure') {
							$form.kvsProcessFormErrors(response);
						} else if (response['status'] == 'success') {
							if (callbacks && callbacks['success']) {
								callbacks['success']($form, response['data']);
							} else if (response['redirect']) {
								window.location = response['redirect'];
							} else {
								if ($form.attr('data-success') == 'message' && $form.attr('data-message')) {
									$.fancybox('<form><div class="success">' + $form.attr('data-message') + '</div></form>', {
										topRatio: 0.3,

										beforeClose: function() {
											window.location.reload();
											return true;
										}
									});
								} else {
									var $reloader = $('[data-reload-to]');
									if ($reloader.exists()) {
										window.location = $reloader.attr('data-reload-to');
									} else {
										window.location.reload();
									}
								}
							}
						} else {
							$form.find('.generic-error').html(defaultErrorMessage).show();
							utilitiesScrollTo($form, 0);
							if (callbacks && callbacks['error']) {
								callbacks['error']($form);
							}
						}

					} else if (xhr.getResponseHeader('Content-Type').indexOf('text/html') >= 0) {
						if (callbacks && callbacks['success']) {
							callbacks['success']($form, response);
						} else {
							if ($(response).attr('data-fancybox') == 'message' || $(response).find('[data-fancybox="message"]').exists()) {
								$.fancybox($(response), {
									topRatio: 0.3,

									beforeClose: function() {
										var $redirectTo = this.inner.find('[data-fancybox-redirect-to]');
										if ($redirectTo.exists()) {
											window.location = $redirectTo.attr('data-fancybox-redirect-to');
										} else {
											window.location.reload();
										}
										return true;
									}
								});
							} else if ($form.attr('data-success') == 'message' && $form.attr('data-message')) {
								$.fancybox('<form><div class="success">' + $form.attr('data-message') + '</div></form>', {
									topRatio: 0.3,

									beforeClose: function() {
										window.location.reload();
										return true;
									}
								});
							} else {
								$form.empty().append(response);
							}
						}
					} else {
						$form.find('.generic-error').html(defaultErrorMessage).show();
						utilitiesScrollTo($form, 0);
						if (callbacks && callbacks['error']) {
							callbacks['error']($form);
						}
					}
				},

				error: function () {
					considerFormBlocking($form, false);
					$form.find('.generic-error').html(defaultErrorMessage).show();
					utilitiesScrollTo($form, 0);
					if (callbacks && callbacks['error']) {
						callbacks['error']($form);
					}
				},

				complete: function() {
					if (callbacks && callbacks['complete']) {
						callbacks['complete']($form);
					}
				}
			});

			$form.find('input, select, textarea').each(function() {
				var $field = $(this);

				var hideErrorFunction = function() {
					var $errorContainer = $field.parent().find('.field-error');
					$errorContainer.fadeOut();
					$field.removeClass('error');
					$field.parents('fieldset').removeClass('error');
					$field.parents('.file-control').find('[type="text"]').removeClass('error');
				};

				$field.on('change', hideErrorFunction);
				if ($field.get(0).tagName.toLowerCase() == 'textarea' || $field.get(0).type == 'text' || $field.get(0).type == 'password') {
					$field.on('keypress', hideErrorFunction);
				}
			});

			$form.find('.file-control [type="file"]').on('change', function() {
				var $input = $(this);
				var value = $input.val();
				if (value.lastIndexOf('/') >= 0) {
					value = value.substring(value.lastIndexOf('/') + 1);
				}
				if (value.lastIndexOf('\\') >= 0) {
					value = value.substring(value.lastIndexOf('\\') + 1);
				}
				var files = $input.prop("files");
				if (files && files.length > 1) {
					value = '';
					for (var i = 0; i < files.length; i++) {
						if (value) {
							value += ', ';
						}
						if (i >= 3) {
							value += '...';
							break;
						}
						value += files[i].name;
					}
				}

				var $container = $input.parents('.file-control');
				if ($input.attr('multiple') && (!files || files.length == 1)) {
					var $clone = $container.clone(true, true);
					$clone.wrap('<form>').parent('form').trigger('reset');
					$clone.unwrap();
					$container.parent().append($clone);
				}
				$container.find('[type="text"]').val(($container.find('.button').html().trim() || '') + ' ' + value);
			});

			$form.find('.list-selector').each(function() {
				var $container = $(this);

				$container.find('[type="text"]').on('focus', function() {
					var $input = $(this);
					var url = $container.attr('data-selector');
					var name = $container.attr('data-name');
					var selected = ($container.attr('data-selected') || '').split(',');

					if (!$container.find('.list-selector-popup').exists()) {
						if (url && name) {
							$.ajax({
								url: url,
								type: 'GET',
								beforeSend: function () {
									$input.block({message: null});
									$input.css({cursor: 'wait'});
								},
								complete: function () {
									$input.unblock();
									$input.css({cursor: 'text'});
								},
								success: function (html) {
									var selectedIds = [];
									var selectedLabels = [];
									var filterValue = '';
									var $popupContent = $('<div class="list-selector-popup">' + html + '</div>');

									var $filterInput = $popupContent.find('[name="filter"]');
									for (var i = 0; i < selected.length; i++) {
										var id = selected[i].trim();
										if (id) {
											var $checkbox = $popupContent.find('input[type="checkbox"][value="' + id + '"],input[type="radio"][value="' + id + '"]');
											var $label = $popupContent.find('label[for="' + $checkbox.attr('id') + '"]');
											$checkbox.prop('checked', true).trigger('change');
											selectedIds.push(id);
											selectedLabels.push($label.text());
										}
									}

									$container.append($popupContent);
									$popupContent.find('input[type="checkbox"]').on('click', function() {
										var $checkbox = $(this);
										var i = 0;
										var $label = $popupContent.find('label[for="' + $checkbox.attr('id') + '"]');
										if ($label.text()) {
											var value = $checkbox.prop('value');
											if ($checkbox.prop('checked')) {
												selectedIds.push(value);
												selectedLabels.push($label.text());
												$container.append($('<input type="hidden" name="' + name + '[]" value="' + value + '"/>'))
											} else {
												for (i = 0; i < selectedIds.length; i++) {
													if (selectedIds[i] == value) {
														selectedIds.splice(i, 1);
														selectedLabels.splice(i, 1);
														break;
													}
												}
												$container.find('input[type="hidden"][value="' + value + '"]').remove();
											}
											var selectedText = '';
											for (i = 0; i < selectedLabels.length; i++) {
												if (selectedText != '') {
													selectedText += ', ';
												}
												selectedText += selectedLabels[i];
											}
											$input.prop('value', selectedText);
											$input.trigger('change');
										}
									}).on('focus', function() {
										$filterInput.trigger('focus');
									});

									$popupContent.find('input[type="radio"]').on('change', function() {
										var $checkbox = $(this);
										var $label = $popupContent.find('label[for="' + $checkbox.attr('id') + '"]');
										if ($label.text()) {
											var value = $checkbox.prop('value');
											if ($checkbox.prop('checked')) {
												$container.find('input[type="hidden"][name="' + name + '[]"]').remove();
												selectedIds[0] = value;
												selectedLabels[0] = $label.text();
												$container.append($('<input type="hidden" name="' + name + '[]" value="' + value + '"/>'))
											}
											var selectedText = selectedLabels[0];
											$input.prop('value', selectedText);
											$input.trigger('change');
										}
									}).on('focus', function() {
										$filterInput.trigger('focus');
									});

									$filterInput.trigger('focus').on('keyup', function() {
										if ($filterInput.val() == filterValue) {
											return;
										}
										filterValue = this.value.toLowerCase();

										$container.find('.item').each(function() {
											var $item = $(this);
											if (filterValue == '') {
												$item.show();
											} else {
												$item.toggle($item.find('label').text().toLowerCase().indexOf(filterValue) >= 0);
											}
										});
									});

									$(document).on('mouseup', function(e) {
										if (!$container.is(e.target) && $container.has(e.target).length === 0)
										{
											$popupContent.hide();
										}
									});
								}
							});
						}
					} else {
						$container.find('.list-selector-popup').show();
						$container.find('[name="filter"]').trigger('focus');
					}
				});
			});

			$form.find('.smileys-support img').each(function() {
				$(this).prop('title', $(this).attr('alt'));
				$(this).on('click', function() {
					var $textarea = $(this).parents('.smileys-support').find('textarea');
					if (!$textarea.exists()) {
						return;
					}
					var textarea = $textarea.get(0);
					var smiley = $(this).attr('alt');

					if (document.selection) {
						textarea.focus();
						var sel = document.selection.createRange();
						sel.text = smiley;
						textarea.focus();
					} else if (textarea.selectionStart || textarea.selectionStart == '0') {
						var startPos = textarea.selectionStart;
						var endPos = textarea.selectionEnd;
						var scrollTop = textarea.scrollTop;
						textarea.value = textarea.value.substring(0, startPos) + smiley + textarea.value.substring(endPos, textarea.value.length);
						textarea.focus();
						textarea.selectionStart = startPos + smiley.length;
						textarea.selectionEnd = startPos + smiley.length;
						textarea.scrollTop = scrollTop;
					} else {
						textarea.value += smiley;
						textarea.focus();
					}
				});
			});

			$form.find('[data-expand-id]').on('click', function() {
				var $button = $(this);
				var contentId = $button.attr('data-expand-id');
				if (contentId) {
					var $content = $('#' + contentId);
					if ($button.hasClass('expand')) {
						$content.slideDown(animationSpeed, function () {
							$(window).trigger('scroll');
						});
						$button.removeClass('expand').addClass('collapse');
					} else {
						$content.slideUp(animationSpeed, function () {
						});
						$button.removeClass('collapse').addClass('expand');
					}
				}
			});

			$form.find('[data-action="choose"]').each(function() {
				$(this).on('click', function() {
					if ($(this).hasClass('disabled')) {
						return;
					}

					var $form = $(this).parents('form');
					var $radio = $(this).find('input');
					$form.find('[data-action="choose"] [type="radio"]').prop('checked', false).trigger('change');
					$radio.prop('checked', true).trigger('change');

					if ($radio.prop('checked')) {
						$form.find('[data-action="choose"]').removeClass('active');
						$(this).addClass('active');

						var $captchaControl = $form.find('.captcha-control');
						if ($captchaControl.exists()) {
							if ($radio.attr('name') == 'payment_option') {
								$captchaControl.append($captchaControl.parent().find('[type="submit"]'));
								$captchaControl.parent().find('label').removeClass('hidden');
								$captchaControl.removeClass('hidden');
							} else if ($radio.attr('name') == 'card_package_id') {
								$captchaControl.parent().append($captchaControl.find('[type="submit"]'));
								$captchaControl.parent().find('label').addClass('hidden');
								$captchaControl.addClass('hidden');
							}
						}
					}
				});
			});

			if (recaptchaLoaded) {
				utilitiesRecaptcha($form);
			}
		};

		var utilitiesAjaxFancyBox = function ($sender, url, afterShowCallback) {
			$.fancybox([{href: url, type: 'ajax'}], {
				afterLoad: function(arg1) {
					if (arg1 && typeof arg1.content == 'string') {
						if (arg1.content.indexOf('<body>') >= 0 && arg1.content.indexOf('</body>') >= 0) {
							window.location = url;
							return false;
						}
					}
					return true;
				},

				afterShow: function() {
					if (!afterShowCallback) {
						this.inner.find('[data-form="ajax"]').each(function () {
							utilitiesAjaxForm($(this));
						});
						this.inner.find('[data-form="ajax-upload"]').each(function () {
							initVideoUploadForm($(this));
						});
						this.inner.find('[data-fancybox="ajax"]').each(function() {
							$(this).on('click', function(e) {
								e.preventDefault();
								utilitiesAjaxFancyBox($(this), this.href || $(this).attr('data-href'));
							});
						});
					}
					utilitiesLoadSmileys(this.inner);
					if (afterShowCallback) {
						afterShowCallback.call(this);
					}
				},

				beforeClose: function() {
					if (this.inner.find('[data-fancybox="refresh"]').exists()) {
						if ($sender && $sender.attr('data-fancybox-refresh-id')) {
							utilitiesReloadBlock($sender.attr('data-fancybox-refresh-id'), $sender, false, true);
						} else {
							window.location.reload();
						}
					} else if ($sender && $sender.attr('data-fancybox-refresh-id')) {
						utilitiesReloadBlock($sender.attr('data-fancybox-refresh-id'), $sender, false, true);
					}
					return true;
				},

				helpers: {
					overlay: {closeClick: false}
				},

				type: 'ajax',
				topRatio: 0.3
			});
		};

		var utilitiesReloadBlock = function(blockId, sender, scroll, animate) {
			if (!blockId) {
				window.location.reload();
				return;
			}
			var params = null;
			if (storage[blockId]) {
				params = storage[blockId];
			}

			var args = {};
			args.success = function (sender, html) {
				storage[blockId] = params;
				var animationOpacity = 0.1;
				if (!animate) {
					animationOpacity = 1;
				}
				if (scroll) {
					utilitiesScrollTo($('#' + blockId));
				}
				$('#' + blockId).animate({opacity: animationOpacity}, animationSpeed, function () {
					var div = document.createElement('div');
					div.innerHTML = html;

					var content = $(div).children().first();
					$(content).css('opacity', animationOpacity);
					$(this).replaceWith(content);
					$('#' + blockId).animate({opacity: 1}, animationSpeed);
					initAjaxLists($('#' + blockId));

					if (typeof(Storage) !== 'undefined') {
						var userId = '';
						if (pageContext && pageContext['userId']) {
							userId = pageContext['userId'] + ':';
						}
						sessionStorage.setItem(userId + location.href + '#' + blockId, $('#' + blockId).html());
						sessionStorage.setItem(userId + location.href + '#' + blockId + ':params', JSON.stringify(params));
					}
				});
			};

			var errorTries = 0;
			args.error = function () {
				errorTries++;
				var hasFromParameter = false;
				for (var paramName in params) {
					if (params.hasOwnProperty(paramName)) {
						if (paramName.indexOf('from') == 0 && parseInt(params[paramName])>1) {
							params[paramName] = parseInt(params[paramName]) - 1;
							if (errorTries > 1) {
								params[paramName] = 1;
							}
							hasFromParameter = true;
						}
					}
				}
				if (!hasFromParameter) {
					params = null;
				}

				if (errorTries > 1) {
					delete args.error;
				}

				utilitiesGetBlock(blockId, sender, args, params);
			};

			utilitiesGetBlock(blockId, sender, args, params);
		};

		var initMenu = function () {
			$('.navigation button').on('click', function() {
				$(this).parents('.navigation').toggleClass('open');
			});

			$('.primary, .logo').on('click', function() {
				var length = sessionStorage.length;
				for (var i = 0; i < length; i++) {
					var key = sessionStorage.key(i);
					if (key && key.indexOf('#list') >= 0) {
						sessionStorage.removeItem(key);
					}
				}
			});
		};

		var initTabs = function () {
			var $tabLinks = $('.tabs-menu li a');
			if ($tabLinks.exists()) {
				var tabLinks = {};
				var tabQuery = '';
				var currentActiveTab = '';
				$tabLinks.each(function() {
					if ($(this).attr('href').indexOf('#') == 0) {
						var tabId = $(this).attr('href');
						if (!currentActiveTab || tabId == window.location.hash) {
							currentActiveTab = tabId;
						}
						tabLinks[tabId] = $(this);
						tabQuery += ',' + tabId;
					}
				});

				var $tabs = $(tabQuery ? tabQuery.substr(1) : '');
				$tabs.hide();

				if (currentActiveTab) {
					tabLinks[currentActiveTab].addClass('active');
					$(currentActiveTab).show();
				}

				$tabLinks.on('click', function(e) {
					if ($(this).attr('href').indexOf('#') == 0) {
						e.preventDefault();
						var tabId = $(this).attr('href');
						$tabLinks.removeClass('active');
						$tabs.hide();
						$(tabId).show();
						if (tabLinks[tabId]) {
							tabLinks[tabId].addClass('active');
						}

						$(window).trigger('scroll');
					}
				});
			}
		};

		var initFancyBox = function () {
			$('[data-fancybox="ajax"]').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					utilitiesAjaxFancyBox($(this), this.href || $(this).attr('data-href'));
				});
			});

			$('.block-album .images a.item, .block-screenshots a.item, .list-albums-images a, .list-videos-screenshots a').fancybox({
				openEffect: 'none',
				closeEffect: 'none',
				prevEffect: 'none',
				nextEffect: 'none',
				helpers: {
					title: {
						type: 'inside'
					},
					buttons: {
						position: 'bottom'
					}
				}
			});

			if (window.location.href.indexOf('?login') > 0) {
				$('#login').trigger('click');
			}
			if (window.location.href.indexOf('?signup') > 0) {
				$('#signup').trigger('click');
			}
		};

		var initRating = function () {
			var $ratingContainer = $('.rating-container, [data-rating]');
			var $links = $ratingContainer.find('[data-vote]');
			$links.on('click', function(e) {
				e.preventDefault();
				var $link = $(this);
				if ($link.hasClass('disabled') || $link.hasClass('voted')) {
					return;
				}

				var params = {
					action: 'rate',
					vote: parseInt($link.attr('data-vote')) || 0
				};
				if ($link.attr('data-video-id') > 0) {
					params['video_id'] = $link.attr('data-video-id');
				} else if ($link.attr('data-album-id') > 0) {
					params['album_id'] = $link.attr('data-album-id');
				} else if ($link.attr('data-post-id') > 0) {
					params['post_id'] = $link.attr('data-post-id');
				} else if ($link.attr('data-playlist-id') > 0) {
					params['playlist_id'] = $link.attr('data-playlist-id');
				} else if ($link.attr('data-model-id') > 0) {
					params['model_id'] = $link.attr('data-model-id');
				} else if ($link.attr('data-cs-id') > 0) {
					params['content_source_id'] = $link.attr('data-cs-id');
				} else if ($link.attr('data-dvd-id') > 0) {
					params['dvd_id'] = $link.attr('data-dvd-id');
				} else if ($ratingContainer.attr('data-rating') && $ratingContainer.attr('data-id')) {
					params[$ratingContainer.attr('data-rating') + '_id'] = $ratingContainer.attr('data-id');
				}

				utilitiesAjaxRequest($link, params, function(json) {
					if (json['status']=='success') {
						$links.addClass('disabled');
						$link.removeClass('disabled').addClass('voted');
						$ratingContainer.find('.voters').html($ratingContainer.find('.voters').attr('data-success'));

						var $scale = $ratingContainer.find('.scale');
						var oldRating = parseFloat($scale.attr('data-rating'));
						var oldRatingVotes = parseInt($scale.attr('data-votes'));
						if (oldRatingVotes > 0) {
							if (oldRating == 0) {
								oldRatingVotes = 0;
							}
							var newRating = (oldRating * oldRatingVotes + vote) / (oldRatingVotes + 1) / 5 * 100;
							if (newRating > 100) {
								newRating = 100;
							}
							$ratingContainer.find('.scale').css({width: newRating + '%'});
						}
					} else {
						$links.addClass('disabled');
						$ratingContainer.find('.voters').html($ratingContainer.find('.voters').attr('data-error'));
					}
				});
				if ($link.attr('data-flag-id')) {
					params['action'] = 'flag';
					params['flag_id'] = $link.attr('data-flag-id');
					delete params['vote'];
					utilitiesAjaxRequest($link, params, function() {});
				}
			});
		};

		var initAddToFavourites = function () {
			$('.btn-favourites li a').on('click', function(e) {
				var $link = $(this);
				var videoId = $link.attr('data-video-id');
				var albumId = $link.attr('data-album-id');
				var favType = $link.attr('data-fav-type') || 0;
				var createPlaylistUrl = $link.attr('data-create-playlist-url');
				var playlistId = $link.attr('data-playlist-id') || 0;
				if ((videoId || albumId)) {
					e.preventDefault();
					if ($link.hasClass('delete')) {
						utilitiesAjaxRequest($link.parents('li'), {action: 'delete_from_favourites', video_id: videoId, album_id: albumId, fav_type: favType, playlist_id: playlistId}, function(json) {
							if (json['status']=='success') {
								if (playlistId > 0) {
									$link.parents('li').addClass('hidden');
									$link.parents('ul').find('#add_playlist_' + playlistId).removeClass('hidden');
								} else {
									$link.parents('li').addClass('hidden');
									$link.parents('ul').find('#add_fav_' + favType).removeClass('hidden');
								}
							}
						});
					} else {
						if (favType == 10 && !playlistId) {
							if (createPlaylistUrl) {
								utilitiesAjaxFancyBox($link, createPlaylistUrl, function () {
									var $form = this.inner.find('form');
									utilitiesAjaxForm($form, {
										success: function($form, newPlaylistData) {
											$.fancybox.close();

											newPlaylistData = $(newPlaylistData);
											playlistId = newPlaylistData.attr('data-playlist-id');
											var playlistTitle = newPlaylistData.attr('data-playlist-title');

											if (playlistId) {
												utilitiesAjaxRequest($link.parents('li'), {action: 'add_to_favourites', video_id: videoId, album_id: albumId, fav_type: favType, playlist_id: playlistId}, function(json) {
													if (json['status']=='success') {
														var $newItem = $link.parents('ul').find('#add_playlist_').clone(true, true);
														$newItem.find('a').each(function() {
															if ($(this).attr('data-playlist-id')) {
																$(this).attr('data-playlist-id', $(this).attr('data-playlist-id').replace('%ID%', playlistId));
															}
															if ($(this).attr('href')) {
																$(this).attr('href', $(this).attr('href').replace('%ID%', playlistId));
															}
															$(this).html($(this).html().replace('%1%', playlistTitle));
														});
														$newItem.attr('id', 'add_playlist_' + playlistId);
														$newItem.insertBefore($link.parents('li'));
														$newItem = $link.parents('ul').find('#delete_playlist_').clone(true, true);
														$newItem.find('a').each(function() {
															if ($(this).attr('data-playlist-id')) {
																$(this).attr('data-playlist-id', $(this).attr('data-playlist-id').replace('%ID%', playlistId));
															}
															if ($(this).attr('href')) {
																$(this).attr('href', $(this).attr('href').replace('%ID%', playlistId));
															}
															$(this).html($(this).html().replace('%1%', playlistTitle));
														});
														$newItem.attr('id', 'delete_playlist_' + playlistId);
														$newItem.removeClass('hidden');
														$newItem.insertBefore($link.parents('li'));
													}
												});
											}
										}
									});
								});
							}
						} else {
							utilitiesAjaxRequest($link.parents('li'), {action: 'add_to_favourites', video_id: videoId, album_id: albumId, fav_type: favType, playlist_id: playlistId}, function(json) {
								if (json['status']=='success') {
									if (playlistId > 0) {
										$link.parents('li').addClass('hidden');
										$link.parents('ul').find('#delete_playlist_' + playlistId).removeClass('hidden');
									} else {
										$link.parents('li').addClass('hidden');
										$link.parents('ul').find('#delete_fav_' + favType).removeClass('hidden');
									}
								}
							});
						}
					}
				}
			});
		};

		var initErrorFlaggingForm = function () {
			var $blockFlagging = $('.block-flagging');
			if ($blockFlagging.exists()) {
				var $flaggingForm = $blockFlagging.find('form');
				if ($flaggingForm.exists()) {
					$flaggingForm.each(function () {
						utilitiesAjaxForm($(this), {
							success: function($form) {
								$form.find('.success').show();
								$form.find('.block-radios, .block-textarea').hide();
							}
						});
					});
				}
			}
		};

		var initShareForm = function () {
			var $blockShare = $('.block-share');
			if ($blockShare.exists()) {
				if (typeof window['getEmbed'] == 'function') {
					$blockShare.find('.embed-code').val(window['getEmbed']());
				}
				$blockShare.find('.embed-button').on('click', function (e) {
					e.preventDefault();
					if (typeof window['getEmbed'] == 'function') {
						var embedSize = $(this).attr('data-embed-size');
						if (embedSize && embedSize.indexOf('x') > 0) {
							var embedWidth = parseInt(embedSize.substr(0, embedSize.indexOf('x')));
							var embedHeight = parseInt(embedSize.substr(embedSize.indexOf('x') + 1));
							$blockShare.find('.embed-code').val(window['getEmbed'](embedWidth, embedHeight));
						}
					}
				});
				$blockShare.find('.embed-code-custom-width, .embed-code-custom-height').on('keyup blur', function () {
					var $widthField = $blockShare.find('.embed-code-custom-width');
					var $heightField = $blockShare.find('.embed-code-custom-height');
					if (parseInt($widthField.val()) && parseInt($heightField.val())) {
						$blockShare.find('.embed-code-custom-width-label').html(parseInt($widthField.val()));
						$blockShare.find('.embed-code-custom-height-label').html(parseInt($heightField.val()));
						$blockShare.find('.embed-button-custom').attr('data-embed-size', parseInt($widthField.val()) + 'x' + parseInt($heightField.val()));
					}
				});
			}
		};

		var initCommentForm = function () {
			var $blockComments = $('.block-comments');
			var $blockNewComment = $blockComments.find('.block-new-comment');
			if ($blockComments.exists() && $blockNewComment.exists()) {
				$blockComments.find('.toggle-button').on('click', function (e) {
					e.preventDefault();
					$(this).toggleClass('active');
					$blockNewComment.toggle();
					$blockComments.find('.success').hide();
					if ($blockNewComment.is(':visible')) {
						var $captcha = $blockNewComment.find('.captcha-control img');
						if ($captcha.exists()) {
							if ($captcha.attr('data-src')) {
								$captcha.prop('src', $captcha.attr('data-src').replace(new RegExp('rand=\\d+'),'rand=' + new Date().getTime()));
							} else {
								$captcha.prop('src', $captcha.attr('src').replace(new RegExp('rand=\\d+'),'rand=' + new Date().getTime()));
							}
						}
					}

					utilitiesLoadSmileys($blockNewComment);

				});

				var $commentsForm = $blockComments.find('form');
				if ($commentsForm.exists()) {
					utilitiesAjaxForm($commentsForm, {
						success: function($form, newCommentData) {
							var $anonymousUsernameField = $form.find('[name="anonymous_username"]');
							var anonymousUsername = $anonymousUsernameField.val();
							if (anonymousUsername) {
								$.cookie('kt_anonymous_username', anonymousUsername, {expires: 365, path: '/', samesite: 'Lax'});
							}

							$form.get(0).reset();
							$anonymousUsernameField.val(anonymousUsername || '');

							var $captcha = $form.find('.captcha-control img');
							if ($captcha.exists()) {
								$captcha.prop('src', $captcha.attr('src').replace(new RegExp('rand=\\d+'),'rand=' + new Date().getTime()));
							}

							if (recaptchaLoaded) {
								$form.find('[data-recaptcha-key]').each(function() {
									var recaptchaId = $(this).attr('data-recaptcha-id');
									if (recaptchaId) {
										grecaptcha.reset(recaptchaId);
									}
								});
							}

							if ($blockComments.find('.toggle-button').exists())
							{
								$blockNewComment.hide();
								$blockComments.find('.toggle-button').removeClass('active');
							}

							var commentsBlockId = $blockComments.attr('data-block-id');
							var $commentsList = $('.list-comments');
							if (newCommentData && newCommentData['approved'] && commentsBlockId && $commentsList.exists()) {
								var args = {
									success: function(sender, html) {
										if (typeof(Storage) !== 'undefined') {
											var userId = '';
											if (pageContext && pageContext['userId']) {
												userId = pageContext['userId'] + ':';
											}
											sessionStorage.removeItem(userId + location.href + '#' + commentsBlockId);
											sessionStorage.removeItem(userId + location.href + '#' + commentsBlockId + ':params');
										}

										var resultElement = document.createElement('DIV');
										resultElement.innerHTML = html;

										var $newItem = $(resultElement).find('.item[data-comment-id="' + (newCommentData['comment_id'] || newCommentData['entry_id']) + '"]').addClass('hidden');
										$commentsList.find('#' + commentsBlockId + '_items').prepend($newItem);

										setTimeout(function() {
											$commentsList.show();
											$newItem.fadeIn();
										}, 200);
									}
								};
								utilitiesGetBlock(commentsBlockId, null, args);
							} else {
								$commentsForm.find('.success').show();
							}
						}
					});
				}

				$commentsForm.find('[name="anonymous_username"]').val($.cookie('kt_anonymous_username') || '');
			}
		};

		var initCommentActions = function () {
			$('.list-comments').on('click', '.comment-options a', function(e) {
				var $link = $(this);
				var $item = $(this).parents('.item');
				var $container = $(this).parents('.comment-options');
				var $rating = $container.find('.comment-rating');
				var $ratingLinks = $container.find('.comment-like, .comment-dislike');
				var commentId = $item.attr('data-comment-id');

				if (($link.hasClass('comment-like') || $link.hasClass('comment-dislike'))) {
					e.preventDefault();
					if ($link.hasClass('disabled')) {
						return;
					}
					var increment = ($link.hasClass('comment-dislike') ? -1 : 1);
					utilitiesAjaxRequest($link, {action: 'vote_comment', vote: increment, comment_id: commentId}, function(json) {
						if (json['status'] == 'success') {
							$ratingLinks.fadeOut();
							if ($rating.exists()) {
								var ratingValue = parseInt($rating.html());
								if (!isNaN(ratingValue)) {
									ratingValue += increment;
									$rating.html(ratingValue);
									if (ratingValue > 0) {
										$rating.addClass('positive');
									} else if (ratingValue < 0) {
										$rating.addClass('negative');
										$item.addClass('dim-comment');
									} else if (ratingValue == 0) {
										$rating.removeClass('positive').removeClass('negative');
										$item.removeClass('dim-comment');
									}
								}
							}
						} else {
							$ratingLinks.addClass('disabled');
						}
					});
				} else if ($link.hasClass('comment-edit')) {
					e.preventDefault();
					$.fancybox($('.popup-edit-comment').clone(true, true).removeClass('hidden'), {
						afterShow: function () {
							var $form = this.inner.find('form');
							$form.find('[name="comment_id"], [name="entry_id"]').val(commentId);

							var originalText = $item.find('.original-text').html() || '';
							originalText = originalText.replace(/<br>/gi, '\n').replace(/&gt;/gi, '>').replace(/&lt;/gi, '<').replace(/&#34;/gi, '"');
							originalText = originalText.replace(/<img.*?alt=['"](.*?)['"].*?>/gi, '$1');
							$form.find('[name="comment"], [name="entry"]').val(originalText);

							utilitiesAjaxForm($form, {
								success: function() {
									$.fancybox.close();
									var $blockComments = $('.block-comments');
									var commentsBlockId = $blockComments.attr('data-block-id');
									if (commentsBlockId) {
										var args = {
											success: function(sender, html) {
												if (typeof(Storage) !== 'undefined') {
													var userId = '';
													if (pageContext && pageContext['userId']) {
														userId = pageContext['userId'] + ':';
													}
													sessionStorage.removeItem(userId + location.href + '#' + commentsBlockId);
													sessionStorage.removeItem(userId + location.href + '#' + commentsBlockId + ':params');
												}

												var resultElement = document.createElement('DIV');
												resultElement.innerHTML = html;

												var newCommentText = $(resultElement).find('.item[data-comment-id="' + commentId + '"] .comment-text').html() || '';
												$item.find('.comment-text').html(newCommentText);
											}
										};
										utilitiesGetBlock(commentsBlockId, null, args);
									}
								}
							});
							utilitiesLoadSmileys(this.inner);
						},

						helpers: {
							overlay: {closeClick: false}
						},

						topRatio: 0.3
					});
				}
			});
		};

		var initAjaxLists = function ($container) {
			if (!$container) {
				$container = $(document);

				if (typeof(Storage) !== 'undefined') {
					var ajaxIds = {};
					$container.find('[data-action="ajax"]').each(function() {
						var id = $(this).attr('data-block-id');
						if (!ajaxIds[id]) {
							ajaxIds[id] = true;
						}
					});

					var userId = '';
					if (pageContext && pageContext['userId']) {
						userId = pageContext['userId'] + ':';
					}

					for (var blockId in ajaxIds) {
						if (ajaxIds.hasOwnProperty(blockId)) {
							var html = sessionStorage.getItem(userId + location.href + '#' + blockId);
							if (!html) {
								html = sessionStorage.getItem(location.href + '#' + blockId);
							}
							if (html) {
								$('#' + blockId).html(html).find('[data-fancybox="ajax"]').each(function() {
									$(this).on('click', function(e) {
										e.preventDefault();
										utilitiesAjaxFancyBox($(this), this.href || $(this).attr('data-href'));
									});
								});
							}
							var params = sessionStorage.getItem(userId + location.href + '#' + blockId + ':params');
							if (!params) {
								params = sessionStorage.getItem(location.href + '#' + blockId + ':params');
							}
							if (params) {
								try {
									storage[blockId] = JSON.parse(params);
								} catch (e) {}

								if (listNonCachableBlocks[blockId]) {
									$('#' + blockId).find('img.lazy-load').removeClass('lazy-load');
									utilitiesReloadBlock(blockId, blockId, false, false);
								}
							}
						}
					}
				}
			} else {
				$container.find('[data-fancybox="ajax"]').each(function() {
					$(this).on('click', function(e) {
						e.preventDefault();
						utilitiesAjaxFancyBox($(this), this.href || $(this).attr('data-href'));
					});
				});
			}

			if ($.fn.Lazy) {
				var webP = new Image();
				webP.onload = webP.onerror = function() {
					if (webP.height == 2) {
						$container.find('img.lazy-load[data-webp]').each(function () {
							$(this).attr('data-original', $(this).attr('data-webp'));
						});
					}

					$container.find('img.lazy-load').Lazy(utilitiesMergeOptions(lazyLoadDefOptions, 'lazyload'));
				};
				webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
			}

			if ($.fn.thumbs) {
				$container.find('img[data-cnt]').thumbs();
			}
			if ($.fn.videopreview) {
				$container.find('img[data-preview]').videopreview();
			}

			$container.find('[data-action="ajax"], [data-action="inputpage"]').on('click keydown', function (e) {
				if (e.type == 'click') {
					e.preventDefault();
				}

				var args = {};
				var $sender = $(this);
				var appendTo = $sender.attr('data-append-items-to');
				var maxQueries = parseInt($sender.attr('data-max-queries')) || 0;

				var blockId = $sender.attr('data-block-id');
				if (!blockId) {
					return;
				}
				var containerId = $sender.attr('data-container-id');

				if (maxQueries && appendTo) {
					var currentQueries = parseInt($('#' + appendTo).attr('data-current-queries')) || 0;
					if (currentQueries < maxQueries) {
						currentQueries++;
						if (currentQueries == maxQueries) {
							$('#' + containerId).remove();
						} else {
							$('#' + appendTo).attr('data-current-queries', currentQueries);
						}
					} else {
						return;
					}
				}

				var params = utilitiesParseParameters($sender.attr('data-parameters'));

				if ($sender.is('input')) {
					if (e.type == 'click') {
						return;
					}
					if (e.keyCode != 13 && e.key != 'Enter' && e.key != 'enter') {
						return;
					}
					var paginationVar = $sender.attr('data-pagination-var');
					var paginationValue = parseInt($sender.val());
					if (!paginationVar || !paginationValue) {
						return;
					}
					params[paginationVar] = paginationValue;
				}

				var userId = '';
				if (pageContext && pageContext['userId']) {
					userId = pageContext['userId'] + ':';
				}

				args.success = function (sender, html) {
					if (appendTo) {
						var resultElement = document.createElement('DIV');
						resultElement.innerHTML = html;

						if (containerId) {
							var $newContainer = $(resultElement).find('#' + containerId);
							if ($newContainer.exists()) {
								$('#' + containerId).replaceWith($newContainer);
								initAjaxLists($newContainer);
							} else {
								$('#' + containerId).remove();
							}
						}

						var $itemsToAppend = $(resultElement).find('#' + appendTo + ' .item');
						$itemsToAppend.css({display: 'none'});
						if ($('#' + appendTo).attr('data-append-to-beginning') == 'true') {
							$itemsToAppend.insertBefore($('#' + appendTo).find('.item').first());
						} else {
							$itemsToAppend.insertAfter($('#' + appendTo).find('.item').last());
						}
						$itemsToAppend.fadeIn().promise().done(function () {
							for (var paramName in params) {
								if (params.hasOwnProperty(paramName)) {
									if (paramName.indexOf('from') == 0 && parseInt(params[paramName])>1) {
										delete params[paramName];
										break;
									}
								}
							}
							if (!listNonCachableBlocks[blockId]) {
								if (typeof(Storage) !== 'undefined') {
									sessionStorage.setItem(userId + location.href + '#' + blockId, $('#' + blockId).html());
									sessionStorage.setItem(userId + location.href + '#' + blockId + ':params', JSON.stringify(params));
								}
							}
						});
						initAjaxLists($itemsToAppend);

					} else {
						utilitiesScrollTo($('#' + blockId));
						$('#' + blockId).animate({opacity: 0.1}, animationSpeed, function () {
							var div = document.createElement('div');
							div.innerHTML = html;

							var content = $(div).find('#' + blockId);
							$(content).css('opacity', '0.1');
							$(this).replaceWith(content);
							$('#' + blockId).animate({opacity: 1}, animationSpeed);
							initAjaxLists($('#' + blockId));

							if (typeof(Storage) !== 'undefined') {
								sessionStorage.setItem(userId + location.href + '#' + blockId, $('#' + blockId).html());
								sessionStorage.setItem(userId + location.href + '#' + blockId + ':params', JSON.stringify(params));
							}
						});
					}
				};

				utilitiesGetBlock(blockId, containerId ? $('#' + containerId) : $sender, args, params);
			});

			$container.find('[data-rt]').on('click mousedown', function() {
				var rotatorParams = $(this).attr('data-rt');
				if (rotatorParams) {
					var url = window.location.href;
					if (url.indexOf('#') > 0) {
						url = url.substring(0, url.indexOf('#'));
					}
					var img = new Image();
					img.src = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'mode=async&action=rotator_videos&pqr=' + rotatorParams;
					$(this).attr('data-rt', '');
				}
			});

			var playlistFunction;
			$container.find('[data-playlist-item]').on('click', playlistFunction = function(e) {
				if (e) {
					e.preventDefault();
				}

				$container.find('[data-playlist-item]').removeClass('selected');

				var $item = $(this);
				$item.addClass('selected');

				var playlistItemUrl = $item.attr('data-playlist-item');
				if (playlistItemUrl) {
					storage['playlist_state'] = playlistItemUrl;

					var args = {};
					args.url = playlistItemUrl;
					args.success = function (sender, html) {
						$('.player').html('').append($(html.trim()).find('.player-holder'));
						$('.player-holder').find('[data-fancybox="ajax"]').each(function() {
							$(this).on('click', function(e) {
								e.preventDefault();
								utilitiesAjaxFancyBox($(this), this.href || $(this).attr('data-href'));
							});
						});
						$('.player-holder').find('[data-form="ajax"]').each(function () {
							utilitiesAjaxForm($(this));
						});
						if (e) {
							setTimeout(function () {
								utilitiesScrollTo($('.player'), 0);
							}, 0);
						}
					};
					utilitiesGetBlock('video_view_video_view', $item, args);
				}
			});
			if (!storage['playlist_state']) {
				var $defaultItem = $container.find('[data-playlist-item]').first();
				if ($defaultItem.exists()) {
					$defaultItem.addClass('selected');
					playlistFunction.call($defaultItem);
				}
			} else {
				$container.find('[data-playlist-item]').each(function() {
					if ($(this).attr('data-playlist-item') == storage['playlist_state']) {
						$(this).addClass('selected');
					}
				});
			}

			$container.find('[data-fav-video-id]').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var $link = $(this);
				var videoId = $link.attr('data-fav-video-id');
				var favType = $link.attr('data-fav-type') || 0;
				utilitiesAjaxRequest($link, {action: 'add_to_favourites', video_id: videoId, video_ids: [videoId], fav_type: favType, playlist_id: 0}, function(json) {
					if (json['status'] == 'failure' && json['errors'] && json['errors'][0] && json['errors'][0]['code'] == 'not_logged_in') {
						if (pageContext && pageContext['loginUrl']) {
							utilitiesAjaxFancyBox($link, pageContext['loginUrl']);
						}
					} else {
						$link.addClass('fixed');
					}
				});
			});
			$container.find('[data-fav-album-id]').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var $link = $(this);
				var albumId = $link.attr('data-fav-album-id');
				var favType = $link.attr('data-fav-type') || 0;
				utilitiesAjaxRequest($link, {action: 'add_to_favourites', album_id: albumId, album_ids: [albumId], fav_type: favType}, function(json) {
					if (json['status'] == 'failure' && json['errors'] && json['errors'][0] && json['errors'][0]['code'] == 'not_logged_in') {
						if (pageContext && pageContext['loginUrl']) {
							utilitiesAjaxFancyBox($link, pageContext['loginUrl']);
						}
					} else {
						$link.addClass('fixed');
					}
				});
			});

			function createDeleteCallback($form, $sender, blockId) {
				return function(json) {
					if (json['status'] == 'success') {
						utilitiesReloadBlock(blockId, $sender, true, true);
						if ($form.attr('data-refresh-block-ids')) {
							var blockIds = $form.attr('data-refresh-block-ids').split(',');
							for (var j = 0; j < blockIds.length; j++) {
								utilitiesReloadBlock(blockIds[j], $sender, false, true);
							}
						} else if ($sender.attr('data-redirect-url')) {
							window.location = $sender.attr('data-redirect-url');
						}
					} else {
						for (var i = 0; i < json['errors'].length; i++) {
							var error = json['errors'][i];
							var errorMessage = error['message'];
							if (errorMessage) {
								$form.find('.generic-error').empty().html(errorMessage).fadeIn();
							}
						}
						utilitiesScrollTo($('#' + blockId), 0);
					}
				};
			}

			$container.find('[data-action="select"]').each(function() {
				$(this).on('click', function(e) {
					if ($(this).hasClass('disabled')) {
						return;
					}

					var $form = $(this).parents('form');
					var $checkbox = $(this).find('input');
					if (!$(e.target).is($checkbox)) {
						$checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
					}

					var selectedNumber = parseInt($form.attr('data-selected-cnt')) || 0;
					if ($checkbox.prop('checked')) {
						$(this).addClass('active');
						selectedNumber++;
					} else {
						$(this).removeClass('active');
						selectedNumber = Math.max(selectedNumber - 1, 0);
					}
					$form.find('input[data-mode="selection"]').prop('disabled', selectedNumber == 0);
					$form.find('input[data-action="select_all"]').toggleClass('active', selectedNumber == $form.find('input[type=checkbox]').length - $form.find('input[type=checkbox][disabled]').length);
					$form.attr('data-selected-cnt', selectedNumber);
				});
			});

			$container.find('[data-action="choose"]').each(function() {
				$(this).on('click', function() {
					if ($(this).hasClass('disabled')) {
						return;
					}

					var $form = $(this).parents('form');
					var $radio = $(this).find('input');
					$radio.prop('checked', true).trigger('change');

					if ($radio.prop('checked')) {
						$form.find('[data-action="choose"]').removeClass('active');
						$(this).addClass('active');
					}
				});
			});

			$container.find('[data-action="delete"]').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();

					if ($(this).hasClass('disabled')) {
						return;
					}

					var $form = $(this).parents('form');
					var $button = $(this);
					var confirmText = $button.attr('data-confirm') || '';
					if (!confirmText || confirm(confirmText)) {
						var objectId = $button.attr('data-id');
						if (!objectId) {
							return;
						}

						var blockId = $form.attr('data-block-id');
						var params = utilitiesParseParameters($form.attr('data-parameters'));
						params['function'] = 'get_block';
						params['block_id'] = blockId;
						params['delete'] = [objectId];
						utilitiesAjaxRequest($button, params, createDeleteCallback($form, $button, blockId));
					}
				});
			});

			$container.find('[data-action="delete_mixed"]').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();

					if ($(this).hasClass('disabled')) {
						return;
					}

					var $form = $(this).parents('form');
					var $button = $(this);
					var confirmText = $button.attr('data-confirm') || '';
					if (!confirmText || confirm(confirmText)) {
						var videoId = $button.attr('data-video-id');
						var albumId = $button.attr('data-album-id');
						if (!videoId && !albumId) {
							return;
						}

						var blockId = $form.attr('data-block-id');
						var params = utilitiesParseParameters($form.attr('data-parameters'));
						params['function'] = 'get_block';
						params['block_id'] = blockId;
						if (videoId) {
							params['delete_video_ids'] = [videoId];
						} else if (albumId) {
							params['delete_album_ids'] = [albumId];
						}
						utilitiesAjaxRequest($button, params, createDeleteCallback($form, $button, blockId));
					}
				});
			});

			$container.find('form[data-controls]').each(function () {
				var $form = $(this);
				var blockId = $form.attr('data-block-id');

				$form.find('input[type="button"]').each(function() {
					$(this).on('click', function(e) {
						e.preventDefault();

						var $button = $(this);
						var confirmText = $button.attr('data-confirm') || '';
						if (confirmText) {
							confirmText = utilitiesCountFormat(confirmText, '%1%', parseInt($form.attr('data-selected-cnt')));
						}
						var params = {};
						if (!confirmText || confirm(confirmText)) {
							if ($button.attr('data-action') == 'select_all') {
								if ($button.hasClass('active')) {
									$form.find('input[type=checkbox]').each(function() {
										if (this.checked) {
											$(this).trigger('click');
										}
									});
								} else {
									$form.find('input[type=checkbox]').each(function() {
										if (!this.checked) {
											$(this).trigger('click');
										}
									});
								}
							} else if ($button.attr('data-action') == 'delete_multi') {
								params = utilitiesParseParameters($form.attr('data-parameters'));
								params['function'] = 'get_block';
								params['block_id'] = blockId;
								params['delete'] = [];
								$form.find('input[type=checkbox]').each(function() {
									if (this.checked) {
										params['delete'].push(this.value);
									}
								});

								utilitiesAjaxRequest($button, params, createDeleteCallback($form, $button, blockId));
							} else if ($button.attr('data-action') == 'delete_mixed_multi') {
								params = utilitiesParseParameters($form.attr('data-parameters'));
								params['function'] = 'get_block';
								params['block_id'] = blockId;
								params['delete_video_ids'] = [];
								params['delete_album_ids'] = [];
								$form.find('input[type=checkbox]').each(function() {
									if (this.checked) {
										if (this.name == 'delete_video_ids[]') {
											params['delete_video_ids'].push(this.value);
										} else if (this.name == 'delete_album_ids[]') {
											params['delete_album_ids'].push(this.value);
										}
									}
								});

								utilitiesAjaxRequest($button, params, createDeleteCallback($form, $button, blockId));
							} else if ($button.attr('data-action') == 'move_multi') {
								var playlistSelectorUrl = $button.attr('data-href');
								if (playlistSelectorUrl) {
									utilitiesAjaxFancyBox($button, playlistSelectorUrl, function () {
										var $inner_form = this.inner.find('form');
										utilitiesAjaxForm($inner_form, {
											beforeSubmit: function($inner_form) {
												$.fancybox.close();

												var playlistId = parseInt($inner_form.find('[name="playlist_id"]:checked').val());
												if (playlistId) {
													params = utilitiesParseParameters($form.attr('data-parameters'));
													params['function'] = 'get_block';
													params['block_id'] = blockId;
													params['move_to_playlist_id'] = playlistId;
													params['delete'] = [];
													$form.find('input[type=checkbox]').each(function() {
														if (this.checked) {
															params['delete'].push(this.value);
														}
													});

													utilitiesAjaxRequest($button, params, createDeleteCallback($form, $button, blockId));
												} else {
													var createPlaylistUrl = $inner_form.attr('data-create-playlist-url');
													if (createPlaylistUrl) {
														utilitiesAjaxFancyBox($button, createPlaylistUrl, function () {
															var $inner_form = this.inner.find('form');
															utilitiesAjaxForm($inner_form, {
																success: function($inner_form, newPlaylistData) {
																	$.fancybox.close();

																	newPlaylistData = $(newPlaylistData);
																	playlistId = newPlaylistData.attr('data-playlist-id');

																	if (playlistId) {
																		params = utilitiesParseParameters($form.attr('data-parameters'));
																		params['function'] = 'get_block';
																		params['block_id'] = blockId;
																		params['move_to_playlist_id'] = playlistId;
																		params['delete'] = [];
																		$form.find('input[type=checkbox]').each(function() {
																			if (this.checked) {
																				params['delete'].push(this.value);
																			}
																		});

																		utilitiesAjaxRequest($button, params, createDeleteCallback($form, $button, blockId));
																	}
																}
															});
														});
													}
												}
												return false;
											}
										});
									});
								}
							} else if ($button.attr('data-action') == 'redirect') {
								var redirectUrl = $button.attr('data-redirect-url');
								if (!redirectUrl) {
									return;
								}

								window.location = redirectUrl;
							} else if ($button.attr('data-action') == 'delete_playlist') {
								var playlistId = $button.attr('data-id');
								if (!playlistId) {
									return;
								}

								params['action'] = 'delete_playlists';
								params['delete'] = [playlistId];
								utilitiesAjaxRequest($button, params, createDeleteCallback($form, $button, blockId));
							} else if ($button.attr('data-action') == 'delete_dvd') {
								var dvdId = $button.attr('data-id');
								if (!dvdId) {
									return;
								}

								params['action'] = 'delete_dvds';
								params['delete'] = [dvdId];
								utilitiesAjaxRequest($button, params, createDeleteCallback($form, $button, blockId));
							}
						}
					});
				})
			});
		};

		var initAjaxForms = function () {
			$('[data-form="ajax"]').each(function () {
				utilitiesAjaxForm($(this));
			});
		};

		var initSubscriptions = function () {
			var $btnSubscriptions = $('[data-subscribe-to], [data-unsubscribe-to]');
			$btnSubscriptions.on('click', function(e) {
				e.preventDefault();

				var $btn = $(this);
				if ($btn.hasClass('done')) {
					return;
				}
				var subscriptionTo = $btn.attr('data-subscribe-to') || $btn.attr('data-unsubscribe-to');
				var confirmation = $btn.attr('data-confirm');
				var subscriptionId = $btn.attr('data-id');
				if (subscriptionTo && subscriptionId) {
					var params = {action: 'subscribe'};
					if (!$btn.attr('data-subscribe-to')) {
						params['action'] = 'unsubscribe';
					}
					if (subscriptionTo == 'category') {
						params[params['action'] + '_category_id'] = subscriptionId;
					} else if (subscriptionTo == 'model') {
						params[params['action'] + '_model_id'] = subscriptionId;
					} else if (subscriptionTo == 'content_source') {
						params[params['action'] + '_cs_id'] = subscriptionId;
					} else if (subscriptionTo == 'user') {
						params[params['action'] + '_user_id'] = subscriptionId;
					} else if (subscriptionTo == 'playlist') {
						params[params['action'] + '_playlist_id'] = subscriptionId;
					} else if (subscriptionTo == 'dvd') {
						params[params['action'] + '_dvd_id'] = subscriptionId;
					}
					if (!confirmation || confirm(confirmation)) {
						utilitiesAjaxRequest($btn, params, function (json) {
							if (json['status'] == 'success') {
								$btn.addClass('done');
								var $buttonInfo = $btn.parents().first().find('.button-info');
								if ($buttonInfo.exists()) {
									if (params['action'] == 'subscribe') {
										$buttonInfo.html(parseInt($buttonInfo.html()) + 1);
									} else {
										$buttonInfo.html(parseInt($buttonInfo.html()) - 1);
									}
								}
							}
						});
					}
				}
			});
		};

		var initSearch = function () {
			var selectedOptions, userId = '';
			if (typeof(Storage) !== 'undefined') {
				if (pageContext && pageContext['userId']) {
					userId = pageContext['userId'] + ':';
				}

				selectedOptions = sessionStorage.getItem(userId + location.href + '#search_filter');
				if (!selectedOptions) {
					selectedOptions = sessionStorage.getItem(location.href + '#search_filter');
				}
				if (selectedOptions) {
					selectedOptions = JSON.parse(selectedOptions);
					for (var id in selectedOptions) {
						$('#' + id).prop('checked', true);
					}
					if (!selectedOptions.search_string_filter) {
						$('#search_string_filter').prop('checked', false);
					}
				} else {
					selectedOptions = {search_string_filter: true};
				}
			}

			if (typeof $.Autocomplete == 'function' && $.Autocomplete.defaults.serviceUrl) {
				$('#search_form input[type="text"]').autocomplete({
					paramName: 'q',
					onSelect: function(suggestion) {
						if (suggestion && suggestion.data && suggestion.data.url) {
							window.location = suggestion.data.url;
						}
					}
				});
			}

			$('#search_form').on('submit', function(e) {
				try {
					if (this['q'].value == '') {
						this['q'].focus();
						e.preventDefault();
						return;
					}
					if ($(this).attr('data-url')) {
						var value = this['q'].value.replace(/[-]/g, '[dash]').replace(/[ ]+/g, '-').replace(/[?]/g, '').replace(/[&]/g, '%26').replace(/[?]/g, '%3F').replace(/[/]/g, '%2F').replace(/\[dash]/g, '--');
						window.location = $(this).attr('data-url').replace('%QUERY%', encodeURIComponent(value));
						e.preventDefault();
					}
				} catch (e) {}
			});

			$('#search_form .search-button').on('click', function() {
				$('#search_form').submit();
			});

			$('[data-search-filter-ids] input, [data-search-filter-ids] button').on('click', function() {
				var $container = $(this).parents('[data-search-filter-ids]');

				if ($(this).prop('type') == 'button' || $(this).prop('tagName').toLowerCase() == 'button') {
					$container.find('input[name*="[]"]').each(function () {
						var $input = $(this);
						$input.prop('checked', false);

						if (selectedOptions) {
							if ($input.attr('id')) {
								if ($input.prop('checked')) {
									selectedOptions[$input.attr('id')] = true;
								} else {
									delete selectedOptions[$input.attr('id')];
								}
							}
							sessionStorage.setItem(userId + location.href + '#search_filter', JSON.stringify(selectedOptions));
						}
					});
				} else {
					if (selectedOptions) {
						if ($(this).attr('id')) {
							if ($(this).prop('checked')) {
								selectedOptions[$(this).attr('id')] = true;
							} else {
								delete selectedOptions[$(this).attr('id')];
							}
						}
						sessionStorage.setItem(userId + location.href + '#search_filter', JSON.stringify(selectedOptions));
					}
				}

				var blockIds = $container.attr('data-search-filter-ids').split(',');

				var params = {};
				var paramGroups = {};
				$container.find('input').each(function () {
					var $input = $(this);
					if ($input.prop('type') == 'hidden' || ($input.prop('type') == 'checkbox' && $input.prop('checked')) || ($input.prop('type') == 'radio' && $input.prop('checked'))) {
						if ($input.prop('name').indexOf('[]') > 0) {
							var paramName = $input.prop('name').replace('[]', '');
							if ($input.attr('data-group-id')) {
								var groupMap = paramGroups[paramName] || {};
								var group = groupMap[$input.attr('data-group-id')] || [];
								group.push($input.prop('value'));
								groupMap[$input.attr('data-group-id')] = group;
								paramGroups[paramName] = groupMap;
							} else {
								params[paramName] = $input.prop('value') + (params[paramName] ? ',' + params[paramName] : '');
							}
						} else {
							params[$input.prop('name')] = $input.prop('value');
						}
					}
				});

				for (var groupName in paramGroups) {
					if (paramGroups.hasOwnProperty(groupName)) {
						var groupedArray = [];
						for (var groupId in paramGroups[groupName]) {
							if (paramGroups[groupName].hasOwnProperty(groupId)) {
								groupedArray.push('(' + paramGroups[groupName][groupId].join(',') + ')');
							}
						}
						params[groupName] = groupedArray.join('|');
					}
				}


				for (var paramName in params) {
					if (params.hasOwnProperty(paramName)) {
						if (params[paramName] == 'all') {
							delete params[paramName];
						}
					}
				}

				for (var i = 0; i < blockIds.length; i++) {
					storage[blockIds[i]] = params;
					utilitiesReloadBlock(blockIds[i], $container, false, true);
				}
			});
		};

		var initVideoUploadForm = function($uploadForms) {
			if (!$uploadForms) {
				$uploadForms = $('[data-form="ajax-upload"]');
			}
			$uploadForms.each(function () {
				var $form = $(this);
				var redirectUrl = $form.attr('data-redirect-url');
				var continueForm = $form.attr('data-continue-form');
				var lastPercent = 0;
				var realName = '';

				var progressFunction = function(percent, uploadData) {
					percent = Math.min(parseFloat(percent) || 0, 100);
					if (percent > lastPercent) {
						$form.addClass('uploading');
						if (!$form.find('.progressbar').exists()) {
							$form.append($('<div class="progressbar"><div class="progress"></div><div class="text"></div></div>'))
						}
						$form.find('.progressbar .progress').stop(true, true).animate({width: percent + '%'});
						$form.find('.progressbar .text').html(parseInt(percent) + '%');
						if (percent > 55) {
							$form.find('.progressbar').addClass('half-done');
						}
						lastPercent = percent;
						if (percent != 100 && continueForm) {
							$('#' + continueForm).show();
							if (realName && !$('#' + continueForm).find('input[data-autopopulate-name]').val()) {
								$('#' + continueForm).find('input[data-autopopulate-name]').val(realName.replace(/\.[^/.]+$/, ''));
							}
						}
					}

					if (uploadData && uploadData['filename']) {
						$form.unblock().addClass('uploading-finished');
						if (redirectUrl) {
							window.location = redirectUrl.replace('%HASH%', uploadData['filename']);
						} else if (continueForm) {
							var $continueForm = $('#' + continueForm);
							$continueForm.show();
							$continueForm.find('input[type="submit"]').enable(true);
							$continueForm.find('input[name="file"]').val(uploadData['filename'] + '.mp4');
							$continueForm.find('input[name="file_hash"]').val(uploadData['filename']);
							$continueForm.find('input[name="files"]').val(uploadData['filename']);

							var $previewImage = $('[data-preview-src]');
							if ($previewImage.exists()) {
								$previewImage.prop('src', $previewImage.attr('data-preview-src').replace('%HASH%', uploadData['filename']));
							}

							var $previewInfo = $('[data-info-src]');
							if ($previewInfo.exists()) {
								if (uploadData['dimensions'] && uploadData['duration_string'] && uploadData['size_string']) {
									$previewInfo.html($previewInfo.attr('data-info-src').replace('%1%', uploadData['dimensions'][0] + 'x' + uploadData['dimensions'][1]).replace('%2%', uploadData['duration_string']).replace('%3%', uploadData['size_string']));
								}
								if (uploadData['files_count'] && uploadData['files_size_string']) {
									$previewInfo.html(utilitiesCountFormat(utilitiesCountFormat($previewInfo.attr('data-info-src').replace('%2%', uploadData['files_size_string']), '%1%', uploadData['files_count']), '%3%', uploadData['files_skipped']));
								}
							}
						}
					}
				};

				utilitiesAjaxForm($form, {
					success: function($form, uploadData) {
						progressFunction(100, uploadData);
					},

					beforeSerialize: function($form) {
						var md5filename = '';
						if ($form.find('[name="url"]').val() || $form.find('[name="content"]').val() || $form.find('[name="content[]"]').val() || $form.find('[name="upload_option"]:checked').val() == 'embed') {
							for (var i = 0; i < 32; i++) {
								md5filename += '' + Math.floor((Math.random() * 10));
							}
							$form.find('[name="filename"]').val(md5filename);
						} else {
							$form.find('[name="filename"]').val(md5filename);
						}
					},

					beforeSubmit: function($form) {
						lastPercent = 0;

						var action = $form.find('[name="action"]').val();
						var uploadType = $form.find('[name="upload_option"]:checked').val() || 'file';
						var hash = $form.find('[name="filename"]').val();
						var $fileInput, files;

						if (action == 'upload_files') {
							$fileInput = $form.find('[name="content[]"]');
							files = [];

							var totalsize = 0;
							var filesLoaded = 0, sizeLoaded = 0;
							$fileInput.each(function() {
								for (var i = 0; i < this.files.length; i++) {
									files.push(this.files[i]);
									totalsize += this.files[i].size || 0;
								}
							});

							var uploadSendFileFunction = function (index) {
								if (index - 1 > filesLoaded || index - 1 > files.length) {
									return;
								}

								var data = new FormData();
								data.append('filename', hash);
								data.append('files', files.length.toString());
								data.append('index', index);
								if (index > 0) {
									data.append('content', files[index - 1]);
								}

								$.ajax({
									type: 'POST',
									url: $form.attr('action'),
									xhr: function() {
										var xhr = new window.XMLHttpRequest();
										xhr.withCredentials = true;
										xhr.upload.addEventListener('progress', function(event) {
											if (event.lengthComputable) {
												progressFunction((sizeLoaded + event.loaded) / totalsize * 100);
											}
										},false);
										return xhr;
									},

									success: function (response) {
										if (typeof response != 'object') {
											response = JSON.parse(response);
										}
										if (response['status'] == 'success') {
											if (index > 0) {
												filesLoaded++;
												sizeLoaded += files[index - 1].size || 0;
												if (files.length > filesLoaded) {
													progressFunction(sizeLoaded / totalsize * 100);
													uploadSendFileFunction(filesLoaded + 1);
												} else {
													progressFunction(100);
													uploadSendFileFunction(0);
												}
											} else {
												progressFunction(100, response['data']);
											}
										} else {
											$form.kvsProcessFormErrors(response).removeClass('uploading').unblock().find('.progressbar').hide().remove();
										}
									},

									error: function (xhr) {
										if (xhr.status == 0) {
											setTimeout(function () {
												uploadSendFileFunction(index);
											}, 10 * 1000);
											return;
										}
										$form.find('.generic-error').html(defaultErrorMessage).show();
										$form.removeClass('uploading').unblock().find('.progressbar').hide().remove();
									},

									data: data,
									processData: false,
									contentType: false
								});
							};

							setTimeout(function () {
								$form.block({message: null});
								uploadSendFileFunction(filesLoaded + 1);
							}, 0);
							return false;
						} else if (action == 'upload_file') {
							if (uploadType == 'file') {
								$fileInput = $form.find('[name="content"]');
								if ($fileInput.exists()) {
									files = $fileInput.get(0).files;
									if (files && files.length == 1) {
										var chunkSize = 9 * 1024 * 1024;
										if (pageContext && pageContext['upload_chunk_size']) {
											chunkSize = parseInt(pageContext['upload_chunk_size']);
										}
										var filesize = files[0].size || 0;
										var chunks = Math.floor(filesize / chunkSize);
										var chunksLoaded = 0;
										if (filesize % chunkSize > 0) {
											chunks++;
										}
										realName = files[0].name;

										var uploadSliceFunction = function (file, start, end) {
											var slice = file['mozSlice'] ? file['mozSlice'] : (file['webkitSlice'] ? file['webkitSlice'] : (file['slice'] ? file['slice'] : function () {
											}));
											return slice.bind(file)(start, end);
										};

										var uploadSendChunkFunction = function (index) {
											if (index - 1 > chunksLoaded) {
												return;
											}

											var data = new FormData();
											data.append('filename', hash);
											data.append('realname', files[0].name);
											data.append('upload_option', 'file');
											data.append('chunks', chunks.toString());
											data.append('index', index);
											data.append('size', filesize.toString());
											if (index > 0) {
												data.append('content', uploadSliceFunction(files[0], (index - 1) * chunkSize, Math.min(index * chunkSize, filesize)));
											}

											$.ajax({
												type: 'POST',
												url: $form.attr('action'),
												xhr: function() {
													var xhr = new window.XMLHttpRequest();
													xhr.withCredentials = true;
													xhr.upload.addEventListener('progress', function(event) {
														if (event.lengthComputable) {
															progressFunction((chunkSize * chunksLoaded + event.loaded) / filesize * 100);
														}
													},false);
													return xhr;
												},

												success: function (response) {
													if (typeof response != 'object') {
														response = JSON.parse(response);
													}
													if (response['status'] == 'success') {
														if (index > 0) {
															chunksLoaded++;
															if (chunks > chunksLoaded) {
																progressFunction((chunkSize * chunksLoaded) / filesize * 100);
																uploadSendChunkFunction(chunksLoaded + 1);
															} else {
																progressFunction(100);
																uploadSendChunkFunction(0);
															}
														} else {
															progressFunction(100, response['data']);
														}
													} else {
														$form.kvsProcessFormErrors(response).removeClass('uploading').unblock().find('.progressbar').hide().remove();
													}
												},

												error: function (xhr) {
													if (xhr.status == 0) {
														setTimeout(function () {
															uploadSendChunkFunction(index);
														}, 10 * 1000);
														return;
													}
													$form.find('.generic-error').html(defaultErrorMessage).show();
													$form.removeClass('uploading').unblock().find('.progressbar').hide().remove();
												},

												data: data,
												processData: false,
												contentType: false
											});
										};

										setTimeout(function () {
											$form.block({message: null});
											uploadSendChunkFunction(chunksLoaded + 1);
										}, 0);
										return false;
									}
								}
							} else if (uploadType == 'url') {
								var $urlInput = $form.find('[name="url"]');
								if ($urlInput.exists()) {
									var url = $urlInput.val();
									if (url) {
										var uploadSendUrlFunction = function () {
											$.ajax({
												type: 'POST',
												url: $form.attr('action'),
												xhrFields: {
													withCredentials: true
												},

												success: function (response) {
													if (typeof response != 'object') {
														response = JSON.parse(response);
													}
													if (response['status'] == 'success') {
														if (response['data']['state'] == 'uploading') {
															progressFunction(response['data']['percent'], response['data']);
															setTimeout(uploadSendUrlFunction, 1000);
														} else {
															progressFunction(100, response['data']);
														}
													} else {
														$form.kvsProcessFormErrors(response).removeClass('uploading').unblock().find('.progressbar').hide().remove();
													}
												},

												error: function (xhr) {
													if (xhr.status == 0) {
														setTimeout(function () {
															uploadSendUrlFunction();
														}, 10 * 1000);
														return;
													}
													$form.find('.generic-error').html(defaultErrorMessage).show();
													$form.removeClass('uploading').unblock().find('.progressbar').hide().remove();
												},

												data: {
													upload_option: 'url',
													filename: hash,
													url: url,
													upload_v2: 'true'
												}
											});
										};

										setTimeout(function () {
											$form.block({message: null});
											uploadSendUrlFunction();
										}, 0);
										return false;
									}
								}
							}
						}
						return true;
					}

				}, true);

				$form.find('[name="upload_option"]').on('change', function() {
					var $radio = $(this);
					if ($radio.prop('checked')) {
						var disabledProp = 'disabled';
						if ($radio.val() == 'file') {
							$form.find('[name="content"]').parents('.file-control').find('input').prop(disabledProp, false).trigger('click');
							$form.find('[name="url"]').prop(disabledProp, true).val('').trigger('change');
							$form.find('[name="embed"]').prop(disabledProp, true).val('').trigger('change').parents('.row').find('label').removeClass('required');
							$form.find('[name="duration"]').prop(disabledProp, true).val('').trigger('change').parents('.row').find('label').removeClass('required');
							$form.find('[name="screenshot"]').parents('.file-control').find('input').prop(disabledProp, true).val('').trigger('change').parents('.row').find('label').removeClass('required');
						} else if ($radio.val() == 'url') {
							$form.find('[name="content"]').parents('.file-control').find('input').prop(disabledProp, true).val('').trigger('change');
							$form.find('[name="url"]').prop(disabledProp, false).trigger('focus');
							$form.find('[name="embed"]').prop(disabledProp, true).val('').trigger('change').parents('.row').find('label').removeClass('required');
							$form.find('[name="duration"]').prop(disabledProp, true).val('').trigger('change').parents('.row').find('label').removeClass('required');
							$form.find('[name="screenshot"]').parents('.file-control').find('input').prop(disabledProp, true).val('').trigger('change').parents('.row').find('label').removeClass('required');
						} else if ($radio.val() == 'embed') {
							$form.find('[name="url"]').prop(disabledProp, true).val('').trigger('change');
							$form.find('[name="content"]').parents('.file-control').find('input').prop(disabledProp, true).val('').trigger('change');
							$form.find('[name="embed"]').prop(disabledProp, false).trigger('focus').parents('.row').find('label').addClass('required');
							$form.find('[name="duration"]').prop(disabledProp, false).parents('.row').find('label').addClass('required');
							$form.find('[name="screenshot"]').parents('.file-control').find('input').prop(disabledProp, false).parents('.row').find('label').addClass('required');
						}
					}
				});

				var params = {mode: 'async', format: 'json', action: $form.find('[name="action"]').val()};
				$form.attr('action', ($form.attr('action') || '') + (($form.attr('action') || '').indexOf('?') >=0 ? '&' : '?') + $.param(params));
			});
		};

		var initProfile = function() {
			$('[data-action="message"],[data-action="add_to_friends"]').on('click', function(e) {
				e.preventDefault();

				var $btn = $(this);
				if ($btn.hasClass('done')) {
					return;
				}

				var popupClass = '.popup-send-message';
				if ($btn.attr('data-action') == 'add_to_friends') {
					popupClass = '.popup-add-to-friends';
				}
				$.fancybox($(popupClass).clone(true, true).removeClass('hidden'), {
					afterShow: function () {
						var $form = this.inner.find('form');
						utilitiesAjaxForm($form, {
							success: function() {
								$btn.addClass('done');
								$.fancybox.close();
								if ($btn.attr('data-action') == 'add_to_friends') {
									window.location.reload();
								}
							}
						});
						utilitiesLoadSmileys(this.inner);
					},

					helpers: {
						overlay: {closeClick: false}
					},

					topRatio: 0.3
				});
			});
		};

		var initMessages = function () {
			var $messageForm = $('#send_message_form');
			if ($messageForm.exists()) {
				utilitiesLoadSmileys($messageForm);
				utilitiesAjaxForm($messageForm, {
					success: function($form, newMessageData) {
						var editing = false;
						if ($form.find('[name="message_id"]').val() == newMessageData['message_id']) {
							editing = true;
						}
						$form.get(0).reset();
						$form.find('[name="message_id"]').val('');

						var messagesBlockId = $form.attr('data-block-id');
						var $messagesList = $('.list-messages');
						if (newMessageData && messagesBlockId && $messagesList.exists()) {
							var args = {
								success: function(sender, html) {
									var resultElement = document.createElement('DIV');
									resultElement.innerHTML = html;

									if (editing) {
										$messagesList.find('.item[data-message-id="' + newMessageData['message_id'] + '"]').replaceWith($(resultElement).find('.item[data-message-id="' + newMessageData['message_id'] + '"]'));
									} else {
										var $newItem = $(resultElement).find('.item[data-message-id="' + newMessageData['message_id'] + '"]').addClass('hidden');
										$messagesList.find('#' + messagesBlockId + '_items').append($newItem);

										setTimeout(function() {
											$messagesList.show();
											$newItem.fadeIn();
										}, 200);
									}
								}
							};
							utilitiesGetBlock(messagesBlockId, null, args);
						}
					}
				});
			}

			$('[data-action="delete_conversation"], [data-action="ignore_conversation"]').on('click', function(e) {
				e.preventDefault();

				var $button = $(this);
				var confirmText = $button.attr('data-confirm') || '';
				if (!confirmText || confirm(confirmText)) {
					var userId = $button.attr('data-user-id');
					if (!userId) {
						return;
					}

					var blockId = $button.attr('data-block-id');
					var params = {};
					params['function'] = 'get_block';
					params['block_id'] = blockId;
					params['action'] = $button.attr('data-action');
					params['conversation_user_id'] = userId;
					utilitiesAjaxRequest($button, params, function(json) {
						if (json['status'] == 'success') {
							window.location.reload();
						}
					});
				}
			});

			$(document).on('click', '.list-messages [data-edit-message-id]', function(e) {
				e.preventDefault();

				var $button = $(this);
				var messageId = $button.attr('data-edit-message-id');
				if (!messageId) {
					return;
				}

				var $form = $('#send_message_form');
				if ($form.find('[name="message_id"]').val()) {
					return;
				}
				$form.find('[name="message_id"]').val(messageId);
				utilitiesScrollTo($form);

				var $item = $button.parents('.item[data-message-id="' + messageId + '"]');
				$item.addClass('editing');

				var originalText = $item.find('.original-text').html() || '';
				originalText = originalText.replace(/<br>/gi, '\n').replace(/&gt;/gi, '>').replace(/&lt;/gi, '<').replace(/&#34;/gi, '"');
				originalText = originalText.replace(/<img.*?alt=['"](.*?)['"].*?>/gi, '$1');
				originalText = originalText.trim();
				$form.find('[name="message"]').val(originalText).trigger('focus');
			});
		};

		var initStats = function() {
			var sendStatsReq = function(action) {
				var statsUrl = window.location.href;
				if (statsUrl.indexOf('#') > 0) {
					statsUrl = statsUrl.substring(0, statsUrl.indexOf('#'));
				}
				if (statsUrl.indexOf('?') >= 0) {
					statsUrl += '&';
				} else {
					statsUrl += '?';
				}

				if (action == 'js_stats' && pageContext) {
					if (pageContext['disableStats']) {
						return;
					}
					if (pageContext['videoId']) {
						statsUrl += 'video_id=' + pageContext['videoId'] + '&';
					}
					if (pageContext['albumId']) {
						statsUrl += 'album_id=' + pageContext['albumId'] + '&';
					}
				}

				var img = new Image();
				img.src = statsUrl + 'mode=async&action=' + action + '&rand=' + new Date().getTime();
			};

			$.cookie('kt_tcookie', '1', {expires: 7, path: '/', samesite: 'Lax'});
			if ($.cookie('kt_tcookie') == '1') {
				sendStatsReq('js_stats');
			}

			if (pageContext && pageContext['userId']) {
				var reporter = function() {
					sendStatsReq('js_online_status');
				};
				reporter();
				setInterval(reporter, 60 * 1000);
			}
		};

		var initAutoscroll = function() {
			var $autoscroll = $('[data-autoscroll="true"]');
			if ($autoscroll.exists()) {
				utilitiesScrollTo($autoscroll.first());
			}
		};

		var initRecaptcha = function() {
			if (typeof window['grecaptcha'] == 'object' && typeof window['grecaptcha']['render'] == 'function') {
				recaptchaLoaded = true;
				utilitiesRecaptcha();
			} else {
				if (new Date().getTime() - recaptchaInit < 5000) {
					setTimeout(initRecaptcha, 50);
				}
			}
		};

		var initLocaleSwitcher = function() {
			$('[data-locale]').on('click', function(e) {
				var locale = $(this).attr('data-locale');
				if (locale) {
					var domain = window.location.host;
					if ((domain.match(/[.]/g) || []).length > 1) {
						domain = domain.substring(domain.indexOf('.') + 1);
					}
					$.cookie('kt_lang', locale, {domain: domain, expires: 365, path: '/', samesite: 'Lax'});
				}
				if (!$(this).attr('href')) {
					e.preventDefault();
					window.location.reload();
				}
			});
		};

		var initMethods = [
			initMenu,
			initTabs,
			initFancyBox,
			initRating,
			initAddToFavourites,
			initErrorFlaggingForm,
			initShareForm,
			initCommentForm,
			initCommentActions,
			initAjaxLists,
			initAjaxForms,
			initSubscriptions,
			initSearch,
			initVideoUploadForm,
			initProfile,
			initMessages,
			initStats,
			initAutoscroll,
			initRecaptcha,
			initLocaleSwitcher
		];

		for (var i = 0; i < initMethods.length; i++) {
			if (typeof initMethods[i] == 'function') {
				try {
					initMethods[i].call(this);
				} catch (e) {
					if (console && console.error) {
						console.error(e);
					}
				}
			}
		}
	})();
});