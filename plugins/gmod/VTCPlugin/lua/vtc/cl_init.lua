print("hello client")   

net.Receive("open_vtc_menu", function()

menu = vgui.Create("DFrame")

menu:SetSize(250,300)
menu:SetPos((ScrW()/2) - 125, (ScrH()/2) - 250)
menu:SetTitle("Vertcoin Plugin - Menu")
menu:SetDraggable(false)
menu:ShowCloseButton(true)
menu:MakePopup(true)


local login = vgui.Create("Button", menu)
login:SetSize(50,50)
login:Dock( TOP )
login:SetText("login")

local editwallet = vgui.Create("Button", menu)
editwallet:SetSize((ScrW()/2.13)-400,50)
editwallet:SetPos(5,100)
editwallet:SetText("Edit Saved Wallet")

local editwallet = vgui.Create("Button", menu)
editwallet:SetSize((ScrW()/2.13)-400,50)
editwallet:SetPos(5,160)
editwallet:SetText("Withdraw")

local register = vgui.Create("Button", menu)
register:SetSize(50,50)
register:Dock( BOTTOM )
register:SetText("Register")


--
loginmenu = vgui.Create("DFrame")

loginmenu:SetSize(250,100)
loginmenu:SetPos((ScrW()/2) - 125, (ScrH()/2) - 250)
loginmenu:SetTitle("Vertcoin Plugin - login")
loginmenu:SetDraggable(false)
loginmenu:SetVisible(false)
loginmenu:ShowCloseButton(true)
loginmenu:MakePopup(true)

local TextEntryPH = vgui.Create( "DTextEntry", loginmenu )
TextEntryPH:Dock( TOP )
TextEntryPH:DockMargin( 0, 5, 0, 0 )
TextEntryPH:SetPlaceholderText( "Username" )
TextEntryPH.OnEnter = function( self )
    usrname =  self:GetValue() 
end


local loginbutton = vgui.Create("Button", loginmenu)
loginbutton:SetSize(50,20)
loginbutton:Dock( BOTTOM )
loginbutton:SetText("Login")

function loginbutton:OnMousePressed()
	--print(TextEntryPH:GetValue())
	net.Start("vtc_login")
	net.WriteString(TextEntryPH:GetValue())
	net.SendToServer()
end

--
registermenu = vgui.Create("DFrame")

registermenu:SetSize(250,100)
registermenu:SetPos((ScrW()/2) - 125, (ScrH()/2) - 250)
registermenu:SetTitle("Vertcoin Plugin - register")
registermenu:SetDraggable(false)
registermenu:SetVisible(false)
registermenu:ShowCloseButton(true)
registermenu:MakePopup(true)

local username = vgui.Create( "DTextEntry", registermenu )
username:Dock( TOP )
username:DockMargin( 0, 5, 0, 0 )
username:SetPlaceholderText( "Username" )
username.OnEnter = function( self )
    usrname = self:GetValue()
end

local password = vgui.Create( "DTextEntry", registermenu )
password:SetSize((ScrW()/2.13)-400,20)
password:SetPos(5,55)
password:SetPlaceholderText( "password" )
password.OnEnter = function( self )
    pw =  self:GetValue() 
end

local registerbutton = vgui.Create("Button", registermenu)
registerbutton:SetSize(50,20)
registerbutton:Dock( BOTTOM )
registerbutton:SetText("Register")


-- login 
function login:OnMousePressed()
	menu:SetVisible(false)
	loginmenu:SetVisible(true)
end

function register:OnMousePressed()
	menu:SetVisible(false)
	registermenu:SetVisible(true)
end

end)