<? Php

 / **
  * _____ _ _
  * |  __ \ |  |  |  |  / \
  * |  | __) ||  |  ___ |  | _ / \ _ __ ___ __ _
  * |  ___ / |  |  / _ \ |  __ |  // \ \ |  `__ | / _ \ / _` |
  * |  |  |  ||  (_) ||  | _ / ____ \ |  |  |  __ / |  (_ | |
  * | _ |  | _ |  \ ___ / \ __ | / _ / \ _ \ | _ |  \ ___ |  \ __, _ |
  * @autor Mohamed El Yousfi
  * /

 Namensraum mohagames \ PlotArea;

 benutze mohagames \ PlotArea \ listener \ EventListener;
 benutze mohagames \ PlotArea \ tasks \ PositioningTask;
 benutze mohagames \ PlotArea \ utils \ Group;
 benutze mohagames \ PlotArea \ utils \ Member;
 benutze mohagames \ PlotArea \ utils \ PermissionManager;
 benutze mohagames \ PlotArea \ utils \ Plot;
 benutze mohagames \ PlotArea \ utils \ PublicChest;
 benutze pocketmine \ command \ Command;
 benutze pocketmine \ command \ CommandSender;
 Verwenden Sie pocketmine \ command \ ConsoleCommandSender.
 benutze pocketmine \ event \ Listener;
 benutze pocketmine \ item \ ItemFactory;
 benutze pocketmine \ item \ ItemIds;
 benutze pocketmine \ plugin \ PluginBase;
 benutze pocketmine \ utils \ Config;
 benutze pocketmine \ utils \ TextFormat;
 benutze SQLite3;

 Klasse Main erweitert PluginBase implementiert Listener
 {
     public $ pos_1 = array ();
     public $ pos_2 = array ();
     public $ db;
     öffentliche statische $ Instanz;
     public $ item;


     öffentliche Funktion onEnable (): void
     {

         Main :: $ instance = $ this;

         $ config = new Config ($ this-> getDataFolder (). "config.yml", -1, array ("item_id" => ItemIds :: WOODEN_SHOVEL, "plot_popup" => true, "max_members" => 10, "  xp-add "=> 100," xp-deduct "=> 100));
         $ Config> save ();
         $ this-> item = $ config-> get ("item_id");
         $ popup = $ config-> get ("plot_popup");
         if ($ popup) {
             $ this-> getScheduler () -> scheduleRepeatingTask (neue PositioningTask (), 30);
         }

         // Dadurch werden die Datenbanken erstellt, sofern sie noch nicht vorhanden sind
         $ this-> db = new SQLite3 ($ this-> getDataFolder (). "PlotArea.db");
         $ this-> db-> query ("TABELLE ERSTELLEN, WENN KEINE Truhen existieren (chest_id INTEGER PRIMARY KEY AUTOINCREMENT, chest_location TEXT, chest_world TEXT, status TEXT, plot_id INTEGER)");
         $ this-> db-> query ("TABELLE ERSTELLEN, WENN KEINE ZEICHNUNGEN VORHANDEN SIND (plot_id INTEGER PRIMARY KEY AUTOINCREMENT, plot_name TEXT, plot_owner TEXT, plot_world TEXT, plot_permissions TEXT, default NULL, max_members INTEGER group)  );
         $ this-> db-> query ("TABELLE ERSTELLEN, WENN KEINE Gruppen EXISTIEREN (group_id INTEGER PRIMARY KEY AUTOINCREMENT, group_name TEXT, master_plot TEXT)");
         // Dies zeichnet die Ereignisse auf
         $ this-> getServer () -> getPluginManager () -> registerEvents ($ this, $ this);
         $ this-> getServer () -> getPluginManager () -> registerEvents (neuer EventListener (), $ this);

     }

     / **
      * @param CommandSender $ sender
      * @param Befehl $ Befehl
      * @param string $ label
      * @param array $ args
      * @ Return Bool
      * @throws \ ReflectionException
      * /
     öffentliche Funktion onCommand (CommandSender $ sender, Command $ command, String $ label, Array $ args): bool
     {
         switch ($ command-> getName ()) {
             Fall "Grundstückswand":
                 $ item = ItemFactory :: get ($ this-> item);
                 $ item-> setCustomName ("Plot wall");
                 $ Sender-> GetInventory () -> addItem ($ item);
                 $ sender-> sendMessage ("§aSie haben eine Grundstückswand erhalten");
                 return true;

             case "saveplot":
                 if (isset ($ this-> pos_1 [$ sender-> getName ()]) && isset ($ this-> pos_2 [$ sender-> getName ()])) {
                     if (isset ($ args [0])) {

                         $ pos1 = $ this-> pos_1 [$ sender-> getName ()];
                         $ pos2 = $ this-> pos_2 [$ sender-> getName ()];
                         unset ($ this-> POS_1 [$ sender-> getName ()]);
                         unset ($ this-> POS_2 [$ sender-> getName ()]);

                         $ p_name = $ args [0];
                         if (Plot :: getPlotByName ($ p_name) == null) {
                             Plot :: save ($ p_name, $ sender-> getLevel (), array ($ pos1, $ pos2));
                             $ sender-> sendMessage ("§2Der Plot §a $ p_name §2wird erfolgreich gespeichert!");
                         } else {
                             $ sender-> sendMessage ("§4A Plot existiert bereits mit diesem Namen");
                         }
                     } else {
                         $ sender-> sendMessage ("§cU muss einen Plotnamen angeben. usage: / saveplot name");
                     }

                 } else {
                     $ sender-> sendMessage ("Sie müssen noch die Position des Plots bestimmen.");
                 }
                 return true;

             Fall "Handlungsinfo":
                 $ plot = Plot :: get ($ sender);
                 if ($ plot! == null) {
                     $ line = "\ n§3 ---------------------------- \ n";
                     $ size = $ plot-> getSize ();
                     $ plot_name = $ plot-> getName ();
                     $ owner = $ plot-> getOwner ();
                     $ members = $ plot-> getMembersList ();
                     $ x_size = $ size [0];
                     $ z_size = $ size [1];

                     if ($ sender-> hasPermission ("pa.staff.devinfo")) {
                         $ plot-> isGrouped ()?  $ grpd = "\ n§3Gruppiert: §a₹": $ grpd = "\ n§3Gruppiert: §c✗";
                     } else {
                         $ grpd = null;
                     }

                     if ($ owner === null) {
                         $ owner = "Dieses Grundstück ist von niemandem";
                     }

                     if (! $ members) {
                         $ members = "Keine Mitglieder";
                     }

                     $ message = $ line.  Msgstr "Parzelleninformation der Parzelle: §b $ Parzellenname \ n§3Besitzer: §b $ Eigentümer \ n§3Mitglieder: §b $ Mitglieder $ Grpd".  $ Linie;
                     $ Sender-> Nachricht ($ message);
                 } else {
                     $ sender-> sendMessage ("§cU ist nicht in einem Plot");
                 }
                 return true;

             Fall "Handlung":
                 if (isset ($ args [0])) {
                     switch ($ args [0]) {
                         Fall "Setowner":
                             $ plot = Plot :: get ($ sender);
                             if ($ plot! == null) {
                                 if ($ sender-> hasPermission ("pa.staff.plot.setowner")) {
                                     if (isset ($ args [1])) {
                                         if (! empty ($ args [1])) {
                                             $ owner = $ args [1];
                                         } else {
                                             $ owner = null;
                                         }

                                     } else {
                                         $ owner = null;
                                     }

                                     $ ans = $ plot-> setOwner ($ owner, $ sender);
                                     if ($ ans) {
                                         $ sender-> sendMessage (TextFormat :: GREEN. $ owner. "§2ist jetzt der Eigentümer von plot §a". $ plot-> getName ());
                                     }
                                     else {
                                         $ sender-> sendMessage ("§4Dieser Player existiert nicht.");
                                     }
                                 } else {
                                     $ sender-> sendMessage ("§4Sie haben keinen Urlaub");
                                 }
                             } else {
                                 $ sender-> sendMessage ("§cU ist nicht in einem Plot");
                             }
                             break;
                         case "addmember":
                             if (isset ($ args [1])) {
                                 $ plot = Plot :: get ($ sender);
                                 if ($ plot! == null) {
                                     if ($ sender-> hasPermission ("pa.staff.plot.addmember") || $ plot-> isOwner ($ sender-> getName ()) {
                                         $ ans = $ plot-> addMember ($ args [1], $ sender);
                                         if ($ ans) {
                                             $ sender-> sendMessage ("§aSie haben erfolgreich ein Mitglied hinzugefügt");
                                         }
                                         else {
                                             if (! Member :: exists ($ args [1])) {
                                                 $ sender-> sendMessage ("§4Dieser Player existiert nicht.");
                                             } elseif ($ plot-> getMaxMembers () == count ($ plot-> getMembers ())) {
                                                 $ sender-> sendMessage ("§4Sie können keine Mitglieder mehr hinzufügen.");
                                             } elseif ($ plot-> isMember ($ args [1])) {
                                                 $ sender-> sendMessage ("§4Dieser Spieler ist bereits Mitglied der Handlung.");
                                             } else {
                                                 $ sender-> sendMessage ("§4 Es ist ein Fehler aufgetreten, bitte melden Sie dies einem Mitarbeiter.");
                                             }

                                         }
                                     } else {
                                         $ sender-> sendMessage ("§4Sie haben keinen Urlaub");
                                     }
                                 } else {
                                     $ sender-> sendMessage ("§4U ist nicht in einem Plot");
                                 }
                             } else {
                                 $ sender-> sendMessage ("§cU muss einen Mitgliedsnamen angeben.§4". $ command-> getUsage ());
                             }
                             break;

                         case "removemember":
                             if (isset ($ args [1])) {
                                 $ plot = Plot :: get ($ sender);
                                 if ($ plot! == null) {
                                     if ($ sender-> hasPermission ("pa.staff.plot.removemember") || $ plot-> isOwner ($ sender-> getName ()) {
                                         $ ans = $ plot-> removeMember ($ args [1], $ sender);
                                         $ ans?  $ msg = "§aDas Mitglied wurde erfolgreich entfernt": $ msg = "§4Dieser Spieler ist kein Mitglied der Handlung";
                                         $ Sender-> Nachricht ($ msg);
                                     } else {
                                         $ sender-> sendMessage ("§4Sie haben keine Berechtigungen");
                                     }
                                 } else {
                                     $ sender-> sendMessage ("§4U ist nicht in einem Plot");
                                 }

                             } else {
                                 $ sender-> sendMessage ("§cU muss einen Mitgliedsnamen angeben.");
                             }
                             break;

                         Groß- / Kleinschreibung "Löschen":
                             if ($ sender-> hasPermission ("pa.staff.plot.delete")) {
                                 $ plot = Plot :: get ($ sender, false);
                                 if ($ plot! == null) {
                                     $ Grundstück> delete ($ sender);
                                     $ sender-> sendMessage ("§aDer Plot wurde erfolgreich gelöscht");
                                 } else {
                                     $ sender-> sendMessage ("§4U ist nicht in einem Plot.");
                                 }


                             } else {
                                 $ sender-> sendMessage ("§4Sie haben keine Berechtigungen");
                             }
                             break;


                         case "set flag":
                             if (isset ($ args [1]) && isset ($ args [2]) && isset ($ args [3]) {
                                 $ plot = Plot :: get ($ sender);
                                 if ($ plot! == null) {
                                     if ($ sender-> hasPermission ("pa.staff.plot.setflag") || $ plot-> isOwner ($ sender-> getName ()) {
                                         if (strtolower ($ args [3]) == "false") {
                                             $ bool = false;
                                         }
                                         if (strtolower ($ args [3]) == "true") {
                                             $ bool = true;
                                         }
                                         $ res = $ plot-> setPermission ($ args [1], $ args [2], $ bool);

                                         if ($ res === false) {
                                             $ sender-> sendMessage ("§4Dieses Flag existiert nicht");
                                         } elseif (is_null ($ res)) {
                                             $ sender-> sendMessage ("§4U kann die Berechtigungen eines Spielers nicht ändern, der kein Mitglied der Handlung ist.");
                                         } elseif ($ res) {
                                             $ sender-> sendMessage ("§aDer Urlaub wurde erfolgreich angepasst!");
                                         }
                                     } else {
                                         $ sender-> sendMessage ("§4Sie haben keinen Urlaub");
                                     }
                                 } else {
                                     $ sender-> sendMessage ("§4U ist nicht in einem Plot.");
                                 }
                             }
                             else {
                                 $ sender-> sendMessage ("§4 Ungültige Argumente angegeben. §cBefehlsverwendung: / plot setflag [player] [permission] [true / false]");
                             }

                             break;

                         case "flags":
                             $ plot = Plot :: get ($ sender);
                             if ($ plot! == null) {
                                 if ($ sender-> hasPermission ("pa.staff.plot.flags") || $ plot-> isOwner ($ sender-> getName ()) {
                                     $ perm_mngr = neuer PermissionManager ($ plot);
                                     $ perms = $ perm_mngr-> permission_list;
                                     $ perms_text = "§bFlags, die Sie pro Benutzer festlegen können: \ n";
                                     foreach ($ perms wenn $ perm => $ value) {
                                         $ perms_text. = TextFormat :: DARK_AQUA.  $ perm.  "\ N";
                                     }
                                     $ Sender-> Nachricht ($ perms_text);
                             }
                             }
                             break;

                         Fall "publicchest":
                             $ plot = Plot :: get ($ sender);
                             if ($ plot! == null) {
                                 if ($ sender-> hasPermission ("pa.staff.plot.publicchest") || $ plot-> isOwner ($ sender-> getName ()) {
                                     $ this-> chest_register [$ sender-> getName ()] = true;
                                     $ sender-> sendMessage ("§aKlicken Sie auf das Kästchen, das Sie öffentlich / privat machen möchten.");
                                 } else {
                                     $ sender-> sendMessage ("§4Sie haben keinen Urlaub");
                                 }
                             } else {
                                 $ sender-> sendMessage ("§4U ist nicht in einem Plot.");
                             }
                             break;

                         case "userinfo":
                             if (isset ($ args [1])) {
                                 $ plot = Plot :: get ($ sender);
                                 if ($ plot! == null) {
                                     if ($ plot-> isOwner ($ sender-> getName ()) || $ sender-> hasPermission ("pa.staff.plot.userinfo")) {
                                         $ perms = $ plot-> getPlayerPermissions ($ args [1]);
                                         $ message = TextFormat :: GREEN.  $ args [1].  "\ N";
                                         if ($ perms! == null) {
                                             foreach ($ perms wenn $ key => $ value) {
                                                 $ wert?  $ txt = "§a₹": $ txt = "§c✗";
                                                 $ message. = TextFormat :: DARK_GREEN.  $ key.  ":".  $ txt.  "\ N";
                                             }
                                         } else {
                                             $ message = "§4Der Spieler hat keine Berechtigungen";
                                         }
                                         $ Sender-> Nachricht ($ message);
                                     } else {
                                         $ sender-> sendMessage ("§4Sie haben keinen Urlaub");
                                     }
                                 } else {
                                     $ sender-> sendMessage ("§4U ist nicht in einem Plot.");
                                 }
                             } else {
                                 $ sender-> sendMessage ("§4Bitte geben Sie einen Spielernamen an.");
                             }

                             break;


                         case "creategroup":
                             if ($ sender-> hasPermission ("pa.staff.plot.creategroup")) {
                                 if (isset ($ args [1]) && isset ($ args [2])) {
                                     $ plot = Plot :: get ($ sender);
                                     $ link_plot = Plot :: getPlotByName ($ args [2]);
                                     if ($ plot! == null && $ link_plot! == null) {
                                         if (! $ plot-> isGrouped () &&! $ link_plot-> isGrouped () &&! Group :: groupExists ($ args [1]) {
                                             $ res = Group :: saveGroup ($ args [1], $ plot, $ link_plot);
                                             $ res?  $ msg = "§aDie Gruppe wurde erfolgreich erstellt und der Plot zur Gruppe hinzugefügt."  : $ msg = "§4Der Master-Plot und der Slave-Plot können nicht identisch sein.";
                                             $ Sender-> Nachricht ($ msg);
                                         } else {
                                             $ sender-> sendMessage ("§4Geben Sie einen gültigen Plot- und Gruppennamen ein.");
                                         }
                                     } else {
                                         $ sender-> sendMessage ("§4U ist nicht in einem Plot oder der angegebene Plot existiert nicht.");
                                     }
                                 }
                                 else {
                                     $ sender-> sendMessage ("§4Bitte geben Sie einen Gruppennamen und einen Slave-Plot ein. §c / plot creategroup [Gruppenname] [Slave-Plot]");
                                 }
                             } else {
                                 $ sender-> sendMessage ("§4Sie haben keinen Urlaub");
                             }
                             break;

                         case "joingroup":
                             if ($ sender-> hasPermission ("pa.staff.plot.joingroup")) {
                                 if (isset ($ args [1])) {
                                     $ plot = Plot :: get ($ sender);
                                     if ($ plot! == null) {
                                         if (Group :: groupExists ($ args [1])) {
                                             $ group = Group :: getGroup ($ args [1]);
                                             $ Group-> addtogroup ($ Grundstück);
                                             $ sender-> sendMessage ("§aDer Plot wurde erfolgreich zur Gruppe hinzugefügt.");
                                         } else {
                                             $ sender-> sendMessage ("§4Die Gruppe existiert nicht");
                                         }
                                     } else {
                                         $ sender-> sendMessage ("§4U ist nicht in einem Plot");
                                     }
                                 } else {
                                     $ sender-> sendMessage ("§4Bitte geben Sie einen Gruppennamen ein");
                                 }
                             } else {
                                 $ sender-> sendMessage ("§4Sie haben keinen Urlaub");
                             }
                             break;

                         case "leavegroup":
                             if ($ sender-> hasPermission ("pa.staff.plot.leavegroup")) {
                                 $ plot = Plot :: get ($ sender);
                                 if ($ plot! == null && $ plot-> isGrouped ()) {
                                     $ Grundstück> getGroup () -> RemoveFromGroup ($ Grundstück);
                                     $ sender-> sendMessage ("§aDer Plot wurde erfolgreich aus der Gruppe entfernt.");
                                 } else {
                                     $ sender-> sendMessage ("§4U ist nicht in einem Plot.");
                                 }
                             } else {
                                 $ sender-> sendMessage ("§4Sie haben keinen Urlaub");
                             }
                             break;


                         Fall "Gruppe teilen":
                             if ($ sender-> hasPermission ("pa.staff.plot.deletegroup")) {
                                 if (! isset ($ args [1])) {
                                     $ plot = Plot :: get ($ sender);
                                     if ($ plot! == null && $ plot-> isGrouped ()) {
                                         $ Grundstück> getGroup () -> Löschen ();
                                         $ sender-> sendMessage ("§aDie Gruppe wurde erfolgreich gelöscht.");
                                     } else {
                                         $ sender-> sendMessage ("§4U ist nicht in einem Plot");
                                     }
                                 } else {
                                     if (Group :: groupExists ($ args [1])) {
                                         Group :: getGroup ($ args [1]) -> Delete ();
                                         $ sender-> sendMessage ("§aDie Gruppe wurde erfolgreich gelöscht.");
                                     }
                                 }
                             }
                             break;

                         case "setmaxmembers":
                             if ($ sender-> hasPermission ("pa.staff.plot.setmaxmembers")) {
                                 if (isset ($ args [1])) {
                                     if (is_numeric ($ args [1])) {
                                         $ plot = Plot :: get ($ sender);
                                         if ($ plot! == null) {
                                             $ Grundstück> setMaxMembers ($ args [1]);
                                             $ sender-> sendMessage ("§aDie maximale Mitgliederzahl wurde erfolgreich angepasst auf". TextFormat :: DARK_GREEN. $ args [1]);
                                         } else {
                                             $ sender-> sendMessage ("§4U ist nicht in einem Plot");
                                         }

                                     } else {
                                         $ sender-> sendMessage ("§4Bitte geben Sie eine Nummer ein.");
                                     }
                                 } else {
                                     $ sender-> sendMessage ("§4Bitte geben Sie eine Nummer ein.");
                                 }
                             }
                             break;
                         Standard:
                             $ commands = "";
                             $ plot = Plot :: get ($ sender);
                             if ($ plot! == null) {
                                 if ($ plot-> isOwner ($ sender-> getName ()) || $ sender-> hasPermission ("pa.staff")) {
                                     $ commands. = "§c / plot flags §4 Bietet eine Liste aller Flags, die Sie verwenden können. §c / plot publicchest §4Erstelle einen öffentlichen / privaten Cache \ n §c / plot addmember [member] §4Einen Member hinzufügen  Der Plot \ n§c / Plot-Entferner [Mitglied] §4Entfernt ein Mitglied des Plot \ n§c / Plot-Setflags [Spieler] [Flag] [Wahr / Falsch] §4 Berechtigungen pro Mitglied anpassen \ n§c / Plot-Benutzerinfo [  player] §4Dies enthält Informationen über einen bestimmten Benutzer. ";
                                 }
                             }
                             if ($ sender-> hasPermission ("pa.staff")) {
                                 $ commands. = "§c / Plotsetowner [Eigentümer] §4Setzt den Eigentümer eines Plots \ n§c / Ploterstellungsgruppe [Gruppenname] [Slave-Plot] §4Erstellen Sie eine Gruppe mit dem aktuellen Plot als Master-Plot \ n§c  / plot leavegroup §4Lösche den aktuellen Plot der Gruppe \ n§c / plot deletegroup [Gruppenname] §4Lösche eine Gruppe \ n§c / plot lösche §4Lösche den Plot ";
                             }
                             if (leer ($ -Befehle)) {
                                 $ commands = "§cOpsie! Es gibt keine Befehle, die Sie verwenden können.";
                             }
                             $ sender-> sendMessage ("§4Bitte verwenden Sie einen gültigen Befehl. \ n $ commands");

                             break;
                     }
                 } else {
                     $ commands = "";
                     $ plot = Plot :: get ($ sender);
                     if ($ plot! == null) {
                         if ($ plot-> isOwner ($ sender-> getName ()) || $ sender-> hasPermission ("pa.staff")) {
                             $ commands. = "§c / plot flags §4 Bietet eine Liste aller Flags, die Sie verwenden können. §c / plot publicchest §4Erstelle einen öffentlichen / privaten Cache \ n §c / plot addmember [member] §4Einen Member hinzufügen  Der Plot \ n§c / Plot-Entferner [Mitglied] §4Entfernt ein Mitglied des Plot \ n§c / Plot-Setflags [Spieler] [Flag] [Wahr / Falsch] §4 Berechtigungen pro Mitglied anpassen \ n§c / Plot-Benutzerinfo [  player] §4Dies enthält Informationen über einen bestimmten Benutzer. ";
                         }
                     }
                     if ($ sender-> hasPermission ("pa.staff")) {
                         $ commands. = "§c / Plotsetowner [Eigentümer] §4Setzt den Eigentümer eines Plots \ n§c / Ploterstellungsgruppe [Gruppenname] [Slave-Plot] §4Erstellen Sie eine Gruppe mit dem aktuellen Plot als Master-Plot \ n§c  / plot leavegroup §4Lösche den aktuellen Plot der Gruppe \ n§c / plot deletegroup [Gruppenname] §4Lösche eine Gruppe \ n§c / plot lösche §4Lösche den Plot ";
                     }
                     if (leer ($ -Befehle)) {
                         $ commands = "§cOpsie! Es gibt keine Befehle, die Sie verwenden können.";
                     }
                     $ sender-> sendMessage ("§4Bitte verwenden Sie einen gültigen Befehl. \ n $ commands");
                 }

                 return true;

             Fall "Flushperms":
                 if ($ sender instanceof ConsoleCommandSender) {
                     Permission :: resetAllPlotPermissions ();
                     $ sender-> sendMessage ("Perms wurden erfolgreich gelöscht!");
                 }
                 return true;
             Standard:
                 return false;
         }
     }

     / **
      * @return gemischt
      * /
     öffentliche statische Funktion getInstance (): Main
     {
         return Main :: $ instance;
     }
 }
