<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head profile="http://gmpg.org/xfn/11">
		<title>Spread the net - Stop Malaria</title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="swfobject.js" type="text/javascript"></script>
		<script type="text/javascript" src="jquery-1.2.6.min.js"></script>
		<script type="text/javascript" src="jQueryRollover.js"></script>
<script language="javascript">
      function submitCheck(form){
	      document.getElementById(form).submit();
      }
</script>
	</head>
	<body>
		<div class='whole'>
				<?include('header-popup.php');?>
				<div class='right'>
				<h1 style='margin:20px 0 10px 0;padding-top:0px;'>Send this web site to a friend!</h1>
				<p>You can also help stop the spread of malaria by sending this web site to your friends and family. Fill out the form below along with your personalized message and they'll be able to help Spread the Net - and slay the real monster.</p>
	  <form method=post id='chalForm' action='send_site.php'>
 		  <input name=lang type=hidden value="EN">
               <table width='650'>
                    <tr>
                         <td class="form_labels">Your Name:</td>
                         <td class="form_labels">Your E-mail:</td>
                    </tr>
                    <tr>
                         <td class="form_labels" width="185"><input name="name"  style="width:308px" type="text" /></td>
                         <td class="form_labels" width="185"><input name="email" style="width:308px" type="text" /></td>
                    </tr>
                    <tr>
                         <td colspan="3" class="form_labels"><br />
                              Write your own message:</td>
                    </tr>
                    <tr>
                         <td colspan="3"><textarea style='width:638px;' class="text_challenge" name="message" cols="76" rows="7"></textarea></td>
                    </tr>
                         <td colspan="3">&nbsp;</td>
                    </tr>
               </table>
               <table width="644" style="border: 1px solid #999; padding: 10px;">
                    <tr>
                         <td class="form_labels">Name:</td>
                         <td class="form_labels">E-mail Address:</td>
                    </tr>
                    <tr>
                         <td class="form_labels"><input style="width:308px" class="txtfield_name" name="name1" size="36" type="text" style="margin-bottom:10px" /></td>
                         <td colspan="2" class="form_labels"><input style="width:308px" class="txtfield_email" name="email1" type="text" size="36" style="margin-bottom:10px" /></td>
                    </tr>
                    <tr>
                         <td class="form_labels">Name:</td>
                         <td class="form_labels">E-mail Address:</td>
                    </tr>
                    <tr>
                         <td class="form_labels"><input style="width:308px" class="txtfield_name" name="name2" size="36" type="text" style="margin-bottom:10px" /></td>
                         <td colspan="2" class="form_labels"><input style="width:308px" class="txtfield_email"  name="email2" type="text" size="36" style="margin-bottom:10px" /></td>
                    </tr>
                    <tr>
                         <td class="form_labels">Name:</td>
                         <td class="form_labels">E-mail Address:</td>
                    </tr>
                    <tr>
                         <td class="form_labels"><input style="width:308px" class="txtfield_name" name="name3" size="36" type="text" style="margin-bottom:10px" /></td>
                         <td colspan="2" class="form_labels"><input style="width:308px" class="txtfield_email"  name="email3" type="text" size="36" style="margin-bottom:10px" /></td>
                    </tr>
                    <tr>
                         <td class="form_labels">Name:</td>
                         <td class="form_labels">E-mail Address:</td>
                    </tr>
                    <tr>
                         <td class="form_labels"><input style="width:308px" name="name4" class="txtfield_name" size="36" type="text" style="margin-bottom:10px" /></td>
                         <td colspan="2" class="form_labels"><input style="width:308px" class="txtfield_email"  name="email4" type="text" size="36" style="margin-bottom:10px" /></td>
                    </tr>
                    <tr>
                         <td class="form_labels">Name:</td>
                         <td class="form_labels">E-mail Address:</td>
                    </tr>
                    <tr>
                         <td class="form_labels"><input style="width:308px" class="txtfield_name" name="name5" size="36" type="text" /></td>
                         <td colspan="2" class="form_labels"><input style="width:308px" class="txtfield_email"  name="email5" type="text" size="36" /></td>
                    </tr>
               </table>
               <br />
               <div>
                    <img style="cursor:pointer;float:right;" onclick="submitCheck('chalForm');"  src="img/send.jpg" />
               </div>
          </form>
				</div>
			</div>	
		</div>
	</body>
</html>
