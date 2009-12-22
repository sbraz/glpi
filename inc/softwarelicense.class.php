<?php


/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2009 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/// License class
class SoftwareLicense extends CommonDBTM {

   // From CommonDBTM
   public $table = 'glpi_softwarelicenses';
   public $type = 'SoftwareLicense';
   public $dohistory = true;

   static function getTypeName() {
      global $LANG;

      return $LANG['software'][11];
   }

   function canCreate() {
      return haveRight('software', 'w');
   }

   function canView() {
      return haveRight('software', 'r');
   }

   function pre_updateInDB($input,$updates,$oldvalues=array()) {

      // Clean end alert if expire is after old one
      if ((isset($oldvalues['expire']) && ($oldvalues['expire'] < $this->fields['expire']))) {
         $alert=new Alert();
         $alert->clear($this->type,$this->fields['id'],ALERT_END);
      }
      return array($input,$updates);
   }

   function prepareInputForAdd($input) {

      // Unset to set to default using mysql default value
      if (empty ($input['expire'])) {
         unset ($input['expire']);
      }

      if (!isset($input['computers_id']) || $input['computers_id'] <= 0) {
         $input['computers_id'] = -1;
      } else {
         // Number is 1 for affected license
         $input['number']=1;
      }

      return $input;
   }

   function prepareInputForUpdate($input) {

      if (isset($input['computers_id']) && $input['computers_id'] == 0) {
         $input['computers_id'] = -1;
      }
      if ((isset($input['computers_id']) && $input['computers_id'] > 0)
          || (!isset($input['computers_id']) && isset($this->fields['computers_id'])
              && $this->fields['computers_id']>0)) {
         // Number is 1 for affected license
         $input['number']=1;
      }
      return $input;
   }

   function post_addItem($newID, $input) {

      $itemtype = 'Software';
      $dupid = $this->fields["softwares_id"];
      if (isset ($input["_duplicate_license"])) {
         $itemtype = 'SoftwareLicense';
         $dupid = $input["_duplicate_license"];
      }
      // Add infocoms if exists for the licence
      $ic = new Infocom();
      if ($ic->getFromDBforDevice($itemtype, $dupid)) {
         unset ($ic->fields["id"]);
         $ic->fields["items_id"] = $newID;
         if (isset($ic->fields["immo_number"])) {
            $ic->fields["immo_number"] = autoName($ic->fields["immo_number"], "immo_number", 1,
                                                  'Infocom', $input['entities_id']);
         }
         if (empty($ic->fields['use_date'])) {
            unset($ic->fields['use_date']);
         }
         if (empty($ic->fields['buy_date'])) {
            unset($ic->fields['buy_date']);
         }
         $ic->fields["itemtype"] = $this->type;
         $ic->addToDB();
      }
   }

   function defineTabs($ID,$withtemplate) {
      global $LANG, $CFG_GLPI;

      $ong[1] = $LANG['title'][26];
      if ($ID) {
         if (haveRight("infocom","r")) {
            $ong[4] = $LANG['Menu'][26];
         }
         if (haveRight("document","r")) {
            $ong[5]=$LANG['Menu'][27];
         }
         $ong[12] = $LANG['title'][38];
      }
      return $ong;
   }

