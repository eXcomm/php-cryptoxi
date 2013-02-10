// Generated by CoffeeScript 1.4.0
var $loader, input, online, output, passphrase, register_buttons, send_button;

send_button = $('#send');

input = $('#input');

output = $('#output');

passphrase = $('#pass');

online = $('#online');

$loader = $("<img src=\"lib/jcryption/examples/advanced/loading.gif\" alt=\"Loading...\" title=\"Loading...\" style=\"margin-right:15px;\" />");

$(function() {
  var hashObj, password, randomString;
  register_buttons();
  $("#input, #send,#clearSessionStorage").attr("disabled", true);
  /*
    Creates a random string
    @returns {string} A random string
  */

  randomString = function() {
    var chars, i, randomstring, rnum, string_length;
    chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
    string_length = 128;
    randomstring = "";
    i = 0;
    while (i < string_length) {
      rnum = Math.floor(Math.random() * chars.length);
      randomstring += chars.substring(rnum, rnum + 1);
      i++;
    }
    return randomstring;
  };
  password = void 0;
  if (!sessionStorage.isConnected) {
    hashObj = new jsSHA(randomString(), "ASCII");
    password = hashObj.getHash("SHA-512", "HEX");
    $.jCryption.authenticate(password, "src/crypt.php?generateKeypair=true", "src/crypt.php?handshake=true", (function(AESKey) {
      $("#input, #send,#clearSessionStorage").attr("disabled", false);
      $("#status").html("<span style=\"font-size: 16px;\">Secure Connection Established!</span>");
      $("#status").removeClass("alert-info");
      $("#status").addClass("alert-success");
      sessionStorage.setItem("isConnected", "1");
      return sessionStorage.setItem("password", password);
    }), function() {
      return alert("Authentication failed");
    });
  } else {
    $("#input, #send,#clearSessionStorage").attr("disabled", false);
    $("#status").html("<span style=\"font-size: 16px;\">Secure Connection Established!</span>");
    $("#status").removeClass("alert-info");
    $("#status").addClass("alert-success");
    password = sessionStorage.password;
  }
  $("#send").click(function() {
    var encryptedPassphrase, encryptedString;
    encryptedString = $.jCryption.encrypt($("#input").val(), password);
    encryptedPassphrase = $.jCryption.encrypt(passphrase.val(), password);
    $("#output").prepend("<br/>").prepend("----------");
    $("#output").prepend("<br/>").prepend("Plaintext: " + $("#input").val());
    $("#output").prepend("<br/>").prepend("Encrypted: " + encryptedString);
    return $.ajax({
      url: "src/crypt.php?",
      dataType: "json",
      type: "POST",
      data: {
        jCryption: encryptedString,
        passphrase: encryptedPassphrase
      },
      success: function(response) {
        $("#output").prepend("<br/>").prepend("Served sent: " + response.data);
        $("#output").prepend("<br/>").prepend("Decrypted: " + $.jCryption.decrypt(response.data, password));
        return console.dir(response);
      }
    });
  });
  return $("#clearSessionStorage").click(function(e) {
    sessionStorage.clear();
    return window.location = window.location;
  });
});

register_buttons = function() {
  return $.get('src/sign_in_out.php', function(data) {
    $('#registerNav').html(data);
    $('#login').click(function() {
      return console.log("login");
    });
    $('#register').click(function() {
      return console.log("register");
    });
    return $('#logout').click(function() {
      return console.log("logout");
    });
  });
};
