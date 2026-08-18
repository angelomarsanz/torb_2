<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Vrent - Email Confirmation</title>
  
  <!--[if mso]>
  <style type="text/css">
    body, table, td {font-family: Arial, Helvetica, sans-serif !important;}
  </style>
  <![endif]-->
  
  <style type="text/css">
    /* ============================================
       RESET & BASE STYLES
       ============================================ */
    html {
      font-size: 62.5%;
    }
    
    body {
      margin: 0;
      padding: 0;
      font-size: 1.6rem;
      font-weight: 300;
      line-height: 1.667;
      background-color: #fafafa;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    
    table {
      border-collapse: collapse;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }
    
    img {
      display: block;
      border: 0;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }
    
    /* ============================================
       TYPOGRAPHY
       ============================================ */
    p {
      margin: 0;
      padding: 0;
      color: #777777;
      font-family: 'proxima_nova_rgregular', 'Proxima Nova', 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 1.6rem;
      line-height: 1.667;
    }
    
    .text-16 {
      font-size: 1.6rem;
    }
    
    .text-18 {
      font-size: 1.8rem;
    }
    
    .font-weight-700 {
      font-weight: 700;
    }
    
    .text-left {
      text-align: left;
    }
    
    .text-right {
      text-align: right;
    }
    
    .text-center {
      text-align: center;
    }
    
    .text-justify {
      text-align: justify;
    }
    
    /* ============================================
       LAYOUT & CONTAINERS
       ============================================ */
    .email-wrapper {
      width: 100%;
      background-color: #fafafa;
      padding: 20px 0;
    }
    
    .email-container {
      max-width: 600px;
      width: 100%;
      margin: 0 auto;
      background-color: #ffffff;
      border: 1px solid #d8d8d8;
      box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
    }
    
    .email-content {
      padding: 30px;
    }
    
    /* ============================================
       SPACING UTILITIES
       ============================================ */
    .mt-20 {
      margin-top: 20px;
    }
    
    .mb-20 {
      margin-bottom: 20px;
    }
    
    .p-1 {
      padding: 15px;
    }
    
    .p-3 {
      padding: 30px;
    }
    
    /* ============================================
       COMPONENTS
       ============================================ */
    .logo-container {
      padding: 20px 30px;
      text-align: center;
      background-color: #ffffff;
    }
    
    .logo-img {
      max-width: 100%;
      height: auto;
      display: block;
      margin: 0 auto;
    }
    
    .content-section {
      padding: 30px;
      background-color: #ffffff;
    }
    
    .button-container {
      margin-top: 20px;
      text-align: center;
    }
    
    .button-link {
      display: inline-block;
      text-decoration: none;
    }
    
    .button {
      display: inline-block;
      padding: 12px 30px;
      background-color: #1dbf73;
      color: #ffffff !important;
      text-decoration: none;
      border: 1px solid #1dbf73;
      border-radius: 4px;
      font-family: 'proxima_nova_rgregular', 'Proxima Nova', 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 1.4rem;
      font-weight: 600;
      text-transform: uppercase;
      text-align: center;
      line-height: 1.4;
      -webkit-text-size-adjust: none;
      mso-hide: all;
    }
    
    .button:hover {
      background-color: #1aa862;
      border-color: #1aa862;
    }
    
    /* ============================================
       RESPONSIVE DESIGN
       ============================================ */
    @media only screen and (max-width: 600px) {
      .email-container {
        width: 100% !important;
        max-width: 100% !important;
      }
      
      .email-content,
      .content-section {
        padding: 20px !important;
      }
      
      .logo-container {
        padding: 15px 20px !important;
      }
      
      .button {
        width: 100% !important;
        max-width: 280px !important;
        padding: 14px 20px !important;
        font-size: 1.4rem !important;
      }
      
      .text-16,
      .text-18,
      p {
        font-size: 1.5rem !important;
      }
    }
    
    @media only screen and (min-width: 601px) {
      .desktop-padding {
        padding: 50px !important;
      }
    }
    
    /* ============================================
       EMAIL CLIENT FIXES
       ============================================ */
    /* Outlook specific fixes */
    .ExternalClass {
      width: 100%;
    }
    
    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
      line-height: 100%;
    }
    
    /* Prevent iOS auto-detecting phone numbers */
    a[x-apple-data-detectors] {
      color: inherit !important;
      text-decoration: none !important;
      font-size: inherit !important;
      font-family: inherit !important;
      font-weight: inherit !important;
      line-height: inherit !important;
    }
  </style>
</head>
<body style="margin: 0; padding: 0; background-color: #fafafa;">
  <!-- 
    Email Wrapper Table
    Outer container that centers the email and provides background color
  -->
  <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="email-wrapper">
    <tr>
      <td align="center" style="padding: 20px 0;">
        <!-- 
          Email Container Table
          Main content container with max-width of 600px for optimal email client display
        -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container">
          <!-- Logo Section -->
          <tr>
            <td class="logo-container" style="padding: 20px 30px; text-align: center; background-color: #ffffff;">
              <!-- 
                Placeholder: #image_link# 
                Replaced by sendPhpEmail() method with actual logo URL
              -->
              <img src="#image_link#" alt="Logo" class="logo-img" style="max-width: 100%; height: auto; display: block; margin: 0 auto; border: 0;">
            </td>
          </tr>
          
          <!-- Content Section -->
          <tr>
            <td class="content-section" style="padding: 30px; background-color: #ffffff;">
              <!-- Message Body -->
              <!-- 
                Placeholder: #message_body# 
                Replaced by sendPhpEmail() method with email content from database template
                The message_body contains #BUTTON_HTML# placeholder which gets replaced with button HTML
                This allows the button to appear inline where specified in the database template
              -->
              <div style="color: #777777; font-family: 'proxima_nova_rgregular', 'Proxima Nova', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 1.6rem; line-height: 1.667;">
                #message_body#
              </div>
            </td>
          </tr>
          
          <!-- Footer Spacer -->
          <tr>
            <td style="padding: 0; height: 20px; background-color: #ffffff;">
              &nbsp;
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
