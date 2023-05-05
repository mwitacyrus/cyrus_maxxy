<?php




class PHP_Email_Form
{
    public $to;
    public $from_name;
    public $from_email;
    public $subject;
    public $message;
    public $headers;
    public $smtp;
	public $ajax = true;


    public function add_message($content, $label = '')
    {
        $this->message .= ($label ? $label . ": " : "") . $content . "\n";
    }

    public function send()
    {
        $this->generate_headers();

        if (!$this->smtp) {
            return mail($this->to, $this->subject, $this->message, $this->headers);
        }

        $this->smtp['subject'] = $this->subject;
        $this->smtp['to'] = $this->to;
        $this->smtp['from_name'] = $this->from_name;
        $this->smtp['from_email'] = $this->from_email;
        $this->smtp['body'] = $this->message;

        $smtp_response = $this->send_smtp_email();

        return $smtp_response;
    }

    private function generate_headers()
    {
        $this->headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
        $this->headers .= "Reply-To: " . $this->from_email . "\r\n";
        $this->headers .= "Content-type: text/plain; charset=UTF-8\r\n";
    }

    private function send_smtp_email()
    {
        $to = $this->smtp['to'];
        $subject = $this->smtp['subject'];
        $message = $this->smtp['body'];
        $headers = $this->headers;

        $host = $this->smtp['host'];
        $username = $this->smtp['username'];
        $password = $this->smtp['password'];
        $port = $this->smtp['port'];

        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
                'personalizations' => array(
                    array(
                        'to' => array(
                            array(
                                'email' => $to,
                                'name' => $to
                            )
                        ),
                        'subject' => $subject
                    )
                ),
                'from' => array(
                    'email' => $username,
                    'name' => $username
                ),
                'content' => array(
                    array(
                        'type' => 'text/html',
                        'value' => $message
                    )
                )
            )));

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $password,
                'Content-Type: application/json'
            ));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            curl_close($ch);

            return $response;
        } else {
            $msg = "URL extension is not enabled on this server. Please enable it or use the PHP mail() function instead.";
            error_log($msg);

            return false;
        }
    }
}
?>