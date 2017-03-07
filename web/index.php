<!DOCTYPE html>
<html>
    <head>
        <style>
            /* Optional Styling: */
            body { background: #fafafa; font-size: 13px; font-family: Verdana; padding: 40px; }
            fieldset { width: 280px; background: #fff; padding: 10px; display: block; }
            legend { font-size: 12px; margin: 0; }
            input, textarea { margin: 0; padding: 3px; border: 1px solid #aaa; -moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; width: 278px }
            label { width: 100%; font-weight: bold; float: left; }
            .submit { background: #444; color: #fff; width: inherit; border: none; padding: 10px; cursor: pointer; } .submit:hover { background: #000; }
            .msg { padding: 10px; border: 1px solid #ccc; background: #fff; width: 285px; margin: 0 0 20px; }
            .msg.success { border-color: #86a62f; background: #faffec; }
            .msg.error { border-color: #cd5a5a; background: #fff7f7; }

            /* Required for Honey Pot: */
            .robotic { display: none; }
        </style>
    </head>
    <body>
        <form method="post" action="">
            <fieldset>
                <legend>Not so safe mailer</legend>
                <p>
                    <label>Name:</label>
                    <input name="name" type="text" id="name" />
                </p>
                <p>
                    <label>From E-Mail:</label>
                    <input name="fromemail" type="text" id="fromemail"/>
                </p>
                <p>
                    <label>To E-mail:</label>
                    <input name="toemail" type="text" id="toemail" />
                </p>
                <p>
                    <label>Message:</label>
                    <textarea name="message" id="message"></textarea>
                </p>
                <p>
                    <input type="submit" value="Send Message" class="submit" />
                </p>
            </fieldset>
        </form>
    </body>
</html>

<?php
$path = dirname(__DIR__);
require_once("$path/common/common.php");

$pdata = filter_input_array(INPUT_POST);

if (!is_null($pdata)) {
    $subject    = 'Contact Form Submission';
    $from_name  = $pdata['name'];
    $from_email = $pdata['fromemail'];
    $to_email   = $pdata['toemail'];
    $message    = $pdata['message'];
    if ($from_name && $from_email && $to_email && $message) {

        $ipaddr   = getUserIP();
        $postdata = base64_encode(json_encode($pdata));
        /* Let's see if this has been used before */
        $query    = "SELECT ID,COUNT FROM IPLOG WHERE IPADDR=\"$ipaddr\"";
        $data     = $sqli->query($query);
        $exists   = false;
        while ($row      = $data->fetchArray(SQLITE3_ASSOC)) {
            $id     = $row['ID'];
            $count  = $row['COUNT'];
            $exists = true;
        }

        if ($exists) {
            $flag = 0;
            if (++$count >= $threshold && !in_array($ipaddr,$whitelist)) {
                $flag = 1;
            }
            $query = "UPDATE IPLOG SET REQUESTDATA=\"$postdata\", UPDATED_ON=".time().", COUNT=".$count.", FLAGGED=".$flag." WHERE ID=$id";
            $ret   = $sqli->query($query);
        } else {
            $location = ($geoloc ? "needs code first" : "disabled");
            $query    = "INSERT INTO IPLOG (IPADDR,GEOLOC,REQUESTDATA,COUNT,CREATED_ON,UPDATED_ON,FLAGGED) VALUES (\"$ipaddr\",\"$location\",\"$postdata\", 1, ".time().", NULL,0)";

            $sqli->query($query);
        }
        $success = "You are human and your message was sent!";
    } else {
        $error = "All fields are required.";
    }

    if ($success) {
        echo '<div class="msg success">'.$success.'</div>';
    }
}
$sqli->close();