   /**
    * Print the Software / license form
    *
    *@param $target form target
    *@param $ID Integer : Id of the version or the template to print
    *@param $softwares_id ID of the software for add process
    *
    *@return true if displayed  false if item not found or not right to display
    **/
   function showForm($target,$ID,$softwares_id=-1) {
      global $CFG_GLPI,$LANG;

      if (!haveRight("software","w")) {
         return false;
      }

      if ($ID > 0) {
         $this->check($ID,'r');
      } else {
         // Create item
         $this->check(-1,'w');
         $this->fields['softwares_id']=$softwares_id;
         $this->fields['number']=1;
      }

      $this->showTabs($ID);
      $this->showFormHeader($target,$ID,'',2);

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['help'][31]."&nbsp;:</td>";
      echo "<td>";
      if ($ID>0) {
         $softwares_id=$this->fields["softwares_id"];
      } else {
         echo "<input type='hidden' name='softwares_id' value='$softwares_id'>";
      }
      echo "<a href='software.form.php?id=".$softwares_id."'>".
                 Dropdown::getDropdownName("glpi_softwares",$softwares_id)."</a>";
      echo "</td>";
      echo "<td>".$LANG['common'][17]."&nbsp;:</td>";
      echo "<td>";
      Dropdown::show('SoftwareLicenseType',
                     array('value' => $this->fields["softwarelicensetypes_id"]));
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['common'][16]."&nbsp;:</td>";
      echo "<td>";
      autocompletionTextField("name",$this->table,"name",$this->fields["name"],40);
      echo "</td>";
      echo "<td>".$LANG['common'][19]."&nbsp;:</td>";
      echo "<td>";
      autocompletionTextField("serial",$this->table,"serial",$this->fields["serial"],40);
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['software'][1]."&nbsp;:</td>";
      echo "<td>";
      SoftwareVersion::dropdown("softwareversions_id_buy",
                        array('softwares_id' => $this->fields["softwares_id"],
                               'value'       => $this->fields["softwareversions_id_buy"]));
      echo "</td>";
      echo "<td>".$LANG['common'][20]."&nbsp;:</td>";
      echo "<td>";
      autocompletionTextField("otherserial",$this->table,"otherserial",
                              $this->fields["otherserial"],40);
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['software'][2]."&nbsp;:</td>";
      echo "<td>";
      SoftwareVersion::dropdown("softwareversions_id_use",
                        array('softwares_id' => $this->fields["softwares_id"],
                               'value'       => $this->fields["softwareversions_id_use"]));
      echo "</td>";
      echo "<td rowspan='4' class='middle'>".$LANG['common'][25]."&nbsp;:</td>";
      echo "<td class='center middle' rowspan='4'>";
      echo "<textarea cols='45' rows='5' name='comment' >".$this->fields["comment"];
      echo "</textarea></td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['tracking'][29]."&nbsp;:</td>";
      echo "<td>";
      if ($this->fields["computers_id"]>0) {
         echo "1  (".$LANG['software'][50].")";
      } else {
         Dropdown::showInteger("number",$this->fields["number"],1,1000,1,array(-1=>$LANG['software'][4]));
      }
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['software'][50]."&nbsp;:</td>";
      echo "<td>";
      if ($this->fields["number"]==1) {
         Dropdown::show('Computer',
                        array('value'  => $this->fields["computers_id"],
                              'entity' => ($this->fields['is_recursive']
                              ? getSonsOf('glpi_entities', $this->fields['entities_id'])
                              : $this->fields['entities_id'])));
      } else {
         echo $LANG['software'][51];
      }
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['software'][32]."&nbsp;:</td>";
      echo "<td>";
      showDateFormItem('expire',$this->fields["expire"]);
      echo "</td></tr>\n";

      $this->showFormButtons($ID,'',2);

      echo "<div id='tabcontent'></div>";
      echo "<script type='text/javascript'>loadDefaultTab();</script>";

      return true;
   }

   /**
    * Is the license may be recursive
    *
    * @return boolean
   **/
   function maybeRecursive () {

      $soft=new Software();
      if (isset($this->fields["softwares_id"]) && $soft->getFromDB($this->fields["softwares_id"])) {
         return $soft->isRecursive();
      }
      return false;
   }

