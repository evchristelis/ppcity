<?php
class PHPMailer
{
    /////////////////////////////////////////////////
    // PUBLIC VARIABLES
    /////////////////////////////////////////////////

    /**
     * Email priority (1 = High, 3 = Normal, 5 = low).
     * @var int
     */
    var $Priority          = 3;

    /**
     * Sets the CharSet of the message.
     * @var string
     */
    var $CharSet           = "iso-8859-1";

    /**
     * Sets the Content-type of the message.
     * @var string
     */
    var $ContentType        = "text/plain";

    /**
     * Sets the Encoding of the message. Options for this are "8bit",
     * "7bit", "binary", "base64", and "quoted-printable".
     * @var string
     */
    var $Encoding          = "8bit";

    /**
     * Holds the most recent mailer error message.
     * @var string
     */
    var $ErrorInfo         = "";

    /**
     * Sets the From email address for the message.
     * @var string
     */
    var $From               = "root@localhost";

    /**
     * Sets the From name of the message.
     * @var string
     */
    var $FromName           = "Root User";

    /**
     * Sets the Sender email (Return-Path) of the message.  If not empty,
     * will be sent via -f to sendmail or as 'MAIL FROM' in smtp mode.
     * @var string
     */
    var $Sender            = "";

    /**
     * Sets the Subject of the message.
     * @var string
     */
    var $Subject           = "";

    /**
     * Sets the Body of the message.  This can be either an HTML or text body.
     * If HTML then run IsHTML(true).
     * @var string
     */
    var $Body               = "";

    /**
     * Sets the text-only body of the message.  This automatically sets the
     * email to multipart/alternative.  This body can be read by mail
     * clients that do not have HTML email capability such as mutt. Clients
     * that can read HTML will view the normal Body.
     * @var string
     */
    var $AltBody           = "";

    /**
     * Sets word wrapping on the body of the message to a given number of 
     * characters.
     * @var int
     */
    var $WordWrap          = 0;

    /**
     * Method to send mail: ("mail", "sendmail", or "smtp").
     * @var string
     */
    var $Mailer            = "mail";

    /**
     * Sets the path of the sendmail program.
     * @var string
     */
    var $Sendmail          = "/usr/sbin/sendmail";
    
    /**
     * Path to PHPMailer plugins.  This is now only useful if the SMTP class 
     * is in a different directory than the PHP include path.  
     * @var string
     */
    var $PluginDir         = "";

    /**
     *  Holds PHPMailer version.
     *  @var string
     */
    var $Version           = "1.73";

    /**
     * Sets the email address that a reading confirmation will be sent.
     * @var string
     */
    var $ConfirmReadingTo  = "";

    /**
     *  Sets the hostname to use in Message-Id and Received headers
     *  and as default HELO string. If empty, the value returned
     *  by SERVER_NAME is used or 'localhost.localdomain'.
     *  @var string
     */
    var $Hostname          = "";

    /////////////////////////////////////////////////
    // SMTP VARIABLES
    /////////////////////////////////////////////////

    /**
     *  Sets the SMTP hosts.  All hosts must be separated by a
     *  semicolon.  You can also specify a different port
     *  for each host by using this format: [hostname:port]
     *  (e.g. "smtp1.example.com:25;smtp2.example.com").
     *  Hosts will be tried in order.
     *  @var string
     */
    var $Host        = "localhost";

    /**
     *  Sets the default SMTP server port.
     *  @var int
     */
    var $Port        = 25;

    /**
     *  Sets the SMTP HELO of the message (Default is $Hostname).
     *  @var string
     */
    var $Helo        = "";

    /**
     *  Sets SMTP authentication. Utilizes the Username and Password variables.
     *  @var bool
     */
    var $SMTPAuth     = false;

    /**
     *  Sets SMTP username.
     *  @var string
     */
    var $Username     = "";

    /**
     *  Sets SMTP password.
     *  @var string
     */
    var $Password     = "";

    /**
     *  Sets the SMTP server timeout in seconds. This function will not 
     *  work with the win32 version.
     *  @var int
     */
    var $Timeout      = 10;

    /**
     *  Sets SMTP class debugging on or off.
     *  @var bool
     */
    var $SMTPDebug    = false;

    /**
     * Prevents the SMTP connection from being closed after each mail 
     * sending.  If this is set to true then to close the connection 
     * requires an explicit call to SmtpClose(). 
     * @var bool
     */
    var $SMTPKeepAlive = false;

    /**#@+
     * @access private
     */
    var $smtp            = NULL;
    var $to              = array();
    var $cc              = array();
    var $bcc             = array();
    var $ReplyTo         = array();
    var $attachment      = array();
    var $CustomHeader    = array();
    var $message_type    = "";
    var $boundary        = array();
    var $language        = array();
    var $error_count     = 0;
    var $LE              = "\n";
    /**#@-*/
    
    /////////////////////////////////////////////////
    // VARIABLE METHODS
    /////////////////////////////////////////////////

    /**
     * Sets message type to HTML.  
     * @param bool $bool
     * @return void
     */
    function IsHTML($bool) {
        if($bool == true)
            $this->ContentType = "text/html";
        else
            $this->ContentType = "text/plain";
    }

    /**
     * Sets Mailer to send message using SMTP.
     * @return void
     */
    function IsSMTP() {
        $this->Mailer = "smtp";
    }

    /**
     * Sets Mailer to send message using PHP mail() function.
     * @return void
     */
    function IsMail() {
        $this->Mailer = "mail";
    }

    /**
     * Sets Mailer to send message using the $Sendmail program.
     * @return void
     */
    function IsSendmail() {
        $this->Mailer = "sendmail";
    }

    /**
     * Sets Mailer to send message using the qmail MTA. 
     * @return void
     */
    function IsQmail() {
        $this->Sendmail = "/var/qmail/bin/sendmail";
        $this->Mailer = "sendmail";
    }


    /////////////////////////////////////////////////
    // RECIPIENT METHODS
    /////////////////////////////////////////////////

    /**
     * Adds a "To" address.  
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddAddress($address, $name = "") {
        $cur = count($this->to);
        $this->to[$cur][0] = trim($address);
        $this->to[$cur][1] = $name;
    }

    /**
     * Adds a "Cc" address. Note: this function works
     * with the SMTP mailer on win32, not with the "mail"
     * mailer.  
     * @param string $address
     * @param string $name
     * @return void
    */
    function AddCC($address, $name = "") {
        $cur = count($this->cc);
        $this->cc[$cur][0] = trim($address);
        $this->cc[$cur][1] = $name;
    }

    /**
     * Adds a "Bcc" address. Note: this function works
     * with the SMTP mailer on win32, not with the "mail"
     * mailer.  
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddBCC($address, $name = "") {
        $cur = count($this->bcc);
        $this->bcc[$cur][0] = trim($address);
        $this->bcc[$cur][1] = $name;
    }

    /**
     * Adds a "Reply-to" address.  
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddReplyTo($address, $name = "") {
        $cur = count($this->ReplyTo);
        $this->ReplyTo[$cur][0] = trim($address);
        $this->ReplyTo[$cur][1] = $name;
    }


    /////////////////////////////////////////////////
    // MAIL SENDING METHODS
    /////////////////////////////////////////////////

    /**
     * Creates message and assigns Mailer. If the message is
     * not sent successfully then it returns false.  Use the ErrorInfo
     * variable to view description of the error.  
     * @return bool
     */
    function Send() {
        $header = "";
        $body = "";
        $result = true;

        if((count($this->to) + count($this->cc) + count($this->bcc)) < 1)
        {
            $this->SetError($this->Lang("provide_address"));
            return false;
        }

        // Set whether the message is multipart/alternative
        if(!empty($this->AltBody))
            $this->ContentType = "multipart/alternative";

        $this->error_count = 0; // reset errors
        $this->SetMessageType();
        $header .= $this->CreateHeader();
        $body = $this->CreateBody();

        if($body == "") { return false; }

        // Choose the mailer
        switch($this->Mailer)
        {
            case "sendmail":
                $result = $this->SendmailSend($header, $body);
                break;
            case "mail":
                $result = $this->MailSend($header, $body);
                break;
            case "smtp":
                $result = $this->SmtpSend($header, $body);
                break;
            default:
            $this->SetError($this->Mailer . $this->Lang("mailer_not_supported"));
                $result = false;
                break;
        }

        return $result;
    }
    
    /**
     * Sends mail using the $Sendmail program.  
     * @access private
     * @return bool
     */
    function SendmailSend($header, $body) {
        if ($this->Sender != "")
            $sendmail = sprintf("%s -oi -f %s -t", $this->Sendmail, $this->Sender);
        else
            $sendmail = sprintf("%s -oi -t", $this->Sendmail);

        if(!@$mail = popen($sendmail, "w"))
        {
            $this->SetError($this->Lang("execute") . $this->Sendmail);
            return false;
        }

        fputs($mail, $header);
        fputs($mail, $body);
        
        $result = pclose($mail) >> 8 & 0xFF;
        if($result != 0)
        {
            $this->SetError($this->Lang("execute") . $this->Sendmail);
            return false;
        }

        return true;
    }

