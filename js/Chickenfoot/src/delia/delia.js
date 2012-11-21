/**
 * Delia control
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

Delia.Delia = Class.create();
Delia.Delia.prototype = Object.extend(new Core.ChickenFoot(), {
	/**
	 * @post contructor
	 */
	initialize: function() {
		//Core.ChickenFoot chicken foot parent instance
		this.parent = new Core.ChickenFoot();

		//constant values
		this.INPUT = new Object();
		this.INPUT.TEXT 		= "input.text";
		this.INPUT.RADIO		= "input.radio";
		this.INPUT.SELECT 		= "input.select";
		this.INPUT.CHECKBOX 	= "input.checkbox";
		this.INPUT.TEXTAREA		= "input.textarea";
		
		//Core.Url delia url path
		this.m_oDeliaUrl  = new Core.Url("/delia/index.php");		
	},
	
	/**
	 * @post go to delia page
	 * @param String sPage is the page name
	 */
	go: function(sPage) {
		this.parent.go(this.m_oDeliaUrl.add("?page=" + sPage));
	},

	/**
	 * @post wait until sPath is found on the page
	 		 (the function abord waiting if it wait to much)
	 * @param Pattern sPath is the path to find
	 * @iIndex Int (optional)[default = 0]
	 * @return MatchPointer
	 */
	waitForPath: function(sPath, iIndex) {
		var iTest = 10;
		while(iTest > 0)
		{
			var oMatchesPtr = this.find(sPath);
			
			if(! $('deliaminiloading').visible()) {
				break;
			}
			this.sleep(this.m_fSleepTime);
			this.output("waiting delia finishing the current request...");
			iTest--;
		}
		
		return this.parent.waitForPath(sPath, iIndex);
	},

	/**
	 * @post search elements sChildXPath into the sZoneID
	 * @param String sZoneID is the zone where to search
	 * @param String sChildXPath is the child XPath of sZoneID
	 * @return XPath
	 */
	path: function(sZoneID, sChildXPath) {
		var sZoneIDArray = {	
			"sidebar":		"delia5.central.sidebar",
			"tabs":			"delia5.central.content.tabs",
			"tab_content":	"delia5.central.content.tab_content",
			"toolbar":		"delia5.central.content.tab_content.toolbar"
		}
		
		//concatene all sub zoneID
		var oZoneXPath = new Core.XPath("");
		var sZoneSubIDArray=sZoneIDArray[sZoneID].split(".");
		for (var i=0; i<sZoneSubIDArray.length; i++) {
			oZoneXPath = oZoneXPath.add("//div[@id='"+sZoneSubIDArray[i]+"']");
		}
		
		return oZoneXPath.add(sChildXPath);
	},
	
	/**
	 * @post click on menu rubrick that name contains sName
	 * @param String sName is the rubrick name
	 */
	menu: function(sName) {
		var oPath = this.path("sidebar", "//span[@class='mif-tree-name'][contains(text(), '"+sName+"')]");
		//mouse over the menu item, then click on the menu item
		var oMatchesPtr = this.waitForPath(oPath);
		this.fireMouseEvent('mouseover', oMatchesPtr.element);
		return this.click(oPath);
	},
	
	/**
	 * @post click on tab that name contains sName
	 * @param String sName is the tab name
	 */
	tab: function(sName) {
		return this.click(this.path("tabs", "/*[contains(text(), '"+sName+"')]"));
	},
	
	/**
	 * @post click on toolbar button that name contains sName
	 * @param String sName is the toolbar button name
	 */
	toolbar: function(sName) {
		return this.click(this.path("toolbar", "/*[contains(text(), '"+sName+"')]"));
	},

	/**
	 * @post click on element in the list that name contains sName
	 * @param String sColumn is the name of the column
	 * @param String sName in the column of the list
	 */
	list: function(sColumn, sName) {
		//sleep a little before executing this.find() function
		this.sleep(this.m_fSleepTime);
		
		var oPathToTable = this.path("tab_content", "//table[@class='liste_items']");
		var oPathToColumn = oPathToTable.add("/thead/tr");
		var oMatcheColumns = this.find(oPathToColumn.add("/th"));
		
		//search the first indice of the column named sColumn
		var iColumnIndex = 0;
		for(var iColumnIndex=1; iColumnIndex<oMatcheColumns.count; iColumnIndex++) {
			var oColumn = this.find(oPathToColumn.add("/th["+iColumnIndex+"][contains(descendant-or-self::node(), '"+sColumn+"')]"));
			if(oColumn.hasMatch) {
				break;
			}
		}
		
		//click on the list where we have sName in the sColumn
		var oPathToRows = oPathToTable.add("/tbody/tr");
		
		return this.click(oPathToRows.add("/td["+iColumnIndex+"][contains(descendant-or-self::node(), '"+sName+"')]"));
	},
	
	/**
	 * @post enter sValue on text input, with label that contains sName
	 * @param sName
	 * @param sValue
	 * @param sType (optional)[default = FIELD.TEXT, FIELD.RADIO, INPUT.SELECT, INPUT.CHECKBOX]
	 */
	input: function(sName, sValue, sType) {
		if(sType==undefined) {
			sType = this.INPUT.TEXT;
		}
		
		if(sType == this.INPUT.TEXT) {
			return this.enter(this.path("tab_content", "//label[contains(text(), '"+sName+"')]/../..//input[@type='text']"), sValue);
		} else if(sType == this.INPUT.RADIO) {
			return this.click(this.path("tab_content", "//label[contains(text(), '"+sName+"')]/../..//input[@type='radio'][../span[contains(text(), '"+sValue+"')]]"));
		} else if(sType == this.INPUT.SELECT) {
			return this.pick(this.path("tab_content", "//label[contains(text(), '"+sName+"')]/../..//select"), sValue);
		} else if(sType == this.INPUT.CHECKBOX) {
			return this.check(this.path("tab_content", "//label[contains(text(), '"+sName+"')]/../..//input[@type='checkbox']"), sValue);
		} else if(sType == this.INPUT.TEXTAREA) {
			return this.enter(this.path("tab_content", "//label[contains(text(), '"+sName+"')]/../..//textarea"), sValue);
		}
	},
	
	/**
	 * @post drag item to drop item
	 * @param sAction action like 'method1_to_method2'
	 * @param oParams params like [[param list to method 1], [param list to method 2]]
	 */
	dradAndDrop: function(sAction, oParams) {
		var oActions = sAction.split('_to_');
		if(oActions.length == 2 && oParams.length == 2)
		{
			this.m_sMode = 'silence'; //do not make actions, just retrives the paths
			
			//call action from this object methods with params
			var oMatchesSrcPtr = this[oActions[0]].apply(this, oParams[0]);
			var oMatchesDestPtr = this[oActions[1]].apply(this, oParams[1]);
			
			this.m_sMode = 'action'; //after, make actions as normal
			
			return this.parent.dradAndDrop(oMatchesSrcPtr, oMatchesDestPtr);
		}
		else
		{
			return this.parent.dradAndDrop(oParams[0], oParams[1]);
		}
	}
});