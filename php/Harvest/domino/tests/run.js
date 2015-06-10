module.exports = {
	'Test domino' : function (browser) {
		var params = browser.globals.test_settings.globals;

		browser
			.url('https://dominoweb.domino-info.fr:7001/cgiphl/pw_main.pgm')
			.waitForElementVisible('body', 1000)
			.setValue('input[name="name1"]', params.user)
			.setValue('input[name="name2"]', params.password)
			.waitForElementVisible('input[type="button"][value="OK"]', 1000)
			.click('input[type="button"][value="OK"]')
			.pause(1000)
			.assert.containsText('#main', 'Night Watch')
			.end();
	}
};