    /**
     * Sends mail using the PHP mail() function.  
     * @access private
     * @return bool
     */
    function MailSend($header, $body) {
        $to = "";
        for($i = 0; $i < count($this->to); $i++)
        {
            if($i != 0) { $to .= ", "; }
            $to .= $this->to[$i][0];
        }

        if ($this->Sender != "" && strlen(ini_get("safe_mode"))< 1)
        {
            $old_from = ini_get("sendmail_from");
            ini_set("sendmail_from", $this->Sender);
            $params = sprintf("-oi -f %s", $this->Sender);
            $rt = @mail($to, $this->EncodeHeader($this->Subject), $body, 
                        $header, $params);
        }
        else
            $rt = @mail($to, $this->EncodeHeader($this->Subject), $body, $header);

        if (isset($old_from))
            ini_set("sendmail_from", $old_from);

        if(!$rt)
        {
            $this->SetError($this->Lang("instantiate"));
            return false;
        }

        return true;
    }

    /**
     * Sends mail via SMTP using PhpSMTP (Author:
     * Chris Ryan).  Returns bool.  Returns false if there is a
     * bad MAIL FROM, RCPT, or DATA input.
     * @access private
     * @return bool
     */
    function SmtpSend($header, $body) {
        include_once($this->PluginDir . "class.smtp.php");
        $error = "";
        $bad_rcpt = array();

        if(!$this->SmtpConnect())
            return false;

        $smtp_from = ($this->Sender == "") ? $this->From : $this->Sender;
        if(!$this->smtp->Mail($smtp_from))
        {
            $error = $this->Lang("from_failed") . $smtp_from;
            $this->SetError($error);
            $this->smtp->Reset();
            return false;
        }

        // Attempt to send attach all recipients
        for($i = 0; $i < count($this->to); $i++)
        {
            if(!$this->smtp->Recipient($this->to[$i][0]))
                $bad_rcpt[] = $this->to[$i][0];
        }
        for($i = 0; $i < count($this->cc); $i++)
        {
            if(!$this->smtp->Recipient($this->cc[$i][0]))
                $bad_rcpt[] = $this->cc[$i][0];
        }
        for($i = 0; $i < count($this->bcc); $i++)
        {
            if(!$this->smtp->Recipient($this->bcc[$i][0]))
                $bad_rcpt[] = $this->bcc[$i][0];
        }

        if(count($bad_rcpt) > 0) // Create error message
        {
            for($i = 0; $i < count($bad_rcpt); $i++)
            {
                if($i != 0) { $error .= ", "; }
                $error .= $bad_rcpt[$i];
            }
            $error = $this->Lang("recipients_failed") . $error;
            $this->SetError($error);
            $this->smtp->Reset();
            return false;
        }

        if(!$this->smtp->Data($header . $body))
        {
            $this->SetError($this->Lang("data_not_accepted"));
            $this->smtp->Reset();
            return false;
        }
        if($this->SMTPKeepAlive == true)
            $this->smtp->Reset();
        else
            $this->SmtpClose();

        return true;
    }

    /**
     * Initiates a connection to an SMTP server.  Returns false if the 
     * operation failed.
     * @access private
     * @return bool
     */
    function SmtpConnect() {
        if($this->smtp == NULL) { $this->smtp = new SMTP(); }

        $this->smtp->do_debug = $this->SMTPDebug;
        $hosts = explode(";", $this->Host);
        $index = 0;
        $connection = ($this->smtp->Connected()); 

        // Retry while there is no connection
        while($index < count($hosts) && $connection == false)
        {
            if(strstr($hosts[$index], ":"))
                list($host, $port) = explode(":", $hosts[$index]);
            else
            {
                $host = $hosts[$index];
                $port = $this->Port;
            }

            if($this->smtp->Connect($host, $port, $this->Timeout))
            {
                if ($this->Helo != '')
                    $this->smtp->Hello($this->Helo);
                else
                    $this->smtp->Hello($this->ServerHostname());
        
                if($this->SMTPAuth)
                {
                    if(!$this->smtp->Authenticate($this->Username, 
                                                  $this->Password))
                    {
						
                        $this->SetError($this->Lang("authenticate"));
                        $this->smtp->Reset();
                        $connection = false;
                    }
                }
                $connection = true;
            }
            $index++;
        }
        if(!$connection)
            $this->SetError($this->Lang("connect_host"));

        return $connection;
    }

    /**
     * Closes the active SMTP session if one exists.
     * @return void
     */
    function SmtpClose() {
        if($this->smtp != NULL)
        {
            if($this->smtp->Connected())
            {
                $this->smtp->Quit();
                $this->smtp->Close();
            }
        }
    }

    /**
     * Sets the language for all class error messages.  Returns false 
     * if it cannot load the language file.  The default language type
     * is English.
     * @param string $lang_type Type of language (e.g. Portuguese: "br")
     * @param string $lang_path Path to the language file directory
     * @access public
     * @return bool
     */
    function SetLanguage($lang_type, $lang_path = "language/") {
		global $config;
	
		$lang_path = $config["physicalPath"] . "core/phpmailer/" . $lang_path;
			
		if(file_exists($lang_path.'phpmailer.lang-'.$lang_type.'.php'))
            include($lang_path.'phpmailer.lang-'.$lang_type.'.php');
        else if(file_exists($lang_path.'phpmailer.lang-en.php'))
            include($lang_path.'phpmailer.lang-en.php');
        else
        {
            $this->SetError("Could not load language file");
            return false;
        }
		
        $this->language = $PHPMAILER_LANG;
    
        return true;
    }

    /////////////////////////////////////////////////
    // MESSAGE CREATION METHODS
    /////////////////////////////////////////////////

    /**
     * Creates recipient headers.  
     * @access private
     * @return string
     */
    function AddrAppend($type, $addr) {
        $addr_str = $type . ": ";
        $addr_str .= $this->AddrFormat($addr[0]);
        if(count($addr) > 1)
        {
            for($i = 1; $i < count($addr); $i++)
                $addr_str .= ", " . $this->AddrFormat($addr[$i]);
        }
        $addr_str .= $this->LE;

        return $addr_str;
    }
    
    /**
     * Formats an address correctly. 
     * @access private
     * @return string
     */
    function AddrFormat($addr) {
        if(empty($addr[1]))
            $formatted = $addr[0];
        else
        {
            $formatted = $this->EncodeHeader($addr[1], 'phrase') . " <" . 
                         $addr[0] . ">";
        }

        return $formatted;
    }

    /**
     * Wraps message for use with mailers that do not
     * automatically perform wrapping and for quoted-printable.
     * Original written by philippe.  
     * @access private
     * @return string
     */
    function WrapText($message, $length, $qp_mode = false) {
        $soft_break = ($qp_mode) ? sprintf(" =%s", $this->LE) : $this->LE;

        $message = $this->FixEOL($message);
        if (substr($message, -1) == $this->LE)
            $message = substr($message, 0, -1);

        $line = explode($this->LE, $message);
        $message = "";
        for ($i=0 ;$i < count($line); $i++)
        {
          $line_part = explode(" ", $line[$i]);
          $buf = "";
          for ($e = 0; $e<count($line_part); $e++)
          {
              $word = $line_part[$e];
              if ($qp_mode and (strlen($word) > $length))
              {
                $space_left = $length - strlen($buf) - 1;
                if ($e != 0)
                {
                    if ($space_left > 20)
                    {
                        $len = $space_left;
                        if (substr($word, $len - 1, 1) == "=")
                          $len--;
                        elseif (substr($word, $len - 2, 1) == "=")
                          $len -= 2;
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);
                        $buf .= " " . $part;
                        $message .= $buf . sprintf("=%s", $this->LE);
                    }
                    else
                    {
                        $message .= $buf . $soft_break;
                    }
                    $buf = "";
                }
                while (strlen($word) > 0)
                {
                    $len = $length;
                    if (substr($word, $len - 1, 1) == "=")
                        $len--;
                    elseif (substr($word, $len - 2, 1) == "=")
                        $len -= 2;
                    $part = substr($word, 0, $len);
                    $word = substr($word, $len);

                    if (strlen($word) > 0)
                        $message .= $part . sprintf("=%s", $this->LE);
                    else
                        $buf = $part;
                }
              }
              else
              {
                $buf_o = $buf;
                $buf .= ($e == 0) ? $word : (" " . $word); 

                if (strlen($buf) > $length and $buf_o != "")
                {
                    $message .= $buf_o . $soft_break;
                    $buf = $word;
                }
              }
          }
          $message .= $buf . $this->LE;
        }

