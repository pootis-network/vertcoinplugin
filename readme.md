# Vertcoin plugin
plugin to add Vertcoin rewards to various games.

powered by PHP and mySQL alongside the coinPayments api.
# supported games (planned):
Unreal Tournament 1999

Garry's Mod

Minecraft

Team Fortress 2 
# api
you can use server.php to communicate with the backend via POST requests.
this relives you of having to manage password encryption and stuff and makes developing game plugins for this easier.

you will need to send the following via $__POST in php:
$username
$password
$gameserver_key
$action

optional values are
$wallet
$points

$action will tell the server what your plugin what it wants to do (ie sign up or edit an address or something.)
valid functions are:

register (requires $username and $password)

editWallet (requires $username and $password and $wallet)

addPoints (requires $username and $points)
removePoints (requires $username and $points)
