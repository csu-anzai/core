<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$					    *
********************************************************************************************************/
//Edited with Kajona Language Editor GUI, see www.kajona.de and www.mulchprod.de for more information
//Kajona Language Editor Core Build 398

//non-editable entries
$lang["permissions_default_header"]      = array(0 => "Anzeigen", 1 => "Bearbeiten", 2 => "Löschen", 3 => "Rechte", 4 => "", 5 => "", 6 => "", 7 => "", 8 => "", 9 => "Changelog");
$lang["permissions_header"]              = array(0 => "Anzeigen", 1 => "Bearbeiten", 2 => "Löschen", 3 => "Rechte", 4 => "Einstellungen",  5 => "Systemtasks", 6 => "Systemlog", 7 => "", 8 => "Aspekte");
$lang["permissions_root_header"]         = array(0 => "Anzeigen", 1 => "Bearbeiten", 2 => "Löschen", 3 => "Rechte", 4 => "Universal 1", 5 => "Universal 2", 6 => "Universal 3", 7 => "Universal 4", 8 => "Universal 5", 9 => "Changelog");


//editable entries
$lang["_admin_nr_of_rows_"]              = "Anzahl Datensätze pro Seite";
$lang["_admin_nr_of_rows_hint"]          = "Anzahl an Datensätzen in den Admin-Listen, sofern das Modul dies unterstützt. Kann von einem Modul überschrieben werden!";
$lang["_admin_only_https_"]              = "Admin nur per HTTPS";
$lang["_admin_only_https_hint"]          = "Bevorzugt die Verwendung von HTTPS im Adminbereich. Der Webserver muss hierfür HTTPS unterstützen.";
$lang["_cookies_only_https_hint"]            = "Wenn aktiviert werden Cookies nur für https zugelassen. Nur sinnvoll wenn alle Daten per https abgerufen werden.";
$lang["_cookies_only_https_"]            = "Secure Cookies";
$lang["_remoteloader_max_cachetime_"]    = "Cachedauer externer Quellen";
$lang["_remoteloader_max_cachetime_hint"] = "Cachedauer in Sekunden für extern nachgeladene Inhalte (z.B. RSS-Feeds).";
$lang["_system_admin_email_"]            = "Admin E-Mail";
$lang["_system_admin_email_hint"]        = "Falls ausgefüllt, wird im Fall eines schweren Fehlers eine E-Mail an diese Adresse gesendet.";
$lang["_system_browser_cachebuster_"]    = "Browser-Cachebuster";
$lang["_system_browser_cachebuster_hint"] = "Dieser Wert wird als GET-Parameter allen Verweisen auf JS/CSS-Dateien angehängt. Durch hochzählen des Wertes kann der Browser dazu gezwungen werden die entsprechenden Dateien erneut vom Server herunter zu laden, unabhängig von den Caching-Einstellungen des Browsers und den vom Server gesendeten HTTP-Headern. Der Wert wird über den Systemtask 'Cache leeren' automatisch hochgezählt.";
$lang["_system_changehistory_enabled_"]  = "Änderungshistorie aktiv";
$lang["_system_dbdump_amount_"]          = "Anzahl DB-Dumps";
$lang["_system_dbdump_amount_hint"]      = "Definiert, wie viele Datenbank-Sicherungen vorgehalten werden sollen.";
$lang["_system_graph_type_"]             = "Verwendete Chart-Bibliothek: ";
$lang["_system_graph_type_hint"]         = "Gültige Werte: pchart, ezc, jqplot. pChart muss gesondern heruntergeladen und installiert werden, ezc benötigt im Optimalfall das PHP-Modul 'cairo'.<br />Siehe hierzu auch <a href=\"http://www.kajona.de/nicecharts.html\" target=\"_blank\">http://www.kajona.de/nicecharts.html</a>";
$lang["_system_lock_maxtime_"]           = "Maximale Sperrdauer";
$lang["_system_lock_maxtime_hint"]       = "Nach der angegebenen Dauer in Sekunden werden gesperrte Datensätze automatisch wieder freigegeben.";
$lang["_system_mod_rewrite_"]            = "URL-Rewriting";
$lang["_system_mod_rewrite_hint"]        = "Schaltet URL-Rewriting für Nice-URLs ein oder aus. Das Apache-Modul \"mod_rewrite\" muss dazu installiert sein und in der .htaccess-Datei aktiviert werden!";
$lang["_system_mod_rewrite_admin_only_"]        = "Backend Rewrite ohne /admin";
$lang["_system_mod_rewrite_admin_only_hint"]        = "Wenn aktiviert werden Backend Links ohne /admin generiert. Nur bei Seiten ohne Portal zu aktivieren.";
$lang["_system_portal_disable_"]         = "Portal deaktiviert";
$lang["_system_portal_disable_hint"]     = "Diese Einstellung aktiviert/deaktiviert das gesamte Portal.";
$lang["_system_portal_disablepage_"]     = "Zwischenseite";
$lang["_system_portal_disablepage_hint"] = "Diese Seite wird angezeigt, wenn das Portal deaktiviert wurde.";
$lang["_system_release_time_"]           = "Dauer einer Session";
$lang["_system_release_time_hint"]       = "Nach dieser Dauer in Sekunden wird eine Session automatisch ungültig.";
$lang["_system_timezone_"]               = "System Zeitzone";
$lang["_system_timezone_hint"]           = "Die Zeitzone wird zur Berechnung der korrekten Zeit- und Datumswerte verwendet. Eine Liste möglicher Werte ist unter <a href='http://www.php.net/manual/en/timezones.php' target='_blank'>http://www.php.net/manual/en/timezones.php</a> zu finden.";
$lang["_system_email_forcesender_"]      = "Default Absender erzwingen";
$lang["_system_email_forcesender_hint"]  = "Manche E-Mail Gateways stellen nur E-Mails einer bestimmten Abender-Adresse zu. Der Versand aller Mails von der Default-Absender-Adresse kann hiermit erzwungen werden.";
$lang["_system_email_defaultsender_"]    = "Standard E-Mail Absender";
$lang["_system_email_defaultsender_hint"]    = "Sofern eine Mail keine Von-Adresse eingetragen hat wird die hier genannte Adresse verwendet.";
$lang["_system_session_ipfixation_"]     = "Session an IP binden";
$lang["_system_session_ipfixation_hint"] = "Normalerweise wird die Session an die IP des Clients gebunden. Bspw. beim Einsatz von Proxy Servern kann dies aber zu Problem (Logouts) führen.";
$lang["_system_lists_clickable_"]        = "Standard-Listen klickbar";
$lang["_system_lists_clickable_hint"]    = "Wenn aktiv wird bei Klick auf eine Listenzeile die erste Aktion automatisch ausgeführt";
$lang["_system_permission_assignment_threshold_"]    = "Schwellenwert Zuweisungen";
$lang["_system_permission_assignment_threshold_hint"]    = "Anzahl an User zu Gruppe Zuweisungen, bei der anstatt der OR-Abfrage der Berechtigungen ein Subselect verwendet wird.";
$lang["about_part1"]                     = "<h2>Kajona V6 - Open Source Content Management System</h2>Kajona V 6.2<br /><br /><a href=\"http://www.kajona.de\" target=\"_blank\">www.kajona.de</a><br /><a href=\"mailto:info@kajona.de\" target=\"_blank\">info@kajona.de</a><br /><br />Für weitere Infomationen, Support oder bei Anregungen besuchen Sie einfach unsere Webseite.<br />Support erhalten Sie auch in unserem <a href=\"http://board.kajona.de/\" target=\"_blank\">Forum</a>.";
$lang["about_part2_header"]              = "<h2>Entwicklungsleitung</h2>";
$lang["about_part2a_header"]             = "<h2>Contributors / Entwickler</h2>";
$lang["about_part2b_header"]             = "<h2>Übersetzungen</h2>";
$lang["about_part4"]                     = "<h2>Spenden</h2><p>Wenn Ihnen Kajona gefällt und Sie das Projekt unterstützen möchten können Sie hier an das Projekt spenden: </p> <form method=\"post\" action=\"https://www.paypal.com/cgi-bin/webscr\" target=\"_blank\"><input type=\"hidden\" value=\"_donations\" name=\"cmd\" /> <input type=\"hidden\" value=\"donate@kajona.de\" name=\"business\" /> <input type=\"hidden\" value=\"Kajona Development\" name=\"item_name\" /> <input type=\"hidden\" value=\"0\" name=\"no_shipping\" /> <input type=\"hidden\" value=\"1\" name=\"no_note\" /> <input type=\"hidden\" value=\"EUR\" name=\"currency_code\" /> <input type=\"hidden\" value=\"0\" name=\"tax\" /> <input type=\"hidden\" value=\"PP-DonationsBF\" name=\"bn\" /> <input type=\"submit\" name=\"submit\" value=\"Spenden via PayPal\" class=\"inputSubmit\" /></form>";
$lang["action_about"]                    = "Über Kajona";
$lang["action_list_aspect"]                  = "Aspekte";
$lang["action_changelog"]                = "Änderungshistorie";
$lang["action_list"]                     = "Installierte Module";
$lang["action_locked_records"]           = "Gesperrte Datensätze";
$lang["action_final_delete_record"]           = "Datensatz endgültig löschen";
$lang["final_delete_question"]           = "Möchten Sie den Datensatz &quot;{0}&quot; wirklich aus der Datenbank löschen? Ein Wiederherstellen des Datensatzes ist dann nicht mehr möglich. Alle untergeordneten Datensätze werden ebenfalls gelöscht.";
$lang["final_delete_submit"]           = "Löschen";
$lang["action_deleted_records"]           = "Gelöschte Datensätze";
$lang["action_restore_record_blocked"]           = "Der Datensatz kann nicht wiederhergestellt werden, übergeordnete Datensätze sind noch nicht wiederhergestellt.";
$lang["action_restore_record"]           = "Datensatz wiederherstellen";
$lang["action_system_info"]              = "Systeminformationen";
$lang["action_system_sessions"]          = "Sessions";
$lang["action_system_settings"]          = "Systemeinstellungen";
$lang["action_system_tasks"]             = "System-Tasks";
$lang["action_systemlog"]                = "System-Log";
$lang["action_unlock_record"]            = "Datensatz entsperren";

