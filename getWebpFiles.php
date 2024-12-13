<?php

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Downloads an image.
 *
 * @param \GuzzleHttp\Client $client
 *    The GuzzleHttp client.
 * @param string $fileName
 *    The .web file full path.
 */
function downloadImage($client, $fileName) {
  $dirname = dirname(__DIR__ . $fileName);
  if (!is_dir($dirname)) {
    mkdir($dirname, 0777, TRUE);
  }

  try {
   // print(__DIR__ . $fileName);
    $resource = fopen(__DIR__ . $fileName, 'w');
    $client->request('GET', $fileName, ['sink' => $resource]);
  } catch (GuzzleException $e) {
    print($e->getMessage() . "\n");
  }
}

/**
 * Gets images form text.txt file.
 *
 * @param \GuzzleHttp\Client $client
 *     The GuzzleHttp client.
 */
function getImagesFromTxt($client) {
  $file = fopen(__DIR__ . '/text.txt', 'r');

  if (!$file) {
    print("Couldn't find txt file\n");
    die();
  }

  while (($line = fgets($file)) !== FALSE) {
    downloadImage($client, rtrim($line, "\n"));
  }

  fclose($file);
}

/**
 * Scrapping for images in static website directory.
 *
 * @param \GuzzleHttp\Client $client
 *    The GuzzleHttp client.
 * @param string $dirPath
 *    The static website directory
 */
function getImagesFromSiteDirectory($client, $dirPath) {
  if(!is_dir($dirPath)) {
    print("Invalid directory!\n");
    die();
  }
  $dir = new RecursiveDirectoryIterator($dirPath);
  $srcSets = [];
  foreach (new RecursiveIteratorIterator($dir) as $filename => $file) {
    if ($file->getExtension() != 'html') {
      continue;
    }
    print("Searching for .webp files in $filename\n");
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTMLFile($filename);
    $xpath = new DOMXpath($doc);
    $imgs = $xpath->query("//source[@type='image/webp']");
    for ($i=0; $i < $imgs->length; $i++) {
      $img = $imgs->item($i);
      $src = $img->getAttribute("srcset");
      $srcSets = array_merge($srcSets, explode(',', $src));
    }
  }

  print("Downloading .webp images\n");

  foreach ($srcSets as $srcSet) {
    // srcset contains image path and dimension
    $imagePath = explode(' ', $srcSet)[0];
    if(!empty($imagePath)) {
     downloadImage($client, $imagePath);
    }
  }
}

/**
 * Main function.
 */
function main() {
  $arg = getopt('b:d:');
  if (count($arg) < 1) {
    print("No base URL argument supplied!\n");
    die();
  }

  $baseURL = $arg['b'];
  $client = new Client(['base_uri' => $baseURL]);
  if (!array_key_exists('d', $arg)) {
    // no directory specified -> downloading .web files form text.txt file
   getImagesFromTxt($client);
    return;
  }

  // directory specified -> web scraping for .wep files
  getImagesFromSiteDirectory($client, $arg['d']);
}

main();