   function getSearchOptions() {
      global $LANG;

      // Only use for History (not by search Engine)
      $tab = array();

      $tab[2]['table']     = 'glpi_softwarelicenses';
      $tab[2]['field']     = 'name';
      $tab[2]['linkfield'] = 'name';
      $tab[2]['name']      = $LANG['common'][16];

      $tab[3]['table']     = 'glpi_softwarelicenses';
      $tab[3]['field']     = 'serial';
      $tab[3]['linkfield'] = 'serial';
      $tab[3]['name']      = $LANG['common'][19];

      $tab[162]['table']     = 'glpi_softwarelicenses';
      $tab[162]['field']     = 'otherserial';
      $tab[162]['linkfield'] = '';
      $tab[162]['name']      = $LANG['common'][20];

      $tab[4]['table']     = 'glpi_softwarelicenses';
      $tab[4]['field']     =   'number';
      $tab[4]['linkfield'] = 'number';
      $tab[4]['name']      = $LANG['tracking'][29];
      $tab[4]['datatype']  = 'number';

      $tab[5]['table']     = 'glpi_softwarelicensetypes';
      $tab[5]['field']     = 'name';
      $tab[5]['linkfield'] = 'softwarelicensetypes_id';
      $tab[5]['name']      = $LANG['common'][17];

      $tab[6]['table']     = 'glpi_softwareversions';
      $tab[6]['field']     = 'name';
      $tab[6]['linkfield'] = 'softwareversions_id_buy';
      $tab[6]['name']      = $LANG['software'][1];

      $tab[7]['table']     = 'glpi_softwareversions';
      $tab[7]['field']     = 'name';
      $tab[7]['linkfield'] = 'softwareversions_id_use';
      $tab[7]['name']      = $LANG['software'][2];

      $tab[8]['table']     = 'glpi_softwarelicenses';
      $tab[8]['field']     = 'expire';
      $tab[8]['linkfield'] = 'expire';
      $tab[8]['name']      = $LANG['software'][32];
      $tab[8]['datatype']  = 'date';

      $tab[9]['table']     = 'glpi_computers';
      $tab[9]['field']     = 'name';
      $tab[9]['linkfield'] = 'computers_id';
      $tab[9]['name']      = $LANG['software'][50];

      $tab[16]['table']     = 'glpi_softwarelicenses';
      $tab[16]['field']     = 'comment';
      $tab[16]['linkfield'] = 'comment';
      $tab[16]['name']      = $LANG['common'][25];
      $tab[16]['datatype']  = 'text';

      return $tab;
   }

   static function cronInfo($name) {
      global $LANG;

      return array('description' => $LANG['crontask'][4]);
   }

   /**
    * Cron action on softwares : alert on expired licences
    *
    * @param $task to log, if NULL display
    *
    * @return 0 : nothing to do 1 : done with success
    **/
   static function cronSoftware($task=NULL) {
      global $DB,$CFG_GLPI,$LANG;

      if (!$CFG_GLPI['use_mailing'] || !$CFG_GLPI['use_licenses_alert']) {
         return false;
      }

      loadLanguage($CFG_GLPI["language"]);

      $message=array();
      $items_notice=array();
      $items_end=array();

      // Check notice
      $query = "SELECT `glpi_softwarelicenses`.*, `glpi_softwares`.`name` AS softname
                FROM `glpi_softwarelicenses`
                INNER JOIN `glpi_softwares`
                     ON (`glpi_softwarelicenses`.`softwares_id` = `glpi_softwares`.`id`)
                LEFT JOIN `glpi_alerts`
                     ON (`glpi_softwarelicenses`.`id` = `glpi_alerts`.`items_id`
                         AND `glpi_alerts`.`itemtype` = 'SoftwareLicense'
                         AND `glpi_alerts`.`type` = '".ALERT_END."')
                WHERE `glpi_alerts`.`date` IS NULL
                      AND `glpi_softwarelicenses`.`expire` IS NOT NULL
                      AND `glpi_softwarelicenses`.`expire` < CURDATE()
                      AND `glpi_softwares`.`is_template`='0'
                      AND `glpi_softwares`.`is_deleted`='0'";

      $result=$DB->query($query);
      if ($DB->numrows($result)>0) {
         while ($data=$DB->fetch_array($result)) {
            if (!isset($message[$data["entities_id"]])) {
               $message[$data["entities_id"]]="";
            }
            if (!isset($items_notice[$data["entities_id"]])) {
               $items[$data["entities_id"]]=array();
            }
            $name = $data['softname'].' - '.$data['name'].' - '.$data['serial'];

            // define message alert
            if (strstr($message[$data["entities_id"]],$name)===false) {
               $message[$data["entities_id"]] .= $LANG['mailing'][51]." ".$name.": ".
                                                 convDate($data["expire"])."<br>\n";
            }
            $items[$data["entities_id"]][]=$data["id"];
         }
      }

      if (count($message)>0) {
         foreach ($message as $entity => $msg) {
            $mail=new MailingAlert("alertlicense",$msg,$entity);
            if ($mail->send()) {
               if ($task) {
                  $task->log(Dropdown::getDropdownName("glpi_entities",$entity).":  $msg\n");
                  $task->addVolume(1);
               } else {
                  addMessageAfterRedirect(Dropdown::getDropdownName("glpi_entities",$entity).":  $msg");
               }

               // Mark alert as done
               $alert=new Alert();
               $input["itemtype"] = 'SoftwareLicense';

               $input["type"]=ALERT_END;
               if (isset($items[$entity])) {
                  foreach ($items[$entity] as $ID) {
                     $input["items_id"]=$ID;
                     $alert->add($input);
                     unset($alert->fields['id']);
                  }
               }
            } else {
               if ($task) {
                  $task->log(Dropdown::getDropdownName("glpi_entities",$entity).": Send licenses alert failed\n");
               } else {
                  addMessageAfterRedirect(Dropdown::getDropdownName("glpi_entities",$entity).
                                          ": Send licenses alert failed",false,ERROR);
               }
            }
         }
         return 1;
      }
      return 0;
   }

