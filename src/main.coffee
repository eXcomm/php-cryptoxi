send_button = $('#send')
input = $('#input')
output = $('#output')
passphrase = $('#pass')
online = $('#online')


# Loader gif image string
$loader = $("<img src=\"lib/jcryption/examples/advanced/loading.gif\" alt=\"Loading...\" title=\"Loading...\" style=\"margin-right:15px;\" />")
$ ->
  register_buttons();
  $("#input, #send,#clearSessionStorage").attr "disabled", yes

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
    $.jCryption.authenticate password, "src/crypt.php?generateKeypair=true", "src/crypt.php?handshake=true", ((AESKey) ->
      
      # Enable the buttons and the textfield
      $("#input, #send,#clearSessionStorage").attr "disabled", false
      $("#status").html "<span style=\"font-size: 16px;\">Secure Connection Established!</span>"
      $("#status").removeClass("alert-info")
      $("#status").addClass("alert-success")
      # Save the current AES key into the sessionStorage
      sessionStorage.setItem "isConnected", "1"
      sessionStorage.setItem "password", password
    ), ->
      
      # Authentication failed
      alert "Authentication failed"

  else
    
    # Enable the buttons and the textfield
    $("#input, #send,#clearSessionStorage").attr "disabled", false
    $("#status").html "<span style=\"font-size: 16px;\">Secure Connection Established!</span>"
    $("#status").removeClass("alert-info")
    $("#status").addClass("alert-success")
    # Store the password from sessionStorage in the password variables
    password = sessionStorage.password
  $("#send").click ->
    
    # Encrypt the data with the AES key
    encryptedString = $.jCryption.encrypt($("#input").val(), password)
    encryptedPassphrase = $.jCryption.encrypt(passphrase.val(), password)
    
    # logging
    $("#output").prepend("<br/>").prepend "----------"
    $("#output").prepend("<br/>").prepend "Plaintext: " + $("#input").val()
    $("#output").prepend("<br/>").prepend "Encrypted: " + encryptedString
    
    # Send the data to the server
    $.ajax
      url: "src/crypt.php?"
      dataType: "json"
      type: "POST"
      data:
        jCryption: encryptedString
        passphrase: encryptedPassphrase

      success: (response) ->
        
        # Logging
        $("#output").prepend("<br/>").prepend "Served sent: " + response.data
        $("#output").prepend("<br/>").prepend "Decrypted: " + $.jCryption.decrypt(response.data, password)
        console.dir response


  $("#clearSessionStorage").click (e) ->
    
    # Clear the session storage
    sessionStorage.clear()
    
    # Refresh the page
    window.location = window.location
encrypt= (str)->
  password = sessionStorage.password
  $.jCryption.encrypt(str, password)
decrypt= (str)->
  password = sessionStorage.password
  $.jCryption.decrypt(str, password)

  