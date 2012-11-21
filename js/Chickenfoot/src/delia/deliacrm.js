/**
 * Deliacrm control in Delia
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

Delia.Deliacrm = Class.create();
Delia.Deliacrm.prototype = Object.extend(new Delia.Delia(), {
	/**
	 * @post contructor
	 * @param String sPage is the database page name
	 * @param String sName is the name of the database in the menu
	 */
	initialize: function() {
		//constant values
		this.TYPE_FIELD = new Object();
		this.TYPE_FIELD.SHORT_TEXT 			= "Short text";
		this.TYPE_FIELD.LONG_TEXT 			= "Long text";
		this.TYPE_FIELD.YES_NO 				= "Yes / No";
		this.TYPE_FIELD.NUMERIC 			= "Numeric";
		this.TYPE_FIELD.MONETARY 			= "Monetary";
		this.TYPE_FIELD.FILE 				= "File";
		this.TYPE_FIELD.ITEM 				= "Item";
		this.TYPE_FIELD.SEQUENCE 			= "Sequence";
		this.TYPE_FIELD.EXCLUSIVE_CHOISE 	= "Exclusive choice";
		this.TYPE_FIELD.MULTIPLE_CHOISE 	= "Multiple choice";
	},
	
	/**
	 * @post create a new field
	 * @param String sID is the identifier name of the field
	 * @param String sName is the name of the field
	 * @param String sType (optional)[default = TYPE_FIELD.SHORT_TEXT])
	 */
	newField: function(sID, sName, sType) {
		if(sType==undefined) {
			sType = this.TYPE_FIELD.SHORT_TEXT;
		}
		
		this.tab("Fields");
		this.toolbar("New field");
		this.input("Identifier", sID);
		this.input("Name", sName);
		this.input("Type", sType, this.INPUT.SELECT);
		this.tab("Save");
	},
	
	newItem: function(sName) {
		this.toolbar("New item");
		this.input("Name", sName);
		this.tab("Save");
		this.tab("Item list");
	}
});