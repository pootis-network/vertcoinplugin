# Vertcoin plugin
plugin to add Vertcoin rewards to various games.

powered by PHP and mySQL alongside the coinPayments api.
# supported games (planned):
* Unreal Tournament 1999

* Unreal Tournament 2017 

* Garry's Mod 

* Minecraft

* Team Fortress 2 

suggest us new games to add support for by making a plugin/issue! (game must have HTTP POST support and multiplayer)
# api
you can use server.php to communicate with the backend via POST requests.
this relives you of having to manage password encryption and stuff and makes developing game plugins for this easier.

you will need to send the following via $__POST in php:
Value | Explanation 
------------ | -------------
$username | username to look up in database (always required)
$password | used for securing sensitive functions (withdraw/edit wallet)
$gameserver_key | used to restrict unknown servers from connecting to your backend instance
$action | what to tell the server to do

optional values are:
Value | Explanation 
------------ | -------------
$wallet | Cryptocurrency address 
$points | how many points to add/remove (only used if $action is set to add/remove points)

$action will tell the backend server what your plugin what it wants to do 
valid functions are:

Value | Explanation 
------------ | -------------
register | $username and $password
editWallet | $username and $password and $wallet
addPoints | $username and $points
removePoints | $username and $points
