<?php

	// Current version of GLPI
	define("GLPI_VERSION","0.7");

	// TIMES
	define("MINUTE_TIMESTAMP",60);
	define("HOUR_TIMESTAMP",3600);
	define("DAY_TIMESTAMP",86400);
	define("WEEK_TIMESTAMP",604800);
	define("MONTH_TIMESTAMP",2592000);

	// CACHE CONTROL
	define("ENABLE_CACHE",true);
	define("DEFAULT_CACHE_LIFETIME",WEEK_TIMESTAMP);
	// Max cache size in octet : default 20 Mb
	define("MAX_CACHE_SIZE",20000000);
	define("CACHE_FILELOCKINGCONTROL",true);
	define("CACHE_READCONTROL",true);
	define("CACHE_WRITECONTROL",true);



	// ITEMS TYPE
	define("GENERAL_TYPE",0);//
	define("COMPUTER_TYPE",1);//
	define("NETWORKING_TYPE",2);//
	define("PRINTER_TYPE",3);//
	define("MONITOR_TYPE",4);//
	define("PERIPHERAL_TYPE",5);//
	define("SOFTWARE_TYPE",6);//
	define("CONTACT_TYPE",7);//
	define("ENTERPRISE_TYPE",8);//
	define("INFOCOM_TYPE",9);//
	define("CONTRACT_TYPE",10);//
	define("CARTRIDGE_TYPE",11);//
	define("TYPEDOC_TYPE",12);
	define("DOCUMENT_TYPE",13);//
	define("KNOWBASE_TYPE",14);//
	define("USER_TYPE",15);//
	define("TRACKING_TYPE",16);//
	define("CONSUMABLE_TYPE",17);//
	define("CONSUMABLE_ITEM_TYPE",18);
	define("CARTRIDGE_ITEM_TYPE",19);
	define("LICENSE_TYPE",20);
	define("LINK_TYPE",21);
	define("STATE_TYPE",22);
	define("PHONE_TYPE",23);//
	define("DEVICE_TYPE",24);
	define("REMINDER_TYPE",25);
	define("STAT_TYPE",26);
	define("GROUP_TYPE",27);
	define("ENTITY_TYPE",28);
	define("RESERVATION_TYPE",29);
	define("AUTH_MAIL_TYPE",30);
	define("AUTH_LDAP_TYPE",31);
	define("OCSNG_TYPE",32);
	define("REGISTRY_TYPE",33);

	// GLPI MODE
	define("NORMAL_MODE",0);
	define("TRANSLATION_MODE",1);
	define("DEBUG_MODE",2);
	define("DEMO_MODE",3);

	// DEVICE TYPE
	define("MOBOARD_DEVICE",1);
	define("PROCESSOR_DEVICE",2);
	define("RAM_DEVICE",3);
	define("HDD_DEVICE",4);
	define("NETWORK_DEVICE",5);
	define("DRIVE_DEVICE",6); 
	define("CONTROL_DEVICE",7);
	define("GFX_DEVICE",8);
	define("SND_DEVICE",9);
	define("PCI_DEVICE",10);
	define("CASE_DEVICE",11);
	define("POWER_DEVICE",12);

	// DEVICE INTERNAL ACTION
	define("HISTORY_ADD_DEVICE",1);
	define("HISTORY_UPDATE_DEVICE",2);
	define("HISTORY_DELETE_DEVICE",3);
	define("HISTORY_INSTALL_SOFTWARE",4);
	define("HISTORY_UNINSTALL_SOFTWARE",5);

	// OCSNG TYPES
	define("HARDWARE_FL",0);
	define("BIOS_FL",1);
	define("MEMORIES_FL",2);
	define("SLOTS_FL",3);
	define("REGISTRY_FL",4);
	define("CONTROLLERS_FL",5);
	define("MONITORS_FL",6);
	define("PORTS_FL",7);
	define("STORAGES_FL",8);
	define("DRIVES_FL",9);
	define("INPUTS_FL",10);
	define("MODEMS_FL",11);
	define("NETWORKS_FL",12);
	define("PRINTERS_FL",13);
	define("SOUNDS_FL",14);
	define("VIDEOS_FL",15);
	define("SOFTWARES_FL",16);

	define("MAX_OCS_CHECKSUM",131071);



	// MAILING TYPE
	define("USER_MAILING_TYPE",1);
	define("PROFILE_MAILING_TYPE",2);
	define("GROUP_MAILING_TYPE",3);

	// MAILING USERS TYPE
	define("ADMIN_MAILING",1);
	define("ASSIGN_MAILING",2);
	define("AUTHOR_MAILING",3);
	define("OLD_ASSIGN_MAILING",4);
	define("TECH_MAILING",5);
	define("USER_MAILING",6);

	// EXPORT TYPE
	define("GLOBAL_SEARCH",-1);
	define("HTML_OUTPUT",0);
	define("SYLK_OUTPUT",1);
	define("PDF_OUTPUT",2);

	// HELPDESK LINK HARDWARE DEFINITION : CHECKSUM SYSTEM : BOTH=1*2^0+1*2^1=3
	define("HELPDESK_MY_HARDWARE",0);
	define("HELPDESK_ALL_HARDWARE",1);

	// ALERTS TYPE
	define("ALERT_THRESHOLD",1);
	define("ALERT_END",2);
	define("ALERT_NOTICE",3);

	// Default number of items displayed in global search
	define("GLOBAL_SEARCH_DISPLAY_COUNT",10);
	
	//REGISTRY HIVE
	$REGISTRY_HIVE=array("HKEY_CLASSES_ROOT",
	"HKEY_CURRENT_USER",
	"HKEY_LOCAL_MACHINE",
	"HKEY_USERS",
	"HKEY_CURRENT_CONFIG",
	"HKEY_DYN_DATA");

	$LINK_ID_TABLE=array(
			COMPUTER_TYPE=> "glpi_computers",
			NETWORKING_TYPE => "glpi_networking",
			PRINTER_TYPE => "glpi_printers",
			MONITOR_TYPE => "glpi_monitors",
			PERIPHERAL_TYPE => "glpi_peripherals",
			SOFTWARE_TYPE => "glpi_software",
			CONTACT_TYPE => "glpi_contacts",
			ENTERPRISE_TYPE => "glpi_enterprises",
			INFOCOM_TYPE => "glpi_infocoms",
			CONTRACT_TYPE => "glpi_contracts",
			CARTRIDGE_TYPE => "glpi_cartridges_type",
			TYPEDOC_TYPE => "glpi_type_docs",
			DOCUMENT_TYPE => "glpi_docs",
			KNOWBASE_TYPE => "glpi_kbitems",
			USER_TYPE => "glpi_users",
			TRACKING_TYPE => "glpi_tracking",
			CONSUMABLE_TYPE => "glpi_consumables_type",
			CONSUMABLE_ITEM_TYPE => "glpi_consumables",
			CARTRIDGE_ITEM_TYPE => "glpi_cartridges",
			LICENSE_TYPE => "glpi_licenses",
			LINK_TYPE => "glpi_links",
			STATE_TYPE => "",
			PHONE_TYPE => "glpi_phones",
			DEVICE_TYPE => "???",
			REMINDER_TYPE => "glpi_reminder",
			STAT_TYPE => "???",
			GROUP_TYPE => "glpi_groups",
			ENTITY_TYPE => "glpi_entities",
			RESERVATION_TYPE => "glpi_reservation_item",
			OCSNG_TYPE => "glpi_ocs_config",
			REGISTRY_TYPE => "glpi_registry",
			);

	$INFOFORM_PAGES=array( 
			COMPUTER_TYPE=> "front/computer.form.php",
			NETWORKING_TYPE => "front/networking.form.php",
			PRINTER_TYPE => "front/printer.form.php",
			MONITOR_TYPE => "front/monitor.form.php",
			PERIPHERAL_TYPE => "front/peripheral.form.php",
			SOFTWARE_TYPE => "front/software.form.php",
			CONTACT_TYPE => "front/contact.form.php",
			ENTERPRISE_TYPE => "front/enterprise.form.php",
			INFOCOM_TYPE => "front/infocom.form.php",
			CONTRACT_TYPE => "front/contract.form.php",
			CARTRIDGE_TYPE => "front/cartridge.form.php",
			TYPEDOC_TYPE => "front/typedoc.form.php",
			DOCUMENT_TYPE => "front/document.form.php",
			KNOWBASE_TYPE => "front/knowbase.form.php",
			USER_TYPE => "front/user.form.php",
			TRACKING_TYPE => "front/tracking.form.php",
			CONSUMABLE_TYPE => "front/consumable.form.php",
			CONSUMABLE_ITEM_TYPE => "??",
			CARTRIDGE_ITEM_TYPE => "??",
			LICENSE_TYPE => "??",
			LINK_TYPE => "front/link.form.php",
			STATE_TYPE => "??",
			PHONE_TYPE => "front/phone.form.php",
			DEVICE_TYPE => "???",
			REMINDER_TYPE => "front/reminder.form.php",
			STAT_TYPE => "???",
			GROUP_TYPE => "front/group.form.php",
			ENTITY_TYPE => "front/entity.form.php",
			RESERVATION_TYPE => "???",
			OCSNG_TYPE => "front/ocsng.form.php",
			REGISTRY_TYPE => "???",
			);

	define("AUTH_CAS",0);
	define("AUTH_DB_GLPI",1);
	define("AUTH_MAIL",2);
	define("AUTH_LDAP",3);
	define("NOT_YET_AUTHENTIFIED",-1);



?>