   /**
    * Get number of bought licenses of a version
    *
    * @param $softwareversions_id version ID
    * @param $entity to search for licenses in (default = all active entities)
    *
    * @return number of installations
    */
   static function countForVersion($softwareversions_id, $entity='') {
      global $DB;

      $query = "SELECT COUNT(*)
                FROM `glpi_softwarelicenses`
                WHERE `softwareversions_id_buy` = '$softwareversions_id' " .
                      getEntitiesRestrictRequest('AND', 'glpi_softwarelicenses','',$entity);

      $result = $DB->query($query);

      if ($DB->numrows($result) != 0) {
         return $DB->result($result, 0, 0);
      }
      return 0;
   }

   /**
    * Get number of licensesof a software
    *
    * @param $softwares_id software ID
    * @return number of licenses
    */
   static function countForSoftware($softwares_id) {
      global $DB;

      $query = "SELECT `id`
                FROM `glpi_softwarelicenses`
                WHERE `softwares_id` = '$softwares_id'
                      AND `number` = '-1' " .
                      getEntitiesRestrictRequest('AND', 'glpi_softwarelicenses', '', '', true);

      $result = $DB->query($query);
      if ($DB->numrows($result)) {
         // At least 1 unlimited license, means unlimited
         return -1;
      }
      $query = "SELECT SUM(`number`)
                FROM `glpi_softwarelicenses`
                WHERE `softwares_id` = '$softwares_id'
                      AND `number` > '0' " .
                      getEntitiesRestrictRequest('AND', 'glpi_softwarelicenses', '', '', true);

      $result = $DB->query($query);
      $nb = $DB->result($result,0,0);
      return ($nb ? $nb : 0);
   }

