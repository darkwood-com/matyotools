/**
 * TimeSheet control
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

TimeSheet.TimeSheet = Class.create();
TimeSheet.TimeSheet.prototype = Object.extend(new Core.ChickenFoot(), {
	/**
	 * @post contructor
	 */
	initialize: function() {		
		//Core.Url time sheet url path
		this.m_oTimeSheetUrl  = new Core.Url("http://temps.les-argonautes.com/", true);		
	},
	
	/**
	 * @post enter into time sheet
	 */
	login: function() {
		this.go(this.m_oTimeSheetUrl);
		
		this.click(new Core.XPath("//input[@type='submit']"));
		this.click(new Core.XPath("//td[@title='Feuilles de temps']"));
		
		this.sleep(5.0);
	},
	
	/**
	 * @pre call add(sTaskHour, sTaskMin, sClientOrArray[Array("client":String,"file":String,"task":String])
	 		  or add(sTaskHour, sTaskMin, sClientOrArray[String], sFile[String], sTask[String])
	 * @post add a task
	 * @param String sTaskHour
	 * @param String sTaskMin
	 * @param Array("client":String,"file":String,"task":String) sClientOrArray
	 * @param int iDay
	 */
	add: function(sTaskHour, sTaskMin, oTaskArray, iDay) {
		if(iDay==undefined) {
			iDay = 0;
		}
		
		this.sleep(1.0);
		
		//enter the date
		var oDate = new Date();
		oDate.addDays(iDay);
		this.enter(new Core.XPath("//input[@name='JourDateSaisie']"), oDate.toString('dd'));
		this.enter(new Core.XPath("//input[@name='MoisDateSaisie']"), oDate.toString('MM'));
		this.enter(new Core.XPath("//input[@name='AnneeDateSaisie']"), oDate.toString('yyyy'));
		
		//enter the time
		this.enter(new Core.XPath("//input[@id='txtHeureDebutH']"), sTaskHour);
		this.enter(new Core.XPath("//input[@id='txtHeureDebutM']"), sTaskMin);
		
		//enter the task
		this.pick(new Core.XPath("//select[@id='ddlCodeClient']"), oTaskArray["client"]);
		this.sleep(1.0);
		this.pick(new Core.XPath("//select[@id='ddlDossier']"), oTaskArray["file"]);
		this.sleep(1.0);
		this.pick(new Core.XPath("//select[@id='ddlTache']"), oTaskArray["task"]);
		
		//validate
		this.click(new Core.XPath("//img[@alt='Ajouter']"));
	}
});