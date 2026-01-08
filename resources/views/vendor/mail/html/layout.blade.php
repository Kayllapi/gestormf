<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: rgb(249, 249, 249);" id="bodyTable">
    <tbody><tr>
    <td align="center" valign="top" style="padding-right:10px;padding-left:10px;" id="bodyCell">
    <!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" style="width:600px;" width="600"><tr><td align="center" valign="top"><![endif]-->
    {{ $header ?? '' }}

    <!-- Email Wrapper Body Open // -->
    <table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperBody">
      <tbody><tr>
        <td align="center" valign="top">

          <!-- Table Card Open // -->
          <table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">

            <tbody>

            <tr>
              <td align="center" valign="top" style="padding-top:20px;padding-left:20px;padding-right:20px;text-align: center;" class="containtTable ui-sortable">
                {{ Illuminate\Mail\Markdown::parse($slot) }}
              </td>
            </tr>

            <tr>
              <td height="20" style="font-size:1px;line-height:1px;">&nbsp;</td>
            </tr>

            
          </tbody></table>
          <!-- Table Card Close// -->

          <!-- Space -->
          <table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
            <tbody><tr>
              <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
            </tr>
          </tbody></table>

        </td>
      </tr>
    </tbody></table>
    <!-- Email Wrapper Body Close // -->

    {{ $footer ?? '' }}

    {{ $subcopy ?? '' }}

    <!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]-->
    </td>
    </tr>
</tbody></table>
</body>
</html>