   /**
    * Show Licenses of a software
    *
    * @param $softwares_id ID of the software
    * @return nothing
    */
   static function showForSoftware($software) {
      global $DB, $CFG_GLPI, $LANG;

      $softwares_id = $software->getField('id');
      $license = new SoftwareLicense;
      $computer = new Computer();

      if (!$software->can($softwares_id,"r")) {
         return false;
      }

      if (isset($_REQUEST["start"])) {
         $start = $_REQUEST["start"];
      } else {
         $start = 0;
      }

      if (isset($_REQUEST["sort"]) && !empty($_REQUEST["sort"])) {
         $sort = "`".$_REQUEST["sort"]."`";
      } else {
         $sort = "`entity`, `name`";
      }

      if (isset($_REQUEST["order"]) && $_REQUEST["order"]=="DESC") {
         $order = "DESC";
      } else {
         $order = "ASC";
      }

      // Righ type is enough. Can add a License on a software we have Read access
      $canedit = haveRight("software", "w");

      // Total Number of events
      $number = countElementsInTable("glpi_softwarelicenses",
                                     "glpi_softwarelicenses.softwares_id = $softwares_id " .
                                             getEntitiesRestrictRequest('AND', 'glpi_softwarelicenses',
                                                                        '', '', true));
      echo "<br><div class='center'>";
      if ($number < 1) {
         echo "<table class='tab_cadre_fixe'>";
         echo "<tr><th>".$LANG['search'][15]."</th></tr>\n";
         if ($canedit){
            echo "<tr class='tab_bg_2'><td class='center'>";
            echo "<a href='softwarelicense.form.php?softwares_id=$softwares_id'>".$LANG['software'][8];
            echo "</a></td></tr>\n";
         }
         echo "</table></div>\n";
         return;
      }

      // Display the pager
      printAjaxPager($LANG['software'][11],$start,$number);

      $rand=mt_rand();
      $query = "SELECT `glpi_softwarelicenses`.*,
                       `buyvers`.`name` AS buyname,
                       `usevers`.`name` AS usename,
                       `glpi_entities`.`completename` AS entity,
                       `glpi_softwarelicensetypes`.`name` AS typename
                FROM `glpi_softwarelicenses`
                LEFT JOIN `glpi_softwareversions` AS buyvers
                     ON (`buyvers`.`id` = `glpi_softwarelicenses`.`softwareversions_id_buy`)
                LEFT JOIN `glpi_softwareversions` AS usevers
                     ON (`usevers`.`id` = `glpi_softwarelicenses`.`softwareversions_id_use`)
                LEFT JOIN `glpi_entities`
                     ON (`glpi_entities`.`id` = `glpi_softwarelicenses`.`entities_id`)
                LEFT JOIN `glpi_softwarelicensetypes`
                     ON (`glpi_softwarelicensetypes`.`id`
                          = `glpi_softwarelicenses`.`softwarelicensetypes_id`)
                WHERE (`glpi_softwarelicenses`.`softwares_id` = '$softwares_id') " .
                       getEntitiesRestrictRequest('AND', 'glpi_softwarelicenses', '', '', true) ."
                ORDER BY $sort $order
                LIMIT ".intval($start)."," . intval($_SESSION['glpilist_limit']);

      initNavigateListItems('SoftwareLicense',$LANG['help'][31] ." = ". $software->fields["name"]);

      if ($result=$DB->query($query)) {
         if ($DB->numrows($result)) {
            if ($canedit) {
               echo "<form method='post' name='massiveactionlicense_form$rand' id='".
                      "massiveactionlicense_form$rand' action=\"".$CFG_GLPI["root_doc"].
                      "/front/massiveaction.php\">";
            }
            $sort_img="<img src=\"" . $CFG_GLPI["root_doc"] . "/pics/" .
                        ($order == "DESC" ? "puce-down.png" : "puce-up.png") . "\" alt='' title=''>";
            echo "<table class='tab_cadre_fixehov'><tr>";
            echo "<th>&nbsp;</th>";
            echo "<th>".($sort=="`name`"?$sort_img:"").
                      "<a href='javascript:reloadTab(\"sort=name&amp;order=".
                        ($order=="ASC"?"DESC":"ASC")."&amp;start=0\");'>".$LANG['common'][16]."</a></th>";

            if ($software->isRecursive()) {
               // Ereg to search entity in string for match default order
               echo "<th>".(strstr($sort,"entity")?$sort_img:"").
                         "<a href='javascript:reloadTab(\"sort=entity&amp;order=".
                           ($order=="ASC"?"DESC":"ASC")."&amp;start=0\");'>".$LANG['entity'][0]."</a></th>";
            }
            echo "<th>".($sort=="`serial`"?$sort_img:"").
                      "<a href='javascript:reloadTab(\"sort=serial&amp;order=".
                        ($order=="ASC"?"DESC":"ASC")."&amp;start=0\");'>".$LANG['common'][19]."</a></th>";
            echo "<th>".$LANG['tracking'][29]."</th>";
            echo "<th>".($sort=="`typename`"?$sort_img:"").
                      "<a href='javascript:reloadTab(\"sort=typename&amp;order=".
                        ($order=="ASC"?"DESC":"ASC")."&amp;start=0\");'>".$LANG['common'][17]."</a></th>";
            echo "<th>".($sort=="`buyname`"?$sort_img:"").
                      "<a href='javascript:reloadTab(\"sort=buyname&amp;order=".
                        ($order=="ASC"?"DESC":"ASC")."&amp;start=0\");'>".$LANG['software'][1]."</a></th>";
            echo "<th>".($sort=="`usename`"?$sort_img:"").
                      "<a href='javascript:reloadTab(\"sort=usename&amp;order=".
                        ($order=="ASC"?"DESC":"ASC")."&amp;start=0\");'>".$LANG['software'][2]."</a></th>";
            echo "<th>".($sort=="`expire`"?$sort_img:"").
                      "<a href='javascript:reloadTab(\"sort=expire&amp;order=".
                        ($order=="ASC"?"DESC":"ASC")."&amp;start=0\");'>".$LANG['software'][32]."</a></th>";
            echo "<th>".$LANG['help'][25]."</th>"; // "Computer" rather than "Affected To computer" ($LANG['software'][50] is too long) ??
            echo "</tr>\n";

            for ($tot=0 ; $data=$DB->fetch_assoc($result) ; ) {
               addToNavigateListItems('SoftwareLicense',$data['id']);
               echo "<tr class='tab_bg_2'>";
               if ($license->can($data['id'],"w")) {
                  echo "<td><input type='checkbox' name='item[".$data["id"]."]' value='1'></td>";
               } else {
                  echo "<td>&nbsp;</td>";
               }
               echo "<td><a href='softwarelicense.form.php?id=".$data['id']."'>".
                          $data['name'].(empty($data['name'])?$data['id']:"")."</a></td>";

               if ($software->isRecursive()) {
                  echo "<td>".$data['entity']."</td>";
               }
               echo "<td>".$data['serial']."</td>";
               echo "<td class='right'>".($data['number']>0?$data['number']:$LANG['software'][4])."</td>";
               echo "<td>".$data['typename']."</td>";
               echo "<td>".$data['buyname']."</td>";
               echo "<td>".$data['usename']."</td>";
               echo "<td>".convDate($data['expire'])."</td>";
               if ($data['computers_id']>0 && $computer->getFromDB($data['computers_id'])) {
                  $link = $computer->fields['name'];
                  if (empty($link) || $_SESSION['glpiis_ids_visible']) {
                     $link .= " (".$computer->fields['id'].")";
                  }
                  if ($computer->fields['is_deleted']) {
                     $link .= " (".$LANG['common'][28].")";
                  }
                  echo "<td><a href='computer.form.php?id=".$data['computers_id']."'>".$link."</a>";

                  // search installed version name
                  // should be same as name of used_version, except for multiple installation
                  $sql = "SELECT `glpi_softwareversions`.`name`
                          FROM `glpi_softwareversions`,
                               `glpi_computers_softwareversions`
                          WHERE `glpi_softwareversions`.`softwares_id` = '$softwares_id'
                                 AND `glpi_computers_softwareversions`.`softwareversions_id`
                                      =`glpi_softwareversions`.`id`
                                 AND `glpi_computers_softwareversions`.`computers_id`
                                      ='".$data['computers_id']."'
                          ORDER BY `name`";

                  $installed='';
                  foreach ($DB->request($sql) as $inst) {
                     $installed .= (empty($installed)?'':', ').$inst['name'];
                  }
                  echo " (".(empty($installed) ? $LANG['common'][89] : $installed).")";
                  echo "</td>";
               } else {
                  echo "<td>&nbsp;</td>";
               }
               echo "</tr>";

               if ($data['number']<0) {
                  // One illimited license, total is illimited
                  $tot = -1;
               } else if ($tot>=0) {
                  // Not illimited, add the current number
                  $tot += $data['number'];
               }
            }
            echo "<tr class='tab_bg_1'>";
            echo "<td colspan='".($software->isRecursive()?4:3)."' class='right b'>".$LANG['common'][33];
            echo "</td><td class='right b'>".($tot>0?$tot:$LANG['software'][4])."</td>";
            echo "<td colspan='5' class='center'>";
            if ($canedit) {
               echo "<a href='softwarelicense.form.php?softwares_id=$softwares_id'>".
                      $LANG['software'][8]."</a>";
            }
            echo "</td></tr>";
            echo "</table>\n";

            if ($canedit) {
               openArrowMassive("massiveactionlicense_form$rand",true);
               dropdownMassiveAction('SoftwareLicense',0,array('softwares_id' => $softwares_id));
               closeArrowMassive();

               echo "</form>";
            }
         } else {
            echo $LANG['search'][15];
         }
      }
      echo "</div>";
   }

}

?>