$lang["delete_aspect_question"]          = "Möchten Sie den Aspekt &quot;<b>%%element_name%%</b>&quot; wirklich löschen?";
$lang["aspect_isDefault"]                = "Standard Aspekt";
$lang["aspect_list_empty"]               = "Keine Aspekete angelegt";
$lang["cache_entry_size"]                = "Größe";
$lang["cache_hash1"]                     = "Hash 1";
$lang["cache_hash2"]                     = "Hash 2";
$lang["cache_leasetime"]                 = "Gültig bis";
$lang["cache_source"]                    = "Quelle";
$lang["change_action"]                   = "Aktion";
$lang["change_module"]                   = "Modul";
$lang["change_newvalue"]                 = "Neuer Wert";
$lang["change_oldvalue"]                 = "Alter Wert";
$lang["change_property"]                 = "Eigenschaft";
$lang["change_record"]                   = "Objekt";
$lang["change_type_setting"]             = "Einstellung";
$lang["change_user"]                     = "Benutzer";
$lang["change_report_title"]             = "Änderungshistorie";
$lang["change_export_excel"]             = "Daten nach Excel exportieren";
$lang["change_diff"]                     = "Vergleich";
$lang["dateStyleLong"]                   = "d.m.Y H:i:s";
$lang["dateStyleShort"]                  = "d.m.Y";
$lang["desc"]                            = "Rechte ändern an";
$lang["dialog_cancelButton"]             = "Abbrechen";
$lang["dialog_copyButton"]             = "Ja, kopieren";
$lang["dialog_copyHeader"]             = "Kopieren bestätigen";
$lang["dialog_deleteButton"]             = "Ja, löschen";
$lang["dialog_deleteHeader"]             = "Löschen bestätigen";
$lang["dialog_loadingHeader"]            = "Bitte warten";
$lang["dialog_removeAssignmentButton"]   = "Ja, Zuordnung löschen";
$lang["dialog_removeAssignmentHeader"]   = "Zuordnung löschen bestätigen";

