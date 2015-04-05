$(window).load(function() {
	var $cards = $('#cards');
	$cards.find('.user-card').click(function() {
		var $self = $(this);
        var source = $self.data('source');
		var slug = $self.data('slug');
		var isGolden = $self.data('isGolden');

        $.ajax({
            url: Routing.generate('user_card', { source: source, slug: slug, isGolden: isGolden ? '1' : '0' }),
            success: function(data) {
                $self.html(data);
            }
        });
	});

	$('[data-toggle="popover"]').popover({
		trigger: 'hover',
		placement: 'auto'
	});

    $('form.form-auto-submit :input').on('change', function() {
        $(this).closest('form').submit();
    });
});
