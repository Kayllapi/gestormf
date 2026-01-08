	<!-- Email Wrapper Footer Open // -->
    <table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperFooter">
      <tbody><tr>
        <td align="center" valign="top">
          <!-- Content Table Open// -->
          <table border="0" cellpadding="0" cellspacing="0" width="100%" class="footer">
            <tbody>

            <tr>
              <td align="center" valign="top" style="padding: 0px 10px 10px;" class="footerEmailInfo">
                <!-- Information of NewsLetter (Subscribe Info)// -->
                <p class="text" style="color:#777777; font-family:'Open Sans', Helvetica, Arial, sans-serif; font-size:12px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:20px; text-transform:none; text-align:center; padding:0; margin:0;">
                {{ Illuminate\Mail\Markdown::parse($slot) }}
                </p>
              </td>
            </tr>

            <!-- Space -->
            <tr>
              <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
            </tr>
          </tbody></table>
          <!-- Content Table Close// -->
        </td>
      </tr>

      <!-- Space -->
      <tr>
        <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
      </tr>
    </tbody></table>
    <!-- Email Wrapper Footer Close // -->