$lang["errorintro"]                      = "Bitte überarbeiten Sie die folgenden Felder";
$lang["fehler_setzen"]                   = "Fehler beim Speichern der Rechte";
$lang["filebrowser"]                     = "Datei auswählen";
$lang["form_aspect_default"]             = "Standard-Aspekt";
$lang["form_aspect_name"]                = "Name";
$lang["form_aspect_name_hint"]           = "Der Name wird als interner Titel verwendet. Um einen Aspekt zu lokalisieren, kann dieser über einen Lang-Eintrag (aspect_NAME) gelabelt werden.";
$lang["form_deletedrecordsfilter_systemid"] = "Systemid";
$lang["form_deletedrecordsfilter_class"] = "Klasse";
$lang["form_deletedrecordsfilter_comment"] = "Record Comment";
$lang["form_default_group_name"]         = "Basisdaten";
$lang["locked_record_info"]              = "Gesperrt seit: {0} &middot; Gesperrt durch: {1}";
$lang["log_empty"]                       = "Keine Einträge im System-Logfile vorhanden";
$lang["login_xml_error"]                 = "Login fehlgeschlagen";
$lang["login_xml_succeess"]              = "Login erfolgreich";
$lang["logout_xml"]                      = "Logout erfolgreich";
$lang["mail_body"]                       = "Inhalt";
$lang["mail_cc"]                         = "Empfänger in Kopie";
$lang["mail_recipient"]                  = "Empfänger";
$lang["mail_send_error"]                 = "Fehler beim Versenden der E-Mail. Bitte versuchen Sie die letzte Aktion erneut.";
$lang["mail_send_success"]               = "E-Mail erfolgreich verschickt.";
$lang["mail_subject"]                    = "Betreff";

