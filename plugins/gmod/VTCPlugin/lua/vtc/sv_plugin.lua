print("VertcoinPlugin - GMOD PLUGIN | LOADING")
util.AddNetworkString("vtc_login")
util.AddNetworkString("vtc_register")
util.AddNetworkString("vtc_editwallet")
util.AddNetworkString("vtc_withdraw")
util.AddNetworkString("open_vtc_menu")

-- chat commands
hook.Add( "PlayerSay", "PlayerSayExample", function( ply, text, team )
	if ( string.lower( text ) == "!vtc" ) then
			net.Start("open_vtc_menu")
			net.Send(ply)
			return ""
	end
end)

-- user wants to login
net.Receive("vtc_login", function( len, ply )
    -- ply:SetNWString("vtcUsername", "thexkey2")
    ply.Username = net.ReadString()

    if ply.Username == "" then
        ply:ChatPrint("You need to type in a Username to login.")
    else
        -- attempt login
        http.Post( BACKEND_URL, { gameserver_key = GAMESERVER_KEY, action = "login", username = ply.Username },

        -- onSuccess function
                function( body, length, headers, code )
                    print( body )
                    if body == "Account does not exist!" then
                        ply:ChatPrint("The account you are trying to login to does not exist.")
                    elseif body == "Success" then
                        ply:ChatPrint("Successfully binded account to SteamID.")
                    else
                        ply:ChatPrint("An unknown error has occured.")
                    end
                end,

        -- onFailure function
                function( message )
                    print( message )
                    ply:ChatPrint("An network error occured while attempting to sign in.")
                end

        )
    end


end)

-- user wants to register
net.Receive("vtc_register", function(len, ply)
    ply:SetNWString("vtcUsername", "thexkeytest")
    ply:SetNWString("vtcPassword", "password")
    ply.Username = ply:GetNWString("vtcUsername")
    ply.Password = ply:GetNWString("vtcPassword")



    if ply.Username == "" or ply.Password == "" then
        ply:ChatPrint("You need to type in a Username/Password to register.")
    else
        -- attempt register
        http.Post( BACKEND_URL, { gameserver_key = GAMESERVER_KEY, action = "register", username = ply.Username , password = ply.Password },

        -- onSuccess function
                function( body, length, headers, code )
                    print( body )
                    if body == "Username already exists in database!" then
                        ply:ChatPrint("The account username is taken.")
                    elseif body == "Success" then
                        ply:ChatPrint("Successfully registered and binded account to SteamID.")
                    else
                        ply:ChatPrint("An unknown error has occured.")
                    end
                end,

        -- onFailure function
                function( message )
                    print( message )
                    ply:ChatPrint("An network error occured while attempting to sign in.")
                end

        )
    end


end)

-- user wants to edit wallet address
net.Receive("vtc_editwallet", function(len, ply)
    ply:SetNWString("vtcUsername", "thexkeytest")
    ply:SetNWString("vtcPassword", "password")
    ply:SetNWString("vtcWallet", "3298573490iojvbsdoufhe")
    ply.Username = ply:GetNWString("vtcUsername")
    ply.Password = ply:GetNWString("vtcPassword")
    ply.Wallet = ply:GetNWString("vtcWallet")


    if ply.Username == "" or ply.Password == "" or ply.Wallet == "" then
        ply:ChatPrint("You need to type in all of the info.")
    else
        -- attempt edit
        http.Post( BACKEND_URL, { gameserver_key = GAMESERVER_KEY, action = "editWallet", username = ply.Username , password = ply.Password, wallet = ply.Wallet },

        -- onSuccess function
                function( body, length, headers, code )
                    print( body )
                    if body == "Invalid Account Details" then
                        ply:ChatPrint("The account login is incorrect.")
                    elseif body == "Account does not exist!" then
                        ply:ChatPrint("Account does not exist.")
                    elseif body == "Successfully Edited Wallet" then
                        ply:ChatPrint("Successfully edited saved wallet address.")
                    else
                        ply:ChatPrint("An unknown error has occurred.")
                    end
                end,

        -- onFailure function
                function( message )
                    print( message )
                    ply:ChatPrint("An network error occurred while attempting to sign in.")
                end

        )
    end


end)