<?php

require 'vendor/autoload.php';

use HeadlessChromium\BrowserFactory;

$headless = true;
$email = getenv('HOLLEY_EMAIL');
$password = getenv('HOLLEY_PASSWORD');

if (!($email && $password)) {
    throw new InvalidArgumentException('Invalid email or password.');
}

$browserFactory = new BrowserFactory();
$browser = $browserFactory->createBrowser([
    'headless' => $headless,
]);

try {
    $page = $browser->createPage();
    $page->navigate('https://b2b.holley.com/')->waitForNavigation();

    // login

    $page->keyboard()->typeRawKey('Tab')->typeText($email);
    $page->keyboard()->typeRawKey('Tab')->typeText($password);

    // mouse() ではうまくクリックできなかった
    $page->evaluate('document.getElementById("modal-login-button").click()');

    sleep(3);

    // Downloadsをクリックした時点でAPIキーが生成される
    // mouse() ではうまくクリックできなかった
    $page->evaluate('document.querySelector("#nav-downloads > a").click()');

    sleep(3);

    $downloadUrl = $page->evaluate('[...document.querySelectorAll("#nav-downloads > ul > li > a")].find(e => e.innerText === "Available Inventory Report")?.href')->getReturnValue();

    echo $downloadUrl;
} finally {
    $browser->close();
}
