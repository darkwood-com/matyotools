$(window).load(function() {
	$('#cards .user-card').click(function() {
		var $self = $(this);
		var slug = $self.data('slug');
		var isGolden = $self.data('isGolden');

        $.ajax({
            url: Routing.generate('user_card', { slug: slug, isGolden: isGolden ? '1' : '0' }),
            success: function(data) {
                $self.html(data);
            }
        });
	});
});