        return $message;
    }
    
    /**
     * Set the body wrapping.
     * @access private
     * @return void
     */
    function SetWordWrap() {
        if($this->WordWrap < 1)
            return;
            
        switch($this->message_type)
        {
           case "alt":
              // fall through
           case "alt_attachments":
              $this->AltBody = $this->WrapText($this->AltBody, $this->WordWrap);
              break;
           default:
              $this->Body = $this->WrapText($this->Body, $this->WordWrap);
              break;
        }
    }

    /**
     * Assembles message header.  
     * @access private
     * @return string
     */
    function CreateHeader() {
        $result = "";
        
        // Set the boundaries
        $uniq_id = md5(uniqid(time()));
        $this->boundary[1] = "b1_" . $uniq_id;
        $this->boundary[2] = "b2_" . $uniq_id;

        $result .= $this->HeaderLine("Date", $this->RFCDate());
        if($this->Sender == "")
            $result .= $this->HeaderLine("Return-Path", trim($this->From));
        else
            $result .= $this->HeaderLine("Return-Path", trim($this->Sender));
        
        // To be created automatically by mail()
        if($this->Mailer != "mail")
        {
            if(count($this->to) > 0)
                $result .= $this->AddrAppend("To", $this->to);
            else if (count($this->cc) == 0)
                $result .= $this->HeaderLine("To", "undisclosed-recipients:;");
            if(count($this->cc) > 0)
                $result .= $this->AddrAppend("Cc", $this->cc);
        }

        $from = array();
        $from[0][0] = trim($this->From);
        $from[0][1] = $this->FromName;
        $result .= $this->AddrAppend("From", $from); 

        // sendmail and mail() extract Bcc from the header before sending
        if((($this->Mailer == "sendmail") || ($this->Mailer == "mail")) && (count($this->bcc) > 0))
            $result .= $this->AddrAppend("Bcc", $this->bcc);

        if(count($this->ReplyTo) > 0)
            $result .= $this->AddrAppend("Reply-to", $this->ReplyTo);

        // mail() sets the subject itself
        if($this->Mailer != "mail")
            $result .= $this->HeaderLine("Subject", $this->EncodeHeader(trim($this->Subject)));

        $result .= sprintf("Message-ID: <%s@%s>%s", $uniq_id, $this->ServerHostname(), $this->LE);
        $result .= $this->HeaderLine("X-Priority", $this->Priority);
        $result .= $this->HeaderLine("X-Mailer", "PHPMailer [version " . $this->Version . "]");
        
        if($this->ConfirmReadingTo != "")
        {
            $result .= $this->HeaderLine("Disposition-Notification-To", 
                       "<" . trim($this->ConfirmReadingTo) . ">");
        }

        // Add custom headers
        for($index = 0; $index < count($this->CustomHeader); $index++)
        {
            $result .= $this->HeaderLine(trim($this->CustomHeader[$index][0]), 
                       $this->EncodeHeader(trim($this->CustomHeader[$index][1])));
        }
        $result .= $this->HeaderLine("MIME-Version", "1.0");

        switch($this->message_type)
        {
            case "plain":
                $result .= $this->HeaderLine("Content-Transfer-Encoding", $this->Encoding);
                $result .= sprintf("Content-Type: %s; charset=\"%s\"",
                                    $this->ContentType, $this->CharSet);
                break;
            case "attachments":
                // fall through
            case "alt_attachments":
                if($this->InlineImageExists())
                {
                    $result .= sprintf("Content-Type: %s;%s\ttype=\"text/html\";%s\tboundary=\"%s\"%s", 
                                    "multipart/related", $this->LE, $this->LE, 
                                    $this->boundary[1], $this->LE);
                }
                else
                {
                    $result .= $this->HeaderLine("Content-Type", "multipart/mixed;");
                    $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                }
                break;
            case "alt":
                $result .= $this->HeaderLine("Content-Type", "multipart/alternative;");
                $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
        }

        if($this->Mailer != "mail")
            $result .= $this->LE.$this->LE;

        return $result;
    }

    /**
     * Assembles the message body.  Returns an empty string on failure.
     * @access private
     * @return string
     */
    function CreateBody() {
        $result = "";

        $this->SetWordWrap();

        switch($this->message_type)
        {
            case "alt":
                $result .= $this->GetBoundary($this->boundary[1], "", 
                                              "text/plain", "");
                $result .= $this->EncodeString($this->AltBody, $this->Encoding);
                $result .= $this->LE.$this->LE;
                $result .= $this->GetBoundary($this->boundary[1], "", 
                                              "text/html", "");
                
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE.$this->LE;
    
                $result .= $this->EndBoundary($this->boundary[1]);
                break;
            case "plain":
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                break;
            case "attachments":
                $result .= $this->GetBoundary($this->boundary[1], "", "", "");
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE;
     
                $result .= $this->AttachAll();
                break;
            case "alt_attachments":
                $result .= sprintf("--%s%s", $this->boundary[1], $this->LE);
                $result .= sprintf("Content-Type: %s;%s" .
                                   "\tboundary=\"%s\"%s",
                                   "multipart/alternative", $this->LE, 
                                   $this->boundary[2], $this->LE.$this->LE);
    
                // Create text body
                $result .= $this->GetBoundary($this->boundary[2], "", 
                                              "text/plain", "") . $this->LE;

                $result .= $this->EncodeString($this->AltBody, $this->Encoding);
                $result .= $this->LE.$this->LE;
    
                // Create the HTML body
                $result .= $this->GetBoundary($this->boundary[2], "", 
                                              "text/html", "") . $this->LE;
    
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE.$this->LE;

                $result .= $this->EndBoundary($this->boundary[2]);
                
                $result .= $this->AttachAll();
                break;
        }
        if($this->IsError())
            $result = "";

        return $result;
    }

    /**
     * Returns the start of a message boundary.
     * @access private
     */
    function GetBoundary($boundary, $charSet, $contentType, $encoding) {
        $result = "";
        if($charSet == "") { $charSet = $this->CharSet; }
        if($contentType == "") { $contentType = $this->ContentType; }
        if($encoding == "") { $encoding = $this->Encoding; }

        $result .= $this->TextLine("--" . $boundary);
        $result .= sprintf("Content-Type: %s; charset = \"%s\"", 
                            $contentType, $charSet);
        $result .= $this->LE;
        $result .= $this->HeaderLine("Content-Transfer-Encoding", $encoding);
        $result .= $this->LE;
       
        return $result;
    }
    
    /**
     * Returns the end of a message boundary.
     * @access private
     */
    function EndBoundary($boundary) {
        return $this->LE . "--" . $boundary . "--" . $this->LE; 
    }
    
    /**
     * Sets the message type.
     * @access private
     * @return void
     */
    function SetMessageType() {
        if(count($this->attachment) < 1 && strlen($this->AltBody) < 1)
            $this->message_type = "plain";
        else
        {
            if(count($this->attachment) > 0)
                $this->message_type = "attachments";
            if(strlen($this->AltBody) > 0 && count($this->attachment) < 1)
                $this->message_type = "alt";
            if(strlen($this->AltBody) > 0 && count($this->attachment) > 0)
                $this->message_type = "alt_attachments";
        }
    }

    /**
     * Returns a formatted header line.
     * @access private
     * @return string
     */
    function HeaderLine($name, $value) {
        return $name . ": " . $value . $this->LE;
    }

    /**
     * Returns a formatted mail line.
     * @access private
     * @return string
     */
    function TextLine($value) {
        return $value . $this->LE;
    }

    /////////////////////////////////////////////////
    // ATTACHMENT METHODS
    /////////////////////////////////////////////////

    /**
     * Adds an attachment from a path on the filesystem.
     * Returns false if the file could not be found
     * or accessed.
     * @param string $path Path to the attachment.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @return bool
     */
    function AddAttachment($path, $name = "", $encoding = "base64", 
                           $type = "application/octet-stream") {
        if(!@is_file($path))
        {
            $this->SetError($this->Lang("file_access") . $path);
            return false;
        }

        $filename = basename($path);
        if($name == "")
            $name = $filename;

        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;

        return true;
    }

    /**
     * Attaches all fs, string, and binary attachments to the message.
     * Returns an empty string on failure.
     * @access private
     * @return string
     */
    function AttachAll() {
        // Return text of body
        $mime = array();

        // Add all attachments
        for($i = 0; $i < count($this->attachment); $i++)
        {
            // Check for string attachment
            $bString = $this->attachment[$i][5];
            if ($bString)
                $string = $this->attachment[$i][0];
            else
                $path = $this->attachment[$i][0];

            $filename    = $this->attachment[$i][1];
            $name        = $this->attachment[$i][2];
            $encoding    = $this->attachment[$i][3];
            $type        = $this->attachment[$i][4];
            $disposition = $this->attachment[$i][6];
            $cid         = $this->attachment[$i][7];
            
            $mime[] = sprintf("--%s%s", $this->boundary[1], $this->LE);
            $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $name, $this->LE);
            $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);

            if($disposition == "inline")
                $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);

            $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", 
                              $disposition, $name, $this->LE.$this->LE);

            // Encode as string attachment
            if($bString)
            {
                $mime[] = $this->EncodeString($string, $encoding);
                if($this->IsError()) { return ""; }
                $mime[] = $this->LE.$this->LE;
            }
            else
            {
                $mime[] = $this->EncodeFile($path, $encoding);                
                if($this->IsError()) { return ""; }
                $mime[] = $this->LE.$this->LE;
            }
        }

        $mime[] = sprintf("--%s--%s", $this->boundary[1], $this->LE);

        return join("", $mime);
    }
    
    /**
     * Encodes attachment in requested format.  Returns an
     * empty string on failure.
     * @access private
     * @return string
     */
    function EncodeFile ($path, $encoding = "base64") {
        if(!@$fd = fopen($path, "rb"))
        {
            $this->SetError($this->Lang("file_open") . $path);
            return "";
        }
        $magic_quotes = get_magic_quotes_runtime();
        set_magic_quotes_runtime(0);
        $file_buffer = fread($fd, filesize($path));
        $file_buffer = $this->EncodeString($file_buffer, $encoding);
        fclose($fd);
        set_magic_quotes_runtime($magic_quotes);

        return $file_buffer;
    }

    /**
     * Encodes string to requested format. Returns an
     * empty string on failure.
     * @access private
     * @return string
     */
    function EncodeString ($str, $encoding = "base64") {
        $encoded = "";
        switch(strtolower($encoding)) {
          case "base64":
              // chunk_split is found in PHP >= 3.0.6
              $encoded = chunk_split(base64_encode($str), 76, $this->LE);
              break;
          case "7bit":
          case "8bit":
              $encoded = $this->FixEOL($str);
              if (substr($encoded, -(strlen($this->LE))) != $this->LE)
                $encoded .= $this->LE;
              break;
          case "binary":
              $encoded = $str;
              break;
          case "quoted-printable":
              $encoded = $this->EncodeQP($str);
              break;
          default:
              $this->SetError($this->Lang("encoding") . $encoding);
              break;
        }
        return $encoded;
    }

    /**
     * Encode a header string to best of Q, B, quoted or none.  
     * @access private
     * @return string
     */
    function EncodeHeader ($str, $position = 'text') {
      $x = 0;
      
      switch (strtolower($position)) {
        case 'phrase':
          if (!preg_match('/[\200-\377]/', $str)) {
            // Can't use addslashes as we don't know what value has magic_quotes_sybase.
            $encoded = addcslashes($str, "\0..\37\177\\\"");

            if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str))
              return ($encoded);
            else
              return ("\"$encoded\"");
          }
          $x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
          break;
        case 'comment':
          $x = preg_match_all('/[()"]/', $str, $matches);
          // Fall-through
        case 'text':
        default:
          $x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
          break;
      }

      if ($x == 0)
        return ($str);

      $maxlen = 75 - 7 - strlen($this->CharSet);
      // Try to select the encoding which should produce the shortest output
      if (strlen($str)/3 < $x) {
        $encoding = 'B';
        $encoded = base64_encode($str);
        $maxlen -= $maxlen % 4;
        $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
      } else {
        $encoding = 'Q';
        $encoded = $this->EncodeQ($str, $position);
        $encoded = $this->WrapText($encoded, $maxlen, true);
        $encoded = str_replace("=".$this->LE, "\n", trim($encoded));
      }

      $encoded = preg_replace('/^(.*)$/m', " =?".$this->CharSet."?$encoding?\\1?=", $encoded);
      $encoded = trim(str_replace("\n", $this->LE, $encoded));
      
      return $encoded;
    }
    
    /**
     * Encode string to quoted-printable.  
     * @access private
     * @return string
     */
    function EncodeQP ($str) {
        $encoded = $this->FixEOL($str);
        if (substr($encoded, -(strlen($this->LE))) != $this->LE)
            $encoded .= $this->LE;

        // Replace every high ascii, control and = characters
        $encoded = preg_replace('/([\000-\010\013\014\016-\037\075\177-\377])/e',
                  "'='.sprintf('%02X', ord('\\1'))", $encoded);
        // Replace every spaces and tabs when it's the last character on a line
        $encoded = preg_replace("/([\011\040])".$this->LE."/e",
                  "'='.sprintf('%02X', ord('\\1')).'".$this->LE."'", $encoded);

        // Maximum line length of 76 characters before CRLF (74 + space + '=')
        $encoded = $this->WrapText($encoded, 74, true);

        return $encoded;
    }

    /**
     * Encode string to q encoding.  
     * @access private
     * @return string
     */
    function EncodeQ ($str, $position = "text") {
        // There should not be any EOL in the string
        $encoded = preg_replace("[\r\n]", "", $str);

        switch (strtolower($position)) {
          case "phrase":
            $encoded = preg_replace("/([^A-Za-z0-9!*+\/ -])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
            break;
          case "comment":
            $encoded = preg_replace("/([\(\)\"])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
          case "text":
          default:
            // Replace every high ascii, control =, ? and _ characters
            $encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e',
                  "'='.sprintf('%02X', ord('\\1'))", $encoded);
            break;
        }
        
        // Replace every spaces to _ (more readable than =20)
        $encoded = str_replace(" ", "_", $encoded);

        return $encoded;
    }

    /**
     * Adds a string or binary attachment (non-filesystem) to the list.
     * This method can be used to attach ascii or binary data,
     * such as a BLOB record from a database.
     * @param string $string String attachment data.
     * @param string $filename Name of the attachment.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @return void
     */
    function AddStringAttachment($string, $filename, $encoding = "base64", 
                                 $type = "application/octet-stream") {
        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $string;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $filename;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = true; // isString
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;
    }
    
    /**
     * Adds an embedded attachment.  This can include images, sounds, and 
     * just about any other document.  Make sure to set the $type to an 
     * image type.  For JPEG images use "image/jpeg" and for GIF images 
     * use "image/gif".
     * @param string $path Path to the attachment.
     * @param string $cid Content ID of the attachment.  Use this to identify 
     *        the Id for accessing the image in an HTML form.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.  
     * @return bool
     */
    function AddEmbeddedImage($path, $cid, $name = "", $encoding = "base64", 
                              $type = "application/octet-stream") {
    
        if(!@is_file($path))
        {
            $this->SetError($this->Lang("file_access") . $path);
            return false;
        }

        $filename = basename($path);
        if($name == "")
            $name = $filename;

        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "inline";
        $this->attachment[$cur][7] = $cid;
    
        return true;
    }
    
    /**
     * Returns true if an inline attachment is present.
     * @access private
     * @return bool
     */
    function InlineImageExists() {
        $result = false;
        for($i = 0; $i < count($this->attachment); $i++)
        {
            if($this->attachment[$i][6] == "inline")
            {
                $result = true;
                break;
            }
        }
        
        return $result;
    }

    /////////////////////////////////////////////////
    // MESSAGE RESET METHODS
    /////////////////////////////////////////////////

    /**
     * Clears all recipients assigned in the TO array.  Returns void.
     * @return void
     */
    function ClearAddresses() {
        $this->to = array();
    }

    /**
     * Clears all recipients assigned in the CC array.  Returns void.
     * @return void
     */
    function ClearCCs() {
        $this->cc = array();
    }

    /**
     * Clears all recipients assigned in the BCC array.  Returns void.
     * @return void
     */
    function ClearBCCs() {
        $this->bcc = array();
    }

    /**
     * Clears all recipients assigned in the ReplyTo array.  Returns void.
     * @return void
     */
    function ClearReplyTos() {
        $this->ReplyTo = array();
    }

    /**
     * Clears all recipients assigned in the TO, CC and BCC
     * array.  Returns void.
     * @return void
     */
    function ClearAllRecipients() {
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
    }

    /**
     * Clears all previously set filesystem, string, and binary
     * attachments.  Returns void.
     * @return void
     */
    function ClearAttachments() {
        $this->attachment = array();
    }

    /**
     * Clears all custom headers.  Returns void.
     * @return void
     */
    function ClearCustomHeaders() {
        $this->CustomHeader = array();
    }


    /////////////////////////////////////////////////
    // MISCELLANEOUS METHODS
    /////////////////////////////////////////////////

    /**
     * Adds the error message to the error container.
     * Returns void.
     * @access private
     * @return void
     */
    function SetError($msg) {
        $this->error_count++;
        $this->ErrorInfo = $msg;
    }

    /**
     * Returns the proper RFC 822 formatted date. 
     * @access private
     * @return string
     */
    function RFCDate() {
        $tz = date("Z");
        $tzs = ($tz < 0) ? "-" : "+";
        $tz = abs($tz);
        $tz = ($tz/3600)*100 + ($tz%3600)/60;
        $result = sprintf("%s %s%04d", date("D, j M Y H:i:s"), $tzs, $tz);

        return $result;
    }
    
    /**
     * Returns the appropriate server variable.  Should work with both 
     * PHP 4.1.0+ as well as older versions.  Returns an empty string 
     * if nothing is found.
     * @access private
     * @return mixed
     */
    function ServerVar($varName) {
        global $HTTP_SERVER_VARS;
        global $HTTP_ENV_VARS;

        if(!isset($_SERVER))
        {
            $_SERVER = $HTTP_SERVER_VARS;
            if(!isset($_SERVER["REMOTE_ADDR"]))
                $_SERVER = $HTTP_ENV_VARS; // must be Apache
        }
        
        if(isset($_SERVER[$varName]))
            return $_SERVER[$varName];
        else
            return "";
    }

    /**
     * Returns the server hostname or 'localhost.localdomain' if unknown.
     * @access private
     * @return string
     */
    function ServerHostname() {
        if ($this->Hostname != "")
            $result = $this->Hostname;
        elseif ($this->ServerVar('SERVER_NAME') != "")
            $result = $this->ServerVar('SERVER_NAME');
        else
            $result = "localhost.localdomain";

        return $result;
    }

    /**
     * Returns a message in the appropriate language.
     * @access private
     * @return string
     */
    function Lang($key) {
        if(count($this->language) < 1)
            $this->SetLanguage("en"); // set the default language
    
        if(isset($this->language[$key]))
            return $this->language[$key];
        else
            return "Language string failed to load: " . $key;
    }
    
    /**
     * Returns true if an error occurred.
     * @return bool
     */
    function IsError() {
        return ($this->error_count > 0);
    }

    /**
     * Changes every end of line from CR or LF to CRLF.  
     * @access private
     * @return string
     */
    function FixEOL($str) {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\r", "\n", $str);
        $str = str_replace("\n", $this->LE, $str);
        return $str;
    }

    /**
     * Adds a custom header. 
     * @return void
     */
    function AddCustomHeader($custom_header) {
        $this->CustomHeader[] = explode(":", $custom_header, 2);
    }
}

