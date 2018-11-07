<?php
// Defintion of a sponsor object. It contains the necessary information
// sponsors need to provide
class Sponsor
{
  // Link url of the sponsor
  private $linkURL;

  // URL of the sponsors logo
  private $logoURL;

  function __construct($linkURL, $logoURL)
  {
    $this->linkURL = $linkURL;
    $this->logoURL = $logoURL;
  }

  // Render this sponsor instance  
  public function render()
  {
    return "<a href=\"" . $this->linkURL . "\"><img src=\"" . $this->logoURL . "\"></a>";
  }

  public static $prices = [
    20,  // Basic 
    100, // Footer
    150  // README
  ];

  public static function renderFooter()
  {
    $sponsors = [
      // All sponsors for the footer
      //new Sponsor("https://example.com", "https://example.com/logo.png"),
    ];

    Sponsor::renderSponsors($sponsors);
  }

  public static function renderOthers()
  {
    $sponsors = [
      // All sponsors for the sponsors tab
      //new Sponsor("https://example.com", "https://example.com/logo.png"),
    ];

    Sponsor::renderSponsors($sponsors);
  }

  private static function renderSponsors($sponsors)
  {
    echo "<div class=\"sponsors\">";

    foreach($sponsors as $sponsor)
      echo $sponsor->render();

    echo "</div>";
  }

  private static $donors = [
    //"Author" => "Message"
  ];

  public static function donorCount()
  {
    return sizeof(Sponsor::$donors);
  }

  public static function renderDonors()
  {
    echo "<div class=\"donors\">";

    foreach (Sponsor::$donors as $donorName => $message) {
      echo "<div><div class=\"message\">\"" . $message ."\"</div><div class=\"donor\">" . $donorName . "</div></div>";
    }

    echo "</div>";
  }
}

