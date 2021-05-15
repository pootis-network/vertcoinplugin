![bg](https://user-images.githubusercontent.com/24478471/118374957-384fda80-b584-11eb-84c9-164d613dab61.png)

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
# backend api (for developers)
you can use server.php to communicate with the backend via POST requests.
this relives you of having to manage password encryption and stuff and makes developing game plugins for this easier.

you will need to send the following via HTTP POST (x-www-form-urlencoded) :
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
Commands | Required Values | Explanation 
------------ | ------------- | -------------
register | $username and $password | Creates a account in the database
editWallet | $username and $password and $wallet | Edits the saved Wallet address in the database after logging in
addPoints | $username and $points | Adds points to the account in database
removePoints | $username and $points | Removes points to the account in database
getbalance | $username | Returns the balance of a account in database
withdraw | $username and $password | attempts a withdrawl to saved wallet
