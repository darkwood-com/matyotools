module.exports = {
	'Test domino' : function (browser) {
		var params = browser.globals.test_settings.globals,
			wait = 5000;

		browser

			// login
			.url('https://dominoweb.domino-info.fr:7001/cgiphl/pw_main.pgm')
			.waitForElementVisible('body', wait)
			.setValue('input[name="name1"]', params.user)
			.setValue('input[name="name2"]', params.password)
			.waitForElementVisible('input[type="button"][value="OK"]', wait)
			.click('input[type="button"][value="OK"]')

            // timesheet
			.waitForElementVisible('#menu_3', wait)
			.click('#menu_3')
			.waitForElementPresent('#href_1', wait)
			.getAttribute('#href_1', 'src', function(data) {
				console.log('Url: ' + data.value);

				//close tab
				this.click('#tabdesk td[class="tdesk_close"]')
					.waitForElementNotPresent('#tabdesk td[class="tdesk_close"]', wait)

				// timesheet iframe
				this.init(data.value)
					.waitForElementVisible('body', wait)

					// client
					.click('#f1_28_btn')
					.waitForElementPresent('#f1_28_select', wait)
					.click('#f1_28_select option[value="24003603"]')

					// dossier
					.click('#f1_30_btn')
					.waitForElementPresent('#sfl2_4', wait)
					.click('#sfl2_4')
					.pause('1000')

					.doubleClick('#sfl2_4')

					.pause('5000')

					// add row
					.injectScript('pw_fnkey(\'10\',0,0,0);')
			})
			.pause(wait)
			.end();
	}
};