$lang["messageprovider_exceptions_name"] = "System-Fehlermeldungen";
$lang["messageprovider_personalmessage_name"] = "Persönliche Nachrichten";
$lang["modul_titel_aspect"]                = "Aspekte bearbeiten";
$lang["modul_rechte_root"]               = "Root-Rechte";
$lang["modul_sortdown"]                  = "Nach unten verschieben";
$lang["modul_sortup"]                    = "Nach oben verschieben";
$lang["modul_status_disabled"]           = "Modul aktiv schalten (ist inaktiv)";
$lang["modul_status_enabled"]            = "Modul inaktiv schalten (ist aktiv)";
$lang["modul_status_system"]             = "Hmmm. Den System-Kernel deaktivieren? Zuvor bitte format c: ausführen!";
$lang["modul_titel"]                     = "System";
$lang["moduleRightsTitle"]               = "Rechte";
$lang["numberStyleDecimal"]              = ",";
$lang["numberStyleThousands"]            = ".";
$lang["pageview_forward"]                = "Weiter";
$lang["pageview_total"]                  = "Gesamt: ";

$lang["quickhelp_change"]                = "Mit Hilfe dieses Formulares können die Rechte eines Datensatzes angepasst werden.";
$lang["quickhelp_list"]                  = "Die Liste der Module gibt eine schnelle Übersicht über die aktuell im System installierten Module.<br />Zusätzlich werden die aktuell installierten Versionen der installierten Module genannt, ebenso das ursprüngliche Installationsdatum des Moduls.<br />Über die Rechte des Moduls kann der Modul-Rechte-Knoten bearbeitet werden, von welchem die Inhalte bei aktivierter Rechtevererbung ihre Einstellungen erben.<br />Durch Verschieben der Module in der Liste lässt sich die Reihenfolge in der Modulnavigation anpassen.";
$lang["quickhelp_module_list"]           = "Die Liste der Module gibt eine schnelle Übersicht über die aktuell im System installierten Module.<br />Zusätzlich werden die aktuell installierten Versionen der installierten Module genannt, ebenso das ursprüngliche Installationsdatum des Moduls.<br />Über den Rechte-Button der Module können die jeweiligen Modul-Root-Rechte bearbeitet werden, welche an einzelne Datensätze des Moduls vererbt werden (solange die Rechteerbung des Datensatzes aktiviert ist).<br />Durch Verschieben der Module in der Liste lässt sich die Reihenfolge in der Modulnavigation anpassen.";
$lang["quickhelp_system_info"]           = "Kajona versucht an dieser Stelle, ein paar Informationen über das System heraus zu finden, auf welchem sich die Installation befindet.";
$lang["quickhelp_system_settings"]       = "Hier können grundlegende Einstellungen des Systems vorgenommen werden. Hierfür kann jedes Modul beliebige Einstellungsmöglichkeiten anbieten. Die hier vorgenommenen Einstellungen sollten mit Vorsicht verändert werden, falsche Einstellungen können das System im schlimmsten Fall unbrauchbar machen.<br /><br />Hinweis: Werden Werte an einem Modul geändert, so muss für JEDES Modul der Speichern-Button gedrückt werden. Ein Abändern der Einstellungen verschiedener Module wird beim Speichern nicht übernommen. Es werden nur die Werte der zum Speichern-Button zugehörigen Felder übernommen.";
$lang["quickhelp_system_tasks"]          = "Systemtasks sind kleine Programme, die alltägliche Aufaben wie Wartungsarbeiten im System übernehmen.<br />Hierzu gehört das Sichern der Datenbank und ggf. das Rückspielen einer Sicherung in das System.";
$lang["quickhelp_systemlog"]             = "Das Systemlogbuch gibt die Einträge des Logfiles aus, in welche die Module Nachrichten schreiben können.<br />Die Feinheit des Loggings kann in der config-Datei (/project/system/config/config.php) eingestellt werden.";
$lang["quickhelp_title"]                 = "Schnellhilfe";
$lang["quickhelp_updateCheck"]           = "Mit der Aktion Updatecheck werden die Versionsnummern der im System installierten Module mit den Versionsnummern der aktuell verfügbaren Module verglichen. Sollte ein Modul nicht mehr in der neusten Verion installiert sein, so gibt Kajona in der Zeile dieses Moduls einen Hinweis hierzu aus.";
$lang["send"]                            = "Versenden";

