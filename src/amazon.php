<?php
// Bootstrap the application
require_once "bootstrap.php";

// Banners
$banners = [
  // Side banners
  "side" => [
    "dimensions" => [160, 600],
    "de-de" => [
      // Amazon Business
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=3&p=14&l=ur1&category=amazon_business&banner=05JYHYWX3G1T2C17VFG2&f=ifr&linkID=52dd8da6c2c7abc87c7bec134ad1b307&t=invfu-21&tracking_id=invfu-21",
      // Various
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=3&p=14&l=ez&f=ifr&linkID=1d9cca0b5a5c323ede459beb6706f306&t=invfu-21&tracking_id=invfu-21",
      // Music unlimited
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=3&p=14&l=ur1&category=musicunlimited&banner=1AYW45V5VK9HC0JDT502&f=ifr&linkID=96e82e64efac6d4f50c7e2c4eccdbe1e&t=invfu-21&tracking_id=invfu-21",
      // Rebel code book
      "//ws-eu.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace=DE&source=ss&ref=as_ss_li_til&ad_type=product_link&tracking_id=invfu-21&language=de_DE&marketplace=amazon&region=DE&placement=0738206709&asins=0738206709&linkId=6b1ad6a55bdf2b2cd883e3721f197b64&show_border=true&link_opens_in_new_window=true",
            
    ],
    "en-gb" => [
      // Amazon business
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=2&p=14&l=ur1&category=amazon_business&banner=104TZC55Y0CHEG4WKH82&f=ifr&linkID=96a78e1bf0dfc764e35194c8a30c1c34&t=invfu0b-21&tracking_id=invfu0b-21",
      // Kindle unlimited
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=2&p=14&l=ur1&category=kindle_unlimited&banner=116BMX0VPH8CFG6W6G82&f=ifr&linkID=39f526ef9ea18ca9b33d74237454246f&t=invfu0b-21&tracking_id=invfu0b-21",
      // Prime music
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=2&p=14&l=ur1&category=primemusic&banner=0WCC25HN3HXERPPAQX02&f=ifr&linkID=902f9ec4cf1689c4e64de3be73cb8fd2&t=invfu0b-21&tracking_id=invfu0b-21",
      // Rebel code
      "//ws-eu.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace=GB&source=ac&ref=qf_sp_asin_til&ad_type=product_link&tracking_id=invfu0b-21&marketplace=amazon&region=GB&placement=0738206709&asins=0738206709&linkId=fa55eaf2c7c347f27382092c904aedb8&show_border=false&link_opens_in_new_window=true&price_color=333333&title_color=0066c0&bg_color=ffffff",

    ],
    "es-es" => [
      // Amazin business
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=30&p=14&l=ur1&category=amazon_business&banner=0AQGA3XRNC9SMYA7K8G2&f=ifr&linkID=aa990729949ce6aeccf292dbac6b30b9&t=invfu01-21&tracking_id=invfu01-21",
      // 
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=30&p=11&l=ur1&category=amu&banner=1F9748QTW9589TQACEG2&f=ifr&linkID=daa14c20b2ad06a9496d000828be864a&t=invfu01-21&tracking_id=invfu01-21",
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=30&p=14&l=ur1&category=industria_empresas&banner=18CHPGE9KGKJWX4WXVG2&f=ifr&linkID=7b2fecf98176d324865fcf0819702918&t=invfu01-21&tracking_id=invfu01-21",

    ],
    "fr-fr" => [
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=8&p=14&l=ur1&category=amazon_business&banner=1KM4PJJQ73WMHB8NHCG2&f=ifr&linkID=b45e9420aae219bea39a9a8979544f21&t=invfu03-21&tracking_id=invfu03-21",
      "https://rcm-eu.amazon-adsystem.com/e/cm?o=8&p=14&l=ur1&category=amu&banner=1AR9GF6AW6JY4XBD46G2&f=ifr&linkID=baf536a50572a3fd25e9ed615736d59b&t=invfu03-21&tracking_id=invfu03-21",

    ],
    "en-us" => [
      "//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=primeent&banner=0XFKWQVGDFG5VJ2ARBG2&f=ifr&linkID=6fb80dc169c390e35e995047108371ee&t=invfu-20&tracking_id=invfu-20",
      "//rcm-na.amazon-adsystem.com/e/cm?o=1&p=14&l=ur1&category=wireless&banner=0PNTRFWGKQW8GEJPVER2&f=ifr&linkID=a587a6bf5443b9ec728d580337015658&t=invfu-20&tracking_id=invfu-20",

    ],    
  ],
  // Top banners
  "top" => [

  ],
  // Mobile banners
  "mobile" => [

  ]
];

// Detect the users local and reqquested banner type
$local = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$local = substr($local, 0, 5);
$local = strtolower($local);
$bannerType = $_GET['banner'];

// Banner selection
$typedBanners = $banners[$bannerType];
$dimensions = $typedBanners['dimensions'];

// Check if we support this country
if(!array_key_exists($local, $typedBanners))
  return;

// Fetch the supported banners and select one randomly
$bannerCollection = $banners[$bannerType][$local];
$index = rand(0, sizeof($bannerCollection) - 1);
$banner = $bannerCollection[$index];

?>
<iframe 
  src="<?= $banner ?>" 
  width="<?= $dimensions[0] ?>" height="<?= $dimensions[1] ?>" 
  scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0">
</iframe>
