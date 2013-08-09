<?
//$_POST['donation'] = "$" . $_POST['donation'];

for($x = 1; $x <= 5; $x++){
if ($_POST['lang'] == 'EN'){
	$garb = array('\"', "\'");
	$rep = array('&quot;', "&#39;");
	$message = str_replace($garb, $rep, $_POST['message']);
	$body = <<<END
		<center>
		<table width='720' cellpadding='0' cellspacing='0'>
			<tr>
				
				<td colspan=2 style='font-family: Arial;text-align:right;width:720px; font-size:10px; padding-bottom:20px;'>If you are unable to see the message below, <a href='http://staging.lushconcepts.com/demos/unicef/slaythemonster/emails/email2.php?name={$_POST['name' . $x]}&nameSender={$_POST['name']}&message=$message'>click here to view</a>.</td>
			</tr>
			<tr>
			<td colspan=2 style='background:#25a6e0;width:720px'><a href='http://unicef.ca/portal/SmartDefault.aspx'><img border='0' src='http://staging.lushconcepts.com/demos/unicef/slaythemonster/img/header_email_en.jpg' /></a></td>
			</tr>
			<tr valign='top'>
			<td style='width:290px;padding:25px 0px 0px 0px; margin:0px;'><a style='padding:0px; margin:0px;' href='https://secure.unicef.ca/portal/SmartDefault.aspx?at=1209&appealID=35&CID=50'><img border='0' style='padding:0px; margin:0px;' src='http://staging.lushconcepts.com/demos/unicef/slaythemonster/img/side.jpg' /></a></td>
			<td style='width:430px;padding:25px 0 0 0px;font-family: Arial;'>
					<h1 style='color:#25a6e0;font-size:25px;padding-top:7px;'>Hi {$_POST['name' . $x]}</h1>
					<p style='font-size:13px; font-family: Arial;color: #7f7f7f;'>{$_POST['name']} has sent you this email so that you can help spread the net and stop malaria. Just click on the link below and find out how easy it is to help.</p>
					<p style='font-size:13px;color: #7f7f7f;'>{$message}</p>
					<p align='right'><a href='http://staging.lushconcepts.com/demos/unicef/slaythemonster/'><img border='0' src='http://staging.lushconcepts.com/demos/unicef/slaythemonster/img/buttonStart.jpg'></a></p>
					<img style='float:left; margin:0px; padding:0px;' src='http://staging.lushconcepts.com/demos/unicef/slaythemonster/img/fly_email_en.jpg' />
					<p style='clear:both;'>
					<p style='font-size:13px;color: #7f7f7f;'>Malaria is responsible for over one million deaths each year, the majority being children in Africa. In fact, malaria kills a child somewhere in the world every 30 seconds and is responsible for one in five of all childhood deaths in Africa.</p>
					<h1 style='color:#25a6e0;font-size:18px;margin-bottom:0px;padding-bottom:0px;'>1 Net. 10 Bucks. Save lives.</h1>
					<p style='font-size:13px;margin-top:0px; padding-top:0px;color: #7f7f7f;'>Malaria-carrying mosquitoes bite at night, while children are sleeping. The simplest, most effective way to prevent the transmission of malaria is with a $10 insecticide-treated bed net.</p>
					<h1 style='color:#25a6e0;font-size:18px;margin-bottom:0px;padding-bottom:0px;'>Help defeat the monsters in the bedroom.<br/>Donate today.</h1>
					<p style='font-size:13px;margin-top:0px; padding-top:0px;color: #7f7f7f;'>UNICEF is a global leader in malaria prevention, purchasing more bed nets and distributing them to more families in need than any other organization in the world. Please give what you can today.</p>
					
			</td>
			</tr>
		</table>
		</center>
END;


}else{
	
	
$body = <<<END
<table width="750">
     <tr>
          <td colspan="4"><img src="http://unicefvaccinations.com/fr/img/emailheader.gif"></td>
     </tr>
     <tr>
          <td colspan="4" height="30"></td>
     <tr>
     	  <td width="15">&nbsp;</td>
          <td valign="top" width="169"><img src="http://unicefvaccinations.com/fr/img/left_col_challenge.png" align="right" /></td>
          <td width="500" valign="top" style="padding:0px 20px 0px 20px;"><p style="font-family:Arial, Helvetica, sans-serif; font-size: 12px; color: #666;"><span style="font-size:21px; color: #00aeef;">Aiderez-vous l'UNICEF &agrave; atteindre son objectif?</span><br />
                    <br />
                    {$_POST['name' . $x]}, {$_POST['name']} a choisi de verser <span style="font-weight:bold; color: #00aeef; font-size: 16px;">{$_POST['donation']} \$</span> &agrave; l'UNICEF pour l'aider &agrave; atteindre son objectif de vacciner contre la rougeole un million d'enfants de plus en 2009; et vous lance le d&eacute;fi de verser le m&ecirc;me montant ou m&ecirc;me plus. <br />
                    <br />
                    Accepterez-vous de relever ce d&eacute;fi et de nous aider &agrave; prot&eacute;ger un million d'enfants de plus contre la rougeole au cours des prochaines semaines?  <br />
                    <br />
                    La rougeole, une infection respiratoire virale qui attaque le syst&egrave;me immunitaire, tue plus d'enfants chaque ann&eacute;e que toute autre maladie &eacute;vitable par vaccin. Les enfants de moins de cinq ans sont les plus &agrave; risque. Parmi les enfants qui survivent, nombreux sont ceux qui souffrent d'incapacit&eacute;s permanentes. Mais on peut facilement pr&eacute;venir la rougeole gr&acirc;ce &agrave; un vaccin efficace et s&ucirc;r ne co&ucirc;tant que 36 cents la dose.<br />
                    <br />
                   L'UNICEF s'est fix&eacute; comme objectif de vacciner un million d'enfants de plus d'ici la fin de l'ann&eacute;e. Il ne reste que peu de temps! Cependant, gr&acirc;ce &agrave; votre soutien, nous pourrons parvenir &agrave; prot&eacute;ger un million d'enfants de plus contre cette maladie mortelle, mais &eacute;vitable par vaccin.<br />
                    <br />
                    <span style="color: #00aeef; font-weight: bold;">Message de {$_POST['name']} : </span></p>
            <table cellpadding="0" cellspacing="0" width=100%>
                    <tr>
                         <td style="border:1px solid #00aeef; border-left: none; border-right: none;"><p style="font-family:Arial, Helvetica, sans-serif; font-size: 12px; color: #666;"><br />
                         {$message}<br />
                         <br />
                         </p></td>
                    </tr>
            </table>
               <table cellpadding="0" cellspacing="0">
                    <tr><td colspan="2" height="20">&nbsp;</td></tr>
                    <tr>
                         <td width="270"><p style="font-family:Arial, Helvetica, sans-serif; font-size: 12px; color: #666; font-weight:bold; color: #00aeff;">Merci de votre soutien!</p></td>
                         <td><a href="https://secure.unicef.ca/portal/SmartDefault.aspx?at=1223&appealID=76" style="border:none; text-decoration: none;"><img src="http://unicefvaccinations.com/fr/img/btn_make_donation.png" style="border:none; text-decoration: none;" /></a></td>
                    </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td><a href="http://fr.unicef-vaccination.com/" style="border:none; text-decoration: none;"><img src="http://unicefvaccinations.com/img/btn_visit_site_fr.png" width="178" height="64" style="border:none; text-decoration: none;" /></a></td>
                    </tr>
          </table></td>

  </tr>
</table>
END;
}
$to      = $_POST['email' . $x];
if ($_POST['lang'] == 'EN'){
	$subject = $_POST['name'. $x] . ', help slay the monster';
}else{
	$subject = $_POST['name'] . ' Aidez à chasser le monstre';
}
$headers = 'From: ' . $_POST['email'] . "\r\n" .
    'Reply-To: ' . $_POST['email'] . "\r\n" .
    'X-Mailer: PHP/' . phpversion() .
    'MIME-Version: 1.0' . "\r\n" .
    'Content-type: text/html; charset=iso-8859-1' . "\r\n";


mail($to, $subject, $body, $headers);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head profile="http://gmpg.org/xfn/11">
		<title>Spread the net - Stop Malaria</title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="swfobject.js" type="text/javascript"></script>
		<script type="text/javascript" src="jquery-1.2.6.min.js"></script>
		<script type="text/javascript" src="jQueryRollover.js"></script>
	</head>
	<body>
		<div class='whole'>
				<?include('header-popup.php');?>
				<div class='right'>
<?

if ($_POST['lang'] == 'EN'){
	echo "<h1 style='padding:20px 0 0 0px; margin:0px;'>Thank you!<br>The web site has been sent.</h1>";
}else{
	echo "<h1 style='padding:20px 0 0 0px; margin:0px;'>Thank you!<br>The web site has been sent.</h1>";
}
?>
		
				</div>
			</div>	
		</div>
	</body>
</html>