$lang["session_activity"]                = "Aktivität";
$lang["session_admin"]                   = "Administration, Modul: ";
$lang["session_loggedin"]                = "angemeldet";
$lang["session_loggedout"]               = "Gast";
$lang["session_logout"]                  = "Session beenden";
$lang["session_status"]                  = "Status";
$lang["session_username"]                = "Benutzer";
$lang["session_valid"]                   = "Gültig bis";
$lang["setAbsolutePosOk"]                = "Speichern der Position erfolgreich";
$lang["setPrevIdOk"]                     = "Speichern des neuen Eltern-Knotens erfolgreich";
$lang["setStatusError"]                  = "Fehler beim Ändern des Status";
$lang["setStatusOk"]                     = "Ändern des Status erfolgreich";
$lang["settings_updated"]                = "Einstellungen wurden geändert.";
$lang["setzen_erfolg"]                   = "Rechte erfolgreich gespeichert";
$lang["save_rights_success"]             = "Rechte erfolgreich gespeichert";
$lang["save_rights_error"]               = "Fehler beim Speichern der Berechtigungen";

$lang["status_active"]                   = "Status ändern (ist aktiv)";
$lang["status_inactive"]                 = "Status ändern (ist inaktiv)";
$lang["systemtask_cacheSource_source"]   = "Cache-Arten";
$lang["systemtask_cacheSource_namespace"] = "Cache-Namespace";
$lang["systemtask_cancel_execution"]     = "Ausführung beenden";
$lang["systemtask_close_dialog"]         = "OK";
$lang["systemtask_compresspicuploads_done"] = "Die Bildverkleinerung und -komprimierung wurde abgeschlossen.";
$lang["systemtask_compresspicuploads_found"] = "Gefundene Bilder";
$lang["systemtask_compresspicuploads_height"] = "Maximale Höhe (Pixel)";
$lang["systemtask_compresspicuploads_hint"] = "Um Speicherplatz zu sparen können Sie alle hochgeladenen Bilder im Ordner \"/files/images\" auf die angegebene Maximalgröße verkleinern und neu komprimieren lassen.<br />Beachten Sie, dass dieser Vorgang nicht rückgängig gemacht werden kann und es ggf. zu Qualitätseinbußen kommen kann.<br />Der Vorgang kann einige Minuten in Anspruch nehmen.";
$lang["systemtask_compresspicuploads_name"] = "Hochgeladene Bilder komprimieren";
$lang["systemtask_compresspicuploads_processed"] = "Bearbeitete Bilder";
$lang["systemtask_compresspicuploads_width"] = "Maximale Breite (Pixel)";
$lang["systemtask_dbconsistency_curprev_error"] = "Folgende Eltern-Kind Beziehungen sind fehlerhaft (fehlender Elternteil)";
$lang["systemtask_dbconsistency_curprev_ok"] = "Alle Eltern-Kind Beziehungen sind korrekt";
$lang["systemtask_dbconsistency_date_error"] = "Folgende Datum-Records sind fehlerhaft (fehlender System-Record)";
$lang["systemtask_dbconsistency_date_ok"] = "Alle Datum-Records haben einen zugehörigen System-Record";
$lang["systemtask_dbconsistency_firstlevel_error"] = "Nicht alle Knoten auf erster Ebene gehören zu einem Modul";
$lang["systemtask_dbconsistency_firstlevel_ok"] = "Alle Knoten auf erster Ebene gehören zu einem Modul";
$lang["systemtask_dbconsistency_name"]   = "Datenbankkonsistenz überprüfen";
$lang["systemtask_dbexport_stream"]      = "Dump nach Erstellung an Browser senden";
$lang["systemtask_dbexport_error"]       = "Fehler beim Sichern der Datenbank. Weitere Informationen finden Sie im Logfile.";
$lang["systemtask_dbexport_excludetitle"] = "Tabellen ausschließen";
$lang["systemtask_dbexport_name"]        = "Datenbank sichern";
$lang["systemtask_dbexport_success"]     = "Sicherung erfolgreich angelegt";
$lang["systemtask_dbimport_datefileinfo"] = "Zeitstempel gemäß Dateiinfo";
$lang["systemtask_dbimport_datefilename"] = "Zeitstempel gemäß Dateiname";
$lang["systemtask_dbimport_error"]       = "Fehler beim Einspielen der Sicherung";
$lang["systemtask_dbimport_file"]        = "Vorhandene Sicherungen";
$lang["systemtask_dbimport_name"]        = "Datenbank importieren";
$lang["systemtask_dbimport_success"]     = "Sicherung erfolgreich eingespielt";
$lang["systemtask_dialog_title"]         = "Systemtask wird ausgeführt";
$lang["systemtask_dialog_title_done"]    = "Systemtask abgeschlossen";
$lang["systemtask_filedump_error"]       = "Während der Sicherung ist ien Fehler aufgetreten.";
$lang["systemtask_filedump_name"]        = "Sicherung des Dateisystems erstellen";
$lang["systemtask_filedump_success"]     = "Die Sicherung wurde erfolgreich angelegt. <br/>Aus Sicherheitsgründen sollte die Sicherung schnellstmöglich vom Server entfernt werden. <br />Name der Sicherungsdatei:&nbsp;";
$lang["systemtask_flushcache_all"]       = "Alle Einträge";
$lang["systemtask_flushcache_error"]     = "Ein Fehler ist aufgetreten.";
$lang["systemtask_flushcache_name"]      = "Globalen Cache leeren";
$lang["systemtask_flushcache_success"]   = "Der Cache wurde geleert.";
$lang["systemtask_flushpiccache_deleted"] = "<br />Anzahl gelöschter Bilder: ";
$lang["systemtask_flushpiccache_done"]   = "Leeren abgeschlossen.";
$lang["systemtask_flushpiccache_name"]   = "Bildercache leeren";
$lang["systemtask_flushpiccache_skipped"] = "<br />Anzahl übersprungener Bilder: ";
$lang["systemtask_group_cache"]          = "Cache";
$lang["systemtask_group_database"]       = "Datenbank";
$lang["systemtask_group_default"]        = "Verschiedenes";
$lang["systemtask_group_ldap"]           = "Ldap";
$lang["systemtask_group_pages"]          = "Seiten";
$lang["systemtask_group_search"]         = "Suche";
$lang["systemtask_group_stats"]          = "Statistiken";
$lang["systemtask_progress"]             = "Fortschritt";
$lang["systemtask_run"]                  = "Ausführen";
$lang["systemtask_runningtask"]          = "Task";
$lang["systemtask_status_error"]         = "Fehler beim Setzen des Status.";
$lang["systemtask_status_success"]       = "Der Status wurde erfolgreich gesetzt.";
$lang["systemtask_systemstatus_active"]  = "Aktiv";
$lang["systemtask_systemstatus_inactive"] = "Inaktiv";
$lang["systemtask_systemstatus_name"]    = "Status eines Datensatzes setzen";
$lang["systemtask_systemstatus_status"]  = "Status";
$lang["systemtask_systemstatus_systemid"] = "Systemid";
$lang["systemtask_rightsinheritcheck_name"] = "Rechtevererbung optimieren";
$lang["systemtask_rightsinheritcheck_intro"] = "Für nachfolgende Knoten wurde die Rechte-Vererbung wieder aktiviert. Die Knoten brachen die Vererbung auf, obwohl sie die selben Rechte wie ihr Eltern-Knoten verwendet hatten.";
$lang["systemtask_rightsinheritcheck_empty"] = "Alle Knoten sind optimiert";
$lang["titel_erben"]                     = "Rechte erben";
$lang["permissions_toggle_visible"]                     = "Nicht-konfigurierte Zeilen einblenden";
$lang["permissions_toggle_hidden"]                     = "Nicht-konfigurierte Zeilen ausblenden";
$lang["permissions_success"]                     = "Die Berechtigungen wurden erfolgreich gespeichert.";
$lang["permissons_filter"]                     = "Filter-Text";
$lang["permissons_add_group"]                     = "Gruppe hinzufügen";
$lang["titel_leer"]                      = "<em>Kein Titel hinterlegt</em>";
$lang["titel_root"]                      = "Rechte-Root-Satz";
$lang["titleTime"]                       = "Uhr";
$lang["toolsetCalendarMonth"]            = "\"Januar\", \"Februar\", \"März\", \"April\", \"Mai\", \"Juni\", \"Juli\", \"August\", \"September\", \"Oktober\", \"November\", \"Dezember\"";
$lang["toolsetCalendarWeekday"]          = "\"So\", \"Mo\", \"Di\", \"Mi\", \"Do\", \"Fr\", \"Sa\"";
$lang["toolsetCalendarMonthShort"]       = array('Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez');
$lang["toolsetCalendarWeekdayShort"]     = array('S', 'M', 'D', 'M', 'D', 'F', 'S');
$lang["treeviewtoggle"]                  = "Baum anzeigen / ausblenden";
$lang["update_available"]                = "Bitte updaten!";
$lang["update_invalidXML"]               = "Die Antwort vom Server war leider nicht korrekt. Bitte versuchen Sie die letzte Aktion erneut.";
$lang["update_module_localversion"]      = "Diese Installation";
$lang["update_module_name"]              = "Modul";
$lang["update_module_remoteversion"]     = "Verfügbar";
$lang["update_nodom"]                    = "Diese PHP-Installation unterstützt kein XML-DOM. Dies ist für den Update-Check erforderlich.";
$lang["update_nofilefound"]              = "Die Liste der Updates konnte nicht geladen werden.<br />Gründe hierfür können sein, dass auf diesem System der PHP-Config-Wert 'allow_url_fopen' auf 'off' gesetzt wurde, oder das System keine Unterstützung für Sockets bietet.";
$lang["update_nourlfopen"]               = "Für diese Funktion muss der Wert &apos;allow_url_fopen&apos; in der PHP-Konfiguration auf &apos;on&apos; gesetzt sein!";

