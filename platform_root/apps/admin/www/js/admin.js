$(function() {
	$('.editMenuOption').mouseover(function() {
		$(this).addClass('editMenuOptionOver');
	}).mouseout(function() {
		$(this).removeClass('editMenuOptionOver');
	}).click(function() {
		$('.editMenuOption').removeClass('selected');
		$(this).addClass('selected');
		$('.propertyContainer').addClass('hidden');
		$('#' + $(this).attr('id') + 'Container').removeClass('hidden');
		$('#propertyMenuItem').val($(this).attr('id'));
	});
	$('#manageFiles').click(function(e) {
		e.stopPropagation();
		e.preventDefault();
		window.open('/kcfinder/browse.php', 'File Manager', 'width=800,height=500');
	});
	if ($('#editForm').length) {
		var editForm = $('#editForm');
		var formAction = editForm.attr('ACTION');
		var previewAction = mainURL + '/previewContent';
		$('#preview').show();
		$('#update').click(function() {
			editForm.attr('target', '_self');
			editForm.attr('ACTION', formAction);
		});
		$('#preview').click(function() {
			editForm.attr('target', '_blank');
			editForm.attr('ACTION', previewAction);
		});
	}
	if ($('#overviewForm').length) {
		var selectToggle = false;
		var recordOverviewActionTrigger = $('#recordOverviewActionTrigger');
		var input, option;
		$('#selectToggle').click(function() {
			if (selectToggle) {
				$(this).removeClass('deselectAll').addClass('selectAll').attr('title', 'Select All');
				$('#overviewForm input[name="selected[]"]').removeAttr('checked');
			} else {
				$(this).removeClass('selectAll').addClass('deselectAll').attr('title', 'Un-select All');
				$('#overviewForm input[name="selected[]"]').attr('checked', 'checked');
			}
			selectToggle = !selectToggle;
		});
		recordOverviewActionTrigger.click(function() {
			$('#recordOverviewActions').css('display', 'block').css('left', $(this).position().left).css('top', $(this).position().top);
		});
		$('#recordOverviewActionsClose').click(function() {
			$(this).parent().css('display', 'none');
		});
		$('#deleteSelected').click(function() {
			if (confirm('Proceed with delete action?')) {
			$('#recordOverviewActions').css('display', 'none');
				input = $('<input>').attr('type', 'hidden').attr('name', 'recordOverviewAction').val('deleteSelected');
				$('#overviewForm').append($(input)).submit();
			}
		});
		$('#duplicateToLanguage').click(function() {
			$('#duplicateToLanguageSelect').css('display', 'block').css('left', recordOverviewActionTrigger.position().left).css('top', recordOverviewActionTrigger.position().top);
			$('#recordOverviewActions').css('display', 'none');
		});
		$('#confirmDuplicateToLanguage').click(function() {
			input = $('<input>').attr('type', 'hidden').attr('name', 'recordOverviewAction').val('duplicateToLanguage');
			option = $('<input>').attr('type', 'hidden').attr('name', 'recordOverviewActionOption[languageID]').val($('#duplicateToLanguageSelect select option:selected').val());
			$('#overviewForm').append($(input)).append($(option)).submit();
		});
		$('#duplicateToLanguageSelectClose').click(function() {
			$(this).parent().css('display', 'none');
		});
	}
	if ($('.kcfinderSelect').length) {
		$('.kcfinderSelect').click(function() {
			openKCFinder($(this).prev());
		});
	}
	if ($('#addRecipient').length) {
		$('#addRecipient').click(function() {
			$('#recipientsTable').show();
			$('#recipientsTable tbody').append($('#recipientTemplate tbody').html());
			$('#recipientsNotes').show();
		});
		$('.removeRecipient').live('click', function() {
			$(this).parent().parent().remove();
		});
	}
});

function openKCFinder(input) {
	window.KCFinder = {
		callBack: function(url) {
			window.KCFinder = null;
			input.val(url.replace('/kcfinder/upload/files', ''));
		}
	};
	window.open('/kcfinder/browse.php', 'File Manager', 'width=800,height=500');
}
