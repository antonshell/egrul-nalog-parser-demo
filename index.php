<?php

use \antonshell\EgrulNalogParser\Parser;

require 'vendor/autoload.php';

/**
 * @return array|mixed
 * @throws Exception
 */
function parseUploadFile(){
    $parser = new Parser();
    $uploadDir = __DIR__ . '/uploads/';
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);
    $ext = pathinfo($uploadFile, PATHINFO_EXTENSION);

    if(!in_array($ext,['pdf','PDF'])){
        echo 'wrong file format';
        die();
    }

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        $data = $parser->parseDocument($uploadFile);
    } else {
        throw new Exception('Cant upload file');
    }

    return $data;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['scenario'])){
    $parser = new Parser();
    $scenario = $_POST['scenario'];
    $data = [];

    switch ($scenario) {
        case 'parse-pe-default':
            $filePath = __DIR__ . '/docs/nalog_pe.pdf';
            $data = $parser->parseNalogPe($filePath);
            break;
        case 'parse-org-default':
            $filePath = __DIR__ . '/docs/nalog_org.pdf';
            $data = $parser->parseNalogOrg($filePath);
            break;
        case 'parse-custom':
            $data = parseUploadFile();
            break;
        default:
            echo 'wrong scenario';
            die();
            break;
    }

    echo '<pre>';
    print_r($data);
    die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Egrul Nalog Parser Demo</title>

    <link rel="stylesheet" href="node_modules/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css">
</head>
<body>
    <div class="container">
        <h1>Egrul Nalog Parser Demo</h1>

        <br><br>

        <form class="form-inline" action="" method="post">
            <a class="btn btn-default btn-lg" href="docs/nalog_org.pdf">
                <i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i>&nbsp;
                Organization PDF Example
            </a>

            <input type="hidden" name="scenario" value="parse-org-default">
            <button type="submit" class="btn btn-success btn-lg">Parse Document!</button>
        </form>

        <hr>

        <form class="form-inline" action="" method="post">
            <a class="btn btn-default btn-lg" href="docs/nalog_pe.pdf">
                <i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i>&nbsp;
                Individual Entrepreneur PDF Example
            </a>

            <input type="hidden" name="scenario" value="parse-pe-default">
            <button type="submit" class="btn btn-success btn-lg">Parse Document!</button>
        </form>

        <br><br>

        <h1>Parse custom document</h1>

        <hr>

        <form class="form-inline" action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="exampleFormControlFile1">Upload pdf file</label>
                <input type="file" class="form-control-file" name="file" id="exampleFormControlFile1">
            </div>

            <input type="hidden" name="scenario" value="parse-custom">
            <button type="submit" class="btn btn-success btn-lg">Parse Document!</button>
        </form>
    </div>
</body>
</html>


