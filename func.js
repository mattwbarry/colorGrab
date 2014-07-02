$(document).ready(function() {

	$('#uploadSubmit').on('click', function() {
		submitImg()
	})
	$('#uploadFile').on('keyup', function(e) {
		if (e.which == 13) {
			submitImg()
		}
	})

	//change background color to match color hovered
	$('#responseWrapper').on('hover', '.colorBlock', function(e) {
		$('body').css('background', $(this).css('background'))

		$('#colorHolder').html('#' + $(this).attr('data-color'))
		$('#colorHolder').css({
			'display': 'block',
			'top': $(this).offset().top - 35,
			'left': $(this).offset().left + ($(this).width() / 2) - ($('#colorHolder').width() / 2 + 10) - 1
		})
	})
	$('#responseWrapper').on('mouseout', function() {
		$('#colorHolder').css({
			'display': 'none'
		})
	})

	function submitImg() {
		$.ajax({
			type: 'POST',
			url: 'colorGrab.php',
			data: {
				'filename': $('#uploadFile').val(),
				'splitByRC': $('#optionsRows').prop('checked'),
				'numRows': $('#optionSelectRows').val(),
				'numCols': $('#optionSelectCols').val(),
				'splitByBlock': $('#optionsBlocks').prop('checked'),
				'blockSize': $('#optionSelectSize').val()
			},
			success: function(data) {
				$('#responseWrapper').html(data)

				var arrColorBlock = document.getElementsByClassName('colorBlock');
				var j = 0
				for (var i = 0; i < arrColorBlock.length; i++) {
					setTimeout(function() {
						arrColorBlock[j].style.opacity = 1.0;
						//arrColorBlock[j].style.right = '0px';
						j++
					}, i * 2)
				}

			}
		})
	}

})
