<?php
namespace Craft;

class EmailsService extends BaseApplicationComponent
{

	// SEND NOTIFICATIONS TO PEOPLE LISTED
	public function sendAll($vars = array(), $user = '') 
	{

    $headerHtml = craft()->emails->getEmailHeaderHtml();
    $footerHtml = craft()->emails->getEmailFooterHtml();

    foreach ($vars['customers'] as &$customer) {



          $body = '';
          $body .= $headerHtml;
           //$body .= '<p>' . $vars['greeting'];
           //$body .= "<br><br>";
           $body .= $vars['message'];
           //$body .= "<br><br>";
           //$body .= nl2br($vars['signature']) . '</p>';
          $body .= $footerHtml;

            // SEND THE EMAIL
            $email = new EmailModel();
            //$email->toEmail = $options['email'];
            $email->toEmail = $customer['email'];
            $email->subject = $vars['subject'];
            $email->body    = $body;
            if (!craft()->email->sendEmail($email)) {
                    $error = Craft::t('The email could not be sent.') . print_r($email);
                    EmailsPlugin::log($error, LogLevel::Error);
                    throw new Exception($error);
            } else {
              $notice = Craft::t('Email sent to ') . $customer['email'];
              EmailsPlugin::log($notice, LogLevel::Info);
            }
    }
    if (!empty($vars['entryIds'])) {
        $entryIds = explode("-", $vars['entryIds']);
        foreach($entryIds as $entryId) {
            $entry = craft()->entries->getEntryById($entryId);
            $emailsSentSoFar = $entry->emailsSent;
            $emailsSentSoFar  = $emailsSentSoFar + 1;
            $entry->setContentFromPost(array(
                'emailsSent' => $emailsSentSoFar
            ));
            craft()->entries->saveEntry($entry);
        }
    }
    return TRUE;

	}
/*
  // REQUIRES CUSTOMER MODEL, GREETING, MESSAGE, SIGNATURE, SUBJECT
  // SEND NOTIFICATIONS TO A SINGLE EMAIL ADDRESS
  public function sendAll($vars = array(), $user = '') 
  {

    $headerHtml = craft()->emails->getEmailHeaderHtml();
    $footerHtml = craft()->emails->getEmailFooterHtml();

    $body = $headerHtml;
    //$body .= '<p>' . $vars['greeting'];
    //$body .= "<br><br>";
    $body .= $vars['message'];
    //$body .= "<br><br>";
    //$body .= nl2br($vars['signature']) . '</p>';
    $body .= $footerHtml;

    // SEND THE EMAIL
    $email = new EmailModel();
    //$email->toEmail = $options['email'];
    $email->toEmail = $customer['email'];
    $email->subject = $vars['subject'];
    $email->body    = $body;
    if (!craft()->email->sendEmail($email)) {
            $error = Craft::t('The email could not be sent.') . print_r($email);
            EmailsPlugin::log($error, LogLevel::Error);
            throw new Exception($error);
    }

    return TRUE;

  }
*/

  // HOLDS THE HTML HEADER
  public function getEmailHeaderHtml() 
  {
$headerHtml = <<<HEADER
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title></title>
    <style>
    body {
      font-family: sans-serif;
    }
    @media only screen and (max-width: 580px) {
      td.responsive {
          width: 100% !important;
          text-align: left;
      }
  }
     p, li {
      margin-top: 0;
      text-align: left; color: #673535; font-size: 16px; font-family: font-family: Arial, Helvetica, sans-serif; line-height: 1.3em;
    }
    p a, li a {
      color: #ff3366;
    }
    </style>
  </head>

  <body>
    <!-- <style> -->
    <table width="100%" data-made-with-foundation>
      <tr>
        <td align="center" valign="top">
          <center>
            <table width="580" style="background: #ffffff;">
              <tbody>
                <tr>
                  <td align="right" height="20" style="font-size:16px; line-height:16px; color: #0000000; text-align: right;" colspan="2"></td>
                </tr>
                <tr>
                  <td class="responsive" width="300" style="padding-left: 16px;">
                          <img src="https://lacuisineparis.com/img/la-cuisine-paris-cooking-classes-600x240.gif" align="left" alt="La Cuisine Paris" width="300" height="120">
                  </td>
                  <td class="responsive"  width="280" valign="top" style="padding-right: 16px; text-align: right; color: #ff3366; font-size: 15px; font-family: sans-serif; font-weight: normal;line-height: 22px;">
                    <a style="color: #ff3366;" href="tel:+33140517818">+33 (0)1 40 51 78 18</a><br>
                    <a style="color: #ff3366;" href="mailto:contact@lacuisineparis.com">contact@lacuisineparis.com</a><br>
                    <a style="color: #ff3366;" href="https://lacuisineparis.com">www.lacuisineparis.com</a>
                  </td>
                </tr>
                <tr>
                  <td align="right" height="20" style="font-size:16px; line-height:16px; color: #ffffff; text-align: right;" colspan="2"></td>
                </tr>
              </tbody>
            </table>
            <table width="580">
              <tr>
                <td style="padding: 0 16px 0; text-align: left; color: #673535; font-size: 18px; font-family: sans-serif;">

HEADER;
  
    return $headerHtml;
  }

  // HOLDS THE HTML HEADER
  public function getEmailFooterHtml() 
  {

$footerHtml = <<<FOOTER
      </td>
    </tr>
</table>
        </center>
      </td>
    </tr>
  </table>

<table width="100%" data-made-with-foundation>
  <tr>
    <td align="center" valign="top">
      <center>
<table width="580" bgcolor="#ffffff">
  <tr>
    <td align="right" height="20" style="font-size:16px; line-height:16px; color: #ffffff; text-align: right; padding: 0 16px;" colspan="2"><hr><br></td>
  </tr>
  <tr>
      <td class="responsive"  width="400" valign="top" style="padding-left: 16px; text-align: left; color: #ff3366; font-size: 12px; font-family: sans-serif; font-weight: normal;line-height: 22px;">
        La Cusine Paris, 80 Quai de l'Hôtel de ville, 75004 Paris, France<br>
        <a style="color: #ff3366;" href="tel:+33140517818">+33 (0)1 40 51 78 18</a> <br>
        <a style="color: #ff3366;" href="mailto:contact@lacuisineparis.com">contact@lacuisineparis.com</a> <br>
        <a style="color: #ff3366;" href="https://lacuisineparis.com">www.lacuisineparis.com</a>
      </td>
      <td class="responsive"  width="180" valign="top" style="padding-right: 16px; text-align: left; color: #ff3366; font-size: 12px; font-family: sans-serif; font-weight: normal;line-height: 22px;"><img src="https://lacuisineparis.com/img/la-cuisine-paris-cooking-classes-600x240.gif" align="left" alt="La Cuisine Paris" width="150" height="60"></td>
  </tr>
</table>   
        </center>
      </td>
    </tr>
  </table>
</body>
</html>
FOOTER;
  
    return $footerHtml;
  }



}