class sql_db
{
	var $db_connect_id;
	var $query_result;
	var $row = array();
	var $rowset = array();
	var $num_queries = 0;

	function sql_db($sqlserver, $sqluser, $sqlpassword, $database, $persistency = true)
	{

		$this->persistency = $persistency;
		$this->user = $sqluser;
		$this->password = $sqlpassword;
		$this->server = $sqlserver;
		$this->dbname = $database;

		if($this->persistency)
		{
			$this->db_connect_id = @mysql_pconnect($this->server, $this->user, $this->password);
		}
		else
		{
			$this->db_connect_id = @mysql_connect($this->server, $this->user, $this->password);
		}
		if($this->db_connect_id)
		{
			if($database != "")
			{
				$this->dbname = $database;
				$dbselect = @mysql_select_db($this->dbname);
				if(!$dbselect)
				{
					@mysql_close($this->db_connect_id);
					$this->db_connect_id = $dbselect;
				}
			}

			@mysql_query("SET NAMES utf8;", $this->db_connect_id);
			return $this->db_connect_id;
		}
		else
		{
			return false;
		}
	}

	function sql_close()
	{
		if($this->db_connect_id)
		{
			if($this->query_result)
			{
				@mysql_free_result($this->query_result);
			}
			$result = @mysql_close($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}

	function sql_query($query = "")
	{
		unset($this->query_result);
		if($query != "")
		{
			//mysql_real_escape_string
			
			$_injection = false;
			/*
			if(preg_match("/drop table /",strtolower($query)))
			{
				$_injection = true;
			}
			
			if(preg_match("/truncate /",strtolower($query)))
			{
				$_injection = true;
			}	
			
			if(preg_match("/union select/",strtolower($query)))
			{
				$_injection = true;
			}
			*/
			//ALTER
			
			$HTTP_HOST = $_SERVER['HTTP_HOST'];
			$PHP_SELF = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : " ??? ";
			$IP = (getenv("HTTP_X_FORWARDED_FOR")) ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR");
			$PHP_SELF_IP = $IP . "@" . $HTTP_HOST . " (" . $PHP_SELF . ")";
			
			if(!$_injection)
			{
				$this->num_queries++;
	
				$this->query_result = @mysql_query($query, $this->db_connect_id);
				
				$message = @mysql_error($this->db_connect_id);
				$code = @mysql_errno($this->db_connect_id);
				if($message != "")
				{
					LogError($message,$PHP_SELF_IP,$query,"MYSQL");
				}
			}
			else
			{				
				LogError("",$PHP_SELF_IP,$query,"INJECTION ATTACH");
				$this->query_result = "";
			}
		}
		
		if($this->query_result)
		{
			unset($this->row[$this->query_result]);
			unset($this->rowset[$this->query_result]);
			return $this->query_result;
		}
		else
		{
			return false;
		}
	}

	function sql_numrows($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_num_rows($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_affectedrows()
	{
		if($this->db_connect_id)
		{
			$result = @mysql_affected_rows($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_numfields($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_num_fields($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldname($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_field_name($query_id, $offset);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fieldtype($offset, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_field_type($query_id, $offset);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fetchrow($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$this->row[$query_id] = @mysql_fetch_array($query_id);
			return $this->row[$query_id];
		}
		else
		{
			return false;
		}
	}
	function sql_fetchrowset($query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			unset($this->rowset[$query_id]);
			unset($this->row[$query_id]);
			while($this->rowset[$query_id] = @mysql_fetch_array($query_id))
			{
				$result[] = $this->rowset[$query_id];
			}
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_fetchfield($field, $rownum = -1, $query_id = 0)
	{
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			if($rownum > -1)
			{
				$result = @mysql_result($query_id, $rownum, $field);
			}
			else
			{
				if(empty($this->row[$query_id]) && empty($this->rowset[$query_id]))
				{
					if($this->sql_fetchrow())
					{
						$result = $this->row[$query_id][$field];
					}
				}
				else
				{
					if($this->rowset[$query_id])
					{
						$result = $this->rowset[$query_id][$field];
					}
					else if($this->row[$query_id])
					{
						$result = $this->row[$query_id][$field];
					}
				}
			}
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_rowseek($rownum, $query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}
		if($query_id)
		{
			$result = @mysql_data_seek($query_id, $rownum);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_nextid(){
		if($this->db_connect_id)
		{
			$result = @mysql_insert_id($this->db_connect_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	function sql_freeresult($query_id = 0){
		if(!$query_id)
		{
			$query_id = $this->query_result;
		}

		if ( $query_id )
		{
			unset($this->row[$query_id]);
			unset($this->rowset[$query_id]);

			@mysql_free_result($query_id);

			return true;
		}
		else
		{
			return false;
		}
	}
	
	function sql_error($query_id = 0)
	{
		$result["message"] = @mysql_error($this->db_connect_id);
		$result["code"] = @mysql_errno($this->db_connect_id);

		return $result;
	}
	
	function RowSelectorQuery($Statement)
	{
		$result = $this->sql_query($Statement);
		$dr = $this->sql_fetchrow($result);
		$this->sql_freeresult($result);
		return $dr;
	}
	
	function RowSelector($TableName = "", $PrimaryKeys, $QuotFields)
	{
		$Statement = "";
		$WhereStatement = "";
		
		if(!empty($PrimaryKeys))
		{
			foreach($PrimaryKeys as $key=>$val)
			{
				if($PrimaryKeys[$key] != "")
					$WhereStatement .= " " . $key . "=" . ((bool)($QuotFields[$key]) ? "'" : "") . ($PrimaryKeys[$key]) . ((bool)($QuotFields[$key]) ? "'" : "") . " AND " ;
			}
			
			if($WhereStatement != "")
			{
				$WhereStatement = " WHERE " . substr($WhereStatement,0,strlen($WhereStatement)-4);
			}
	
			if($WhereStatement != "")
			{
				$Statement = "SELECT * FROM " . $TableName . $WhereStatement . " LIMIT 1 ";
			}
			
			if($Statement != "")
			{
				$result = $this->sql_query($Statement);
				$dr = $this->sql_fetchrow($result);
				$this->sql_freeresult($result);
				return $dr;
			}
		}
	}
	
	function ExecuteUpdater($TableName = "", $PrimaryKeys, $Collector, $QuotFields)
	{
		$Statement = "";
		$WhereStatement = "";
		
		if(!empty($PrimaryKeys))
		{
			foreach($PrimaryKeys as $key=>$val)
			{
				if($PrimaryKeys[$key] != "")
					$WhereStatement .= " `" . $key . "`=" . ((bool)($QuotFields[$key]) ? "'" : "") . $PrimaryKeys[$key] . ((bool)($QuotFields[$key]) ? "'" : "") . " AND " ;
			}
		}
	
		if($WhereStatement != "")
		{
			$WhereStatement = " WHERE " . substr($WhereStatement,0,strlen($WhereStatement)-4);
		}
		
		if($WhereStatement != "")
		{
			$Statement = "UPDATE `" . $TableName . "` SET ";
			foreach($Collector as $key=>$val)
			{
				$Statement .= "`" . $key . "`=" . ($Collector[$key] != "" ? ((bool)($QuotFields[$key]) ? "'" : "") . $Collector[$key] . ((bool)($QuotFields[$key]) ? "'" : "") : " null " ) . ",";
			}
			//str_replace("'","''",$Collector[$key])
	
			$Statement = substr($Statement,0,strlen($Statement)-1) . $WhereStatement;
		}
		else
		{
			$Statement = "INSERT INTO `" . $TableName . "` (";
			foreach($Collector as $key=>$val)
			{
				$Statement .=  "`" . $key . "`,";
			}
	
			$Statement = substr($Statement,0,strlen($Statement)-1) . ") VALUES (";
	
			foreach($Collector as $key=>$val)
			{
				$Statement .= ($Collector[$key] != "" ? ((bool)($QuotFields[$key]) ? "'" : "") . $Collector[$key] . ((bool)($QuotFields[$key]) ? "'" : "") : " null " ) . ",";
			}
			//str_replace("'","''",$Collector[$key])
	
			$Statement = substr($Statement,0,strlen($Statement)-1) . ")";
		}
	
		//echo $Statement;
		$this->sql_query($Statement);
	}

	function ExecuteDeleter($TableName = "", $PrimaryKeys , $QuotFields)
	{
		$Statement = "";
		$WhereStatement = "";
		
		if(!empty($PrimaryKeys))
		{
			foreach($PrimaryKeys as $key=>$val)
			{
				if($PrimaryKeys[$key] != "")
					$WhereStatement .= " `" . $key . "`=" . ((bool)($QuotFields[$key]) ? "'" : "") . $PrimaryKeys[$key] . ((bool)($QuotFields[$key]) ? "'" : "") . " AND " ;
			}
	
			if($WhereStatement != "")
			{
				$WhereStatement = " WHERE " . substr($WhereStatement,0,strlen($WhereStatement)-4);
				$Statement = "DELETE FROM `" . $TableName . "` " . $WhereStatement . " ";
				$this->sql_query($Statement);
				
				//echo $Statement;
			}
		}
	}
}
	function randomCode($characters) 
	{
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}

	function SendMail($Body, $Subject = "", $To="")
	{
		$MailContent = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>";
		$MailContent .= "<html>";
		$MailContent .= "<head>";
		$MailContent .= "<style type='text/css'>";
			$MailContent .= ".n {font-family: Verdana, Arial;font-size: 12px;}";
			$MailContent .= ".ns {font-family: Verdana, Arial;font-size: 9px;}";
			$MailContent .= ".h {font-family: Verdana, Arial;font-size: 12px;font-weight: bold;color: #000099;}";
			$MailContent .= ".tbl {background-color:  #dddddd;}";
			$MailContent .= ".c {background-color:  #f1f1f1;}";
		$MailContent .= "</style>";
		$MailContent .= "</head>";
		$MailContent .= "<body class='n'>";

		$MailContent .= $Body;

		$MailContent .= "</body>";
		$MailContent .= "</html>";	

		//global $config;
		
		$mailServer="localhost";
		$contactMail="jordan.air@gmail.com";

		$mail = new PHPMailer();
		$mail->IsMail();
		$mail->Host     = $mailServer;
		$mail->SMTPAuth = false;
		$mail->CharSet = "UTF-8";	
		$mail->From     = $contactMail;
		$mail->FromName = site_title;
		$mail->Subject  = $Subject != "" ? $Subject : site_title;
		$mail->Body     = $MailContent;
		$To = ($To != "" ? $To : $contactMail);
		$mail->AddAddress($To);	
		$mail->IsHTML(true);
		if(!$mail->Send())
		{
			LogError("Error during send mail","to: " . $To,$mail->ErrorInfo,"PHP");
		}
	}

	$host = 'localhost';
	$database = 'waspmote';
	$dbuser = 'waspmote';
	$dbpass = 'qwe#123!@#';

	if(isset($_GET['func']) && $_GET['func']=='checkUser') {
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$rowUser = $db->RowSelectorQuery("SELECT * FROM users WHERE email='".$_REQUEST['email']."' AND password='".$_REQUEST['pass']."'");
		if(intval($rowUser['user_id'])>0) {
			array_push($data,array('id' => $rowUser['user_id']));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json); 
		} else {
			$data = array();
			array_push($data,array('id' => '0'));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='passReminder') {
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$rowUser = $db->RowSelectorQuery("SELECT * FROM users WHERE email='".$_REQUEST['email']."' AND is_valid='True'");
		if(intval($rowUser['user_id'])>0) {
			define('user_passwordReminder',"You password in #SITENAME# <br><br>with username #USERNAME# is::<b> #PASSWORD#</b><br><br>");
			$MailContent = user_passwordReminder;		
			$MailContent = str_replace("#SITENAME#","ppcity",$MailContent);
			$MailContent = str_replace("#USERNAME#",$rowUser['email'],$MailContent);
			$MailContent = str_replace("#PASSWORD#",$rowUser['password'],$MailContent);
			SendMail($MailContent,"ppcity Password request :: " ,$rowUser['email']);
			array_push($data,array('id' => $rowUser['user_id']));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json); 
		} else {
			$data = array();
			array_push($data,array('id' => '0'));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='activate') {
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$code=$_REQUEST['code'];
		//check first if the user already exist
		$rowUser = $db->RowSelectorQuery("SELECT * FROM users WHERE random_code='".$_REQUEST['code']."' LIMIT 1");
		if(intval($rowUser['user_id'])!=0) {
			$updateQuery="UPDATE users SET is_valid='True' WHERE user_id='".$rowUser['user_id']."'";
			$db->sql_query($updateQuery);
			array_push($data,array('id' => $rowUser['user_id']));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json); 
		} else {
			$data = array();
			$newID=$rowUser['user_id'];
			array_push($data,array('id' => "0"));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='getLastRecord' && $_GET['type']==1) { //libelium
		//URL http://app.ppcity.eu/api/api.php?func=getLastRecord&type=1&device_id=4
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$device_id=$_GET['device_id'];
		$filter=" AND device_id='".$device_id."'";
		$rowAVG30m=$db->RowSelectorQuery("SELECT AVG(temperature) AS temp30m ,AVG(humidity) AS hum30m,AVG(pressure) AS press30m,AVG(concentration) AS no230m,AVG(noise) AS noise30m,AVG(o3) AS o330m FROM data WHERE 1=1 ".$filter." AND date_insert >= DATE_SUB(NOW(),INTERVAL 30 MINUTE) LIMIT 1");
		$rowAVG1h=$db->RowSelectorQuery("SELECT AVG(temperature) AS temp1h ,AVG(humidity) AS hum1h,AVG(pressure) AS press1h,AVG(concentration) AS no21h,AVG(noise) AS noise1h,AVG(o3) AS o31h FROM data WHERE 1=1 ".$filter." AND date_insert >= DATE_SUB(NOW(),INTERVAL 1 HOUR) LIMIT 1");
		$rowAVG6h=$db->RowSelectorQuery("SELECT AVG(temperature) AS temp6h ,AVG(humidity) AS hum6h,AVG(pressure) AS press6h,AVG(concentration) AS no26h,AVG(noise) AS noise6h,AVG(o3) AS o36h FROM data WHERE 1=1 ".$filter." AND date_insert >= DATE_SUB(NOW(),INTERVAL 6 HOUR) LIMIT 1");
		$rowAVG1d=$db->RowSelectorQuery("SELECT AVG(temperature) AS temp1d ,AVG(humidity) AS hum1d,AVG(pressure) AS press1d,AVG(concentration) AS no21d,AVG(noise) AS noise1d,AVG(o3) AS o31d FROM data WHERE 1=1 ".$filter." AND date_insert >= DATE_SUB(NOW(),INTERVAL 1 DAY) LIMIT 1");
		$rowAVG7d=$db->RowSelectorQuery("SELECT AVG(temperature) AS temp7d ,AVG(humidity) AS hum7d,AVG(pressure) AS press7d,AVG(concentration) AS no27d,AVG(noise) AS noise7d,AVG(o3) AS o37d FROM data WHERE 1=1 ".$filter." AND date_insert >= DATE_SUB(NOW(),INTERVAL 7 DAY) LIMIT 1");
		
		$query="SELECT * FROM data WHERE 1=1 ".$filter." ORDER BY date_insert DESC LIMIT 1";
		$rowRecord = $db->RowSelectorQuery($query);
		if(intval($rowRecord['device_id'])>0){
			array_push($data,array(
				'id' => $rowRecord['id'],
				'device_id' => $rowRecord['device_id'],
				'temperature' => $rowRecord['temperature'],
				'humidity' => $rowRecord['humidity'],
				'pressure' => $rowRecord['pressure'],
				'concentration' => $rowRecord['concentration'],
				'noise' => $rowRecord['noise'],
				'o3' => $rowRecord['o3'],
				'avgTemperature30m' => $rowAVG30m['temp30m'],
				'avgHumidity30m' => $rowAVG30m['hum30m'],
				'avgPressure30m' => $rowAVG30m['press30m'],
				'avgNO230m' => $rowAVG30m['no230m'],
				'avgNoise30m' => $rowAVG30m['noise30m'],
				'avgO330m' => $rowAVG30m['o330m'],
				'avgTemperature1h' => $rowAVG1h['temp1h'],
				'avgHumidity1h' => $rowAVG1h['hum1h'],
				'avgPressure1h' => $rowAVG1h['press1h'],
				'avgNO21h' => $rowAVG1h['no21h'],
				'avgNoise1h' => $rowAVG1h['noise1h'],
				'avgO31h' => $rowAVG1h['o31h'],
				'avgTemperature6h' => $rowAVG6h['temp6h'],
				'avgHumidity6h' => $rowAVG6h['hum6h'],
				'avgPressure6h' => $rowAVG6h['press6h'],
				'avgNO26h' => $rowAVG6h['no26h'],
				'avgNoise6h' => $rowAVG6h['noise6h'],
				'avgO36h' => $rowAVG6h['o36h'],
				'avgTemperature1d' => $rowAVG1d['temp1d'],
				'avgHumidity1d' => $rowAVG1d['hum1d'],
				'avgPressure1d' => $rowAVG1d['press1d'],
				'avgNO21d' => $rowAVG1d['no21d'],
				'avgNoise1d' => $rowAVG1d['noise1d'],
				'avgO31d' => $rowAVG1d['o31d'],
				'avgTemperature7d' => $rowAVG7d['temp7d'],
				'avgHumidity7d' => $rowAVG7d['hum7d'],
				'avgPressure7d' => $rowAVG7d['press7d'],
				'avgNO27d' => $rowAVG7d['no27d'],
				'avgNoise7d' => $rowAVG7d['noise7d'],
				'avgO37d' => $rowAVG7d['o37d'],
				
				'date_insert' => $rowRecord['date_insert']
			));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		} else {
			$data = array();
			array_push($data,array('error' => 'No records found'));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='getLastRecord' && $_GET['type']==2) { //purpleair
		//URL http://app.ppcity.eu/api/api.php?func=getLastRecord&type=2&device_id=13
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$device_id=$_GET['device_id'];
		$filter=" AND sensorID='".$device_id."'";
		$rowAVG30m=$db->RowSelectorQuery("SELECT AVG(pm1val) AS avg130m ,AVG(pm10val) AS avg1030m,AVG(aqi1) AS aqi130m,AVG(aqi10) AS aqi1030m  FROM padata WHERE 1=1 ".$filter." AND time >= DATE_SUB(NOW(),INTERVAL 30 MINUTE) LIMIT 1");
		$rowAVG1h=$db->RowSelectorQuery("SELECT AVG(pm10val) AS avg101h,AVG(pm1val) AS avg11h,AVG(aqi1) AS aqi11h,AVG(aqi10) AS aqi101h FROM padata WHERE 1=1 ".$filter." AND time >= DATE_SUB(NOW(),INTERVAL 1 HOUR) LIMIT 1");
		$rowAVG6h=$db->RowSelectorQuery("SELECT AVG(pm10val) AS avg106h,AVG(pm1val) AS avg16h,AVG(aqi1) AS aqi16h,AVG(aqi10) AS aqi106h FROM padata WHERE 1=1 ".$filter." AND time >= DATE_SUB(NOW(),INTERVAL 6 HOUR) LIMIT 1");
		$rowAVG1d=$db->RowSelectorQuery("SELECT AVG(pm10val) AS avg101d,AVG(pm1val) AS avg11d,AVG(aqi1) AS aqi11d,AVG(aqi10) AS aqi101d FROM padata WHERE 1=1 ".$filter." AND time >= DATE_SUB(NOW(),INTERVAL 1 DAY) LIMIT 1");
		$rowAVG7d=$db->RowSelectorQuery("SELECT AVG(pm10val) AS avg107d,AVG(pm1val) AS avg17d,AVG(aqi1) AS aqi17d,AVG(aqi10) AS aqi107d FROM padata WHERE 1=1 ".$filter." AND time >= DATE_SUB(NOW(),INTERVAL 7 DAY) LIMIT 1");
		$rowST=$db->RowSelectorQuery("SELECT * FROM padata WHERE 1=1 ".$filter."  AND time >= (now() - INTERVAL 30 MINUTE) ORDER BY time  ASC LIMIT 1");
		
		$query="SELECT * FROM padata WHERE 1=1 ".$filter." ORDER BY time DESC LIMIT 1";
		$rowRecord = $db->RowSelectorQuery($query);

		//$result = $db->sql_query($query.$filter);
		if(intval($rowRecord['ID'])>0){
			array_push($data,array(
				'id' => $rowRecord['ID'],
				'device_id' => $rowRecord['sensorID'],
				'aqivalue' => $rowRecord['aqivalue'],
				'rtaqi' => $rowRecord['rtaqi'],
				'staqi' => $rowRecord['staqi'],
				'30minaqi' => $rowRecord['30minaqi'],
				'1hraqi' => $rowRecord['1hraqi'],
				'6hraqi' => $rowRecord['6hraqi'],
				'24hraqi' => $rowRecord['24hraqi'],
				'1wkaqi' => $rowRecord['1wkaqi'],
				'chartgms' => $rowRecord['chartgms'],
				'chastgms' => $rowRecord['chastgms'],
				'cha30mingms' => $rowRecord['cha30mingms'],
				'cha1hrgms' => $rowRecord['cha1hrgms'],
				'cha6hrgms' => $rowRecord['cha6hrgms'],
				'cha24hrgms' => $rowRecord['cha24hrgms'],
				'cha1wkgms' => $rowRecord['cha1wkgms'],
				'chbrtgms' => $rowRecord['chbrtgms'],
				'chbstgms' => $rowRecord['chbstgms'],
				'chb30mingms' => $rowRecord['chb30mingms'],
				'chb1hrgms' => $rowRecord['chb1hrgms'],
				'chb6hrgms' => $rowRecord['chb6hrgms'],
				'chb24hrgms' => $rowRecord['chb24hrgms'],
				'chb1wgms' => $rowRecord['chb1wgms'],
				'pm25level' => $rowRecord['pm25level'],
				'pm10level' => $rowRecord['pm10level'],
				//'pm25val' => $rowRecord['pm25val'],
				'pm1val' => $rowRecord['pm1val'],
				'pm10val' => $rowRecord['pm10val'],
				'avg130m'=> $rowAVG30m['avg130m'],
				'avg1030m'=> $rowAVG30m['avg1030m'],
				'aqi130m'=> $rowAVG30m['aqi130m'],
				'aqi1030m'=> $rowAVG30m['aqi1030m'],
				'avg11h'=> $rowAVG1h['avg11h'],
				'avg101h'=> $rowAVG1h['avg101h'],
				'aqi11h'=> $rowAVG1h['aqi11h'],
				'aqi101h'=> $rowAVG1h['aqi101h'],
				'avg16h'=> $rowAVG6h['avg16h'],
				'avg106h'=> $rowAVG6h['avg106h'],
				'aqi16h'=> $rowAVG6h['aqi16h'],
				'aqi106h'=> $rowAVG6h['aqi106h'],
				'avg11d'=> $rowAVG1d['avg11d'],
				'avg101d'=> $rowAVG1d['avg101d'],
				'aqi11d'=> $rowAVG1d['aqi11d'],
				'aqi101d'=> $rowAVG1d['aqi101d'],
				'avg17d'=> $rowAVG7d['avg17d'],
				'avg107d'=> $rowAVG7d['avg107d'],
				'aqi17d'=> $rowAVG7d['aqi17d'],
				'aqi107d'=> $rowAVG7d['aqi107d'],
				'aqi1valST'=> $rowST['aqi1'],
				'aqi10valST'=> $rowST['aqi10'],
				'pm1valST'=> $rowST['pm1val'],
				'pm10valST'=> $rowST['pm10val'],
				'date_insert' => $rowRecord['time']
			));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		} else {
			$data = array();
			array_push($data,array('error' => 'No records found'));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='getRecordsPeriod' && $_GET['type']==2) { //purpleair Period
		//URL http://app.ppcity.eu/api/api.php?func=getLastRecord&type=2&device_id=13
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$device_id=$_GET['device_id'];
		$filter=" AND sensorID='".$device_id."'";
		$periodFrom=$_GET['periodFrom'];
		$periodTo=$_GET['periodTo'];
		$filter.= " AND date(time)>='".$periodFrom."'";
		$filter.= " AND date(time)<='".$periodTo."'";
		$ts1 = strtotime($periodFrom);
		$ts2 = strtotime($periodTo);
		$seconds_diff = (($ts2 - $ts1)/3600/24)+1;
		$error="";
		if($seconds_diff>10 || $seconds_diff<1) $error="Invalid number of days";
		$query="SELECT * FROM padata WHERE 1=1 ".$filter." ORDER BY time ASC";
		$result = $db->sql_query($query);
		if($db->sql_numrows($result)>0){
			while ($rowRecord = $db->sql_fetchrow($result)) {
				array_push($data,array(
					'id' => $rowRecord['ID'],
					'device_id' => $rowRecord['sensorID'],
					'aqivalue' => $rowRecord['aqivalue'],
					'rtaqi' => $rowRecord['rtaqi'],
					'staqi' => $rowRecord['staqi'],
					'30minaqi' => $rowRecord['30minaqi'],
					'1hraqi' => $rowRecord['1hraqi'],
					'6hraqi' => $rowRecord['6hraqi'],
					'24hraqi' => $rowRecord['24hraqi'],
					'1wkaqi' => $rowRecord['1wkaqi'],
					'chartgms' => $rowRecord['chartgms'],
					'chastgms' => $rowRecord['chastgms'],
					'cha30mingms' => $rowRecord['cha30mingms'],
					'cha1hrgms' => $rowRecord['cha1hrgms'],
					'cha6hrgms' => $rowRecord['cha6hrgms'],
					'cha24hrgms' => $rowRecord['cha24hrgms'],
					'cha1wkgms' => $rowRecord['cha1wkgms'],
					'chbrtgms' => $rowRecord['chbrtgms'],
					'chbstgms' => $rowRecord['chbstgms'],
					'chb30mingms' => $rowRecord['chb30mingms'],
					'chb1hrgms' => $rowRecord['chb1hrgms'],
					'chb6hrgms' => $rowRecord['chb6hrgms'],
					'chb24hrgms' => $rowRecord['chb24hrgms'],
					'chb1wgms' => $rowRecord['chb1wgms'],
					'pm25level' => $rowRecord['pm25level'],
					'pm10level' => $rowRecord['pm10level'],
					'pm1val' => $rowRecord['pm1val'],
					'pm10val' => $rowRecord['pm10val'],
					'date_insert' => $rowRecord['time']
				));
			}
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		} else {
			if($error=="") $error="No records found";
			$data = array();
			array_push($data,array('error' => $error));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='getRecordsPeriod' && $_GET['type']==1) { //libelium
		//http://app.ppcity.eu/api/api.php?func=getRecordsPeriod&type=1&device_id=4&periodFrom=2019-03-20&periodTo=2019-03-21
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$device_id=$_GET['device_id'];

		$filter=" AND device_id='".$device_id."'";
		$periodFrom=$_GET['periodFrom'];
		$periodTo=$_GET['periodTo'];
		$filter.= " AND date(date_insert)>='".$periodFrom."'";
		$filter.= " AND date(date_insert)<='".$periodTo."'";
		$ts1 = strtotime($periodFrom);
		$ts2 = strtotime($periodTo);
		$seconds_diff = (($ts2 - $ts1)/3600/24)+1;
		$error="";
		if($seconds_diff>10 || $seconds_diff<1) $error="Invalid number of days";
		$query="SELECT * FROM data WHERE 1=1 ".$filter." ORDER BY date_insert ASC";
		$result = $db->sql_query($query);

		if($db->sql_numrows($result)>0){
			while ($rowRecord = $db->sql_fetchrow($result)) {
				array_push($data,array(
					'id' => $rowRecord['id'],
					'device_id' => $rowRecord['device_id'],
					'temperature' => $rowRecord['temperature'],
					'humidity' => $rowRecord['humidity'],
					'pressure' => $rowRecord['pressure'],
					'concentration' => $rowRecord['concentration'],
					'noise' => $rowRecord['noise'],
					'o3' => $rowRecord['o3'],
					'date_insert' => $rowRecord['date_insert']
				));
			}
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		} else {
			if($error=="") $error="No records found";
			$data = array();
			array_push($data,array('error' => 'No records found'));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='getRecordsDay' && $_GET['type']==1) { //libelium
		//http://app.ppcity.eu/api/api.php?func=getRecordsDay&type=1&date=2019-03-18
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		//$device_id=$_GET['device_id'];
		//$filter=" AND device_id='".$device_id."'";
		$periodDate=$_GET['date'];
		$filter.= " AND date(date_insert)='".$periodDate."'";
		$query="SELECT * FROM data WHERE 1=1 ".$filter." ORDER BY date_insert ASC";
		$result = $db->sql_query($query);
		if($db->sql_numrows($result)>0){
			while ($rowRecord = $db->sql_fetchrow($result)) {
				array_push($data,array(
					'id' => $rowRecord['id'],
					'device_id' => $rowRecord['device_id'],
					'temperature' => $rowRecord['temperature'],
					'humidity' => $rowRecord['humidity'],
					'pressure' => $rowRecord['pressure'],
					'concentration' => $rowRecord['concentration'],
					'noise' => $rowRecord['noise'],
					'o3' => $rowRecord['o3'],
					'date_insert' => $rowRecord['date_insert']
				));
			}
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		} else {
			if($error=="") $error="No records found";
			$data = array();
			array_push($data,array('error' => 'No records found'));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='getRecordsDay' && $_GET['type']==2) { //purpleair Period
		//URL http://app.ppcity.eu/api/api.php?func=getLastRecord&type=2&device_id=13
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$periodDate=$_GET['date'];
		$filter.= " AND date(time)='".$periodDate."'";
		
		$query="SELECT * FROM padata WHERE 1=1 ".$filter." ORDER BY time ASC";
		//$rowRecord = $db->RowSelectorQuery($query);
		$result = $db->sql_query($query);
		if($db->sql_numrows($result)>0){
			while ($rowRecord = $db->sql_fetchrow($result)) {
				array_push($data,array(
					'id' => $rowRecord['ID'],
					'device_id' => $rowRecord['sensorID'],
					'aqivalue' => $rowRecord['aqivalue'],
					'rtaqi' => $rowRecord['rtaqi'],
					'staqi' => $rowRecord['staqi'],
					'30minaqi' => $rowRecord['30minaqi'],
					'1hraqi' => $rowRecord['1hraqi'],
					'6hraqi' => $rowRecord['6hraqi'],
					'24hraqi' => $rowRecord['24hraqi'],
					'1wkaqi' => $rowRecord['1wkaqi'],
					'chartgms' => $rowRecord['chartgms'],
					'chastgms' => $rowRecord['chastgms'],
					'cha30mingms' => $rowRecord['cha30mingms'],
					'cha1hrgms' => $rowRecord['cha1hrgms'],
					'cha6hrgms' => $rowRecord['cha6hrgms'],
					'cha24hrgms' => $rowRecord['cha24hrgms'],
					'cha1wkgms' => $rowRecord['cha1wkgms'],
					'chbrtgms' => $rowRecord['chbrtgms'],
					'chbstgms' => $rowRecord['chbstgms'],
					'chb30mingms' => $rowRecord['chb30mingms'],
					'chb1hrgms' => $rowRecord['chb1hrgms'],
					'chb6hrgms' => $rowRecord['chb6hrgms'],
					'chb24hrgms' => $rowRecord['chb24hrgms'],
					'chb1wgms' => $rowRecord['chb1wgms'],
					'pm25level' => $rowRecord['pm25level'],
					'pm10level' => $rowRecord['pm10level'],
					'pm1val' => $rowRecord['pm1val'],
					'pm10val' => $rowRecord['pm10val'],
					'date_insert' => $rowRecord['time']
				));
			}
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		} else {
			if($error=="") $error="No records found";
			$data = array();
			array_push($data,array('error' => $error));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='getRecordsParams' && $_GET['type']==2) { //purpleair Period
		//URL http://app.ppcity.eu/api/api.php?func=getRecordsParams&type=2&param=aqivalue&condition=less&value=80
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$param=$_GET['param'];
		$value=$_GET['value'];
		$condition=$_GET['condition'];
		$device_id=$_GET['device_id'];
		$filter=" AND ".$param.($condition=='less'?'<=':'>=').$value;
		if(intval($device_id)>0) $filter.=" AND sensorID='".$device_id."'";
		$query="SELECT * FROM padata WHERE 1=1 ".$filter." ORDER BY time ASC LIMIT 1000";

		$result = $db->sql_query($query);
		if($db->sql_numrows($result)>0){
			while ($rowRecord = $db->sql_fetchrow($result)) {
				array_push($data,array(
					'id' => $rowRecord['ID'],
					'device_id' => $rowRecord['sensorID'],
					'aqivalue' => $rowRecord['aqivalue'],
					'rtaqi' => $rowRecord['rtaqi'],
					'staqi' => $rowRecord['staqi'],
					'30minaqi' => $rowRecord['30minaqi'],
					'1hraqi' => $rowRecord['1hraqi'],
					'6hraqi' => $rowRecord['6hraqi'],
					'24hraqi' => $rowRecord['24hraqi'],
					'1wkaqi' => $rowRecord['1wkaqi'],
					'chartgms' => $rowRecord['chartgms'],
					'chastgms' => $rowRecord['chastgms'],
					'cha30mingms' => $rowRecord['cha30mingms'],
					'cha1hrgms' => $rowRecord['cha1hrgms'],
					'cha6hrgms' => $rowRecord['cha6hrgms'],
					'cha24hrgms' => $rowRecord['cha24hrgms'],
					'cha1wkgms' => $rowRecord['cha1wkgms'],
					'chbrtgms' => $rowRecord['chbrtgms'],
					'chbstgms' => $rowRecord['chbstgms'],
					'chb30mingms' => $rowRecord['chb30mingms'],
					'chb1hrgms' => $rowRecord['chb1hrgms'],
					'chb6hrgms' => $rowRecord['chb6hrgms'],
					'chb24hrgms' => $rowRecord['chb24hrgms'],
					'chb1wgms' => $rowRecord['chb1wgms'],
					'pm25level' => $rowRecord['pm25level'],
					'pm10level' => $rowRecord['pm10level'],
					'pm1val' => $rowRecord['pm1val'],
					'pm10val' => $rowRecord['pm10val'],
					'date_insert' => $rowRecord['time']
				));
			}
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		} else {
			if($error=="") $error="No records found";
			$data = array();
			array_push($data,array('error' => $error));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='getRecordsParams' && $_GET['type']==1) { //libelium
		//http://app.ppcity.eu/api/api.php?func=getRecordsDay&type=1&date=2019-03-18
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$param=$_GET['param'];
		$value=$_GET['value'];
		$condition=$_GET['condition'];
		$device_id=$_GET['device_id'];
		$filter=" AND ".$param.($condition=='less'?'<=':'>=').$value;
		if(intval($device_id)>0) $filter.=" AND device_id='".$device_id."'";

		$query="SELECT * FROM data WHERE 1=1 ".$filter." ORDER BY date_insert ASC";
		$result = $db->sql_query($query);
		if($db->sql_numrows($result)>0){
			while ($rowRecord = $db->sql_fetchrow($result)) {
				array_push($data,array(
					'id' => $rowRecord['id'],
					'device_id' => $rowRecord['device_id'],
					'temperature' => $rowRecord['temperature'],
					'humidity' => $rowRecord['humidity'],
					'pressure' => $rowRecord['pressure'],
					'concentration' => $rowRecord['concentration'],
					'noise' => $rowRecord['noise'],
					'o3' => $rowRecord['o3'],
					'date_insert' => $rowRecord['date_insert']
				));
			}
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		} else {
			if($error=="") $error="No records found";
			$data = array();
			array_push($data,array('error' => 'No records found'));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	} else if(isset($_GET['func']) && $_GET['func']=='getSensors') { 
		//http://app.ppcity.eu/api/api.php?func=getSensors
		$db = new sql_db($host, $dbuser, $dbpass, $database, false);
		$data = array();
		$query="SELECT * FROM mysensors t1 INNER JOIN sensortypes t2 ON t1.sensortype_id=t2.sensortype_id INNER JOIN organizations t3 ON t1.organization_id=t3.organization_id";
		$result = $db->sql_query($query);
		if($db->sql_numrows($result)>0){
			while ($rowRecord = $db->sql_fetchrow($result)) {
				array_push($data,array(
					'id' => $rowRecord['mysensor_id'],
					'status' => $rowRecord['is_valid'],
					'label' => $rowRecord['sensor_label'],
					'name' => $rowRecord['sensor_name'],
					'location' => $rowRecord['sensor_location'],
					'encloser_number' => $rowRecord['sensor_case'],
					'type_id' => $rowRecord['sensortype_id'],
					'type_name' => $rowRecord['sensortype_name'],
					'organization_id' => $rowRecord['organization_id'],
					'organization_name' => $rowRecord['organization_name']
				));
			}
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		} else {
			if($error=="") $error="No records found";
			$data = array();
			array_push($data,array('error' => 'No records found'));
			$json = json_encode($data);
			$json = "[" . substr($json, 1, strlen($json) - 2) . "]";
			print_r($json);
		}
	}

?>


