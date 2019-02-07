$(function() {
	$('form:not(.novalidate)').submit(function() {
		var form = $(this);
		var submit = false;
		var errors = [];
		var missing = false;
		var formData = false;
		var firstField = $('input[name="first"]', $(this));
		var lastField = $('input[name="last"]', $(this))
		var emailField = $('input[name="email"]', $(this));
		if (firstField.length) {
			var first = $.trim(firstField.val());
			if (first == '') {
				missing = true;
			}
		}
		if (lastField.length) {
			var last = $.trim(lastField.val());
			if (last == '') {
				missing = true;
			}
		}
		if (emailField.length) {
			var email = $.trim(emailField.val());
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if (!regex.test(email)) {
				errors.push('Please enter a valid email');
			}
		}
		$('.required', $(this)).each(function() {
			if ($(this).attr('type') == 'checkbox') {
				if ($(this).attr('checked') != 'checked') {
					missing = true;
				}
			} else {
				if ($.trim($(this).val()) == '') {
					missing = true;
				}
			}
		});
		if (missing) {
			errors.push('You are missing required fields');
			errors.push('Please check your form and make sure all required field are filled out');
		}
		if (errors.length > 0) {
			alert(errors.join("\n"));
		} else {
			if ($(this).parent().hasClass('popForm')) {
				var pfContainer = $(this).parent();
				var pfSurvey = pfContainer.hasClass('survey');
				formData = $(this).serialize();
				formData += '&formSubmit=true';
				if (pfSurvey) {
					formData += '&_survey=' + pfContainer.attr('id');
				}
				$.ajax({
					type: 'POST',
					url: '/api/processForm',
					data: formData,
					success: function(response) {
						if (response['success']) {
							if (typeof(ga) !== 'undefined') {
								if (pfSurvey) {
									ga('send', 'pageview', 'survey/submitted/' + pfContainer.attr('id'));
									//_gaq.push(['_trackPageview', 'survey/submitted/' + pfContainer.attr('id')]);
								} else {
									ga('send', 'pageview', 'popForm/submitted/' + window.location.pathname);
									//_gaq.push(['_trackPageview', 'popForm/submitted/' + window.location.pathname]);
								}
							}
							if (pfSurvey) {
								if ($('.confirmation', pfContainer).length) {
									pfContainer.children().hide();
									$('.confirmation', pfContainer).show();
								} else {
									alert(response['msg'].join("\n"));
								}
								pfContainer.fadeOut(5000);
							} else {
								if ($('.confirmation', pfContainer).length) {
									pfContainer.children().hide();
									$('.confirmation', pfContainer).show();
								} else {
									alert(response['msg'].join("\n"));
								}
								pfContainer.fadeOut(5000, 'swing', function() {
									pfContainer.children().show();
									$('.confirmation', pfContainer).hide();
									form.find('input[type=text], textarea').val('');
									form.find('option:selected').prop('selected', false);
									form.find('input[type=radio], input[type=checkbox]').prop('checked', false);
									form.parent().hide();
									reloadCaptcha();
								});
							}
						} else {
							alert(response['errors'].join("\n"));
						}
					}
				});
			}
		}
		return submit;
	});
	if ($('ul#topNav').length) {
		var topWidth, subWidth, subDim;
		$('ul#topNav li.topNavItem ul.subNav li.subNavItem ul.subNav2').each(function() {
			$(this).parent().addClass('hasSub').parent().css('display', 'block');
			$(this).css('display', 'block');
			topWidth = $(this).parent().parent().width();
			subWidth = $(this).parent().children(':first-child').width();
			if (subWidth + 30 < topWidth) {
				subWidth = topWidth;
			} else {
				subWidth += 30;
			}
			if ($(this).prev().hasClass('subNav2')) {
				subWidth += $(this).prev().width();
			}
			$(this).css('left', subWidth + 'px');
			$(this).css('display', '');
			$(this).parent().parent().css('display', '');
		});
		$('ul#topNav li.topNavItem').mouseover(function() {
			$(this).addClass('active');
		}).mouseout(function() {
			$(this).removeClass('active');
		});
		$('ul#topNav li.topNavItem ul.subNav li.subNavItem').mouseover(function() {
			$(this).addClass('active');
		}).mouseout(function() {
			$(this).removeClass('active');
		});
		$('ul#topNav li.topNavItem ul.subNav li.subNavItem ul.subNav2 li').mouseover(function() {
			$(this).addClass('active');
		}).mouseout(function() {
			$(this).removeClass('active');
		});
		$('a.topNav[href="' + window.location.pathname + '"]').parent().addClass('current');
	}
	$('#currentLanguage').click(function() {
		$('#languageDropdown').show();
	});
	$('#languageDropdown').mouseleave(function() {
		$(this).hide();
	});
	if ($('div.popForm').length) {
		$('div.popForm').append('<a href="javascript: void(0);" class="closePopForm">x</a>');
		$('a.popForm').click(function() {
			if (typeof(ga) !== 'undefined') {
				ga('send', 'pageview', 'popForm/' + window.location.pathname);
				//_gaq.push(['_trackPageview', 'popForm/' + window.location.pathname]);
			}
			$($(this).attr('href')).show().center();
		});
		$('a.closePopForm').click(function() {
			$(this).parent().hide();
		});
	}
	var survey = $('.survey');
	if (survey.length) {
		survey.each(function() {
			var svy = $(this);
			var id = svy.attr('id')
			var done = readCookie('survey-' + id);
			$('.confirmation', svy).hide();
			if (!done) {
				setTimeout(function() {
					if (typeof(ga) !== 'undefined') {
						ga('send', 'pageview', 'survey/' + id);
						//_gaq.push(['_trackPageview', 'survey/' + id]);
					}
					svy.show().center();
				}, 2500);
				createCookie('survey-' + id, true, 365);
			}
		});
	}
	var printFrame = $('.printFrame');
	if (printFrame.length) {
		var printContent, printWindow;
		printFrame.each(function() {
			$(this).click(function() {
				printContent = $('#' + $(this).attr('rel')).html();
				printWindow = window.open('', '', ',width=500');
				printWindow.document.write('<html>');
				printWindow.document.write('<head>');
				printWindow.document.write('<style>');
				printWindow.document.write('h3 {font-size: 14px; margin: 0}');
				printWindow.document.write('table {font-size: 12px;}');
				printWindow.document.write('input {border: 1px solid #000000; margin-left: 10px;}');
				printWindow.document.write('textarea {border: 1px solid #000000;}');
				printWindow.document.write('</style>');
				printWindow.document.write('</head>');
				printWindow.document.write('<body>');
				printWindow.document.write(printContent);
				printWindow.document.write('</body>');
				printWindow.document.write('</html>');
				printWindow.print();
				printWindow.close();
			});
		});
	}
	$('a[href$=".pdf"]').attr('target', '_blank');
	$('a.reloadCaptcha').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		reloadCaptcha();
	});
});

function createCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = '; expires=' + date.toGMTString();
	} else {
		var expires = ''
	}
	document.cookie = name + '=' + value + expires + '; path=/';
}

function readCookie(name) {
	var nameEQ = name + '=';
	var ca = document.cookie.split(';');
	for(var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1, c.length);
		}
		if (c.indexOf(nameEQ) == 0) {
			return c.substring(nameEQ.length, c.length);
		}
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name, '', -1);
}

function reloadCaptcha() {
	d = new Date();
	$('.captcha img').attr('src', '/captcha.php?' + d.getTime());
}
