# sharenet-php (Version: CSDV)

Welcome to the ShareNet. Sharenet is a free project based on the p2p concept. Our aim is to create a network with hundreds of nodes relaying messages to all the peers stored in the server list. The relay is self learning, if a message has an unknown origin, the server may add it depending on your relay settings.

In order to make the relay easy to set up, we decided to write it in PHP, the script was first published in 2013. You just have to upload it at the root of your website and the relay will be working. However, when you set up a relay, it doesn't know any other peer. You will have to add a user in data/config/user.lst and send a message to another relay to make it add your server.

To send a message you can use the relay_one.php file function sharenetSend($user, $message); .

Each message is stored into the data/ folder: hash.shm (ShareNet Message), you can configure your relay not to store messages.

# Security Bugs:
PLEASE REPORT SECURITY BUGS. (see  'Have questions or need help to implement?', later in this file)

# Usage
require_once("relay_one.php");
sharenetSend("user","mymessage");

# Installation
1:
Upload files and foler:
php-ban-ip
update.php
relay_one.php

2:
call relay_one.php one time.

3:
add 'user' to the data user file. (data/config/user.lst)(line by line)

4:
add serverurl to the data user file. (data/config/serv.lst)(line by line)
Actual first seed: [https://idenlink.de/api_online/Sharenet/relay_one.php]

5:
Report your relay, to check the system. -> see'Have questions or need help to implement?'

# Features
- Supports everything from: https://github.com/benjaminrathelot/sharenet-php
- Can also handle urls like https://domain.tld/xxx....
- Blocks connections after 10000 requests (by default / ip ban)
- Includes automatic updating.
- The relay will ask the origin server if its user did sent this message and then, add the 2 servers into its list.
- Deleting old messages after a number of days.

 
 # Have questions or need help to implement?
  <a href="https://telegram.im/@dmd23" target="_blank"><img src="https://telegram.im/widget-logo/?v=2&bg=29a0da&color=ffffff&round=on&login=%40dmd23&t=&b=&width=100&height=100&fontsize=35&r=50" alt="@dmd23"></a>
 # TELEGRAM: @dmd23
  
  
  
  
  # Thank me
 - Work with me on the project.
 - Make sugesstions to improve the script.
 - donate some coffee bucks: 
 -   <a href="https://unze4u.de/UShort/s.php?i=fu" target="_blank"><img src="images/patreon_logo.png" alt="https://unze4u.de/UShort/s.php?i=fu" style="width:100px;height:100px;"></a>
  - <a href="https://unze4u.de/UShort/s.php?i=fu" target="_blank">PATREONS.COM </a>
  - <a href="https://unze4u.de/UShort/s.php?i=fv" target="_blank">PAYPAL </a>
  - LTC(Litcoin):  MLZ3ZDsWd2v5KPq8dVMZWbbsuH3xxZbgh5
  - You dont have Litecoin:
  -   <a href="https://unze4u.de/UShort/s.php?i=fx" target="_blank"><img src="images/changeio.JPG" alt="https://unze4u.de/UShort/s.php?i=fx" style="width:100%;height:100%"></a>


  
 # Licence
 Copyright (c) CS-Digital UG (hatungsbeschränkt) https://cs-digital-ug.de/ 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
THE USE OR OTHER DEALINGS IN THE SOFTWARE
  
  
  
  
  #####################################################################
# Haftung (german)
  - §1 Es wird keine haftung für das Projekt übernommen.
  - §2 Sollten einzelne Bestimmungen dieses Vertrages unwirksam oder undurchführbar sein oder nach Vertragsschluss unwirksam oder undurchführbar werden, bleibt davon die Wirksamkeit des Vertrages im Übrigen unberührt. An die Stelle der unwirksamen oder undurchführbaren Bestimmung soll diejenige wirksame und durchführbare Regelung treten, deren Wirkungen der wirtschaftlichen Zielsetzung am nächsten kommen, die die Vertragsparteien mit der unwirksamen bzw. undurchführbaren Bestimmung verfolgt haben. Die vorstehenden Bestimmungen gelten entsprechend für den Fall, dass sich der Vertrag als lückenhaft erweist.
  
  




#######################################################
# PROBS:
Inspired by: https://github.com/benjaminrathelot/sharenet-php