$lang["uploadfile"]                      = "Ausgewählte Datei";


$lang["warnung_settings"]                = "ACHTUNG!!!<br />Bei folgenden Einstellungen können falsche Werte das System unbrauchbar machen!";
$lang["systemtask_permissions_hint"]     = "Dieser Task ändert die Berechtigungen einer Gruppe rekursiv. Die Berechtigungen werden dabei unabhängig des Vererbungsstatus gesetzt, d.h. die Rechte werden entweder durch Vererbung oder durch direkte Manipulation angepasst. Die Berechtigungen anderer Gruppen bleiben unverändert.";
$lang["systemtask_permissions_systemid"]     = "Start Systemid";
$lang["systemtask_permissions_groupid"]     = "Relevante Gruppe";
$lang["systemtask_permissions_finished"]     = "Ausführung abgeschlossen";
$lang["systemtask_permissions_name"]     = "Rechte rekursiv setzen";
$lang["generic_changelog_no_systemid"]  = "Diese Aktion kann nur mit einer gültigen Systemid aufgerufen werden. Bitte eine entsprechende Systemid angeben.";
$lang["generic_changelog_not_versionable"]  = "Der Datensatz steht nicht unter Versionierung";
$lang["generic_record_locked"]  = "Dieser Datensatz wurde von dem Benutzer '{0}' gesperrt und kann daher nicht bearbeitet werden.";

