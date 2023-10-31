<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP-OOP-MongoDB-CRUD</title>
</head>

<body class="bg-dark">
    <?php 

require '../class/wrapper.php';
require '../class/plugin.php';

//Instantiate
$appWrapper = new AppWrapper();
// Plugin Instantiate
$plugins = new PlugIns();

$appList = [
    'getSignIn',
    'getDashboard',
];

$pluginsList = [
    'CustomAnimateScrollPlugin',
    'BootstrapPlugIn',
    'jqueryPlugin',
    'SweetAlert2Plugin',
    'BoxiconsPlugin',
];

foreach ($pluginsList as $pluginName) {
    echo $plugins->$pluginName();
}

foreach ($appList as $appName) {
    echo $appWrapper->$appName();
}

    ?>
</body>

</html>