module.exports = {
	'Test domino' : function (browser) {
		var params = browser.globals.test_settings.globals;

        var wait = 5000,
			saisieDesTempsPath = '#menu_3',
			frameSaisieDesTempsPath = "#href_1",
			clientPath = "#f1_28_sel",
			dossierPath = "#f1_30_phl",
			buttonAddPath = "#f1_pw_btndyn_201";

		browser
			.url('https://dominoweb.domino-info.fr:7001/cgiphl/pw_main.pgm')
			.waitForElementVisible('body', wait)
			.setValue('input[name="name1"]', params.user)
			.setValue('input[name="name2"]', params.password)
			.waitForElementVisible('input[type="button"][value="OK"]', wait)
			.click('input[type="button"][value="OK"]')
            .waitForElementVisible(saisieDesTempsPath, wait)
			.click(saisieDesTempsPath)
			.waitForElementPresent(frameSaisieDesTempsPath, wait)
			.getAttribute(frameSaisieDesTempsPath, "src", function(data) {
				console.log(data.value);
				this.url(data.value);
				/*this.waitForElementPresent(clientPath, wait)
					.waitForElementPresent(dossierPath, wait)
					.setValue(clientPath, "AEGE GROUPE/Plan de Communication/Sté 07")
					.setValue(dossierPath, "78282")
					.click(buttonAddPath);*/
			})
			.pause(wait)
			.end();
	}
};