$lang["changelog_tooltipUnit"]       = "Änderung";
$lang["changelog_tooltipUnitPlural"] = "Änderungen";
$lang["changelog_tooltipHtml"]       = "<span><strong>%count% %unit%</strong> am %date%</span>";
$lang["changelog_tooltipColumn"]     = "Über die Kalender-Heatmap können Sie per Klick den Datensatz für ein bestimmtes Datum nachladen.";

$lang["workflow_oracle_stats_title"]  = "Oracle Statistiken neu berechnen";
$lang["workflow_oracle_stats_val1"]  = "Uhrzeit der Berechnung";

$lang["workflow_messagequeue_title"]  = "Message-Queue Abarbeitung";
$lang["workflow_queue_sender_val1"]  = "Stunden";
$lang["workflow_queue_sender_val2"]  = "Minuten";


$lang["update_in_progress"] = "Das System wird gerade aktualisiert. Bitte warten Sie...";
$lang["form_objectlist_add_search"]  = "{0} hinzufügen ...";

$lang["object_browser_reset"] = "Objekt entfernen";

$lang["copy_to_clipboard"] = "In die Zwischenablage kopieren";
$lang["copy_page_url"] = "Seiten-URL";
$lang["link_was_copied"] = "Link wurde kopiert";

$lang["error_model_not_found"] = "Fehler beim Verarbeiten der Anfrage, der zu ladende Datensatz ist nicht bekannt. Bitte starten Sie die letzte Aktion erneut.";

$lang["systemtask_samplecontent_installer"] = "Samplecontent";
$lang["systemtask_samplecontent_installer_name"] = "Samplecontent Installer";
$lang["systemtask_samplecontent_installer_error"] = "Samplecontent konnte nicht installiert werden";

$lang["systemtask_form_name"] = "Form-Demo";
