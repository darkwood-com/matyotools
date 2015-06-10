module.exports = {
	'Test domino' : function (browser) {
		var params = browser.globals.test_settings.globals;

        var wait = 5000, saisieDesTempsPath = '#menu_3';

		browser
			.url('https://dominoweb.domino-info.fr:7001/cgiphl/pw_main.pgm')
			.waitForElementVisible('body', wait)
			.setValue('input[name="name1"]', params.user)
			.setValue('input[name="name2"]', params.password)
			.waitForElementVisible('input[type="button"][value="OK"]', wait)
			.click('input[type="button"][value="OK"]')
            .waitForElementVisible(saisieDesTempsPath, wait)
			.click(saisieDesTempsPath)
            .pause(wait)
			.end();
	}
};
