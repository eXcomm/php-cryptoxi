send_button = $('#send')
input = $('#input')
output = $('#output')
passphrase = $('#pass')
online = $('#online')

# Loader gif image string
$loader = $("<img src=\"loading.gif\" alt=\"Loading...\" title=\"Loading...\" style=\"margin-right:15px;\" />")
$ ->
  
  ###
  Creates a random string
  @returns {string} A random string
  ###
  randomString = ->
    chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz"
    string_length = 128
    randomstring = ""
    i = 0

    while i < string_length
      rnum = Math.floor(Math.random() * chars.length)
      randomstring += chars.substring(rnum, rnum + 1)
      i++
    
    #randomstring += cursor.x;
    #randomstring += cursor.y;
    randomstring
  
  # Initialize the password variable
  password = undefined
  
  # If a connection hasn't been made
  unless sessionStorage.isConnected
    
    # Create a random AES key
    hashObj = new jsSHA(randomString(), "ASCII")
    password = hashObj.getHash("SHA-512", "HEX")
    
    # Authenticate with the server
    $.jCryption.authenticate password, "encrypt.php?generateKeypair=true", "encrypt.php?handshake=true", ((AESKey) ->
      
      # Enable the buttons and the textfield
      $("#text, #send,#clearSessionStorage").attr "disabled", false
      $("#status").html "<span style=\"font-size: 16px;\">Let's Rock!</span>"
      
      # Save the current AES key into the sessionStorage
      sessionStorage.setItem "isConnected", "1"
      sessionStorage.setItem "password", password
    ), ->
      
      # Authentication failed
      alert "Authentication failed"

  else
    
    # Enable the buttons and the textfield
    $("#text, #send,#clearSessionStorage").attr "disabled", false
    $("#status").html "<span style=\"font-size: 16px;\">Let's Rock!</span>"
    
    # Store the password from sessionStorage in the password variables
    password = sessionStorage.password
  $("#send").click ->
    
    # Encrypt the data with the AES key
    encryptedString = $.jCryption.encrypt($("#text").val(), password)
    
    # logging
    $("#log").prepend("\n").prepend "----------"
    $("#log").prepend("\n").prepend "Plaintext: " + $("#text").val()
    $("#log").prepend("\n").prepend "Encrypted: " + encryptedString
    
    # Send the data to the server
    $.ajax
      url: "encrypt.php?"
      dataType: "json"
      type: "POST"
      data:
        jCryption: encryptedString

      success: (response) ->
        
        # Logging
        $("#log").prepend("\n").prepend "Served sent: " + response.data
        $("#log").prepend("\n").prepend "Decrypted: " + $.jCryption.decrypt(response.data, password)


  $("#clearSessionStorage").click (e) ->
    
    # Clear the session storage
    sessionStorage.clear()
    
    # Refresh the page
    window.location = window.location

