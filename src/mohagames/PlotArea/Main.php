<?php

  / **
 * _____ _ _
 * |  __ \ |  |  |  |  / \
 * |  |  __) ||  |  ___ |  |  _ / \ _ __ ___ __ _
 * |  ___ / |  |  / _ \ |  __ |  // \ \ |  `__ |  / _ \ / _` |
 * |  |  |  ||  (_) ||  |  _ / ____ \ |  |  |  __ / |  (_ | |
 * |  _ |  |  _ |  \ ___ / \ __ |  / _ / \ _ \ |  _ |  \ ___ |  \ __, _ |
 * @author Mohamed El Yousfi
 * /

Namespace mohagames\PlotArea;

use mohagames\PlotArea\listener\EventListener;
use mohagames\PlotArea\tasks\PositioningTask;
use mohagames\PlotArea\utils\Group;
use mohagames\PlotArea\utils\Member;
use mohagames\PlotArea\utils\PermissionManager;
use mohagames\PlotArea\utils\Plot;
use mohagames\PlotArea \utils\PublicChest;
use pocketmine\command \Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender.
use pocketmine\event\listener;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\ plugin\PluginBase;
use pocketmine\utils\config;
use pocketmine\utils\TextFormat;
use SQLite3;

Main class extends PluginBase implements listeners {
 public $ pos_1 = array ();
 public $ pos_2 = array ();
 public $ db;
 public static $ instance;
 public $ item;


 public function onEnable (): void
 {

 Main :: $ instance = $ this;

 $ config = new Config ($ this-> getDataFolder (). "config.yml", -1, array ("item_id" => ItemIds :: WOODEN_SHOVEL, "plot_popup" => true, "max_members" => 10, "  xp-add "=> 100," xp-deduct "=> 100));
 $ Config> save ();
 $ this-> item = $ config-> get ("item_id");
 $ popup = $ config-> get ("plot_popup");
 if ($ popup) {
 $ this-> getScheduler () -> scheduleRepeatingTask (new PositioningTask (), 30);
 }

 // This will create the databases if they don't already exist
 $ this-> db = new SQLite3 ($ this-> getDataFolder (). "PlotArea.db");
 $ this-> db-> query ("CREATE TABLE IF NO chests exist (chest_id INTEGER PRIMARY KEY AUTOINCREMENT, chest_location TEXT, chest_world TEXT, status TEXT, plot_id INTEGER)");
 $ this-> db-> query ("CREATE TABLE IF NO DRAWINGS ARE AVAILABLE (plot_id INTEGER PRIMARY KEY AUTOINCREMENT, plot_name TEXT, plot_owner TEXT, plot_world TEXT, plot_permissions TEXT, default NULL, max_members INTEGER group));
 $ this-> db-> query ("CREATE TABLE IF NO GROUPS EXIST (group_id INTEGER PRIMARY KEY AUTOINCREMENT, group_name TEXT, master_plot TEXT)");
 // This records the events
 $ this-> getServer () -> getPluginManager () -> registerEvents ($ this, $ this);
 $ this-> getServer () -> getPluginManager () -> registerEvents (new EventListener (), $ this);

 }

 / **
 * @param CommandSender $ sender
 * @param command $ command
 * @param string $ label
 * @param array $ args
 * @ Return Bool
 * @throws \ ReflectionException
 * /
 public function onCommand (CommandSender $ sender, Command $ command, String $ label, Array $ args): bool
 {
 switch ($ command-> getName ()) {
 Case "property wall":
 $ item = ItemFactory :: get ($ this-> item);
 $ item-> setCustomName ("Plot wall");
 $ Sender-> GetInventory () -> addItem ($ item);
 $ sender-> sendMessage ("§aYou have received a property wall");
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
 $ sender-> sendMessage ("§2The plot §a $ p_name §2 is saved successfully!");
 } else {
 $ sender-> sendMessage ("§4A plot already exists with this name");
 }
 } else {
 $ sender-> sendMessage ("§cU must provide a plot name. usage: / saveplot name");
 }

 } else {
 $ sender-> sendMessage ("You still have to determine the position of the plot.");
 }
 return true;

 Case "Action Info":
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
 $ plot-> isGrouped ()?  $ grpd = "\ n§3Grouped: §a ₹": $ grpd = "\ n§3Grouped: §c✗";
 } else {
 $ grpd = null;
 }

 if ($ owner === null) {
 $ owner = "This property is not owned by anyone";
 }

 if (! $ members) {
 $ members = "No Members";
 }

 $ message = $ line.  Msgstr "Parcel information of the parcel: §b $ parcel name \ n§3owner: §b $ owner \ n§3members: §b $ members $ Grpd".  $ Line;
 $ Sender-> Message ($ message);
 } else {
 $ sender-> sendMessage ("§cU is not in a plot");
 }
 return true;

 Case "action":
 if (isset ($ args [0])) {
 switch ($ args [0]) {
 Case "Setowner":
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
 $ sender-> sendMessage (TextFormat :: GREEN. $ owner. "§2 is now the owner of plot §a". $ plot-> getName ());
 }
 else {
 $ sender-> sendMessage ("§4This player does not exist.");
 }
 } else {
 $ sender-> sendMessage ("§4You are not on vacation");
 }
 } else {
 $ sender-> sendMessage ("§cU is not in a plot");
 }
 break;
 case "addmember":
 if (isset ($ args [1])) {
 $ plot = Plot :: get ($ sender);
 if ($ plot! == null) {
 if ($ sender-> hasPermission ("pa.staff.plot.addmember") || $ plot-> isOwner ($ sender-> getName ()) {
 $ ans = $ plot-> addMember ($ args [1], $ sender);
 if ($ ans) {
 $ sender-> sendMessage ("§aYou have successfully added a member");
 }
 else {
 if (! Member :: exists ($ args [1])) {
 $ sender-> sendMessage ("§4This player does not exist.");
 } elseif ($ plot-> getMaxMembers () == count ($ plot-> getMembers ())) {
 $ sender-> sendMessage ("§4You can no longer add members.");
 } elseif ($ plot-> isMember ($ args [1])) {
 $ sender-> sendMessage ("§4This player is already a member of the story.");
 } else {
 $ sender-> sendMessage ("§4 An error has occurred, please report this to an employee.");
 }

 }
 } else {
 $ sender-> sendMessage ("§4You are not on vacation");
 }
 } else {
 $ sender-> sendMessage ("§4U is not in a plot");
 }
 } else {
 $ sender-> sendMessage ("§cU must provide a member name. §4". $ command-> getUsage ());
 }
 break;

 case "removemember":
 if (isset ($ args [1])) {
 $ plot = Plot :: get ($ sender);
 if ($ plot! == null) {
 if ($ sender-> hasPermission ("pa.staff.plot.removemember") || $ plot-> isOwner ($ sender-> getName ()) {
 $ ans = $ plot-> removeMember ($ args [1], $ sender);
 $ ans?  $ msg = "§aThe member was successfully removed": $ msg = "§4This player is not a member of the plot";
 $ Sender-> Message ($ msg);
 } else {
 $ sender-> sendMessage ("§4You have no permissions");
 }
 } else {
 $ sender-> sendMessage ("§4U is not in a plot");
 }

 } else {
 $ sender-> sendMessage ("§cU must provide a member name.");
 }
 break;

 Upper / lower case "delete":
 if ($ sender-> hasPermission ("pa.staff.plot.delete")) {
 $ plot = Plot :: get ($ sender, false);
 if ($ plot! == null) {
 $ Property> delete ($ sender);
 $ sender-> sendMessage ("§aThe plot was deleted successfully");
 } else {
 $ sender-> sendMessage ("§4U is not in a plot.");
 }


 } else {
 $ sender-> sendMessage ("§4You have no permissions");
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
 $ sender-> sendMessage ("§4This flag does not exist");
 } elseif (is_null ($ res)) {
 $ sender-> sendMessage ("§4U cannot change the permissions of a player who is not a member of the plot.");
 } elseif ($ res) {
 $ sender-> sendMessage ("§aThe vacation was successfully adjusted!");
 }
 } else {
 $ sender-> sendMessage ("§4You are not on vacation");
 }
 } else {
 $ sender-> sendMessage ("§4U is not in a plot.");
 }
 }
 else {
 $ sender-> sendMessage ("§4 Invalid arguments specified. §cCommand use: / plot setflag [player] [permission] [true / false]");
 }

 break;

 case "flags":
 $ plot = Plot :: get ($ sender);
 if ($ plot! == null) {
 if ($ sender-> hasPermission ("pa.staff.plot.flags") || $ plot-> isOwner ($ sender-> getName ()) {
 $ perm_mngr = new PermissionManager ($ plot);
 $ perms = $ perm_mngr-> permission_list;
 $ perms_text = "§bFlags that you can set per user: \ n";
 foreach ($ perms if $ perm => $ value) {
 $ perms_text.  = TextFormat :: DARK_AQUA.  $ perm.  "\ N";
 }
 $ Sender-> Message ($ perms_text);
 }
 }
 break;

 Case "publicchest":
 $ plot = Plot :: get ($ sender);
 if ($ plot! == null) {
 if ($ sender-> hasPermission ("pa.staff.plot.publicchest") || $ plot-> isOwner ($ sender-> getName ()) {
 $ this-> chest_register [$ sender-> getName ()] = true;
 $ sender-> sendMessage ("§aClick the box you want to make public / private.");
 } else {
 $ sender-> sendMessage ("§4You are not on vacation");
 }
 } else {
 $ sender-> sendMessage ("§4U is not in a plot.");
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
 foreach ($ perms if $ key => $ value) {
 worth $?  $ txt = "§a ₹": $ txt = "§c✗";
 $ message.  = TextFormat :: DARK_GREEN.  $ key.  ":".  $ txt.  "\ N";
 }
 } else {
 $ message = "§4The player has no permissions";
 }
 $ Sender-> Message ($ message);
 } else {
 $ sender-> sendMessage ("§4You are not on vacation");
 }
 } else {
 $ sender-> sendMessage ("§4U is not in a plot.");
 }
 } else {
 $ sender-> sendMessage ("§4Please provide a player name.");
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
 $ res?  $ msg = "§aThe group was created successfully and the plot was added to the group."  : $ msg = "§4The master plot and the slave plot cannot be identical.";
 $ Sender-> Message ($ msg);
 } else {
 $ sender-> sendMessage ("§4Enter a valid plot and group name.");
 }
 } else {
 $ sender-> sendMessage ("§4U is not in a plot or the specified plot does not exist.");
 }
 }
 else {
 $ sender-> sendMessage ("§4Please enter a group name and a slave plot. §c / plot creategroup [group name] [slave plot]");
 }
 } else {
 $ sender-> sendMessage ("§4You are not on vacation");
 }
 break;

 case "joingroup":
 if ($ sender-> hasPermission ("pa.staff.plot.joingroup")) {
 if (isset ($ args [1])) {
 $ plot = Plot :: get ($ sender);
 if ($ plot! == null) {
 if (Group :: groupExists ($ args [1])) {
 $ group = Group :: getGroup ($ args [1]);
 $ Group-> addtogroup ($ property);
 $ sender-> sendMessage ("§aThe plot was successfully added to the group.");
 } else {
 $ sender-> sendMessage ("§4The group does not exist");
 }
 } else {
 $ sender-> sendMessage ("§4U is not in a plot");
 }
 } else {
 $ sender-> sendMessage ("§4Please enter a group name");
 }
 } else {
 $ sender-> sendMessage ("§4You are not on vacation");
 }
 break;

 case "leavegroup":
 if ($ sender-> hasPermission ("pa.staff.plot.leavegroup")) {
 $ plot = Plot :: get ($ sender);
 if ($ plot! == null && $ plot-> isGrouped ()) {
 $ Property> getGroup () -> RemoveFromGroup ($ property);
 $ sender-> sendMessage ("§aThe plot was successfully removed from the group.");
 } else {
 $ sender-> sendMessage ("§4U is not in a plot.");
 }
 } else {
 $ sender-> sendMessage ("§4You are not on vacation");
 }
 break;


 Case "share group":
 if ($ sender-> hasPermission ("pa.staff.plot.deletegroup")) {
 if (! isset ($ args [1])) {
 $ plot = Plot :: get ($ sender);
 if ($ plot! == null && $ plot-> isGrouped ()) {
 $ Plot> getGroup () -> delete ();
 $ sender-> sendMessage ("§aThe group was deleted successfully.");
 } else {
 $ sender-> sendMessage ("§4U is not in a plot");
 }
 } else {
 if (Group :: groupExists ($ args [1])) {
 Group :: getGroup ($ args [1]) -> Delete ();
 $ sender-> sendMessage ("§aThe group was deleted successfully.");
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
 $ Plot> setMaxMembers ($ args [1]);
 $ sender-> sendMessage ("§aThe maximum number of members was successfully adjusted to". TextFormat :: DARK_GREEN. $ args [1]);
 } else {
 $ sender-> sendMessage ("§4U is not in a plot");
 }

 } else {
 $ sender-> sendMessage ("§4Please enter a number.");
 }
 } else {
 $ sender-> sendMessage ("§4Please enter a number.");
 }
 }
 break;
 Default:
 $ commands = "";
 $ plot = Plot :: get ($ sender);
 if ($ plot! == null) {
 if ($ plot-> isOwner ($ sender-> getName ()) || $ sender-> hasPermission ("pa.staff")) {
 $ commands.  = "§C / plot flags §4 Provides a list of all flags you can use. §C / plot publicchest §4Create a public / private cache \ n §c / plot addmember [member] §4Add a member The plot \ n  §C / Plot Remover [Member] §4Removes a member of the Plot \ n§c / Plot Set Flag [Player] [Flag] [True / False] §4 Adjust Permissions Per Member \ n§c / Plot User Info [player  ] §4This contains information about a specific user. ";
 }
 }
 if ($ sender-> hasPermission ("pa.staff")) {
 $ commands.  = "§C / Plotsetowner [owner] §4 sets the owner of a plot \ n§c / plot creation group [group name] [slave plot] §4 create a group with the current plot as master plot \ n§c / plot leavegroup §  4Delete the current plot of the group \ n§c / plot deletegroup [group name] §4Delete a group \ n§c / plot delete §4Delete the plot ";
 }
 if (empty ($ commands)) {
 $ commands = "§cOpsie! There are no commands you can use.";
 }
 $ sender-> sendMessage ("§4Please use a valid command. \ n $ commands");

 break;
 }
 } else {
 $ commands = "";
 $ plot = Plot :: get ($ sender);
 if ($ plot! == null) {
 if ($ plot-> isOwner ($ sender-> getName ()) || $ sender-> hasPermission ("pa.staff")) {
 $ commands.  = "§C / plot flags §4 Provides a list of all flags you can use. §C / plot publicchest §4Create a public / private cache \ n §c / plot addmember [member] §4Add a member The plot \ n  §C / Plot Remover [Member] §4Removes a member of the Plot \ n§c / Plot Set Flag [Player] [Flag] [True / False] §4 Adjust Permissions Per Member \ n§c / Plot User Info [player  ] §4This contains information about a specific user. ";
 }
 }
 if ($ sender-> hasPermission ("pa.staff")) {
 $ commands.  = "§C / Plotsetowner [owner] §4 sets the owner of a plot \ n§c / plot creation group [group name] [slave plot] §4 create a group with the current plot as master plot \ n§c / plot leavegroup §  4Delete the current plot of the group \ n§c / plot deletegroup [group name] §4Delete a group \ n§c / plot delete §4Delete the plot ";
 }
 if (empty ($ commands)) {
 $ commands = "§cOpsie! There are no commands you can use.";
 }
 $ sender-> sendMessage ("§4Please use a valid command. \ n $ commands");
 }

 return true;

 Flushperms case:
 if ($ sender instanceof ConsoleCommandSender) {
 Permission :: resetAllPlotPermissions ();
 $ sender-> sendMessage ("Perms were deleted successfully!");
 }
 return true;
 Default:
 return false;
 }
 }

 / **
 * @return mixed
 * /
 public static function getInstance (): Main
 {
 return Main :: $ instance;
 }
  }
